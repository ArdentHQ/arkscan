<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Facades\Network;
use App\Models\Round;
use App\Services\NumberFormatter;

final class RoundViewModel implements ViewModel
{
    private Round $round;

    public function __construct(Round $round)
    {
        $this->round = $round;
    }

    public function balance(): string
    {
        return NumberFormatter::currency($this->round->balance->toFloat(), Network::currency());
    }
}
