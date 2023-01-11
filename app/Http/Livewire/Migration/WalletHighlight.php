<?php

declare(strict_types=1);

namespace App\Http\Livewire\Migration;

use App\Http\Livewire\Migration\Concerns\HandlesStats;
use Illuminate\View\View;
use Livewire\Component;

final class WalletHighlight extends Component
{
    use HandlesStats;

    public function render(): View
    {
        return view('livewire.migration.wallet-highlight', $this->viewData());
    }
}
