<?php

namespace App\Http\Controllers\UtilisationProduction;

use App\Http\Controllers\Controller;

class DashboardUtilisationProductionController extends Controller
{
    private $title;

    private $generalUri;

    public function __construct()
    {
        $this->title = 'Dashboard Sistem Utilisasi Produksi';
        $this->generalUri = 'dashboard-utilisation-production';
    }

    public function index()
    {
        session(['module' => 'utilisation-production']);
        $data['title'] = $this->title;

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'easyadmin::backend.idev.dashboard_ajax' : 'backend.idev.extend.dashboard.dashboard_utilisation_production';

        return view($layout, $data);
    }
}
