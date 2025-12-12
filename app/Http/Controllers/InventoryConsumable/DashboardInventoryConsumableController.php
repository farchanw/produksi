<?php

namespace App\Http\Controllers\InventoryConsumable;

use App\Models\InventoryConsumable;
use App\Models\InventoryConsumableMovement;
use App\Http\Controllers\Controller;
use App\Models\InventoryConsumableCategory;
use Idev\EasyAdmin\app\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardInventoryConsumableController extends Controller
{
    private $title;
    private $generalUri;

    public function __construct()
    {
        $this->title = 'Dashboard Sistem Kartu Stok';
        $this->generalUri = 'dashboard-inventory-consumable';
    }


    public function index()
    {
        session(['module' => 'inventory-consumable']);
        $data['title'] = $this->title;

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.dashboard_ajax' : 'backend.idev.dashboard_inventory_consumable';

        $data['import_scripts'] = [
             ['source' => asset('vendor/Chart.js/chart.umd.js')],
        ];

        // InventoryConsumables Chart
        $data['dataInventoryConsumablesChartItems'] = InventoryConsumable::all();
        $data['dataInventoryConsumablesChartYears'] = InventoryConsumableMovement::selectRaw('YEAR(movement_datetime) year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');
        $data['dataInventoryConsumablesStockYears'] = InventoryConsumableMovement::selectRaw('YEAR(movement_datetime) year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');
        $data['dataInventoryConsumablesStockCategories'] = InventoryConsumableCategory::where('parent_id', null)->get();

        return view($layout, $data);
    }

}