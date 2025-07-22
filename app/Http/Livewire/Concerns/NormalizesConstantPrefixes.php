<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use Illuminate\Support\Str;

trait NormalizesConstantPrefixes
{
    protected static function normalizePrefix(string $prefix, string $default): string
    {
        if (Str::of($prefix)->lower()->trim('_') === $default) {
            $prefix = '';
        } elseif ($prefix === '_') {
            $prefix = '';
        }

        if ($prefix === '') {
            return '';
        }

        $prefix = (string) Str::of($prefix)
            ->replace('-', '_')
            ->upper();

        if (! Str::endsWith($prefix, '_')) {
            $prefix .= '_';
        }

        return $prefix;
    }
}
