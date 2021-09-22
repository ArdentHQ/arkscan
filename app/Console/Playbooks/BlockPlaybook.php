<?php

declare(strict_types=1);

namespace App\Console\Playbooks;

use App\Models\Block;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class BlockPlaybook extends Playbook
{
    public function run(InputInterface $input, OutputInterface $output): void
    {
        Block::factory(30)->create();
    }
}
