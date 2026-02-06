<?php

namespace App\Helpers\Common;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

class DatetimeHelper
{
    public static function optionsForYears($withPlaceholder = true)
    {
        $optionsYear = [];

        if ($withPlaceholder) {
            $optionsYear[] = [
                'value' => '',
                'text' => 'Select...',
            ];
        }

        $currentYear = (int) date('Y');

        for ($y = $currentYear; $y >= $currentYear - 50; $y--) {
            $optionsYear[] = [
                'value' => $y,
                'text' => $y,
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
            array_unshift($optionsMonth, ['value' => '', 'text' => 'Select...']);
        }

        return $optionsMonth;
    }

    public static function getKpiPeriode($date)
    {
        try {
            return Carbon::createFromFormat('Y-m', $date)
                ->startOfMonth()
                ->format('Y-m-d');
        } catch (InvalidFormatException $e) {
            return $date;
        }
    }

    public static function getKpiPeriodeValue($date)
    {
        try {
            return Carbon::createFromFormat('Y-m-d', $date)
                ->format('Y-m');
        } catch (InvalidFormatException $e) {
            return $date;
        }
    }

    public static function getKpiPeriodeFormatted($date)
    {
        try {
            return Carbon::createFromFormat('Y-m-d', $date)
                ->locale('id')
                ->translatedFormat('F Y');
        } catch (InvalidFormatException $e) {
            return $date;
        }
    }
}
