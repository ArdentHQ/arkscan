<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Block;

interface BlockRepository
{
    public function findByHeight(int $height): Block;
}
