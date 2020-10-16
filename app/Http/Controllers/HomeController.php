<?php

declare(strict_types=1);

namespace  App\Http\Controllers;

use Illuminate\Http\Request;

final class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('home');
    }
}
