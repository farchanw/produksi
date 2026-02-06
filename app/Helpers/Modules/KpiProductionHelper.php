<?php

namespace App\Helpers\Modules;

use App\Models\MasterSection;

class KpiProductionHelper
{
    public static function optionsForSections()
    {
        return MasterSection::select('id as value', 'nama as text')
            ->orderBy('nama', 'ASC')
            ->get();
    }

    public static function optionsKategori()
    {
        return [
            ['value' => 'personal', 'text' => 'Personal'],
            ['value' => 'divisi', 'text' => 'Divisi'],
        ];
    }

    public static function calculateScore($values)
    {
        return collect($values)->map(function ($item) {
            $realisasi = isset($item['realisasi']) ? floatval($item['realisasi']) : 0;
            $target = isset($item['target']) ? floatval($item['target']) : 1;
            $tipe = $item['tipe'] ?? 'Max';
            $bobot = isset($item['bobot']) ? floatval($item['bobot']) : 1;

            $tipe = strtolower($tipe);
            $skor = 0;

            if ($tipe === 'max') {

                if ($target <= 0) {
                    $skor = 0;
                } else {
                    $skor = ($realisasi / $target) * 100;
                }

            } elseif ($tipe === 'min') {

                if ($realisasi <= 0) {
                    $skor = 100;
                } elseif ($target <= 0) {
                    $skor = 0;
                } else {
                    $skor = ($target / $realisasi) * 100;
                }

            } else {
                // fallback if tipe is unknown
                $skor = 0;
            }


            $skor = round($skor, 2);
            $skor_akhir = round($skor * ($bobot / 100), 2);

            return [
                'aspek_kpi_item_id' => $item['aspek_kpi_item_id'],
                'realisasi' => $realisasi,
                'skor' => $skor,
                'skor_akhir' => $skor_akhir,
            ];
        });
    }

    public static function mapLaporanPersonal($records)
    {
        return $records->map(function ($item) {
            $values = collect(json_decode($item->aspek_values, true))
                ->keyBy(fn ($v) => (int) $v['aspek_kpi_item_id']);

            $match = $values->get((int) $item->aspek_kpi_item_id);

            $item->skor = $match['skor'] ?? null;
            $item->realisasi = $match['realisasi'] ?? null;
            $item->skor_akhir = $match['skor_akhir'] ?? null;

            unset($item->aspek_values);

            return $item;
        });
    }

    public static function mapLaporanPersonalBulk($records)
    {
        return collect(json_decode($records, true))->map(function ($item) {
            $values = collect($item);

            $item['skor'] = $values->get('skor') ?? null;
            $item['realisasi'] = $values->get('realisasi') ?? null;
            $item['skor_akhir'] = $values->get('skor_akhir') ?? null;

            unset($item->aspek_values);

            return $item;
        });
    }
}
