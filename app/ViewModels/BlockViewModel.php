<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use App\Services\Timestamp;
use Illuminate\Support\Arr;
use Spatie\ViewModels\ViewModel;

final class BlockViewModel extends ViewModel
{
    private Block $model;

    public function __construct(Block $block)
    {
        $this->model = $block;
    }

    public function url(): string
    {
        return route('block', $this->model->id);
    }

    public function id(): string
    {
        return $this->model->id;
    }

    public function timestamp(): string
    {
        return Timestamp::fromGenesisHuman($this->model->timestamp);
    }

    public function delegate(): Wallet
    {
        /* @phpstan-ignore-next-line */
        return $this->model->delegate;
    }

    public function delegateUsername(): string
    {
        return Arr::get($this->delegate(), 'attributes.delegate.username', 'Genesis');
    }

    public function height(): string
    {
        return NumberFormatter::number($this->model->height);
    }

    public function transactionCount(): int
    {
        return $this->model->number_of_transactions;
    }

    public function amount(): string
    {
        return NumberFormatter::currency($this->model->total_amount / 1e8, Network::currency());
    }

    public function amountFiat(): string
    {
        return ExchangeRate::convert($this->model->total_amount / 1e8, $this->model->timestamp);
    }

    public function fee(): string
    {
        return NumberFormatter::currency($this->model->total_fee / 1e8, Network::currency());
    }

    public function feeFiat(): string
    {
        return ExchangeRate::convert($this->model->total_fee / 1e8, $this->model->timestamp);
    }

    public function reward(): string
    {
        return NumberFormatter::currency($this->model->reward / 1e8, Network::currency());
    }

    public function rewardFiat(): string
    {
        return ExchangeRate::convert($this->model->reward / 1e8, $this->model->timestamp);
    }
}
