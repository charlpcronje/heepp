<?php
namespace core\extension\ui\scss\formatter;

class Compact extends Formatter {
    public function __construct() {
        $this->indentLevel = 0;
        $this->indentChar = '';
        $this->break = '';
        $this->open = ' {';
        $this->close = "}\n\n";
        $this->tagSeparator = ',';
        $this->assignSeparator = ':';
        $this->keepSemicolons = true;
    }

    public function indentStr() {
        return ' ';
    }
}
