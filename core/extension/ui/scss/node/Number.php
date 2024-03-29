<?php
namespace core\extension\ui\scss\node;
use core\extension\ui\scss\Compiler;
use core\extension\ui\scss\Node;
use core\extension\ui\scss\Type;

class Number extends Node implements \ArrayAccess {
    static public $precision = 10;
    static protected $unitTable = [
        'in' => [
            'in' => 1,
            'pc' => 6,
            'pt' => 72,
            'px' => 96,
            'cm' => 2.54,
            'mm' => 25.4,
            'q'  => 101.6,
        ],
        'turn' => [
            'deg'  => 360,
            'grad' => 400,
            'rad'  => 6.28318530717958647692528676, // 2 * M_PI
            'turn' => 1,
        ],
        's' => [
            's'  => 1,
            'ms' => 1000,
        ],
        'Hz' => [
            'Hz'  => 1,
            'kHz' => 0.001,
        ],
        'dpi' => [
            'dpi'  => 1,
            'dpcm' => 2.54,
            'dppx' => 96,
        ],
    ];
    public $dimension;
    public $units;
    public function __construct($dimension, $initialUnit) {
        $this->type      = Type::T_NUMBER;
        $this->dimension = $dimension;
        $this->units     = is_array($initialUnit) ? $initialUnit : ($initialUnit ? [$initialUnit => 1] : []);
    }

    public function coerce($units) {
        if ($this->unitless()) {
            return new Number($this->dimension, $units);
        }
        $dimension = $this->dimension;
        foreach (static::$unitTable['in'] as $unit => $conv) {
            $from       = isset($this->units[$unit]) ? $this->units[$unit] : 0;
            $to         = isset($units[$unit]) ? $units[$unit] : 0;
            $factor     = pow($conv, $from - $to);
            $dimension /= $factor;
        }
        return new Number($dimension, $units);
    }

    public function normalize() {
        $dimension = $this->dimension;
        $units     = [];
        $this->normalizeUnits($dimension, $units, 'in');
        return new Number($dimension, $units);
    }

    public function offsetExists($offset):bool {
        if ($offset === -3) {
            return $this->sourceColumn !== null;
        }
        if ($offset === -2) {
            return $this->sourceLine !== null;
        }
        if ($offset === -1
            || $offset === 0
            || $offset === 1
            || $offset === 2
        ) {
            return true;
        }
        return false;
    }

    public function offsetGet($offset):mixed {
        switch ($offset) {
            case -3:
                return $this->sourceColumn;
            case -2:
                return $this->sourceLine;
            case -1:
                return $this->sourceIndex;
            case 0:
                return $this->type;
            case 1:
                return $this->dimension;
            case 2:
                return $this->units;
        }
    }

    public function offsetSet($offset, $value):void {
        if ($offset === 1) {
            $this->dimension = $value;
        } elseif ($offset === 2) {
            $this->units = $value;
        } elseif ($offset == -1) {
            $this->sourceIndex = $value;
        } elseif ($offset == -2) {
            $this->sourceLine = $value;
        } elseif ($offset == -3) {
            $this->sourceColumn = $value;
        }
    }

    public function offsetUnset($offset):void {
        if ($offset === 1) {
            $this->dimension = null;
        } elseif ($offset === 2) {
            $this->units = null;
        } elseif ($offset === -1) {
            $this->sourceIndex = null;
        } elseif ($offset === -2) {
            $this->sourceLine = null;
        } elseif ($offset === -3) {
            $this->sourceColumn = null;
        }
    }

    public function unitless() {
        return ! array_sum($this->units);
    }

    public function unitStr() {
        $numerators   = [];
        $denominators = [];
        foreach ($this->units as $unit => $unitSize) {
            if ($unitSize > 0) {
                $numerators = array_pad($numerators, count($numerators) + $unitSize, $unit);
                continue;
            }
            if ($unitSize < 0) {
                $denominators = array_pad($denominators, count($denominators) + $unitSize, $unit);
                continue;
            }
        }
        return implode('*', $numerators) . (count($denominators) ? '/' . implode('*', $denominators) : '');
    }

    public function output(Compiler $compiler = null) {
        $dimension = round($this->dimension, static::$precision);
        $units = array_filter($this->units, function ($unitSize) {
            return $unitSize;
        });
        if (count($units) > 1 && array_sum($units) === 0) {
            $dimension = $this->dimension;
            $units     = [];
            $this->normalizeUnits($dimension, $units, 'in');
            $dimension = round($dimension, static::$precision);
            $units     = array_filter($units, function ($unitSize) {
                return $unitSize;
            });
        }
        $unitSize = array_sum($units);
        if ($compiler && ($unitSize > 1 || $unitSize < 0 || count($units) > 1)) {
            $compiler->throwError((string) $dimension . $this->unitStr() . " isn't a valid CSS value.");
        }
        reset($units);
        $unit = key($units);
        $dimension = number_format($dimension, static::$precision, '.', '');
        return (static::$precision ? rtrim(rtrim($dimension, '0'), '.') : $dimension) . $unit;
    }

    public function __toString() {
        return $this->output();
    }

    private function normalizeUnits(&$dimension, &$units, $baseUnit = 'in') {
        $dimension = $this->dimension;
        $units     = [];
        foreach ($this->units as $unit => $exp) {
            if (isset(static::$unitTable[$baseUnit][$unit])) {
                $factor = pow(static::$unitTable[$baseUnit][$unit], $exp);

                $unit = $baseUnit;
                $dimension /= $factor;
            }
            $units[$unit] = $exp + (isset($units[$unit]) ? $units[$unit] : 0);
        }
    }
}
