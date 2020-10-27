<?php

declare(strict_types=1);

namespace App\Console\Playbooks;

use App\Models\Wallet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class WalletPlaybook extends Playbook
{
    public function run(InputInterface $input, OutputInterface $output): void
    {
        Wallet::factory(30)->create();
    }
}
