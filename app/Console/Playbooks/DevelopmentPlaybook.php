<?php

declare(strict_types=1);

namespace  App\Console\Playbooks;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DevelopmentPlaybook extends Playbook
{
    public function before(): array
    {
        return [
            BlockPlaybook::once(),
            RoundPlaybook::once(),
            TransactionPlaybook::once(),
            WalletPlaybook::once(),
        ];
    }

    public function run(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('<info>[Playbook] Development - success</info>');
    }
}
