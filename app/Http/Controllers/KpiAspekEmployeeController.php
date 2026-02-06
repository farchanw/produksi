<?php

namespace App\Http\Controllers;

use App\Models\KpiAspekEmployee;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class KpiAspekEmployeeController extends DefaultController
{
    protected $modelClass = KpiAspekEmployee::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'Kpi Aspek Employee';
        $this->generalUri = 'kpi-aspek-employee';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Kpi employee id', 'column' => 'kpi_employee_id', 'order' => true],
                    ['name' => 'Aspek kpi header id', 'column' => 'aspek_kpi_header_id', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['kpi_employee_id'],
            'headers' => [
                    ['name' => 'Kpi employee id', 'column' => 'kpi_employee_id'],
                    ['name' => 'Aspek kpi header id', 'column' => 'aspek_kpi_header_id'], 
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
                        'label' => 'Kpi employee id',
                        'name' =>  'kpi_employee_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('kpi_employee_id', $id),
                        'value' => (isset($edit)) ? $edit->kpi_employee_id : ''
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Aspek kpi header id',
                        'name' =>  'aspek_kpi_header_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('aspek_kpi_header_id', $id),
                        'value' => (isset($edit)) ? $edit->aspek_kpi_header_id : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'kpi_employee_id' => 'required|string',
                    'aspek_kpi_header_id' => 'required|string',
        ];

        return $rules;
    }

}
