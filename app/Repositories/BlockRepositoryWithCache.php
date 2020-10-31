<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\BlockRepository;
use App\Models\Block;

final class BlockRepositoryWithCache implements BlockRepository
{
    use Concerns\ManagesCache;

    private BlockRepository $blocks;

    public function __construct(BlockRepository $blocks)
    {
        $this->blocks = $blocks;
    }

    public function findByHeight(int $height): Block
    {
        return $this->remember(fn () => $this->blocks->findByHeight($height));
    }
}
