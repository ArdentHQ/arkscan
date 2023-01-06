<?php

declare(strict_types=1);

namespace App\Http\Livewire\Migration;

use App\Actions\CacheNetworkSupply;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\NumberFormatter;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

final class Stats extends Component
{
    const CACHE_WALLETS_SECONDS = 300;

    public function render(): View
    {
        $migratedBalance = $this->migratedBalance();
        $totalSupply     = $this->totalSupply();
        $remainingSupply = $totalSupply->minus($migratedBalance->valueOf());

        return view('livewire.migration.stats', [
            'amountMigrated'  => $migratedBalance->toFloat(),
            'remainingSupply' => $remainingSupply->toFloat(),
            'percentage'      => NumberFormatter::percentage($migratedBalance->toInt() / $totalSupply->toInt()),
            'walletsMigrated' => $this->walletsMigrated(),
        ]);
    }

    private function migratedBalance(): BigNumber
    {
        $wallet = Wallet::firstWhere('address', config('explorer.migration_address'));
        if ($wallet === null) {
            return BigNumber::new(0);
        }

        return $wallet->balance;
    }

    private function totalSupply(): BigNumber
    {
        return BigNumber::new(CacheNetworkSupply::execute());
    }

    private function walletsMigrated(): int
    {
        return (int) Cache::remember('migration:wallets_count', self::CACHE_WALLETS_SECONDS, function () {
            return Transaction::select('sender_public_key')
                ->where('recipient_id', config('explorer.migration_address'))
                ->get()
                ->pluck('sender_public_key')
                ->unique()
                ->count();
        });
    }
}
