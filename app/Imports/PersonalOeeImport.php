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

                // collect operator + assistant
                $people = collect([
                    $row['operator'] ?? null,
                    $row['foreman'] ?? null,
                ])
                ->filter()      // remove empty
                ->unique();     // same person in both columns → once

                return $people->map(function ($person) use ($row) {
                    return [
                        'person' => trim($person),
                        'oee'    => (float) $row['oee'],
                    ];
                });
            })
            ->groupBy('person')
            ->map(function ($items) {
                return [
                    'avg_oee' => round($items->avg('oee'), 2),
                    'count' => $items->count(),
                ];
            });
    }
}
