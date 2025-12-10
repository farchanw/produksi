<?php

namespace App\Http\Controllers;

use App\Models\InventoryConsumableMovement;
use App\Models\InventoryConsumable;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Idev\EasyAdmin\app\Helpers\Validation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class InventoryConsumableMovementController extends DefaultController
{
    protected $modelClass = InventoryConsumableMovement::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Inventory Consumable Movement';
        $this->generalUri = 'inventory-consumable-movement';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Item', 'column' => 'item', 'order' => true],
                    ['name' => 'Qty', 'column' => 'qty', 'order' => true],
                    ['name' => 'Type', 'column' => 'type', 'order' => true],
                    ['name' => 'Movement datetime', 'column' => 'movement_datetime', 'order' => true],
                    ['name' => 'Notes', 'column' => 'notes', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['qty'],
            'headers' => [
                    ['name' => 'Item', 'column' => 'item'],
                    ['name' => 'Qty', 'column' => 'qty'],
                    ['name' => 'Type', 'column' => 'type'],
                    ['name' => 'Movement datetime', 'column' => 'movement_datetime'],
                    ['name' => 'Notes', 'column' => 'notes'], 
            ]
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $optionsItem = InventoryConsumable::select('id as value', DB::raw('CONCAT_WS(" - ", sku, category, subcategory,  name) as text'))
            ->orderBy('name', 'ASC')
            ->get()
            ->toArray();
        $optionsType = [
            ['value' => 'in', 'text' => 'In'],
            ['value' => 'out', 'text' => 'Out'],
            ['value' => 'adjust', 'text' => 'Adjust'],
        ];

        $fields = [
                    [
                        'type' => 'select',
                        'label' => 'Item',
                        'name' =>  'item_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('item_id', $id),
                        'value' => (isset($edit)) ? $edit->item_id : '',
                        'options' => $optionsItem
                    ],
                    [
                        'type' => 'number',
                        'label' => 'Qty',
                        'name' =>  'qty',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('qty', $id),
                        'value' => (isset($edit)) ? $edit->qty : '',
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Type',
                        'name' =>  'type',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('type', $id),
                        'value' => (isset($edit)) ? $edit->type : '',
                        'options' => $optionsType
                    ],
                    [
                        'type' => 'datetime',
                        'label' => 'Movement datetime',
                        'name' =>  'movement_datetime',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('movement_datetime', $id),
                        'value' => (isset($edit)) ? $edit->movement_datetime : ''
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Notes',
                        'name' =>  'notes',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('notes', $id),
                        'value' => (isset($edit)) ? $edit->notes : ''
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

        $dataQueries = $this->modelClass::join('inventory_consumables', 'inventory_consumable_movements.item_id', '=', 'inventory_consumables.id')
            ->where($filters)
            ->where(function ($query) use ($orThose) {
                $efc = ['#', 'created_at', 'updated_at', 'id', 'item'];

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
            })
            ->orderBy($orderBy, $orderState)
            ->select('inventory_consumable_movements.*', 'inventory_consumables.name as item');

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

            $insert->save();

            $this->afterMainInsert($insert, $request);

            // Save stock
            if ($insert->type == 'out') {
                $insert->qty = -1 * abs((int) $insert->qty);
            } elseif ($insert->type == 'in') {
                $insert->qty = abs((int) $insert->qty);
            } elseif ($insert->type == 'adjust') {
                $insert->qty = (int) $insert->qty;
            } else {
                return response()->json([
                    'status' => false,
                    'alert' => 'danger',
                    'message' => 'Invalid value',
                ], 200);
            }


            $rowStock = DB::table('inventory_consumable_stocks')
                ->where('item_id', $insert->item_id)
                ->first();

            $currentStock = $rowStock ? (int) $rowStock->stock : 0;
            $newStock = $currentStock; // default

            if ($rowStock) {

                if ($insert->type == 'adjust') {
                    // Adjusting means the new stock is exactly qty
                    $newStock = (int) $insert->qty;

                } else {
                    // Increment/decrement operation
                    $newStock = $currentStock + (int) $insert->qty;
                }

                // ❗ Prevent negative stock
                if ($newStock < 0) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'danger',
                        'message' => 'Invalid value',
                    ], 200);
                }

                DB::table('inventory_consumable_stocks')
                    ->where('item_id', $insert->item_id)
                    ->update(['stock' => $newStock]);

            } else {

                // When no existing stock exists, also ensure not negative
                if ((int) $insert->qty < 0) {
                    return response()->json([
                        'status' => false,
                        'alert' => 'danger',
                        'message' => 'Invalid value',
                    ], 200);
                }

                DB::table('inventory_consumable_stocks')->insert([
                    'item_id' => $insert->item_id,
                    'stock' => (int) $insert->qty,
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

            // Jangan update qty
            if ($change->qty) {
                unset($change->qty);
            }
            
            $change->save();

            $this->afterMainUpdate($change, $request);

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

}
