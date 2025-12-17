<?php

namespace App\Helpers;

use Idev\EasyAdmin\app\Helpers\Constant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class Sidebar
{
    
    public function generate()
  {
    $menus = $this->menus();
    $arrMenu = [];

    foreach ($menus as $menu) {

      // 🔴 INI KUNCINYA
      if (empty($menu['visibility'])) {
        continue;
      }

      $menu['url'] = Route::has($menu['key'] . '.index')
        ? route($menu['key'] . '.index')
        : '/idev-admin';

      $menu['base_key'] = $menu['key'];
      $menu['key'] = $menu['key'] . '.index';

      $arrMenu[] = $menu;
    }

    return $arrMenu;
  }
    
    
    public function menus()
    {
        $role = "admin";
        $currentModule = session()->get('module');
        
        if (config('idev.enable_role', true)) {
            $role = Auth::user()->role->name;
        }
        
        $generalMenu = [
            [
                'name' => 'Back Home',
                'icon' => 'ti ti-arrow-left',
                'key' => 'idev-admin',
                'base_key' => 'idev-admin',
                'visibility' => true,
                'ajax_load' => false,
                'childrens' => []
            ],
            [
                'name' => 'Dashboard',
                'icon' => 'ti ti-dashboard',
                'key' => 'dashboard-' . $currentModule,
                'base_key' => 'dashboard-' . $currentModule,
                'visibility' => true,
                'ajax_load' => false,
                'childrens' => []
            ],
        ];
        
        return array_merge(
            $generalMenu,
            $this->menuInventoryConsumable($role, $currentModule),
            $this->menuUtilisationProduction($role, $currentModule),
            $this->menuSetting($role, $currentModule),
            
        );
    }
    
    
    private function menuInventoryConsumable($role, $module)
    {
        $currentModule = 'inventory-consumable';
        
        return [
            [
                'name' => 'Item',
                'icon' => 'ti ti-menu',
                'key' => 'inventory-consumable',
                'base_key' => 'inventory-consumable',
                'visibility' => $this->canAccessMenu('inventory-consumable') && $module == $currentModule,
                'ajax_load' => false,
                'childrens' => []
            ],
            [
                'name' => 'Kartu Stock',
                'icon' => 'ti ti-menu',
                'key' => 'inventory-consumable-movement',
                'base_key' => 'inventory-consumable-movement',
                'visibility' => $this->canAccessMenu('inventory-consumable-movement') && $module == $currentModule,
                'ajax_load' => false,
                'childrens' => []
            ],
            [
                'name' => 'Input Kategori',
                'icon' => 'ti ti-menu',
                'key' => 'inventory-consumable-category',
                'base_key' => 'inventory-consumable-category',
                'visibility' => $this->canAccessMenu('inventory-consumable-category') && $module == $currentModule,
                'ajax_load' => false,
                'childrens' => []
            ],
        ];
    }



    private function menuUtilisationProduction($role, $module)
    {
        $currentModule = 'utilisation-production';
        
        return [
            [
                'name' => 'Utilisasi Produksi',
                'icon' => 'ti ti-menu',
                'key' => 'utilisation-production',
                'base_key' => 'utilisation-production',
                'visibility' => $this->canAccessMenu('utilisation-production') && $module == $currentModule,
                'ajax_load' => false,
                'childrens' => []
            ],
        ];
    }
    
    
    
    private function menuSetting($role, $module)
    {
        $currentModule = 'setting';
        
        return [
            [
                'name' => 'Role',
                'icon' => 'ti ti-key',
                'key' => 'role',
                'base_key' => 'role',
                'visibility' => $this->canAccessMenu('role') && $module == $currentModule,
                'ajax_load' => false,
                'childrens' => []
            ],
            [
                'name' => 'User',
                'icon' => 'ti ti-users',
                'key' => 'user',
                'base_key' => 'user',
                'visibility' => $this->canAccessMenu('user') && $module == $currentModule,
                'ajax_load' => false,
                'childrens' => []
            ],
        ];
    }
    
    public function defaultAllAccess($exclude = [])
    {
        return ['list', 'create', 'show', 'edit', 'delete', 'import-excel-default', 'export-excel-default', 'export-pdf-default'];
    }
    
    
    public function accessCustomize($menuKey)
    {
        $arrMenu = [
            'dashboard' => ['list'],
        ];
        
        return $arrMenu[$menuKey] ?? $this->defaultAllAccess();
    }

    
  private function canAccessMenu($menuKey)
  {
    $permission = (new Constant())->permissions();

    if (!isset($permission['list_access'])) {
      return false;
    }

    // DEBUG (boleh aktifkan sementara)
    // dd($menuKey, $permission['list_access']);

    // cek minimal boleh list/index
    return in_array($menuKey . '.index', $permission['list_access']);
  }

}
