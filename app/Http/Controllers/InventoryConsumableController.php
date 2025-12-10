<?php

namespace App\Http\Controllers;

use App\Models\InventoryConsumable;
use App\Models\InventoryConsumableStock;
use App\Models\InventoryConsumableMovement;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Idev\EasyAdmin\app\Helpers\Validation;
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
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Inventory Consumable';
        $this->generalUri = 'inventory-consumable';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Sku', 'column' => 'sku', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true], 
                    ['name' => 'Stock', 'column' => 'stock', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['sku'],
            'headers' => [
                    ['name' => 'Sku', 'column' => 'sku'],
                    ['name' => 'Name', 'column' => 'name'], 
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
                        'label' => 'Sku',
                        'name' =>  'sku',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('sku', $id),
                        'value' => (isset($edit)) ? $edit->sku : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Name',
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
                    'sku' => 'required|string',
                    'name' => 'required|string',
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
            ->where($filters)
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
            ->orderBy($orderBy, $orderState)
            ->select('inventory_consumables.*', 'inventory_consumable_stocks.stock');

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

            $stockInsert = new InventoryConsumableStock();
            $stockInsert->stock = 0;
            $stockInsert->item_id = $insert->id;
            $stockInsert->save();

            $insert->save();

            $this->afterMainInsert($insert, $request);

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


}
