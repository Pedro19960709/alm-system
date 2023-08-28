<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function getIndex()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {   
        $credentials = $request->getCredentials();
        // dd($credentials);
        if(Auth::attempt($credentials))
        {
            $user = Auth::getProvider()->retrieveByCredentials($credentials);

            Auth::login($user);
            
            return [
                'type' => 'success',
                'message' => $user->name
            ];
        }

        return [
            'type' => 'error',
            'message' => 'Datos erroneos!'
        ];
    }

    public function authenticated(Request $request, $user)
    {
        return redirect('/dashboard');
    }
}