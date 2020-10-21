<?php

declare(strict_types=1);

namespace App\Services\Blockchain\Networks\ARK;

use App\Contracts\Network;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class Development implements Network
{
    public function name(): string
    {
        return 'ARK Development Network';
    }

    public function alias(): string
    {
        return 'devnet';
    }

    public function currency(): string
    {
        return 'DARK';
    }

    public function currencySymbol(): string
    {
        return 'DѦ';
    }

    public function confirmations(): int
    {
        return 51;
    }

    public function knownWallets(): array
    {
        return Cache::rememberForever(
            'ark.development.wallets.known',
            fn () => Http::get('https://raw.githubusercontent.com/ArkEcosystem/common/master/devnet/known-wallets-extended.json')->json()
        );
    }

    public function canBeExchanged(): bool
    {
        return false;
    }

    public function host(): string
    {
        return 'https://dwallets.ark.io/api';
    }

    public function usesMarketsquare(): bool
    {
        return false;
    }
}
