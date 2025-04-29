<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\BlockRepository as Contract;
use App\Models\Block;

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
}
