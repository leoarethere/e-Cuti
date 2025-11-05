<?php

namespace App\Exports;

use App\Models\Cuti; // <-- Import model
use Illuminate\Support\Collection; // <-- Import Collection
use Maatwebsite\Excel\Concerns\WithMapping; // <-- Gunakan FromCollection
use Maatwebsite\Excel\Concerns\WithHeadings; // <-- Untuk header
use Maatwebsite\Excel\Concerns\FromCollection; // <-- Untuk memformat data

class CutiExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @param Collection $records
    */
    public function __construct(protected Collection $records)
    {
        // Kita menerima data yang sudah difilter
    }

    /**
    * @return Collection
    */
    public function collection()
    {
        // Langsung kembalikan data yang sudah diterima
        return $this->records;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Ini adalah baris header di file Excel
        return [
            'ID Cuti',
            'NIP Pegawai',
            'Nama Pegawai',
            'Jabatan',
            'Tanggal Mulai',
            'Tanggal Akhir',
            'Status',
            'Alasan',
        ];
    }

    /**
     * @param Cuti $cuti
     * @return array
     */
    public function map($cuti): array
    {
        // Memetakan setiap baris data
        return [
            $cuti->id,
            $cuti->employee->NIP,       // Ambil dari relasi
            $cuti->employee->nama,      // Ambil dari relasi
            $cuti->employee->jabatan,   // Ambil dari relasi
            $cuti->tanggal_mulai,
            $cuti->tanggal_akhir,
            ucfirst($cuti->status),
            $cuti->alasan,
        ];
    }
}