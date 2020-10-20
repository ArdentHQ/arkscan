<?php

declare(strict_types=1);

namespace App\Console\Playbooks;

use App\Models\Transaction;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TransactionPlaybook extends Playbook
{
    public function run(InputInterface $input, OutputInterface $output): void
    {
        Transaction::factory(100)->create();
    }
}
