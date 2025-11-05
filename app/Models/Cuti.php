<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- Import ini

class Cuti extends Model
{
    use HasFactory;

    // Tambahkan 'status' agar bisa diisi
    protected $fillable = [
        'employee_id', // <-- INI YANG HILANG/TERLEWAT
        'jenis_cuti',
        'tanggal_mulai',
        'tanggal_akhir',
        'alasan',
        'alamat_selama_cuti',
        'status_sdm',
        'tanggapan_sdm',
        'status_tata_usaha',
        'tanggapan_tata_usaha',
        'status_kepala',
        'tanggapan_kepala',
        'status_global',
    ];

    /**
     * Mendefinisikan relasi: Satu Cuti dimiliki oleh satu Employee.
     */
    public function employee(): BelongsTo // <-- Tipe relasinya
    {
        return $this->belongsTo(Employee::class);
    }
}