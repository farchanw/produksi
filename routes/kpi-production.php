<?php
use App\Http\Controllers\KpiProduction\DashboardKpiProductionController;
use App\Http\Controllers\KpiProduction\MasterSectionController;
use App\Http\Controllers\KpiProduction\MasterSubsectionController;
use App\Http\Controllers\KpiProduction\MasterKpiController;
use App\Http\Controllers\KpiProduction\AspekKpiHeaderController;
use App\Http\Controllers\KpiProduction\AspekKpiItemController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'kpi-production'], function () {
    Route::resource('dashboard-kpi-production', DashboardKpiProductionController::class);
    Route::get('dashboard-kpi-production-api', [DashboardKpiProductionController::class, 'indexApi'])->name('dashboard-inventory.listapi');
    Route::get('dashboard-kpi-production-export-pdf-default', [DashboardKpiProductionController::class, 'exportPdf'])->name('dashboard-inventory.export-pdf-default');
    Route::get('dashboard-kpi-production-export-excel-default', [DashboardKpiProductionController::class, 'exportExcel'])->name('dashboard-inventory.export-excel-default');
    Route::post('dashboard-kpi-production-import-excel-default', [DashboardKpiProductionController::class, 'importExcel'])->name('dashboard-inventory.import-excel-default');
    Route::get('dashboard-kpi-production-chart-data-out-default', [DashboardKpiProductionController::class, 'chartDataOut']);



    Route::resource('master-section', MasterSectionController::class);
    Route::get('master-section-api', [MasterSectionController::class, 'indexApi'])->name('master-section.listapi');
    Route::get('master-section-export-pdf-default', [MasterSectionController::class, 'exportPdf'])->name('master-section.export-pdf-default');
    Route::get('master-section-export-excel-default', [MasterSectionController::class, 'exportExcel'])->name('master-section.export-excel-default');
    Route::post('master-section-import-excel-default', [MasterSectionController::class, 'importExcel'])->name('master-section.import-excel-default');



    Route::resource('master-subsection', MasterSubsectionController::class);
    Route::get('master-subsection-api', [MasterSubsectionController::class, 'indexApi'])->name('master-subsection.listapi');
    Route::get('master-subsection-export-pdf-default', [MasterSubsectionController::class, 'exportPdf'])->name('master-subsection.export-pdf-default');
    Route::get('master-subsection-export-excel-default', [MasterSubsectionController::class, 'exportExcel'])->name('master-subsection.export-excel-default');
    Route::post('master-subsection-import-excel-default', [MasterSubsectionController::class, 'importExcel'])->name('master-subsection.import-excel-default');



    Route::resource('master-kpi', MasterKpiController::class);
    Route::get('master-kpi-api', [MasterKpiController::class, 'indexApi'])->name('master-kpi.listapi');
    Route::get('master-kpi-export-pdf-default', [MasterKpiController::class, 'exportPdf'])->name('master-kpi.export-pdf-default');
    Route::get('master-kpi-export-excel-default', [MasterKpiController::class, 'exportExcel'])->name('master-kpi.export-excel-default');
    Route::post('master-kpi-import-excel-default', [MasterKpiController::class, 'importExcel'])->name('master-kpi.import-excel-default');



    Route::resource('aspek-kpi-header', AspekKpiHeaderController::class);
    Route::get('aspek-kpi-header-api', [AspekKpiHeaderController::class, 'indexApi'])->name('aspek-kpi-header.listapi');
    Route::get('aspek-kpi-header-export-pdf-default', [AspekKpiHeaderController::class, 'exportPdf'])->name('aspek-kpi-header.export-pdf-default');
    Route::get('aspek-kpi-header-export-excel-default', [AspekKpiHeaderController::class, 'exportExcel'])->name('aspek-kpi-header.export-excel-default');
    Route::post('aspek-kpi-header-import-excel-default', [AspekKpiHeaderController::class, 'importExcel'])->name('aspek-kpi-header.import-excel-default');
});
