<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // public function callback()
    // {
    //     $googleUser = Socialite::driver('google')->stateless()->user();

    //     // Find user by google_id, or fallback to matching email
    //     $user = User::where('google_id', $googleUser->getId())->first();

    //     if (!$user) {
    //         $user = User::updateOrCreate(
    //             ['email' => $googleUser->getEmail()],
    //             [
    //                 'name'      => $googleUser->getName(),
    //                 'google_id' => $googleUser->getId(),
    //                 'password'  => bcrypt(str()->random(16)), // dummy password
    //             ]
    //         );
    //     }

    //     if (!$user->hasVerifiedEmail()) {
    //         $user->markEmailAsVerified();
    //     }

    //     Auth::login($user, remember: true);

    //     return redirect('/chat');
    // }



    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {


            // Redirect back to login with a flash message
            return redirect('/login')->with('error', 'Google login was cancelled or failed.');
        }

        // Find user by google_id, or fallback to matching email
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name'      => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'password'  => bcrypt(str()->random(16)), // dummy password
                ]
            );
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::login($user, remember: true);

        return redirect('/chat');
    }

}
