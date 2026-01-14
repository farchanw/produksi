<?php

namespace App\Http\Controllers\KpiProduction;

use App\Helpers\Modules\KpiProductionHelper;
use App\Helpers\Modules\DatetimeHelper;
use App\Models\AspekKpiHeader;
use App\Models\AspekKpiItem;
use App\Models\KpiEmployee;
use App\Models\KpiEvaluation;
use Illuminate\Support\Facades\DB;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Exception;
use Idev\EasyAdmin\app\Helpers\Constant;
use Illuminate\Support\Facades\Validator;
use Idev\EasyAdmin\app\Helpers\Validation;

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
                    ['name' => 'Bulan', 'column' => 'bulan', 'order' => true],
                    ['name' => 'Tahun', 'column' => 'tahun', 'order' => true],
                    ['name' => 'Aspek', 'column' => 'aspek_kpi_header_id', 'order' => true],
                    //['name' => 'Aspek values', 'column' => 'aspek_values', 'order' => true],
                    ['name' => 'Skor akhir', 'column' => 'skor_akhir', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['kategori'],
            'headers' => [
                    ['name' => 'Kategori', 'column' => 'kategori'],
                    ['name' => 'Kode', 'column' => 'kode'],
                    ['name' => 'Bulan', 'column' => 'bulan'],
                    ['name' => 'Tahun', 'column' => 'tahun'],
                    ['name' => 'Aspek kpi header id', 'column' => 'aspek_kpi_header_id'],
                    ['name' => 'Aspek values', 'column' => 'aspek_values'],
                    ['name' => 'Skor akhir', 'column' => 'skor_akhir'], 
            ]
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


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $optionsAspekKpiHeader = AspekKpiHeader::select('id as value', 'nama as text')->get()->toArray();
        $optionsAspekKpiHeader = array_merge([['value' => '', 'text' => 'Select...']], $optionsAspekKpiHeader);

        $emptyAspekValues = [];

        $fields = [
                    [
                        'type' => 'select',
                        'label' => 'Kategori',
                        'name' =>  'kategori',
                        'class' => 'col-4 my-2',
                        'required' => $this->flagRules('kategori', $id),
                        'value' => (isset($edit)) ? $edit->kategori : '',
                        'options' => KpiProductionHelper::optionsKategori(),

                    ],
                    [
                        'type' => 'select2',
                        'label' => 'Kode',
                        'name' =>  'kode',
                        'class' => 'col-4 my-2',
                        'required' => $this->flagRules('kode', $id),
                        'value' => (isset($edit)) ? $edit->kode : '',
                        'options' => KpiEmployee::select('id as value', DB::raw("CONCAT(nama, ' / ', nik) as text"))->orderBy('nama', 'ASC')->get()->toArray(),
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Bulan',
                        'name' =>  'bulan',
                        'class' => 'col-2 my-2',
                        'required' => $this->flagRules('bulan', $id),
                        'value' => (isset($edit)) ? $edit->bulan : '',
                        'options' => DatetimeHelper::optionsForMonths(),
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Tahun',
                        'name' =>  'tahun',
                        'class' => 'col-2 my-2',
                        'required' => $this->flagRules('tahun', $id),
                        'value' => (isset($edit)) ? $edit->tahun : '',
                        'options' => DatetimeHelper::optionsForYears(),
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Aspek',
                        'name' =>  'aspek_kpi_header_id',
                        'class' => 'col-12 my-2',
                        'required' => $this->flagRules('aspek_kpi_header_id', $id),
                        'value' => (isset($edit)) ? $edit->aspek_kpi_header_id : '',
                        'options' => $optionsAspekKpiHeader,
                    ],
                    [
                        'type' => 'dynamic_form_kpi_aspek_values',
                        'label' => 'Penilaian',
                        'name' =>  'aspek_values',
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
                    'bulan' => 'required|string',
                    'tahun' => 'required|string',
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

            $insert = new $this->modelClass();
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
            $weights = collect($values)->map(fn ($v) => (float) $v['bobot']);

            if ($weights->contains(fn ($w) => $w < 0)) {
                throw new Exception('Bobot tidak boleh negatif');
            }

            $sum = $weights->sum();

            if ($sum <= 0) {
                throw new Exception('Total bobot harus lebih dari 0');
            }

            $normalizedWeights = $weights->map(fn ($w) => $w / $sum);

            // validate normalized sum
            $epsilon = 1e-6;
            if (abs($normalizedWeights->sum() - 1.0) > $epsilon) {
                throw new Exception('Total bobot harus 1');
            }

            
            // === aspek_values ===





            $calculatedScores = collect($values)->map(function($item) {
                $realisasi = isset($item['realisasi']) ? floatval($item['realisasi']) : 0;
                $target    = isset($item['target']) ? floatval($item['target']) : 1;
                $tipe      = $item['tipe'] ?? 'Max';
                $bobot     = isset($item['bobot']) ? floatval($item['bobot']) : 1;

                // Calculate skor based on tipe
                $skor = 0;
                if (strtolower($tipe) === 'max') {
                    $skor = ($realisasi / $target) * 100;
                } elseif (strtolower($tipe) === 'min') {
                    $skor = ($target / max($realisasi, 0.0001)) * 100; // avoid divide by zero
                }
                $skor = round($skor * 100) / 100; // optional rounding

                // Calculate skor_akhir (weighted by bobot)
                $skor_akhir = round($skor * $bobot, 2);

                return [
                    'realisasi'        => $realisasi,
                    'skor'             => $skor,
                    'skor_akhir'       => $skor_akhir,
                ];
            });

            $insert->aspek_values = $calculatedScores->toJson();

            // === skor_akhir ===
            $skor_akhir = $calculatedScores->sum(fn ($item) => $item['skor_akhir']);
            $insert->skor_akhir = $skor_akhir;

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
}
