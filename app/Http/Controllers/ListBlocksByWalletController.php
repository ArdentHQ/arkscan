<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\View\View;

final class ListBlocksByWalletController
{
    public function __invoke(Wallet $wallet): View
    {
        /** @var WalletViewModel */
        $viewModel = ViewModelFactory::make($wallet);

        if (! $viewModel->isDelegate()) {
            abort(404);
        }

        return view('app.blocks-by-wallet', [
            'wallet' => $viewModel,
        ]);
    }
}
