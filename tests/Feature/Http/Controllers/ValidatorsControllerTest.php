<?php

declare(strict_types=1);

use App\Models\Wallet;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Compilers\BladeCompiler;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this
        ->get(route('validators'))
        ->assertOk();
});

it('should filter validators via url', function () {
    $wallet = Wallet::factory()
        ->resignedValidator()
        ->create();

    Route::get('/test-validators', function () {
        return BladeCompiler::render('<livewire:validators.tabs :defer-loading="false" />');
    });

    $this
        ->get('/test-validators/')
        ->assertOk()
        ->assertSee($wallet->hash);

    $this
        ->get('/test-validators?resigned=false')
        ->assertOk()
        ->assertDontSee($wallet->hash);

    $this
        ->get('/test-validators?resigned=0')
        ->assertOk()
        ->assertDontSee($wallet->hash);

    $this
        ->get('/test-validators?resigned=1')
        ->assertOk()
        ->assertSee($wallet->hash);
});
