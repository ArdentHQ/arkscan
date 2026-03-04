<?php

declare(strict_types=1);

namespace Tests\Stubs;

use App\Contracts\BlockRepository;
use App\Models\Block;

class BlockRepositoryStub implements BlockRepository
{
    public int $findByHashCalls = 0;

    public int $findByHeightCalls = 0;

    public int $findByIdentifierCalls = 0;

    public function __construct(private Block $block)
    {
        //
    }

    public function findByHash($hash): Block
    {
        $this->findByHashCalls++;

        return $this->block;
    }

    public function findByHeight($height): Block
    {
        $this->findByHeightCalls++;

        return $this->block;
    }

    public function findByIdentifier($height): Block
    {
        $this->findByIdentifierCalls++;

        return $this->block;
    }

    public function last(): Block
    {
        return $this->block;
    }
}
