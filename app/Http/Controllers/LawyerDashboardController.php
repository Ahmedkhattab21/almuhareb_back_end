<?php

namespace App\Http\Controllers;

class LawyerDashboardController extends Controller
{
    public function index()
    {
        return view('lawyer.dashboard');
    }
}
