<?php

declare(strict_types=1);

namespace App\Http\Livewire\Webhooks;

use App\Facades\Settings;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class CurrencyUpdate extends Component
{
    /** @var mixed */
    protected $listeners = [
        'currencyChanged' => '$refresh',
    ];

    public function render(): View
    {
        return view('livewire.webhooks.currency-update', [
            'currency' => Settings::currency(),
        ]);
    }
}
