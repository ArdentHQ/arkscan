<?php

declare(strict_types=1);

use App\Jobs\CacheResignationId;
use App\Models\Transaction;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Facades\Cache;

it('should cache the resignation ID for the public key', function () {
    $transaction = Transaction::factory()->create();

    expect(Cache::tags('wallet')->has(md5("resignation_id/$transaction->sender_public_key")))->toBeFalse();

    (new CacheResignationId($transaction->sender_public_key, (string) $transaction->id))->handle(new WalletCache());

    expect(Cache::tags('wallet')->has(md5("resignation_id/$transaction->sender_public_key")))->toBeTrue();

    expect(Cache::tags('wallet')->get(md5("resignation_id/$transaction->sender_public_key")))->toBeString();
});
