<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Block;

use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

trait HasDelegate
{
    public function delegate(): ?Wallet
    {
        return Cache::remember(
            "block:delegate:{$this->block->id}",
            Carbon::now()->addHour(),
            fn (): ?Wallet => $this->block->delegate
        );
    }

    public function address(): string
    {
        return Arr::get($this->delegate() ?? [], 'attributes.delegate.address', 'Genesis');
    }

    public function username(): string
    {
        return Arr::get($this->delegate() ?? [], 'attributes.delegate.username', 'Genesis');
    }
}
