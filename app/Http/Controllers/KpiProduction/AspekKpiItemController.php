<?php

namespace App\Http\Controllers\KpiProduction;

use App\Models\AspekKpiItem;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;
use Idev\EasyAdmin\app\Helpers\Constant;

class AspekKpiItemController extends DefaultController
{
    protected $modelClass = AspekKpiItem::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Aspek Kpi Item';
        $this->generalUri = 'aspek-kpi-item';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Aspek kpi header id', 'column' => 'aspek_kpi_header_id', 'order' => true],
                    ['name' => 'Master kpi id', 'column' => 'master_kpi_id', 'order' => true],
                    ['name' => 'Bobot', 'column' => 'bobot', 'order' => true],
                    ['name' => 'Target', 'column' => 'target', 'order' => true],
                    ['name' => 'Realisasi', 'column' => 'realisasi', 'order' => true],
                    ['name' => 'Skor', 'column' => 'skor', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['aspek_kpi_header_id'],
            'headers' => [
                    ['name' => 'Aspek kpi header id', 'column' => 'aspek_kpi_header_id'],
                    ['name' => 'Master kpi id', 'column' => 'master_kpi_id'],
                    ['name' => 'Bobot', 'column' => 'bobot'],
                    ['name' => 'Target', 'column' => 'target'],
                    ['name' => 'Realisasi', 'column' => 'realisasi'],
                    ['name' => 'Skor', 'column' => 'skor'], 
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
                        'label' => 'Aspek kpi header id',
                        'name' =>  'aspek_kpi_header_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('aspek_kpi_header_id', $id),
                        'value' => (isset($edit)) ? $edit->aspek_kpi_header_id : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Master kpi id',
                        'name' =>  'master_kpi_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('master_kpi_id', $id),
                        'value' => (isset($edit)) ? $edit->master_kpi_id : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Bobot',
                        'name' =>  'bobot',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('bobot', $id),
                        'value' => (isset($edit)) ? $edit->bobot : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Target',
                        'name' =>  'target',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('target', $id),
                        'value' => (isset($edit)) ? $edit->target : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Realisasi',
                        'name' =>  'realisasi',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('realisasi', $id),
                        'value' => (isset($edit)) ? $edit->realisasi : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Skor',
                        'name' =>  'skor',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('skor', $id),
                        'value' => (isset($edit)) ? $edit->skor : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'aspek_kpi_header_id' => 'required|string',
                    'master_kpi_id' => 'required|string',
                    'bobot' => 'required|string',
                    'target' => 'required|string',
                    'realisasi' => 'required|string',
                    'skor' => 'required|string',
        ];

        return $rules;
    }

}
