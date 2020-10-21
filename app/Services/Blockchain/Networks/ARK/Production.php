<?php

declare(strict_types=1);

namespace App\Services\Blockchain\Networks\ARK;

use App\Contracts\Network;
use ArkEcosystem\Crypto\Networks\Mainnet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class Production implements Network
{
    public function name(): string
    {
        return 'ARK Public Network';
    }

    public function alias(): string
    {
        return 'mainnet';
    }

    public function currency(): string
    {
        return 'ARK';
    }

    public function currencySymbol(): string
    {
        return 'Ѧ';
    }

    public function confirmations(): int
    {
        return 51;
    }

    public function knownWallets(): array
    {
        return Cache::rememberForever(
            'ark.production.wallets.known',
            fn () => Http::get('https://raw.githubusercontent.com/ArkEcosystem/common/master/mainnet/known-wallets-extended.json')->json()
        );
    }

    public function canBeExchanged(): bool
    {
        return true;
    }

    public function host(): string
    {
        return 'https://wallets.ark.io/api';
    }

    public function usesMarketsquare(): bool
    {
        return true;
    }

    public function epoch(): Carbon
    {
        return Carbon::parse(Mainnet::new()->epoch());
    }
}
