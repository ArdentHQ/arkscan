<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Block;

interface BlockRepository
{
    /**
     * @param int|string $id
     */
    public function findById($id): Block;

    /**
     * @param int|string $height
     */
    public function findByHeight($height): Block;

    /**
     * @param int|string $height
     */
    public function findByIdentifier($height): Block;
}
