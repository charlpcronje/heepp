<?php
namespace core\extension\ui\scss\base;

class Range {
    public $first;
    public $last;

    public function __construct($first,$last) {
        $this->first = $first;
        $this->last  = $last;
    }

    public function includes($value) {
        return $value >= $this->first && $value <= $this->last;
    }
}
