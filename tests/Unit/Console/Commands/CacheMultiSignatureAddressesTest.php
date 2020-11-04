<?php

declare(strict_types=1);

use App\Console\Commands\CacheMultiSignatureAddresses;
use App\Jobs\CacheMultiSignatureAddress;
use App\Models\Wallet;
use Illuminate\Support\Facades\Queue;
use function Tests\configureExplorerDatabase;

it('should execute the command', function () {
    Queue::fake();

    configureExplorerDatabase();

    Wallet::factory(10)->create([
        'attributes' => ['multiSignature' => []],
    ]);

    (new CacheMultiSignatureAddresses())->handle();

    Queue::assertPushed(CacheMultiSignatureAddress::class, 10);
});
