<?php

namespace App\Helpers\Modules;

class KpiProductionHelper {
    public static function optionsKategori()
    {
        return [
            ['value' => 'personal', 'text' => 'Personal'],
            ['value' => 'divisi', 'text' => 'Divisi'],
        ];
    }

    public static function calculateScore($values)
    {
        return collect($values)->map(function($item) {
            $realisasi = isset($item['realisasi']) ? floatval($item['realisasi']) : 0;
            $target    = isset($item['target']) ? floatval($item['target']) : 1;
            $tipe      = $item['tipe'] ?? 'Max';
            $bobot     = isset($item['bobot']) ? floatval($item['bobot']) : 1;

            // Normalize tipe
            $tipe = strtolower($tipe);

            // Initialize skor
            $skor = 0;

            // Avoid division by zero for target
            if ($target <= 0) {
                $skor = 0;

            } elseif ($tipe === 'max') {
                // Higher is better (allow > 100)
                $skor = ($realisasi / $target) * 100;

            } elseif ($tipe === 'min') {
                // Lower is better (allow > 100)
                if ($realisasi <= 0) {
                    // Best possible result
                    $skor = 100;
                } else {
                    $skor = ($target / $realisasi) * 100;
                }
            }

            // Round skor to 2 decimal places
            $skor = round($skor, 2);

            // Calculate skor_akhir (weighted by bobot percentage)
            $skor_akhir = round($skor * ($bobot / 100), 2);


            return [
                'aspek_kpi_item_id' => $item['aspek_kpi_item_id'],
                'realisasi'         => $realisasi,
                'skor'              => $skor,
                'skor_akhir'        => $skor_akhir,
            ];
        });
    }
}