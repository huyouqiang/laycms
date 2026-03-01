<?php

namespace App\Http\Controllers;

use App\Models\Form;

class DashboardController extends Controller
{
    public function index()
    {
        $forms = Form::orderBy('sort_order')->get();
        return view('dashboard', compact('forms'));
    }
}
