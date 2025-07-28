<?php

use App\Http\Controllers\TenantFileController;
use Illuminate\Support\Facades\Route;
use Numista\Collection\UI\Public\Controllers\AddressController;
use Numista\Collection\UI\Public\Controllers\Auth\AuthenticatedSessionController;
use Numista\Collection\UI\Public\Controllers\Auth\RegisteredUserController;
use Numista\Collection\UI\Public\Controllers\CartController;
use Numista\Collection\UI\Public\Controllers\CheckoutController;
use Numista\Collection\UI\Public\Controllers\ContactSellerController;
use Numista\Collection\UI\Public\Controllers\MyAccountController;
use Numista\Collection\UI\Public\Controllers\OrderController;
use Numista\Collection\UI\Public\Controllers\ProfileController; // Import new controller
use Numista\Collection\UI\Public\Controllers\PublicImageController;
use Numista\Collection\UI\Public\Controllers\PublicItemController;

Route::get('/', [PublicItemController::class, 'index'])->name('public.items.index');
Route::get('/items/{item:slug}', [PublicItemController::class, 'show'])->name('public.items.show');
Route::post('/items/{item:slug}/contact', ContactSellerController::class)->name('public.items.contact');

Route::get('/item-images/{image}', [PublicImageController::class, 'show'])->name('public.images.show');
Route::get('/tenant-files/{path}', [TenantFileController::class, 'show'])->where('path', '.*')->name('tenant.files');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::prefix('my-account')->name('my-account.')->group(function () {
        // THE FIX: Redirect base to a new dashboard route
        Route::get('/', [MyAccountController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [MyAccountController::class, 'orders'])->name('orders');
        Route::resource('addresses', AddressController::class)->except(['show']);

        // THE FIX: Add routes for profile management
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
});

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{item}', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{item}', [CartController::class, 'remove'])->name('remove');
});
