<?php declare(strict_types=1);

namespace ajf\pico8bot\AST;

class Constant extends Node
{
    private $value;

    public function __construct(int $line, float $value) {
        parent::__construct($line);

        $this->value = $value;
    }

    public function getValue(): float {
        return $this->value;
    }

    public function __toString(): string {
        return var_export($this->value, true);
    }
}
