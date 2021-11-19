<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\Component;

final class TableSkeleton extends Component
{
    public string $class;

    private Collection $items;

    public function __construct(private string $device, array $items, string $class = 'hidden md:block')
    {
        $this->items = collect($items);
        $this->class = $class;
    }

    public function render(): View
    {
        if ($this->device === 'desktop') {
            $headers = $this->items->map(function ($item): array {
                return array_merge(
                    ['component' => "tables.headers.{$this->device}.".$this->getType($item)],
                    $this->getOptions($item)
                );
            });
            $rows    = $this->items->values()->map(function ($item): array {
                return array_merge(
                    ['component' => "tables.rows.{$this->device}.skeletons.".$this->getType($item)],
                    $this->getOptions($item)
                );
            });
        } else {
            $headers = collect([]); // Mobile has no separate headers
            $rows    = $this->items->map(function ($item): array {
                return array_merge(
                    ['component' => "tables.rows.{$this->device}.skeletons.".$this->getType($item)],
                    $this->getOptions($item)
                );
            });
        }

        return ViewFacade::make("components.tables.skeletons.{$this->device}", [
            'headers' => $headers->toArray(),
            'rows'    => $rows->toArray(),
        ]);
    }

    private function getType(array | string $item): string
    {
        if (is_string($item)) {
            return $item;
        }

        return Arr::get($item, 'type', '');
    }

    private function getOptions(array | string $item): array
    {
        if (is_string($item)) {
            return [];
        }

        return $item;
    }
}
