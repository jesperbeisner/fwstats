<?php

declare(strict_types=1);

use Fwstats\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('/', [Controllers\IndexController::class, 'index'])->name('index');
