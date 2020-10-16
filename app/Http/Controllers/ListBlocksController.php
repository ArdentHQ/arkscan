<?php

declare(strict_types=1);

namespace  App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;

final class ListBlocksController extends Controller
{
    public function __invoke(Request $request)
    {
        return Block::paginate();
    }
}
