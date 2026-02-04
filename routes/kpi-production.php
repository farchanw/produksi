<?php
use App\Http\Controllers\KpiProduction\DashboardKpiProductionController;
use App\Http\Controllers\KpiProduction\MasterSectionController;
use App\Http\Controllers\KpiProduction\MasterSubsectionController;
use App\Http\Controllers\KpiProduction\MasterKpiController;
use App\Http\Controllers\KpiProduction\AspekKpiHeaderController;
use App\Http\Controllers\KpiProduction\AspekKpiItemController;
use App\Http\Controllers\KpiProduction\KpiEmployeeController;
use App\Http\Controllers\KpiProduction\KpiEvaluationController;
use App\Http\Controllers\KpiProduction\KpiEvaluationPersonalController;
use App\Http\Controllers\KpiProduction\KpiPersonalOeeController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TestPersonalOeeController;


Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'kpi-production'], function () {
    Route::post('/test-upload-oee', [TestPersonalOeeController::class, 'upload']);
    
    Route::get('/test-upload-oee', function () {
        $csrf = csrf_token();
        return <<<HTML
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{$csrf}">
            <input type="file" name="file" required>
            <button type="submit">Upload</button>
        </form>
        HTML;
    });



    Route::resource('dashboard-kpi-production', DashboardKpiProductionController::class);
    Route::get('dashboard-kpi-production-api', [DashboardKpiProductionController::class, 'indexApi'])->name('dashboard-kpi-production.listapi');



    Route::get('kpi-production-fetch-master-kpi-default', [MasterKpiController::class, 'fetchDefault'])->name('kpi-production.fetch-master-kpi-default');
    Route::get('kpi-production-fetch-aspek-kpi-item-default', [AspekKpiItemController::class, 'fetchDefault'])->name('kpi-production.fetch-aspek-kpi-item-default');
    Route::get('kpi-production-fetch-aspek-kpi-item-by-kode-default', [AspekKpiItemController::class, 'fetchByKodeDefault'])->name('kpi-production.fetch-aspek-kpi-item-by-kode-default');
    Route::get('kpi-production-fetch-kpi-employee-default', [KpiEmployeeController::class, 'fetchDefault'])->name('kpi-production.fetch-kpi-employee-default');


    
    Route::get('kpi-production-export-pdf-laporan-personal-default', [KpiEvaluationPersonalController::class, 'exportPdfLaporanPersonalDefault'])->name('kpi-evaluation-personal.export-pdf-laporan-personal-default');
    


    Route::post('kpi-production-kpi-evaluation-personal-bulk-action-default', [KpiEvaluationPersonalController::class, 'bulkAction'])->name('kpi-evaluation-personal.bulk-action-default');



    Route::post('import-excel-oee-personal-bulanan-default', [KpiPersonalOeeController::class, 'importExcelBulanan'])->name('kpi-personal-oee.import-excel-oee-personal-bulanan-default');
});

Route::group(['middleware' => ['web', 'auth', 'middlewareByAccess'], 'prefix' => 'kpi-production'], function () {
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



    Route::resource('aspek-kpi-item', AspekKpiItemController::class);
    Route::get('aspek-kpi-item-api', [AspekKpiItemController::class, 'indexApi'])->name('aspek-kpi-item.listapi');
    Route::get('aspek-kpi-item-export-pdf-default', [AspekKpiItemController::class, 'exportPdf'])->name('aspek-kpi-item.export-pdf-default');
    Route::get('aspek-kpi-item-export-excel-default', [AspekKpiItemController::class, 'exportExcel'])->name('aspek-kpi-item.export-excel-default');
    Route::post('aspek-kpi-item-import-excel-default', [AspekKpiItemController::class, 'importExcel'])->name('aspek-kpi-item.import-excel-default');



    Route::resource('kpi-employee', KpiEmployeeController::class);
    Route::get('kpi-employee-api', [KpiEmployeeController::class, 'indexApi'])->name('kpi-employee.listapi');
    Route::get('kpi-employee-export-pdf-default', [KpiEmployeeController::class, 'exportPdf'])->name('kpi-employee.export-pdf-default');
    Route::get('kpi-employee-export-excel-default', [KpiEmployeeController::class, 'exportExcel'])->name('kpi-employee.export-excel-default');
    Route::post('kpi-employee-import-excel-default', [KpiEmployeeController::class, 'importExcel'])->name('kpi-employee.import-excel-default');



    Route::resource('kpi-evaluation', KpiEvaluationController::class);
    Route::get('kpi-evaluation-api', [KpiEvaluationController::class, 'indexApi'])->name('kpi-evaluation.listapi');
    Route::get('kpi-evaluation-export-pdf-default', [KpiEvaluationController::class, 'exportPdf'])->name('kpi-evaluation.export-pdf-default');
    Route::get('kpi-evaluation-export-excel-default', [KpiEvaluationController::class, 'exportExcel'])->name('kpi-evaluation.export-excel-default');
    Route::post('kpi-evaluation-import-excel-default', [KpiEvaluationController::class, 'importExcel'])->name('kpi-evaluation.import-excel-default');



    Route::resource('kpi-evaluation-personal', KpiEvaluationPersonalController::class);
    Route::get('kpi-evaluation-personal-api', [KpiEvaluationPersonalController::class, 'indexApi'])->name('kpi-evaluation-personal.listapi');
    Route::get('kpi-evaluation-personal-export-pdf-default', [KpiEvaluationPersonalController::class, 'exportPdf'])->name('kpi-evaluation-personal.export-pdf-default');
    Route::get('kpi-evaluation-personal-export-excel-default', [KpiEvaluationPersonalController::class, 'exportExcel'])->name('kpi-evaluation-personal.export-excel-default');
    Route::post('kpi-evaluation-personal-import-excel-default', [KpiEvaluationPersonalController::class, 'importExcel'])->name('kpi-evaluation-personal.import-excel-default');


    Route::resource('kpi-personal-oee', KpiPersonalOeeController::class);
    Route::get('kpi-personal-oee-api', [KpiPersonalOeeController::class, 'indexApi'])->name('kpi-personal-oee.listapi');
    Route::get('kpi-personal-oee-export-pdf-default', [KpiPersonalOeeController::class, 'exportPdf'])->name('kpi-personal-oee.export-pdf-default');
    Route::get('kpi-personal-oee-export-excel-default', [KpiPersonalOeeController::class, 'exportExcel'])->name('kpi-personal-oee.export-excel-default');
    Route::post('kpi-personal-oee-import-excel-default', [KpiPersonalOeeController::class, 'importExcel'])->name('kpi-personal-oee.import-excel-default');
});
