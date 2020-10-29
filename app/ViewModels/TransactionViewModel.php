<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Facades\Network;
use App\Models\Transaction;
use App\Services\Blockchain\NetworkStatus;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use App\Services\Timestamp;
use App\Services\Transactions\TransactionDirection;
use App\Services\Transactions\TransactionState;
use App\Services\Transactions\TransactionType;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

final class TransactionViewModel implements ViewModel
{
    use Concerns\Transaction\HasDirection;
    use Concerns\Transaction\HasIcons;
    use Concerns\Transaction\HasState;
    use Concerns\Transaction\HasType;
    use Concerns\Transaction\InteractsWithEntities;
    use Concerns\Transaction\InteractsWithMultiPayment;
    use Concerns\Transaction\InteractsWithMultiSignature;
    use Concerns\Transaction\InteractsWithTypeData;
    use Concerns\Transaction\InteractsWithVendorField;
    use Concerns\Transaction\InteractsWithVotes;
    use Concerns\Transaction\InteractsWithWallets;

    private Transaction $transaction;

    private TransactionType $type;

    private TransactionState $state;

    private TransactionDirection $direction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
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

    public function timestamp(): string
    {
        return Timestamp::fromGenesisHuman($this->transaction->timestamp);
    }

    public function nonce(): string
    {
        $wallet = Cache::remember(
            "transaction:wallet:{$this->transaction->sender_public_key}",
            Carbon::now()->addHour(),
            fn () => $this->transaction->sender
        );

        return NumberFormatter::number($wallet->nonce->toNumber());
    }

    public function fee(): string
    {
        return NumberFormatter::currency($this->transaction->fee->toFloat(), Network::currency());
    }

    public function feeFiat(): string
    {
        return ExchangeRate::convert($this->transaction->fee->toFloat(), $this->transaction->timestamp);
    }

    public function amount(): string
    {
        if ($this->isMultiPayment()) {
            $amount = collect(Arr::get($this->transaction->asset ?? [], 'payments', []))->sum('amount') / 1e8;
        } else {
            $amount = $this->transaction->amount->toFloat();
        }

        return NumberFormatter::currency($amount, Network::currency());
    }

    public function amountFiat(): string
    {
        return ExchangeRate::convert($this->transaction->amount->toFloat(), $this->transaction->timestamp);
    }

    public function confirmations(): string
    {
        $block = Cache::remember(
            "transaction:confirmations:{$this->transaction->block_id}",
            Carbon::now()->addHour(),
            fn () => $this->transaction->block
        );

        if (is_null($block)) {
            return NumberFormatter::number(0);
        }

        return NumberFormatter::number(NetworkStatus::height() - $block->height->toNumber());
    }
}
