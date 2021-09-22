<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use Illuminate\View\View;

final class ListBlocksByWalletController
{
    public function __invoke(Wallet $wallet): View
    {
        return view('app.blocks-by-wallet', [
            'wallet' => ViewModelFactory::make($wallet),
        ]);
    }
}
