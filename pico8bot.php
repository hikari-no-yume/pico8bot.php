<?php declare(strict_types=1);

namespace ajf\pico8bot;

require_once __DIR__ . "/vendor/autoload.php";

if ($argc < 3) {
    die("gib 2 arguments pl0x\n");
}

$expr = $argv[1];
$out = $argv[2];

$tokeniser = new Tokeniser;
$tokens = $tokeniser->tokenise($expr);

$parser = new Parser;
$ast = $parser->parse($tokens);

$compiler = new Compiler;
$closure = $compiler->compile($ast);

$renderer = new Renderer($closure);
$image = $renderer->renderFrame(0, 320, 320);

\imageGIF($image, $out);
