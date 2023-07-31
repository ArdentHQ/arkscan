<?php

namespace App\Http\Livewire\Concerns;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator as Base;
use IteratorAggregate;
use Livewire\Wireable;
use Traversable;

class LivewireLengthAwarePaginator implements Wireable, Arrayable, IteratorAggregate//, ArrayAccess, Countable, Jsonable, JsonSerializable
{
    public function __construct(
        public Base $paginator,
        public ?string $model = null,
    ) {
        //
    }

    public function getIterator(): Traversable
    {
        return $this->paginator->getIterator();
    }

    public function __call(string $method, mixed $args)
    {
        return $this->paginator->{$method}(...$args);
    }

    public function toLivewire(): array
    {
        if ($this->items() !== []) {
            // dd($this->toArray());
        }
        return $this->toArray();
    }

    public function toArray(): array
    {
        $items = $this->paginator->items();
        if ($this->model !== null) {
            $items = array_map(fn ($item) => $item->model()->toArray(), $items);
        }

        return [
            'items' => $items,
            'total' => $this->paginator->total(),
            'perPage' => $this->paginator->perPage(),
            'currentPage' => $this->paginator->currentPage(),
            'options' => $this->paginator->getOptions(),
            'model' => $this->model,
        ];
    }

    public static function fromLivewire($value): self
    {
        if ($value['model'] !== null && $value['items'] !== null) {
            $value['items'] = array_map(fn ($item) => (new $value['model']())->fill($item), $value['items']);
        }

        return new static(
            new Base($value['items'], $value['total'], $value['perPage'], $value['currentPage'], $value['options']),
            $value['model']
        );
    }
}
