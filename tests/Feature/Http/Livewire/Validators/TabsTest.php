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
                'page'    => 1,
                'perPage' => Network::validatorCount(),
            ],

            'missed-blocks' => [
                'page'    => 1,
                'perPage' => 25,
            ],

            'recent-votes' => [
                'page'    => 1,
                'perPage' => 25,
            ],
        ]);
});

it('should get querystring data', function () {
    $instance = Livewire::test(Tabs::class)
        ->instance();

    expect($instance->queryString())->toBe([
        'view'    => ['except' => 'validators'],
        'page'    => ['except' => 1],
        'perPage' => ['except' => Network::validatorCount()],
    ]);
});

it('should change querystring if different view', function () {
    $instance = Livewire::test(Tabs::class)
        ->set('view', 'testing')
        ->instance();

    expect($instance->queryString())->toBe([
        'view'    => ['except' => 'validators'],
        'page'    => ['except' => 1],
        'perPage' => ['except' => 25],
    ]);
});

it('should change view with event', function () {
    Livewire::test(Tabs::class)
        ->assertSet('view', 'validators')
        ->emit('showValidatorsView', 'missed-blocks')
        ->assertSet('view', 'missed-blocks')
        ->emit('showValidatorsView', 'validators')
        ->assertSet('view', 'validators');
});
