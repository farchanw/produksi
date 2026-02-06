<?php

namespace App\Http\Controllers\InventoryConsumable;

use App\Helpers\Modules\InventoryConsumableHelper;
use App\Models\InventoryConsumable;
use App\Models\InventoryConsumableMovement;
use App\Models\InventoryConsumableStock;
use Exception;
use Idev\EasyAdmin\app\Helpers\Constant;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
        $this->title = 'Item';
        $this->generalUri = 'inventory-consumable';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
            ['name' => 'No', 'column' => '#', 'order' => true],
            ['name' => 'Kode Item', 'column' => 'sku', 'order' => true],
            ['name' => 'Nama Item', 'column' => 'name', 'order' => true, 'formatting' => 'toInventoryConsumableNotifyStockLow'],
            ['name' => 'Jenis', 'column' => 'kind', 'order' => true],
            ['name' => 'Kategori', 'column' => 'category', 'order' => true],
            ['name' => 'Subkategori', 'column' => 'subcategory', 'order' => true],
            ['name' => 'Min. Stock', 'column' => 'minimum_stock', 'order' => true],
            ['name' => 'Stock', 'column' => 'stock', 'order' => true, 'formatting' => 'toInventoryConsumableNotifyStockLow'],
            ['name' => 'Satuan', 'column' => 'satuan', 'order' => true],
            // ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            // ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];

        $this->importExcelConfig = [
            'primaryKeys' => ['sku'],
            'headers' => [
                ['name' => 'Sku', 'column' => 'sku'],
                ['name' => 'Name', 'column' => 'name'],
                ['name' => 'Category', 'column' => 'category'],
                ['name' => 'Minimum Stock', 'column' => 'minimum_stock'],
                ['name' => 'Satuan', 'column' => 'satuan'],
            ],
        ];

        $this->importScripts = [
            ['source' => asset('vendor/select2/js/select2.min.js')],
            ['source' => asset('vendor/tom-select/tom-select.complete.min.js')],
            ['source' => asset('js/modules/module-inventory-consumable.js')],

        ];

        $this->importStyles = [
            ['source' => asset('vendor/select2/css/select2.min.css')],
            ['source' => asset('vendor/select2/css/select2-bootstrap-5-theme.min.css')],
            ['source' => asset('vendor/tom-select/tom-select.css')],
            ['source' => asset('vendor/tom-select/tom-select.fix.css')],
        ];
    }

    protected function fields($mode = 'create', $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $optionsCategory = InventoryConsumableHelper::optionsForCategories();
        $optionsSubcategory = InventoryConsumableHelper::optionsForSubcategories()->toArray();
        $optionsKind = InventoryConsumableHelper::optionsForKinds();

        if (isset($edit)) {
            $edit->subcategory_id = DB::table('inventory_consumable_item_subcategory')
                ->where('item_id', $edit->id)
                ->pluck('subcategory_id');
        }

        $fields = [
            [
                'type' => 'text',
                'label' => 'Kode Item',
                'name' => 'sku',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('sku', $id),
                'value' => (isset($edit)) ? $edit->sku : InventoryConsumable::select(DB::raw('MAX(sku) as sku'))->first()->sku + 1,
            ],
            [
                'type' => 'select',
                'label' => 'Jenis',
                'name' => 'kind_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('kind_id', $id),
                'value' => (isset($edit)) ? $edit->kind_id : '',
                'options' => $optionsKind,
            ],
            [
                'type' => 'select',
                'label' => 'Kategori',
                'name' => 'category_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('category', $id),
                // 'value' => (isset($edit)) ? $edit->category : '',
                'value' => isset($edit->category_id) ? $edit->category_id : '',
                'options' => $optionsCategory,
            ],
            [
                'type' => 'text',
                'label' => 'Nama',
                'name' => 'name',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('name', $id),
                'value' => (isset($edit)) ? $edit->name : '',
            ],
            [
                'type' => 'checklist_searchable',
                'label' => 'Subkategori',
                'name' => 'subcategory_id',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('subcategory_id', $id),
                'value' => isset($edit) ? $edit->subcategory_id : '',
                'options' => $optionsSubcategory,
            ],
            [
                'type' => 'number',
                'label' => 'Min. Stock',
                'name' => 'minimum_stock',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('minimum_stock', $id),
                'value' => (isset($edit)) ? $edit->minimum_stock : '',
            ],
            [
                'type' => 'text',
                'label' => 'Satuan',
                'name' => 'satuan',
                'class' => 'col-md-12 my-2',
                'required' => $this->flagRules('satuan', $id),
                'value' => (isset($edit)) ? $edit->satuan : '',
            ],
        ];

        return $fields;
    }

    protected function filters()
    {
        $optionsCategory = InventoryConsumableHelper::optionsForCategories();
        array_unshift($optionsCategory, ['value' => '', 'text' => 'Semua']);

        $fields = [
            [
                'type' => 'select',
                'label' => 'Kategori',
                'name' => 'category_id',
                'class' => 'col-md-2',
                'options' => $optionsCategory,
            ],
        ];

        return $fields;
    }

    protected function rules($id = null)
    {
        $rules = [
            'name' => 'required|string',
            'kind_id' => 'required|integer',
            'category_id' => 'required|integer',
            'minimum_stock' => 'required|integer',
            'satuan' => 'required|string',
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
        // filters
        if (request('category_id')) {
            $filters[] = ['inventory_consumable_categories.id', '=', request('category_id')];
        }
        if (request('kind_id')) {
            $filters[] = ['inventory_consumable_kinds.id', '=', request('kind_id')];
        }

        $dataQueries = $this->modelClass::join('inventory_consumable_stocks', 'inventory_consumable_stocks.item_id', '=', 'inventory_consumables.id')
            ->join('inventory_consumable_categories', 'inventory_consumables.category_id', '=', 'inventory_consumable_categories.id')
            ->join('inventory_consumable_kinds', 'inventory_consumables.kind_id', '=', 'inventory_consumable_kinds.id')
            ->leftJoin('inventory_consumable_item_subcategory as pivot', 'inventory_consumables.id', '=', 'pivot.item_id')
            ->leftJoin('inventory_consumable_subcategories as subcategories', 'pivot.subcategory_id', '=', 'subcategories.id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $efc = ['#', 'created_at', 'updated_at', 'id', 'name', 'category', 'subcategory', 'kind'];

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

                $query->orWhere('inventory_consumables.name', 'LIKE', '%'.$orThose.'%');
                $query->orWhere('inventory_consumable_categories.name', 'LIKE', '%'.$orThose.'%');
            })
            ->orderBy($orderBy, $orderState)
            ->groupBy(
                'inventory_consumables.id',
                'inventory_consumable_stocks.stock'
            )
            ->select(
                'inventory_consumables.*',
                'inventory_consumable_stocks.stock',
                'inventory_consumable_categories.name AS category',
                'inventory_consumable_kinds.name AS kind',
                DB::raw('GROUP_CONCAT(subcategories.name SEPARATOR ", ") as subcategory')

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

            $insert = new $this->modelClass;
            foreach ($this->fields('create') as $key => $th) {
                if ($th['name'] === 'category') {
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

            if (empty($insert->sku)) {
                $insert->sku = $insert->id;
            }

            // Save SUBCATEGORY
            $dataSubcategory = $request->subcategory_id ?? [];
            unset($insert->subcategory_id);

            $insert->save();

            if (empty($insert->sku)) {
                $insert->sku = $insert->id;
                $insert->save(); // update with SKU
            }

            $this->afterMainInsert($insert, $request);

            // Save stock
            $insertStock = InventoryConsumableStock::updateOrCreate(
                ['item_id' => $insert->id],
                ['stock' => 0]
            );

            // Save subcategories
            if (! empty($dataSubcategory)) {
                $insert->subcategories()->sync($dataSubcategory);
            }

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
                if ($th['name'] === 'category') {
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

            // Save SUBCATEGORY
            $dataSubcategory = $request->subcategory_id ?? [];
            unset($change->subcategory_id);

            $change->save();

            $this->afterMainUpdate($change, $request);

            // Save subcategories
            if (! empty($dataSubcategory)) {
                $change->subcategories()->sync($dataSubcategory);
            }

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
        $year = $request->year;
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
        $year = $request->year;
        $itemId = $request->item_id;

        $data = InventoryConsumableHelper::getMovementOutChartData($year, $itemId);

        return response()->json($data);
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
        $permission[] = 'inventory_consumable_history';
        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'backend.idev.list_drawer';
        if (isset($this->drawerLayout)) {
            $layout = $this->drawerLayout;
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
        $data['buttonTextCreate'] = 'Input Item';
        $data['buttonTextCreateNew'] = 'Input Item';

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

        $data['actionButtonViews'] = $this->actionButtonViews;
        $data['templateImportExcel'] = '#';
        $data['import_scripts'] = $this->importScripts;
        $data['import_styles'] = $this->importStyles;
        $data['filters'] = $this->filters();

        return view($layout, $data);
    }

    protected function show($id)
    {
        $data['headerLayout'] = $this->pageHeaderLayout;
        $data['table_headers'] = [
            ['name' => 'Tanggal', 'column' => 'movement_datetime', 'order' => false],
            ['name' => 'Item', 'column' => 'item', 'order' => false],
            ['name' => 'Kategori', 'column' => 'category', 'order' => false],
            ['name' => 'Subkategori', 'column' => 'subcategory', 'order' => false],
            ['name' => 'Type', 'column' => 'type', 'order' => false],
            ['name' => 'Qty', 'column' => 'qty', 'order' => false],
            ['name' => 'Awal', 'column' => 'stock_awal', 'order' => false],
            ['name' => 'Akhir', 'column' => 'stock_akhir', 'order' => false],
            ['name' => 'Catatan', 'column' => 'notes', 'order' => false],
            // ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
            // ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];
        $data['title'] = $this->title;
        $data['uri_key'] = $this->generalUri;
        $data['uri_list_api'] = route($this->generalUri.'.listapi');
        $data['uri_create'] = route($this->generalUri.'.create');
        $data['url_store'] = route($this->generalUri.'.store');
        $data['edit_fields'] = $this->fields('edit');

        $data['actionButtonViews'] = $this->actionButtonViews;
        $data['templateImportExcel'] = '#';
        $data['import_scripts'] = $this->importScripts;
        $data['import_styles'] = $this->importStyles;
        $data['filters'] = $this->filters();
        $data['dataList'] = InventoryConsumableMovement::join(
            'inventory_consumables',
            'inventory_consumables.id',
            '=',
            'inventory_consumable_movements.item_id'
        )
            ->join(
                'inventory_consumable_stocks',
                'inventory_consumable_stocks.item_id',
                '=',
                'inventory_consumables.id'
            )
            ->join(
                'inventory_consumable_categories',
                'inventory_consumables.category_id',
                '=',
                'inventory_consumable_categories.id'
            )
            ->leftJoin(
                'inventory_consumable_item_subcategory as pivot',
                'inventory_consumables.id',
                '=',
                'pivot.item_id'
            )
            ->leftJoin(
                'inventory_consumable_subcategories as subcategories',
                'pivot.subcategory_id',
                '=',
                'subcategories.id'
            )
            ->select(
                'inventory_consumable_movements.id',
                'inventory_consumable_movements.item_id',
                'inventory_consumable_movements.qty',
                'inventory_consumable_movements.type',
                'inventory_consumable_movements.created_at',
                'inventory_consumable_movements.updated_at',
                'inventory_consumable_movements.qty',
                'inventory_consumable_movements.stock_awal',
                'inventory_consumable_movements.stock_akhir',
                'inventory_consumable_movements.movement_datetime',
                'inventory_consumable_movements.notes',
                'inventory_consumable_movements.updated_at',
                'inventory_consumables.name as item',
                'inventory_consumable_categories.name as category',
                DB::raw('GROUP_CONCAT(DISTINCT subcategories.name SEPARATOR ", ") as subcategory')
            )
            ->where('inventory_consumable_movements.item_id', $id)
            ->groupBy('inventory_consumable_movements.id')
            ->orderBy('inventory_consumable_movements.id', 'DESC')
            ->limit(100)
            ->get();

        return view('backend.idev.extend.show.show_inventory_consumable', $data);
    }

    public function fetchItemsByCategory(Request $request)
    {
        $items = InventoryConsumableHelper::getItemsByCategory($request->category_id);

        return response()->json($items);
    }

    public function fetchItemsStockData(Request $request)
    {
        $categoryId = $request->category_id;
        $query = InventoryConsumable::join('inventory_consumable_stocks', 'inventory_consumable_stocks.item_id', '=', 'inventory_consumables.id')
            ->select(
                'inventory_consumables.id as value',
                'inventory_consumables.minimum_stock',
                'inventory_consumables.satuan',
                'inventory_consumables.name as text',
                'inventory_consumable_stocks.stock as stock'
            );

        if ($categoryId) {
            $query->with('category')->where('inventory_consumables.category_id', $categoryId);
        }

        $items = $query->get();

        return response()->json($items);
    }

    public function fetchItemSubcategories(Request $request)
    {
        $id = $request->id;
        $items = InventoryConsumableHelper::getItemSubcategories($id);

        return response()->json($items);
    }
}
