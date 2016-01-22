pico8bot, in PHP
================

I think [@pico8bot](https://twitter.com/pico8bot) on Twitter is pretty cool. Just for fun, I've reimplemented the image generation algorithm in PHP.

I looked at the original source code to find out how the `%` operator works, the colour palette, the number of frames, how `x` and `y` are set, and the width and height: https://github.com/Objelisks/picobot/blob/master/bot.js

![Screenshot!](blob/master/screenshot.png)

Capabilities
------------

* Full language support:
   * Operators: `+`, `-`, `*`, `/`, `%`
   * Functions: `sin`, `cos`, `abs`, `tan2`, `sqrt`, `floor`, `max`, `min`
   * Variables: `x`, `y`, `t`
* Uses PHP's tokeniser because I'm lazy
* Shunting-yard parser which generates abstract syntax tree
* Two evaluation methods:
   * Naïve recursive evaluator
   * JIT compilation to PHP code

Requirements
------------

PHP >= 7.0

PHP Gd extension

Usage
-----

    $ php pico8bot.php <expresssion> <outfile name>

Currently doesn't do animation or give a choice of evaluator (it always uses JIT).

e.g.

    $ php picto8bot.php 'sin(2.07*x)*cos(9.76%8.24)*flr(max(y, 2.51))' test.gif
