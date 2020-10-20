<?php

declare(strict_types=1);

namespace App\Contracts;

interface Aggregate
{
    public function aggregate(): string;
}
