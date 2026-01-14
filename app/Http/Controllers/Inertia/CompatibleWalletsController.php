<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class CompatibleWalletsController
{
    public function __invoke(): Response
    {
        return Inertia::renderWithMeta('Resources/CompatibleWallets', 'compatible-wallets', [

        ]);
    }
}
