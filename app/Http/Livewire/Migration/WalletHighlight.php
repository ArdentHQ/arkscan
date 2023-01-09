<?php

declare(strict_types=1);

namespace App\Http\Livewire\Migration;

use App\Facades\Network;
use App\Services\NumberFormatter;
use Illuminate\View\View;

final class WalletHighlight extends Stats
{
    public function render(): View
    {
        $migratedBalance = Network::migratedBalance();
        $totalSupply     = $this->totalSupply();
        $remainingSupply = $totalSupply->minus($migratedBalance->valueOf());

        return view('livewire.migration.wallet-highlight', [
            'amountMigrated'  => $migratedBalance->toFloat(),
            'remainingSupply' => $remainingSupply->toFloat(),
            'percentage'      => NumberFormatter::percentage($migratedBalance->toInt() / $totalSupply->toInt()),
            'walletsMigrated' => $this->walletsMigrated(),
        ]);
    }
}
