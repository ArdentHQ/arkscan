<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Services\QRCode;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * @property string $walletUri
 */
final class WalletQrCode extends Component
{
    use HasModal;

    public string $address;

    public ?string $amount = null;

    public ?string $smartbridge = null;

    public function render(): View
    {
        return view('livewire.wallet-qr-code', [
            'wallet' => ViewModelFactory::make(Wallets::findByAddress($this->address)),
        ]);
    }

    // @codeCoverageIgnoreStart
    public function updated(string $propertyName): void
    {
        $this->validateOnly($propertyName, [
            'amount'      => ['numeric', 'min:0.00000001'],
            'smartbridge' => ['string', 'max:255'],
        ]);
    }

    // @codeCoverageIgnoreEnd

    public function toggleQrCode(): void
    {
        if ($this->modalShown) {
            $this->closeModal();
        } else {
            $this->openModal();

            $this->amount      = null;
            $this->smartbridge = null;
        }
    }

    /**
     * @TODO: use http_build_query once v3 wallet is done
     */
    // @codeCoverageIgnoreStart
    public function getWalletUriProperty(): string
    {
        $uri  = config('explorer.uri_prefix').':'.$this->address;

        $data = '';

        if ($this->amount !== null && $this->amount !== '') {
            $data = 'amount='.$this->amount.'&';
        }

        if ($this->smartbridge !== null && $this->smartbridge !== '') {
            $data .= 'vendorField='.rawurlencode($this->smartbridge);
        }

        $data = trim($data, '&');

        if ($data === '') {
            return $uri;
        }

        return $uri.'?'.$data;
    }

    // @codeCoverageIgnoreEnd

    public function getCodeProperty(): string
    {
        return QRCode::generate($this->walletUri);
    }
}
