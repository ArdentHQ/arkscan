<?php

declare(strict_types=1);

use App\Http\Livewire\WalletQrCode;
use App\Models\Wallet;
use Illuminate\Support\Facades\Config;
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

it('generates correct url for QR Code', function () {
    $wallet = Wallet::factory()->create(['address' => 'DRgF3PvzeGWndQjET7dZsSmnrc6uAy23ES']);

    Livewire::test(WalletQrCode::class, ['address' => $wallet->address])
        ->call('toggleQrCode')
        ->assertSeeHtml('href="https://app.arkvault.io/#/?method=transfer&amp;recipient=DRgF3PvzeGWndQjET7dZsSmnrc6uAy23ES&amp;coin=ARK&amp;nethash=2a44f340d76ffc3df204c5f38cd355b7496c9065a1ade2ef92071436bd72e867"');
});

it('generates correct url for QR Code on mainnet', function () {
    Config::set('explorer.network', 'production');

    $wallet = Wallet::factory()->create(['address' => 'AWkBFnqvCF4jhqPSdE2HBPJiwaf67tgfGR']);

    Livewire::test(WalletQrCode::class, ['address' => $wallet->address])
        ->call('toggleQrCode')
        ->assertSeeHtml('href="https://app.arkvault.io/#/?method=transfer&amp;recipient=AWkBFnqvCF4jhqPSdE2HBPJiwaf67tgfGR&amp;coin=ARK&amp;nethash=6e84d08bd299ed97c212c886c98a57e36545c8f5d645ca7eeae63a8bd62d8988"');
});
