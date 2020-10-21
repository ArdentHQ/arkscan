<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

final class ShowBlockController
{
    public function __invoke(Request $request, Block $block): \Illuminate\Http\Response
    {
        return Response::noContent();
    }
}
