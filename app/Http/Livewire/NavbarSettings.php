<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

final class NavbarSettings extends Component
{
    public array $state = [];

    public function mount(): void
    {
        if (Cookie::has('settings')) {
            $this->state = json_decode(Cookie::get('settings'), true);
        } else {
            $this->state = [
                'language'        => 'en',
                'currency'        => 'usd',
                'priceSource'     => 'cryptocompare',
                'statisticsChart' => true,
                'darkTheme'       => true,
            ];
        }
    }

    public function updatedState(): void
    {
        Cookie::queue('settings', json_encode($this->state));
    }
}
