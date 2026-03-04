<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Actions\CacheNetworkHeight;
use App\Contracts\ViewModel;
use App\Models\MultiPayment;
use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\Transactions\TransactionMethod;
use App\ViewModels\Concerns\Transaction\CanBeValidatorRegistration;
use App\ViewModels\Concerns\Transaction\CanHaveUsername;
use App\ViewModels\Concerns\Transaction\HasMethod;
use App\ViewModels\Concerns\Transaction\HasPayload;
use App\ViewModels\Concerns\Transaction\HasState;
use App\ViewModels\Concerns\Transaction\InteractsWithVotes;
use App\ViewModels\Concerns\Transaction\InteractsWithWallets;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Illuminate\Support\Collection;

final class TransactionViewModel implements ViewModel
{
    use CanBeValidatorRegistration;
    use CanHaveUsername;
    use HasPayload;
    use HasState;
    use HasMethod;
    use InteractsWithVotes;
    use InteractsWithWallets;

    private TransactionMethod $method;

    public function __construct(private Transaction $transaction)
    {
        $this->method = new TransactionMethod($transaction);
    }

    public function model(): Transaction
    {
        return $this->transaction;
    }

    public function hash(): string
    {
        return $this->transaction->hash;
    }

    public function fee(): float
    {
        return UnitConverter::formatUnits((string) $this->transaction->fee(), 'ark');
    }

    public function amount(): float
    {
        if (! $this->isMultiPayment()) {
            return UnitConverter::formatUnits((string) $this->transaction->value, 'ark');
        }

        $amount = BigNumber::zero();
        foreach ($this->multiPaymentRecipients() as $recipient) {
            $amount->plus((string) $recipient->amount);
        }

        return $amount->toFloat();
    }

    public function amountWithFee(): float
    {
        return $this->transaction->value->toFloat() + $this->fee();
    }

    public function amountReceived(?string $walletAddress = null): float
    {
        if ($this->isMultiPayment() && $walletAddress !== null) {
            /** @var Collection<int, MultiPayment> $results */
            $results = (new Collection($this->multiPaymentRecipients()))
                ->filter(function (MultiPayment $recipient) use ($walletAddress) {
                    if (strtolower($recipient->to) === strtolower($walletAddress)) {
                        return true;
                    }

                    return false;
                });

            $amount = BigNumber::zero();
            foreach ($results as $recipient) {
                $amount->plus((string) $recipient->amount);
            }

            return $amount->toFloat();
        }

        return $this->amount();
    }

    public function confirmations(): int
    {
        return abs(CacheNetworkHeight::execute() - $this->transaction->block_number);
    }
}
