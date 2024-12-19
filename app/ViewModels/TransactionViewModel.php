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
use Illuminate\Support\Arr;

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
        return route('transaction', $this->transaction);
    }

    public function model(): Transaction
    {
        return $this->transaction;
    }

    public function id(): string
    {
        return $this->transaction->id;
    }

    public function blockId(): string
    {
        return $this->transaction->block_id;
    }

    public function blockHeight(): int
    {
        return $this->transaction->block_height;
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

    public function gasLimit(): float
    {
        return UnitConverter::formatUnits((string) $this->transaction->gas_limit, 'wei');
    }

    public function gasUsed(): float
    {
        $receipt = $this->transaction->receipt;

        if ($receipt === null) {
            return 0;
        }

        return UnitConverter::formatUnits((string) $receipt->gas_used, 'wei');
    }

    public function sequence(): int
    {
        return $this->transaction->sequence;
    }

    public function fee(): float
    {
        return UnitConverter::formatUnits((string) $this->transaction->fee(), 'gwei');
    }

    public function feeFiat(bool $showSmallAmounts = false): string
    {
        return ExchangeRate::convert($this->fee(), $this->transaction->timestamp, $showSmallAmounts);
    }

    // @codeCoverageIgnoreStart
    // @TODO: adjust for evm https://app.clickup.com/t/86dvfymkn
    public function amountForItself(): float
    {
        /** @var array<int, array<string, mixed>> */
        $payments = Arr::get($this->transaction, 'asset.payments', []);

        return collect($payments)
            ->filter(function ($payment): bool {
                $sender = $this->sender();

                return $sender !== null && $sender->address === $payment['recipientId'];
            })
            ->sum('amount') / config('currencies.notation.crypto', 1e18);
    }

    // @TODO: adjust for evm https://app.clickup.com/t/86dvfymkn
    public function amountExcludingItself(): float
    {
        /** @var array<int, array<string, mixed>> */
        $payments = Arr::get($this->transaction, 'asset.payments', []);

        return collect($payments)
            ->filter(function ($payment): bool {
                $sender = $this->sender();

                return $sender === null || $sender->address !== $payment['recipientId'];
            })
            ->sum('amount') / config('currencies.notation.crypto', 1e18);
    }
    // @codeCoverageIgnoreEnd

    public function amount(): float
    {
        return UnitConverter::formatUnits((string) $this->transaction->amount, 'ark');
    }

    public function amountWithFee(): float
    {
        return $this->transaction->amount->toFloat() + $this->fee();
    }

    public function amountReceived(?string $wallet = null): float
    {
        return $this->amount();
    }

    public function amountFiatExcludingItself(): string
    {
        return ExchangeRate::convert($this->amountExcludingItself(), $this->transaction->timestamp);
    }

    public function amountFiat(bool $showSmallAmounts = false): string
    {
        return ExchangeRate::convert($this->amount(), $this->transaction->timestamp, $showSmallAmounts);
    }

    public function amountReceivedFiat(?string $wallet = null): string
    {
        return ExchangeRate::convert($this->amountReceived($wallet), $this->transaction->timestamp);
    }

    public function totalFiat(bool $withSmallAmounts = false): string
    {
        return ExchangeRate::convert($this->amountWithFee(), $this->transaction->timestamp, $withSmallAmounts);
    }

    public function confirmations(): int
    {
        return abs(CacheNetworkHeight::execute() - $this->transaction->block_height);
    }
}
