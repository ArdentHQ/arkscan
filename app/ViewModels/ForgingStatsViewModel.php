<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Models\ForgingStats;
use App\Services\Timestamp;
use App\ViewModels\Concerns\Block\HasDelegate;
use Carbon\Carbon;

final class ForgingStatsViewModel implements ViewModel
{
    public function __construct(private ForgingStats $forgingStats)
    {
    }

    public function model(): ForgingStats
    {
        return $this->forgingStats;
    }

    public function delegate(): ?WalletViewModel
    {
        if ($this->forgingStats->delegate === null) {
            return null;
        }

        return new WalletViewModel($this->forgingStats->delegate);
    }

    public function address(): string
    {
        return $this->delegate()?->address() ?? 'Genesis';
    }

    public function username(): string
    {
        return $this->delegate()?->username() ?? 'Genesis';
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
