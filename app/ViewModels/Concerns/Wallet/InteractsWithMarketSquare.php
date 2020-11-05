<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Facades\Network;
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
        if (! Network::usesMarketsquare()) {
            return null;
        }

        $username = $this->username();

        if (is_null($username)) {
            return null;
        }

        return 'https://marketsquare.io/delegates/'.Str::slug($username);
    }

    /**
     * @TODO: needs marketsquare
     *
     * @codeCoverageIgnore
     */
    public function commission(): ?int
    {
        if (! Network::usesMarketsquare()) {
            return null;
        }

        return 0;
    }

    /**
     * @TODO: needs marketsquare
     *
     * @codeCoverageIgnore
     */
    public function payoutFrequency(): ?int
    {
        if (! Network::usesMarketsquare()) {
            return null;
        }

        return 0;
    }

    /**
     * @TODO: needs marketsquare
     *
     * @codeCoverageIgnore
     */
    public function payoutMinimum(): ?int
    {
        if (! Network::usesMarketsquare()) {
            return null;
        }

        return 0;
    }
}
