<?php
// app/Http/Controllers/Controller.php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    /**
     * These traits provide the middleware() and validate() methods
     * to all extending controllers. This is the compatibility layer
     * needed for the laravel/ui package in modern Laravel.
     */
    use AuthorizesRequests, ValidatesRequests;
}