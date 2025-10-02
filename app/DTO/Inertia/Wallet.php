<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\Facades\Network;
use App\Models\Wallet as Model;
use App\Services\ExchangeRate;
use App\ViewModels\WalletViewModel;
use ARKEcosystem\Foundation\NumberFormatter\NumberFormatter;

class Wallet
{
    private WalletViewModel $viewModel;

    public function __construct(public Model $wallet)
    {
        $this->viewModel = new WalletViewModel($wallet);
    }

    public function toArray(): array
    {
        $votedWallet = null;
        $vote        = $this->viewModel->vote();
        if ($vote !== null) {
            $votedWallet = (new self($vote->model()))->toArray();
        }

        return [
            'address'       => $this->wallet->address,
            'attributes'    => $this->wallet->attributes,
            'balance'       => (string) $this->wallet->balance,
            'nonce'         => (string) $this->wallet->nonce,
            'public_key'    => $this->wallet->public_key,

            'isActive'      => $this->viewModel->isActive(),
            'isCold'        => $this->viewModel->isCold(),
            'isValidator'   => $this->viewModel->isValidator(),
            'isLegacy'      => $this->viewModel->isLegacy(),
            'isDormant'     => $this->viewModel->isDormant(),
            'legacyAddress' => $this->viewModel->legacyAddress(),
            'username'      => $this->viewModel->username(),
            'vote'          => $votedWallet,
            'votes'         => (string) $this->viewModel->votes(),
            'productivity'  => $this->viewModel->productivity(),

            'formattedBalanceTwoDecimals' => NumberFormatter::new()->formatWithCurrencyCustom($this->viewModel->balance(), Network::currency(), 2),
            'formattedBalanceFull'        => NumberFormatter::new()->formatWithCurrencyCustom($this->viewModel->balance(), Network::currency(), null),
            'fiatValue'                   => ExchangeRate::convert($this->wallet->balance, null),
            'totalForged'                 => (string) $this->viewModel->totalForged(),
        ];
    }
}
