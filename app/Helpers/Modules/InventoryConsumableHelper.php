<?php

namespace App\Helpers\Modules;
use App\Models\InventoryConsumable;
use App\Models\InventoryConsumableCategory;
use App\Models\InventoryConsumableSubcategory;
use Illuminate\Support\Facades\DB;

class InventoryConsumableHelper
{
    public static function moduleName()
    {
        return 'inventory-consumable';
    }

    public static function optionsForMovementTypes()
    {
        return [
            ['value' => 'in', 'text' => 'In'],
            ['value' => 'out', 'text' => 'Out'],
            ['value' => 'adjust', 'text' => 'Adjust'],
        ];
    }

    public static function optionsForCategories()
    {
        return InventoryConsumableCategory::select('id as value', 'name as text')
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function optionsForSubcategories()
    {
        return InventoryConsumableSubcategory::select('id as value', 'name as text')
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function optionsForItems()
    {
        return InventoryConsumable::select(
                'id as value', 
                //DB::raw('CONCAT_WS(" - ", sku, name) as text')
                'name as text'
            )
            ->orderBy('name', 'ASC')
            ->get()
            ->toArray();
    }

    public static function getItemSubcategories($id = null)
    {
        $query = InventoryConsumable::leftJoin('inventory_consumable_item_subcategory as pivot', 'inventory_consumables.id', '=', 'pivot.item_id')
            ->leftJoin('inventory_consumable_subcategories as subcategories', 'pivot.subcategory_id', '=', 'subcategories.id')
            ;

        if ($id) {
            $query->where('inventory_consumables.id', $id);
        }

        $query->select('subcategory_id as value', 'subcategories.name as text');

        return $query->get();
    }
}