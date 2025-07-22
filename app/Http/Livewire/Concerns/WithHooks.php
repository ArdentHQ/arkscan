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

        $beforeMethod = 'updating'.ucfirst(Str::camel($key));
        $afterMethod  = 'updated'.ucfirst(Str::camel($key));

        $args = [$value];
        if ($key !== null) {
            $args[] = $key;
        }

        if (method_exists($this, $beforePaginatorMethod)) {
            call_user_func_array([$this, $beforePaginatorMethod], $args);
        }

        if (method_exists($this, $beforeMethod)) {
            call_user_func_array([$this, $beforeMethod], $args);
        }

        if ($key !== null) {
            data_set($this->{$property}, $key, $value);
        } else {
            $this->{$property} = $value;
        }

        if (method_exists($this, $afterPaginatorMethod)) {
            call_user_func_array([$this, $afterPaginatorMethod], $args);
        }

        if (method_exists($this, $afterMethod)) {
            call_user_func_array([$this, $afterMethod], $args);
        }
    }
}
