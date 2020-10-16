<?php

declare(strict_types=1);

namespace  App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

final class ShowTopWalletsController extends Controller
{
    public function __invoke(Request $request)
    {
        return Wallet::orderBy('balance', 'desc')->paginate();
    }
}
