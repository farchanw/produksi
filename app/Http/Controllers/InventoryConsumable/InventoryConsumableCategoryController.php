<?php

namespace App\Http\Controllers\InventoryConsumable;

use App\Models\InventoryConsumableCategory;
use Illuminate\Http\Request;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Idev\EasyAdmin\app\Helpers\Constant;

class InventoryConsumableCategoryController extends DefaultController
{
    protected $modelClass = InventoryConsumableCategory::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Input Kategori';
        $this->generalUri = 'inventory-consumable-category';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Kategori', 'column' => 'name', 'order' => true],
                    ['name' => 'Nama', 'column' => 'parent', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['name'],
            'headers' => [
                    ['name' => 'Name', 'column' => 'name'],
                    ['name' => 'Parent id', 'column' => 'parent_id'], 
            ]
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $optionsParent = InventoryConsumableCategory::where('parent_id', null)
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->id,
                    'text' => $item->name
                ];
            })->toArray();

        array_unshift($optionsParent, ['value' => '', 'text' => '-- Kategori Utama --']);

        $fields = [
                    [
                        'type' => 'select',
                        'label' => 'Kategori',
                        'name' =>  'parent_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('parent_id', $id),
                        'value' => (isset($edit)) ? $edit->parent_id : '',
                        'options' => $optionsParent,
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Nama',
                        'name' =>  'name',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('name', $id),
                        'value' => (isset($edit)) ? $edit->name : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'name' => 'required|string',
                    'parent_id' => 'nullable|integer',
        ];

        return $rules;
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
                'html_button' => "<a id='export-excel' data-base-url='".$baseUrlExcel."' class='btn btn-sm btn-success radius-6' target='_blank' href='" . url($this->generalUri . '-export-excel-default') . "'  title='Export Excel'><i class='ti ti-cloud-download'></i></a>"
            ],
            [
                'key' => 'export-pdf-default',
                'name' => 'Export Pdf',
                'html_button' => "<a id='export-pdf' data-base-url='".$baseUrlPdf."' class='btn btn-sm btn-danger radius-6' target='_blank' href='" . url($this->generalUri . '-export-pdf-default') . "' title='Export PDF'><i class='ti ti-file'></i></a>"
            ],
        ];

        $permissions =  $this->arrPermissions;
        if ($this->dynamicPermission) {
            $permissions = (new Constant())->permissionByMenu($this->generalUri);
        }
        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'backend.idev.list_drawer';
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

        /* Override edit button */
        // unset first
        if (($key = array_search('easyadmin::backend.idev.buttons.edit', $this->actionButtonViews)) !== false) {
            unset($this->actionButtonViews[$key]);
        }
        // set new edit button
        $this->actionButtonViews[] = 'backend.idev.buttons.edit';

        /* Override delete button */
        // unset first
        if (($key = array_search('easyadmin::backend.idev.buttons.delete', $this->actionButtonViews)) !== false) {
            unset($this->actionButtonViews[$key]);
        }
        // set new delete button
        $this->actionButtonViews[] = 'backend.idev.buttons.delete';

        /* Create new button */
        $this->actionButtonViews[] = 'backend.idev.buttons.inventory_consumable_history';
        


        $data['uri_create'] = route($this->generalUri . '.create');
        $data['url_store'] = route($this->generalUri . '.store');
        $data['fields'] = $this->fields();
        $data['edit_fields'] = $this->fields('edit');
        $data['actionButtonViews'] = $this->actionButtonViews;
        $data['templateImportExcel'] = "#";
        $data['import_scripts'] = $this->importScripts;
        $data['import_styles'] = $this->importStyles;
        $data['filters'] = $this->filters();
        $data['buttonTextCreate'] = 'Input Kategori';
        $data['buttonTextCreateNew'] = 'Input Kategori';
        
        return view($layout, $data);
    }

    protected function fetchCategories()
    {
        return $this->modelClass::where('parent_id', null)->get();
    }



    protected function fetchCategorySubcategories(Request $request)
    {
        $categoryId = $request->category_id;

        if (!$categoryId) {
            return response()->json([]);
        }

        $items = InventoryConsumableCategory::select('id as value', 'name as text')
            ->where('parent_id', $categoryId)
            ->orderBy('name')
            ->get();

        return response()->json($items);
    }

    protected function defaultDataQuery()
    {
        $filters = [];
        $orThose = null;
        $orderBy = 'inventory_consumable_categories.id';
        $orderState = 'DESC';
        if (request('search')) {
            $orThose = request('search');
        }
        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }

        $dataQueries = $this->modelClass::leftJoin('inventory_consumable_categories as parent', 'inventory_consumable_categories.parent_id', '=', 'parent.id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $efc = ['#', 'created_at', 'updated_at', 'id', 'name', 'parent'];

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

                $query->orWhere('inventory_consumable_categories.name', 'LIKE', '%' . $orThose . '%');
            })
            ->orderBy($orderBy, $orderState)
            ->select(
                'inventory_consumable_categories.id',
                'inventory_consumable_categories.name',
                'inventory_consumable_categories.created_at',
                'inventory_consumable_categories.updated_at',
                'parent.name as parent'
        );

        return $dataQueries;
    }


}
