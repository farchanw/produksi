<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\ProductComposition;

class DashboardSettingController extends Controller
{
    private $title;

    private $generalUri;

    public function __construct()
    {
        $this->title = 'Dashboard Setting';
        $this->generalUri = 'dashboard-setting';
    }

    public function index()
    {
        session(['module' => 'setting']);
        // $materials = Material::get();

        // $arrDatas = [];
        // foreach ($materials as $key => $material) {
        //     $composition = ProductComposition::where('material_id', $material->id)->first();
        //     $arrDatas[] = [
        //         'material_name' => $material->nama_barang,
        //         'price' => $composition->price_per_unit
        //     ];
        // }

        $data['title'] = $this->title;

        return view('backend.idev.extend.dashboard.dashboard_setting', $data);
    }
}
