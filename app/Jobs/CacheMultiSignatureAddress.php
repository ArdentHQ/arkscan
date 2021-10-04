<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Cache\WalletCache;
use ArkEcosystem\Crypto\Identities\Address;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

final class CacheMultiSignatureAddress implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public array $wallet)
    {
    }

    public function handle(): void
    {
        $min        = Arr::get($this->wallet, 'attributes.multiSignature.min', 0);
        $publicKeys = Arr::get($this->wallet, 'attributes.multiSignature.publicKeys', []);

        (new WalletCache())->setMultiSignatureAddress($min, $publicKeys, fn () => Address::fromMultiSignatureAsset($min, $publicKeys));
    }
}
