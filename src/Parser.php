<?php declare(strict_types=1);

namespace ajf\pico8bot;

use SplQueue as Queue;
use SplStack as Stack;

class Parser
{
    private function isWhitespace(Token $token): bool {
        return $token->getId() === T_WHITESPACE;
    }

    private function isNumber(Token $token): bool {
        return $token->getId() === T_LNUMBER || $token->getId() == T_DNUMBER;
    }
    
    private function isVariable(Token $token): bool {
        return $token->getId() === T_STRING && (false !== array_search($token->getContent(), VARIABLES, true));
    }

    private function isFunctionName(Token $token): bool {
        return $token->getId() === T_STRING && isset(FUNCTIONS[$token->getContent()]); 
    }

    private function isOperator(Token $token): bool {
        return isset(OPERATORS[$token->getId()]);
    }

    private function isComma(Token $token): bool {
        return $token->getId() === ",";
    }

    private function isLeftParenthesis(Token $token): bool {
        return $token->getId() === "(";
    }

    private function isRightParenthesis(Token $token): bool {
        return $token->getId() === ")";
    }

    private function isParenthesis(Token $token): bool {
        return $this->isLeftParenthesis($token) || $this->isRightParenthesis($token);
    }

    private function shuntingYard(Queue $tokens): Queue {
        // Fasten your seatbelts, we're using a shunting-yard algorithm!
        // It's an elegant (of course, it's Dijkstra) algorithm by Dijkstra
        // Using it, we can convert infix notation into reverse polish notation
        // https://en.wikipedia.org/wiki/Shunting-yard_algorithm

        $output = new Queue;
        $stack = new Stack;

        while (!$tokens->isEmpty()) {
            $token = $tokens->dequeue();
            if ($this->isWhitespace($token)) {
                continue;
            } else if ($this->isNumber($token) || $this->isVariable($token)) {
                $output->enqueue($token);
            } else if ($this->isFunctionName($token)) {
                $stack->push($token);
            } else if ($this->isComma($token)) {
                $stackToken = $stack->top();
                while (!$this->isLeftParenthesis($stackToken)) {
                    if ($stack->isEmpty()) {
                        throw new ParseException("Misplaced comma or mismatched parentheses");
                    }
                    $stackToken = $stack->pop();
                    if (!$this->isWhitespace($stackToken)) {
                        $output->enqueue($stackToken);
                    }
                    $stackToken = $stack->top();
                } 
            } else if ($this->isOperator($token)) {
                if (!$stack->isEmpty()) {
                    $stackToken = $stack->top();
                    // TODO: handle right-associativity, we assume left here
                    while ($this->isOperator($stackToken)
                        && OPERATORS[$token->getId()]['precedence'] <= OPERATORS[$stackToken->getId()]['precedence']) {
                        $output->enqueue($stack->pop());
                        if ($stack->isEmpty()) {
                            break;
                        }
                        $stackToken = $stack->top();
                    }
                }
                $stack->push($token);
            } else if ($this->isLeftParenthesis($token)) {
                $stack->push($token);
            } else if ($this->isRightParenthesis($token)) {
                $stackToken = $stack->top();
                while (!$this->isLeftParenthesis($stackToken)) {
                    if ($stack->isEmpty()) {
                        throw new ParseException("Mismatched parentheses");
                    }
                    $stackToken = $stack->pop();
                    if (!$this->isWhitespace($stackToken)) {
                        $output->enqueue($stackToken);
                    }
                    $stackToken = $stack->top();
                }

                $stack->pop();

                if ($this->isFunctionName($stack->top())) {
                    $output->enqueue($stack->pop());
                }
            } else {
                throw new ParseException("Unexpected token: {$token->getName()} on line {$token->getLine()}");
            }
        }

        while (!$stack->isEmpty()) {
            $stackToken = $stack->pop();
            if ($this->isParenthesis($stackToken)) {
                throw new ParseException("Mismatched parentheses");
            }
            $output->enqueue($stackToken);
        }

        return $output;
    }

    private function constructAST(Queue $tokens): AST\Node {
        if ($tokens->isEmpty()) {
            throw new ParseException("Unexpected end of file");
        }

        $stack = new Stack;
        
        while (!$tokens->isEmpty()) {
            $token = $tokens->bottom();
            if ($this->isNumber($token)) {
                $tokens->dequeue();
                $stack->push(new AST\Constant($token->getLine(), (float)$token->getContent()));
            } else if ($this->isVariable($token)) {
                $tokens->dequeue();
                $stack->push(new AST\Variable($token->getLine(), $token->getContent()));
            } else if ($this->isOperator($token)) {
                $tokens->dequeue();
                $right = $stack->pop();
                $left = $stack->pop();
                $stack->push(new AST\Operator($left->getLine(), $token->getId(), $left, $right));
            } else if ($this->isFunctionName($token)) {
                $tokens->dequeue();
                $arguments = [];
                for ($i = FUNCTIONS[$token->getContent()]['arity'] - 1; $i >= 0;  $i--) {
                    $arguments[] = $stack->pop();
                }
                $stack->push(new AST\FunctionCall($token->getLine(), $token->getContent(), $arguments));
            } else {
                throw new ParseException("Unexpected token {$token->getName()} on line {$token->getLine()}");
            }
        }

        if ($stack->isEmpty()) {
            throw new ParseException("??????");
        }
        if ($stack->count() !== 1) {
            throw new ParseException("Missing operator");
        }

        return $stack->pop();
    }

    public function parse(Queue $tokens): AST\Node {
        // convert to Polish Notation first
        $queue = $this->shuntingYard($tokens);

        // construct AST
        return $this->constructAST($queue);
    }
}
