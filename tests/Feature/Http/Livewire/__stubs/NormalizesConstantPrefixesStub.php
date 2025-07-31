<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Livewire\__stubs;

use App\Http\Livewire\Concerns\NormalizesConstantPrefixes;

/**
 * @coversNothing
 */
class NormalizesConstantPrefixesStub
{
    use NormalizesConstantPrefixes;

    public function callNormalizePrefix(string $prefix, string $default): string
    {
        return static::normalizePrefix($prefix, $default);
    }
}
