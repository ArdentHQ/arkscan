<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Network;
use App\Facades\Wallets;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use ARKEcosystem\Foundation\UserInterface\Support\QRCode;
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
        $uri = config('explorer.vault_url');

        $options = [
            'method'    => 'transfer',
            'coin'      => 'ARK',
            'nethash'   => Network::nethash(),
            'recipient' => $this->address,
        ];

        if ($this->amount !== null && $this->amount !== '') {
            $options['amount'] = $this->amount;
        }

        if ($this->smartbridge !== null && $this->smartbridge !== '') {
            $options['memo'] = rawurlencode($this->smartbridge);
        }

        return $uri.'?'.http_build_query($options);
    }

    // @codeCoverageIgnoreEnd

    public function getCodeProperty(): string
    {
        return QRCode::generate($this->walletUri);
    }
}
