<?php

use App\Http\Controllers\TenantFileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tenant-files/{path}', [TenantFileController::class, 'show'])
    ->where('path', '.*')
    ->name('tenant.files');
