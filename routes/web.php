<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

use App\Http\Controllers\Auth\KakaoController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\SocialController;
/*

|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::group(['prefix' => 'oauth'], function (){
    Route::get('/kakao/login', [KakaoController::class, 'kakaoLogin'])->name('kakao-login');
    Route::get('/kakao-callback', [KakaoController::class, 'kakaoLoginCallback']);

    Route::get('/redirect', [OAuthController::class, 'redirect'])->name('oauth-login');
    Route::post('/callback', [OAuthController::class, 'callback']);

    Route::get('/login/{provider}', [SocialController::class, 'redirect'])->name('social-login');
    Route::get('/{provider}-callback', [SocialController::class, 'callback']);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/board', function () {
        return Inertia::render('Board/Index');
    })->name('board');

    Route::get('/registration-followup', function () {
        return Inertia::render('Auth/Followup/Index');
    })->name('followup');
});

