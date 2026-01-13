<?php

namespace App\Http\Controllers\KpiProduction;


use App\Models\MasterKpi;
use App\Models\MasterSection;
use App\Models\MasterSubsection;
use App\Models\AspekKpiHeader;
use App\Models\AspekKpiItem;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Idev\EasyAdmin\app\Helpers\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Idev\EasyAdmin\app\Helpers\Validation;
use Illuminate\Support\Facades\Validator;
use Exception;

class AspekKpiHeaderController extends DefaultController
{
    protected $modelClass = AspekKpiHeader::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    protected $dynamicPermission = true;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Aspek KPI';
        $this->generalUri = 'aspek-kpi-header';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Bagian', 'column' => 'master_section', 'order' => true],
                    ['name' => 'Subbagian', 'column' => 'master_subsection', 'order' => true],
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['master_section_id'],
            'headers' => [
                    ['name' => 'Master section id', 'column' => 'master_section_id'],
                    ['name' => 'Master subsection id', 'column' => 'master_subsection_id'],
            ]
        ];

        $this->importScripts = [
            ['source' => asset('js/modules/module-kpi-production.js')],
            
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
            $repeatableAspekKpiItemValue = AspekKpiItem::where('aspek_kpi_header_id', $edit->id)->get();
        }



        $fields = [
                    [
                        'type' => 'select',
                        'label' => 'Bagian',
                        'name' =>  'master_section_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('master_section_id', $id),
                        'value' => (isset($edit)) ? $edit->master_section_id : '',
                        'options' => MasterSection::select('id as value', 'nama as text')->orderBy('nama', 'ASC')->get()->toArray(),
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Subbagian',
                        'name' =>  'master_subsection_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('master_subsection_id', $id),
                        'value' => (isset($edit)) ? $edit->master_subsection_id : '',
                        'options' => MasterSubsection::select('id as value', 'nama as text')->orderBy('nama', 'ASC')->get()->toArray(),
                    ],
                    /*
                    [
                        'type' => 'select',
                        'label' => 'Tahun',
                        'name' =>  'tahun',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('tahun', $id),
                        'value' => (isset($edit)) ? $edit->tahun : date('Y'),
                        'options' => $optionsYear,
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Bulan',
                        'name' =>  'bulan',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('bulan', $id),
                        'value' => (isset($edit)) ? $edit->bulan : date('n'),
                        'options' => $optionsMonth,
                    ],
                    */
                    [
                        'type' => 'repeatable_aspek_kpi_item',
                        'label' => 'Aspek KPI',
                        'name' =>  'aspek_kpi',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('aspek_kpi', $id),
                        'value' => (isset($edit)) ? $repeatableAspekKpiItemValue : '',
                        'enable_action' => true,
                        'field_data' => [
                            'master_kpi' => MasterKpi::select('id as value', 'nama as text')->orderBy('nama', 'ASC')->get()->toArray(), 
                        ],
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'master_section_id' => 'required|string',
                    'master_subsection_id' => 'required|string',

                    'kpi'                   => 'required|array|min:1',
                    'kpi.*.master_kpi_id'   => 'required|integer',
                    'kpi.*.bobot'           => 'required|numeric|min:0',
                    'kpi.*.target'          => 'required|numeric|min:0',
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

            $insert->save();

            $this->afterMainInsert($insert, $request);

            foreach ($request->kpi as $row) {
                AspekKpiItem::create([
                    'aspek_kpi_header_id'   => $insert->id,
                    'master_kpi_id'         => $row['master_kpi_id'],
                    'bobot'                 => $row['bobot'],
                    'target'                => $row['target'],
                ]);
            }

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
            
            $change->save();

            $this->afterMainUpdate($change, $request);

            // sync kpi (DESTRUCTIVE)
            /*
            AspekKpiItem::where('aspek_kpi_header_id', $change->id)->delete();
            foreach ($request->kpi as $row) {
                AspekKpiItem::create([
                    'aspek_kpi_header_id' => $change->id,
                    'master_kpi_id'       => $row['master_kpi_id'],
                    'bobot'               => $row['bobot'],
                    'target'              => $row['target'],
                ]);
            }
            */

            foreach ($request->kpi as $row) {
                AspekKpiItem::where('id', $row['id'])->update([
                    'master_kpi_id' => $row['master_kpi_id'],
                    'bobot'         => $row['bobot'],
                    'target'        => $row['target'],
                ]);
            }



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

    protected function defaultDataQuery()
    {
        $filters = [];
        $orThose = null;
        $orderBy = 'aspek_kpi_headers.id';
        $orderState = 'DESC';
        if (request('search')) {
            $orThose = request('search');
        }
        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }

        $dataQueries = $this->modelClass::join('master_subsections', 'aspek_kpi_headers.master_subsection_id', '=', 'master_subsections.id'
        )
            ->join('master_sections', 'master_subsections.master_section_id', '=', 'master_sections.id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $efc = ['#', 'created_at', 'updated_at', 'id', 'master_section', 'master_subsection'];

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
                'aspek_kpi_headers.*',
                'master_sections.nama as master_section',
                'master_subsections.nama as master_subsection'
            );

        return $dataQueries;
    }

    public function index()
    {
        $baseUrlExcel = route($this->generalUri.'.export-excel-default');
        $baseUrlPdf = route($this->generalUri.'.export-pdf-default');

        $moreActions = [
            [
                'key' => 'import-excel-default',
                'name' => 'Import Excel',
                'html_button' => "<button id='import-excel' type='button' class='btn btn-sm btn-info radius-6' href='#' data-bs-toggle='modal' data-bs-target='#modalImportDefault' title='Import Excel' ><i class='ti ti-upload'></i></button>"
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

        $permissions =  $this->arrPermissions;
        if ($this->dynamicPermission) {
            $permissions = (new Constant())->permissionByMenu($this->generalUri);
        }
        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'easyadmin::backend.idev.list_drawer';
        if(isset($this->drawerLayout)){
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


}
