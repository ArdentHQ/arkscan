<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\BlockRepository as Contract;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;

final class BlockRepository implements Contract
{
    public function findByHash($hash): Block
    {
        return Block::where('hash', $hash)->firstOrFail();
    }

    public function findByHeight($height): Block
    {
        return Block::where('number', $height)->firstOrFail();
    }

    public function findByIdentifier($identifier): Block
    {
        return Block::query()
            ->where('hash', $identifier)
            ->orWhere('number', (int) $identifier)
            ->firstOrFail();
    }

    public function last(): Block
    {
        /** @var Block $block */
        $block = Block::withScope(OrderByHeightScope::class)
            ->firstOrFail();

        return $block;
    }
}
