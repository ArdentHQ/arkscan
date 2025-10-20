<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Facades\Network;
use App\Models\Wallet as Model;
use App\Services\ExchangeRate;
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
        public bool $isActive,
        public bool $isCold,
        public bool $isValidator,
        public bool $isLegacy,
        public bool $isDormant,
        public bool $isResigned,
        public ?string $legacyAddress,
        public ?string $username,
        public string $votes,
        public float $productivity,
        public string $formattedBalanceTwoDecimals,
        public string $formattedBalanceFull,
        public string $fiatValue,
        public string $totalForged,
        // TODO: Consider using another data object for the attributes
        #[LiteralTypeScriptType('Record<string, any>')]
        public ?array $attributes,
        public ?self $vote,
    ) {
    }

    public static function fromModel(Model $wallet): self
    {
        $viewModel   = new WalletViewModel($wallet);
        $votedWallet = null;

        $vote        = $viewModel->vote();
        if ($vote !== null) {
            $votedWallet = self::fromModel($vote->model());
        }

        return new self(
            address: $wallet->address,
            balance: (string) $wallet->balance,
            nonce: (string) $wallet->nonce,
            public_key: $wallet->public_key,
            attributes: $wallet->attributes,
            isActive: $viewModel->isActive(),
            isCold: $viewModel->isCold(),
            isValidator: $viewModel->isValidator(),
            isLegacy: $viewModel->isLegacy(),
            isDormant: $viewModel->isDormant(),
            isResigned: $viewModel->isResigned(),
            legacyAddress: $viewModel->legacyAddress(),
            username: $viewModel->username(),
            votes: (string) $viewModel->votes(),
            productivity: $viewModel->productivity(),
            formattedBalanceTwoDecimals: NumberFormatter::new()->formatWithCurrencyCustom($viewModel->balance(), Network::currency(), 2),
            formattedBalanceFull: NumberFormatter::new()->formatWithCurrencyCustom($viewModel->balance(), Network::currency(), null),
            fiatValue: ExchangeRate::convert($wallet->balance, null),
            totalForged: (string) $viewModel->totalForged(),
            vote: $votedWallet,
        );
    }
}
