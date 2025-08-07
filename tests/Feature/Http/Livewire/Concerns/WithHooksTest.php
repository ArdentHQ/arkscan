<?php

declare(strict_types=1);

use Tests\Feature\Http\Livewire\__stubs\WithHooksStub;

it('should call hooks for non-array', function () {
    $stub = new WithHooksStub();
    $stub->callSetWithHooks('testProperty', 'new value');

    expect($stub->hooksCalled)->toBe([
        'updatingTestProperty:new value',
        'updatedTestProperty:new value',
    ]);

    expect($stub->testProperty)->toBe('new value');
});

it('should call hooks for multi-dimensional array', function () {
    $stub = new WithHooksStub();
    $stub->callSetWithHooks('testMultidimentionalArray', 'new value', 'default.value');

    expect($stub->hooksCalled)->toBe([
        'updatingTestMultidimentionalArray.default.value:new value',
        'updatingTestMultidimentionalArrayDefaultValue:new value',
        'updatedTestMultidimentionalArray.default.value:new value',
        'updatedTestMultidimentionalArrayDefaultValue:new value',
    ]);

    expect($stub->testMultidimentionalArray)->toBe([
        'default' => [
            'value' => 'new value',
        ],
    ]);
});

it('should call hooks for single-dimensional array', function () {
    $stub = new WithHooksStub();
    $stub->callSetWithHooks('testArray', 'new value', 'default');

    expect($stub->hooksCalled)->toBe([
        'updatingTestArray.default:new value',
        'updatingTestArrayDefault:new value',
        'updatedTestArray.default:new value',
        'updatedTestArrayDefault:new value',
    ]);

    expect($stub->testArray)->toBe([
        'default' => 'new value',
    ]);
});
