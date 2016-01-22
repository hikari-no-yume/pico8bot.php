<?php declare(strict_types=1);

namespace ajf\pico8bot;

class Evaluator
{
    // referenced from OPERATORS constant
    public static function mod(float $a, float $b): float {
        if ($b === 0) {
            return NAN;
        }
        return $a - $b * floor(@($a / $b));
    }

    public function evaluate(AST\Node $expression, array $environment): float {
        if ($expression instanceof AST\Constant) {
            return $expression->getValue();
        } else if ($expression instanceof AST\Variable) {
            if (!isset($environment[$expression->getName()])) {
                throw new \Exception("'{$expression->getName()} must be defined!");
            }
            return $environment[$expression->getName()];
        } else if ($expression instanceof AST\Operator) {
            static $cachedOperators = [];
            if (!isset($cachedOperators[$expression->getKind()])) {
                $operatorFunction = create_function('$a, $b', 'return ' . OPERATORS[$expression->getKind()]['expression'] . ';');
                $cachedOperators[$expression->getKind()] = $operatorFunction;
            } else {
                $operatorFunction = $cachedOperators[$expression->getKind()];
            }
            $left = $this->evaluate($expression->getLeftOperand(), $environment);
            $right = $this->evaluate($expression->getRightOperand(), $environment);
            return $operatorFunction($left, $right);
        } else if ($expression instanceof AST\FunctionCall) {
            $arguments = array_map(function ($argument) use ($environment) {
                return $this->evaluate($argument, $environment);
            }, $expression->getArguments());
            return FUNCTIONS[$expression->getName()]['phpFunction'](...$arguments);
        } else {
            throw new \Exception("Unexpected node type " . get_class($expression));
        }
    }
}
