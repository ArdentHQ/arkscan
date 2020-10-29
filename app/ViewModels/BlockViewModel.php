<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Models\Block;
use App\Facades\Network;
use App\Services\Avatar;
use App\Services\Timestamp;
use App\Contracts\ViewModel;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use App\Services\Blockchain\NetworkStatus;

final class BlockViewModel implements ViewModel
{
    use Concerns\Block\HasDelegate;
    use Concerns\Block\HasTransactions;
    use Concerns\Block\InteractsWithNeighbours;

    private Block $block;

    public function __construct(Block $block)
    {
        $this->block = $block;
    }

    public function url(): string
    {
        return route('block', $this->block);
    }

    public function id(): string
    {
        return $this->block->id;
    }

    public function timestamp(): string
    {
        return Timestamp::fromGenesisHuman($this->block->timestamp);
    }

    public function height(): string
    {
        return NumberFormatter::number($this->block->height->toNumber());
    }

    public function reward(): string
    {
        return NumberFormatter::currency($this->block->reward->toFloat(), Network::currency());
    }

    public function rewardFiat(): string
    {
        return ExchangeRate::convert($this->block->reward->toFloat(), $this->block->timestamp);
    }

    public function confirmations(): string
    {
        return NumberFormatter::number(abs(NetworkStatus::height() - $this->block->height->toFloat()));
    }
}
