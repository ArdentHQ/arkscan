<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;

final class TableSkeleton extends Component
{
    private Collection $items;

    public function __construct(private string $device, array $items)
    {
        $this->items   = collect($items);
    }

    public function render(): View
    {
        if ($this->device === 'desktop') {
            $headers = $this->items->map(fn ($name) => "tables.headers.{$this->device}.$name");
            $rows    = $this->items->values()->map(fn ($name) => "tables.rows.{$this->device}.skeletons.$name");
        } else {
            $headers = collect([]); // Mobile has no separate headers
            $rows    = $this->items->map(fn ($name) => "tables.rows.{$this->device}.skeletons.$name");
        }

        /* @phpstan-ignore-next-line */
        return view("components.tables.skeletons.{$this->device}", [
            'headers' => $headers->toArray(),
            'rows'    => $rows->toArray(),
        ]);
    }
}
