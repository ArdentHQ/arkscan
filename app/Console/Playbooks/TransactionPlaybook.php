<?php

declare(strict_types=1);

namespace App\Console\Playbooks;

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Transaction;
use App\Services\Timestamp;
use Carbon\Carbon;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TransactionPlaybook extends Playbook
{
    public function run(InputInterface $input, OutputInterface $output): void
    {
        Transaction::factory(30)->create([
            'timestamp' => rand(110982056, 119982056),
        ]);

        Transaction::factory(10)->create([
            'type'       => CoreTransactionTypeEnum::TRANSFER,
            'type_group' => TransactionTypeGroupEnum::CORE,
        ]);

        Transaction::factory(10)->create([
            'type'       => CoreTransactionTypeEnum::SECOND_SIGNATURE,
            'type_group' => TransactionTypeGroupEnum::CORE,
        ]);

        Transaction::factory(10)->create([
            'type'       => CoreTransactionTypeEnum::DELEGATE_REGISTRATION,
            'type_group' => TransactionTypeGroupEnum::CORE,
        ]);

        Transaction::factory(10)->create([
            'type'       => CoreTransactionTypeEnum::VOTE,
            'type_group' => TransactionTypeGroupEnum::CORE,
        ]);

        Transaction::factory(10)->create([
            'type'       => CoreTransactionTypeEnum::MULTI_SIGNATURE,
            'type_group' => TransactionTypeGroupEnum::CORE,
        ]);

        Transaction::factory(10)->create([
            'type'       => CoreTransactionTypeEnum::IPFS,
            'type_group' => TransactionTypeGroupEnum::CORE,
        ]);

        Transaction::factory(10)->create([
            'type'       => CoreTransactionTypeEnum::MULTI_PAYMENT,
            'type_group' => TransactionTypeGroupEnum::CORE,
        ]);

        Transaction::factory(10)->create([
            'type'       => CoreTransactionTypeEnum::DELEGATE_RESIGNATION,
            'type_group' => TransactionTypeGroupEnum::CORE,
        ]);

        Transaction::factory(10)->create([
            'type'       => CoreTransactionTypeEnum::TIMELOCK,
            'type_group' => TransactionTypeGroupEnum::CORE,
        ]);

        Transaction::factory(10)->create([
            'type'       => CoreTransactionTypeEnum::TIMELOCK_CLAIM,
            'type_group' => TransactionTypeGroupEnum::CORE,
        ]);

        Transaction::factory(10)->create([
            'type'       => CoreTransactionTypeEnum::TIMELOCK_REFUND,
            'type_group' => TransactionTypeGroupEnum::CORE,
        ]);

        foreach (range(1, 365) as $day) {
            Transaction::factory(1)->create([
                'timestamp' => Timestamp::fromUnix(Carbon::now()->subDays($day)->unix())->unix(),
            ]);
        }
    }
}
