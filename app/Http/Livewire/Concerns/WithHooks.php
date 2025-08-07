<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use Illuminate\Support\Str;

trait WithHooks
{
    // Hook logic taken from Livewire\Features\SupportPagination\HandlesPagination
    private function setWithHooks(string $property, mixed $value, ?string $key = null): void
    {
        $beforeMethod = 'updating'.ucfirst($property);
        $afterMethod  = 'updated'.ucfirst($property);

        $beforePropertyMethod = null;
        $afterPropertyMethod  = null;
        if ($key !== null) {
            $beforePropertyMethod = 'updating'.ucfirst(Str::of($key)->replace('.', ' ')->camel()->toString());
            $afterPropertyMethod  = 'updated'.ucfirst(Str::of($key)->replace('.', ' ')->camel()->toString());
        }

        $args = [$value];
        if ($key !== null) {
            $args[] = $key;
        }

        if (method_exists($this, $beforeMethod)) {
            // @phpstan-ignore-next-line - complains about array not being a valid callable type
            call_user_func_array([$this, $beforeMethod], $args);
        }

        if ($beforePropertyMethod !== null && method_exists($this, $beforePropertyMethod)) {
            // @phpstan-ignore-next-line - complains about array not being a valid callable type
            call_user_func_array([$this, $beforePropertyMethod], $args);
        }

        if ($key !== null) {
            data_set($this, $property.'.'.$key, $value);
        } else {
            data_set($this, $property, $value);
        }

        if (method_exists($this, $afterMethod)) {
            // @phpstan-ignore-next-line - complains about array not being a valid callable type
            call_user_func_array([$this, $afterMethod], $args);
        }

        if ($afterPropertyMethod !== null && method_exists($this, $afterPropertyMethod)) {
            // @phpstan-ignore-next-line - complains about array not being a valid callable type
            call_user_func_array([$this, $afterPropertyMethod], $args);
        }
    }
}
