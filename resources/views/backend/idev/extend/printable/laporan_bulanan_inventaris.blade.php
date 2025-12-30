@php

@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan Inventaris</title>
    <link rel="stylesheet" href="{{ asset('css/printable.css') }}">
</head>
<body id="laporan_bulanan_inventaris">
    <header>
        <h1>Laporan Bulanan Inventaris</h1>
        <h1>PT. Maju Bakti MASIH CONTOH</h1>
        <h1>Tahun {{$year ?? 2025}}</h1>
    </header>

    <main>
        <table border="1" cellspacing="0" cellpadding="6">
            <thead>
                <tr>
                    <th rowspan="2">BIAYA PRODUKSI</th>
                    <th rowspan="2">POST ANGGARAN</th>
                    <th rowspan="2">PERINCIAN POST ANGGARAN</th>
                    <th rowspan="2">SAT</th>
                    <th colspan="2">NOP</th>
                </tr>
                <tr>
                    <th>Qwt</th>
                    <th>Value</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td rowspan="6" class="category">Expenses Produksi</td>
                    <td rowspan="6" class="category">Penandaan</td>
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
            </tbody>
        </table>
    </main>
</body>
</html>