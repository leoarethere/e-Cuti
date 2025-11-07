<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Form Cuti - {{ $cuti->employee->nama }}</title>
    <style>
        /* --- Konfigurasi Cetak Sesuai Permintaan --- */
        @page {
            size: A4;
            /* Margin 30mm di semua sisi */
            margin: 30mm; 
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }
        
        /* Tabel Master untuk Layout Utama */
        .master-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        .master-table > tbody > tr > td {
            vertical-align: top;
            border: 1px solid #000;
        }

        /* Kolom Header di Kiri (DATA PEGAWAI, dll) */
        .header-col {
            width: 35%;
            font-weight: bold;
            padding: 8px;
            background-color: #f0f0f0; /* Latar abu-abu untuk header */
        }

        /* Kolom Data di Kanan */
        .data-col {
            width: 65%;
            padding: 0; /* Padding 0 agar tabel nested pas */
        }

        /* Tabel Data di dalam Kolom Kanan */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd; /* Garis pemisah antar data */
            vertical-align: top;
        }
        .data-table tr:last-child td {
            border-bottom: none; /* Hapus border di baris terakhir */
        }
        .data-table .label {
            width: 40%;
            font-weight: bold;
        }
        .data-table .value {
            width: 60%;
        }

        h2 {
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }

        /* Blok Tanda Tangan */
        .signature-block {
            padding: 15px;
            text-align: left;
            margin-top: 20px;
        }
        .signature-space {
            height: 70px; /* Ruang untuk TTD */
            width: 200px;
            border: 1px dashed #ccc;
            margin-top: 5px;
            margin-bottom: 5px;
            /* Tampilkan placeholder [foto tanda tangan] seperti di konsep */
            text-align: center;
            line-height: 70px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>

    <h2>FORMULIR PERMINTAAN DAN PEMBERIAN CUTI</h2> 

    <table class="master-table">
        <tr>
            <td class="header-col">DATA PEGAWAI</td> 
            <td class="data-col">
                <table class="data-table">
                    <tr>
                        <td class="label">Nama</td>
                        <td class="value">{{ $cuti->employee->nama }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Nip</td>
                        <td class="value">{{ $cuti->employee->NIP }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Jabatan</td>
                        <td class="value">{{ $cuti->employee->jabatan }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Unit Kerja</td>
                        <td class="value">{{ $cuti->employee->unit_kerja }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Nomor Telepon</td>
                        <td class="value">{{ $cuti->employee->telp ?? '-' }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Sisa Cuti</td>
                        <td class="value">{{ $cuti->employee->sisa_cuti_tahunan }} hari</td> 
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td class="header-col">DATA PENGAJUAN CUTI</td> 
            <td class="data-col">
                <table class="data-table">
                    <tr>
                        <td class="label">Tanggal Pengajuan</td>
                        <td class="value">{{ \Carbon\Carbon::parse($cuti->created_at)->isoFormat('D MMMM Y') }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Jenis Cuti</td>
                        <td class="value">{{ $cuti->jenis_cuti }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Alasan Cuti</td>
                        <td class="value">{{ $cuti->alasan }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Tanggal Mulai</td>
                        <td class="value">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->isoFormat('D MMMM Y') }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Tanggal Akhir</td>
                        <td class="value">{{ \Carbon\Carbon::parse($cuti->tanggal_akhir)->isoFormat('D MMMM Y') }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Alamat Selama Cuti</td>
                        <td class="value">{{ $cuti->alamat_selama_cuti }}</td> 
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td class="header-col">DATA TAMBAHAN</td> 
            <td class="data-col">
                <table class="data-table">
                    <tr>
                        <td class="label">Masa Kerja</td>
                        <td class="value">{{ $masaKerja }} Tahun</td> 
                    </tr>
                    <tr>
                        <td class="label">Lama Cuti</td>
                        <td class="value">{{ $lamaCuti }} hari</td> 
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td class="header-col">DATA PERSETUJUAN</td> 
            <td class="data-col">
                <table class="data-table">
                    <tr>
                        <td class="label">Status Persetujuan SDM</td>
                        <td class="value">{{ $cuti->status_sdm }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Tanggapan Atau Catatan Dari Ketua SDM</td>
                        <td class="value">{{ $cuti->tanggapan_sdm ?? '-' }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Status Persetujuan Kasubbag Tata Usaha</td>
                        <td class="value">{{ $cuti->status_tata_usaha }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Tanggapan Atau Catatan Dari Kasubbag Tata Usaha</td>
                        <td class="value">{{ $cuti->tanggapan_tata_usaha ?? '-' }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Status Persetujuan Kepala Stasiun</td>
                        <td class="value">{{ $cuti->status_kepala }}</td> 
                    </tr>
                    <tr>
                        <td class="label">Tanggapan Atau Catatan Dari Kepala Stasiun</td>
                        <td class="value">{{ $cuti->tanggapan_kepala ?? '-' }}</td> 
                    </tr>
                </table>

                <div class="signature-block">
                    Mengetahui,<br>
                    Kepala Stasiun Tvri Yogyakarta
                    <div class="signature-space">[foto tanda tangan]</div> 
                    <strong>[nama kepala stasiun]</strong><br> 
                    <strong>[nip kepala stasiun]</strong> 
                </div>
            </td>
        </tr>

    </table>

</body>
</html>