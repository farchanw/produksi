<?php

namespace App\Http\Controllers\KpiProduction;

use App\Models\AspekKpiItem;
use App\Models\KpiEvaluation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Idev\EasyAdmin\app\Helpers\Constant;

class AspekKpiItemController extends DefaultController
{
    protected $modelClass = AspekKpiItem::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Aspek Kpi Item';
        $this->generalUri = 'aspek-kpi-item';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Aspek kpi header id', 'column' => 'aspek_kpi_header_id', 'order' => true],
                    ['name' => 'Master kpi id', 'column' => 'master_kpi_id', 'order' => true],
                    ['name' => 'Bobot', 'column' => 'bobot', 'order' => true],
                    ['name' => 'Target', 'column' => 'target', 'order' => true],
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['aspek_kpi_header_id'],
            'headers' => [
                    ['name' => 'Aspek kpi header id', 'column' => 'aspek_kpi_header_id'],
                    ['name' => 'Master kpi id', 'column' => 'master_kpi_id'],
                    ['name' => 'Bobot', 'column' => 'bobot'],
                    ['name' => 'Target', 'column' => 'target'],
            ]
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $fields = [
                    [
                        'type' => 'text',
                        'label' => 'Aspek kpi header id',
                        'name' =>  'aspek_kpi_header_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('aspek_kpi_header_id', $id),
                        'value' => (isset($edit)) ? $edit->aspek_kpi_header_id : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Master kpi id',
                        'name' =>  'master_kpi_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('master_kpi_id', $id),
                        'value' => (isset($edit)) ? $edit->master_kpi_id : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Bobot',
                        'name' =>  'bobot',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('bobot', $id),
                        'value' => (isset($edit)) ? $edit->bobot : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Target',
                        'name' =>  'target',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('target', $id),
                        'value' => (isset($edit)) ? $edit->target : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'aspek_kpi_header_id' => 'required|string',
                    'master_kpi_id' => 'required|string',
                    'bobot' => 'required|string',
                    'target' => 'required|string',
        ];

        return $rules;
    }

    protected function defaultDataQuery()
    {
        $filters = [];
        $orThose = null;
        $orderBy = 'aspek_kpi_items.id';
        $orderState = 'DESC';
        if (request('search')) {
            $orThose = request('search');
        }
        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }

        $dataQueries = $this->modelClass::where($filters)
            ->where(function ($query) use ($orThose) {
                $efc = ['#', 'created_at', 'updated_at', 'id'];

                foreach ($this->tableHeaders as $key => $th) {
                    if (array_key_exists('search', $th) && $th['search'] == false) {
                        $efc[] = $th['column'];
                    }
                    if(!in_array($th['column'], $efc))
                    {
                        if($key == 0){
                            $query->where($th['column'], 'LIKE', '%' . $orThose . '%');
                        }else{
                            $query->orWhere($th['column'], 'LIKE', '%' . $orThose . '%');
                        }
                    }
                }
            })
            ->orderBy($orderBy, $orderState);

        return $dataQueries;
    }

    public function fetchDefault() 
    {
        $data = collect();
        $id = request('aspek_kpi_header_id');

        // optional params
        $kategori = request('kategori');
        $kode = request('kode');
        $periode = request('periode');

        if ($id) {
            $data = $this->defaultDataQuery()
                ->join('master_kpis', 'aspek_kpi_items.master_kpi_id', '=', 'master_kpis.id')
                ->where('aspek_kpi_header_id', $id)
                ->get();

            // get values from kpi_evaluations if exist
            if ($kategori && $kode && $periode) {
                $evaluationData = KpiEvaluation::where('aspek_kpi_header_id', $id)
                    ->where('kode', $kode)
                    ->where('periode', $periode)
                    ->where('kategori', $kategori)
                    ->first();

                if ($evaluationData) {
                    $evaluationDataValues = json_decode($evaluationData->aspek_values);

                    foreach ($evaluationDataValues as $key => $value) {
                        $data->each(function ($item) use ($key, $value) {
                            if ($item->id == $key) {
                                $item->aspek_values = $value;
                            }
                        });
                    }
                }
                
            }
        }

        return response()->json($data);
    }

    public function fetchByKodeDefault() 
    {
        $data = collect();
        $kode = request('kode');

        // optional params
        $kategori = request('kategori');
        $periode = request('periode');

        if (!$kode) {
            return response()->json($data);
        }

        if (strtolower($kategori ?? '') == 'personal') {
            // $kode == nik
            $data = $this->modelClass::join('master_kpis', 'aspek_kpi_items.master_kpi_id', '=', 'master_kpis.id')
                ->join('kpi_employees', 'aspek_kpi_items.aspek_kpi_header_id', '=', 'kpi_employees.aspek_kpi_header_id')
                ->where('kpi_employees.nik', $kode)
                ->select(
                    'aspek_kpi_items.*',
                    'master_kpis.nama',
                    'master_kpis.area_kinerja_utama',
                    'master_kpis.tipe',
                    'master_kpis.satuan',
                    'master_kpis.sumber_data_realisasi'
                )
                ->get();
        }


        return response()->json($data);
    }

}
