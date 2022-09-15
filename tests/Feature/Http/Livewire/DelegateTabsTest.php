<?php

declare(strict_types=1);

use App\Http\Livewire\DelegateTabs;
use App\Models\Wallet;
use Livewire\Livewire;

it('should render without errors', function () {
    Livewire::test(DelegateTabs::class)
        ->assertSeeHtml(trans('pages.delegates.monitor'));
});

it('should get active delegates', function () {
    $instance = Livewire::test(DelegateTabs::class)
        ->instance();

    expect($instance->activeQuery()->count())->toBe(0);

    Wallet::factory(51)
        ->activeDelegate()
        ->create();

    Wallet::factory(15)
        ->standbyDelegate(false)
        ->create();

    Wallet::factory(9)
        ->standbyDelegate(true)
        ->create();

    expect($instance->activeQuery()->count())->toBe(51);
});

it('should get standby delegates', function () {
    $instance = Livewire::test(DelegateTabs::class)
        ->instance();

    expect($instance->activeQuery()->count())->toBe(0);

    Wallet::factory(51)
        ->activeDelegate()
        ->create();

    Wallet::factory(15)
        ->standbyDelegate(false)
        ->create();

    Wallet::factory(9)
        ->standbyDelegate(true)
        ->create();

    expect($instance->standbyQuery()->count())->toBe(15);
});

it('should get resigned delegates', function () {
    $instance = Livewire::test(DelegateTabs::class)
        ->instance();

    expect($instance->activeQuery()->count())->toBe(0);

    Wallet::factory(51)
        ->activeDelegate()
        ->create();

    Wallet::factory(15)
        ->standbyDelegate(false)
        ->create();

    Wallet::factory(9)
        ->standbyDelegate(true)
        ->create();

    expect($instance->resignedQuery()->count())->toBe(9);
});
