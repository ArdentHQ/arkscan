<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;

class ListTransactionsByBlockController extends Controller
{
    public function __invoke(Request $request, Block $block)
    {
        return $block->transactions()->paginate();
    }
}
