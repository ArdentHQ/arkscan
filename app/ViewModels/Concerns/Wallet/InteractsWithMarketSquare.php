<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Facades\Network;
use App\Services\Cache\MarketSquareCache;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait InteractsWithMarketSquare
{
    /**
     * @TODO: needs marketsquare
     *
     * @codeCoverageIgnore
     */
    public function profileUrl(): ?string
    {
        if (! Network::usesMarketSquare()) {
            return null;
        }

        $username = $this->username();

        if (is_null($username)) {
            return null;
        }

        return 'https://marketsquare.io/delegates/'.Str::slug($username);
    }

    public function commission(): ?int
    {
        if (! Network::usesMarketSquare()) {
            return null;
        }

        return Arr::get(
            (new MarketSquareCache())->getProfile($this->wallet->address),
            'ipfs.data.meta.delegate.percentage.min'
        );
    }

    public function payoutFrequency(): ?string
    {
        if (! Network::usesMarketSquare()) {
            return null;
        }

        $profile = (new MarketSquareCache())->getProfile($this->wallet->address);
        $type    = Arr::get($profile, 'ipfs.data.meta.delegate.frequency.type');
        $value   = Arr::get($profile, 'ipfs.data.meta.delegate.frequency.value');

        return trans_choice('generic.'.$type, $value);
    }

    public function payoutMinimum(): ?int
    {
        if (! Network::usesMarketSquare()) {
            return null;
        }

        return Arr::get(
            (new MarketSquareCache())->getProfile($this->wallet->address),
            'ipfs.data.meta.delegate.distribution.min'
        );
    }
}
