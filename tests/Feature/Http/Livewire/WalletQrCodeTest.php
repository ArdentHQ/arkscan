<?php

declare(strict_types=1);

use App\Http\Livewire\WalletQrCode;
use App\Models\Wallet;
use Livewire\Livewire;

it('should render the QR Code', function () {
    $wallet = Wallet::factory()->create(['address' => 'DRgF3PvzeGWndQjET7dZsSmnrc6uAy23ES']);

    $component = Livewire::test(WalletQrCode::class, ['address' => $wallet->address]);
    $component->assertSee('svg');
});

it('should toggle the QR Code', function () {
    $wallet = Wallet::factory()->create(['address' => 'DRgF3PvzeGWndQjET7dZsSmnrc6uAy23ES']);

    $component = Livewire::test(WalletQrCode::class, ['address' => $wallet->address]);
    $component->assertSet('modalShown', false);
    $component->call('toggleQrCode');
    $component->assertSet('modalShown', true);
    $component->call('toggleQrCode');
    $component->assertSet('modalShown', false);
});
