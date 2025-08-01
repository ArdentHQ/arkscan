<?php

declare(strict_types=1);

use Tests\Feature\Http\Livewire\__stubs\NormalizesConstantPrefixesStub;

it('should return empty string if default', function () {
    $stub   = new NormalizesConstantPrefixesStub();
    $result = $stub->callNormalizePrefix('default', 'default');

    expect($result)->toBe('');
})->with([
    'default',
    'DEFAULT',
    'default_',
    'DEFAULT_',
]);

it('should return empty string if underscore', function () {
    $stub   = new NormalizesConstantPrefixesStub();
    $result = $stub->callNormalizePrefix('_', 'default');

    expect($result)->toBe('');
});

it('should add an underscore suffix', function () {
    $stub   = new NormalizesConstantPrefixesStub();
    $result = $stub->callNormalizePrefix('custom', 'default');

    expect($result)->toBe('CUSTOM_');
});

it('should replace hyphens with underscore', function () {
    $stub   = new NormalizesConstantPrefixesStub();
    $result = $stub->callNormalizePrefix('custom-prefix', 'default');

    expect($result)->toBe('CUSTOM_PREFIX_');
});
