<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('landing');
});

// GET /chat — for guests or default conversation
Route::middleware(['web', 'nocache'])->group(function () {
    Route::get('/chat', [ConversationController::class, 'show'])->name('chat');
});
Route::post('/chat', [ConversationController::class, 'send'])->name('chat.send');

// GET /chat/{conversation} — for viewing a specific conversation (logged-in users)
Route::middleware(['auth', 'nocache', 'verified'])->group(function () {
    Route::get('/chat/{conversation}', [ConversationController::class, 'show'])->name('chat.show');
    Route::delete('/chat/{conversation}', [ConversationController::class, 'destroy'])->name('chat.delete');
    Route::post('/chat/new', [ConversationController::class, 'newChat'])->name('chat.new');
});

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');


// Email verification (manual, safer)
Route::get('/email/verify', function () {
    return view('mail.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('chat');
    }

    $request->fulfill();

    return redirect()->route('chat');
})->middleware(['auth', 'signed'])->name('verification.verify');



Route::post('/email/resend', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->route('chat');
    }

    $request->user()->sendEmailVerificationNotification();

    return back()->with('resent', true);
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');


// Forget Password
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Illuminate\Http\Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user) use ($request) {
            $user->forceFill([
                'password' => bcrypt($request->password)
            ])->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');



Route::middleware(['guest'])->group(function () {
    // login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::delete('/logout', [AuthController::class, 'destroy'])
->name('logout')
->middleware('auth');

Route::get('/test', function () {
    return view('test');
});