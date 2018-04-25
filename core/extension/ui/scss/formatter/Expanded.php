<?php
namespace core\extension\ui\scss\formatter;
use core\extension\ui\scss\Formatter;
use core\extension\ui\scss\OutputBlock;

class Expanded extends Formatter {
    public function __construct() {
        $this->indentLevel = 0;
        $this->indentChar = '  ';
        $this->break = "\n";
        $this->open = ' {';
        $this->close = '}';
        $this->tagSeparator = ', ';
        $this->assignSeparator = ': ';
        $this->keepSemicolons = true;
    }

    protected function indentStr() {
        return str_repeat($this->indentChar, $this->indentLevel);
    }

    protected function blockLines(OutputBlock $block) {
        $inner = $this->indentStr();
        $glue = $this->break . $inner;
        foreach ($block->lines as $index => $line) {
            if (substr($line, 0, 2) === '/*') {
                $block->lines[$index] = preg_replace('/(\r|\n)+/', $glue, $line);
            }
        }
        $this->write($inner . implode($glue, $block->lines));
        if (empty($block->selectors) || ! empty($block->children)) {
            $this->write($this->break);
        }
    }
}
