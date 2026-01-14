<?php

namespace App\Helpers\Modules;

class DatetimeHelper {
    public static function optionsForYears()
    {
        $optionsYear = [];

        $currentYear = (int) date('Y');

        for ($y = $currentYear; $y >= $currentYear - 50; $y--) {
            $optionsYear[] = [
                'value' => $y,
                'text'  => $y,
            ];
        }

        return $optionsYear;
    }

    public static function optionsForMonths() 
    {
        return [
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
    }


}