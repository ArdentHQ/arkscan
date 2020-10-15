<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;

class ListBlocksController extends Controller
{
    public function __invoke(Request $request)
    {
        return Block::paginate();
    }
}
