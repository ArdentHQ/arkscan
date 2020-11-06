<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\BlockRepository;
use App\Models\Block;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class BlockRepositoryWithCache implements BlockRepository
{
    use Concerns\ManagesCache;

    private BlockRepository $blocks;

    public function __construct(BlockRepository $blocks)
    {
        $this->blocks = $blocks;
    }

    public function findById($id): Block
    {
        return $this->remember(fn () => $this->blocks->findById($id));
    }

    public function findByHeight($height): Block
    {
        return $this->remember(fn () => $this->blocks->findByHeight($height));
    }

    public function findByIdentifier($identifier): Block
    {
        return $this->remember(fn () => $this->blocks->findByIdentifier($identifier));
    }

    private function getCache(): TaggedCache
    {
        return Cache::tags('blocks');
    }
}
