<?php

namespace App\Http\Controllers\KpiProduction;

use App\Models\MasterSection;
use App\Models\MasterSubsection;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class MasterSubsectionController extends DefaultController
{
    protected $modelClass = MasterSubsection::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Master Subsection';
        $this->generalUri = 'master-subsection';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Bagian', 'column' => 'master_section_id', 'order' => true],
                    ['name' => 'Nama Subbagian', 'column' => 'nama', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['master_section_id'],
            'headers' => [
                    ['name' => 'Master section id', 'column' => 'master_section_id'],
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
                        'type' => 'select',
                        'label' => 'Bagian',
                        'name' =>  'master_section_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('master_section_id', $id),
                        'value' => (isset($edit)) ? $edit->master_section_id : '',
                        'options' => MasterSection::select('id as value', 'nama as text')->orderBy('nama', 'ASC')->get()->toArray(),
                    ],
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
                    'master_section_id' => 'required|string',
                    'nama' => 'required|string',
        ];

        return $rules;
    }

}
