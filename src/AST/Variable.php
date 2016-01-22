<?php declare(strict_types=1);

namespace ajf\pico8bot\AST;

use const ajf\pico8bot\VARIABLES;

class Variable extends Node
{
    private $name;

    public function __construct(int $line, string $name) {
        parent::__construct($line);
        
        if (false === array_search($name, VARIABLES, true)) {
            throw new \UnexpectedValueException("'$name' is not one of the defined variables: " . var_export(VARIABLES, true));
        }

        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }

    public function __toString(): string {
        return $this->name;
    }
}
