<?php

declare(strict_types=1);

namespace  App\Console\Playbooks;

final class PlaybookDefinition
{
    /** @var string */
    public $id;

    /** @var \App\Console\Playbooks\Playbook */
    public $playbook;

    /** @var int */
    public $times = 1;

    /** @var bool */
    public $once = false;

    public static function times(string $className, int $times): self
    {
        $definition = new static($className);

        $definition->times = $times;

        return $definition;
    }

    public static function once(string $className): self
    {
        $definition = new static($className);

        $definition->once = true;

        return $definition;
    }

    public function __construct(string $className)
    {
        $this->playbook = app($className);
        $this->id       = get_class($this->playbook);
    }
}
