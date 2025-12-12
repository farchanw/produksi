<?php
use App\Http\Controllers\UtilisationProduction\DashboardUtilisationProductionController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'utilisation-production'], function () {
    Route::resource('dashboard-utilisation-production', DashboardUtilisationProductionController::class);
    Route::get('dashboard-utilisation-production-api', [DashboardUtilisationProductionController::class, 'indexApi'])->name('dashboard-inventory.listapi');
    Route::get('dashboard-utilisation-production-export-pdf-default', [DashboardUtilisationProductionController::class, 'exportPdf'])->name('dashboard-inventory.export-pdf-default');
    Route::get('dashboard-utilisation-production-export-excel-default', [DashboardUtilisationProductionController::class, 'exportExcel'])->name('dashboard-inventory.export-excel-default');
    Route::post('dashboard-utilisation-production-import-excel-default', [DashboardUtilisationProductionController::class, 'importExcel'])->name('dashboard-inventory.import-excel-default');
    Route::get('dashboard-utilisation-production-chart-data-out-default', [DashboardUtilisationProductionController::class, 'chartDataOut']);


});
