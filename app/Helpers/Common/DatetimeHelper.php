<?php

namespace App\Helpers\Common;

use Carbon\Carbon;

class DatetimeHelper {
    public static function optionsForYears($withPlaceholder = true)
    {
        $optionsYear = [];

        if ($withPlaceholder) {
            $optionsYear[] = [
                'value' => '',
                'text'  => 'Select...',
            ];
        }

        $currentYear = (int) date('Y');

        for ($y = $currentYear; $y >= $currentYear - 50; $y--) {
            $optionsYear[] = [
                'value' => $y,
                'text'  => $y,
            ];
        }

        return $optionsYear;
    }

    public static function optionsForMonths($withPlaceholder = true) 
    {
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

        if ($withPlaceholder) {
            $optionsMonth = array_merge([['value' => '', 'text' => 'Select...']], $optionsMonth);
        }

        return $optionsMonth;
    }

    public static function getKpiPeriode($date) 
    {
        $date = Carbon::createFromFormat('Y-m', $date);

        return $date->startOfMonth()->format('Y-m-d');
    }

    public static function getKpiPeriodeValue($date) 
    {
        $date = Carbon::createFromFormat('Y-m-d', $date);

        return $date->format('Y-m');
    }


}