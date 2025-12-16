<?php

use App\Http\Controllers\InventoryConsumable\InventoryConsumableController;
use App\Http\Controllers\InventoryConsumable\InventoryConsumableMovementController;
use App\Http\Controllers\InventoryConsumable\DashboardInventoryConsumableController;
use App\Http\Controllers\InventoryConsumable\InventoryConsumableCategoryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'inventory-consumable'], function () {
    Route::resource('dashboard-inventory-consumable', DashboardInventoryConsumableController::class);
    Route::get('dashboard-inventory-consumable-chart-data-out-default', [DashboardInventoryConsumableController::class,'chartDataOut']);

    Route::get('inventory-consumable-chart-data-out-default', [InventoryConsumableController::class, 'chartDataOut']);
    Route::get('inventory-consumable-fetch-items-by-category-default', [InventoryConsumableController::class, 'fetchItemsByCategory']);
    Route::get('inventory-consumable-fetch-items-stock-data-default', [InventoryConsumableController::class, 'fetchItemsStockData']);

    Route::get('inventory-consumable-category-fetch-category-subcategories-default', [InventoryConsumableCategoryController::class, 'fetchCategorySubcategories']);
    Route::get('inventory-consumable-category-fetch-categories', [InventoryConsumableCategoryController::class, 'fetchCategories']);
});

Route::group(['middleware' => ['web', 'auth', 'middlewareByAccess'], 'prefix' => 'inventory-consumable'], function () {

    Route::get('dashboard-inventory-consumable-api', [DashboardInventoryConsumableController::class, 'indexApi'])->name('dashboard-inventory.listapi');
    Route::get('dashboard-inventory-consumable-export-pdf-default', [DashboardInventoryConsumableController::class, 'exportPdf'])->name('dashboard-inventory.export-pdf-default');
    Route::get('dashboard-inventory-consumable-export-excel-default', [DashboardInventoryConsumableController::class, 'exportExcel'])->name('dashboard-inventory.export-excel-default');
    Route::post('dashboard-inventory-consumable-import-excel-default', [DashboardInventoryConsumableController::class, 'importExcel'])->name('dashboard-inventory.import-excel-default');


    Route::resource('inventory-consumable', InventoryConsumableController::class);
    Route::get('inventory-consumable-api', [InventoryConsumableController::class, 'indexApi'])->name('inventory-consumable.listapi');
    Route::get('inventory-consumable-export-pdf-default', [InventoryConsumableController::class, 'exportPdf'])->name('inventory-consumable.export-pdf-default');
    Route::get('inventory-consumable-export-excel-default', [InventoryConsumableController::class, 'exportExcel'])->name('inventory-consumable.export-excel-default');
    Route::post('inventory-consumable-import-excel-default', [InventoryConsumableController::class, 'importExcel'])->name('inventory-consumable.import-excel-default');


    Route::resource('inventory-consumable-movement', InventoryConsumableMovementController::class);
    Route::get('inventory-consumable-movement-api', [InventoryConsumableMovementController::class, 'indexApi'])->name('inventory-consumable-movement.listapi');
    Route::get('inventory-consumable-movement-export-pdf-default', [InventoryConsumableMovementController::class, 'exportPdf'])->name('inventory-consumable-movement.export-pdf-default');
    Route::get('inventory-consumable-movement-export-excel-default', [InventoryConsumableMovementController::class, 'exportExcel'])->name('inventory-consumable-movement.export-excel-default');
    Route::post('inventory-consumable-movement-import-excel-default', [InventoryConsumableMovementController::class, 'importExcel'])->name('inventory-consumable-movement.import-excel-default');



    Route::resource('inventory-consumable-category', InventoryConsumableCategoryController::class);
    Route::get('inventory-consumable-category-api', [InventoryConsumableCategoryController::class, 'indexApi'])->name('inventory-consumable-category.listapi');
    Route::get('inventory-consumable-category-export-pdf-default', [InventoryConsumableCategoryController::class, 'exportPdf'])->name('inventory-consumable-category.export-pdf-default');
    Route::get('inventory-consumable-category-export-excel-default', [InventoryConsumableCategoryController::class, 'exportExcel'])->name('inventory-consumable-category.export-excel-default');
    Route::post('inventory-consumable-category-import-excel-default', [InventoryConsumableCategoryController::class, 'importExcel'])->name('inventory-consumable-category.import-excel-default');
});
