<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;

class RouteController extends Controller
{
    public function switchLang($locale)
    {
        $allowed = ['cz', 'sk', 'en'];

        if (! in_array($locale, $allowed)) {
            $locale = 'en';
        }

        App::setLocale($locale);
        session(['locale' => $locale]);

        return redirect()->back();
    }

    public function home()
    {
        return view('home');
    }

    public function prediction()
    {
        return view('prediction');
    }
}
