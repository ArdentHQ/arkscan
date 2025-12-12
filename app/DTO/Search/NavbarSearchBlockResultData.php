<?php

declare(strict_types=1);

namespace App\DTO\Search;

use App\ViewModels\BlockViewModel;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('INavbarSearchBlockResultData')]
final class NavbarSearchBlockResultData extends Data
{
    public function __construct(
        public string $hash,
        public int $transactionCount,
        public ?NavbarSearchMemoryWalletData $validator,
    ) {
    }

    public static function fromViewModel(BlockViewModel $block): self
    {
        return new self(
            hash: $block->hash(),
            transactionCount: $block->transactionCount(),
            validator: NavbarSearchMemoryWalletData::fromMemoryWallet($block->validator()),
        );
    }
}
