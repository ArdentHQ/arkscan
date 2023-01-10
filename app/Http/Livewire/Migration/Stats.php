<?php

declare(strict_types=1);

namespace App\Http\Livewire\Migration;

use App\Actions\CacheTotalSupply;
use App\Facades\Network;
use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\NumberFormatter;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;

// We need this as a regular class as we inherit from it for WalletHighlight
/** @phpstan-ignore-next-line */
class Stats extends Component
{
    public const CACHE_WALLETS_SECONDS = 300;

    public function render(): View
    {
        $migratedBalance    = Network::migratedBalance();
        $totalSupply        = $this->totalSupply();
        $migratedPercentage = $this->migratedPercentage($migratedBalance, $totalSupply);
        $remainingSupply    = $totalSupply->minus($migratedBalance->valueOf());

        return view('livewire.migration.stats', [
            'amountMigrated'  => $migratedBalance->toFloat(),
            'remainingSupply' => $remainingSupply->toFloat(),
            'percentage'      => NumberFormatter::percentage($migratedPercentage),
            'walletsMigrated' => $this->walletsMigrated(),
        ]);
    }

    protected function migratedPercentage(BigNumber $migrated, BigNumber $supply): float
    {
        return $migrated->toInt() / $supply->toInt() * 100;
    }

    protected function totalSupply(): BigNumber
    {
        return BigNumber::new(CacheTotalSupply::execute());
    }

    protected function walletsMigrated(): int
    {
        /** @var string $cache */
        $cache = Cache::remember('migration:wallets_count', self::CACHE_WALLETS_SECONDS, function () {
            return Transaction::select('sender_public_key')
                ->where('recipient_id', config('explorer.migration_address'))
                ->get()
                ->pluck('sender_public_key')
                ->unique()
                ->count();
        });

        return (int) $cache;
    }
}
