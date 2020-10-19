<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

final class ListBlocksController
{
    public function __invoke(Request $request)
    {
        return Response::noContent();
    }
}
