<?php

declare(strict_types=1);

namespace App\Console\Playbooks;

use App\Models\Round;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RoundPlaybook extends Playbook
{
    public function run(InputInterface $input, OutputInterface $output): void
    {
        Round::factory(30)->create();
    }
}
