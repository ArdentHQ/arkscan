<?php

declare(strict_types=1);

namespace App\Console\Commands\Concerns;

use App\Events\Statistics\StatisticsUpdate;

trait DispatchesStatisticsEvents
{
    private bool $hasChanges = false;

    private function dispatchEvent(string $event): void
    {
        if (! $this->hasChanges) {
            return;
        }

        if (! is_subclass_of($event, StatisticsUpdate::class)) {
            return;
        }

        $event::dispatch();

        $this->hasChanges = false;
    }
}
