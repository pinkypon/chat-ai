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
    // Show login page
    public function showLogin()
    {
        return view('auth.login');
    }

    // Show register page
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle login request
    public function login(Request $request)
    {
        // Validate login input
        $attribute = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        // Attempt to log in with given credentials
        if (! Auth::attempt($attribute)) {
            // If login fails, throw validation error
            throw ValidationException::withMessages([
                'email' => 'Sorry, those credentials do not match.'
            ]);
        }

        // Regenerate session to prevent fixation
        request()->session()->regenerate();

        // Redirect to chat page after successful login
        return redirect('/chat');
    }

    // Handle registration request
    public function register(Request $request)
    {
        // Validate registration input
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

        // Create user with validated attributes
        $user = User::create($userAttributes);

        // Automatically log in the new user
        Auth::login($user);

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        // Redirect user to email verification notice page
        return redirect()->route('verification.notice');
    }

    // Handle logout request
    public function destroy(Request $request)
    {
        // Log out the authenticated user
        Auth::logout();

        // Invalidate current session
        $request->session()->invalidate();

        // Regenerate CSRF token for security
        $request->session()->regenerateToken();

        // Redirect to homepage
        return redirect('/');
    }
}
