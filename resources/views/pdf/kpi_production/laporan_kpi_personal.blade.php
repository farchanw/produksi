<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan KPI</title>
    <link rel="stylesheet" href="{{ asset('css/printable.css') }}">
</head>

<body id="laporan_kpi_personal">
    <header>
        <h1>Laporan Penilaian Kinerja Berbasis KPI</h1>
    </header>

    <main>
        <table class="info-table">
            <tbody>
                <tr>
                    <td class="label">Nama</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $nama }}</td>
                </tr>
                <tr>
                    <td class="label">NIK</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $nik }}</td>
                </tr>
                <tr>
                    <td class="label">Divisi</td>
                    <td class="separator">:</td>
                    <td class="value">Produksi</td>
                </tr>
                <tr>
                    <td class="label">Bulan dan Tahun</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $bulanNama }} {{ $tahun }}</td>
                </tr>
            </tbody>
        </table>


        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Area Kinerja Utama</th>
                    <th>KPI</th>
                    <th>Bobot</th>
                    <th>Target</th>
                    <th>Realisasi</th>
                    <th>Skor</th>
                    <th>Skor Akhir</th>
                </tr>
            </thead>
            <tbody>
                 @foreach ($records as $item)
                    <tr>
                        <td class="bold center">{{ $loop->iteration }}</td>
                        <td>{{ $item->area_kinerja_utama }}</td>
                        <td>{{ $item->nama_kpi }}</td>
                        <td class="center">{{ $item->bobot }}</td>
                        <td class="center">{{ $item->target }}</td>
                        <td class="highlight">{{ $item->realisasi }}</td>
                        <td class="center bold">{{ $item->skor }}</td>
                        <td class="center bold">{{ $item->skor_akhir }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="bold center">Total</td>
                    <td class="center bold">{{ $records->sum('bobot') }}</td> <!-- TOTAL BOBOT -->
                    <td colspan="3"></td>
                    <td class="center bold">{{ $records->sum('skor_akhir') }}</td> <!-- TOTAL Skor Akhir -->
                </tr>
            </tfoot>
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