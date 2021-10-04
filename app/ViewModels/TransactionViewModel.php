<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Actions\CacheNetworkHeight;
use App\Contracts\ViewModel;
use App\Models\Transaction;
use App\Services\ExchangeRate;
use App\Services\Timestamp;
use App\Services\Transactions\TransactionDirection;
use App\Services\Transactions\TransactionState;
use App\Services\Transactions\TransactionType;
use App\ViewModels\Concerns\Transaction\HasDirection;
use App\ViewModels\Concerns\Transaction\HasIcons;
use App\ViewModels\Concerns\Transaction\HasState;
use App\ViewModels\Concerns\Transaction\HasType;
use App\ViewModels\Concerns\Transaction\InteractsWithDelegateRegistration;
use App\ViewModels\Concerns\Transaction\InteractsWithEntities;
use App\ViewModels\Concerns\Transaction\InteractsWithMultiPayment;
use App\ViewModels\Concerns\Transaction\InteractsWithMultiSignature;
use App\ViewModels\Concerns\Transaction\InteractsWithTypeData;
use App\ViewModels\Concerns\Transaction\InteractsWithVendorField;
use App\ViewModels\Concerns\Transaction\InteractsWithVotes;
use App\ViewModels\Concerns\Transaction\InteractsWithWallets;
use Carbon\Carbon;
use Illuminate\Support\Arr;

final class TransactionViewModel implements ViewModel
{
    use HasDirection;
    use HasIcons;
    use HasState;
    use HasType;
    use InteractsWithDelegateRegistration;
    use InteractsWithEntities;
    use InteractsWithMultiPayment;
    use InteractsWithMultiSignature;
    use InteractsWithTypeData;
    use InteractsWithVendorField;
    use InteractsWithVotes;
    use InteractsWithWallets;

    private TransactionType $type;

    private TransactionState $state;

    private TransactionDirection $direction;

    public function __construct(private Transaction $transaction)
    {
        $this->type        = new TransactionType($transaction);
        $this->state       = new TransactionState($transaction);
        $this->direction   = new TransactionDirection($transaction);
    }

    public function url(): string
    {
        return route('transaction', $this->transaction);
    }

    public function id(): string
    {
        return $this->transaction->id;
    }

    public function blockId(): string
    {
        return $this->transaction->block_id;
    }

    public function timestamp(bool $short = false): string
    {
        return Timestamp::fromGenesisHuman($this->transaction->timestamp, $short);
    }

    public function dateTime(): Carbon
    {
        return Timestamp::fromGenesis($this->transaction->timestamp);
    }

    public function nonce(): int
    {
        return $this->transaction->nonce;
    }

    public function fee(): float
    {
        return $this->transaction->fee->toFloat();
    }

    public function feeFiat(): string
    {
        return ExchangeRate::convert($this->transaction->fee->toFloat(), $this->transaction->timestamp);
    }

    public function amountForItself(): float
    {
        return collect(Arr::get($this->transaction, 'asset.payments', []))
            ->filter(function ($payment): bool {
                $sender = $this->sender();

                return $sender !== null && $sender->address === $payment['recipientId'];
            })
            ->sum('amount') / 1e8;
    }

    public function amountExcludingItself(): float
    {
        return collect(Arr::get($this->transaction, 'asset.payments', []))
            ->filter(function ($payment): bool {
                $sender = $this->sender();

                return $sender === null || $sender->address !== $payment['recipientId'];
            })
            ->sum('amount') / 1e8;
    }

    public function amount(): float
    {
        if ($this->isMultiPayment()) {
            return collect(Arr::get($this->transaction, 'asset.payments', []))
                ->sum('amount') / 1e8;
        }

        return $this->transaction->amount->toFloat();
    }

    public function amountReceived(?string $wallet = null): float
    {
        if ($this->isMultiPayment() && $wallet !== null) {
            return collect(Arr::get($this->transaction, 'asset.payments', []))
                ->where('recipientId', $wallet)
                ->sum('amount') / 1e8;
        }

        return $this->amount();
    }

    public function amountFiatExcludingItself(): string
    {
        return ExchangeRate::convert($this->amountExcludingItself(), $this->transaction->timestamp);
    }

    public function amountFiat(): string
    {
        return ExchangeRate::convert($this->amount(), $this->transaction->timestamp);
    }

    public function amountReceivedFiat(?string $wallet = null): string
    {
        return ExchangeRate::convert($this->amountReceived($wallet), $this->transaction->timestamp);
    }

    public function confirmations(): int
    {
        return abs(CacheNetworkHeight::execute() - $this->transaction->block_height);
    }

    public function ipfsHash(): ?string
    {
        return Arr::get($this->transaction, 'asset.ipfs');
    }
}
