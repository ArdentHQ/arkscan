<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Services\NumberFormatter;

trait InteractsWithMarketSquare
{
    /**
     * @TODO: needs marketsquare
     *
     * @codeCoverageIgnore
     */
    public function commission(): string
    {
        return NumberFormatter::percentage(0);
    }

    /**
     * @TODO: needs marketsquare
     *
     * @codeCoverageIgnore
     */
    public function payoutFrequency(): string
    {
        return NumberFormatter::number(0);
    }

    /**
     * @TODO: needs marketsquare
     *
     * @codeCoverageIgnore
     */
    public function payoutMinimum(): string
    {
        return NumberFormatter::number(0);
    }
}
