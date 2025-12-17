<?php

namespace App\Helpers\Modules;
use App\Models\InventoryConsumable;
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

    public static function optionsForItems()
    {
        return InventoryConsumable::select('id as value', DB::raw('CONCAT_WS(" - ", sku, name) as text'))
            ->orderBy('name', 'ASC')
            ->get()
            ->toArray();
    }
}