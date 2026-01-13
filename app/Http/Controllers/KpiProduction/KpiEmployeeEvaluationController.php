<?php

namespace App\Http\Controllers\KpiProduction;

use App\Models\KpiEmployeeEvaluation;
use App\Models\Employee;
use Idev\EasyAdmin\app\Http\Controllers\DefaultController;

class KpiEmployeeEvaluationController extends DefaultController
{
    protected $modelClass = KpiEmployeeEvaluation::class;
    protected $title;
    protected $generalUri;
    protected $tableHeaders;
    // protected $actionButtons;
    // protected $arrPermissions;
    protected $dynamicPermission = true;
    protected $importExcelConfig;

    public function __construct()
    {
        $this->title = 'KPI Karyawan';
        $this->generalUri = 'kpi-employee-evaluation';
        // $this->arrPermissions = [];
        $this->actionButtons = ['btn_edit', 'btn_show', 'btn_delete'];

        $this->tableHeaders = [
                    ['name' => 'No', 'column' => '#', 'order' => true],
                    ['name' => 'Employee', 'column' => 'employee_id', 'order' => true],
                    ['name' => 'Aspek kpi header id', 'column' => 'aspek_kpi_header_id', 'order' => true],
                    ['name' => 'Bulan', 'column' => 'bulan', 'order' => true],
                    ['name' => 'Tahun', 'column' => 'tahun', 'order' => true],
                    ['name' => 'Skor', 'column' => 'skor', 'order' => true], 
                    ['name' => 'Created at', 'column' => 'created_at', 'order' => true],
                    ['name' => 'Updated at', 'column' => 'updated_at', 'order' => true],
        ];


        $this->importExcelConfig = [ 
            'primaryKeys' => ['employee_id'],
            'headers' => [
                    ['name' => 'Employee id', 'column' => 'employee_id'],
                    ['name' => 'Aspek kpi header id', 'column' => 'aspek_kpi_header_id'],
                    ['name' => 'Bulan', 'column' => 'bulan'],
                    ['name' => 'Tahun', 'column' => 'tahun'],
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

        $optionsYear = [];

        $currentYear = (int) date('Y');

        for ($y = $currentYear; $y >= $currentYear - 50; $y--) {
            $optionsYear[] = [
                'value' => $y,
                'text'  => $y,
            ];
        }

        $optionsMonth = [
            ['value' => 1, 'text' => 'Januari'],
            ['value' => 2, 'text' => 'Februari'],
            ['value' => 3, 'text' => 'Maret'],
            ['value' => 4, 'text' => 'April'],
            ['value' => 5, 'text' => 'Mei'],
            ['value' => 6, 'text' => 'Juni'],
            ['value' => 7, 'text' => 'Juli'],
            ['value' => 8, 'text' => 'Agustus'],
            ['value' => 9, 'text' => 'September'],
            ['value' => 10, 'text' => 'Oktober'],
            ['value' => 11, 'text' => 'November'],
            ['value' => 12, 'text' => 'Desember'],
        ];


        $fields = [
                    [
                        'type' => 'select',
                        'label' => 'Employee',
                        'name' =>  'employee_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('employee_id', $id),
                        'value' => (isset($edit)) ? $edit->employee_id : '',
                        'options' => Employee::select('id as value', 'nama as text')->orderBy('nama', 'ASC')->get()->toArray(),
                    ],
                    [
                        'type' => 'text',
                        'label' => 'Aspek kpi header id',
                        'name' =>  'aspek_kpi_header_id',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('aspek_kpi_header_id', $id),
                        'value' => (isset($edit)) ? $edit->aspek_kpi_header_id : ''
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Bulan',
                        'name' =>  'bulan',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('bulan', $id),
                        'value' => (isset($edit)) ? $edit->bulan : '',
                        'options' => $optionsMonth,
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Tahun',
                        'name' =>  'tahun',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('tahun', $id),
                        'value' => (isset($edit)) ? $edit->tahun : '',
                        'options' => $optionsYear,
                    ],
                    [
                        'type' => 'number',
                        'label' => 'Realisasi',
                        'name' =>  'realisasi',
                        'class' => 'col-md-12 my-2',
                        'required' => $this->flagRules('realisasi', $id),
                        'value' => (isset($edit)) ? $edit->realisasi : ''
                    ],
        ];
        
        return $fields;
    }


    protected function rules($id = null)
    {
        $rules = [
                    'employee_id' => 'required|string',
                    'aspek_kpi_header_id' => 'required|string',
                    'bulan' => 'required|string',
                    'tahun' => 'required|string',
                    'skor' => 'required|string',
        ];

        return $rules;
    }

}
