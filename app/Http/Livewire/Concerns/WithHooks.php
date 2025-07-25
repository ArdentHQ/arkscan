<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use Illuminate\Support\Str;

trait WithHooks
{
    // Hook logic taken from Livewire\Features\SupportPagination\HandlesPagination
    private function setWithHooks(string $property, mixed $value, ?string $key = null): void
    {
        $beforePaginatorMethod = 'updating'.ucfirst($property);
        $afterPaginatorMethod  = 'updated'.ucfirst($property);

        $beforeMethod = null;
        $afterMethod  = null;
        if ($key !== null) {
            $beforeMethod = 'updating'.ucfirst(Str::camel($key));
            $afterMethod  = 'updated'.ucfirst(Str::camel($key));
        }

        $args = [$value];
        if ($key !== null) {
            $args[] = $key;
        }

        if (method_exists($this, $beforePaginatorMethod)) {
            // @phpstan-ignore-next-line - complains about array not being a valid callable type
            call_user_func_array([$this, $beforePaginatorMethod], $args);
        }

        if ($beforeMethod !== null && method_exists($this, $beforeMethod)) {
            // @phpstan-ignore-next-line - complains about array not being a valid callable type
            call_user_func_array([$this, $beforeMethod], $args);
        }

        if ($key !== null) {
            data_set($this, $property.'.'.$key, $value);
        } else {
            data_set($this, $property, $value);
        }

        if (method_exists($this, $afterPaginatorMethod)) {
            // @phpstan-ignore-next-line - complains about array not being a valid callable type
            call_user_func_array([$this, $afterPaginatorMethod], $args);
        }

        if ($afterMethod !== null && method_exists($this, $afterMethod)) {
            // @phpstan-ignore-next-line - complains about array not being a valid callable type
            call_user_func_array([$this, $afterMethod], $args);
        }
    }
}
