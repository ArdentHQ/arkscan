<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Models\Block;

final class BlockViewModel implements ViewModel
{
    public function __construct(private Block $block)
    {
    }

    public function url(): string
    {
        return route('block', $this->hash());
    }

    public function hash(): string
    {
        return $this->block->hash;
    }
}
