<?php

namespace App\Http\Controllers\KpiProduction;

use App\Http\Controllers\Controller;

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

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.dashboard_ajax' : 'backend.idev.extend.dashboard.dashboard_kpi_production';

        return view($layout, $data);
    }
}
