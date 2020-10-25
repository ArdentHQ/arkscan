<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Services\QRCode;
use Livewire\Component;

final class WalletQrCode extends Component
{
    public string $address;

    public int $amount = 10;

    public string $smartbridge = 'Hello';

    public function getCodeProperty(): string
    {
        return QRCode::generate(sprintf(
            'ark:transfer?recipient=%s&amount=%s&vendorField=%s',
            $this->address,
            $this->amount,
            $this->smartbridge,
        ));
    }
}
