<?php

namespace App\Http\Controllers\InventoryConsumable;

use App\Models\InventoryConsumableStock;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class InventoryConsumableStockController extends DefaultController
{
    protected $modelClass = InventoryConsumableStock::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Inventory Consumable Stock';
        $this->generalUri = 'inventory-consumable-stock';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Item id', 'column' => 'item_id', 'order' => true],
                    ['name' => 'Stock', 'column' => 'stock', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['item_id'],
            'headers' => [
                    ['name' => 'Item id', 'column' => 'item_id'],
                    ['name' => 'Stock', 'column' => 'stock'], 
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
                        'label' => 'Item id',
                        'name' =>  'item_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('item_id', $id),
                        'value' => (isset($edit)) ? $edit->item_id : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Stock',
                        'name' =>  'stock',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('stock', $id),
                        'value' => (isset($edit)) ? $edit->stock : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'item_id' => 'required|string',
                    'stock' => 'required|string',
        ];

        return $rules;
    }

}
