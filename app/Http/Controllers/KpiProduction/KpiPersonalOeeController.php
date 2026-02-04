<?php

namespace App\Http\Controllers\KpiProduction;

use App\Helpers\Modules\KpiProductionHelper;
use App\Helpers\Common\DatetimeHelper;
use App\Models\KpiPersonalOee;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use App\Imports\PersonalOeeImport;
use App\Models\KpiEmployee;
use Maatwebsite\Excel\Facades\Excel;

class KpiPersonalOeeController extends DefaultController
{
    protected $modelClass = KpiPersonalOee::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Kpi Personal Oee';
        $this->generalUri = 'kpi-personal-oee';
        $this->arrPermissions = [
            'import-excel-oee-personal-bulanan-default',
        ];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Nama', 'column' => 'nama', 'order' => true],
                    ['name' => 'Nik', 'column' => 'nik', 'order' => true],
                    ['name' => 'Periode', 'column' => 'periode', 'order' => true, 'formatting' => 'toKpiPeriodDate'],
                    ['name' => 'A (%)', 'column' => 'availability', 'order' => true],
                    ['name' => 'P (%)', 'column' => 'performance', 'order' => true],
                    ['name' => 'Q (%)', 'column' => 'quality', 'order' => true],
                    ['name' => 'OEE (%)', 'column' => 'oee', 'order' => true],
                    ['name' => 'Target', 'column' => 'target', 'order' => true],
                    ['name' => 'Bobot', 'column' => 'bobot', 'order' => true],
                    ['name' => 'Nilai', 'column' => 'nilai', 'order' => true], 
                    //['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    //['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['kpi_employees_id'],
            'headers' => [
                    ['name' => 'Kpi employees id', 'column' => 'kpi_employees_id'],
                    ['name' => 'Periode', 'column' => 'periode'],
                    ['name' => 'Availability', 'column' => 'availability'],
                    ['name' => 'Performance', 'column' => 'performance'],
                    ['name' => 'Quality', 'column' => 'quality'],
                    ['name' => 'Oee', 'column' => 'oee'],
                    ['name' => 'Target', 'column' => 'target'],
                    ['name' => 'Bobot', 'column' => 'bobot'],
                    ['name' => 'Nilai', 'column' => 'nilai'], 
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

        $fields = [
                    [
                        'type' => 'text',
                        'label' => 'Kpi employees id',
                        'name' =>  'kpi_employees_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('kpi_employees_id', $id),
                        'value' => (isset($edit)) ? $edit->kpi_employees_id : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Periode',
                        'name' =>  'periode',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('periode', $id),
                        'value' => (isset($edit)) ? $edit->periode : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Availability',
                        'name' =>  'availability',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('availability', $id),
                        'value' => (isset($edit)) ? $edit->availability : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Performance',
                        'name' =>  'performance',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('performance', $id),
                        'value' => (isset($edit)) ? $edit->performance : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Quality',
                        'name' =>  'quality',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('quality', $id),
                        'value' => (isset($edit)) ? $edit->quality : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Oee',
                        'name' =>  'oee',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('oee', $id),
                        'value' => (isset($edit)) ? $edit->oee : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Target',
                        'name' =>  'target',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('target', $id),
                        'value' => (isset($edit)) ? $edit->target : ''
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
                        'label' => 'Nilai',
                        'name' =>  'nilai',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('nilai', $id),
                        'value' => (isset($edit)) ? $edit->nilai : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'kpi_employees_id' => 'required|string',
                    'periode' => 'required|string',
                    'availability' => 'required|string',
                    'performance' => 'required|string',
                    'quality' => 'required|string',
                    'oee' => 'required|string',
                    'target' => 'required|string',
                    'bobot' => 'required|string',
                    'nilai' => 'required|string',
        ];

        return $rules;
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

        $dataQueries = $this->modelClass::join('kpi_employees', 'kpi_employees.id', '=', 'kpi_personal_oees.kpi_employees_id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $efc = ['#', 'created_at', 'updated_at', 'id', 'nama'];

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
            ->orderBy($orderBy, $orderState)
            ->select(
                'kpi_personal_oees.*',
                'kpi_employees.nama as nama',
                'kpi_employees.nik as nik'
            );

        return $dataQueries;
    }

    public function index()
    {
        $baseUrlExcel = route($this->generalUri.'.export-excel-default');
        $baseUrlPdf = route($this->generalUri.'.export-pdf-default');
        $baseUrlImportExcelOeePersonalBulananDefault = route($this->generalUri.'.import-excel-oee-personal-bulanan-default');

        $moreActions = [
            [
                'key' => 'import-excel-default',
                'name' => 'Import Excel',
                //'html_button' => "<button id='import-excel' type='button' class='btn btn-sm btn-info radius-6' href='#' data-bs-toggle='modal' data-bs-target='#modalImportDefault' title='Import Excel' ><i class='ti ti-upload'></i></button>"
                'html_button' => ''
            ],
            [
                'key' => 'export-excel-default',
                'name' => 'Export Excel',
                'html_button' => "<a id='export-excel' data-base-url='".$baseUrlExcel."' class='btn btn-sm btn-success radius-6' target='_blank' href='" . $baseUrlExcel . "'  title='Export Excel'><i class='ti ti-cloud-download'></i></a>"
            ],
            [
                'key' => 'export-pdf-default',
                'name' => 'Export Pdf',
                'html_button' => "<a id='export-pdf' data-base-url='".$baseUrlPdf."' class='btn btn-sm btn-danger radius-6' target='_blank' href='" . $baseUrlPdf . "' title='Export PDF'><i class='ti ti-file'></i></a>"
            ],
        ];

        $customActions = [
            [
                'key' => 'import-excel-oee-personal-bulanan-default',
                'name' => 'Import Excel OEE Personal Bulanan',
                'html_button' => '<button
                    type="button"
                    id="import-excel-oee-personal-bulanan-default"
                    class="btn btn-sm btn-success radius-6"
                    data-bs-toggle="modal"
                    data-bs-target="#modalImportExcelOeePersonalBulananDefault"
                    data-base-url="'.$baseUrlImportExcelOeePersonalBulananDefault.'"
                    data-csrf="'.csrf_token().'"
                    title="Import Excel Data OEE Personal Bulanan"
                    >
                    <i class="ti ti-upload"></i>
                    </button>
                '
            ],
        ];

        $moreActions = array_merge($moreActions, $customActions);

        $permissions =  $this->arrPermissions;
        if ($this->dynamicPermission) {
            $permissions = (new Constant())->permissionByMenu($this->generalUri);
            $permissions = array_merge($permissions, $this->arrPermissions);
        }
        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'easyadmin::backend.idev.list_drawer';
        if(isset($this->drawerLayout)){
            $layout = $this->drawerLayout;
        }
        $data['permissions'] = $permissions;
        $data['more_actions'] = $moreActions;
        $data['headerLayout'] = $this->pageHeaderLayout;
        $data['table_headers'] = $this->tableHeaders;
        $data['title'] = $this->title;
        $data['uri_key'] = $this->generalUri;
        $data['uri_list_api'] = route($this->generalUri . '.listapi');
        $data['uri_create'] = route($this->generalUri . '.create');
        $data['url_store'] = route($this->generalUri . '.store');
        $data['fields'] = $this->fields();
        $data['edit_fields'] = $this->fields('edit');
        $data['actionButtonViews'] = $this->actionButtonViews;
        $data['templateImportExcel'] = "#";
        $data['import_scripts'] = $this->importScripts;
        $data['import_styles'] = $this->importStyles;
        $data['filters'] = $this->filters();
        
        return view($layout, $data);
    }

    public function importExcelBulanan(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $import = new PersonalOeeImport();
        Excel::import($import, $request->file('file'));

        $periode = $request->periode;
        $periode = DatetimeHelper::getKpiPeriode($periode);
        $pattern = '/^(\d+(?:\.\d+)*)\s*-\s*(.*)$/';

        foreach ($import->result as $id => $value) {
            $nik = $id;
            $nama = $id;
            if (preg_match($pattern, $id, $matches)) {
                [, $nik, $nama] = $matches;
            }
            $employee = KpiEmployee::firstOrCreate([
                'nik' => $nik,
                'nama' => $nama,
            ]);

            $personalOee = KpiPersonalOee::updateOrCreate([
                'kpi_employees_id' => $employee->id,
            ], [
                'periode' => $periode,
                'oee' => $value['avg_oee'],
                'availability' => $value['avg_a'],
                'performance' => $value['avg_p'],
                'quality' => $value['avg_q'],
            ]);
        }

        return redirect()->route($this->generalUri.'.index');
    }

}
