<?php

declare(strict_types=1);

use App\Enums\SortDirection;
use App\Facades\Network;
use App\Http\Livewire\Validators\Tabs;
use Livewire\Livewire;

it('should render', function () {
    Livewire::test(Tabs::class)
        ->assertSee('Validators');
});

it('should set initial data', function () {
    Livewire::test(Tabs::class)
        ->assertSet('tabQueryData', [
            'validators' => [
                'paginators.validators'        => 1,
                'paginatorsPerPage.validators' => Network::validatorCount(),
                'sortKeys.validators'          => 'rank',
                'sortDirections.validators'    => 'asc',
                'filters.validators.active'    => true,
                'filters.validators.standby'   => true,
                'filters.validators.dormant'   => false,
                'filters.validators.resigned'  => false,
            ],

            'missed-blocks' => [
                'paginators.missed-blocks'        => 1,
                'paginatorsPerPage.missed-blocks' => 25,
                'sortKeys.missed-blocks'          => 'age',
                'sortDirections.missed-blocks'    => 'desc',
            ],

            'recent-votes' => [
                'paginators.recent-votes'        => 1,
                'paginatorsPerPage.recent-votes' => 25,
                'sortKeys.recent-votes'          => 'age',
                'sortDirections.recent-votes'    => 'desc',
                'filters.recent-votes.vote'      => true,
                'filters.recent-votes.unvote'    => true,
            ],
        ]);
});

it('should get querystring data', function () {
    $instance = Livewire::test(Tabs::class)
        ->instance();

    expect($instance->queryString())->toBe([
        'view'    => ['except' => 'validators', 'history' => true],
    ]);
});

it('should change querystring if different view', function () {
    $instance = Livewire::test(Tabs::class)
        ->set('view', 'testing')
        ->instance();

    expect($instance->queryString())->toBe([
        'view' => ['except' => 'validators', 'history' => true],
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

it('should revert to validators tab with unknown view', function () {
    $this->get('/validators?view=unknown')
        ->assertOk();
});

it('should have sorting querystring data', function () {
    $instance = Livewire::test(Tabs::class)
        ->instance();

    expect($instance->queryStringHasTableSorting())->toBe([
        'sortKeys.default'       => ['as' => 'sort', 'except' => 'rank'],
        'sortDirections.default' => ['as' => 'sort-direction', 'except' => 'asc'],
    ]);
});

it('should get and set sort key for current view', function () {
    $component = Livewire::test(Tabs::class)
        ->set('view', 'recent-votes')
        ->call('sortBy', 'age')
        ->assertSet('sortKeys.recent-votes', 'age')
        ->assertSet('sortDirections.recent-votes', SortDirection::ASC)
        ->assertSet('sortKey', 'age')
        ->assertSet('sortDirection', SortDirection::ASC)
        ->set('view', 'missed-blocks')
        ->call('sortBy', 'test-key')
        ->call('sortBy', 'test-key')
        ->assertSet('sortKeys.missed-blocks', 'test-key')
        ->assertSet('sortDirections.recent-votes', SortDirection::DESC)
        ->assertSet('sortKey', 'test-key')
        ->assertSet('sortDirection', SortDirection::DESC);

    expect($component->tabQueryData['missed-blocks']['sortKeys.missed-blocks'])->toBe('test-key');
    expect($component->tabQueryData['missed-blocks']['sortDirections.missed-blocks'])->toBe(SortDirection::DESC);
    expect($component->savedQueryData['recent-votes']['sortKeys.recent-votes'])->toBe('age');
    expect($component->savedQueryData['recent-votes']['sortDirections.recent-votes'])->toBe(SortDirection::ASC);
});

it('should only trigger view is ready if view exists when updating via property', function () {
    Livewire::test(Tabs::class)
        ->set('view', 'unknown')
        ->assertSet('view', 'validators')
        ->assertNotDispatched('setUnknownReady')
        ->set('view', 'missed-blocks')
        ->assertSet('view', 'missed-blocks')
        ->assertDispatched('setMissedBlocksReady')
        ->set('view', 'validators')
        ->assertSet('view', 'validators')
        ->set('view', 'missed-blocks')
        ->assertSet('view', 'missed-blocks')
        ->assertNotDispatched('setMissedBlocksReady');
});

it('should only trigger view is ready if view exists when calling trigger method directly', function () {
    $instance = Livewire::test(Tabs::class)->instance();

    $instance->triggerViewIsReady('unknown');

    expect(array_key_exists('unknown', $instance->alreadyLoadedViews))->toBeFalse();

    $instance->triggerViewIsReady('validators');

    expect($instance->alreadyLoadedViews['validators'])->toBeTrue();
});

it('should throw exception if noResultsMessage is called', function () {
    Livewire::test(Tabs::class)->noResultsMessage;
})->throws(\Exception::class, 'Base getNoResultsMessageProperty not implemented');
