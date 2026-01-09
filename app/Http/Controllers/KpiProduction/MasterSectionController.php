<?php

namespace App\Http\Controllers\KpiProduction;

use App\Models\MasterSection;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class MasterSectionController extends DefaultController
{
    protected $modelClass = MasterSection::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Master Section';
        $this->generalUri = 'master-section';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Nama', 'column' => 'nama', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['nama'],
            'headers' => [
                    ['name' => 'Nama', 'column' => 'nama'], 
            ]
        ];
    }


    protected function fields($mode = "create", $id = '-')
    {
        $edit = null;
        if ($id != '-') {
            $edit = $this->modelClass::where('id', $id)->first();
        }

        $fields = [
                    [
                        'type' => 'text',
                        'label' => 'Nama',
                        'name' =>  'nama',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('nama', $id),
                        'value' => (isset($edit)) ? $edit->nama : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'nama' => 'required|string',
        ];

        return $rules;
    }

}
