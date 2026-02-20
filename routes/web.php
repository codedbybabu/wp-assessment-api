
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Auth;


/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Here is where you can register web routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "web" middleware group. Make something great! | */


// Public Web Routes
Route::get('/', [HomeController::class , 'index'])->name('home');
Route::get('/shop', [HomeController::class , 'shop'])->name('shop.index');
Route::get('/product/{slug}', [HomeController::class , 'product'])->name('product.show');
Route::get('/login', [HomeController::class , 'login'])->name('login');

// Auth Web Routes
Route::post('/web-login', [AuthController::class , 'login'])->name('web.login');
Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

// Dashboard Routes (Grouped)
Route::middleware(['auth:web'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class , 'index'])->name('dashboard');
    Route::get('/customer', [DashboardController::class , 'customer'])->name('dashboard.customer');
    Route::get('/silver', [DashboardController::class , 'silver'])->name('dashboard.silver');
    Route::get('/gold', [DashboardController::class , 'gold'])->name('dashboard.gold');
});
