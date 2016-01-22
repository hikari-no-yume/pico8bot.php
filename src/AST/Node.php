<?php declare(strict_types=1);

namespace ajf\pico8bot\AST;

abstract class Node
{
    private $line;

    public function __construct(int $line) {
        $this->line = $line;
    }

    public function getLine(): int {
        return $this->line;
    }
}
