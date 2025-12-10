<?php

namespace App\Http\Controllers;

use App\Models\InventoryConsumable;
use App\Models\InventoryConsumableMovement;
use Idev\EasyAdmin\app\Models\Role;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private $title;
    private $generalUri;

    public function __construct()
    {
        $this->title = 'Dashboard';
        $this->generalUri = 'dashboard';
    }


    public function index()
    {
        $data['title'] = $this->title;

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.dashboard_ajax' : 'backend.dashboard';

        $data['import_scripts'] = [
             ['source' => asset('vendor/Chart.js/chart.umd.js')],
        ];

        // InventoryConsumables Chart
        $data['dataInventoryConsumablesChartItems'] = InventoryConsumable::all();
        $data['dataInventoryConsumablesChartYears'] = InventoryConsumableMovement::selectRaw('YEAR(movement_datetime) year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view($layout, $data);
    }

}