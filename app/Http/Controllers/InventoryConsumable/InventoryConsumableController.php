<?php

namespace App\Http\Controllers\InventoryConsumable;

use App\Models\InventoryConsumable;
use App\Models\InventoryConsumableCategory;
use App\Models\InventoryConsumableStock;
use App\Models\InventoryConsumableMovement;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Helpers\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class InventoryConsumableController extends DefaultController
{
    protected $modelClass = InventoryConsumable::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Daftar Kartu Stok';
        $this->generalUri = 'inventory-consumable';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_inventory_consumable_history', 'btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Sku', 'column' => 'sku', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true], 
                    ['name' => 'Category', 'column' => 'category', 'order' => true], 
                    ['name' => 'Subcategory', 'column' => 'subcategory', 'order' => true], 
                    ['name' => 'Minimum Stock', 'column' => 'minimum_stock', 'order' => true],
                    ['name' => 'Stock', 'column' => 'stock', 'order' => true], 
                    ['name' => 'Satuan', 'column' => 'satuan', 'order' => true], 
                    ['name' => 'Harga satuan', 'column' => 'harga_satuan', 'order' => true], 
                    ['name' => 'Harga total', 'column' => 'harga_total', 'order' => true], 
                    //['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    //['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['sku'],
            'headers' => [
                    ['name' => 'Sku', 'column' => 'sku'],
                    ['name' => 'Name', 'column' => 'name'], 
                    ['name' => 'Category', 'column' => 'category'], 
                    ['name' => 'Subcategory', 'column' => 'subcategory'], 
                    ['name' => 'Minimum Stock', 'column' => 'minimum_stock'],
                    ['name' => 'Satuan', 'column' => 'satuan'], 
                    ['name' => 'Harga Satuan', 'column' => 'harga_satuan'],
            ]
        ];

        $this->importScripts = [
            ['source' => asset('js/modules/module-inventory-consumable.js')],
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $optionsCategory = InventoryConsumableCategory::select('id as value', 'name as text')
            ->whereNull('parent_id')
            ->get();

        if (isset($edit)) {
            $optionsSubcategory = InventoryConsumableCategory::select('id as value', 'name as text')
            ->where('parent_id', '=', $edit->category)
            ->get();
            
            // Get subcategory
            $subcategory = InventoryConsumableCategory::find($edit->category_id);

            // Get parent category (top-level)
            $categoryId = $subcategory?->parent_id ? $subcategory->parent_id : $subcategory->id;
        } else {
            $optionsSubcategory = InventoryConsumableCategory::select('id as value', 'name as text')
            ->whereNotNull('parent_id')
            ->get();
        }

        $fields = [
                    [
                        'type' => 'text',
                        'label' => 'Sku',
                        'name' =>  'sku',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('sku', $id),
                        'value' => (isset($edit)) ? $edit->sku : ''
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Category',
                        'name' =>  'category',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('category', $id),
                        //'value' => (isset($edit)) ? $edit->category : '',
                        'value' => isset($categoryId) ? $categoryId : '',
                        'options' => $optionsCategory,
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Subcategory',
                        'name' =>  'subcategory',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('subcategory', $id),
                        'value' => (isset($edit)) ? $edit->category_id : '', // category_id is subcategory
                        //'value' => isset($subcategory->id) ? $subcategory->id : '',
                        'options' => [],
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Name',
                        'name' =>  'name',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('name', $id),
                        'value' => (isset($edit)) ? $edit->name : ''
                    ],
                    [
                        'type' => 'number',
                        'label' => 'Minimum Stock',
                        'name' =>  'minimum_stock',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('minimum_stock', $id),
                        'value' => (isset($edit)) ? $edit->minimum_stock : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Satuan',
                        'name' =>  'satuan',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('satuan', $id),
                        'value' => (isset($edit)) ? $edit->satuan : ''
                    ],
                    [
                        'type' => 'number',
                        'label' => 'Harga satuan',
                        'name' =>  'harga_satuan',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('harga_satuan', $id),
                        'value' => (isset($edit)) ? $edit->harga_satuan : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'sku' => 'required|string',
                    'name' => 'required|string',
                    'category' => 'required|string',
                    'subcategory' => 'required|string',
                    'minimum_stock' => 'required|integer',
                    'satuan' => 'required|string',
                    'harga_satuan' => 'required|integer',
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

        $dataQueries = $this->modelClass::join('inventory_consumable_stocks', 'inventory_consumable_stocks.item_id', '=', 'inventory_consumables.id')
            ->leftJoin('inventory_consumable_categories AS category_child', 'inventory_consumables.category_id', '=', 'category_child.id')
            ->leftJoin('inventory_consumable_categories AS category_parent', 'category_child.parent_id', '=', 'category_parent.id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $efc = ['#', 'created_at', 'updated_at', 'id', 'harga_total', 'name', 'category', 'subcategory'];

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
                'inventory_consumables.*', 
                'inventory_consumable_stocks.stock',
                'category_child.name AS subcategory',
                'category_parent.name AS category',
                DB::raw('(inventory_consumables.harga_satuan * inventory_consumable_stocks.stock) AS harga_total')
            );

        return $dataQueries;
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
                'message' => 'Kolom tidak boleh kosong',
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
                if($th['name'] === 'category' || $th['name'] === 'subcategory') {
                    continue;
                }

                if ($request[$th['name']]) {
                    $insert->{$th['name']} = $request[$th['name']];
                }
            }
            if (array_key_exists('columns', $appendStore)) {
                foreach ($appendStore['columns'] as $key => $as) {
                    $insert->{$as['name']} = $as['value'];
                }
            }



            $resolveCategoryData = $this->resolveCategoryDataAndSubcategory(
                $request['category'],
                $request['subcategory']
            );

            $insert->category_id = $resolveCategoryData->subcategory->id;



            $insert->save();

            $this->afterMainInsert($insert, $request);

            // Save stok
            $stockInsert = new InventoryConsumableStock();
            $stockInsert->stock = 0;
            $stockInsert->item_id = $insert->id;
            $stockInsert->save();


            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Berhasil Disimpan',
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
                'message' => 'Kolom tidak boleh kosong',
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
                if($th['name'] === 'category' || $th['name'] === 'subcategory') {
                    continue;
                }

                if ($request[$th['name']]) {
                    $change->{$th['name']} = $request[$th['name']];
                }
            }
            if (array_key_exists('columns', $appendUpdate)) {
                foreach ($appendUpdate['columns'] as $key => $as) {
                    $change->{$as['name']} = $as['value'];
                }
            }


            $resolveCategoryData = $this->resolveCategoryDataAndSubcategory(
                $request['category'],
                $request['subcategory']
            );

            $change->category_id = $resolveCategoryData->subcategory->id;
            
            $change->save();

            $this->afterMainUpdate($change, $request);

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Berhasil Diupdate',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // data stock chart
    public function chartData(Request $request)
    {
        $year   = $request->year;
        $itemId = $request->item_id;

        $rows = InventoryConsumableMovement::selectRaw("
            MONTH(movement_datetime) AS month,
            SUM(
                CASE 
                    WHEN type = 'in' THEN qty
                    WHEN type = 'out' THEN -qty
                    ELSE 0
                END
            ) AS total
        ")
        ->where('item_id', $itemId)
        ->whereYear('movement_datetime', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');


        $labels = [];
        $values = [];
        $stock = 0;

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = date('M', mktime(0, 0, 0, $m, 1));

            if (isset($rows[$m])) {
                $stock += $rows[$m]->total;
            }

            $values[] = $stock;
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    public function chartDataOut(Request $request)
    {
        $year   = $request->year;
        $itemId = $request->item_id;

        $rows = InventoryConsumableMovement::selectRaw("
                MONTH(movement_datetime) AS month,
                SUM(qty) AS total
            ")
            ->where('item_id', $itemId)
            ->where('type', 'out')
            ->whereYear('movement_datetime', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $values = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = date('M', mktime(0, 0, 0, $m, 1));
            $values[] = $rows[$m]->total ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
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
        $permission[] = 'inventory_consumable_history';
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
        $data['uri_create'] = route($this->generalUri . '.create');
        $data['url_store'] = route($this->generalUri . '.store');
        $data['fields'] = $this->fields();
        $data['edit_fields'] = $this->fields('edit');
        $data['buttonTextCreate'] = 'Input Kartu Stok';
        $data['buttonTextCreateNew'] = 'Input Kartu Stok';
        
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
        

        $data['actionButtonViews'] = $this->actionButtonViews;
        $data['templateImportExcel'] = "#";
        $data['import_scripts'] = $this->importScripts;
        $data['import_styles'] = $this->importStyles;
        $data['filters'] = $this->filters();
        
        return view($layout, $data);
    }

    protected function indexApi()
    {
        $manyDatas = 10;
        if (request('manydatas')) {
            $manyDatas = request('manydatas');
            if ($manyDatas == "All") {
                $manyDatas = 10000; // we are using this boundaries to prevent lack of request
            }
        }
        $permission =  $this->arrPermissions;
        if ($this->dynamicPermission) {
            $permission = (new Constant())->permissionByMenu($this->generalUri);
        }
        foreach ($this->extendPermissions as $keyE => $ep) {
            $permission[] = $ep;
        }
        $permission[] = 'inventory_consumable_history';
        $eb = [];
        $dataColumns = [];
        $dataColFormat = [];
        foreach ($this->tableHeaders as $key => $col) {
            if ($key > 0) {
                $dataColumns[] = $col['column'];
                if (array_key_exists("formatting", $col)) {
                    $dataColFormat[$col['column']] = $col['formatting'];
                }
            }
        }

        foreach ($this->actionButtons as $key => $ab) {
            if (in_array(str_replace("btn_", "", $ab), $permission)) {
                $eb[] = $ab;
            }
        }

        $dataQueries = $this->defaultDataQuery()->paginate($manyDatas);

        $datas['extra_buttons'] = $eb;
        $datas['data_columns'] = $dataColumns;
        $datas['data_col_formatting'] = $dataColFormat;
        $datas['data_queries'] = $dataQueries;
        $datas['data_permissions'] = $permission;
        $datas['uri_key'] = $this->generalUri;

        foreach ($datas['data_queries'] as $key => $dq) {
            if($dq->stock <= $dq->minimum_stock){
                $dq->name = "<span class='text-danger fw-bold'>".$dq->name."</span>";
                $dq->stock = "<span class='text-danger fw-bold'>".$dq->stock."</span>";
            }
        }

        return $datas;
    }

    protected function show($id)
    {
        $singleData = $this->defaultDataQuery()->where('inventory_consumables.id', $id)->first();
        unset($singleData['id']);

        $data['detail'] = $singleData;

        return view('easyadmin::backend.idev.show-default', $data);
    }

    private function resolveCategoryDataAndSubcategory($categoryValue, $subcategoryValue)
    {
        /*  Resolve CATEGORY */
        // Try lookup as ID (only root categories)
        $categoryModel = InventoryConsumableCategory::whereNull('parent_id')
            ->where('id', $categoryValue)
            ->first();

        // Try lookup as name if not ID match
        if (!$categoryModel) {
            $categoryModel = InventoryConsumableCategory::whereNull('parent_id')
                ->where('name', $categoryValue)
                ->first();
        }

        // If still not found use as is for new category name
        $categoryName = $categoryModel?->name ?? $categoryValue;

        // Create or fetch CATEGORY
        $categoryInsert = InventoryConsumableCategory::firstOrCreate(
            [
                'name' => $categoryName,
                'parent_id' => null,
            ]
        );


        /* Resolve SUBCATEGORY (ID or NAME under CATEGORY) */
        // Try lookup as ID under this category
        $subcategoryModel = InventoryConsumableCategory::where('parent_id', $categoryInsert->id)
            ->where('id', $subcategoryValue)
            ->first();

        // Try lookup by name under this category
        if (!$subcategoryModel) {
            $subcategoryModel = InventoryConsumableCategory::where('parent_id', $categoryInsert->id)
                ->where('name', $subcategoryValue)
                ->first();
        }

        // If still not found use as is for new subcategory name
        $subcategoryName = $subcategoryModel?->name ?? $subcategoryValue;

        // Create or fetch SUBCATEGORY under CATEGORY
        $subcategoryInsert = InventoryConsumableCategory::firstOrCreate(
            [
                'name' => $subcategoryName,
                'parent_id' => $categoryInsert->id,
            ]
        );

        return (object)[
            'category' => $categoryInsert,
            'subcategory' => $subcategoryInsert,
        ];
    }
    


    public function fetchItemsByCategory(Request $request)
    {
        $categoryId = $request->category_id;

        if (!$categoryId) {
            return response()->json([]);
        }

        $items = InventoryConsumable::select('id as value', DB::raw("CONCAT(sku, ' - ', name) AS text"))
            ->where('category_id', $categoryId)
            ->get();

        return response()->json($items);
    }


    public function fetchItemsStockData(Request $request)
    {
        $categoryId = $request->category_id;
        $subcategoryId = $request->subcategory_id;

        $query = InventoryConsumable::join('inventory_consumable_stocks', 'inventory_consumable_stocks.item_id', '=', 'inventory_consumables.id')
            ->select(
                'inventory_consumables.id as value',
                DB::raw("CONCAT(inventory_consumables.sku, ' - ', inventory_consumables.name) AS text"),
                'inventory_consumable_stocks.stock as stock'
            );

        if ($categoryId) {
            $query->with('category')->whereHas('category', function ($q) use ($categoryId) {
                $q->where('parent_id', $categoryId);
            });
        }

        if ($subcategoryId) {
            $query->where('inventory_consumables.category_id', $subcategoryId);
        }

        $items = $query->get();

        return response()->json($items);
    }

}
