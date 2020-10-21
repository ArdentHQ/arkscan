<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

final class ShowTopWalletsController
{
    public function __invoke(Request $request): \Illuminate\Http\Response
    {
        return Response::noContent();
    }
}
