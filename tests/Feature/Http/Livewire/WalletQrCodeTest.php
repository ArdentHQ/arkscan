<?php

declare(strict_types=1);

use App\Http\Livewire\WalletQrCode;
use Livewire\Livewire;

it('should render the QR Code', function () {
    $component = Livewire::test(WalletQrCode::class, ['address' => 'DRgF3PvzeGWndQjET7dZsSmnrc6uAy23ES']);
    $component->assertSee('svg');
});
