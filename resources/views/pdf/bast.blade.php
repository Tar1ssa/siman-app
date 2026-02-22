<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link href="{{asset('assets/dist/assets/css/plugins/bootstrap.min.css')}}" rel="stylesheet">
<style>
@page {
    margin: 2.5cm 2.5cm 2.5cm 3cm;
}

body {
    font-family:  "Aptos", Arial, Helvetica, sans-serif;
    font-size: 12pt;
    line-height: 1.4;
}

/* --- HEADER (kop surat) --- */
.header {
    text-align: center;
    font-weight: bold;
    text-transform: uppercase;
}

.sub-header {
    text-align: center;
    font-size: 11pt;
}

.divider {
    border-top: 2px solid black;
    margin: 10px 0 20px 0;
}

/* --- TITLE --- */
.title {
    text-align: center;
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 15px;
}

/* --- CONTENT --- */
.paragraph {
    text-align: justify;
    margin-bottom: 12px;
}

/* --- PARTIES (PIHAK) --- */
.party-table {
    width: 100%;
    margin-bottom: 15px;
}

.party-table td {
    vertical-align: top;
}

/* --- SIGNATURE --- */
.signature {
    width: 100%;
    margin-top: 60px;
}

.signature td {
    text-align: center;
    vertical-align: top;
}

/* --- LAMPIRAN TABLE --- */
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

/* --- LAMPIRAN TITLE --- */
.lampiran-title {
    text-align: center;
    font-weight: bold;
    margin-top: 40px;
}

.page-break {
    page-break-before: always;      /* For DomPDF */
    break-before: page;
    padding-top: 1.5cm;
}

/* DomPDF does NOT like img-fluid for logos */
.logo-header {
    width: 4cm;      /* standard gov letterhead size */
    height: auto;
    object-fit: cover;
}

/* Make sure images render sharply in DomPDF */
img {
    image-rendering: crisp-edges;

}
</style>
</head>

<body class="">
    {{-- <div class="row d-flex align-items-center mb-2 justify-content-center">
        <div class="col-md-2 d-flex justify-content-center align-items-center ">
                <img class="logo-header" src="{{ public_path('assets/pdflogo.png') }}" alt="Logo KLHK-BPLH"
                style="object-fit:cover;"/>
        </div>
        <div class="col-md-8 text-center">
            <div style="font-weight:bold; text-transform:uppercase; font-size:12.5pt;">
                KEMENTERIAN LINGKUNGAN HIDUP/BADAN PENGENDALIAN LINGKUNGAN HIDUP
                <br>
                <span class="fs-3">SEKRETARIAT UTAMA</span>
            </div>

            <div style="font-size:8.5pt; margin-top:6px;">
                Jl. DI Panjaitan Kav.24, Kebon Nanas, Jakarta Timur 13410<br>
                Gedung A Lantai 4 Website: www.kemenlh.go.id
            </div>

        </div>
        <div class="col-md-2"></div>
    </div> --}}

    {{-- <table width="100%" style="border-collapse: collapse; margin-bottom: 8px;">
        <tr>
            <!-- LOGO COLUMN (fixed space) -->
            <td width="15%" align="center" style="vertical-align: middle;">
                <div style="width:100%; text-align:center;">
                    <img
                        src="{{ public_path('assets/pdflogo.png') }}"
                        class="logo-header"
                        alt="Logo KLHK-BPLH"
                        style="display:block; margin:0 auto;"
                    >
                </div>
            </td>

            <!-- CENTER TEXT COLUMN (stays centered no matter what) -->
            <td width="70%" align="center" style="vertical-align: middle;">
                <div style="font-weight:bold; text-transform:uppercase; font-size:12.5pt;">
                    KEMENTERIAN LINGKUNGAN HIDUP /<br>
                    BADAN PENGENDALIAN LINGKUNGAN HIDUP
                </div>

                <div style="font-weight:bold; font-size:14pt; margin-top:2px;">
                    SEKRETARIAT UTAMA
                </div>
            </td>

            <!-- BALANCING COLUMN -->
            <td width="15%"></td>
        </tr>
        <tr>
            <td width="100%" colspan="3" align="center" style="vertical-align: middle;">
                <div style="font-weight:bold; font-size:6.5pt; margin-top:6px; line-height:1.3;">
                    Jl. DI Panjaitan Kav.24, Kebon Nanas, Jakarta Timur 13410
                    Gedung A Lantai 4 — Website: www.kemenlh.go.id
                </div>
            </td>
        </tr>
    </table> --}}

    <table width="100%"
       style="border-collapse: collapse; margin-bottom: 0px; table-layout: fixed;">
        <tr>
            <!-- LEFT SPACER equal to logo width -->
            <td width="18%"></td>

            <!-- CENTER TEXT (TRUE CENTER OF PAGE) -->
            <td width="64%" align="center" style="vertical-align: middle;">
                <div style="font-weight:bold; text-transform:uppercase; font-size:12.5pt;">
                    KEMENTERIAN LINGKUNGAN HIDUP /<br>
                    BADAN PENGENDALIAN LINGKUNGAN HIDUP
                </div>

                <div style="font-weight:bold; font-size:14pt; margin-top:2px;">
                    SEKRETARIAT UTAMA
                </div>

            </td>

            <!-- RIGHT SPACER equal to left -->
            <td width="18%"></td>
        </tr>

        <tr>
            <td ></td>
            <td colspan="2" align="left" style="vertical-align: middle;">
                <div style=" font-size:7pt; margin-top:6px; line-height:1.3;">
                    Jl. DI Panjaitan Kav.24, Kebon Nanas, Jakarta Timur 13410
                    Gedung A Lantai 4 Website: www.kemenlh.go.id
                </div>
            </td>
        </tr>
    </table>

<!-- Logo placed ABSOLUTELY on the left -->
<img
   src="{{ public_path('assets/pdflogo.png') }}"
   class="logo-header"
   style="position:absolute; left:-0.6cm; top:-0.8cm;"
>


<div style="border-top:0.1cm solid black; margin:1px 0 20px 0;"></div>


<div class="title">
BERITA ACARA SERAH TERIMA<br>
BARANG MILIK NEGARA
</div>

<div style="text-align:center; margin-bottom:20px; font-weight:bold;">
Nomor: BA-&nbsp;&nbsp;&nbsp;&nbsp;/PSLB3/BMN/{{ now()->format('m/Y') }}
</div>

<div class="paragraph">
Pada hari ini <b>{{ $hari }}</b> tanggal <b>{{ $tanggal }}</b> bulan
<b>{{ $bulan }}</b> tahun <b>{{ $tahun }}</b>, kami yang bertanda tangan di bawah ini:
</div>

<table class="party-table ms-5 mb-4">
<tr>
    <td width="5%">1.</td>
    <td width="20%">Nama</td>
    <td width="10px">:</td>
    <td>{{ $pihak_pertama_nama }}</td>
</tr>
<tr>
    <td></td>
    <td>NIP</td>
    <td width="10px">:</td>
    <td>{{ $pihak_pertama_nip }}</td>
</tr>
<tr>
    <td></td>
    <td>Jabatan</td>
    <td width="10px">:</td>
    <td>{{ $pihak_pertama_jabatan }}</td>
</tr>
<tr>
    <td></td>
    <td>Alamat</td>
    <td width="10px">:</td>
    <td>{{ $pihak_pertama_alamat }}</td>
</tr>
</table>
<table class="mb-2">

    <tr><td colspan="3"><b>Selanjutnya disebut PIHAK PERTAMA</b></td></tr>
</table>
<table class="party-table ms-5 mb-4">
<tr>
    <td width="5%">2.</td>
    <td width="20%">Nama</td>
    <td width="10px">:</td>
    <td>{{ $pihak_kedua_nama }}</td>
</tr>
<tr>
    <td></td>
    <td>NIP</td>
    <td width="10px">:</td>
    <td>{{ $pihak_kedua_nip }}</td>
</tr>
<tr>
    <td></td>
    <td>Jabatan</td>
    <td width="10px">:</td>
    <td>{{ $pihak_kedua_jabatan }}</td>
</tr>
<tr>
    <td></td>
    <td>Alamat</td>
    <td width="10px">:</td>
    <td>{{ $pihak_kedua_alamat }}</td>
</tr>
</table>
<table class="mb-2">

    <tr><td colspan="3"><b>Selanjutnya disebut PIHAK KEDUA</b></td></tr>
</table>
<div class="paragraph">
    <div style="text-align: center;">

        <b>Pasal 1</b><br>
    </div>
<b>PIHAK PERTAMA</b> menyerahkan Barang Milik Negara Deputi Bidang Pengelolaan Sampah,
Limbah dan Bahan Berbahaya dan Beracun berupa
<b>{{ $barang }}</b> sebagaimana tercantum dalam lampiran berita acara ini kepada PIHAK KEDUA.
</div>

<div class="paragraph">
    <div style="text-align: center;">
        <b>Pasal 2</b><br>
    </div>
<b>PIHAK KEDUA</b> menerima Barang Milik Negara Deputi Bidang Pengelolaan Sampah, Limbah dan
Bahan Berbahaya dan Beracun berupa
<b>{{ $barang }}</b>  tersebut dari <b>PIHAK PERTAMA</b>.
</div>

<div class="paragraph">
    Dengan diserahkannya Barang Milik Negara  Deputi Bidang Pengelolaan Sampah, Limbah dan
Bahan Berbahaya dan Beracun berupa <b>{{ $barang }}</b> tersebut dari <b>PIHAK PERTAMA</b> maka selanjutnya mengenai pengurusan, pemeliharaan, kehilangan barang milik
negara tersebut di atas menjadi tanggung jawab <b>PIHAK KEDUA</b>;
</div>

<table class="signature">
<tr>
<td>
Yang Menerima,<br>
PIHAK KEDUA<br><br><br><br><br>
<b>{{ $pihak_kedua_nama }}</b><br>
NIP. {{ $pihak_kedua_nip }}
</td>

<td>
Yang Menyerahkan,<br>
PIHAK PERTAMA<br><br><br><br><br>
<b>{{ $pihak_pertama_nama }}</b><br>
NIP. {{ $pihak_pertama_nip }}
</td>
</tr>
</table>

<div class="page-break"></div>

<div class="lampiran-title">
LAMPIRAN BERITA ACARA SERAH TERIMA<br>
BARANG MILIK NEGARA
</div>
<div style="text-align:center; margin-bottom:20px; font-weight:bold;">
Nomor: BA-&nbsp;&nbsp;&nbsp;&nbsp;/PSLB3/BMN/{{ now()->format('m/Y') }}
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
<tr>
<td>1</td>
<td>{{ $barang }}</td>
<td>{{ $tahun_perolehan }}</td>
<td>{{ $keterangan }}</td>
<td>{{ $jumlah }}</td>
<td>{{ $merk }}</td>
<td>{{ $kondisi }}</td>
<td></td>
</tr>
</tbody>
</table>

<table class="signature">
<tr>
<td>
Yang Menerima,<br>
PIHAK KEDUA<br><br><br><br><br>
<b>{{ $pihak_kedua_nama }}</b><br>
NIP. {{ $pihak_kedua_nip }}
</td>

<td>
Yang Menyerahkan,<br>
PIHAK PERTAMA<br><br><br><br><br>
<b>{{ $pihak_pertama_nama }}</b><br>
NIP. {{ $pihak_pertama_nip }}
</td>
</tr>
</table>

</body>
</html>
