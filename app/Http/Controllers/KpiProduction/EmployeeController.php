<?php

namespace App\Http\Controllers\KpiProduction;

use App\Models\Employee;
use App\Models\MasterSection;
use App\Models\MasterSubsection;
use Illuminate\Http\Request;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class EmployeeController extends DefaultController
{
    protected $modelClass = Employee::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Employee';
        $this->generalUri = 'employee';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Nama', 'column' => 'nama', 'order' => true],
                    ['name' => 'Bagian', 'column' => 'master_section', 'order' => true],
                    ['name' => 'Subbagian', 'column' => 'master_subsection', 'order' => true],
                    ['name' => 'Aspek KPI Header', 'column' => 'aspek_kpi_header_id', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['nama'],
            'headers' => [
                    ['name' => 'Nama', 'column' => 'nama'],
                    ['name' => 'Master section id', 'column' => 'master_section_id'],
                    ['name' => 'Master subsection id', 'column' => 'master_subsection_id'],
                    ['name' => 'Aspek kpi header id', 'column' => 'aspek_kpi_header_id'], 
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
                        'label' => 'Nama',
                        'name' =>  'nama',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('nama', $id),
                        'value' => (isset($edit)) ? $edit->nama : ''
                    ],
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
                    [
                        'type' => 'text',
                        'label' => 'Aspek KPI Header',
                        'name' =>  'aspek_kpi_header_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('aspek_kpi_header_id', $id),
                        'value' => (isset($edit)) ? $edit->aspek_kpi_header_id : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'nama' => 'required|string',
                    'master_section_id' => 'required|string',
                    'master_subsection_id' => 'required|string',
                    'aspek_kpi_header_id' => 'required|string',
        ];

        return $rules;
    }

    protected function defaultDataQuery()
    {
        $filters = [];
        $orThose = null;
        $orderBy = 'employees.id';
        $orderState = 'DESC';
        if (request('search')) {
            $orThose = request('search');
        }
        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }

        $dataQueries = $this->modelClass::join('master_subsections', 'employees.master_subsection_id', '=', 'master_subsections.id')
            ->join('master_sections', 'employees.master_section_id', '=', 'master_sections.id')
            ->join('aspek_kpi_headers', 'employees.aspek_kpi_header_id', '=', 'aspek_kpi_headers.id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $efc = ['#', 'created_at', 'updated_at', 'id', 'master_section', 'master_subsection', 'aspek_kpi_header', 'nama'];

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
                'employees.*',
                'master_subsections.nama as master_subsection',
                'master_sections.nama as master_section',
                'aspek_kpi_headers.id as aspek_kpi_header'    
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
