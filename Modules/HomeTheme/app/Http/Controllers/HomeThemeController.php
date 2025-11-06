<?php

namespace Modules\HomeTheme\Http\Controllers;

use App\Http\Controllers\Controller;

class HomeThemeController extends Controller
{
    public function index()
    {
        $page = load_page('/');

        return view('hometheme::index', ['page' => $page]);
    }
}
