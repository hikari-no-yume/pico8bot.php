<?php declare(strict_types=1);

namespace ajf\pico8bot;

class Renderer
{
    const PICO_8_PALETTE = [
        [0, 0, 0],
        [29, 43, 83],
        [129, 37, 83],
        [0, 135, 81],
        [171, 82, 54],
        [95, 87, 79],
        [194, 195, 199],
        [255, 241, 232],
        [255, 0, 77],
        [255, 163, 0],
        [255, 255, 39],
        [0, 231, 86],
        [41, 173, 255],
        [131, 118, 156],
        [255, 119, 168],
        [255, 204, 170]
    ];

    private $function;

    private static function mod(float $a, float $b): int {
        return (int)($a - $b * floor($a / $b));
    }

    public function __construct(callable $function) {
        $this->function = $function;
    }

    public function renderFrame(float $t, int $width, int $height) /* : resource (Gd) */ {
        $image = \imageCreate($width, $height);
        $palette = [];
        foreach (self::PICO_8_PALETTE as list($red, $green, $blue)) {
            $palette[] = \imageColorAllocate($image, $red, $green, $blue);
        }
        
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $value = ($this->function)(($x - $width / 2), ($y - $height / 2), $t);
                \imageSetPixel($image, $x, $y, $palette[self::mod(abs(floor($value)), count($palette))]);
            }
        }

        return $image;
    }

    public function renderFrames(int $frames, int $width, int $height): array {
        $frames = [];
        for ($t = 0; $t < $frames; $t++) {
            $frames[] = $this->renderFrame($width, $height, $t);
        }
        return $frames;
    }
}
