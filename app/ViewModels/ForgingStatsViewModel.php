<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Actions\CacheNetworkHeight;
use App\Contracts\ViewModel;
use App\DTO\MemoryWallet;
use App\Models\ForgingStats;
use App\Services\ExchangeRate;
use App\Services\Timestamp;
use App\ViewModels\Concerns\Block\HasDelegate;
use App\ViewModels\Concerns\Block\HasTransactions;
use App\ViewModels\Concerns\Block\InteractsWithNeighbours;
use Carbon\Carbon;

final class ForgingStatsViewModel implements ViewModel
{
    use HasDelegate;

    public function __construct(private ForgingStats $forgingStats)
    {
    }

    public function model(): ForgingStats
    {
        return $this->forgingStats;
    }

    public function delegate(): WalletViewModel
    {
        return new WalletViewModel($this->forgingStats->delegate);
    }

    public function timestamp(): string
    {
        return Timestamp::fromGenesisHuman($this->forgingStats->timestamp);
    }

    public function dateTime(): Carbon
    {
        return Timestamp::fromGenesis($this->forgingStats->timestamp);
    }

    public function height(): int
    {
        return $this->forgingStats->missed_height->toNumber();
    }
}
