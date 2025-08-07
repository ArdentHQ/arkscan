<?php

declare(strict_types=1);

use App\Jobs\CacheResignationIds;
use App\Models\Transaction;
use App\Services\Cache\CommandsCache;
use App\Services\Cache\WalletCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

it('should cache the resignation ID for the public key', function () {
    $transaction = Transaction::factory()
        ->validatorResignation()
        ->create();

    expect(Cache::tags('wallet')->has(md5("resignation_id/$transaction->sender_public_key")))->toBeFalse();

    (new CacheResignationIds())->handle(new WalletCache(), new CommandsCache());

    expect(Cache::tags('wallet')->has(md5("resignation_id/$transaction->sender_public_key")))->toBeTrue();

    expect(Cache::tags('wallet')->get(md5("resignation_id/$transaction->sender_public_key")))->toBeString();
});

it('should only cache resignation IDs since last run', function () {
    $this->freezeTime();

    $transaction = Transaction::factory()
        ->validatorResignation()
        ->create(['timestamp' => Carbon::now()->subMinutes(5)->getTimestampMs()]);

    expect(Cache::tags('wallet')->has(md5("resignation_id/$transaction->sender_public_key")))->toBeFalse();

    (new CacheResignationIds())->handle(new WalletCache(), new CommandsCache());

    expect(Cache::tags('wallet')->has(md5("resignation_id/$transaction->sender_public_key")))->toBeTrue();
    expect(Cache::tags('wallet')->get(md5("resignation_id/$transaction->sender_public_key")))->toBe($transaction->hash);

    Cache::tags('wallet')->set(md5("resignation_id/$transaction->sender_public_key"), 'overridden-hash');

    expect(Cache::tags('commands')->get(md5('resignation_ids:last_updated')))->toBe(Carbon::now()->getTimestampMs());
    expect(Cache::tags('wallet')->get(md5("resignation_id/$transaction->sender_public_key")))->toBe('overridden-hash');

    $newTransaction = Transaction::factory()
        ->validatorResignation()
        ->create(['timestamp' => Carbon::now()->addMinutes(1)->getTimestampMs()]);

    (new CacheResignationIds())->handle(new WalletCache(), new CommandsCache());

    expect(Cache::tags('wallet')->get(md5("resignation_id/$transaction->sender_public_key")))->toBe('overridden-hash');
    expect(Cache::tags('wallet')->get(md5("resignation_id/$newTransaction->sender_public_key")))->toBe($newTransaction->hash);
});

it('should not cache a resignation ID for non-resignation transactions', function () {
    $transaction = Transaction::factory()
        ->transfer()
        ->create();

    expect(Cache::tags('wallet')->has(md5("resignation_id/$transaction->sender_public_key")))->toBeFalse();

    (new CacheResignationIds())->handle(new WalletCache(), new CommandsCache());

    expect(Cache::tags('wallet')->has(md5("resignation_id/$transaction->sender_public_key")))->toBeFalse();
});
