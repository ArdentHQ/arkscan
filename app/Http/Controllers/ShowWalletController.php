<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\View\View;

final class ShowWalletController
{
    public function __invoke(Wallet $wallet): View
    {
        return view('app.wallet', [
            'wallet' => $wallet,
        ]);
    }
}
