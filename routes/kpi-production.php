<?php
use App\Http\Controllers\KpiProduction\DashboardKpiProductionController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'kpi-production'], function () {
    Route::resource('dashboard-kpi-production', DashboardKpiProductionController::class);
    Route::get('dashboard-kpi-production-api', [DashboardKpiProductionController::class, 'indexApi'])->name('dashboard-inventory.listapi');
    Route::get('dashboard-kpi-production-export-pdf-default', [DashboardKpiProductionController::class, 'exportPdf'])->name('dashboard-inventory.export-pdf-default');
    Route::get('dashboard-kpi-production-export-excel-default', [DashboardKpiProductionController::class, 'exportExcel'])->name('dashboard-inventory.export-excel-default');
    Route::post('dashboard-kpi-production-import-excel-default', [DashboardKpiProductionController::class, 'importExcel'])->name('dashboard-inventory.import-excel-default');
    Route::get('dashboard-kpi-production-chart-data-out-default', [DashboardKpiProductionController::class, 'chartDataOut']);


});
