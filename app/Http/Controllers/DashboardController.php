<?php

namespace App\Http\Controllers;

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
        return redirect(route('login'));
    }
}
