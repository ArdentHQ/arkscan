<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;

final class NavbarSettings extends Component
{
    public string $language = 'english';

    public string $currency = 'usd';

    public string $priceSource = 'coingecko';

    public bool $statisticsChart = true;

    public bool $darkTheme = true;

    public function render()
    {
        return view('livewire.navbar-settings');
    }
}
