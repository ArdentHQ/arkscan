<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Actions\CacheNetworkHeight;
use App\Contracts\ViewModel;
use App\Models\Transaction;
use App\Services\ExchangeRate;
use App\Services\Timestamp;
use App\Services\Transactions\TransactionDirection;
use App\Services\Transactions\TransactionMethod;
use App\Services\Transactions\TransactionState;
use App\ViewModels\Concerns\Transaction\CanBeValidatorRegistration;
use App\ViewModels\Concerns\Transaction\CanHaveUsername;
use App\ViewModels\Concerns\Transaction\HasDirection;
use App\ViewModels\Concerns\Transaction\HasMethod;
use App\ViewModels\Concerns\Transaction\HasPayload;
use App\ViewModels\Concerns\Transaction\HasState;
use App\ViewModels\Concerns\Transaction\InteractsWithVotes;
use App\ViewModels\Concerns\Transaction\InteractsWithWallets;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Carbon\Carbon;

final class TransactionViewModel implements ViewModel
{
    use CanBeValidatorRegistration;
    use CanHaveUsername;
    use HasDirection;
    use HasPayload;
    use HasState;
    use HasMethod;
    use InteractsWithVotes;
    use InteractsWithWallets;

    private TransactionMethod $method;

    private TransactionState $state;

    private TransactionDirection $direction;

    public function __construct(private Transaction $transaction)
    {
        $this->method      = new TransactionMethod($transaction);
        $this->state       = new TransactionState($transaction);
        $this->direction   = new TransactionDirection($transaction);
    }

    public function url(): string
    {
        return route('transaction', $this->transaction->hash);
    }

    public function model(): Transaction
    {
        return $this->transaction;
    }

    public function hash(): string
    {
        return $this->transaction->hash;
    }

    public function blockHash(): string
    {
        return $this->transaction->block_hash;
    }

    public function blockHeight(): int
    {
        return $this->transaction->block_number;
    }

    public function timestamp(): string
    {
        return Timestamp::fromUnixHuman($this->transaction->timestamp);
    }

    public function dateTime(): Carbon
    {
        return Timestamp::fromUnix($this->transaction->timestamp);
    }

    public function nonce(): int
    {
        return $this->transaction->nonce;
    }

    public function gas(): float
    {
        return UnitConverter::formatUnits((string) $this->transaction->gas, 'wei');
    }

    public function gasUsed(): float
    {
        $receipt = $this->transaction->receipt;

        if ($receipt === null) {
            return 0;
        }

        return UnitConverter::formatUnits((string) $receipt->gas_used, 'wei');
    }

    public function transactionIndex(): int
    {
        return $this->transaction->transaction_index;
    }

    public function fee(): float
    {
        return UnitConverter::formatUnits((string) $this->transaction->fee(), 'ark');
    }

    public function feeFiat(bool $showSmallAmounts = false): string
    {
        return ExchangeRate::convert($this->fee(), $this->transaction->timestamp, $showSmallAmounts);
    }

    public function amountForItself(): float
    {
        if (! $this->isMultiPayment()) {
            return 0;
        }

        $recipients = $this->multiPaymentRecipients();

        $amount = collect($recipients)
            ->filter(function ($recipient): bool {
                $sender = $this->sender();

                return $sender !== null && strtolower($sender->address) === strtolower($recipient['address']);
            })
            ->sum('amount');

        return $amount;
    }

    public function amountExcludingItself(): float
    {
        if (! $this->isMultiPayment()) {
            return 0;
        }

        $recipients = $this->multiPaymentRecipients();

        $amount = collect($recipients)
            ->filter(function ($recipient): bool {
                $sender = $this->sender();

                return $sender === null || strtolower($sender->address) !== strtolower($recipient['address']);
            })
            ->sum('amount');

        return $amount;
    }

    public function amount(): float
    {
        if (! $this->isMultiPayment()) {
            return UnitConverter::formatUnits((string) $this->transaction->value, 'ark');
        }

        return collect($this->multiPaymentRecipients())
            ->sum('amount');
    }

    public function amountWithFee(): float
    {
        return $this->transaction->value->toFloat() + $this->fee();
    }

    public function amountReceived(?string $walletAddress = null): float
    {
        if ($this->isMultiPayment() && $walletAddress !== null) {
            return collect($this->multiPaymentRecipients())
                ->filter(function ($recipient) use ($walletAddress) {
                    if (strtolower($recipient['address']) === strtolower($walletAddress)) {
                        return true;
                    }

                    return false;
                })
                ->sum('amount');
        }

        return $this->amount();
    }

    public function amountFiatExcludingItself(): string
    {
        return ExchangeRate::convert($this->amountExcludingItself(), $this->transaction->timestamp);
    }

    public function amountFiat(bool $showSmallAmounts = false, ?string $walletAddress = null): string
    {
        return ExchangeRate::convert($this->amount($walletAddress), $this->transaction->timestamp, $showSmallAmounts);
    }

    public function amountReceivedFiat(?string $walletAddress = null): string
    {
        return ExchangeRate::convert($this->amountReceived($walletAddress), $this->transaction->timestamp);
    }

    public function totalFiat(bool $withSmallAmounts = false): string
    {
        return ExchangeRate::convert($this->amountWithFee(), $this->transaction->timestamp, $withSmallAmounts);
    }

    public function confirmations(): int
    {
        return abs(CacheNetworkHeight::execute() - $this->transaction->block_number);
    }
}
