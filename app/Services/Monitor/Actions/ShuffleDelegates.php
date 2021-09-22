<?php

declare(strict_types=1);

namespace App\Services\Monitor\Actions;

use App\Services\Monitor\RoundCalculator;

final class ShuffleDelegates
{
    public static function execute(array $delegates, int $height): array
    {
        $seedSource  = (string) RoundCalculator::calculate($height)['round'];
        $currentSeed = hex2bin(hash('sha256', $seedSource));
        $delCount    = count($delegates);

        // $seeds = [];
        for ($i = 0; $i < $delCount; $i++) {
            // $elements = [];

            for ($x = 0; $x < 4 && $i < $delCount; $i++, $x++) {
                $newIndex             = intval(unpack('C*', $currentSeed)[$x + 1]) % $delCount;
                $b                    = $delegates[$newIndex];
                $delegates[$newIndex] = $delegates[$i];
                $delegates[$i]        = $b;

                // $elements[] = [
                //     'i'        => $i,
                //     'x'        => $x,
                //     'newIndex' => $newIndex,
                // ];
            }

            // $seeds[bin2hex($currentSeed)] = $elements;

            $currentSeed = hex2bin(hash('sha256', $currentSeed));
        }

        // dump([
        //     'SEEDS_EQUAL' => array_keys($seeds) === array_keys(static::EXPECTED['seeds']),
        //     'SEEDS_DIFFS' => array_diff(array_keys($seeds), array_keys(static::EXPECTED['seeds'])),
        // ]);

        // foreach ($seeds as $hash => $actual) {
        //     $expected = static::EXPECTED['seeds'][$hash];

        //     for ($i = 0; $i < count($expected); $i++) {
        //         dump([
        //             'i'        => $expected[$i]['i'] === $actual[$i]['i'],
        //             'x'        => $expected[$i]['x'] === $actual[$i]['x'],
        //             'newIndex' => $expected[$i]['newIndex'] === $actual[$i]['newIndex'],
        //             'expected' => $expected[$i],
        //             'actual'   => $actual[$i],
        //         ]);
        //     }
        // }

        return $delegates;
    }
}
