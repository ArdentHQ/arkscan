<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

trait DeferLoading
{
    public bool $isReady = false;

    public function setIsReady(): void
    {
        $this->isReady = true;
    }
}
