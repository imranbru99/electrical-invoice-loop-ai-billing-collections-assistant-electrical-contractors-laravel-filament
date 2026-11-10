<?php

namespace App\Http\Controllers;

use App\Support\Modules;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function home(): View
    {
        return view('marketing.home', [
            'modules' => Modules::all(),
        ]);
    }

    public function features(): View
    {
        return view('marketing.features', [
            'modules' => Modules::all(),
        ]);
    }

    public function workflow(): View
    {
        return view('marketing.workflow');
    }

    public function pricing(): View
    {
        return view('marketing.pricing');
    }

    public function modules(): View
    {
        return view('marketing.modules', [
            'modules' => Modules::all(),
        ]);
    }

    public function demo(): View
    {
        return view('marketing.demo');
    }
}
