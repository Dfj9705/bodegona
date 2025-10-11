<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

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

Route::get('/', [AppController::class, 'welcome'])->name('welcome');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');



Route::get('/login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class,'authenticate']);
Route::post('/logout', [LoginController::class,'logout'])->name('logout');
// Auth::routes();
Route::middleware(['auth','admin'])->group(function () {
    // RUTAS PARA MARCAS
    Route::get('/brand', [AppController::class, 'brand'])->name('brand.view');
    Route::get('/brands', [BrandController::class, 'index']);
    Route::post('/brands', [BrandController::class, 'store']);
    Route::post('/brands/{brand}', [BrandController::class, 'update']);
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy']);

    //RUTAS PARA USERS
    Route::get('/user', [AppController::class, 'user'])->name('user.view');
    Route::apiResource('/users', UserController::class);
    Route::post('/users/rol/{user}', [UserController::class, 'rol']);
    Route::post('/users/password/{user}', [UserController::class, 'password']);
    Route::post('/users/status/{user}', [UserController::class, 'status']);
    
    //RUTAS PARA PRODUCTOS
    Route::get('/product', [AppController::class, 'product'])->name('products.view');
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/products/{product}', [ProductController::class, 'update']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::get('/products/{product}/images', [ProductController::class, 'images']);
    Route::post('/products/image/{image}', [ProductController::class, 'deleteImage']);

    //RUTAS PARA PRODUCTOS
    Route::get('/charts', [AppController::class, 'chart'])->name('chart.view');
    Route::get('/chart/movements', [ChartController::class, 'movements']);
    Route::get('/chart/sales', [ChartController::class, 'sales']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/movement', [AppController::class, 'movement'])->name('movements.view');
    Route::get('/receipt', [ReportController::class, 'receipt'])->name('pdf.receipt');
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/movements', [MovementController::class, 'index']);
    Route::post('/movements', [MovementController::class, 'store']);
});