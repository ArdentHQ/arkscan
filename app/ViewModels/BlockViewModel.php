<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Models\Block;
use App\Services\Timestamp;
use Carbon\Carbon;

final class BlockViewModel implements ViewModel
{
    public function __construct(private Block $block)
    {
    }

    public function hash(): string
    {
        return $this->block->hash;
    }

    public function dateTime(): Carbon
    {
        return Timestamp::fromUnix($this->block->timestamp);
    }
}
