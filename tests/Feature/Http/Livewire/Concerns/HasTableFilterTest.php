<?php

declare(strict_types=1);

use Tests\Feature\Http\Livewire\__stubs\HasTableFilterStub;

it('should load default filter data', function () {
    $instance = new HasTableFilterStub();

    $instance->filters = [
        'default' => [
            'testing' => true,
        ],
    ];

    $instance->mountHasTableFilter();

    expect($instance->filters['default'])->toBe(['testing' => true]);
    expect($instance->selectAllFilters['default'])->toBe(true);
});

it('should create filter entry if it does not exist', function () {
    $instance = new HasTableFilterStub();

    expect($instance->filters)->toBe([]);

    $instance->setFilter('testing', true, 'test-page');

    expect($instance->filters)->toBe(['test-page' => ['testing' => true]]);
});

it('should get default filter if const exists', function () {
    $instance = new HasTableFilterStub();

    expect($instance->defaultFilters('stub'))->toBe([
        'testing' => true,
    ]);
});

it('should not get default filter if const does not exist', function () {
    $instance = new HasTableFilterStub();

    expect($instance->defaultFilters('testing'))->toBe([]);
});
