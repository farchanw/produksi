<?php

namespace App\Helpers\Modules;
use App\Models\InventoryConsumable;
use App\Models\InventoryConsumableCategory;
use App\Models\InventoryConsumableSubcategory;
use App\Models\InventoryConsumableKind;
use App\Models\InventoryConsumableMovement;
use Illuminate\Support\Facades\DB;

class InventoryConsumableHelper
{
    public static function moduleName()
    {
        return 'inventory-consumable';
    }

    public static function optionsForMovementTypes($withPlaceholder = false)
    {
        $options = [
            ['value' => 'in', 'text' => 'In'],
            ['value' => 'out', 'text' => 'Out'],
            ['value' => 'adjust', 'text' => 'Adjust'],
        ];

        if ($withPlaceholder) {
            array_unshift($options, ['value' => '', 'text' => 'Pilih...']);
        }

        return $options;
    }

    public static function optionsForCategories($withPlaceholder = false)
    {
        $options = InventoryConsumableCategory::select('id as value', 'name as text')
            ->orderBy('name', 'ASC')
            ->get()
            ->toArray();

        if ($withPlaceholder) {
            array_unshift($options, ['value' => '', 'text' => 'Semua']);
        }

        return $options;
    }

    public static function optionsForSubcategories()
    {
        return InventoryConsumableSubcategory::select('id as value', 'name as text')
            ->orderBy('name', 'ASC')
            ->get();
    }

    public static function optionsForItems($withPlaceholder = false)
    {
        $options = InventoryConsumableHelper::getItemsByCategory()->toArray();
    
        if ($withPlaceholder) {
            array_unshift($options, ['value' => '', 'text' => 'Pilih Barang...']);
        }

        return $options;
    }

    public static function optionsForKinds()
    {
        return InventoryConsumableKind::select('id as value', 'name as text')
            ->orderBy('name', 'ASC')
            ->get()
            ->toArray();
    }

    public static function getItemsByCategory($categoryId = null)
    {
        $items = InventoryConsumable::select(
            'id as value',
            'name as text',
            'satuan as data_satuan',
            DB::raw('LPAD(sku, 4, "0") as data_sku'),
            /*
            DB::raw("
                JSON_OBJECT(
                    'sku', LPAD(sku, 4, '0'),
                    'name', name,
                    'satuan', satuan
                ) AS text
            ")
            */
        );

        if ($categoryId) {
            $items->where('category_id', $categoryId);
        }

        $items->orderBy('name', 'ASC');

        return $items->get();
    }

    public static function getItemSubcategories($id = null)
    {
        $query = InventoryConsumable::leftJoin(
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
            );

        if ($id) {
            $query->where('inventory_consumables.id', $id);
        }

        return $query
            ->select(
                'pivot.subcategory_id as value',
                'subcategories.name as text'
            )
            ->whereNotNull('pivot.subcategory_id')
            ->whereNotNull('subcategories.name')
            ->get();
    }


    public static function getDataExportLaporanBulanan($query, $year, $month)
    {
        return $query
            ->where('inventory_consumable_movements.type', 'in')
            ->whereYear('inventory_consumable_movements.movement_datetime', $year)
            ->whereMonth('inventory_consumable_movements.movement_datetime', $month)
            ->select(
                'inventory_consumable_kinds.name as kind',
                'inventory_consumable_kinds.id as kind_id',
                'inventory_consumable_categories.id as category_id',
                'inventory_consumable_categories.name as category',
                'inventory_consumables.id as item_id',
                'inventory_consumables.name as item',
                'inventory_consumables.satuan',
                DB::raw('SUM(inventory_consumable_movements.qty) as qty'),
                DB::raw('SUM(inventory_consumable_movements.harga_total) as price')
            )
            ->groupBy(
                'inventory_consumable_kinds.name',
                'inventory_consumable_categories.id',
                'inventory_consumable_categories.name',
                'inventory_consumables.id',
                'inventory_consumables.name',
                'inventory_consumables.satuan'
            )
            ->orderBy('inventory_consumable_kinds.id', 'ASC')
            ->get()

            // GROUP BY KIND FIRST
            ->groupBy('kind_id')
            ->map(function ($kindRows) {

                $kindName = $kindRows->first()->kind;

                $categories = $kindRows
                    ->groupBy('category_id')
                    ->map(function ($categoryRows) {

                        $items = $categoryRows
                            ->groupBy(fn ($row) => $row->item . '|' . $row->satuan)
                            ->map(function ($rows) {
                                return [
                                    'name'   => $rows->first()->item,
                                    'satuan' => $rows->first()->satuan,
                                    'qty'    => $rows->sum('qty'),
                                    'price'  => $rows->sum('price'),
                                ];
                            })
                            ->values();

                        return [
                            'name'  => $categoryRows->first()->category,
                            'items' => $items,
                        ];
                    })
                    ->values();

                return [
                    'kind_id'    => $kindRows->first()->kind_id,
                    'kind'       => $kindName,
                    'categories' => $categories,
                ];
            })
            ->sortBy('kind_id')
            ->values();
    }

    public static function getMovementOutChartData($year, $itemId)
    {
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

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}