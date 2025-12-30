<?php

use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/test1', function () {
    return view('backend.idev.extend.printable.laporan_bulanan_inventaris');
});

Route::get('/', [WelcomeController::class, 'index'])->name('login')->middleware('web');
Route::get('home-admin', [WelcomeController::class, 'index'])->name('login')->middleware('web');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index')->middleware('web');

require __DIR__ . '/inventory-consumable.php';
require __DIR__ . '/utilisation-production.php';
require __DIR__ . '/kpi-production.php';
require __DIR__ . '/setting.php';

