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

#[TypeScript('IValidator')]
class IValidator extends Data
{
    public function __construct(
        public ?int $rank,
        public string $address,
        // public string $balance,
        // public string $nonce,
        // public ?string $public_key,
        public bool $isActive,
        // public bool $isCold,
        // public bool $isValidator,
        // public bool $isLegacy,
        public bool $isDormant,
        public bool $isResigned,
        // public ?string $legacyAddress,
        public ?string $username,
        public bool $hasUsername,
        public int $voterCount,
        public float $votes,
        public float $votesPercentage,
        public int $missedBlocks,
        #[LiteralTypeScriptType('"success" | "warning" | "danger" | "inactive"')]
        public string $missedBlocksState,
        // public float $productivity,
        // public string $formattedBalanceTwoDecimals,
        // public string $formattedBalanceFull,
        // public string $fiatValue,
        // public string $totalForged,
        // // TODO: Consider using another data object for the attributes
        // #[LiteralTypeScriptType('Record<string, any>')]
        // public ?array $attributes,
        // #[LiteralTypeScriptType('IWallet | null')]
        // public ?Wallet $vote,
        // public ?string $voteUrl,
        // public ?float $votePercentage,
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

        $voteUrl = null;
        if ($viewModel->isValidator() && $wallet->public_key !== null) {
            $voteUrl = $viewModel->voteUrl();
        }

        $missedBlocksState = 'success';
        
        $missedBlocks = $viewModel->missedBlocks();
        
        if ($viewModel->isActive()) {
            $missedPercentage = $viewModel->productivity();
    
            if ($missedPercentage < config('arkscan.productivity.danger')) {
                $missedBlocksState = 'danger';
            } elseif ($missedPercentage < config('arkscan.productivity.warning')) {
                $missedBlocksState = 'warning';
            }
        } else {
            $missedBlocksState = 'inactive';
        }

        return new self(
            rank: $viewModel->rank(),
            address: $wallet->address,
            // balance: (string) $wallet->balance,
            // nonce: (string) $wallet->nonce,
            // public_key: $wallet->public_key,
            // attributes: $wallet->attributes,
            isActive: $viewModel->isActive(),
            // isCold: $viewModel->isCold(),
            // isValidator: $viewModel->isValidator(),
            // isLegacy: $viewModel->isLegacy(),
            isDormant: $viewModel->isDormant(),
            isResigned: $viewModel->isResigned(),
            // legacyAddress: $viewModel->legacyAddress(),
            username: $viewModel->username(),
            hasUsername: $viewModel->hasUsername(),
            votes: $viewModel->votes(),
            votesPercentage: $viewModel->votesPercentage(),
            missedBlocks: $missedBlocks,
            missedBlocksState: $missedBlocksState,
            // productivity: $viewModel->productivity(),
            // formattedBalanceTwoDecimals: NumberFormatter::new()->formatWithCurrencyCustom($viewModel->balance(), Network::currency(), 2),
            // formattedBalanceFull: NumberFormatter::new()->formatWithCurrencyCustom($viewModel->balance(), Network::currency(), null),
            // fiatValue: ExchangeRate::convert($wallet->balance, null),
            // totalForged: (string) $viewModel->totalForged(),
            // vote: $votedWallet,
            // voteUrl: $voteUrl,
            // votePercentage: $viewModel->votePercentage(),
            voterCount: $viewModel->voterCount(),
        );
    }
}
