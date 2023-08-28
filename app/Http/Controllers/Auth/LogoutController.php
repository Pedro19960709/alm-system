<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Session;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        Session::flush();

        Auth::logout();

        return redirect()->to('/login');
    }
}

