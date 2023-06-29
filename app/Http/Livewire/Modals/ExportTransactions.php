<?php

declare(strict_types=1);

namespace App\Http\Livewire\Modals;

use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class ExportTransactions extends Component
{
    use HasModal;

    public function render(): View
    {
        return view('livewire.modals.export-transactions');
    }
}
