<?php declare(strict_types=1);

namespace ajf\pico8bot\AST;

use const ajf\pico8bot\OPERATORS;

class Operator extends Node
{
    private $kind;
    private $left;
    private $right;

    public function __construct(int $line, string $kind, Node $left, Node $right) {
        parent::__construct($line);

        if (!isset(OPERATORS[$kind])) {
            throw new \UnexpectedValueException("'$kind' is not one of the accepted operator types: " . var_export(array_keys(OPERATORS), true));
        }

        $this->kind = $kind;
        $this->left = $left;
        $this->right = $right;
    }

    public function getKind(): string {
        return $this->kind;
    }

    public function getLeftOperand(): Node {
        return $this->left;
    }

    public function getRightOperand(): Node {
        return $this->right;
    }

    public function __toString(): string {
        return "( $this->left $this->kind $this->right )";
    }
}
