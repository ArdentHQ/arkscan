<?php

declare(strict_types=1);

namespace App\Facades;

use App\Contracts\WalletRepository;
use Illuminate\Support\Facades\Facade;

final class Wallets extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return WalletRepository::class;
    }
}
