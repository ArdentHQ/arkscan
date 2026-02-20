<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Facades\Network;
use App\Models\Wallet as Model;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter as ExplorerNumberFormatter;
use App\ViewModels\WalletViewModel;
use ARKEcosystem\Foundation\NumberFormatter\NumberFormatter;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('IWallet')]
class Wallet extends Data
{
    public function __construct(
        public string $address,
        public string $balance,
        public string $nonce,
        public ?string $public_key,
        public ?string $legacyAddress,
        public ?string $username,
        public string $votes,
        public float $productivity,
        public float $balancePercentage,
        public string $formattedBalanceTwoDecimals,
        public string $formattedBalanceFull,
        public string $formattedBalanceFullWithoutSuffix,
        public string $fiatValue,
        public string $totalForged,
        // TODO: Consider using another data object for the attributes
        #[LiteralTypeScriptType('Record<string, any>')]
        public ?array $attributes,
        public ?self $vote,
        public ?string $voteUrl,
        public ?float $votePercentage,
    ) {
    }

    public static function stub(string $address): self
    {
        return new self(
            address: $address,
            balance: '0',
            nonce: '0',
            public_key: null,
            attributes: null,
            legacyAddress: null,
            username: null,
            votes: '0',
            productivity: 0.0,
            balancePercentage: 0.0,
            formattedBalanceTwoDecimals: NumberFormatter::new()->formatWithCurrencyCustom(0, Network::currency(), 2),
            formattedBalanceFull: NumberFormatter::new()->formatWithCurrencyCustom(0, Network::currency(), null),
            formattedBalanceFullWithoutSuffix: '0',
            fiatValue: ExchangeRate::convert(0, null),
            totalForged: '0',
            vote: null,
            voteUrl: null,
            votePercentage: null,
        );
    }

    public static function fromModel(Model $wallet, bool $isVote = false): self
    {
        $viewModel   = new WalletViewModel($wallet);
        $votedWallet = null;

        if (! $isVote) {
            $vote = $viewModel->vote();
            if ($vote !== null) {
                $votedWallet = self::fromModel($vote->model(), true);
            }
        }

        $voteUrl = null;
        if ($viewModel->isValidator() && $wallet->public_key !== null) {
            $voteUrl = $viewModel->voteUrl();
        }

        return new self(
            address: $wallet->address,
            balance: (string) $wallet->balance,
            nonce: (string) $wallet->nonce,
            public_key: $wallet->public_key,
            attributes: $wallet->attributes,
            legacyAddress: $viewModel->legacyAddress(),
            username: $viewModel->username(),
            votes: (string) $viewModel->votes(),
            productivity: $viewModel->productivity(),
            balancePercentage: $viewModel->balancePercentage(),
            formattedBalanceTwoDecimals: NumberFormatter::new()->formatWithCurrencyCustom($viewModel->balance(), Network::currency(), 2),
            formattedBalanceFull: NumberFormatter::new()->formatWithCurrencyCustom($viewModel->balance(), Network::currency(), null),
            formattedBalanceFullWithoutSuffix: ExplorerNumberFormatter::currencyWithoutSuffix($viewModel->balance(), Network::currency()),
            fiatValue: ExchangeRate::convert($wallet->balance, null),
            totalForged: (string) $viewModel->totalForged(),
            vote: $votedWallet,
            voteUrl: $voteUrl,
            votePercentage: $viewModel->votePercentage(),
        );
    }
}
