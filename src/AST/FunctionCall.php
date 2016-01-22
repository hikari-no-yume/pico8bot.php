<?php declare(strict_types=1);

namespace ajf\pico8bot\AST;

use const ajf\pico8bot\FUNCTIONS;

class FunctionCall extends Node
{
    private $name;
    private $arguments;

    public function __construct(int $line, string $name, array $arguments) {
        parent::__construct($line);

        if (!isset(FUNCTIONS[$name])) {
            throw new \UnexpectedValueException("'$name' is not one of the accepted functions: " . var_export(array_keys(FUNCTIONS), true));
        }

        $functionData = FUNCTIONS[$name];

        if (count($arguments) !== $functionData['arity']) {
            throw new \RangeException("'$name' has an arity of $functionData[arity], but " . count($arguments) . " arguments given");
        }

        foreach ($arguments as $i => $argument) {
            if (!$argument instanceof Node) {
                throw new \UnexpectedValueException("Argument $i is not " . Node::class);
            }
        }

        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getArguments(): array {
        return $this->arguments;
    }

    public function __toString(): string {
        return "$this->name( " . implode(" , ", $this->arguments) . " )";
    }
}
