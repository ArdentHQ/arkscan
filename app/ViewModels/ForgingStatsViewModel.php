<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Models\ForgingStats;
use App\Services\Timestamp;
use Carbon\Carbon;

final class ForgingStatsViewModel implements ViewModel
{
    public function __construct(private ForgingStats $forgingStats)
    {
    }

    public function delegate(): ?WalletViewModel
    {
        if ($this->forgingStats->delegate === null) {
            return null;
        }

        return new WalletViewModel($this->forgingStats->delegate);
    }

    public function address(): ?string
    {
        return $this->delegate()?->address();
    }

    public function username(): ?string
    {
        return $this->delegate()?->username();
    }

    public function timestamp(): string
    {
        return Timestamp::fromUnixHuman($this->forgingStats->timestamp);
    }

    public function dateTime(): Carbon
    {
        return Timestamp::fromUnix($this->forgingStats->timestamp);
    }

    public function height(): int
    {
        return $this->forgingStats->missed_height;
    }
}
