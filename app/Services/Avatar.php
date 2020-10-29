<?php

declare(strict_types=1);

namespace App\Services;

use mersenne_twister\twister;

// See https://github.com/vechain/picasso/blob/master/src/index.ts

final class Avatar
{
    public static function make(string $seed): string
    {
        // Seed
        $twister = new twister(static::hash($seed));

        // We might just do it based on the colors of the bridgechain
        $defaultColors = [
            ['rgb(244, 67, 54)'],
            ['rgb(233, 30, 99)'],
            ['rgb(156, 39, 176)'],
            ['rgb(103, 58, 183)'],
            ['rgb(63, 81, 181)'],
            ['rgb(33, 150, 243)'],
            ['rgb(3, 169, 244)'],
            ['rgb(0, 188, 212)'],
            ['rgb(0, 150, 136)'],
            ['rgb(76, 175, 80)'],
            ['rgb(139, 195, 74)'],
            ['rgb(205, 220, 57)'],
            ['rgb(255, 193, 7)'],
            ['rgb(255, 152, 0)'],
            ['rgb(255, 87, 34)'],
        ];

        $genColor = function () use ($defaultColors, $twister): string {
            $index = floor(count($defaultColors) * $twister->real_closed());

            // Still missing the array_splice() equivalent there
            return $defaultColors[$index][0];
        };

        $backgroundString = '<rect fill="'.$genColor().'" width="100" height="100"/>';
        $styleString      = '<style>circle{mix-blend-mode:soft-light;}</style>';
        $shapeString      = '';
        $layers           = 3;
        $rs               = [35, 40, 45, 50, 55, 60];
        $cxs              = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100];
        $cys              = [30, 40, 50, 60, 70];

        for ($i = 0; $i < $layers; $i++) {
            $value = floor(count($defaultColors) * $twister->real_closed());

            $r    = $rs[floor(count($rs) * $twister->real_closed())];
            $cx   = $cxs[floor(count($cxs) * $twister->real_closed())];
            $cy   = $cys[floor(count($cys) * $twister->real_closed())];
            $fill = $genColor();

            $shapeString .= '<circle r="'.$r.'" cx="'.$cx.'" cy="'.$cy.'" fill="'.$fill.'"/>';
        }

        return sprintf(
            "<svg version='1.1' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'>%s%s%s</svg>",
            $styleString,
            $backgroundString,
            $shapeString
        );
    }

    private static function hash(string $value): int
    {
        if (strlen($value) === 0) {
            return 0;
        }

        $h = 0;

        for ($i = 0; $i < strlen($value); $i++) {
            $h = $h * 31 + ord($value[$i]);
            $h = $h % (2 ** 32);
        }

        return $h;
    }
}
