<?php

declare(strict_types=1);

use App\Console\Commands\CacheMultiSignatureAddresses;
use App\Jobs\CacheMultiSignatureAddress;
use App\Models\Wallet;
use Illuminate\Support\Facades\Queue;

it('should execute the command', function () {
    Queue::fake();

    Wallet::factory(10)->create([
        'attributes' => ['multiSignature' => []],
    ]);

    (new CacheMultiSignatureAddresses())->handle();

    Queue::assertPushed(CacheMultiSignatureAddress::class, 10);
});
