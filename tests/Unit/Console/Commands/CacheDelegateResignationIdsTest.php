<?php

declare(strict_types=1);

use App\Console\Commands\CacheDelegateResignationIds;
use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Jobs\CacheResignationId;
use App\Models\Transaction;
use Illuminate\Support\Facades\Queue;
use function Tests\configureExplorerDatabase;

it('should execute the command', function () {
    Queue::fake();

    configureExplorerDatabase();

    Transaction::factory(10)->create([
        'type'       => CoreTransactionTypeEnum::DELEGATE_RESIGNATION,
        'type_group' => TransactionTypeGroupEnum::CORE,
    ]);

    (new CacheDelegateResignationIds())->handle();

    Queue::assertPushed(CacheResignationId::class, 10);
});
