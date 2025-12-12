<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia\Concerns;

use App\Enums\SortDirection;

trait WithSorting
{
    // TODO: Re-implement sorting once the UI supports it - https://app.clickup.com/t/86dypp5jv
    //       Look at \App\Http\Livewire\Validators\Concerns\MissedBlocksTab for reference.
    //       Also check `getMissedBlocks` below
    private function sortDirection(string $name = 'default'): SortDirection
    {
        return SortDirection::ASC;
    }

    // TODO: Re-implement sorting once the UI supports it - https://app.clickup.com/t/86dypp5jv
    //       Look at \App\Http\Livewire\Validators\Concerns\MissedBlocksTab for reference.
    //       Also check `getMissedBlocks` below
    private function sortKey(string $name = 'default'): string
    {
        return 'rank';
    }
}
