<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

trait CanViewMore
{
    public bool $viewMore = false;

    public function mount(bool $viewMore = false): void
    {
        $this->viewMore = $viewMore;
    }
}
