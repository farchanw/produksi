<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryConsumableController;
use App\Http\Controllers\InventoryConsumableMovementController;
use App\Http\Controllers\InventoryConsumableCategoryController;
use App\Http\Controllers\DashboardController;


Route::group(['middleware' => ['web', 'auth']], function () {

    Route::resource('dashboard', DashboardController::class);

    Route::resource('inventory-consumable', InventoryConsumableController::class);
    Route::get('inventory-consumable-api', [InventoryConsumableController::class, 'indexApi'])->name('inventory-consumable.listapi');
    Route::get('inventory-consumable-export-pdf-default', [InventoryConsumableController::class, 'exportPdf'])->name('inventory-consumable.export-pdf-default');
    Route::get('inventory-consumable-export-excel-default', [InventoryConsumableController::class, 'exportExcel'])->name('inventory-consumable.export-excel-default');
    Route::post('inventory-consumable-import-excel-default', [InventoryConsumableController::class, 'importExcel'])->name('inventory-consumable.import-excel-default');
    Route::get('inventory-consumable-chart-data-out-default', [InventoryConsumableController::class, 'chartDataOut']);



    Route::resource('inventory-consumable-movement', InventoryConsumableMovementController::class);
    Route::get('inventory-consumable-movement-api', [InventoryConsumableMovementController::class, 'indexApi'])->name('inventory-consumable-movement.listapi');
    Route::get('inventory-consumable-movement-export-pdf-default', [InventoryConsumableMovementController::class, 'exportPdf'])->name('inventory-consumable-movement.export-pdf-default');
    Route::get('inventory-consumable-movement-export-excel-default', [InventoryConsumableMovementController::class, 'exportExcel'])->name('inventory-consumable-movement.export-excel-default');
    Route::post('inventory-consumable-movement-import-excel-default', [InventoryConsumableMovementController::class, 'importExcel'])->name('inventory-consumable-movement.import-excel-default');



});