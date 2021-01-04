<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Block;

use App\Facades\Blocks;
use Throwable;

trait InteractsWithNeighbours
{
    public function previousBlockUrl(): ?string
    {
        return $this->findBlockWithHeight($this->block->height->toNumber() - 1);
    }

    public function nextBlockUrl(): ?string
    {
        return $this->findBlockWithHeight($this->block->height->toNumber() + 1);
    }

    private function findBlockWithHeight(int $height): ?string
    {
        try {
            return route('block', Blocks::findByHeight($height));
        } catch (Throwable) {
            return null;
        }
    }
}
