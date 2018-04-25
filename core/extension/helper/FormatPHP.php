<?php
namespace core\extension\helper;
use core\extension\Extension;

class FormatPHP extends Extension {

    public $indentChar = ' ';
    public $indentSize = 4;
    public $preserveEmptyLines = 1;
    public $indentEmptyLines = false;
    public $breakBeforeElse = false;
    public $breakBeforeCatch = false;
    public $source = '';
    public $tokens;
    public $output;
    protected $lineBreaks = "\n";
    
    public function __construct($source = null) {
        parent::__construct();
        if (isset($source)) {
            $this->source = $source;
        }
    }
    
    public static function format($source) {
        return (new FormatPHP($source))->init();
    }

    public function init() {
        $source = str_replace("\r\n", "\n", $this->source);

        $tokens = token_get_all($source);

        foreach ($tokens as &$token) {
            if (is_array($token)) {
                $token[] = token_name($token[0]);
            }
        }

        $this->tokens = $tokens;

        $this->formatCode($tokens);

        $this->output = $tokens;

        return $this->getOutput($tokens);
    }

    protected function trim(&$token) {
        if (!is_null($token)) {
            if (is_string($token)) {
                $token = trim($token);
            } else {
                $token[1] = trim($token[1]);
            }
        }
    }

    protected function ltrim(&$token) {
        if (!is_null($token)) {
            if (is_string($token)) {
                $token = ltrim($token);
            } else {
                $token[1] = ltrim($token[1]);
            }
        }
    }

    protected function rtrim(&$token) {
        if (!is_null($token)) {
            if (is_string($token)) {
                $token = rtrim($token);
            } else {
                $token[1] = rtrim($token[1]);
            }
        }
    }

    protected function pad(&$token, $before = '', $after = '') {
        if (!is_null($token)) {
            if (is_string($token)) {
                $token = $before . $token . $after;
            } else {
                $token[1] = $before . $token[1] . $after;
            }
        }
    }

    protected function formatCode(&$tokens) {
        $t_count = 0;

        $in_dec = false;
        $in_class_dec = false;
        $in_func_dec = false;
        $in_object = false;
        $in_at = false;
        $in_php = false;
        $in_curly_open = false;
        $in_do_while = false;
        $in_while = false;

        $indentChar = str_repeat($this->indentChar, $this->indentSize);

        $nl = $this->lineBreaks;

        $in_func = false;

        $last = null;
        $lastw = null;

        foreach ($tokens as $k => &$token) {
            if (is_string($token)) {
                $token = str_replace("\r\n", $nl, $token);

                $token = trim($token);

                $id = null;

                switch ($token) {
                    case '{' :
                        $this->rtrim($last);

                        if ($in_dec) {
                            $in_dec = false;

                            $token = ' '.$token . $nl;

                            $t_count++;

                            $token .= str_repeat($indentChar, $t_count);
                        } else {
                            $t_count++;
                            $token = ' ' . $token . $nl . str_repeat($indentChar, $t_count);
                        }
                        break;

                    case '}' :
                        if (!$in_curly_open) {
                            $this->rtrim($last);

                            $t_count--;

                            if ($in_func !== false && $t_count <= $in_func) {
                                $in_func = false;
                            }

                            $token = $nl . str_repeat($indentChar, $t_count) . $token . $nl . str_repeat($indentChar, $t_count);
                        } else {
                            $in_curly_open = false;
                        }
                        break;

                    case ';' :
                        $in_dec = false;
                        $token = $token . $nl . str_repeat($indentChar, $t_count);
                        break;

                    case '.' :
                    case '=' :
                    case '?' :
                    case ':' :
                        $token = ' ' . $token . ' ';
                        break;

                    case '!' :
                    case ',' :
                        $token .= ' ';
                        break;

                    default :
                    //$this->rtrim($last);
                    //$this->trim($token);
                }
            } else {
                list($id, $text) = $token;

                $text = str_replace("\r\n", $nl, $text);

                switch ($id) {
                    case T_OPEN_TAG :
                        //case T_OPEN_TAG_WITH_ECHO :
                        $in_php = true;
                        $text = trim($text);
                        //$this->rtrim($last);
                        $this->trim($lastw);
                        $text .= $nl . str_repeat($indentChar, $t_count);

                        break;

                    case T_CLOSE_TAG :
                        $in_php = false;

                        break;

                    case T_OBJECT_OPERATOR :
                        break;

                    case T_STRING :
                        break;

                    case T_IF :
                        $this->rtrim($last);
                        $text = $nl . str_repeat($indentChar, $t_count) . $text . ' ';
                        break;

                    case T_DO :
                    case T_WHILE :
                    case T_THROW :
                    case T_NEW :
                    case T_RETURN :
                    case T_GLOBAL :
                        $text .= ' ';
                        break;

                    case T_ELSE :
                    case T_ELSEIF :
                        $this->rtrim($last);
                        $this->trim($lastw);

                        if ($this->breakBeforeElse) {
                            $text = $nl . str_repeat($indentChar, $t_count) . $text . ' ';
                        } else {
                            $text = ' ' . $text . ' ';
                        }
                        break;

                    case T_CATCH :
                        $this->rtrim($last);
                        $this->trim($lastw);

                        if ($this->breakBeforeCatch) {
                            $text = $nl . str_repeat($indentChar, $t_count) . $text . ' ';
                        } else {
                            $text = ' ' . $text . ' ';
                        }
                        break;

                    case T_CASE :
                    case T_DEFAULT :
                        $this->rtrim($last);
                        $text = $nl . str_repeat($indentChar, $t_count - 1) . $text . ' ';
                        break;

                    case T_CURLY_OPEN :
                        $in_curly_open = true;
                        break;

                    case T_EXTENDS :
                    case T_IMPLEMENTS :
                        $this->rtrim($last);
                        $text = ' ' . $text . ' ';
                        break;

                    case T_INTERFACE :
                    case T_FUNCTION :
                    case T_CLASS :
                    case T_ABSTRACT :
                    case T_PUBLIC :
                    case T_PRIVATE :
                    case T_PROTECTED :
                    case T_STATIC :

                        if ($id == T_STATIC && $in_func !== false) {
                            // static var inside function
                            $this->rtrim($last);
                            //$text .= ' ';
                            $text = $nl . str_repeat($indentChar, $t_count) . $text . ' ';
                        } else {
                            if ($id == T_FUNCTION && $in_func === false) {
                                $in_func = $t_count;
                            }

                            if ($in_dec) {
                                $text .= ' ';
                            } else {
                                $this->rtrim($last);

                                $text = $nl . str_repeat($indentChar, $t_count) . $text . ' ';
                            }

                            $in_dec = true;
                        }

                        break;

                    case T_CONST :
                        $text = $nl . str_repeat($indentChar, $t_count) . $text . ' ';
                        break;

                    case T_COMMENT :
                        $this->trim($text);
                        $this->rtrim($last);
                        $this->rtrim($lastw);

                        if ($this->isOnSameLine($tokens, $k)) {
                            $text = ' ' . $text . $nl . str_repeat($indentChar, $t_count);
                        } elseif (!$in_func) {
                            $text = $nl . str_repeat($indentChar, $t_count) . $nl . str_repeat($indentChar, $t_count) . $text . $nl . str_repeat($indentChar, $t_count);
                        } else {
                            $text = $nl . str_repeat($indentChar, $t_count) . $text . $nl . str_repeat($indentChar, $t_count);
                        }
                        break;

                    case T_DOC_COMMENT :
                        $result = $nl;
                        $lines = explode("\n", $text);
                        $space = '';
                        foreach ($lines as $line) {
                            $line = trim($line);
                            $result .= str_repeat($indentChar, $t_count) . $space . $line . $nl;
                            $space = ' ';
                        }
                        $text = $result . str_repeat($indentChar, $t_count);
                        break;

                    case T_WHITESPACE :
                        if ($in_func !== false) {
                            $c = substr_count($text, $nl);

                            if ($c) {
                                $text = str_repeat($nl . str_repeat($indentChar, $t_count), min($this->preserveEmptyLines, $c - 1));
                            } else {
                                $this->trim($text);
                            }
                        } else {
                            $this->trim($text);
                        }
                        break;

                    case T_AND_EQUAL :
                    case T_AS :
                    case T_BOOLEAN_AND :
                    case T_BOOLEAN_OR :
                    case T_CONCAT_EQUAL :
                    case T_DIV_EQUAL :
                    case T_DOUBLE_ARROW :
                    case T_IS_EQUAL :
                    case T_IS_GREATER_OR_EQUAL :
                    case T_IS_IDENTICAL :
                    case T_IS_NOT_EQUAL :
                    case T_IS_NOT_IDENTICAL :
                    // case T_SMALLER_OR_EQUAL: // undefined constant ???
                    case T_LOGICAL_AND :
                    case T_LOGICAL_OR :
                    case T_LOGICAL_XOR :
                    case T_MINUS_EQUAL :
                    case T_MOD_EQUAL :
                    case T_MUL_EQUAL :
                    case T_OR_EQUAL :
                    case T_PLUS_EQUAL :
                    case T_SL :
                    case T_SL_EQUAL :
                    case T_SR :
                    case T_SR_EQUAL :
                    case T_START_HEREDOC :
                    case T_XOR_EQUAL :
                        $this->rtrim($last);
                        $text = ' ' . $text . ' ';
                        break;

                    case T_ENCAPSED_AND_WHITESPACE :
                    case T_ARRAY :
                    case T_ARRAY_CAST :
                    //case T_BAD_CHARACTER :
                    case T_BOOL_CAST :
                    case T_BREAK :
                    //case T_CHARACTER :
                    case T_CLONE :
                    case T_CONSTANT_ENCAPSED_STRING :
                    case T_CONTINUE :
                    case T_DEC :
                    case T_DECLARE :
                    case T_DIR :
                    case T_DNUMBER :
                    case T_DOLLAR_OPEN_CURLY_BRACES :
                    case T_DOUBLE_CAST :
                    case T_DOUBLE_COLON :
                    case T_ECHO :
                    case T_EMPTY :
                    case T_ENDDECLARE :
                    case T_ENDFOR :
                    case T_ENDFOREACH :
                    case T_ENDIF :
                    case T_ENDSWITCH :
                    case T_ENDWHILE :
                    case T_END_HEREDOC :
                    case T_EVAL :
                    case T_EXIT :
                    case T_FILE :
                    case T_FINAL :
                    case T_FOR :
                    case T_FOREACH :
                    case T_GOTO :
                    case T_HALT_COMPILER :
                    case T_INC :
                    case T_INCLUDE :
                    case T_INCLUDE_ONCE :
                    case T_INLINE_HTML :
                    case T_INSTANCEOF :
                    case T_INT_CAST :
                    case T_ISSET :
                    case T_IS_SMALLER_OR_EQUAL :
                    case T_LINE :
                    case T_LIST :
                    case T_LNUMBER :
                    case T_METHOD_C :
                    case T_NAMESPACE :
                    case T_NS_C :
                    case T_NS_SEPARATOR :
                    case T_NUM_STRING :
                    case T_OBJECT_CAST :
                    case T_PAAMAYIM_NEKUDOTAYIM :
                    case T_PRINT :
                    case T_REQUIRE :
                    case T_REQUIRE_ONCE :
                    case T_STRING_CAST :
                    case T_STRING_VARNAME :
                        break;

                    // php 4 only
                    //case T_ML_COMMENT : 
                    //case T_OLD_FUNCTION :
                    //break;
                    // PHP >= 4.3
                    case T_FUNC_C :
                    case T_CLASS_C :
                        break;
                }

                $token[1] = $text;
            }

            if ($id != T_WHITESPACE) {
                $last = & $token;
            } else {
                $lastw = &$token;
            }
        }
    }

    protected function isOnSameLine(&$tokens, $k) {
        $j = $k;
        do {
            $j--;

            $token = $tokens[$j];

            if (is_string($token))
                continue;
            if ($token[0] == T_WHITESPACE)
                continue;

            if ($tokens[$k][2] != $token[2])
                return false;

            return true;
        }
        while ($j > 0);
    }

    protected function getOutput(&$tokens) {
        $o = '';
        foreach ($tokens as &$token) {
            $o .= is_string($token) ? $token : $token[1];
        }

        // trim empty lines
        if (!$this->indentEmptyLines) {
            $o = preg_replace('/ +\r?\n/', $this->lineBreaks, $o);
        }

        return $o;
    }

}
