<?php

use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('idev-admin', [WelcomeController::class, 'index'])->name('login')->middleware('web');;

require __DIR__ . '/inventory.php';
require __DIR__ . '/setting.php';

