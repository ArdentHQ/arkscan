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
        ->assertSeeHtml('href="https://app.arkvault.io/#/?coin=ARK&amp;nethash='.config('arkscan.networks.development.nethash').'&amp;method=transfer&amp;recipient=DRgF3PvzeGWndQjET7dZsSmnrc6uAy23ES"');
});

it('generates correct url for QR Code on mainnet', function () {
    Config::set('arkscan.network', 'production');

    $wallet = Wallet::factory()->create(['address' => 'AWkBFnqvCF4jhqPSdE2HBPJiwaf67tgfGR']);

    Livewire::test(WalletQrCode::class, ['address' => $wallet->address])
        ->call('toggleQrCode')
        ->assertSeeHtml('href="https://app.arkvault.io/#/?coin=ARK&amp;nethash='.config('arkscan.networks.production.nethash').'&amp;method=transfer&amp;recipient=AWkBFnqvCF4jhqPSdE2HBPJiwaf67tgfGR"');
});

it('should validate amount', function () {
    Config::set('explorer.network', 'production');

    $wallet = Wallet::factory()->create(['address' => 'AWkBFnqvCF4jhqPSdE2HBPJiwaf67tgfGR']);

    Livewire::test(WalletQrCode::class, ['address' => $wallet->address])
        ->set('amount', 1)
        ->assertHasNoErrors()
        ->set('amount', 0)
        ->assertHasErrors()
        ->set('amount', -1)
        ->assertHasErrors();
});

it('should determine if amount is set', function () {
    $wallet = Wallet::factory()->create(['address' => 'AWkBFnqvCF4jhqPSdE2HBPJiwaf67tgfGR']);

    Livewire::test(WalletQrCode::class, ['address' => $wallet->address])
        ->set('amount', 1)
        ->assertSet('hasAmount', true)
        ->set('amount', 0)
        ->assertSet('hasAmount', false)
        ->set('amount', null)
        ->assertSet('hasAmount', false)
        ->set('amount', '')
        ->assertSet('hasAmount', false)
        ->set('amount', 0.00000001)
        ->assertSet('hasAmount', true);
});
