<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Block;

use App\Models\Block;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

trait InteractsWithNeighbours
{
    public function previousBlockUrl(): ?string
    {
        return $this->findBlockWithHeight($this->block->height->minus(1)->toNumber());
    }

    public function nextBlockUrl(): ?string
    {
        return $this->findBlockWithHeight($this->block->height->plus(1)->toNumber());
    }

    private function findBlockWithHeight(int $height): ?string
    {
        $block = Cache::remember(
            "block:neighbour:$height",
            Carbon::now()->addHour(),
            fn () => Block::where('height', $height)->first()
        );

        if (is_null($block)) {
            return null;
        }

        return route('block', $block);
    }
}
