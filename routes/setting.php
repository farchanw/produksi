<?php

use App\Http\Controllers\Setting\DashboardSettingController;
use Idev\EasyAdmin\app\Http\Controllers\RoleController;
use Idev\EasyAdmin\app\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'setting'], function () {
    Route::get('dashboard-setting', [DashboardSettingController::class, 'index'])->name('dashboard-setting.index');

    Route::resource('role', RoleController::class);
    Route::get('role-api', [RoleController::class, 'indexApi'])->name('role.listapi');
    Route::get('role-export-pdf-default', [RoleController::class, 'exportPdf'])->name('role.export-pdf-default');
    Route::get('role-export-excel-default', [RoleController::class, 'exportExcel'])->name('role.export-excel-default');
    Route::post('role-import-excel-default', [RoleController::class, 'importExcel'])->name('role.import-excel-default');

    Route::resource('user', UserController::class);
    Route::get('user-api', [UserController::class, 'indexApi'])->name('user.listapi');
    Route::get('user-export-pdf-default', [UserController::class, 'exportPdf'])->name('user.export-pdf-default');
    Route::get('user-export-excel-default', [UserController::class, 'exportExcel'])->name('user.export-excel-default');
    Route::post('user-import-excel-default', [UserController::class, 'importExcel'])->name('user.import-excel-default');
  
    Route::get('my-account', [UserController::class, 'profile']);
    Route::post('update-profile', [UserController::class, 'updateProfile']);
});