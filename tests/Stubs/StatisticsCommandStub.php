<?php

declare(strict_types=1);

namespace Tests\Stubs;

use App\Console\Commands\Concerns\DispatchesStatisticsEvents;
use App\Events\NewBlock;

class StatisticsCommandStub
{
    use DispatchesStatisticsEvents;

    public function noEventTrigger(): void
    {
        $this->hasChanges = true;

        $this->dispatchEvent(NewBlock::class);
    }
}
