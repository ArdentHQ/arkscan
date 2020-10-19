<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

final class ShowWalletController
{
    public function __invoke(Request $request, Wallet $wallet)
    {
        return Response::noContent();
    }
}
