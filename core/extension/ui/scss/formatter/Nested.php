<?php
namespace core\extension\ui\scss\formatter;
use core\extension\ui\scss\Formatter;
use core\extension\ui\scss\formatter\OutputBlock;

class Nested extends Formatter {
    private $depth;

    public function __construct() {
        $this->indentLevel = 0;
        $this->indentChar = '  ';
        $this->break = "\n";
        $this->open = ' {';
        $this->close = ' }';
        $this->tagSeparator = ', ';
        $this->assignSeparator = ': ';
        $this->keepSemicolons = true;
    }

    protected function indentStr() {
        $n = $this->depth - 1;

        return str_repeat($this->indentChar, max($this->indentLevel + $n, 0));
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
        foreach ($block->children as $i => $child) {
            $this->block($child);
            if ($i < count($block->children) - 1) {
                $this->write($this->break);
                if (isset($block->children[$i + 1])) {
                    $next = $block->children[$i + 1];

                    if ($next->depth === max($block->depth, 1) && $child->depth >= $next->depth) {
                        $this->write($this->break);
                    }
                }
            }
        }
    }

    protected function block(OutputBlock $block) {
        if ($block->type === 'root') {
            $this->adjustAllChildren($block);
        }

        if (empty($block->lines) && empty($block->children)) {
            return;
        }
        $this->currentBlock = $block;
        $this->depth = $block->depth;
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
            $this->write($this->close);
        }

        if ($block->type === 'root') {
            $this->write($this->break);
        }
    }

    private function adjustAllChildren(OutputBlock $block) {
        // flatten empty nested blocks
        $children = [];
        foreach ($block->children as $i => $child) {
            if (empty($child->lines) && empty($child->children)) {
                if (isset($block->children[$i + 1])) {
                    $block->children[$i + 1]->depth = $child->depth;
                }
                continue;
            }
            $children[] = $child;
        }
        $count = count($children);
        for ($i = 0; $i < $count; $i++) {
            $depth = $children[$i]->depth;
            $j = $i + 1;
            if (isset($children[$j]) && $depth < $children[$j]->depth) {
                $childDepth = $children[$j]->depth;

                for (; $j < $count; $j++) {
                    if ($depth < $children[$j]->depth && $childDepth >= $children[$j]->depth) {
                        $children[$j]->depth = $depth + 1;
                    }
                }
            }
        }
        $block->children = $children;
        // make relative to parent
        foreach ($block->children as $child) {
            $this->adjustAllChildren($child);
            $child->depth = $child->depth - $block->depth;
        }
    }
}
