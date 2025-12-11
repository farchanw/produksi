<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    private $title;
    private $generalUri;

    public function __construct()
    {
        $this->title = 'Welcome';
        $this->generalUri = 'welcome';
    }


    public function index()
    {
        $modules = [
            [
                'title' => 'Inventory Consumable',
                'link' => url('inventory-consumable/dashboard-inventory-consumable'),
                'active' => true,
                'icon' => asset('module-icons/inventory.png')
            ],
            [
                'title' => 'Setting',
                'link' => url('setting/dashboard-setting'),
                'active' => true,
                'icon' => asset('module-icons/settings.png')
            ],
        ];

        $data['title'] = $this->title;
        $data['modules'] = $modules;

        return view('frontend.welcome', $data);
    }

}