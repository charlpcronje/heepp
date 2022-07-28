<?php
use core\extension\ui\scss\Formatter;
use core\extension\ui\scss\formatter\OutputBlock;

class Crunched extends Formatter {
    public function __construct() {
        $this->indentLevel = 0;
        $this->indentChar = '  ';
        $this->break = '';
        $this->open = '{';
        $this->close = '}';
        $this->tagSeparator = ',';
        $this->assignSeparator = ':';
        $this->keepSemicolons = false;
    }

    public function blockLines(OutputBlock $block) {
        $inner = $this->indentStr();
        $glue = $this->break . $inner;
        foreach ($block->lines as $index => $line) {
            if (substr($line, 0, 2) === '/*') {
                unset($block->lines[$index]);
            }
        }
        $this->write($inner . implode($glue, $block->lines));
        if (! empty($block->children)) {
            $this->write($this->break);
        }
    }
}
