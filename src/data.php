<?php declare(strict_types=1);

namespace ajf\pico8bot;

const OPERATORS = [
    '+' => [
        'precedence' => 1,
        'expression' => '$a+$b'
    ],
    '-' => [
        'precedence' => 1,
        'expression' => '$a-$b'
    ],
    '*' => [
        'precedence' => 2,
        'expression' => '$a*$b'
    ],
    '/' => [
        'precedence' => 2,
        'expression' => '@($a/$b)'
    ],
    '%' => [
        'precedence' => 2,
        'expression' => '\ajf\pico8bot\Evaluator::mod($a,$b)'
    ]
];

const FUNCTIONS = [
    'sin' => [
        'arity' => 1,
        'phpFunction' => 'sin'
    ],
    'cos' => [
        'arity' => 1,
        'phpFunction' => 'cos'
    ],
    'abs' => [
        'arity' => 1,
        'phpFunction' => 'abs'
    ],
    'atan2' => [
        'arity' => 2,
        'phpFunction' => 'atan2'
    ],
    'sqrt' => [
        'arity' => 1,
        'phpFunction' => 'sqrt'
    ],
    'flr' => [
        'arity' => 1,
        'phpFunction' => 'floor'
    ],
    'max' => [
        'arity' => 2,
        'phpFunction' => 'max'
    ],
    'min' => [
        'arity' => 2,
        'phpFunction' => 'min'
    ]
];

const VARIABLES = ['x', 'y', 't'];
