<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Actions\CacheNetworkSupply;
use App\Contracts\ViewModel;
use App\Models\Wallet;
use App\Services\ArkVaultUrlBuilder;
use App\Services\ExchangeRate;
use App\ViewModels\Concerns\Wallet\CanBeCold;
use App\ViewModels\Concerns\Wallet\CanBeValidator;
use App\ViewModels\Concerns\Wallet\CanForge;
use App\ViewModels\Concerns\Wallet\CanVote;
use App\ViewModels\Concerns\Wallet\HasType;
use App\ViewModels\Concerns\Wallet\HasVoters;
use Mattiasgeniar\Percentage\Percentage;

final class WalletViewModel implements ViewModel
{
    use CanBeCold;
    use CanBeValidator;
    use CanForge;
    use CanVote;
    use HasType;
    use HasVoters;

    public function __construct(private Wallet $wallet)
    {
    }

    public function url(): string
    {
        return route('wallet', $this->wallet->address);
    }

    public function model(): Wallet
    {
        return $this->wallet;
    }

    public function id(): string
    {
        return $this->address();
    }

    public function address(): string
    {
        return $this->wallet->address;
    }

    public function publicKey(): ?string
    {
        return $this->wallet->public_key;
    }

    public function balance(): float
    {
        return $this->wallet->balance->toFloat();
    }

    public function balanceFiat(): string
    {
        return ExchangeRate::convert($this->balance());
    }

    public function balancePercentage(): float
    {
        return Percentage::calculate($this->wallet->balance->toNumber(), CacheNetworkSupply::execute());
    }

    public function nonce(): int
    {
        return $this->wallet->nonce->toNumber();
    }

    public function voteUrl(): string
    {
        /** @var string $subject */
        $subject = $this->validatorUsername() ?? $this->publicKey();

        return ArkVaultUrlBuilder::get()->generateVote($subject);
    }

    public function name(): ?string
    {
        if ($this->isValidator()) {
            return $this->validatorUsername();
        }

        $knownWallet = $this->findWalletByKnown();
        if ($knownWallet !== null) {
            return $knownWallet['name'];
        }

        return null;
    }
}
