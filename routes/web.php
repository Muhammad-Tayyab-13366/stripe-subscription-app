<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubscriptionController;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Route;

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

Route::get('/login', function () {
    return redirect('/');
});





Route::middleware('guest')->group(function(){
    Route::get('/register', [AuthController::class, 'loadRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'userRegister'])->name('userRegister');
    
    Route::get('/', [AuthController::class, 'loadLogin'])->name('login');
    Route::post('/userLogin', [AuthController::class, 'userLogin'])->name('userLogin');
});

// Route::middleware('auth')->group(function(){
Route::middleware('userAuth')->group(function(){
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
});

Route::middleware('isAuthenticate')->group(function(){
    Route::get('/subscription', [SubscriptionController::class, 'loadSubscription'])->name('subscription');
    Route::post('/get-plan-detail', [SubscriptionController::class, 'getPlanDetail'])->name('getPlanDetail');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
