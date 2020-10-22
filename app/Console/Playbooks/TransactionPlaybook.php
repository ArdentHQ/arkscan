<?php

declare(strict_types=1);

namespace App\Console\Playbooks;

use App\Models\Transaction;
use App\Services\Timestamp;
use Carbon\Carbon;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TransactionPlaybook extends Playbook
{
    public function run(InputInterface $input, OutputInterface $output): void
    {
        Transaction::factory(60)->create([
            'timestamp' => rand(110982056, 119982056),
        ]);

        foreach (range(1, 365) as $day) {
            Transaction::factory(1)->create([
                'timestamp' => Timestamp::fromUnix(Carbon::now()->subDays($day)->unix())->unix(),
            ]);
        }
    }
}
