<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        /**
         * Para trabajar con usuarios de la instancia
         */
        \Instantiation::set_default_instance();
       $login = $request->validate([
            'email'=> 'required|string',
            'password'=>'required|string'
       ]);

       if (!Auth::attempt($login)){
           return response()->json(['message'=> 'invalid login credentials.'], 401);
       }

       $accessToken = Auth::user()->createToken('authToken')->accessToken;
        return response(['user'=> Auth::user(), 'access_token' => $accessToken]);
    }
}
