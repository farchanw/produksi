<?php

namespace App\Http\Controllers\InventoryConsumable;

use App\Models\InventoryConsumableMovement;
use App\Models\InventoryConsumable;
use App\Models\InventoryConsumableCategory;
use App\Helpers\Modules\InventoryConsumableHelper;
use App\Models\InventoryConsumableStock;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Idev\EasyAdmin\app\Helpers\Validation;
use Idev\EasyAdmin\app\Helpers\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Carbon\Carbon;
use Illuminate\Validation\Rules\In;

class InventoryConsumableMovementController extends DefaultController
{
    protected $modelClass = InventoryConsumableMovement::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Riwayat Kartu Stok';
        $this->generalUri = 'inventory-consumable-movement';
        // $this->arrPermissions = [];
        $this->actionButtons = [/*'btn_edit',*/ 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Item', 'column' => 'item', 'order' => true],
                    ['name' => 'Kategori', 'column' => 'category', 'order' => true],
                    ['name' => 'Type', 'column' => 'type', 'order' => true, 'formatting' => 'toInventoryInOutBadge'],
                    ['name' => 'Qty', 'column' => 'qty', 'order' => true],
                    ['name' => 'Awal', 'column' => 'stock_awal', 'order' => true],
                    ['name' => 'Akhir', 'column' => 'stock_akhir', 'order' => true],
                    ['name' => 'Harga', 'column' => 'harga', 'order' => true, 'formatting' => 'toRupiah'],
                    ['name' => 'Tanggal', 'column' => 'movement_datetime', 'order' => true],
                    ['name' => 'Catatan', 'column' => 'notes', 'order' => true], 
                    //['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    //['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['qty'],
            'headers' => [
                    ['name' => 'Item', 'column' => 'item'],
                    ['name' => 'Type', 'column' => 'type'],
                    ['name' => 'Qty', 'column' => 'qty'],
                    ['name' => 'Harga', 'column' => 'harga'],
                    ['name' => 'Movement datetime', 'column' => 'movement_datetime'],
                    ['name' => 'Notes', 'column' => 'notes'],
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

        $optionsItem = InventoryConsumable::select('id as value', DB::raw('CONCAT_WS(" - ", sku, name) as text'))
            ->orderBy('name', 'ASC')
            ->get()
            ->toArray();

        // Top-level categories
        $optionsCategory = InventoryConsumableCategory::select('id as value', 'name as text')->get();

        if (isset($edit)) {
            // 
        }



        $fields = [
                    [
                        'type' => 'select',
                        'label' => 'Kategori',
                        'name' =>  'category_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('category_id', $id),
                        'value' => isset($edit) ? $edit->category_id : '',
                        'options' => $optionsCategory
                    ],
                    [
                        'type' => 'select2',
                        'label' => 'Item',
                        'name' =>  'item_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('item_id', $id),
                        'value' => (isset($edit)) ? $edit->item_id : '',
                        'options' => $optionsItem
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Type',
                        'name' =>  'type',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('type', $id),
                        'value' => (isset($edit)) ? $edit->type : '',
                        'options' => InventoryConsumableHelper::optionsForMovementTypes(),
                        'filter' => true,
                    ],
                    [
                        'type' => 'number',
                        'label' => 'Qty',
                        'name' =>  'qty',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('qty', $id),
                        'value' => (isset($edit)) ? $edit->qty : ''
                    ],
                    [
                        'type' => 'datetime',
                        'label' => 'Tanggal',
                        'name' =>  'movement_datetime',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('movement_datetime', $id),
                        'value' => (isset($edit)) ? $edit->movement_datetime : ''
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Catatan',
                        'name' =>  'notes',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('notes', $id),
                        'value' => (isset($edit)) ? $edit->notes : ''
                    ],
        ];
        
        return $fields;
    }



    protected function filters()
    {
        $optionsCategory = InventoryConsumableHelper::optionsForCategories()->toArray();
        array_unshift($optionsCategory, ['value' => '', 'text' => 'Semua']);

        $optionsType = InventoryConsumableHelper::optionsForMovementTypes();
        array_unshift($optionsType, ['value' => '', 'text' => 'Semua']);


        $fields = [
            [
                'type' => 'select',
                'label' => 'Kategori',
                'name' =>  'category_id',
                'class' => 'col-md-2',
                'options' => $optionsCategory,
            ],
            [
                'type' => 'select',
                'label' => 'Type',
                'name' =>  'type',
                'class' => 'col-md-2',
                'options' => $optionsType,
            ],
            [
                'type' => 'monthrange',
                'label' => 'Tanggal',
                'name' =>  'tanggal',
                'class' => 'col-md-4',
            ],
        ];

        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'item_id' => 'required|string',
                    'qty' => 'required|numeric',
                    'type' => 'required|string',
                    'movement_datetime' => 'required|string',
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
        if (request('type')) {
            $filters[] = ['inventory_consumable_movements.type', '=', request('type')];
        }
        if (request('category_id')) {
            $filters[] = ['inventory_consumable_categories.id', '=', request('category_id')];
        }
        if (request('tanggal_start') && request('tanggal_end')) {
            $filters[] = [
                'inventory_consumable_movements.movement_datetime',
                '>=',
                request('tanggal_start') . '-01 00:00:00'
            ];

            $filters[] = [
                'inventory_consumable_movements.movement_datetime',
                '<',
                Carbon::parse(request('tanggal_end') . '-01')
                    ->addMonth()
                    ->format('Y-m-d 00:00:00')
            ];
        }

        $dataQueries = $this->modelClass::join('inventory_consumables', 'inventory_consumable_movements.item_id', '=', 'inventory_consumables.id')
            ->join('inventory_consumable_categories', 'inventory_consumables.category_id', '=', 'inventory_consumable_categories.id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $efc = ['#', 'created_at', 'updated_at', 'id', 'item', 'category'];

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

                $query->orWhere('inventory_consumables.name', 'LIKE', '%' . $orThose . '%');
                $query->orWhere('inventory_consumable_categories.name', 'LIKE', '%' . $orThose . '%');
            })
            ->orderBy($orderBy, $orderState)
            ->select('inventory_consumable_movements.*', 'inventory_consumables.name as item',  'inventory_consumable_categories.name AS category');

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

            // Set harga
            $hargaSatuan = InventoryConsumable::where('id', $insert->item_id)->first()->harga_satuan;
            $insert->harga = $insert->qty * $hargaSatuan;
            
            // Set stock awal
            $insert->stock_awal = InventoryConsumableStock::where('item_id', $insert->item_id)->first()->stock ?? 0;

            // Set stock akhir
            if ($insert->type == 'out') {
                $insert->stock_akhir = $insert->stock_awal - abs((int) $insert->qty);
            } elseif ($insert->type == 'in') {
                $insert->stock_akhir = $insert->stock_awal + abs((int) $insert->qty);
            } elseif ($insert->type == 'adjust') {
                $insert->stock_akhir = (int) $insert->qty;
            } else {
                return response()->json([
                    'status' => false,
                    'alert' => 'danger',
                    'message' => 'Kolom type tidak valid',
                ], 200);
            }

            // unset
            unset($insert->category_id);

            $insert->save();

            $this->afterMainInsert($insert, $request);

            // Normalize qty based on type
            $qty = match ($insert->type) {
                'out'    => -abs((int) $insert->qty),
                'in'     =>  abs((int) $insert->qty),
                'adjust' => (int) $insert->qty,
                default  => null,
            };

            if ($qty === null) {
                return response()->json([
                    'status' => false,
                    'alert'  => 'danger',
                    'message'=> 'Kolom type tidak valid',
                ], 200);
            }

            $insert->qty = $qty;

            // Get current stock
            $currentStock = (int) DB::table('inventory_consumable_stocks')
                ->where('item_id', $insert->item_id)
                ->value('stock') ?? 0;

            // Calculate new stock
            $newStock = $insert->type === 'adjust'
                ? $insert->qty
                : $currentStock + $insert->qty;

            if ($newStock < 0) {
                return response()->json([
                    'status' => false,
                    'alert'  => 'danger',
                    'message'=> 'Nilai value tidak valid',
                ], 200);
            }

            DB::table('inventory_consumable_stocks')->updateOrInsert(
                ['item_id' => $insert->item_id],
                ['stock' => $newStock]
            );

            
            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Berhasil Dibuat',
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
        $data['uri_create'] = route($this->generalUri . '.create');
        $data['url_store'] = route($this->generalUri . '.store');
        $data['fields'] = $this->fields();
        $data['edit_fields'] = $this->fields('edit');
        
        /* Append build query params */
        $params = [];
        if (request('inventory_consumable_id')) {
            $params['inventory_consumable_id'] = request('inventory_consumable_id');
        }
        $urlListApiQueryParams = http_build_query($params);
        $data['uri_list_api'] = $data['uri_list_api'] . '?' . $urlListApiQueryParams;

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

        $data['actionButtonViews'] = $this->actionButtonViews;
        $data['templateImportExcel'] = "#";
        $data['import_scripts'] = $this->importScripts;
        $data['import_styles'] = $this->importStyles;
        $data['filters'] = $this->filters();
        $data['buttonTextCreate'] = 'Input Kartu Stock';
        $data['buttonTextCreateNew'] = 'Input Kartu Stock';
        
        return view($layout, $data);
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
                'message' => 'Kolom wajib diisi',
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

            // Jangan update qty
            if ($change->qty) {
                unset($change->qty);
            }

            // unset
            unset($change->category);
            
            $change->save();

            $this->afterMainUpdate($change, $request);

            DB::commit();

            return response()->json([
                'status' => true,
                'alert' => 'success',
                'message' => 'Data Berhasil Diperbarui',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    protected function show($id)
    {
        $singleData = $this->defaultDataQuery()->where('inventory_consumable_movements.id', $id)->first();
        unset($singleData['id']);
        unset($singleData['item_id']);

        $data['detail'] = $singleData;

        return view('easyadmin::backend.idev.show-default', $data);
    }
}
