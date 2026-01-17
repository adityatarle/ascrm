<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'mobile' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
        'password' => ['required'],
    ]);

    // Find user by mobile number
    $user = \App\Models\User::where('mobile', $credentials['mobile'])->first();

    if ($user && \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    return back()->withErrors([
        'mobile' => 'The provided credentials do not match our records.',
    ])->onlyInput('mobile');
})->middleware('guest');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');
