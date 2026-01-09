<?php

namespace App\Http\Controllers\KpiProduction;

use App\Models\MasterKpi;
use App\Models\MasterSection;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;


class MasterKpiController extends DefaultController
{
    protected $modelClass = MasterKpi::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Master Kpi';
        $this->generalUri = 'master-kpi';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Nama', 'column' => 'nama', 'order' => true],
                    ['name' => 'Deskripsi', 'column' => 'deskripsi', 'order' => true],
                    ['name' => 'Bagian', 'column' => 'master_section_id', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['nama'],
            'headers' => [
                    ['name' => 'Nama', 'column' => 'nama'],
                    ['name' => 'Deskripsi', 'column' => 'deskripsi'],
                    ['name' => 'Bagian', 'column' => 'master_section_id'], 
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
                    [
                        'type' => 'textarea',
                        'label' => 'Deskripsi',
                        'name' =>  'deskripsi',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('deskripsi', $id),
                        'value' => (isset($edit)) ? $edit->deskripsi : ''
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Bagian',
                        'name' =>  'master_section_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('master_section_id', $id),
                        'value' => (isset($edit)) ? $edit->master_section_id : '',
                        'options' => MasterSection::select('id as value', 'nama as text')->orderBy('nama', 'ASC')->get()->toArray(),
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'nama' => 'required|string',
                    'deskripsi' => 'required|string',
                    'master_section_id' => 'required|string',
        ];

        return $rules;
    }

}
