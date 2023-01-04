<?php

declare(strict_types=1);

namespace App\Http\Livewire\Migration;

use App\Actions\CacheNetworkSupply;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\NumberFormatter;
use Brick\Math\RoundingMode;
use Illuminate\View\View;
use Livewire\Component;

final class Stats extends Component
{
    public function render(): View
    {
        $migratedBalance = $this->migratedBalance();
        $totalSupply     = $this->totalSupply();
        $remainingSupply = $totalSupply->minus($migratedBalance);

        return view('livewire.migration.stats', [
            'amountMigrated'  => $migratedBalance->toFloat(),
            'remainingSupply' => $remainingSupply->toFloat(),
            'percentage'      => NumberFormatter::percentage($migratedBalance->toInt() / $totalSupply->toInt()),
            'walletsMigrated' => $this->walletsMigrated(),
        ]);
    }

    private function migratedBalance(): BigNumber
    {
        return Wallet::firstWhere('address', config('explorer.migration_address'))->balance;
    }

    private function totalSupply(): BigNumber
    {
        return BigNumber::new(CacheNetworkSupply::execute());
    }

    private function walletsMigrated(): int
    {
        return Transaction::select('sender_public_key')
            ->where('recipient_id', config('explorer.migration_address'))
            ->get()
            ->pluck('sender_public_key')
            ->unique()
            ->count();
    }
}
