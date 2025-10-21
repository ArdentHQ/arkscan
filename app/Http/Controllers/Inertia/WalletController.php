<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Wallet as WalletDTO;
use App\Models\Wallet;
use Inertia\Inertia;
use Inertia\Response;

final class WalletController
{
    public function __invoke(Wallet $wallet): Response
    {
        return Inertia::render('Wallet', [
            'wallet' => WalletDTO::fromModel($wallet),
        ]);
    }
}
