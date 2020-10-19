<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Wallet;
use Livewire\Component;

final class WalletTable extends Component
{
    public function render()
    {
        return view('livewire.wallet-table', [
            'wallets' => Wallet::wealthy()->paginate(),
        ]);
    }
}
