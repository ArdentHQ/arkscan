<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Facades\Network;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Blockchain\NetworkStatus;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

final class BlockViewModel implements ViewModel
{
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

    public function delegate(): Wallet
    {
        return Cache::remember(
            "block:delegate:{$this->block->id}",
            Carbon::now()->addHour(),
            fn (): ?Wallet => $this->block->delegate
        );
    }

    public function delegateUsername(): string
    {
        return Arr::get($this->delegate(), 'attributes.delegate.username', 'Genesis');
    }

    public function height(): string
    {
        return NumberFormatter::number($this->block->height->toNumber());
    }

    public function transactionCount(): int
    {
        return $this->block->number_of_transactions;
    }

    public function amount(): string
    {
        return NumberFormatter::currency($this->block->total_amount->toFloat(), Network::currency());
    }

    public function amountFiat(): string
    {
        return ExchangeRate::convert($this->block->total_amount->toFloat(), $this->block->timestamp);
    }

    public function fee(): string
    {
        return NumberFormatter::currency($this->block->total_fee->toFloat(), Network::currency());
    }

    public function feeFiat(): string
    {
        return ExchangeRate::convert($this->block->total_fee->toFloat(), $this->block->timestamp);
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

    public function previousBlockUrl(): ?string
    {
        return $this->findBlockWithHeight($this->block->height->minus(1)->toNumber());
    }

    public function nextBlockUrl(): ?string
    {
        return $this->findBlockWithHeight($this->block->height->plus(1)->toNumber());
    }

    private function findBlockWithHeight(int $height): ?string
    {
        $block = Cache::remember(
            "block:neighbour:$height",
            Carbon::now()->addHour(),
            fn () => Block::where('height', $height)->first()
        );

        if (is_null($block)) {
            return null;
        }

        return route('block', $block);
    }
}
