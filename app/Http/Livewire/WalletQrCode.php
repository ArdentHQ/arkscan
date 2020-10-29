<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Services\QRCode;
use Livewire\Component;

final class WalletQrCode extends Component
{
    public bool $isOpen = false;

    public string $address;

    public int $amount = 10;

    public string $smartbridge = 'Hello';

    /** @phpstan-ignore-next-line */
    protected $listeners = ['toggleQrCode'];

    // @codeCoverageIgnore
    public function updated(string $propertyName): void
    {
        $this->validateOnly($propertyName, [
            'amount'      => ['required', 'numeric'],
            'smartbridge' => ['required', 'string', 'max:255'],
        ]);
    }

    public function toggleQrCode(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

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
