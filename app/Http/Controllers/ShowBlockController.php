<?php

declare(strict_types=1);

namespace  App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;

final class ShowBlockController extends Controller
{
    public function __invoke(Request $request, Block $block)
    {
        return $block;
    }
}
