<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutController extends Controller
{
    public function logout(Request $request) {
        Auth::logout();
        Session::flush();
        return redirect('login')->withHeaders(['Cache-Control' => 'no-store']);
    }
}
