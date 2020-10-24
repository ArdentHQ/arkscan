<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Spatie\ViewModels\ViewModel;

final class BlockViewModel extends ViewModel
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
        return NumberFormatter::number($this->block->height);
    }

    public function transactionCount(): int
    {
        return $this->block->number_of_transactions;
    }

    public function amount(): string
    {
        return NumberFormatter::currency($this->block->total_amount / 1e8, Network::currency());
    }

    public function amountFiat(): string
    {
        return ExchangeRate::convert($this->block->total_amount / 1e8, $this->block->timestamp);
    }

    public function fee(): string
    {
        return NumberFormatter::currency($this->block->total_fee / 1e8, Network::currency());
    }

    public function feeFiat(): string
    {
        return ExchangeRate::convert($this->block->total_fee / 1e8, $this->block->timestamp);
    }

    public function reward(): string
    {
        return NumberFormatter::currency($this->block->reward / 1e8, Network::currency());
    }

    public function rewardFiat(): string
    {
        return ExchangeRate::convert($this->block->reward / 1e8, $this->block->timestamp);
    }

    public function previousBlockUrl(): ?string
    {
        return $this->findBlockWithHeight($this->block->height - 1);
    }

    public function nextBlockUrl(): ?string
    {
        return $this->findBlockWithHeight($this->block->height + 1);
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
