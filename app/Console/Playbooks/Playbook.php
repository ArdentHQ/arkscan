<?php

declare(strict_types=1);

namespace  App\Console\Playbooks;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Playbook
{
    public static int $timesRun = 0;

    final public static function times(int $times): PlaybookDefinition
    {
        return PlaybookDefinition::times(static::class, $times);
    }

    final public static function once(): PlaybookDefinition
    {
        return PlaybookDefinition::once(static::class);
    }

    public function before(): array
    {
        return [];
    }

    abstract public function run(InputInterface $input, OutputInterface $output);

    final public function hasRun(): void
    {
        static::$timesRun += 1;
    }

    final public function timesRun(): int
    {
        return static::$timesRun;
    }

    final public function after(): array
    {
        return [];
    }
}
