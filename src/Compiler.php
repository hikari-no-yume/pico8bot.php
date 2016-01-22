<?php declare(strict_types=1);

namespace ajf\pico8bot;

class Compiler
{
    private function compileExpression(AST\Node $expression): string
    {
        if ($expression instanceof AST\Constant) {
            return var_export($expression->getValue(), true);
        } else if ($expression instanceof AST\Variable) {
            return '$' . $expression->getName();
        } else if ($expression instanceof AST\Operator) {
            $operatorTemplate = OPERATORS[$expression->getKind()]['expression'];
            $leftString = '(' . $this->compileExpression($expression->getLeftOperand()) . ')';
            $rightString = '(' . $this->compileExpression($expression->getRightOperand()) . ')';
            $string = preg_replace(['/\$a/', '/\$b/'], [$leftString, $rightString], $operatorTemplate);
            return $string;
        } else if ($expression instanceof AST\FunctionCall) {
            $argumentStrings = array_map([$this, 'compileExpression'], $expression->getArguments());
            $string = FUNCTIONS[$expression->getName()]['phpFunction'] . '(' . implode(',', $argumentStrings) . ')';
            return $string;
        } else {
            throw new \Exception("Unexpected node type: {getclass($expression)}");
        }
    }
    
    public function compile(AST\Node $expression): \Closure
    {
        $arguments = implode(',', array_map(function (string $variable): string {
            return '$' . $variable;
        }, VARIABLES));
        $body = 'return ' . $this->compileExpression($expression) . ';';
        $closureExpression = 'function(' . $arguments . '){' . $body . '}';
        return eval('return ' . $closureExpression . ';');
    }
}
