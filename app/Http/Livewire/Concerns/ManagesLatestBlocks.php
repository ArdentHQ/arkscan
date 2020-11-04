<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Services\Cache\TableCache;

trait ManagesLatestBlocks
{
    public function pollBlocks(): void
    {
        $this->blocks = (new TableCache())->setLatestBlocks(fn () => Block::withScope(OrderByHeightScope::class)->take(15)->get());
    }
}
