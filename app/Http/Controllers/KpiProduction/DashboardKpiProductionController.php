<?php

namespace App\Http\Controllers\KpiProduction;

use App\Http\Controllers\Controller;
use Idev\EasyAdmin\app\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardKpiProductionController extends Controller
{
    private $title;
    private $generalUri;

    public function __construct()
    {
        $this->title = 'Dashboard Sistem KPI Produksi';
        $this->generalUri = 'dashboard-kpi-production';
    }


    public function index()
    {
        session(['module' => 'kpi-production']);
        $data['title'] = $this->title;

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.dashboard_ajax' : 'backend.idev.dashboard_kpi_production';

        return view($layout, $data);
    }

}