<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use ARKEcosystem\Foundation\UserInterface\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SupportController extends Controller
{
    public function index(Request $request): View
    {
        return view('app.support');
    }
}
