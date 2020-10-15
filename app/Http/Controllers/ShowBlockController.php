<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;

class ShowBlockController extends Controller
{
    public function __invoke(Request $request, Block $block)
    {
        return $block;
    }
}
