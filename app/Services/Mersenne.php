<?php

declare(strict_types=1);

namespace App\Services;

use mersenne_twister\twister;

/**
 * @codeCoverageIgnore
 *
 * TODO: remove in a future PR as it's no longer used. Kept for now so as not to taint
 */
final class Mersenne
{
    private twister $twister;

    public function __construct(int $seed)
    {
        ini_set('precision', '16');

        $this->twister = new twister($seed);
    }

    public function random(): float
    {
        return $this->twister->real_halfopen();
    }
}
