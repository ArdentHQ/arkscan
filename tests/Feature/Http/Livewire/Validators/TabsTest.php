<?php

declare(strict_types=1);

use App\Enums\SortDirection;
use App\Facades\Network;
use App\Http\Livewire\Validators\Tabs;
use App\Livewire\SupportQueryString;
use Livewire\Livewire;

it('should render', function () {
    Livewire::test(Tabs::class)
        ->assertSee('Validators');
});

it('should set initial data', function () {
    Livewire::test(Tabs::class)
        ->assertSet('tabQueryData', [
            'validators' => [
                'paginators.page' => 1,
                'perPage'         => Network::validatorCount(),
                'sortKey'         => 'rank',
                'sortDirection'   => SortDirection::ASC,
            ],

            'missed-blocks' => [
                'paginators.page' => 1,
                'perPage'         => 25,
                'sortKey'         => 'age',
                'sortDirection'   => SortDirection::DESC,
            ],

            'recent-votes' => [
                'paginators.page' => 1,
                'perPage'         => 25,
                'sortKey'         => 'age',
                'sortDirection'   => SortDirection::DESC,
            ],
        ]);
});

it('should get querystring data', function () {
    $instance = Livewire::test(Tabs::class)
        ->instance();

    expect($instance->queryString())->toBe([
        'view'            => ['except' => 'validators'],
        'paginators.page' => ['except' => 1, 'history' => true],
        'perPage'         => ['except' => Network::validatorCount()],
        'sortKey'         => ['except' => 'rank'],
        'sortDirection'   => ['except' => SortDirection::ASC],
    ]);
});

it('should change querystring if different view', function () {
    $instance = Livewire::test(Tabs::class)
        ->set('view', 'testing')
        ->instance();

    expect($instance->queryString())->toBe([
        'view'            => ['except' => 'validators'],
        'paginators.page' => ['except' => 1, 'history' => true],
        'perPage'         => ['except' => 25],
        'sortKey'         => ['except' => 'rank'],
        'sortDirection'   => ['except' => SortDirection::ASC],
    ]);
});

it('should change view with event', function () {
    Livewire::test(Tabs::class)
        ->assertSet('view', 'validators')
        ->dispatch('showValidatorsView', 'missed-blocks')
        ->assertSet('view', 'missed-blocks')
        ->dispatch('showValidatorsView', 'validators')
        ->assertSet('view', 'validators');
});

it('should apply url values to component', function () {
    $instance = Livewire::withUrlParams(['page' => 3])
        ->test(Tabs::class)
        ->instance();

    expect($instance->getPage())->toBe(3);
});
