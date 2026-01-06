<?php

namespace App\Http\Controllers\InventoryConsumable;

use App\Models\InventoryConsumableMovement;
use App\Models\InventoryConsumable;
use App\Models\InventoryConsumableCategory;
use App\Models\InventoryConsumableSubcategory;
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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

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
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Nama Barang', 'column' => 'item', 'order' => true],
                    ['name' => 'Kategori', 'column' => 'category', 'order' => true],
                    ['name' => 'Subkategori', 'column' => 'subcategory', 'order' => true],
                    ['name' => 'Type', 'column' => 'type', 'order' => true, 'formatting' => 'toInventoryInOutBadge'],
                    ['name' => 'Qty', 'column' => 'qty', 'order' => true],
                    ['name' => 'Awal', 'column' => 'stock_awal', 'order' => true],
                    ['name' => 'Akhir', 'column' => 'stock_akhir', 'order' => true],
                    ['name' => 'Harga Satuan', 'column' => 'harga_satuan', 'order' => true, 'formatting' => 'toRupiah'],
                    ['name' => 'Harga Total', 'column' => 'harga_total', 'order' => true, 'formatting' => 'toRupiah'],
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


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::join('inventory_consumables', 'inventory_consumable_movements.item_id', '=', 'inventory_consumables.id')
            ->where('inventory_consumable_movements.id', $id)->first();
        }

        $optionsItem = InventoryConsumable::select('id as value', DB::raw('CONCAT_WS(" - ", sku, name) as text'))
            ->orderBy('name', 'ASC')
            ->get()
            ->toArray();
        array_unshift($optionsItem, ['value' => '', 'text' => 'Pilih Item']);

        $optionsCategory = InventoryConsumableHelper::optionsForCategories()->toArray();
        $optionsSubcategory = [];
        
        if (isset($edit->item_id)) {
            $edit->subcategory_id = DB::table('inventory_consumable_movement_subcategory')
                ->where('movement_id', $edit->id)
                ->pluck('subcategory_id');
            $optionsSubcategory = InventoryConsumableHelper::getItemSubcategories($edit->item_id)->toArray();
        }

        if ($mode == 'create') {
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
                        'label' => 'Nama Item',
                        'name' =>  'item_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('item_id', $id),
                        'value' => (isset($edit)) ? $edit->item_id : '',
                        'options' => $optionsItem
                    ],
                    [
                        'type' => 'checklist_searchable',
                        'label' => 'Subkategori',
                        'name' =>  'subcategory_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('subcategory_id', $id),
                        'value' => isset($edit) ? $edit->subcategory_id : '',
                        'options' => $optionsSubcategory,
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
                        'type' => 'number',
                        'label' => 'Harga Satuan',
                        'name' =>  'harga_satuan',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('harga_satuan', $id),
                        'value' => (isset($edit)) ? $edit->harga_satuan : ''
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
        }

        if ($mode == 'edit') {
            $fields = [
                    [
                        'type' => 'hidden',
                        'label' => 'Kategori',
                        'name' =>  'category_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('category_id', $id),
                        'value' => isset($edit) ? $edit->category_id : '',
                        'options' => $optionsCategory
                    ],
                    [
                        'type' => 'hidden',
                        'label' => 'Nama Item',
                        'name' =>  'item_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('item_id', $id),
                        'value' => (isset($edit)) ? $edit->item_id : '',
                        'options' => $optionsItem
                    ],
                    [
                        'type' => 'hidden',
                        'label' => 'Type',
                        'name' =>  'type',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('type', $id),
                        'value' => (isset($edit)) ? $edit->type : '',
                        'options' => InventoryConsumableHelper::optionsForMovementTypes(),
                        'filter' => true,
                    ],
                    [
                        'type' => 'hidden',
                        'label' => 'Qty',
                        'name' =>  'qty',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('qty', $id),
                        'value' => (isset($edit)) ? $edit->qty : ''
                    ],
                    [
                        'type' => 'number',
                        'label' => 'Harga Satuan',
                        'name' =>  'harga_satuan',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('harga_satuan', $id),
                        'value' => (isset($edit)) ? $edit->harga_satuan : ''
                    ],
                    [
                        'type' => 'hidden',
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
        }
        
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
                    'harga_satuan' => 'required|integer',
        ];

        return $rules;
    }

    protected function defaultDataQuery()
    {
        $filters = [];
        $orThose = null;
        $orderBy = 'inventory_consumable_movements.id';
        $orderState = 'DESC';

        // Search keyword
        if (request('search')) {
            $orThose = request('search');
        }

        // Ordering
        if (request('order')) {
            $orderBy = request('order');
            $orderState = request('order_state');
        }

        // Filters
        if (request('type')) {
            $filters[] = ['inventory_consumable_movements.type', '=', request('type')];
        }

        if (request('category_id')) {
            $filters[] = ['inventory_consumable_categories.id', '=', request('category_id')];
        }

        if (request('subcategory_id')) {
            $filters[] = ['subcategories.id', '=', request('subcategory_id')];
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
            ->leftJoin('inventory_consumable_movement_subcategory as pivot', 'inventory_consumable_movements.id', '=', 'pivot.movement_id')
            ->leftJoin('inventory_consumable_subcategories as subcategories', 'pivot.subcategory_id', '=', 'subcategories.id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $efc = ['#', 'created_at', 'updated_at', 'id', 'item', 'category', 'subcategory'];

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
                $query->orWhere('subcategories.name', 'LIKE', '%' . $orThose . '%');
            })
            ->orderBy($orderBy, $orderState)
            ->groupBy('inventory_consumable_movements.id') // Group because of multiple subcategories
            ->select(
                'inventory_consumable_movements.*',
                'inventory_consumables.name as item',
                'inventory_consumable_categories.name AS category',
                DB::raw('GROUP_CONCAT(subcategories.name SEPARATOR ", ") as subcategory'),

                'inventory_consumable_categories.id as category_id',
                'inventory_consumables.satuan',
            );

        return $dataQueries;
    }




    public function index()
    {
        $baseUrlExcel = route($this->generalUri.'.export-excel-default');
        $baseUrlPdf = route($this->generalUri.'.export-pdf-default');
        $baseUrlLaporanBulananPdf = route($this->generalUri.'.export-laporan-bulanan-pdf-default');

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

        $customActions = [
            [
                'key' => 'export-laporan-bulanan-pdf-default',
                'name' => 'Export Laporan Bulanan',
                'html_button' => '<button
                    type="button"
                    id="export-laporan-bulanan"
                    class="btn btn-sm btn-primary radius-6"
                    data-bs-toggle="modal"
                    data-bs-target="#modalExportLaporan"
                    data-base-url="'.$baseUrlLaporanBulananPdf.'"
                    title="Cetak Laporan Bulanan"
                    >
                    <i class="ti ti-file-report"></i>
                    </button>
                '
            ],
        ];

        $permissions =  $this->arrPermissions;
        if ($this->dynamicPermission) {
            $permissions = (new Constant())->permissionByMenu($this->generalUri);
        }
        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.list_drawer_ajax' : 'backend.idev.list_drawer_inventory_consumable_movement';
        if(isset($this->drawerLayout)){
            $layout = $this->drawerLayout;
        }


        $data['permissions'] = $permissions;
        $data['more_actions'] = $moreActions;
        $data['custom_actions'] = $customActions;
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
            return $beforeInsertResponse;
        }

        DB::beginTransaction();

        try {
            $appendStore = $this->appendStore($request);
            if (array_key_exists('error', $appendStore)) {
                return response()->json($appendStore['error'], 200);
            }

            // GET STOCK
            $currentStock = (int) InventoryConsumableStock::where('item_id', $request->item_id)
                ->value('stock') ?? 0;

            $inputQty       = (int) $request->qty;
            $hargaSatuan    = (int) ($request->harga_satuan ?? 0);
            $type           = $request->type;

            // SET QTY & STOCK
            if ($type === 'in') {
                $qty        = abs($inputQty);
                $stockAkhir = $currentStock + $qty;
                $hargaTotal = $qty * $hargaSatuan;

            } elseif ($type === 'out') {
                $qty        = -abs($inputQty);
                $stockAkhir = $currentStock + $qty;

                if ($stockAkhir < 0) {
                    throw new Exception('Stock tidak mencukupi');
                }

                $hargaTotal = 0;

            } elseif ($type === 'adjust') {
                $stockAkhir = $inputQty;

                if ($stockAkhir < 0) {
                    throw new Exception('Stock tidak valid');
                }

                $qty        = $stockAkhir - $currentStock;
                $hargaTotal = 0;

            } else {
                throw new Exception('Kolom type tidak valid');
            }

            $insert = new $this->modelClass();

            foreach ($this->fields('create') as $th) {
                if ($request->filled($th['name'])) {
                    $insert->{$th['name']} = $request->{$th['name']};
                }
            }

            if (array_key_exists('columns', $appendStore)) {
                foreach ($appendStore['columns'] as $as) {
                    $insert->{$as['name']} = $as['value'];
                }
            }

            $insert->qty               = $qty;
            $insert->stock_awal        = $currentStock;
            $insert->stock_akhir       = $stockAkhir;
            $insert->harga_satuan      = $hargaSatuan;
            $insert->harga_total       = $hargaTotal;
            $insert->movement_datetime = $request->movement_datetime ?? now();

            $dataSubcategory = $request->subcategory_id ?? [];
            unset($insert->subcategory_id, $insert->category_id);

            $insert->save();

            // SET STOCK
            InventoryConsumableStock::updateOrCreate(
                ['item_id' => $insert->item_id],
                ['stock' => $stockAkhir]
            );

            // SET SUBCATEGORY
            if (!empty($dataSubcategory)) {
                $insert->subcategories()->sync($dataSubcategory);
            }

            $this->afterMainInsert($insert, $request);

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
            return $beforeUpdateResponse;
        }

        DB::beginTransaction();




        try {
            $appendUpdate = $this->appendUpdate($request);

            $change = $this->modelClass::lockForUpdate()->findOrFail($id);

            if ($request->filled('harga_satuan')) {
                $change->harga_satuan = (int) $request->harga_satuan;

                if ($change->type === 'in') {
                    $change->harga_total = abs($change->qty) * $change->harga_satuan;
                }
            }

            if ($request->filled('notes')) {
                $change->notes = $request->notes;
            }

            if (array_key_exists('columns', $appendUpdate)) {
                foreach ($appendUpdate['columns'] as $as) {
                    $change->{$as['name']} = $as['value'];
                }
            }


            if (Carbon::parse($change->created_at)->isBefore(now()->subHours(24))) {
                throw new Exception('Tidak dapat mengubah transaksi setelah 24 jam');
            }


            if ($change->stock_akhir != InventoryConsumableStock::where('item_id', $change->item_id)->value('stock')) {
                throw new Exception('Tidak dapat mengubah transaksi lama');
            }

            /** -------------------------------
             * PROTECT : TIDAK BOLEH DIUBAH
             * ------------------------------ */
            unset(
                //$change->item_id,
                $change->qty,
                //$change->type,
                //$change->movement_datetime,
                $change->stock_awal,
                $change->stock_akhir,
                $change->category_id,
                $change->subcategory_id
            );

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


    protected function exportLaporanBulananPdf(Request $request)
    {
        $rawMonthYear = Carbon::parse($request->input('month_year', now()->format('Y-m')));

        $month = $rawMonthYear->month;
        $year  = $rawMonthYear->year;
        $data['monthName'] = Str::upper(Carbon::createFromFormat('m', $month)->locale('id')->translatedFormat('M'));
        $data['year'] = $year;
        $data['records'] = $this->defaultDataQuery()
            ->where('inventory_consumable_movements.type', 'in')
            ->whereYear('inventory_consumable_movements.movement_datetime', $year)
            ->whereMonth('inventory_consumable_movements.movement_datetime', $month)
            ->select(
                'inventory_consumable_categories.id as category_id',
                'inventory_consumable_categories.name as category',
                'inventory_consumables.name as item',
                'inventory_consumables.satuan',

                DB::raw('SUM(inventory_consumable_movements.qty) as qty'),
                DB::raw('SUM(inventory_consumable_movements.harga_total) as price')
            )
            ->groupBy(
                'inventory_consumable_categories.id',
                'inventory_consumable_categories.name',
                'inventory_consumables.name'
            )
            ->get()
            ->groupBy('category_id')
            ->map(function ($categoryRows) {

                return [
                    'name' => $categoryRows->first()->category,
                    'items' => $categoryRows->map(function ($row) {
                        return [
                            'name'      => $row->item,
                            'satuan'    => $row->satuan,
                            'qty'       => (int) $row->qty,
                            'price'     => (int) $row->price,
                        ];
                    })->values(),
                ];

            })->values();

        $pdf = Pdf::loadView('pdf.inventory_consumable.laporan_bulanan_inventaris', $data);
        $pdf->setPaper('A4');

        $fileName = 'laporan-bulanan-inventaris-' . Carbon::now()->format('YmdHis') . '.pdf';

        return $pdf->stream($fileName);
    }
}
