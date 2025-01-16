<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

trait DeferLoading
{
    public bool $isReady = false;

    // We don't want this method to be `final` which phpstan complains about
    // when used in an abstract class - @phpstan-ignore-next-line
    public function setIsReady(): void
    {
        $this->isReady = true;
    }
}
