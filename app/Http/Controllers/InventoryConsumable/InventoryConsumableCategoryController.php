<?php

namespace App\Http\Controllers\InventoryConsumable;

use App\Models\InventoryConsumableCategory;
use Illuminate\Http\Request;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

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
        $this->title = 'Inventory Consumable Category';
        $this->generalUri = 'inventory-consumable-category';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Name', 'column' => 'name', 'order' => true],
                    ['name' => 'Parent', 'column' => 'parent', 'order' => true], 
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

        array_unshift($optionsParent, ['value' => '', 'text' => '-- No Parent --']);

        $fields = [
                    [
                        'type' => 'text',
                        'label' => 'Name',
                        'name' =>  'name',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('name', $id),
                        'value' => (isset($edit)) ? $edit->name : ''
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Parent',
                        'name' =>  'parent_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('parent_id', $id),
                        'value' => (isset($edit)) ? $edit->parent_id : '',
                        'options' => $optionsParent,
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
                'inventory_consumable_categories.name',
                'inventory_consumable_categories.created_at',
                'inventory_consumable_categories.updated_at',
                'parent.name as parent'
        );

        return $dataQueries;
    }


}
