<?php

use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('idev-admin', [WelcomeController::class, 'index'])->name('login')->middleware('web');;

require __DIR__ . '/inventory-consumable.php';
require __DIR__ . '/utilisation-production.php';
require __DIR__ . '/kpi-production.php';
require __DIR__ . '/setting.php';

