<?php

namespace App\Http\Controllers\KpiProduction;

use App\Models\MasterKpi;
use App\Models\MasterSection;
use App\Models\MasterSubsection;
use App\Helpers\Modules\KpiProductionHelper;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Idev\EasyAdmin\app\Helpers\Constant;
use Illuminate\Http\Request;


class MasterKpiController extends DefaultController
{
    protected $modelClass = MasterKpi::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Master Kpi';
        $this->generalUri = 'master-kpi';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Bagian', 'column' => 'master_section', 'order' => true],
                    ['name' => 'Subbagian', 'column' => 'master_subsection', 'order' => true], 
                    ['name' => 'Kategori', 'column' => 'kategori', 'order' => true],
                    ['name' => 'Area Kinerja Utama', 'column' => 'area_kinerja_utama', 'order' => true],
                    ['name' => 'Nama', 'column' => 'nama', 'order' => true],
                    ['name' => 'Deskripsi', 'column' => 'deskripsi', 'order' => true],
                    ['name' => 'Satuan', 'column' => 'satuan', 'order' => true],
                    ['name' => 'Tipe', 'column' => 'tipe', 'order' => true],
                    ['name' => 'Sumber Data Realisasi', 'column' => 'sumber_data_realisasi', 'order' => true],
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['nama'],
            'headers' => [
                    ['name' => 'Nama', 'column' => 'nama'],
                    ['name' => 'Deskripsi', 'column' => 'deskripsi'],
                    ['name' => 'Bagian', 'column' => 'master_section_id'], 
            ]
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $optionsSubsection = MasterSubsection::select('id as value', 'nama as text')->orderBy('nama', 'ASC')->get()->toArray();
        $optionsSubsection = array_merge([['value' => '', 'text' => 'Pilih Subbagian...']], $optionsSubsection);

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
                        'required' => $this->flagRules('master_section_id', $id),
                        'value' => (isset($edit)) ? $edit->master_section_id : '',
                        'options' => $optionsSubsection,
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Kategori',
                        'name' =>  'kategori',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('kategori', $id),
                        'value' => (isset($edit)) ? $edit->kategori : '',
                        'options' => KpiProductionHelper::optionsKategori(),
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Area Kinerja Utama',
                        'name' =>  'area_kinerja_utama',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('area_kinerja_utama', $id),
                        'value' => (isset($edit)) ? $edit->area_kinerja_utama : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Nama',
                        'name' =>  'nama',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('nama', $id),
                        'value' => (isset($edit)) ? $edit->nama : ''
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Deskripsi',
                        'name' =>  'deskripsi',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('deskripsi', $id),
                        'value' => (isset($edit)) ? $edit->deskripsi : ''
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Satuan',
                        'name' =>  'satuan',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('satuan', $id),
                        'value' => (isset($edit)) ? $edit->satuan : '',
                        'options' => [
                            ['value' => 'angka', 'text' => 'Angka'],
                            ['value' => 'persen', 'text' => 'Persen'],
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Tipe',
                        'name' =>  'tipe',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('tipe', $id),
                        'value' => (isset($edit)) ? $edit->tipe : '',
                        'options' => [
                            ['value' => 'Min', 'text' => 'Min'],
                            ['value' => 'Max', 'text' => 'Max'],
                            
                        ]
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Sumber Data Realisasi',
                        'name' =>  'sumber_data_realisasi',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('sumber_data_realisasi', $id),
                        'value' => (isset($edit)) ? $edit->sumber_data_realisasi : ''
                    ],

        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
            
                    'master_section_id' => 'required|string',
                    'master_subsection_id' => 'required|string',
                    'kategori' => 'required|string',
                    'area_kinerja_utama' => 'required|string',
                    'nama' => 'required|string',
                    'deskripsi' => 'nullable|string',
                    'satuan' => 'required|string',
                    'tipe' => 'required|string',
                    'sumber_data_realisasi' => 'required|string',
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

        $dataQueries = $this->modelClass::join('master_subsections', 'master_kpis.master_subsection_id', '=', 'master_subsections.id')
            ->join('master_sections', 'master_subsections.master_section_id', '=', 'master_sections.id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $efc = ['#', 'created_at', 'updated_at', 'id', 'master_section', 'master_subsection', 'nama'];

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
            ->select('master_kpis.*', 'master_sections.nama as master_section', 'master_subsections.nama as master_subsection')
            ->orderBy($orderBy, $orderState);

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

    public function fetchDefault(Request $request)
    {
        $data = collect();

        if ($request->subsection_id) {
            $data = $this->defaultDataQuery()
                        ->where('master_subsection_id', $request->subsection_id)
                        ->get();
        }

        // map $data to [{value, text}]
        $data = $data->map(function ($item) {
            return [
                'value' => $item->id,
                'text'  => $item->nama,
            ];
        });

        return response()->json($data);

    }


}
