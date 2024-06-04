<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use Livewire\Features\SupportLifecycleHooks\SupportLifecycleHooks;

trait SyncsInput
{
    private function syncInput(string $property, mixed $value): void
    {
        $hook = new SupportLifecycleHooks();

        $hook->setComponent($this);
        $updatedHook = $hook->update($property, $property, $value);

        /* @phpstan-ignore-next-line */
        $this->{$property} = $value;

        $updatedHook();
    }
}
