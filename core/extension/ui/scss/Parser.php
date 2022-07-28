<?php
namespace core\extension\ui\scss;
use core\extension\ui\scss\exception\ParserException;
use core\extension\ui\scss\node\Number;

class Parser {
    const SOURCE_INDEX  = -1;
    const SOURCE_LINE   = -2;
    const SOURCE_COLUMN = -3;

    protected static $precedence = [
        '=' => 0,
        'or' => 1,
        'and' => 2,
        '==' => 3,
        '!=' => 3,
        '<=>' => 3,
        '<=' => 4,
        '>=' => 4,
        '<' => 4,
        '>' => 4,
        '+' => 5,
        '-' => 5,
        '*' => 6,
        '/' => 6,
        '%' => 6
    ];

    protected static $commentPattern;
    protected static $operatorPattern;
    protected static $whitePattern;
    private          $sourceName;
    private          $sourceIndex;
    private          $sourcePositions;
    private          $charset;
    private          $count;
    private          $env;
    private          $inParens;
    private          $eatWhiteDefault;
    private          $buffer;
    private          $utf8;
    private          $encoding;
    private          $patternModifiers;

    public function __construct($sourceName,$sourceIndex = 0,$encoding = 'utf-8') {
        $this->sourceName       = $sourceName ? : '(stdin)';
        $this->sourceIndex      = $sourceIndex;
        $this->charset          = null;
        $this->utf8             = !$encoding || strtolower($encoding) === 'utf-8';
        $this->patternModifiers = $this->utf8 ? 'Aisu' : 'Ais';

        if (empty(static::$operatorPattern)) {
            static::$operatorPattern = '([*\/%+-]|[!=]\=|\>\=?|\<\=\>|\<\=?|and|or)';
            $commentSingle           = '\/\/';
            $commentMultiLeft        = '\/\*';
            $commentMultiRight       = '\*\/';
            static::$commentPattern  = $commentMultiLeft.'.*?'.$commentMultiRight;
            static::$whitePattern    = $this->utf8 ? '/'.$commentSingle.'[^\n]*\s*|('.static::$commentPattern.')\s*|\s+/AisuS' : '/'.$commentSingle.'[^\n]*\s*|('.static::$commentPattern.')\s*|\s+/AisS';
        }
    }

    public function getSourceName() {
        return $this->sourceName;
    }

    public function throwParseError($msg = 'parse error') {
        list($line, /* $column */) = $this->getSourcePosition($this->count);
        $loc = empty($this->sourceName) ? "line: $line" : "$this->sourceName on line $line";
        if ($this->peek("(.*?)(\n|$)",$m,$this->count)) {
            throw new ParserException("$msg: failed at `$m[1]` $loc");
        }
        throw new ParserException("$msg: $loc");
    }

    public function parse($buffer) {
        // strip BOM (byte order marker)
        if (substr($buffer,0,3) === "\xef\xbb\xbf") {
            $buffer = substr($buffer,3);
        }
        $this->buffer          = rtrim($buffer,"\x00..\x1f");
        $this->count           = 0;
        $this->env             = null;
        $this->inParens        = false;
        $this->eatWhiteDefault = true;
        $this->saveEncoding();
        $this->extractLineNumbers($buffer);
        $this->pushBlock(null); // root block
        $this->whitespace();
        $this->pushBlock(null);
        $this->popBlock();
        while($this->parseChunk()) {
            ;
        }
        if ($this->count !== strlen($this->buffer)) {
            $this->throwParseError();
        }
        if (!empty($this->env->parent)) {
            $this->throwParseError('unclosed block');
        }
        if ($this->charset) {
            array_unshift($this->env->children,$this->charset);
        }
        $this->env->isRoot = true;
        $this->restoreEncoding();
        return $this->env;
    }

    public function parseValue($buffer,&$out) {
        $this->count           = 0;
        $this->env             = null;
        $this->inParens        = false;
        $this->eatWhiteDefault = true;
        $this->buffer          = (string)$buffer;
        $this->saveEncoding();
        $list = $this->valueList($out);
        $this->restoreEncoding();
        return $list;
    }

    public function parseSelector($buffer,&$out) {
        $this->count           = 0;
        $this->env             = null;
        $this->inParens        = false;
        $this->eatWhiteDefault = true;
        $this->buffer          = (string)$buffer;
        $this->saveEncoding();
        $selector = $this->selectors($out);
        $this->restoreEncoding();
        return $selector;
    }

    protected function parseChunk() {
        $s = $this->seek();
        // the directives
        if (isset($this->buffer[$this->count]) && $this->buffer[$this->count] === '@') {
            if ($this->literal('@at-root') && ($this->selectors($selector) || true) && ($this->map($with) || true) && $this->literal('{')) {
                $atRoot           = $this->pushSpecialBlock(Type::T_AT_ROOT,$s);
                $atRoot->selector = $selector;
                $atRoot->with     = $with;
                return true;
            }
            $this->seek($s);
            if ($this->literal('@media') && $this->mediaQueryList($mediaQueryList) && $this->literal('{')) {
                $media            = $this->pushSpecialBlock(Type::T_MEDIA,$s);
                $media->queryList = $mediaQueryList[2];
                return true;
            }
            $this->seek($s);
            if ($this->literal('@mixin') && $this->keyword($mixinName) && ($this->argumentDef($args) || true) && $this->literal('{')) {
                $mixin       = $this->pushSpecialBlock(Type::T_MIXIN,$s);
                $mixin->name = $mixinName;
                $mixin->args = $args;
                return true;
            }
            $this->seek($s);
            if ($this->literal('@include') && $this->keyword($mixinName) && ($this->literal('(') && ($this->argValues($argValues) || true) && $this->literal(')') || true) && ($this->end() || $this->literal('{') && $hasBlock = true)) {
                $child = [Type::T_INCLUDE,$mixinName,isset($argValues) ? $argValues : null,null];
                if (!empty($hasBlock)) {
                    $include        = $this->pushSpecialBlock(Type::T_INCLUDE,$s);
                    $include->child = $child;
                } else {
                    $this->append($child,$s);
                }
                return true;
            }
            $this->seek($s);
            if ($this->literal('@scssphp-import-once') && $this->valueList($importPath) && $this->end()) {
                $this->append([Type::T_SCSSPHP_IMPORT_ONCE,$importPath],$s);
                return true;
            }
            $this->seek($s);
            if ($this->literal('@import') && $this->valueList($importPath) && $this->end()) {
                $this->append([Type::T_IMPORT,$importPath],$s);
                return true;
            }
            $this->seek($s);
            if ($this->literal('@import') && $this->url($importPath) && $this->end()) {
                $this->append([Type::T_IMPORT,$importPath],$s);
                return true;
            }
            $this->seek($s);
            if ($this->literal('@extend') && $this->selectors($selectors) && $this->end()) {
                // check for '!flag'
                $optional = $this->stripOptionalFlag($selectors);
                $this->append([Type::T_EXTEND,$selectors,$optional],$s);
                return true;
            }
            $this->seek($s);
            if ($this->literal('@function') && $this->keyword($fnName) && $this->argumentDef($args) && $this->literal('{')) {
                $func       = $this->pushSpecialBlock(Type::T_FUNCTION,$s);
                $func->name = $fnName;
                $func->args = $args;
                return true;
            }
            $this->seek($s);
            if ($this->literal('@break') && $this->end()) {
                $this->append([Type::T_BREAK],$s);
                return true;
            }
            $this->seek($s);
            if ($this->literal('@continue') && $this->end()) {
                $this->append([Type::T_CONTINUE],$s);
                return true;
            }
            $this->seek($s);
            if ($this->literal('@return') && ($this->valueList($retVal) || true) && $this->end()) {
                $this->append([Type::T_RETURN,isset($retVal) ? $retVal : [Type::T_NULL]],$s);
                return true;
            }
            $this->seek($s);

            if ($this->literal('@each') && $this->genericList($varNames,'variable',',',false) && $this->literal('in') && $this->valueList($list) && $this->literal('{')) {
                $each = $this->pushSpecialBlock(Type::T_EACH,$s);
                foreach($varNames[2] as $varName) {
                    $each->vars[] = $varName[1];
                }
                $each->list = $list;
                return true;
            }
            $this->seek($s);
            if ($this->literal('@while') && $this->expression($cond) && $this->literal('{')) {
                $while       = $this->pushSpecialBlock(Type::T_WHILE,$s);
                $while->cond = $cond;
                return true;
            }
            $this->seek($s);
            if ($this->literal('@for') && $this->variable($varName) && $this->literal('from') && $this->expression($start) && ($this->literal('through') || ($forUntil = true && $this->literal('to'))) && $this->expression($end) && $this->literal('{')) {
                $for        = $this->pushSpecialBlock(Type::T_FOR,$s);
                $for->var   = $varName[1];
                $for->start = $start;
                $for->end   = $end;
                $for->until = isset($forUntil);

                return true;
            }
            $this->seek($s);
            if ($this->literal('@if') && $this->valueList($cond) && $this->literal('{')) {
                $if        = $this->pushSpecialBlock(Type::T_IF,$s);
                $if->cond  = $cond;
                $if->cases = [];
                return true;
            }
            $this->seek($s);
            if ($this->literal('@debug') && $this->valueList($value) && $this->end()) {
                $this->append([Type::T_DEBUG,$value],$s);
                return true;
            }
            $this->seek($s);
            if ($this->literal('@warn') && $this->valueList($value) && $this->end()) {
                $this->append([Type::T_WARN,$value],$s);
                return true;
            }
            $this->seek($s);
            if ($this->literal('@error') && $this->valueList($value) && $this->end()) {
                $this->append([Type::T_ERROR,$value],$s);
                return true;
            }
            $this->seek($s);
            if ($this->literal('@content') && $this->end()) {
                $this->append([Type::T_MIXIN_CONTENT],$s);
                return true;
            }
            $this->seek($s);
            $last = $this->last();
            if (isset($last) && $last[0] === Type::T_IF) {
                list(,$if) = $last;

                if ($this->literal('@else')) {
                    if ($this->literal('{')) {
                        $else = $this->pushSpecialBlock(Type::T_ELSE,$s);
                    } elseif ($this->literal('if') && $this->valueList($cond) && $this->literal('{')) {
                        $else       = $this->pushSpecialBlock(Type::T_ELSEIF,$s);
                        $else->cond = $cond;
                    }

                    if (isset($else)) {
                        $else->dontAppend = true;
                        $if->cases[]      = $else;

                        return true;
                    }
                }
                $this->seek($s);
            }

            // only retain the first @charset directive encountered
            if ($this->literal('@charset') && $this->valueList($charset) && $this->end()) {
                if (!isset($this->charset)) {
                    $statement = [Type::T_CHARSET,$charset];
                    list($line,$column) = $this->getSourcePosition($s);
                    $statement[static::SOURCE_LINE]   = $line;
                    $statement[static::SOURCE_COLUMN] = $column;
                    $statement[static::SOURCE_INDEX]  = $this->sourceIndex;
                    $this->charset = $statement;
                }
                return true;
            }
            $this->seek($s);
            // doesn't match built in directive, do generic one
            if ($this->literal('@',false) && $this->keyword($dirName) && ($this->variable($dirValue) || $this->openString('{',$dirValue) || true) && $this->literal('{')) {
                if ($dirName === 'media') {
                    $directive = $this->pushSpecialBlock(Type::T_MEDIA,$s);
                } else {
                    $directive       = $this->pushSpecialBlock(Type::T_DIRECTIVE,$s);
                    $directive->name = $dirName;
                }
                if (isset($dirValue)) {
                    $directive->value = $dirValue;
                }
                return true;
            }
            $this->seek($s);
            return false;
        }
        // property shortcut
        // captures most properties before having to parse a selector
        if ($this->keyword($name,false) && $this->literal(': ') && $this->valueList($value) && $this->end()) {
            $name = [Type::T_STRING,'',[$name]];
            $this->append([Type::T_ASSIGN,$name,$value],$s);
            return true;
        }
        $this->seek($s);
        // variable assigns
        if ($this->variable($name) && $this->literal(':') && $this->valueList($value) && $this->end()) {
            // check for '!flag'
            $assignmentFlags = $this->stripAssignmentFlags($value);
            $this->append([Type::T_ASSIGN,$name,$value,$assignmentFlags],$s);
            return true;
        }
        $this->seek($s);
        // misc
        if ($this->literal('-->')) {
            return true;
        }
        // opening css block
        if ($this->selectors($selectors) && $this->literal('{')) {
            $this->pushBlock($selectors,$s);
            return true;
        }
        $this->seek($s);
        // property assign, or nested assign
        if ($this->propertyName($name) && $this->literal(':')) {
            $foundSomething = false;

            if ($this->valueList($value)) {
                $this->append([Type::T_ASSIGN,$name,$value],$s);
                $foundSomething = true;
            }
            if ($this->literal('{')) {
                $propBlock         = $this->pushSpecialBlock(Type::T_NESTED_PROPERTY,$s);
                $propBlock->prefix = $name;
                $foundSomething    = true;
            } elseif ($foundSomething) {
                $foundSomething = $this->end();
            }
            if ($foundSomething) {
                return true;
            }
        }
        $this->seek($s);
        // closing a block
        if ($this->literal('}')) {
            $block = $this->popBlock();

            if (isset($block->type) && $block->type === Type::T_INCLUDE) {
                $include = $block->child;
                unset($block->child);
                $include[3] = $block;
                $this->append($include,$s);
            } elseif (empty($block->dontAppend)) {
                $type = isset($block->type) ? $block->type : Type::T_BLOCK;
                $this->append([$type,$block],$s);
            }
            return true;
        }
        // extra stuff
        if ($this->literal(';') || $this->literal('<!--')) {
            return true;
        }
        return false;
    }

    protected function pushBlock($selectors,$pos = 0) {
        list($line,$column) = $this->getSourcePosition($pos);
        $b               = new Block;
        $b->sourceName   = $this->sourceName;
        $b->sourceLine   = $line;
        $b->sourceColumn = $column;
        $b->sourceIndex  = $this->sourceIndex;
        $b->selectors    = $selectors;
        $b->comments     = [];
        $b->parent       = $this->env;
        if (!$this->env) {
            $b->children = [];
        } elseif (empty($this->env->children)) {
            $this->env->children = $this->env->comments;
            $b->children         = [];
            $this->env->comments = [];
        } else {
            $b->children         = $this->env->comments;
            $this->env->comments = [];
        }
        $this->env = $b;
        return $b;
    }

    protected function pushSpecialBlock($type,$pos) {
        $block       = $this->pushBlock(null,$pos);
        $block->type = $type;
        return $block;
    }

    protected function popBlock() {
        $block = $this->env;
        if (empty($block->parent)) {
            $this->throwParseError('unexpected }');
        }
        $this->env = $block->parent;
        unset($block->parent);
        $comments = $block->comments;
        if (count($comments)) {
            $this->env->comments = $comments;
            unset($block->comments);
        }
        return $block;
    }

    protected function peek($regex,&$out,$from = null) {
        if (!isset($from)) {
            $from = $this->count;
        }
        $r      = '/'.$regex.'/'.$this->patternModifiers;
        $result = preg_match($r,$this->buffer,$out,null,$from);
        return $result;
    }

    protected function seek($where = null) {
        if ($where === null) {
            return $this->count;
        }
        $this->count = $where;
        return true;
    }

    protected function matchString(&$m,$delim) {
        $token = null;
        $end = strlen($this->buffer);
        // look for either ending delim, escape, or string interpolation
        foreach(['#{','\\',$delim] as $lookahead) {
            $pos = strpos($this->buffer,$lookahead,$this->count);
            if ($pos !== false && $pos < $end) {
                $end   = $pos;
                $token = $lookahead;
            }
        }
        if (!isset($token)) {
            return false;
        }
        $match       = substr($this->buffer,$this->count,$end - $this->count);
        $m           = [$match.$token,$match,$token];
        $this->count = $end + strlen($token);
        return true;
    }

    protected function match($regex,&$out,$eatWhitespace = null) {
        if (!isset($eatWhitespace)) {
            $eatWhitespace = $this->eatWhiteDefault;
        }
        $r = '/'.$regex.'/'.$this->patternModifiers;
        if (preg_match($r,$this->buffer,$out,null,$this->count)) {
            $this->count += strlen($out[0]);
            if ($eatWhitespace) {
                $this->whitespace();
            }
            return true;
        }
        return false;
    }

    protected function literal($what,$eatWhitespace = null) {
        if (!isset($eatWhitespace)) {
            $eatWhitespace = $this->eatWhiteDefault;
        }
        $len = strlen($what);
        if (strcasecmp(substr($this->buffer,$this->count,$len),$what) === 0) {
            $this->count += $len;
            if ($eatWhitespace) {
                $this->whitespace();
            }
            return true;
        }
        return false;
    }

    protected function whitespace() {
        $gotWhite = false;
        while(preg_match(static::$whitePattern,$this->buffer,$m,null,$this->count)) {
            if (isset($m[1]) && empty($this->commentsSeen[$this->count])) {
                $this->appendComment([Type::T_COMMENT,$m[1]]);
                $this->commentsSeen[$this->count] = true;
            }
            $this->count += strlen($m[0]);
            $gotWhite    = true;
        }
        return $gotWhite;
    }

    protected function appendComment($comment) {
        $comment[1] = substr(preg_replace(['/^\s+/m','/^(.)/m'],['',' \1'],$comment[1]),1);
        $this->env->comments[] = $comment;
    }

    protected function append($statement,$pos = null) {
        if ($pos !== null) {
            list($line,$column) = $this->getSourcePosition($pos);
            $statement[static::SOURCE_LINE]   = $line;
            $statement[static::SOURCE_COLUMN] = $column;
            $statement[static::SOURCE_INDEX]  = $this->sourceIndex;
        }
        $this->env->children[] = $statement;
        $comments = $this->env->comments;
        if (count($comments)) {
            $this->env->children = array_merge($this->env->children,$comments);
            $this->env->comments = [];
        }
    }

    protected function last() {
        $i = count($this->env->children) - 1;

        if (isset($this->env->children[$i])) {
            return $this->env->children[$i];
        }
    }

    protected function mediaQueryList(&$out) {
        return $this->genericList($out,'mediaQuery',',',false);
    }

    protected function mediaQuery(&$out) {
        $expressions = null;
        $parts       = [];
        if (($this->literal('only') && ($only = true) || $this->literal('not') && ($not = true) || true) && $this->mixedKeyword($mediaType)) {
            $prop = [Type::T_MEDIA_TYPE];
            if (isset($only)) {
                $prop[] = [Type::T_KEYWORD,'only'];
            }
            if (isset($not)) {
                $prop[] = [Type::T_KEYWORD,'not'];
            }
            $media = [Type::T_LIST,'',[]];
            foreach((array)$mediaType as $type) {
                if (is_array($type)) {
                    $media[2][] = $type;
                } else {
                    $media[2][] = [Type::T_KEYWORD,$type];
                }
            }
            $prop[]  = $media;
            $parts[] = $prop;
        }

        if (empty($parts) || $this->literal('and')) {
            $this->genericList($expressions,'mediaExpression','and',false);
            if (is_array($expressions)) {
                $parts = array_merge($parts,$expressions[2]);
            }
        }
        $out = $parts;
        return true;
    }

    protected function mediaExpression(&$out) {
        $s     = $this->seek();
        $value = null;
        if ($this->literal('(') && $this->expression($feature) && ($this->literal(':') && $this->expression($value) || true) && $this->literal(')')) {
            $out = [Type::T_MEDIA_EXPRESSION,$feature];
            if ($value) {
                $out[] = $value;
            }
            return true;
        }
        $this->seek($s);
        return false;
    }

    protected function argValues(&$out) {
        if ($this->genericList($list,'argValue',',',false)) {
            $out = $list[2];
            return true;
        }
        return false;
    }

    protected function argValue(&$out) {
        $s = $this->seek();
        $keyword = null;
        if (!$this->variable($keyword) || !$this->literal(':')) {
            $this->seek($s);
            $keyword = null;
        }
        if ($this->genericList($value,'expression')) {
            $out = [$keyword,$value,false];
            $s   = $this->seek();
            if ($this->literal('...')) {
                $out[2] = true;
            } else {
                $this->seek($s);
            }
            return true;
        }
        return false;
    }

    protected function valueList(&$out) {
        return $this->genericList($out,'spaceList',',');
    }

    protected function spaceList(&$out) {
        return $this->genericList($out,'expression');
    }

    protected function genericList(&$out,$parseItem,$delim = '',$flatten = true) {
        $s     = $this->seek();
        $items = [];
        while($this->$parseItem($value)) {
            $items[] = $value;
            if ($delim) {
                if (!$this->literal($delim)) {
                    break;
                }
            }
        }
        if (count($items) === 0) {
            $this->seek($s);
            return false;
        }
        if ($flatten && count($items) === 1) {
            $out = $items[0];
        } else {
            $out = [Type::T_LIST,$delim,$items];
        }
        return true;
    }

    protected function expression(&$out) {
        $s = $this->seek();
        if ($this->literal('(')) {
            if ($this->literal(')')) {
                $out = [Type::T_LIST,'',[]];

                return true;
            }
            if ($this->valueList($out) && $this->literal(')') && $out[0] === Type::T_LIST) {
                return true;
            }
            $this->seek($s);
            if ($this->map($out)) {
                return true;
            }
            $this->seek($s);
        }
        if ($this->value($lhs)) {
            $out = $this->expHelper($lhs,0);
            return true;
        }
        return false;
    }

    protected function expHelper($lhs,$minP) {
        $operators = static::$operatorPattern;
        $ss          = $this->seek();
        $whiteBefore = isset($this->buffer[$this->count - 1]) && ctype_space($this->buffer[$this->count - 1]);

        while($this->match($operators,$m,false) && static::$precedence[$m[1]] >= $minP) {
            $whiteAfter = isset($this->buffer[$this->count]) && ctype_space($this->buffer[$this->count]);
            $varAfter   = isset($this->buffer[$this->count]) && $this->buffer[$this->count] === '$';
            $this->whitespace();
            $op = $m[1];
            // don't turn negative numbers into expressions
            if ($op === '-' && $whiteBefore && !$whiteAfter && !$varAfter) {
                break;
            }
            if (!$this->value($rhs)) {
                break;
            }
            // peek and see if rhs belongs to next operator
            if ($this->peek($operators,$next) && static::$precedence[$next[1]] > static::$precedence[$op]) {
                $rhs = $this->expHelper($rhs,static::$precedence[$next[1]]);
            }
            $lhs         = [Type::T_EXPRESSION,$op,$lhs,$rhs,$this->inParens,$whiteBefore,$whiteAfter];
            $ss          = $this->seek();
            $whiteBefore = isset($this->buffer[$this->count - 1]) && ctype_space($this->buffer[$this->count - 1]);
        }
        $this->seek($ss);
        return $lhs;
    }

    protected function value(&$out) {
        $s = $this->seek();
        if ($this->literal('not',false) && $this->whitespace() && $this->value($inner)) {
            $out = [Type::T_UNARY,'not',$inner,$this->inParens];
            return true;
        }
        $this->seek($s);
        if ($this->literal('not',false) && $this->parenValue($inner)) {
            $out = [Type::T_UNARY,'not',$inner,$this->inParens];
            return true;
        }
        $this->seek($s);
        if ($this->literal('+') && $this->value($inner)) {
            $out = [Type::T_UNARY,'+',$inner,$this->inParens];
            return true;
        }
        $this->seek($s);
        // negation
        if ($this->literal('-',false) && ($this->variable($inner) || $this->unit($inner) || $this->parenValue($inner))) {
            $out = [Type::T_UNARY,'-',$inner,$this->inParens];
            return true;
        }
        $this->seek($s);

        if ($this->parenValue($out) || $this->interpolation($out) || $this->variable($out) || $this->color($out) || $this->unit($out) || $this->string($out) || $this->func($out) || $this->progid($out)) {
            return true;
        }

        if ($this->keyword($keyword)) {
            if ($keyword === 'null') {
                $out = [Type::T_NULL];
            } else {
                $out = [Type::T_KEYWORD,$keyword];
            }
            return true;
        }
        return false;
    }

    protected function parenValue(&$out) {
        $s = $this->seek();
        $inParens = $this->inParens;

        if ($this->literal('(')) {
            if ($this->literal(')')) {
                $out = [Type::T_LIST,'',[]];
                return true;
            }
            $this->inParens = true;
            if ($this->expression($exp) && $this->literal(')')) {
                $out            = $exp;
                $this->inParens = $inParens;
                return true;
            }
        }
        $this->inParens = $inParens;
        $this->seek($s);
        return false;
    }

    protected function progid(&$out) {
        $s = $this->seek();
        if ($this->literal('progid:',false) && $this->openString('(',$fn) && $this->literal('(')) {
            $this->openString(')',$args,'(');
            if ($this->literal(')')) {
                $out = [Type::T_STRING,'',['progid:',$fn,'(',$args,')']];
                return true;
            }
        }
        $this->seek($s);
        return false;
    }

    protected function func(&$func) {
        $s = $this->seek();
        if ($this->keyword($name,false) && $this->literal('(')) {
            if ($name === 'alpha' && $this->argumentList($args)) {
                $func = [Type::T_FUNCTION,$name,[Type::T_STRING,'',$args]];
                return true;
            }

            if ($name !== 'expression' && !preg_match('/^(-[a-z]+-)?calc$/',$name)) {
                $ss = $this->seek();
                if ($this->argValues($args) && $this->literal(')')) {
                    $func = [Type::T_FUNCTION_CALL,$name,$args];
                    return true;
                }
                $this->seek($ss);
            }
            if (($this->openString(')',$str,'(') || true) && $this->literal(')')) {
                $args = [];
                if (!empty($str)) {
                    $args[] = [null,[Type::T_STRING,'',[$str]]];
                }
                $func = [Type::T_FUNCTION_CALL,$name,$args];
                return true;
            }
        }
        $this->seek($s);
        return false;
    }

    protected function argumentList(&$out) {
        $s = $this->seek();
        $this->literal('(');
        $args = [];
        while($this->keyword($var)) {
            if ($this->literal('=') && $this->expression($exp)) {
                $args[] = [Type::T_STRING,'',[$var.'=']];
                $arg    = $exp;
            } else {
                break;
            }
            $args[] = $arg;
            if (!$this->literal(',')) {
                break;
            }
            $args[] = [Type::T_STRING,'',[', ']];
        }

        if (!$this->literal(')') || !count($args)) {
            $this->seek($s);
            return false;
        }
        $out = $args;
        return true;
    }

    protected function argumentDef(&$out) {
        $s = $this->seek();
        $this->literal('(');
        $args = [];
        while($this->variable($var)) {
            $arg = [$var[1],null,false];
            $ss = $this->seek();
            if ($this->literal(':') && $this->genericList($defaultVal,'expression')) {
                $arg[1] = $defaultVal;
            } else {
                $this->seek($ss);
            }
            $ss = $this->seek();
            if ($this->literal('...')) {
                $sss = $this->seek();

                if (!$this->literal(')')) {
                    $this->throwParseError('... has to be after the final argument');
                }

                $arg[2] = true;
                $this->seek($sss);
            } else {
                $this->seek($ss);
            }

            $args[] = $arg;

            if (!$this->literal(',')) {
                break;
            }
        }
        if (!$this->literal(')')) {
            $this->seek($s);

            return false;
        }
        $out = $args;
        return true;
    }

    protected function map(&$out) {
        $s = $this->seek();
        if (!$this->literal('(')) {
            return false;
        }
        $keys   = [];
        $values = [];
        while($this->genericList($key,'expression') && $this->literal(':') && $this->genericList($value,'expression')) {
            $keys[]   = $key;
            $values[] = $value;

            if (!$this->literal(',')) {
                break;
            }
        }
        if (!count($keys) || !$this->literal(')')) {
            $this->seek($s);

            return false;
        }
        $out = [Type::T_MAP,$keys,$values];
        return true;
    }

    protected function color(&$out) {
        $color = [Type::T_COLOR];
        if ($this->match('(#([0-9a-f]{6})|#([0-9a-f]{3}))',$m)) {
            if (isset($m[3])) {
                $num = hexdec($m[3]);
                foreach([3,2,1] as $i) {
                    $t         = $num & 0xf;
                    $color[$i] = $t << 4 | $t;
                    $num       >>= 4;
                }
            } else {
                $num = hexdec($m[2]);

                foreach([3,2,1] as $i) {
                    $color[$i] = $num & 0xff;
                    $num       >>= 8;
                }
            }
            $out = $color;
            return true;
        }
        return false;
    }

    protected function unit(&$unit) {
        if ($this->match('([0-9]*(\.)?[0-9]+)([%a-zA-Z]+)?',$m)) {
            $unit = new Number($m[1],empty($m[3]) ? '' : $m[3]);
            return true;
        }
        return false;
    }

    protected function string(&$out) {
        $s = $this->seek();

        if ($this->literal('"',false)) {
            $delim = '"';
        } elseif ($this->literal("'",false)) {
            $delim = "'";
        } else {
            return false;
        }
        $content               = [];
        $oldWhite              = $this->eatWhiteDefault;
        $this->eatWhiteDefault = false;
        $hasInterpolation      = false;

        while($this->matchString($m,$delim)) {
            if ($m[1] !== '') {
                $content[] = $m[1];
            }
            if ($m[2] === '#{') {
                $this->count -= strlen($m[2]);

                if ($this->interpolation($inter,false)) {
                    $content[]        = $inter;
                    $hasInterpolation = true;
                } else {
                    $this->count += strlen($m[2]);
                    $content[]   = '#{'; // ignore it
                }
            } elseif ($m[2] === '\\') {
                if ($this->literal('"',false)) {
                    $content[] = $m[2].'"';
                } elseif ($this->literal("'",false)) {
                    $content[] = $m[2]."'";
                } else {
                    $content[] = $m[2];
                }
            } else {
                $this->count -= strlen($delim);
                break; // delim
            }
        }
        $this->eatWhiteDefault = $oldWhite;

        if ($this->literal($delim)) {
            if ($hasInterpolation) {
                $delim = '"';
                foreach($content as &$string) {
                    if ($string === "\\'") {
                        $string = "'";
                    } elseif ($string === '\\"') {
                        $string = '"';
                    }
                }
            }
            $out = [Type::T_STRING,$delim,$content];
            return true;
        }
        $this->seek($s);
        return false;
    }

    protected function mixedKeyword(&$out) {
        $parts = [];
        $oldWhite              = $this->eatWhiteDefault;
        $this->eatWhiteDefault = false;
        for(; ;) {
            if ($this->keyword($key)) {
                $parts[] = $key;
                continue;
            }
            if ($this->interpolation($inter)) {
                $parts[] = $inter;
                continue;
            }
            break;
        }
        $this->eatWhiteDefault = $oldWhite;
        if (count($parts) === 0) {
            return false;
        }
        if ($this->eatWhiteDefault) {
            $this->whitespace();
        }
        $out = $parts;
        return true;
    }

    protected function openString($end,&$out,$nestingOpen = null) {
        $oldWhite              = $this->eatWhiteDefault;
        $this->eatWhiteDefault = false;
        $patt = '(.*?)([\'"]|#\{|'.$this->pregQuote($end).'|'.static::$commentPattern.')';
        $nestingLevel = 0;
        $content = [];

        while($this->match($patt,$m,false)) {
            if (isset($m[1]) && $m[1] !== '') {
                $content[] = $m[1];
                if ($nestingOpen) {
                    $nestingLevel += substr_count($m[1],$nestingOpen);
                }
            }
            $tok = $m[2];
            $this->count -= strlen($tok);
            if ($tok === $end && !$nestingLevel--) {
                break;
            }
            if (($tok === "'" || $tok === '"') && $this->string($str)) {
                $content[] = $str;
                continue;
            }
            if ($tok === '#{' && $this->interpolation($inter)) {
                $content[] = $inter;
                continue;
            }
            $content[]   = $tok;
            $this->count += strlen($tok);
        }
        $this->eatWhiteDefault = $oldWhite;
        if (count($content) === 0) {
            return false;
        }
        // trim the end
        if (is_string(end($content))) {
            $content[count($content) - 1] = rtrim(end($content));
        }
        $out = [Type::T_STRING,'',$content];
        return true;
    }

    protected function interpolation(&$out,$lookWhite = true) {
        $oldWhite              = $this->eatWhiteDefault;
        $this->eatWhiteDefault = true;
        $s = $this->seek();
        if ($this->literal('#{') && $this->valueList($value) && $this->literal('}',false)) {
            if ($lookWhite) {
                $left  = preg_match('/\s/',$this->buffer[$s - 1]) ? ' ' : '';
                $right = preg_match('/\s/',$this->buffer[$this->count]) ? ' ' : '';
            } else {
                $left = $right = false;
            }
            $out                   = [Type::T_INTERPOLATE,$value,$left,$right];
            $this->eatWhiteDefault = $oldWhite;
            if ($this->eatWhiteDefault) {
                $this->whitespace();
            }
            return true;
        }
        $this->seek($s);
        $this->eatWhiteDefault = $oldWhite;
        return false;
    }

    protected function propertyName(&$out) {
        $parts = [];
        $oldWhite              = $this->eatWhiteDefault;
        $this->eatWhiteDefault = false;

        for(; ;) {
            if ($this->interpolation($inter)) {
                $parts[] = $inter;
                continue;
            }

            if ($this->keyword($text)) {
                $parts[] = $text;
                continue;
            }
            if (count($parts) === 0 && $this->match('[:.#]',$m,false)) {
                // css hacks
                $parts[] = $m[0];
                continue;
            }

            break;
        }
        $this->eatWhiteDefault = $oldWhite;
        if (count($parts) === 0) {
            return false;
        }
        // match comment hack
        if (preg_match(static::$whitePattern,$this->buffer,$m,null,$this->count)) {
            if (!empty($m[0])) {
                $parts[]     = $m[0];
                $this->count += strlen($m[0]);
            }
        }
        $this->whitespace(); // get any extra whitespace
        $out = [Type::T_STRING,'',$parts];
        return true;
    }

    protected function selectors(&$out) {
        $s         = $this->seek();
        $selectors = [];
        while($this->selector($sel)) {
            $selectors[] = $sel;

            if (!$this->literal(',')) {
                break;
            }

            while($this->literal(',')) {
                ; // ignore extra
            }
        }

        if (count($selectors) === 0) {
            $this->seek($s);
            return false;
        }
        $out = $selectors;
        return true;
    }

    protected function selector(&$out) {
        $selector = [];
        for(; ;) {
            if ($this->match('[>+~]+',$m)) {
                $selector[] = [$m[0]];
                continue;
            }

            if ($this->selectorSingle($part)) {
                $selector[] = $part;
                $this->match('\s+',$m);
                continue;
            }

            if ($this->match('\/[^\/]+\/',$m)) {
                $selector[] = [$m[0]];
                continue;
            }
            break;
        }
        if (count($selector) === 0) {
            return false;
        }
        $out = $selector;
        return true;
    }

    protected function selectorSingle(&$out) {
        $oldWhite              = $this->eatWhiteDefault;
        $this->eatWhiteDefault = false;
        $parts = [];
        if ($this->literal('*',false)) {
            $parts[] = '*';
        }
        for(; ;) {
            // see if we can stop early
            if ($this->match('\s*[{,]',$m)) {
                $this->count--;
                break;
            }

            $s = $this->seek();

            // self
            if ($this->literal('&',false)) {
                $parts[] = Compiler::$selfSelector;
                continue;
            }

            if ($this->literal('.',false)) {
                $parts[] = '.';
                continue;
            }

            if ($this->literal('|',false)) {
                $parts[] = '|';
                continue;
            }

            if ($this->match('\\\\\S',$m)) {
                $parts[] = $m[0];
                continue;
            }

            // for keyframes
            if ($this->unit($unit)) {
                $parts[] = $unit;
                continue;
            }

            if ($this->keyword($name)) {
                $parts[] = $name;
                continue;
            }

            if ($this->interpolation($inter)) {
                $parts[] = $inter;
                continue;
            }

            if ($this->literal('%',false) && $this->placeholder($placeholder)) {
                $parts[] = '%';
                $parts[] = $placeholder;
                continue;
            }

            if ($this->literal('#',false)) {
                $parts[] = '#';
                continue;
            }
            // a pseudo selector
            if ($this->match('::?',$m) && $this->mixedKeyword($nameParts)) {
                $parts[] = $m[0];
                foreach($nameParts as $sub) {
                    $parts[] = $sub;
                }
                $ss = $this->seek();
                if ($this->literal('(') && ($this->openString(')',$str,'(') || true) && $this->literal(')')) {
                    $parts[] = '(';

                    if (!empty($str)) {
                        $parts[] = $str;
                    }
                    $parts[] = ')';
                } else {
                    $this->seek($ss);
                }
                continue;
            }
            $this->seek($s);
            // attribute selector
            if ($this->literal('[') && ($this->openString(']',$str,'[') || true) && $this->literal(']')) {
                $parts[] = '[';
                if (!empty($str)) {
                    $parts[] = $str;
                }
                $parts[] = ']';
                continue;
            }
            $this->seek($s);
            break;
        }
        $this->eatWhiteDefault = $oldWhite;
        if (count($parts) === 0) {
            return false;
        }
        $out = $parts;
        return true;
    }

    protected function variable(&$out) {
        $s = $this->seek();
        if ($this->literal('$',false) && $this->keyword($name)) {
            $out = [Type::T_VARIABLE,$name];
            return true;
        }
        $this->seek($s);
        return false;
    }

    protected function keyword(&$word,$eatWhitespace = null) {
        if ($this->match($this->utf8 ? '(([\pL\w_\-\*!"\']|[\\\\].)([\pL\w\-_"\']|[\\\\].)*)' : '(([\w_\-\*!"\']|[\\\\].)([\w\-_"\']|[\\\\].)*)',$m,$eatWhitespace)) {
            $word = $m[1];
            return true;
        }
        return false;
    }

    protected function placeholder(&$placeholder) {
        if ($this->match($this->utf8 ? '([\pL\w\-_]+|#[{][$][\pL\w\-_]+[}])' : '([\w\-_]+|#[{][$][\w\-_]+[}])',$m)) {
            $placeholder = $m[1];

            return true;
        }
        return false;
    }

    protected function url(&$out) {
        if ($this->match('(url\(\s*(["\']?)([^)]+)\2\s*\))',$m)) {
            $out = [Type::T_STRING,'',['url('.$m[2].$m[3].$m[2].')']];

            return true;
        }
        return false;
    }

    protected function end() {
        if ($this->literal(';')) {
            return true;
        }
        if ($this->count === strlen($this->buffer) || $this->buffer[$this->count] === '}') {
            // if there is end of file or a closing block next then we don't need a ;
            return true;
        }
        return false;
    }

    protected function stripAssignmentFlags(&$value) {
        $flags = [];
        for($token = &$value; $token[0] === Type::T_LIST && ($s = count($token[2])); $token = &$lastNode) {
            $lastNode = &$token[2][$s - 1];
            while($lastNode[0] === Type::T_KEYWORD && in_array($lastNode[1],['!default','!global'])) {
                array_pop($token[2]);
                $node = end($token[2]);
                $token = $this->flattenList($token);
                $flags[] = $lastNode[1];
                $lastNode = $node;
            }
        }
        return $flags;
    }

    protected function stripOptionalFlag(&$selectors) {
        $optional = false;
        $selector = end($selectors);
        $part     = end($selector);
        if ($part === ['!optional']) {
            array_pop($selectors[count($selectors) - 1]);

            $optional = true;
        }
        return $optional;
    }

    protected function flattenList($value) {
        if ($value[0] === Type::T_LIST && count($value[2]) === 1) {
            return $this->flattenList($value[2][0]);
        }
        return $value;
    }

    protected function to($what,&$out,$until = false,$allowNewline = false) {
        if (is_string($allowNewline)) {
            $validChars = $allowNewline;
        } else {
            $validChars = $allowNewline ? '.' : "[^\n]";
        }
        if (!$this->match('('.$validChars.'*?)'.$this->pregQuote($what),$m,!$until)) {
            return false;
        }
        if ($until) {
            $this->count -= strlen($what); // give back $what
        }
        $out = $m[1];
        return true;
    }

    protected function show() {
        if ($this->peek("(.*?)(\n|$)",$m,$this->count)) {
            return $m[1];
        }
        return '';
    }

    private function pregQuote($what) {
        return preg_quote($what,'/');
    }

    private function extractLineNumbers($buffer) {
        $this->sourcePositions = [0 => 0];
        $prev                  = 0;
        while(($pos = strpos($buffer,"\n",$prev)) !== false) {
            $this->sourcePositions[] = $pos;
            $prev                    = $pos + 1;
        }
        $this->sourcePositions[] = strlen($buffer);
        if (substr($buffer,-1) !== "\n") {
            $this->sourcePositions[] = strlen($buffer) + 1;
        }
    }

    private function getSourcePosition($pos) {
        $low  = 0;
        $high = count($this->sourcePositions);
        while($low < $high) {
            $mid = (int)(($high + $low) / 2);
            if ($pos < $this->sourcePositions[$mid]) {
                $high = $mid - 1;
                continue;
            }
            if ($pos >= $this->sourcePositions[$mid + 1]) {
                $low = $mid + 1;
                continue;
            }
            return [$mid + 1,$pos - $this->sourcePositions[$mid]];
        }
        return [$low + 1,$pos - $this->sourcePositions[$low]];
    }

    private function saveEncoding() {
        if (version_compare(PHP_VERSION,'7.2.0') >= 0) {
            return;
        }
        $iniDirective = 'mbstring'.'.func_overload'; // deprecated in PHP 7.2
        if (ini_get($iniDirective) & 2) {
            $this->encoding = mb_internal_encoding();
            mb_internal_encoding('iso-8859-1');
        }
    }

    private function restoreEncoding() {
        if ($this->encoding) {
            mb_internal_encoding($this->encoding);
        }
    }
}
