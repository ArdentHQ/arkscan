<?php

declare(strict_types=1);

use Livewire\Livewire;
use Tests\Feature\Http\Livewire\__stubs\ComponentWithFilterStub;
use Tests\Feature\Http\Livewire\__stubs\ComponentWithFilterWithoutDefaultStub;
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

it('should populate filters', function () {
    Livewire::test(ComponentWithFilterStub::class)
        ->assertSet('filters.testing.is_true', true)
        ->assertSet('filters.testing.is_false', false);
});

it('should populate filters from querystring', function () {
    Livewire::withQueryParams(['is_true' => 'false', 'is_false' => 'true'])
        ->test(ComponentWithFilterStub::class)
        ->assertSet('filters.testing.is_true', false)
        ->assertSet('filters.testing.is_false', true);
});

it('should ignore filters if already set', function () {
    Livewire::test(ComponentWithFilterStub::class)
        ->set('filters', [
            'testing' => [
                'is_true'  => false,
                'is_false' => true,
            ],
        ])
        ->assertSet('filters.testing.is_true', false)
        ->assertSet('filters.testing.is_false', true);
});

it('should normalize filter types', function () {
    Livewire::withQueryParams(['is_true' => 'false', 'is_false' => 'true'])
        ->test(ComponentWithFilterWithoutDefaultStub::class)
        ->assertSet('filters.default.is_true', false)
        ->assertSet('filters.default.is_false', true);
});
