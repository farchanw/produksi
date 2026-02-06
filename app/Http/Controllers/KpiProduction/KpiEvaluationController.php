<?php

namespace App\Http\Controllers\KpiProduction;

use App\Helpers\Modules\KpiProductionHelper;
use App\Models\AspekKpiHeader;
use App\Models\KpiEmployee;
use App\Models\KpiEvaluation;
use Exception;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KpiEvaluationController extends DefaultController
{
    protected $modelClass = KpiEvaluation::class;

    protected $title;

    protected $generalUri;

    protected $tableHeaders;

    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Pencatatan';
        $this->title = 'Penilaian';
        $this->generalUri = 'kpi-evaluation';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Kategori', 'column' => 'kategori', 'order' => true],
            ['name' => 'Kode', 'column' => 'kode', 'order' => true],
            ['name' => 'Periode', 'column' => 'periode', 'order' => true],
            ['name' => 'Aspek', 'column' => 'aspek_kpi_header_id', 'order' => true],
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
        $optionsEmployee = []; /*KpiEmployee::select('id as value', DB::raw("CONCAT(nama, ' / ', nik) as text"))
            // filter not exist in kpi evaluation
            //->whereNotIn('id', KpiEvaluation::select('kode')->where('kategori', 'personal')->get()->pluck('kode')->toArray())
            ->orderBy('nama', 'ASC')
            ->get()
            ->toArray();
            */

        $emptyAspekValues = [];

        $fields = [
            [
                'type' => 'select',
                'label' => 'Kategori',
                'name' => 'kategori',
                'class' => 'col-4 my-2',
                'required' => $this->flagRules('kategori', $id),
                'value' => (isset($edit)) ? $edit->kategori : '',
                'options' => KpiProductionHelper::optionsKategori(),

            ],
            [
                'type' => 'select2',
                'label' => 'Kode',
                'name' => 'kode',
                'class' => 'col-4 my-2',
                'required' => $this->flagRules('kode', $id),
                'value' => (isset($edit)) ? $edit->kode : '',
                'options' => $optionsEmployee,
            ],
            [
                'type' => 'month',
                'label' => 'Periode',
                'name' => 'periode',
                'class' => 'col-2 my-2',
                'required' => $this->flagRules('periode', $id),
                'value' => (isset($edit)) ? $edit->periode : '',
            ],
            [
                'type' => 'select',
                'label' => 'Aspek',
                'name' => 'aspek_kpi_header_id',
                'class' => 'col-12 my-2',
                'required' => $this->flagRules('aspek_kpi_header_id', $id),
                'value' => (isset($edit)) ? $edit->aspek_kpi_header_id : '',
                'options' => $optionsAspekKpiHeader,
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

        return $fields;
    }

    protected function rules($id = null)
    {
        $rules = [
            'kategori' => 'required|string',
            'kode' => 'required|string',
            'periode' => 'required|string',
            'aspek_kpi_header_id' => 'required|string',
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

        $permissions = $this->arrPermissions;
        if ($this->dynamicPermission) {
            $permissions = (new Constant)->permissionByMenu($this->generalUri);
        }
        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'easyadmin::backend.idev.list_drawer';
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
}
