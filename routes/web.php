<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Middleware\TrustHosts;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*------------------------
    FRONTOFFICE ROUTES
------------------------*/
Route::get('/', function () {
    return view('welcome');
});

/*------------------------
    AUTH ROUTES
------------------------*/
Auth::routes();

/*------------------------
    PASSWORD RESET
------------------------*/
// Modulo di richiesta di collegamento per la reimpostazione della password
Route::get('/forgot-password', function () {
    return view('auth.passwords.email');
})->middleware([TrustHosts::class, 'guest'])->name('password.request');

// Gestione invio modulo
Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
 
    $status = Password::sendResetLink(
        $request->only('email')
    );
 
    return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);
})->middleware([TrustHosts::class, 'guest'])->name('password.email');

// Modulo per la reimpostazione della password
Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.passwords.reset', ['token' => $token]);
})->middleware([TrustHosts::class, 'guest'])->name('password.reset');

// Gestione invio del modulo reset password
Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);
 
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));
 
            $user->save();
 
            event(new PasswordReset($user));
        }
    );
 
    return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
})->middleware([TrustHosts::class, 'guest'])->name('password.update');

/*------------------------
    BACKOFFICE ROUTES
------------------------*/
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
