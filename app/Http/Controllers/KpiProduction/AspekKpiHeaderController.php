<?php

namespace App\Http\Controllers\KpiProduction;

use App\Models\AspekKpiHeader;
use App\Models\MasterKpi;
use App\Models\MasterSection;
use App\Models\MasterSubsection;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class AspekKpiHeaderController extends DefaultController
{
    protected $modelClass = AspekKpiHeader::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    protected $dynamicPermission = true;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Aspek Kpi Header';
        $this->generalUri = 'aspek-kpi-header';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Bagian', 'column' => 'master_section_id', 'order' => true],
                    ['name' => 'Subbagian', 'column' => 'master_subsection_id', 'order' => true],
                    ['name' => 'Tahun', 'column' => 'tahun', 'order' => true],
                    ['name' => 'Bulan', 'column' => 'bulan', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['master_section_id'],
            'headers' => [
                    ['name' => 'Master section id', 'column' => 'master_section_id'],
                    ['name' => 'Master subsection id', 'column' => 'master_subsection_id'],
                    ['name' => 'Tahun', 'column' => 'tahun'],
                    ['name' => 'Bulan', 'column' => 'bulan'], 
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
                        'type' => 'select',
                        'label' => 'Subbagian',
                        'name' =>  'master_subsection_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('master_subsection_id', $id),
                        'value' => (isset($edit)) ? $edit->master_subsection_id : '',
                        'options' => MasterSubsection::select('id as value', 'nama as text')->orderBy('nama', 'ASC')->get()->toArray(),
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Tahun',
                        'name' =>  'tahun',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('tahun', $id),
                        'value' => (isset($edit)) ? $edit->tahun : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Bulan',
                        'name' =>  'bulan',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('bulan', $id),
                        'value' => (isset($edit)) ? $edit->bulan : ''
                    ],
                    [
                        'type' => 'repeatable_aspek_kpi_item',
                        'label' => 'Aspek KPI',
                        'name' =>  'aspek_kpi',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('aspek_kpi', $id),
                        'value' => (isset($edit)) ? $edit->aspek_kpi : '',
                        'enable_action' => true,
                        'field_data' => [
                            'master_kpi' => MasterKpi::select('id as value', 'nama as text')->orderBy('nama', 'ASC')->get()->toArray(), 
                        ],
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'master_section_id' => 'required|string',
                    'master_subsection_id' => 'required|string',
                    'tahun' => 'required|string',
                    'bulan' => 'required|string',
        ];

        return $rules;
    }

}
