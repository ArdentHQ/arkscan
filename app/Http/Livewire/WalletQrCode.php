<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Services\ArkVaultUrlBuilder;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Support\QRCode;
use BaconQrCode\Renderer\Color\Alpha;
use BaconQrCode\Renderer\Color\Gray;
use BaconQrCode\Renderer\RendererStyle\Fill;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * @property string $walletUri
 */
final class WalletQrCode extends Component
{
    public const QR_CODE_SIZE = 224;

    public string $address;

    public ?string $amount = null;

    public ?string $class = null;

    public function mount(?string $class = null): void
    {
        $this->class = $class;
    }

    public function render(): View
    {
        return view('livewire.wallet-qr-code', [
            'wallet' => ViewModelFactory::make(Wallets::findByAddress($this->address)),
        ]);
    }

    public function updated(string $propertyName): void
    {
        $this->validateOnly($propertyName, [
            'amount' => ['numeric', 'min:0.00000001'],
        ]);
    }

    public function getWalletUriProperty(): string
    {
        $options = [];

        if ($this->amount !== null && $this->amount !== '' && floatval($this->amount) > 0) {
            $options['amount'] = $this->amount;
        }

        return ArkVaultUrlBuilder::get()->generateTransfer($this->address, $options);
    }

    public function getCodeProperty(): string
    {
        return QRCode::generate(
            $this->walletUri,
            self::QR_CODE_SIZE,
            Fill::uniformColor(new Alpha(0, new Gray(0)), new Gray(0)),
        );
    }

    public function getHasAmountProperty(): bool
    {
        if ($this->amount === null) {
            return false;
        }

        if ($this->amount === '') {
            return false;
        }

        return floatval($this->amount) > 0;
    }
}
