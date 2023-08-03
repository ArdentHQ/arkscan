<?php

declare(strict_types=1);

use App\Http\Livewire\Delegates\Tabs;
use Livewire\Livewire;

it('should render', function () {
    Livewire::test(Tabs::class)
        ->assertSee('Delegates');
});

it('should set initial data', function () {
    Livewire::test(Tabs::class)
        ->assertSet('tabQueryData', [
            'delegates' => [
                'page'    => 1,
                'perPage' => 51,
            ],
        ]);
});

it('should get querystring data', function () {
    $instance = Livewire::test(Tabs::class)
        ->instance();

    expect($instance->queryString())->toBe([
        'view'    => ['except' => 'delegates'],
        'page'    => ['except' => 1],
        'perPage' => ['except' => 51],
    ]);
});

it('should change querystring if different view', function () {
    $instance = Livewire::test(Tabs::class)
        ->set('view', 'testing')
        ->instance();

    expect($instance->queryString())->toBe([
        'view'    => ['except' => 'delegates'],
        'page'    => ['except' => 1],
        'perPage' => ['except' => 25],
    ]);
});
