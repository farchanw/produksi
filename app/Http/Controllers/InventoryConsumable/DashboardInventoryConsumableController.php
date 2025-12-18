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
    protected $importScripts = [];
    protected $importStyles = [];

    public function __construct()
    {
        $this->title = 'Dashboard Sistem Kartu Stock';
        $this->generalUri = 'dashboard-inventory-consumable';

        $this->importScripts = [
            ['source' => asset('js/modules/module-inventory-consumable.js')],
            ['source' => asset('vendor/Chart.js/chart.umd.js')],
            //['source' => asset('vendor/select2/js/select2.min.js')],
        ];
        $this->importStyles = [
            //['source' => asset('vendor/select2/css/select2.min.css')],
            //['source' => asset('vendor/select2/css/select2-bootstrap-5-theme.min.css')],
        ];
    }


    public function index()
    {
        session(['module' => 'inventory-consumable']);
        $data['title'] = $this->title;

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.dashboard_ajax' : 'backend.idev.extend.dashboard.dashboard_inventory_consumable';

        $data['import_scripts'] = $this->importScripts;
        $data['import_styles'] = $this->importStyles;

        // InventoryConsumables Chart
        $data['dataInventoryConsumablesChartItems'] = InventoryConsumable::limit(100)->get();
        $data['dataInventoryConsumablesStockCategories'] = InventoryConsumableCategory::select('id', 'name')->get();

        return view($layout, $data);
    }

}