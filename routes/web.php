<?php

// routes/web.php

use Illuminate\Support\Facades\Route;
use Numista\Collection\UI\Public\Controllers\AddressController;
use Numista\Collection\UI\Public\Controllers\Auth\AuthenticatedSessionController;
use Numista\Collection\UI\Public\Controllers\Auth\NewPasswordController;
use Numista\Collection\UI\Public\Controllers\Auth\PasswordResetLinkController;
use Numista\Collection\UI\Public\Controllers\Auth\RegisteredUserController;
use Numista\Collection\UI\Public\Controllers\Auth\TenantRegistrationController;
use Numista\Collection\UI\Public\Controllers\CartController;
use Numista\Collection\UI\Public\Controllers\CheckoutController;
use Numista\Collection\UI\Public\Controllers\ContactSellerController;
use Numista\Collection\UI\Public\Controllers\LandingPageController;
use Numista\Collection\UI\Public\Controllers\MyAccountController;
use Numista\Collection\UI\Public\Controllers\OrderController;
use Numista\Collection\UI\Public\Controllers\ProfileController;
use Numista\Collection\UI\Public\Controllers\PublicItemController;
// ADD THIS LINE
use Numista\Collection\UI\Public\Controllers\StripePortalController;
use Numista\Collection\UI\Public\Controllers\StripeWebhookController;
use Numista\Collection\UI\Public\Controllers\SubscriptionController;
use Numista\Collection\UI\Public\Controllers\TenantFileController;
use Numista\Collection\UI\Public\Controllers\TenantProfileController;

/*
|--------------------------------------------------------------------------
| Publicly Accessible & Main Navigation Routes
|--------------------------------------------------------------------------
*/
Route::get('/', LandingPageController::class)->name('landing');
Route::get('/marketplace', [PublicItemController::class, 'index'])->name('public.items.index');
Route::get('/items/{item:slug}', [PublicItemController::class, 'show'])->name('public.items.show');
Route::post('/items/{item:slug}/contact', ContactSellerController::class)->name('public.items.contact');
Route::get('/images/{image}', [TenantFileController::class, 'showImage'])->name('images.show');
Route::get('/tenant-files/{path}', [TenantFileController::class, 'showFile'])->where('path', '.*')->name('tenant.files');

/*
|--------------------------------------------------------------------------
| Shopping Cart Routes
|--------------------------------------------------------------------------
*/
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{item}', [CartController::class, 'add'])->name('add');
    Route::post('/add/{item}/async', [CartController::class, 'addAsync'])->name('add.async');
    Route::patch('/update/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{item}', [CartController::class, 'remove'])->name('remove');
});

/*
|--------------------------------------------------------------------------
| Guest-Only Routes (Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('register-seller', [TenantRegistrationController::class, 'create'])->name('register.seller');
    Route::post('register-seller', [TenantRegistrationController::class, 'store'])->name('register.seller.store');
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/subscribe/{tenant:slug}', [SubscriptionController::class, 'create'])->name('subscription.create');
    Route::post('/subscribe/{tenant:slug}', [SubscriptionController::class, 'store'])->name('subscription.store');

    Route::prefix('my-account')->name('my-account.')->group(function () {
        Route::get('/', [MyAccountController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [MyAccountController::class, 'orders'])->name('orders');
        Route::get('addresses/{address}/delete', [AddressController::class, 'confirmDestroy'])->name('addresses.confirmDestroy');
        Route::resource('addresses', AddressController::class)->except(['show']);
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');

        // ADD THIS ROUTE FOR STRIPE CUSTOMER PORTAL
        Route::get('/subscription/manage', [StripePortalController::class, 'redirectToPortal'])->name('subscription.manage');
    });

    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
});

/*
|--------------------------------------------------------------------------
| Webhook and Catch-All Routes
|--------------------------------------------------------------------------
*/
// Stripe Webhook Route
Route::post(
    'stripe/webhook',
    [StripeWebhookController::class, 'handleWebhook']
)->name('stripe.webhook');

// Tenant public profile route must be last to avoid capturing other top-level routes.
Route::get('/{tenant:slug}', TenantProfileController::class)->name('public.tenants.show');
