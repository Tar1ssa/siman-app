<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page {
        margin: 0.8cm 2.2cm 2.2cm 2cm; /* Top, Right, Bottom, Left margins */
    }

    body{
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12pt;
        line-height: 1.1;
        
    }

    .header{
        text-align: center;
        line-height: 1.5; 
    }

    .header-title{
        font-weight: bold;
        font-size: 14pt;
    }

    .header-address{
        font-size: 9pt;
    }

    .hr{
        border-top:0.1cm solid black; 
        height:0.04cm; 
        margin:1px -0.7cm 5px -0.7cm; 
        border-bottom:0.04cm solid black;
    }

    .logo{
        position: absolute;
        width: 4.2cm;      /* standard gov letterhead size */
        height: auto;
        object-fit: cover;
    }

    .meta-table{
        width: 90%;
        margin-bottom: 20px;
    }

    .meta-table td{
        padding: 0;
        line-height: 1;
    }

    .meta-table td{
        vertical-align: top;
        padding: 2px 5px;
    }

    .content{
        text-align: justify;
    }

    .signature{
        margin-top: 60px;
        width: 100%;
    }

    .signature td{
        vertical-align: top;
    }

    .tembusan{
        margin-top: 30px;
    }

    .page-break {
        page-break-before: always;      /* For DomPDF */
        break-before: page;
        padding-top: 1.5cm;
    }

    .lampiran-title {
        text-align: center;
        font-weight: bold;
        margin-top: 40px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-size: 11pt;
    }

    .table th, .table td {
        border: 1px solid black;
        padding: 6px;
        text-align: center;
    }
</style>
</head>

<body>

<img src="{{ public_path('assets/pdflogo.png') }}" class="logo"
style=" left:-1.7cm; top:-0.9cm;">

<div class="header">
    <div class="header-title">
        KEMENTERIAN LINGKUNGAN HIDUP /<br>
        BADAN PENGENDALIAN LINGKUNGAN HIDUP
    </div>

    <div class="header-title" style="font-size: 12pt;">
        SEKRETARIAT KEMENTERIAN / SEKRETARIAT UTAMA
    </div>

    <div class="header-address">
        Plaza Kuningan Menara Selatan Lantai 11, Jl. H. R. Rasuna Said,
        Kuningan, Jakarta Selatan 12940
    </div>
</div>

<div class="hr"></div>

<table class="meta-table" style="border-collapse: collapse;">
<tr>
    <td width="55">Nomor</td>
    <td width="10">:</td>
    <td>B-…/C.3/KAP.4.1/…/{{$tahun}}</td>
    <td style="text-align:right">… {{$tahun}}</td>
</tr>

<tr>
    <td>Lampiran</td>
    <td>:</td>
    <td colspan="2">1 (satu) Berkas</td>
</tr>

<tr>
    <td>Hal</td>
    <td>:</td>
    <td colspan="2">
        Permohonan Penetapan Status<br>
        Penggunaan Barang Milik Negara <br>(BMN)
    </td>
</tr>
</table>

<p>
Yth. Kepala Biro Umum KLH/BPLH.<br>
di-<br>
&nbsp;&nbsp;&nbsp;Jakarta
</p>

<div class="content">

<p>
Dalam rangka melaksanakan Peraturan Menteri Keuangan Republik Indonesia
Nomor PMK No. 40 Tahun 2024 tentang Tata Cara Penggunaan BMN mengenai
Penetapan Status Penggunaan BMN selain tanah dan/atau bangunan,
yang tidak memiliki bukti kepemilikan, dengan nilai perolehan sampai
dengan Rp 100.000.000,- (seratus juta rupiah) per unit/satuan.
</p>

<p>
Sehubungan dengan hal tersebut di atas, bersama ini dengan hormat kami
ajukan daftar BMN dengan nilai perolehan sampai dengan
Rp 100.000.000,- (seratus juta rupiah) per unit/satuan untuk
<strong>ditetapkan Status Penggunaan BMN</strong> pada Satuan Kerja Deputi
Bidang Pengelolaan Sampah, Limbah, dan Bahan Beracun Berbahaya
(Deputi Bidang PSLB3).
</p>

<p>
Atas perhatian dan kerjasamanya, diucapkan terima kasih.
</p>

</div>

<table class="signature">
<tr>
<td width="60%"></td>
<td>
Kepala Biro Umum,<br><br><br><br><br><br>

<u>Sasmita Nugroho</u><br>
NIP. 19690705 199603 1 001
</td>
</tr>
</table>

<div class="tembusan">
Tembusan:<br>
Deputi Pengelolaan Sampah, Limbah dan Bahan Beracun dan Berbahaya;
</div>

<div class="page-break"></div>

<div class="lampiran-title">
LAMPIRAN PERMOHONAN PENETAPAN STATUS<br>
PENGGUNAAN BARANG MILIK NEGARA (BMN)
</div>
<div style="text-align:center; margin-bottom:20px; font-weight:bold;">
Nomor: B-…/C.3/KAP.4.1/…/{{$tahun}}
</div>



<table class="table">
<thead>
<tr>
<th>No</th>
<th>Nama Barang</th>
<th>Tahun Perolehan</th>
<th>Spesifikasi</th>
<th>Jumlah Barang</th>
<th>Merk</th>
<th>Kondisi</th>
<th>Keterangan</th>
</tr>
</thead>
<tbody>
@foreach($data as $index => $item)
<tr>
<td>{{ $index + 1 }}</td>
<td>{{ $item->barang->nama_barang ?? '' }}</td>
<td>{{ $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->year : '' }}</td>
<td>{{ $item->keterangan ?? '' }}</td>
<td>1</td>
<td>{{ $item->merk ?? '' }}</td>
<td>{{ $item->kondisi ?? '' }}</td>
<td></td>
</tr>
@endforeach
</tbody>
</table>


</body>
</html>
