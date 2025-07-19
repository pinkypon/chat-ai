<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view( 'auth.register');
    }

    public function login(Request $request)
    {
        // validate
        $attribute = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        // attempt to login the user
        if(! Auth::attempt($attribute)){
            throw ValidationException::withMessages([
                'email' =>  'Sorry, those credentials do not match.'
            ]);
        }

        // regenerate the session token
        request()->session()->regenerate();

        // redirect
        return redirect('/chat');
    }

    public function register(Request $request)
    {
        $userAttributes = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
            ],
        ]);

        $user = User::create($userAttributes);

        Auth::login($user);

        // ✅ Send email verification
        $user->sendEmailVerificationNotification();

        // ✅ Redirect to verification page
        return redirect()->route('verification.notice');
    }


    public function destroy(Request $request)
    {
        Auth::logout(); 

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

