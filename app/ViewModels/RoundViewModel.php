<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Models\Round;

final class RoundViewModel implements ViewModel
{
    public function __construct(private Round $round)
    {
    }

    public function balance(): float
    {
        return $this->round->balance->toFloat();
    }
}
