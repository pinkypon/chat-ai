<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordFacade;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Landing page (default homepage)
Route::get('/', function () {
    return view('landing');
});

// Chat routes for guests (can view /chat, send messages without account)
Route::middleware(['web', 'nocache'])->group(function () {
    Route::get('/chat', [ConversationController::class, 'show'])->name('chat');
});
Route::post('/chat', [ConversationController::class, 'send'])->name('chat.send');

// Chat routes for logged-in & verified users (can view specific conversations, delete, start new chat)
Route::middleware(['auth', 'nocache', 'verified'])->group(function () {
    Route::get('/chat/{conversation}', [ConversationController::class, 'show'])->name('chat.show');
    Route::delete('/chat/{conversation}', [ConversationController::class, 'destroy'])->name('chat.delete');
    Route::post('/chat/new', [ConversationController::class, 'newChat'])->name('chat.new');
});

// Google OAuth authentication
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

// Email verification notice page
Route::get('/email/verify', function () {
    return view('mail.verify-email');
})->middleware('auth')->name('verification.notice');

// Handle verification link click
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('chat');
    }

    $request->fulfill(); // mark email as verified
    return redirect()->route('chat');
})->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');

// Resend verification email
Route::post('/email/resend', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('chat');
    }

    $request->user()->sendEmailVerificationNotification();
    return back()->with('resent', true);
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

// Forgot password request (form + email link)
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    // Send reset link email
    $status = Password::sendResetLink($request->only('email'));

    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

// Show reset password form (from email link)
Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

// Handle reset password submission
Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => [
            'required',
            'confirmed',
            PasswordFacade::min(8)->mixedCase()->letters()->numbers()->symbols(),
        ],
    ]);

    // Reset the password
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user) use ($request) {
            $user->forceFill([
                'password' => $request->password, // relies on User model mutator for hashing
            ])->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');

// Guest-only routes (login & register)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout for authenticated users
Route::delete('/logout', [AuthController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

// // Extra test routes (development only)
// Route::get('/test', function () {
//     return view('test');
// });

// Route::get('/react', function () {
//     return view('react-playground');
// });
