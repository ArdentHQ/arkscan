<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\BlockRepository;
use App\Models\Block;
use App\Repositories\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class BlockRepositoryWithCache implements BlockRepository
{
    use ManagesCache;

    public function __construct(private BlockRepository $blocks)
    {
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
