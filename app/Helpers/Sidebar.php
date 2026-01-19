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
        : '/home-admin';

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
                'key' => 'home-admin',
                'base_key' => 'home-admin',
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
            $this->menuKpiProduction($role, $currentModule),
            
        );
    }
    
    
    private function menuInventoryConsumable($role, $module)
    {
        $currentModule = 'inventory-consumable';
        
        return [
            [
                'name' => 'Item',
                'icon' => 'ti ti-box',
                'key' => 'inventory-consumable',
                'base_key' => 'inventory-consumable',
                'visibility' => $this->canAccessMenu('inventory-consumable') && $module == $currentModule,
                'ajax_load' => false,
                'childrens' => []
            ],
            [
                'name' => 'Kartu Stock',
                'icon' => 'ti ti-book',
                'key' => 'inventory-consumable-movement',
                'base_key' => 'inventory-consumable-movement',
                'visibility' => $this->canAccessMenu('inventory-consumable-movement') && $module == $currentModule,
                'ajax_load' => false,
                'childrens' => []
            ],
            [
                'name' => 'Data Kategori',
                'icon' => 'ti ti-tag',
                'key' => '',
                'visibility' => $this->canAccessMenu('inventory-consumable-category') && $module == $currentModule,
                'ajax_load' => false,
                'childrens' => [
                    [
                        'name' => 'Jenis',
                        'icon' => 'ti ti-tag',
                        'key' => 'inventory-consumable-kind.index',
                        'base_key' => 'inventory-consumable-kind.index',
                        'visibility' => $this->canAccessMenu('inventory-consumable-kind') && $module == $currentModule,
                        'ajax_load' => false,
                        'childrens' => []
                    ],
                    [
                        'name' => 'Kategori',
                        'icon' => 'ti ti-tag',
                        'key' => 'inventory-consumable-category.index',
                        'base_key' => '',
                        'visibility' => $this->canAccessMenu('inventory-consumable-category') && $module == $currentModule,
                        'ajax_load' => false,
                        'childrens' => []
                    ],
                    [
                        'name' => 'Subkategori',
                        'icon' => 'ti ti-tag',
                        'key' => 'inventory-consumable-subcategory.index',
                        'base_key' => '',
                        'visibility' => $this->canAccessMenu('inventory-consumable-subcategory') && $module == $currentModule,
                        'ajax_load' => false,
                        'childrens' => []
                    ],
                ]
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



    private function menuKpiProduction($role, $module)
    {
        $currentModule = 'kpi-production';
        
        return [
            [
                'name' => 'Data Bagian',
                'icon' => 'ti ti-tag',
                'key' => '',
                'visibility' => $this->canAccessMenu('inventory-consumable-category') && $module == $currentModule,
                'ajax_load' => false,
                'childrens' => [
                    [
                        'name' => 'Bagian',
                        'icon' => 'ti ti-menu',
                        'key' => 'master-section.index',
                        'base_key' => 'master-section.index',
                        'visibility' => $this->canAccessMenu('master-section') && $module == $currentModule,
                        'ajax_load' => false,
                        'childrens' => []
                    ],
                    [
                        'name' => 'Subbagian',
                        'icon' => 'ti ti-menu',
                        'key' => 'master-subsection.index',
                        'base_key' => 'master-subsection.index',
                        'visibility' => $this->canAccessMenu('master-subsection') && $module == $currentModule,
                        'ajax_load' => false,
                        'childrens' => []
                    ],
                ]
            ],

            [
                'name' => 'KPI',
                'icon' => 'ti ti-menu',
                'key' => 'master-kpi',
                'base_key' => 'master-kpi',
                'visibility' => $this->canAccessMenu('master-kpi') && $module == $currentModule,
                'ajax_load' => false,
                'childrens' => []
            ],
            
            [
                'name' => 'Aspek KPI',
                'icon' => 'ti ti-menu',
                'key' => 'aspek-kpi-header',
                'base_key' => 'aspek-kpi-header',
                'visibility' => $this->canAccessMenu('aspek-kpi-header') && $module == $currentModule,
                'ajax_load' => false,
                'childrens' => []
            ],
            [
                'name' => 'Employee',
                'icon' => 'ti ti-menu',
                'key' => 'kpi-employee',
                'base_key' => 'kpi-employee',
                'visibility' => $this->canAccessMenu('kpi-employee') && $module == $currentModule,
                'ajax_load' => false,
                'childrens' => []
            ],
            [
                //'name' => 'Pencatatan',
                'name' => 'Penilaian',
                'icon' => 'ti ti-menu',
                'key' => 'kpi-evaluation',
                'base_key' => 'kpi-evaluation',
                'visibility' => $this->canAccessMenu('kpi-evaluation') && $module == $currentModule,
                'ajax_load' => false,
                'childrens' => [
                    [
                        'name' => 'Penilaian Personal',
                        'icon' => 'ti ti-menu',
                        'key' => '_',
                        'base_key' => '_',
                        'visibility' => $this->canAccessMenu('master-section') && $module == $currentModule,
                        'ajax_load' => false,
                        'childrens' => []
                    ],
                    [
                        'name' => 'Penilaian Divisi',
                        'icon' => 'ti ti-menu',
                        'key' => '_',
                        'base_key' => '_',
                        'visibility' => $this->canAccessMenu('master-subsection') && $module == $currentModule,
                        'ajax_load' => false,
                        'childrens' => []
                    ],
                ]
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
        $accessInventoryConsumableMovement = array_merge($this->defaultAllAccess(), ['export-laporan-bulanan-default']);



        $arrMenu = [
            'dashboard' => ['list'],

            'inventory-consumable-movement' => $accessInventoryConsumableMovement,
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
