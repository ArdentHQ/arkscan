<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Models\Scopes\DelegateResignationScope;
use App\Models\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

trait CanBeDelegate
{
    public function isDelegate(): bool
    {
        return Arr::has($this->wallet, 'attributes.delegate');
    }

    public function resignationId(): ?string
    {
        if (! Arr::has($this->wallet, 'attributes.delegate.resigned')) {
            return null;
        }

        return Cache::rememberForever('resignationId:'.$this->wallet->address, function () {
            return Transaction::withScope(DelegateResignationScope::class)->firstOrFail()->id;
        });
    }

    public function username(): ?string
    {
        return Arr::get($this->wallet, 'attributes.delegate.username');
    }

    /**
     * @codeCoverageIgnore
     */
    public function rank(): ?int
    {
        return Arr::get($this->wallet, 'attributes.delegate.rank', 0);
    }
}
