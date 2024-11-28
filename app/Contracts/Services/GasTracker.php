<?php

declare(strict_types=1);

namespace App\Contracts\Services;

interface GasTracker
{
    public function low(): float;

    public function average(): float;

    public function high(): float;
}
