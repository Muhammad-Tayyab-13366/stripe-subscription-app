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

Route::get('/', function () {
    return redirect('/login');
});





Route::middleware('guest')->group(function(){
    Route::get('/register', [AuthController::class, 'loadRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'userRegister'])->name('userRegister');
    
    Route::get('/login', [AuthController::class, 'loadLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'userLogin'])->name('userLogin');
});

// Route::middleware('auth')->group(function(){
Route::middleware('userAuth')->group(function(){
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
});

Route::middleware('isAuthenticate')->group(function(){
    Route::get('/subscription', [SubscriptionController::class, 'loadSubscription'])->name('subscription');
    Route::post('/get-plan-detail', [SubscriptionController::class, 'getPlanDetail'])->name('getPlanDetail');
    Route::post('/create-subscription', [SubscriptionController::class, 'createSubscription'])->name('createSubscription');
    Route::post('/cancelSubscription', [SubscriptionController::class, 'cancelSubscription'])->name('cancelSubscription');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
