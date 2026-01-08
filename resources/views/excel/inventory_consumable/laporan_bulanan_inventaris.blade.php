
@php
    function toRoman($number) {
        $map = [
            'M'  => 1000,
            'CM' => 900,
            'D'  => 500,
            'CD' => 400,
            'C'  => 100,
            'XC' => 90,
            'L'  => 50,
            'XL' => 40,
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
            'I'  => 1,
        ];

        $result = '';
        foreach ($map as $roman => $value) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }
        return $result;
    }
@endphp



        <table border="1" cellspacing="0" cellpadding="6">
            <thead>
                <tr>
                    <th rowspan="2">NO</th>
                    <th rowspan="2">BIAYA PRODUKSI</th>
                    <th rowspan="2">POST ANGGARAN</th>
                    <th rowspan="2" class="name">PERINCIAN POST ANGGARAN</th>
                    <th rowspan="2">SAT</th>
                    <th colspan="2">{{ $monthName }}</th>
                </tr>
                <tr>
                    <th>Qwt</th>
                    <th>Value</th>
                </tr>
            </thead>

<tbody>
@foreach ($records as $kindGroup)
    @php
        $kindRowspan = $kindGroup['categories']->sum(
            fn ($c) => count($c['items']) + 1
        );
    @endphp

    @foreach ($kindGroup['categories'] as $category)
        @php
            $itemCount  = count($category['items']);
            $rowspan    = $itemCount + 1;
            $totalQty   = collect($category['items'])->sum('qty');
            $totalPrice = collect($category['items'])->sum('price');
        @endphp

        @foreach ($category['items'] as $item)
            <tr>
                @if ($loop->first && $loop->parent->first)
                    <td rowspan="{{ $kindRowspan }}" class="text-center">
                        {{ toRoman($loop->parent->parent->iteration) }}
                    </td>

                    <td rowspan="{{ $kindRowspan }}" class="expenses">
                        {{ strtoupper($kindGroup['kind']) }}
                    </td>
                @endif

                @if ($loop->first)
                    <td rowspan="{{ $rowspan }}" class="category">
                        {{ $category['name'] }}
                    </td>
                @endif

                <td class="name">{{ $item['name'] }}</td>
                <td class="satuan">{{ $item['satuan'] }}</td>
                <td class="qty">{{ $item['qty'] }}</td>
                <td class="price">
                    {{ $item['price'] }}
                </td>
            </tr>
        @endforeach

        <tr>
            <td class="jumlah" colspan="3">JUMLAH</td>
            <td class="price">
                {{ $item['price'] }}
            </td>
        </tr>
    @endforeach
@endforeach
</tbody>

        </table>
