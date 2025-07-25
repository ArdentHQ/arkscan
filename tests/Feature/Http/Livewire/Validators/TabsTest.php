<?php

declare(strict_types=1);

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
