<?php

declare(strict_types=1);

namespace App\DTO\Search;

use App\DTO\MemoryWallet;
use App\Models\Block;
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

    public static function fromModel(Block $block): self
    {
        return new self(
            hash: $block->hash,
            transactionCount: $block->transactions_count,
            validator: NavbarSearchMemoryWalletData::fromMemoryWallet(MemoryWallet::fromAddress($block->proposer)),
        );
    }
}
