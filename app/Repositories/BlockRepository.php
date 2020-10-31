<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\BlockRepository as Contract;
use App\Models\Block;

final class BlockRepository implements Contract
{
    public function findByHeight(int $height): Block
    {
        return Block::where('height', $height)->firstOrFail();
    }
}
