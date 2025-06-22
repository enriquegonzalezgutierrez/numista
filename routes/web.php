<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantFileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tenant-files/{path}', [TenantFileController::class, 'show'])
    ->where('path', '.*')
    ->name('tenant.files');
