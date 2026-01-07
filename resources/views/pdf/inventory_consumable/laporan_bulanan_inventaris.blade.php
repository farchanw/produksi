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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anggaran dan Realisasi Belanja Barang Produksi</title>
    <link rel="stylesheet" href="{{ asset('css/printable.css') }}">
</head>
<body id="laporan_bulanan_inventaris">
    <header>
        <h1>Anggaran dan Realisasi Belanja Barang Produksi</h1>
        <h1>PT. Sampharindo Perdana</h1>
        <h1>Tahun {{$year ?? 2025}}</h1>
    </header>

    <main>
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
                    {{ number_format($item['price'], 0, ',', '.') }}
                </td>
            </tr>
        @endforeach

        <tr>
            <td class="jumlah" colspan="3">JUMLAH</td>
            <td class="price">
                {{ number_format($totalPrice, 0, ',', '.') }}
            </td>
        </tr>
    @endforeach
@endforeach
</tbody>

            <!--
            <tbody>
                <tr>
                    <td rowspan="6" class="category">Expenses Produksi</td>
                    <td rowspan="6" class="category subcategory">Penandaan</td>
                    <td>Tiner Msn Inject Printer TH 18</td>
                    <td>btl</td>
                    <td>20</td>
                    <td>8.600.000</td>
                </tr>
                <tr>
                    <td>Tiner Msn Inject Printer S1018 (800ml)</td>
                    <td>btl</td>
                    <td>15</td>
                    <td>6.289.000</td>
                </tr>
                <tr>
                    <td>Aseton (cleaning)</td>
                    <td>ltr</td>
                    <td>30</td>
                    <td>1.170.000</td>
                </tr>
                <tr>
                    <td>Tiner Msn Inject jp k33</td>
                    <td>btl</td>
                    <td>4</td>
                    <td>6.320.000</td>
                </tr>
                <tr>
                    <td>Tiner Msn Inject Printer 1072 K (800ml)</td>
                    <td>btl</td>
                    <td>5</td>
                    <td>3.650.000</td>
                </tr>

                <tr>
                    <td class="jumlah" colspan="3">JUMLAH</td>
                    <td>26.029.000</td>
                </tr>

                <tr>
                    <td rowspan="6" class="category">MAIN CATEGORY</td>
                    <td rowspan="6" class="category subcategory">MAIN SUBCATEGORY</td>
                    <td>Item1</td>
                    <td>pcs</td>
                    <td>20</td>
                    <td>8.600.000</td>
                </tr>
                <tr>
                    <td>Item2</td>
                    <td>pcs</td>
                    <td>15</td>
                    <td>6.289.000</td>
                </tr>
                <tr>
                    <td>Item3</td>
                    <td>pcs</td>
                    <td>30</td>
                    <td>1.170.000</td>
                </tr>
                <tr>
                    <td>Item4</td>
                    <td>pcs</td>
                    <td>4</td>
                    <td>6.320.000</td>
                </tr>
                <tr>
                    <td>Item5</td>
                    <td>pcs</td>
                    <td>5</td>
                    <td>3.650.000</td>
                </tr>

                <tr>
                    <td class="jumlah" colspan="3">JUMLAH</td>
                    <td>26.029.000</td>
                </tr>
            </tbody>
            -->
        </table>

        <div class="signature-block">
            Semarang, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
            <br>
            <br>
            <br>
            <br>
            Bagian Produksi
        </div>
    </main>
</body>
</html>