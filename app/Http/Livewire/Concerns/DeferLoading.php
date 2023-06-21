<?php

namespace App\Http\Livewire\Concerns;

trait DeferLoading
{
    public bool $isReady = false;

    public function setIsReady(): void
    {
        $this->isReady = true;
    }
}
