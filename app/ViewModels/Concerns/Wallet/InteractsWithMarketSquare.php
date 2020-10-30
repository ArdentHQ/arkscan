<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

trait InteractsWithMarketSquare
{
    /**
     * @TODO: needs marketsquare
     *
     * @codeCoverageIgnore
     */
    public function commission(): int
    {
        return 0;
    }

    /**
     * @TODO: needs marketsquare
     *
     * @codeCoverageIgnore
     */
    public function payoutFrequency(): int
    {
        return 0;
    }

    /**
     * @TODO: needs marketsquare
     *
     * @codeCoverageIgnore
     */
    public function payoutMinimum(): int
    {
        return 0;
    }
}
