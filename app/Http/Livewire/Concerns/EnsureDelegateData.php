<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\DTO\Slot;
use App\Jobs\CacheDelegateWallets;
use App\Services\Cache\WalletCache;

trait EnsureDelegateData
{
    private function ensureLoadedDelegates(array $delegates): void
    {
        $cache = new WalletCache();
        $hasMissingDelegates = false;
        // for ($i = 0; $i < count($tracking); $i++) {
        foreach ($delegates as $delegateData) {
            $publicKey = null;
            if (is_array($delegateData)) {
                $publicKey = $delegateData['publicKey'];
            } elseif ($delegateData instanceof Slot) {
                $publicKey = $delegateData->wallet()->model()->public_key;
            }

            if ($cache->getDelegate($publicKey) === null) {
                $hasMissingDelegates = true;

                break;
            }
        }

        if (! $hasMissingDelegates) {
            return;
        }

        (new CacheDelegateWallets())->handle($cache);
    }
}
