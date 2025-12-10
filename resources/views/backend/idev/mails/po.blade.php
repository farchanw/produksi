<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>{{$po->title}}</title>
<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
    }
    .container {
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
        border-radius: 5px;
        background-color: #ffffff;
    }
    .header {
        font-size: 20px;
        font-weight: bold;
        color: #ffffff;
        background: #2d6fb7;
        padding: 20px;
    }
    .content {
        margin-bottom: 20px;
        padding: 20px;
    }
    .footer {
        font-size: 14px;
        color: #777;
        margin-top: 20px;
        border-top: 1px solid #eee;
        padding: 20px;
    }
    ul {
        list-style-type: none;
        padding: 0;
    }
    ul li {
        margin-bottom: 5px;
    }
    .table-header{
        font-size: 14px;
        color: #777;
        width: 100%;
        margin-bottom: 20px;
    }
    .styled-table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .styled-table thead {
        background-color: #f2f2f2;
    }

    .styled-table th,
    .styled-table td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
    }
    .styled-table td:nth-child(1),
    .styled-table td:nth-child(3) {
        text-align: center;
    }
    .styled-table tbody tr:nth-child(even) {
        background-color: #fafafa;
    }
</style>
</head>
<body>

<div class="container">
    <div class="header">
        PURCHASE ORDER
    </div>
    <div class="content">
        <table class="table-header">
            <tr>
                <td valign="top">
                    Vendor <br>
                    {{$po->vendor_name}}<br>
                    Jalan Reksonero, Surabaya<br>
                </td>
                <td valign="top">
                    Ship To <br>
                    PT iDev  <br>
                    Jalan Mangkubumi, Semarang<br>
                    08313291331
                </td>
                <td valign="top">
                    PO Date : {{$po->tanggal}}<br>
                    PO Number : {{$po->numbers}}
                </td>
            </tr>
        </table>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Quantity</th>
                    <th>Satuan</th>
                    <th>Catatan</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>
                @php
                $totalPrice = 0;
                @endphp
                @foreach($poDetails as $key => $material)
                <tr>
                    <th>{{ $key+1 }}</th>
                    <th>{{$material->material_name}}</th>
                    <th>{{$material->quantity}}</th>
                    <th>{{$material->unit}}</th>
                    <th>{{$material->notes}}</th>
                    <th>{{"Rp " . number_format($material->price, 2, ",", ".") }}</th>
                </tr>
                @php
                $totalPrice += $material->price;
                @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5">
                        TOTAL
                    </th>
                    <th>
                        {{"Rp " . number_format($totalPrice, 2, ",", ".") }}
                    </th>
                </tr>
            </tfoot>
        </table>

        <p>
            <small>
            Syarat-syarat Pembelian :<br>
            1. Barang yang dikirim harus sesuai Purchase Order (PO). Jika ada kesalahan PO,
            harap hubungi kami untuk penggantian PO<br>
            2. PO harus dilampirkan saat pengiriman barang dan penagihan<br>
            3. PO ini hanya berlaku maksimal 3 bulan setelah tanggal PO<br>
            4. Term Of Payment : Cash On Delivery hari setelah invoice diterima
            </small>
        </p>
    </div>
    <div class="footer">
        <p>Hormat kami,</p>
        <p>Dani Hadi<br>
        <strong>PT Maju Bakti</strong>
    </div>
</div>

</body>
</html>