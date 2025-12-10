<?php

use App\Http\Controllers\Inventory\InventoryConsumableController;
use App\Http\Controllers\Inventory\InventoryConsumableMovementController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'inventory'], function () {
    Route::resource('dashboard-inventory', InventoryConsumableController::class);
    Route::get('dashboard-inventory-api', [InventoryConsumableController::class, 'indexApi'])->name('dashboard-inventory.listapi');
    Route::get('dashboard-inventory-export-pdf-default', [InventoryConsumableController::class, 'exportPdf'])->name('dashboard-inventory.export-pdf-default');
    Route::get('dashboard-inventory-export-excel-default', [InventoryConsumableController::class, 'exportExcel'])->name('dashboard-inventory.export-excel-default');
    Route::post('dashboard-inventory-import-excel-default', [InventoryConsumableController::class, 'importExcel'])->name('dashboard-inventory.import-excel-default');
    Route::get('dashboard-inventory-chart-data-out-default', [InventoryConsumableController::class, 'chartDataOut']);


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
