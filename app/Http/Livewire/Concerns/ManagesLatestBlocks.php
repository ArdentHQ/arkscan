<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Models\Block;
use App\Services\Cache\TableCache;

trait ManagesLatestBlocks
{
    public function pollBlocks(): void
    {
        $this->blocks = (new TableCache())->setLatestBlocks(fn () => Block::latestByHeight()->take(15)->get());
    }
}
