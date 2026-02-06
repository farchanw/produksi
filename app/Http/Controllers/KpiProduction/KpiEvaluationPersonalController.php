<?php

namespace App\Http\Controllers\KpiProduction;

use App\Helpers\Common\DatetimeHelper;
use App\Helpers\Modules\KpiProductionHelper;
use App\Models\AspekKpiHeader;
use App\Models\KpiEmployee;
use App\Models\KpiEvaluation;
use App\Models\KpiAspekEmployee;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KpiEvaluationPersonalController extends DefaultController
{
    protected $modelClass = KpiEvaluation::class;

    protected $title;

    protected $generalUri;

    protected $tableHeaders;

    protected $dynamicPermission = true;

    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Pencatatan';
        $this->title = 'Penilaian';
        $this->generalUri = 'kpi-evaluation-personal';
        $this->arrPermissions = [
            'export-laporan-bulanan-pdf-default',
            'bulk-action-default',
        ];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Nama', 'column' => 'nama', 'order' => true],
            ['name' => 'Nik', 'column' => 'kode', 'order' => true],
            ['name' => 'Periode', 'column' => 'periode', 'order' => true, 'formatting' => 'toKpiPeriodDate'],
            ['name' => 'Aspek', 'column' => 'aspek', 'order' => true],
            ['name' => 'Skor akhir', 'column' => 'skor_akhir', 'order' => true],
            ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];

        $this->importExcelConfig = [
            'primaryKeys' => ['kategori'],
            'headers' => [
            ],
        ];

        $this->importScripts = [
            ['source' => asset('vendor/select2/js/select2.min.js')],
            ['source' => asset('js/modules/module-kpi-production.js')],
        ];
        $this->importStyles = [
            ['source' => asset('vendor/select2/css/select2.min.css')],
            ['source' => asset('vendor/select2/css/select2-bootstrap-5-theme.min.css')],
        ];

    }

    protected function fields($mode = 'create', $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $optionsAspekKpiHeader = AspekKpiHeader::select('id as value', 'nama as text')->get()->toArray();
        array_unshift($optionsAspekKpiHeader, ['value' => '', 'text' => 'Select...']);
        $optionsEmployee = [
            ['value' => '', 'text' => 'Select...'],
        ];

        if (isset($edit)) {
            $optionsEmployee = KpiEmployee::select('id as value', 'nik as text')->where('nik', $edit->kode)->get()->toArray();
        }

        $emptyAspekValues = [];

        $fields = [
            [
                'type' => 'hidden',
                'label' => 'Kategori',
                'name' => 'kategori',
                'class' => ' ',
                'required' => $this->flagRules('kategori', $id),
                'value' => 'personal',
            ],
            [
                'type' => 'month',
                'label' => 'Periode (Bulan & Tahun)',
                'name' => 'periode',
                'class' => 'col-4 my-2',
                'required' => $this->flagRules('periode', $id),
                'value' => (isset($edit)) ? DatetimeHelper::getKpiPeriodeValue($edit->periode) : '',
            ],
            [
                'type' => 'select2',
                'label' => 'Kode',
                'name' => 'kode',
                'class' => 'col-8 my-2',
                'required' => $this->flagRules('kode', $id),
                'value' => (isset($edit)) ? $edit->kode : '',
                'options' => $optionsEmployee,
            ],
            [
                'type' => 'dynamic_form_kpi_aspek_values',
                'label' => 'Penilaian',
                'name' => 'aspek_values',
                'class' => 'col-12 my-2',
                'required' => $this->flagRules('aspek_values', $id),
                'value' => (isset($edit)) ? $edit->aspek_values : '',
                'field_values' => (isset($edit)) ? json_decode($edit->aspek_values) : $emptyAspekValues,
            ],
        ];

        if ($mode === 'edit') {
            $fields = [
                [
                    'type' => 'hidden',
                    'label' => 'Kategori',
                    'name' => 'kategori',
                    'class' => ' ',
                    'required' => $this->flagRules('kategori', $id),
                    'value' => 'personal',
                ],
                [
                    'type' => 'onlyview_alt',
                    'label' => 'Periode',
                    'name' => 'periode',
                    'class' => 'col-4 my-2',
                    'required' => $this->flagRules('periode', $id),
                    'value' => (isset($edit)) ? $edit->periode : '',
                    'text' => (isset($edit)) ? DatetimeHelper::getKpiPeriodeFormatted($edit->periode) : '',
                ],
                [
                    'type' => 'onlyview_alt',
                    'label' => 'Nama',
                    'name' => 'kode',
                    'class' => 'col-8 my-2',
                    'required' => $this->flagRules('kode', $id),
                    'value' => (isset($edit)) ? $edit->kode : '',
                    'text' => (isset($edit)) ? $edit->kode.' - '.KpiEmployee::where('nik', $edit->kode)->first()->nama : '',
                ],
                [
                    'type' => 'dynamic_form_kpi_aspek_values',
                    'label' => 'Penilaian',
                    'name' => 'aspek_values',
                    'class' => 'col-12 my-2',
                    'required' => $this->flagRules('aspek_values', $id),
                    'value' => (isset($edit)) ? $edit->aspek_values : '',
                    'field_values' => (isset($edit)) ? json_decode($edit->aspek_values) : $emptyAspekValues,
                ],
            ];
        }

        return $fields;
    }

    protected function filters()
    {
        $optionsSection = KpiProductionHelper::optionsForSections()->toArray();
        array_unshift($optionsSection, ['value' => '', 'text' => 'Semua']);

        $fields = [
            [
                'type' => 'select',
                'label' => 'Bagian',
                'name' => 'section_id',
                'class' => 'col-md-3',
                'options' => $optionsSection,
            ],
            [
                'type' => 'month',
                'label' => 'Periode',
                'name' => 'periode',
                'class' => 'col-md-3',
            ],
        ];

        return $fields;
    }

    protected function rules($id = null)
    {
        $rules = [
            'kategori' => 'required|string',
            'kode' => 'required|string',
            'periode' => 'required|string',
            // 'aspek_kpi_header_id' => 'required|string',
        ];

        return $rules;
    }

    protected function store(Request $request)
    {
        $rules = $this->rules();

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messageErrors = (new Validation)->modify($validator, $rules);

            return response()->json([
                'status' => false,
                'alert' => 'danger',
                'message' => 'Required Form',
                'validation_errors' => $messageErrors,
            ], 200);
        }

        $beforeInsertResponse = $this->beforeMainInsert($request);
        if ($beforeInsertResponse !== null) {
            return $beforeInsertResponse; // Return early if there's a response
        }

        DB::beginTransaction();

        try {
            $appendStore = $this->appendStore($request);

            if (array_key_exists('error', $appendStore)) {
                return response()->json($appendStore['error'], 200);
            }

            $insert = new $this->modelClass;
            foreach ($this->fields('create') as $key => $th) {
                if ($request[$th['name']]) {
                    $insert->{$th['name']} = $request[$th['name']];
                }
            }
            if (array_key_exists('columns', $appendStore)) {
                foreach ($appendStore['columns'] as $key => $as) {
                    $insert->{$as['name']} = $as['value'];
                }
            }

            // periode
            $insert->periode = DatetimeHelper::getKpiPeriode($insert->periode);

            // aspek_kpi_header_id
            $insert->aspek_kpi_header_id = KpiAspekEmployee::join('kpi_employees', 'kpi_aspek_employees.kpi_employee_id', '=', 'kpi_employees.id')
            ->where('nik', $request->input('kode'))
                ->first()
                ->aspek_kpi_header_id;

            // === aspek_values ===
            $values = $request->input('aspek_values_input', []);

            // Validate bobot
            $weights = collect($values)->map(fn ($v) => (int) $v['bobot']);
            $sum = $weights->sum();
            if ($sum !== 100) {
                return response()->json([
                    'status' => false,
                    'alert' => 'danger',
                    'message' => 'Total bobot tidak valid: '.$sum.'%. Total bobot harus 100%.',
                ], 200);
            }

            // Calculate skor
            $calcScore = KpiProductionHelper::calculateScore($values);
            $insert->aspek_values = $calcScore->toJson();

            // === skor_akhir ===
            $skorAkhir = $calcScore->sum(fn ($item) => $item['skor_akhir']);
            $insert->skor_akhir = $skorAkhir;

            $insert->save();

            $this->afterMainInsert($insert, $request);

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Was Created Successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    protected function update(Request $request, $id)
    {
        $rules = $this->rules($id);

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messageErrors = (new Validation)->modify($validator, $rules);

            return response()->json([
                'status' => false,
                'alert' => 'danger',
                'message' => 'Required Form',
                'validation_errors' => $messageErrors,
            ], 200);
        }

        $beforeUpdateResponse = $this->beforeMainUpdate($id, $request);
        if ($beforeUpdateResponse !== null) {
            return $beforeUpdateResponse; // Return early if there's a response
        }

        DB::beginTransaction();

        try {
            $appendUpdate = $this->appendUpdate($request);

            $change = $this->modelClass::where('id', $id)->first();
            foreach ($this->fields('edit', $id) as $key => $th) {
                if ($request[$th['name']]) {
                    $change->{$th['name']} = $request[$th['name']];
                }
            }
            if (array_key_exists('columns', $appendUpdate)) {
                foreach ($appendUpdate['columns'] as $key => $as) {
                    $change->{$as['name']} = $as['value'];
                }
            }

            // periode
            $change->periode = DatetimeHelper::getKpiPeriode($change->periode);

            // === aspek_values ===
            $values = $request->input('aspek_values_input', []);

            // Validate bobot
            $weights = collect($values)->map(fn ($v) => (int) $v['bobot']);
            $sum = $weights->sum();
            if ($sum !== 100) {
                return response()->json([
                    'status' => false,
                    'alert' => 'danger',
                    'message' => 'Total bobot tidak valid: '.$sum.'%. Total bobot harus 100%.',
                ], 200);
            }

            // Calculate skor
            $calcScore = KpiProductionHelper::calculateScore($values);
            $change->aspek_values = $calcScore->toJson();

            // === skor_akhir ===
            $skorAkhir = $calcScore->sum(fn ($item) => $item['skor_akhir']);
            $change->skor_akhir = $skorAkhir;

            $change->save();

            $this->afterMainUpdate($change, $request);

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Was Updated Successfully',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function index()
    {
        $baseUrlExcel = route($this->generalUri.'.export-excel-default');
        $baseUrlPdf = route($this->generalUri.'.export-pdf-default');
        $baseUrlLaporanPdf = route($this->generalUri.'.export-pdf-laporan-personal-default');

        $baseUrlBulkAction = route($this->generalUri.'.bulk-action-default');

        $moreActions = [
            [
                'key' => 'import-excel-default',
                'name' => 'Import Excel',
                'html_button' => "<button id='import-excel' type='button' class='btn btn-sm btn-info radius-6' href='#' data-bs-toggle='modal' data-bs-target='#modalImportDefault' title='Import Excel' ><i class='ti ti-upload'></i></button>",
            ],
            [
                'key' => 'export-excel-default',
                'name' => 'Export Excel',
                'html_button' => "<a id='export-excel' data-base-url='".$baseUrlExcel."' class='btn btn-sm btn-success radius-6' target='_blank' href='".$baseUrlExcel."'  title='Export Excel'><i class='ti ti-cloud-download'></i></a>",
            ],
            [
                'key' => 'export-pdf-default',
                'name' => 'Export Pdf',
                'html_button' => "<a id='export-pdf' data-base-url='".$baseUrlPdf."' class='btn btn-sm btn-danger radius-6' target='_blank' href='".$baseUrlPdf."' title='Export PDF'><i class='ti ti-file'></i></a>",
            ],
        ];

        $customActions = [
            [
                'key' => 'export-laporan-bulanan-pdf-default',
                'name' => 'Export Laporan Bulanan PDF',
                'html_button' => '<button
                    type="button"
                    id="export-laporan-bulanan-pdf"
                    class="btn btn-sm btn-primary radius-6"
                    data-bs-toggle="modal"
                    data-bs-target="#modalExportLaporanPdf"
                    data-base-url="'.$baseUrlLaporanPdf.'"
                    title="Cetak Laporan Bulanan PDF"
                    >
                    <i class="ti ti-file-report"></i>
                    </button>
                ',
            ],
            [
                'key' => 'bulk-action-default',
                'name' => 'Bulk Action',
                'html_button' => '<button
                    type="button"
                    id="bulk-action"
                    class="btn btn-sm radius-6 bg-primary text-white"
                    data-bs-toggle="modal"
                    data-bs-target="#modalBulkAction"
                    data-base-url="'.$baseUrlBulkAction.'"
                    data-csrf="'.csrf_token().'"
                    title="Bulk Action"
                    >
                    <i class="ti ti-checkbox"></i>
                    </button>
                ',
            ],
        ];

        $moreActions = array_merge($moreActions, $customActions);

        $permissions = $this->arrPermissions;
        if ($this->dynamicPermission) {
            $permissions = (new Constant)->permissionByMenu($this->generalUri);
            $permissions = array_merge($permissions, $this->arrPermissions);
        }
        // dd($permissions);
        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'easyadmin::backend.idev.list_drawer_with_checkbox';
        if (isset($this->drawerLayout)) {
            $layout = $this->drawerLayout;
        }
        /* Override edit button */
        // unset first
        if (($key = array_search('easyadmin::backend.idev.buttons.edit', $this->actionButtonViews)) !== false) {
            unset($this->actionButtonViews[$key]);
            // set new edit button
            $this->actionButtonViews[] = 'backend.idev.buttons.edit';
        }

        /* Override delete button */
        // unset first
        if (($key = array_search('easyadmin::backend.idev.buttons.delete', $this->actionButtonViews)) !== false) {
            unset($this->actionButtonViews[$key]);
            // set new delete button
            $this->actionButtonViews[] = 'backend.idev.buttons.delete';
        }
        $data['permissions'] = $permissions;
        $data['more_actions'] = $moreActions;
        $data['headerLayout'] = $this->pageHeaderLayout;
        $data['table_headers'] = $this->tableHeaders;
        $data['title'] = $this->title;
        $data['uri_key'] = $this->generalUri;
        $data['uri_list_api'] = route($this->generalUri.'.listapi');
        $data['uri_create'] = route($this->generalUri.'.create');
        $data['url_store'] = route($this->generalUri.'.store');
        $data['fields'] = $this->fields();
        $data['edit_fields'] = $this->fields('edit');
        $data['actionButtonViews'] = $this->actionButtonViews;
        $data['templateImportExcel'] = '#';
        $data['import_scripts'] = $this->importScripts;
        $data['import_styles'] = $this->importStyles;
        $data['filters'] = $this->filters();

        return view($layout, $data);
    }

    protected function defaultDataQuery()
    {
        $filters = [];
        $orThose = null;
        $orderBy = 'id';
        $orderState = 'DESC';
        if (request('search')) {
            $orThose = request('search');
        }
        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }
        // filters
        if (request('section_id')) {
            $filters[] = ['aspek_kpi_headers.master_section_id', '=', request('section_id')];
        }
        if (request('periode')) {
            $filters[] = ['kpi_evaluations.periode', '=', DatetimeHelper::getKpiPeriode(request('periode'))];
        }

        $dataQueries = $this->modelClass::join('kpi_employees', 'kpi_evaluations.kode', '=', 'kpi_employees.nik')
            ->join('aspek_kpi_headers', 'kpi_evaluations.aspek_kpi_header_id', '=', 'aspek_kpi_headers.id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $efc = ['#', 'created_at', 'updated_at', 'id', 'aspek_kpi_header_id', 'nama', 'aspek'];

                foreach ($this->tableHeaders as $key => $th) {
                    if (array_key_exists('search', $th) && $th['search'] == false) {
                        $efc[] = $th['column'];
                    }
                    if (! in_array($th['column'], $efc)) {
                        if ($key == 0) {
                            $query->where($th['column'], 'LIKE', '%'.$orThose.'%');
                        } else {
                            $query->orWhere($th['column'], 'LIKE', '%'.$orThose.'%');
                        }
                    }
                }
            })
            ->orderBy($orderBy, $orderState)
            ->select(
                'kpi_evaluations.*',
                'kpi_employees.nama',
                'kpi_employees.nik',
                'aspek_kpi_headers.nama as aspek',
            );

        return $dataQueries;
    }

    public function exportPdfLaporanPersonalDefault(Request $request)
    {
        $nik = $request->input('nik');
        $periode = $request->input('periode');

        $stdDate = DatetimeHelper::getKpiPeriode($periode);
        // dd($stdDate);
        $carbonDate = Carbon::parse($stdDate);

        $month = $carbonDate->month;
        $year = $carbonDate->year;
        $data['bulanNama'] = Carbon::createFromFormat('m', $month)->locale('id')->translatedFormat('F');
        $data['tahun'] = $year;
        $records = KpiEvaluation::join('kpi_employees', 'kpi_employees.nik', '=', 'kpi_evaluations.kode')
            ->join('aspek_kpi_headers', 'aspek_kpi_headers.id', '=', 'kpi_evaluations.aspek_kpi_header_id')
            ->join('aspek_kpi_items', 'aspek_kpi_items.aspek_kpi_header_id', '=', 'aspek_kpi_headers.id')
            ->join('master_kpis', 'master_kpis.id', '=', 'aspek_kpi_items.master_kpi_id')
            ->where('kpi_employees.nik', $nik)
            ->where('kpi_evaluations.periode', $stdDate)
            ->where('kpi_evaluations.kategori', 'personal')
            ->select(
                'kpi_evaluations.id as evaluation_id',
                'kpi_evaluations.periode',
                'kpi_evaluations.aspek_values',
                'kpi_evaluations.skor_akhir',
                'kpi_employees.nama',
                'kpi_employees.nik',
                'aspek_kpi_headers.id as aspek_kpi_header_id',
                'aspek_kpi_items.id as aspek_kpi_item_id',
                'aspek_kpi_items.target',
                'aspek_kpi_items.bobot',
                'master_kpis.nama as nama_kpi',
                'master_kpis.area_kinerja_utama as area_kinerja_utama',
                'master_kpis.tipe',
                'master_kpis.satuan',
                'master_kpis.sumber_data_realisasi'
            )
            ->get();

        $data['records'] = KpiProductionHelper::mapLaporanPersonal($records);
        // dd($data['records']);

        $data['nama'] = $records[0]?->nama ?? '';
        $data['nik'] = $records[0]?->nik ?? '';

        $pdf = Pdf::loadView('pdf.kpi_production.laporan_kpi_personal', $data);
        $pdf->setPaper('A4');

        $fileName = 'laporan-kpi-personal-'.Carbon::now()->format('YmdHis').'.pdf';

        return $pdf->stream($fileName);
    }

    public function bulkAction(Request $request)
    {
        $bulkAction = $request->input('bulk_action');

        if ($bulkAction == 'export-laporan') {
            $evaluationIds = $request->input('kpi_evaluation_ids', []);

            if (empty($evaluationIds)) {
                return response()->json(['message' => 'No KPI evaluations selected'], 422);
            }

            $evaluationIds = json_decode($evaluationIds);

            $records = KpiEvaluation::join('kpi_employees', 'kpi_employees.nik', '=', 'kpi_evaluations.kode')
                ->join('aspek_kpi_headers', 'aspek_kpi_headers.id', '=', 'kpi_evaluations.aspek_kpi_header_id')
                ->join('aspek_kpi_items', 'aspek_kpi_items.aspek_kpi_header_id', '=', 'aspek_kpi_headers.id')
                ->join('master_kpis', 'master_kpis.id', '=', 'aspek_kpi_items.master_kpi_id')
                ->whereIn('kpi_evaluations.id', $evaluationIds)
                ->where('kpi_evaluations.kategori', 'personal')
                ->select(
                    'kpi_evaluations.id as evaluation_id',
                    'kpi_evaluations.periode',
                    'kpi_evaluations.aspek_values',
                    'kpi_evaluations.skor_akhir',
                    'kpi_employees.nama',
                    'kpi_employees.nik',
                    'aspek_kpi_headers.id as aspek_kpi_header_id',
                    'aspek_kpi_items.id as aspek_kpi_item_id',
                    'aspek_kpi_items.target',
                    'aspek_kpi_items.bobot',
                    'master_kpis.nama as nama_kpi',
                    'master_kpis.area_kinerja_utama as area_kinerja_utama',
                    'master_kpis.tipe',
                    'master_kpis.satuan',
                    'master_kpis.sumber_data_realisasi'
                )
                ->get();

            $groupedByEvaluation = $records->groupBy('evaluation_id');

            $data['evaluations'] = $groupedByEvaluation->map(function ($evaluationRecords) {
                $firstRecord = $evaluationRecords->first();

                return [
                    'evaluation_id' => $firstRecord->evaluation_id,
                    'periode' => $firstRecord->periode,
                    'skor_akhir' => $firstRecord->skor_akhir,
                    'nama' => $firstRecord->nama,
                    'nik' => $firstRecord->nik,
                    'records' => KpiProductionHelper::mapLaporanPersonal($evaluationRecords),
                ];
            })->values();

            $firstPeriode = $records->first()?->periode;
            $carbonDate = Carbon::parse($firstPeriode ?? now());

            $data['bulanNama'] = Carbon::createFromFormat('m', $carbonDate->month)
                ->locale('id')
                ->translatedFormat('F');
            $data['tahun'] = $carbonDate->year;

            $pdf = Pdf::loadView('pdf.kpi_production.bulk_laporan_kpi_personal', $data);
            $pdf->setPaper('A4');

            $fileName = 'bulk-laporan-kpi-personal-'.Carbon::now()->format('YmdHis').'.pdf';

            return $pdf->stream($fileName);
        }
    }
}
