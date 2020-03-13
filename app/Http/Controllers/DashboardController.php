<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showByTab($tab)
    {
        return redirect()->to('/settings#/'.$tab);
    }
}
