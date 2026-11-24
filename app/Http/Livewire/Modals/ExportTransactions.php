<?php

declare(strict_types=1);

namespace App\Http\Livewire\Modals;

use App\Facades\Wallets;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Models\Transaction;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class ExportTransactions extends Component
{
    use DeferLoading;
    use HasModal;

    public string $address;

    public bool $hasTransactions = false;

    public ?int $oldestTimestamp = null;

    public function mount(WalletViewModel $wallet): void
    {
        $this->address = $wallet->address();
    }

    public function render(): View
    {
        return view('livewire.modals.export-transactions');
    }

    public function setIsReady(): void
    {
        $this->oldestTimestamp = $this->oldestTransactionTimestamp(Wallets::findByAddress($this->address));

        $this->hasTransactions = $this->oldestTimestamp !== null;
    }

    /**
     * The export uses it as a lower bound for its API requests. Without one, the
     * API scans the whole chain looking for older transactions of the wallet and
     * times out on the last page of long histories.
     */
    private function oldestTransactionTimestamp(Wallet $wallet): ?int
    {
        // `timestamp + 0` keeps Postgres on the sender, recipient and payments
        // indexes instead of walking the timestamp index from the genesis block.
        $result = Transaction::query()->getConnection()->selectOne(
            'SELECT LEAST(
                (SELECT MIN("timestamp" + 0) FROM transactions WHERE sender_public_key = ?),
                (SELECT MIN("timestamp" + 0) FROM transactions WHERE recipient_id = ?),
                (SELECT MIN("timestamp" + 0) FROM transactions WHERE asset->\'payments\' @> ?::jsonb)
            ) AS timestamp',
            [
                $wallet->public_key,
                $wallet->address,
                json_encode([['recipientId' => $wallet->address]]),
            ],
        );

        if ($result?->timestamp === null) {
            return null;
        }

        return (int) $result->timestamp;
    }
}
