<?php

declare(strict_types=1);

namespace Tests\Stubs;

use App\Models\Block;

class RoundsMock
{
    public function __construct(private ?Block $block = null)
    {
        //
    }

    public function delegates()
    {
        return new class($this->block) {
            public function __construct(private ?Block $block = null)
            {
                //
            }

            public function firstWhere()
            {
                return [
                    'status' => 'done',
                    'block'  => $this->block,
                ];
            }
        };
    }
}
