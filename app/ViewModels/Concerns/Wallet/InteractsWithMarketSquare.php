<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Services\Cache\MarketSquareCache;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait InteractsWithMarketSquare
{
    public function profileUrl(): ?string
    {
        $username = $this->username();

        if (is_null($username)) {
            return null;
        }

        return 'https://marketsquare.io/delegates/'.Str::slug($username);
    }

    public function commission(): ?int
    {
        return Arr::get(
            (new MarketSquareCache())->getProfile($this->wallet->address),
            'ipfs.data.meta.delegate.percentage.min'
        );
    }

    public function payoutFrequency(): ?string
    {
        $profile = (new MarketSquareCache())->getProfile($this->wallet->address);
        $type    = Arr::get($profile, 'ipfs.data.meta.delegate.frequency.type');
        $value   = Arr::get($profile, 'ipfs.data.meta.delegate.frequency.value');

        return trans_choice('generic.'.$type, $value);
    }

    public function payoutMinimum(): ?int
    {
        return Arr::get(
            (new MarketSquareCache())->getProfile($this->wallet->address),
            'ipfs.data.meta.delegate.distribution.min'
        );
    }
}
