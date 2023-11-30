<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Services\ExchangeRate;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class FiatValue extends Component
{
    public float $amount;

    public ?int $timestamp = null;

    /** @var mixed */
    protected $listeners = [
        'currencyChanged' => '$refresh',
    ];

    public function mount(float $amount, ?int $timestamp = null): void
    {
        $this->amount    = $amount;
        $this->timestamp = $timestamp;
    }

    public function render(): View
    {
        return view('livewire.fiat-value', [
            'value' => ExchangeRate::convert($this->amount, $this->timestamp),
        ]);
    }
}
