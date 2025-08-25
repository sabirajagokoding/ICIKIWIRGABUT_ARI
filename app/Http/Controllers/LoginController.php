<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class LoginController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect('home');
        } else {
            return view('login');
        }
    }

    public function actionlogin(Request $request)
    {
        $data = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'active' => 1
        ];

        if (Auth::attempt($data)) {
            // Simpan ke session
            session(['role' => Auth::user()->role]);
            return redirect('home');
        } else {
            Session::flash('error', 'Invalid email or password');
            return redirect()->route('login');
        }

    }

    public function actionlogout()
    {
        Auth::logout();
        return redirect('/');
    }
}
