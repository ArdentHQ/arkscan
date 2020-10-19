<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Services\Settings;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

final class NavbarSettings extends Component
{
    public array $state = [];

    public function mount(): void
    {
        $this->state = Settings::all();
    }

    public function updatedState(): void
    {
        Session::put('settings', json_encode($this->state));
    }
}
