<?php

declare(strict_types=1);

namespace App\Http\Livewire\Migration\Concerns;

use App\Actions\CacheTotalSupply;
use App\Facades\Network;
use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\NumberFormatter;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

trait HandlesStats
{
    private static int $cacheWalletsSeconds = 300;

    public function render(): View
    {
        return view('livewire.migration.stats', $this->viewData());
    }

    private function viewData(): array
    {
        $migratedBalance    = Network::migratedBalance();
        $totalSupply        = $this->totalSupply();
        $migratedPercentage = $this->migratedPercentage($migratedBalance, $totalSupply);
        $remainingSupply    = $totalSupply->minus($migratedBalance->valueOf());

        return [
            'amountMigrated'  => $migratedBalance->toFloat(),
            'remainingSupply' => $remainingSupply->toFloat(),
            'percentage'      => NumberFormatter::percentage($migratedPercentage),
            'walletsMigrated' => $this->walletsMigrated(),
        ];
    }

    private function migratedPercentage(BigNumber $migrated, BigNumber $supply): float
    {
        return $migrated->toInt() / $supply->toInt() * 100;
    }

    private function totalSupply(): BigNumber
    {
        return BigNumber::new(CacheTotalSupply::execute());
    }

    private function walletsMigrated(): int
    {
        /** @var string $cache */
        $cache = Cache::remember('migration:wallets_count', self::$cacheWalletsSeconds, function () {
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
