<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use App\Services\BigNumber;

interface GasTracker
{
    public function low(): BigNumber;

    public function average(): BigNumber;

    public function high(): BigNumber;
}
