<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class PersonalOeeImport implements ToCollection, WithHeadingRow
{
    public Collection $result;

public function collection(Collection $rows)
{
    $this->result = $rows
        ->flatMap(function ($row) {

            $people = collect([
                $row['operator'] ?? null,
                $row['foreman'] ?? null,
            ])
            ->filter()
            ->unique();

            // normalize OEE
            $oee = str_replace(',', '.', $row['oee']);
            $oee = rtrim($oee, '%');
            $oee = (float) $oee;

            // normalize a%
            $a = str_replace(',', '.', $row['a']);
            $a = rtrim($a, '%');
            $a = (float) $a;

            // normalize p%
            $p = str_replace(',', '.', $row['p']);
            $p = rtrim($p, '%');
            $p = (float) $p;

            // normalize q%
            $q = str_replace(',', '.', $row['q']);
            $q = rtrim($q, '%');
            $q = (float) $q;

            if ($oee <= 1) {
                $oee *= 100;
            }

            return $people->map(function ($person) use ($oee, $a, $p, $q) {
                return [
                    'person' => trim($person),
                    'oee'    => $oee,
                    'a'      => $a,
                    'p'      => $p,
                    'q'      => $q,
                ];
            });
        })
        ->groupBy('person')
        ->map(function ($items) {
            return [
                'avg_oee' => round($items->avg('oee'), 2),
                'avg_a'   => round($items->avg('a'), 2),
                'avg_p'   => round($items->avg('p'), 2),
                'avg_q'   => round($items->avg('q'), 2),
                'count'   => $items->count(),
            ];
        });
}

}
