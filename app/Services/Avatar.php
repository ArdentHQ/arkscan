<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

// See https://github.com/vechain/picasso/blob/master/src/index.ts

final class Avatar
{
    public static function make(string $seed): string
    {
        return Cache::tags('avatar')->remember($seed, now()->addHour(), function () use ($seed): string {
            $defaultColors = [
                'rgb(244, 67, 54)',
                'rgb(233, 30, 99)',
                'rgb(156, 39, 176)',
                'rgb(103, 58, 183)',
                'rgb(63, 81, 181)',
                'rgb(33, 150, 243)',
                'rgb(3, 169, 244)',
                'rgb(0, 188, 212)',
                'rgb(0, 150, 136)',
                'rgb(76, 175, 80)',
                'rgb(139, 195, 74)',
                'rgb(205, 220, 57)',
                'rgb(255, 193, 7)',
                'rgb(255, 152, 0)',
                'rgb(255, 87, 34)',
            ];

            $twister = new Mersenne(static::hash($seed));

            $genColor = function () use (&$defaultColors, $twister): string {
                $index = (int) floor(count($defaultColors) * $twister->random());

                return array_splice($defaultColors, $index, 1)[0];
            };

            $backgroundString = '<rect fill="'.$genColor().'" width="100" height="100"/>';
            $styleString      = '<style>.picasso circle{mix-blend-mode:soft-light;}</style>';
            $shapeString      = '';
            $layers           = 3;
            $rs               = [35, 40, 45, 50, 55, 60];
            $cxs              = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100];
            $cys              = [30, 40, 50, 60, 70];

            for ($i = 0; $i < $layers; $i++) {
                $r    = array_splice($rs, (int) floor(count($rs) * $twister->random()), 1)[0];
                $cx   = array_splice($cxs, (int) floor(count($cxs) * $twister->random()), 1)[0];
                $cy   = array_splice($cys, (int) floor(count($cys) * $twister->random()), 1)[0];
                $fill = $genColor();

                $shapeString .= '<circle r="'.$r.'" cx="'.$cx.'" cy="'.$cy.'" fill="'.$fill.'"/>';
            }

            return sprintf(
                "<svg version='1.1' xmlns='http://www.w3.org/2000/svg' class='picasso' viewBox='0 0 100 100'>%s%s%s</svg>",
                $styleString,
                $backgroundString,
                $shapeString
            );
        });
    }

    private static function hash(string $value): int
    {
        $h = 0;

        for ($i = 0; $i < strlen($value); $i++) {
            $h = $h * 31 + ord($value[$i]);
            $h = $h % (2 ** 32);
        }

        return $h;
    }
}
