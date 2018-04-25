<?php
namespace core\extension\ui\scss;
use core\extension\ui\scss\formatter\OutputBlock;
use core\extension\ui\scss\sourcemap\SourceMapGenerator;

abstract class Formatter {
    public $indentLevel;
    public $indentChar;
    public $break;
    public $open;
    public $close;
    public $tagSeparator;
    public $assignSeparator;
    public $keepSemicolons;
    protected $currentBlock;
    protected $currentLine;
    protected $currentColumn;
    protected $sourceMapGenerator;
    abstract public function __construct();

    protected function indentStr() {
        return '';
    }

    public function property($name, $value) {
        return rtrim($name) . $this->assignSeparator . $value . ';';
    }

    public function stripSemicolon(&$lines) {
        if ($this->keepSemicolons) {
            return;
        }
        if (($count = count($lines))
            && substr($lines[$count - 1], -1) === ';'
        ) {
            $lines[$count - 1] = substr($lines[$count - 1], 0, -1);
        }
    }

    protected function blockLines(OutputBlock $block) {
        $inner = $this->indentStr();
        $glue = $this->break . $inner;
        $this->write($inner . implode($glue, $block->lines));
        if (! empty($block->children)) {
            $this->write($this->break);
        }
    }

    protected function blockSelectors(OutputBlock $block) {
        $inner = $this->indentStr();
        $this->write($inner
            . implode($this->tagSeparator, $block->selectors)
            . $this->open . $this->break);
    }

    protected function blockChildren(OutputBlock $block) {
        foreach ($block->children as $child) {
            $this->block($child);
        }
    }

    protected function block(OutputBlock $block) {
        if (empty($block->lines) && empty($block->children)) {
            return;
        }
        $this->currentBlock = $block;
        $pre = $this->indentStr();
        if (! empty($block->selectors)) {
            $this->blockSelectors($block);
            $this->indentLevel++;
        }
        if (! empty($block->lines)) {
            $this->blockLines($block);
        }
        if (! empty($block->children)) {
            $this->blockChildren($block);
        }
        if (! empty($block->selectors)) {
            $this->indentLevel--;

            if (empty($block->children)) {
                $this->write($this->break);
            }
            $this->write($pre . $this->close . $this->break);
        }
    }

    public function format(OutputBlock $block, SourceMapGenerator $sourceMapGenerator = null) {
        $this->sourceMapGenerator = null;
        if ($sourceMapGenerator) {
            $this->currentLine = 1;
            $this->currentColumn = 0;
            $this->sourceMapGenerator = $sourceMapGenerator;
        }
        ob_start();
        $this->block($block);
        $out = ob_get_clean();
        return $out;
    }

    protected function write($str) {
        if ($this->sourceMapGenerator) {
            $this->sourceMapGenerator->addMapping(
                $this->currentLine,
                $this->currentColumn,
                $this->currentBlock->sourceLine,
                $this->currentBlock->sourceColumn - 1, //columns from parser are off by one
                $this->currentBlock->sourceName
            );

            $lines = explode("\n", $str);
            $lineCount = count($lines);
            $this->currentLine += $lineCount-1;
            $lastLine = array_pop($lines);
            $this->currentColumn = ($lineCount === 1 ? $this->currentColumn : 0) + strlen($lastLine);
        }
        echo $str;
    }
}
