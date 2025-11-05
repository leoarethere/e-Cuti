<?php

namespace App\Filament\Resources\CutiResource\Pages;

use App\Models\User;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\CutiResource; // <-- TAMBAHKAN BARIS INI
use Filament\Resources\Pages\ListRecords; // <-- TAMBAHKAN BARIS INI
use Filament\Resources\Pages\CreateRecord;
use App\Models\Employee;

class CreateCuti extends CreateRecord
{
    protected static string $resource = CutiResource::class;

    // === TAMBAHKAN FUNGSI BARU INI ===
    protected function mutateDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        
        // === LANGKAH DEBUGGING PAKSA ===
        $employee = Employee::where('user_id', $user->id)->first();
        
        dd(
            'User yang Login:', $user->id,
            'Role User:', $user->role,
            'Profil Employee yang Ditemukan:', $employee
        );
        // ================================
        
        // 1. Logika untuk mengisi ID Pegawai (jika bukan admin)
        if ($user->role !== 'admin') {
            
            // === PERUBAHAN DI SINI ===
            // Kita tidak pakai $user->employee lagi.
            // Kita cari manual ID pegawai berdasarkan ID user yang login.
            $employee = Employee::where('user_id', $user->id)->first();

            // Jika $employee ditemukan (artinya tautannya benar)
            if ($employee) {
                $data['employee_id'] = $employee->id;
            }
            // Jika $employee tidak ditemukan, data['employee_id'] akan tetap kosong
            // dan error akan tetap muncul, membuktikan tautan datanya memang bermasalah.
            // === AKHIR PERUBAHAN ===

        }

        // 2. Logika BARU (Tahap 3): Atur status awal alur kerja
        $data['status_sdm'] = 'pending';
        $data['status_tata_usaha'] = 'pending';
        $data['status_kepala'] = 'pending';
        $data['status_global'] = 'Menunggu Persetujuan SDM'; // Ini adalah titik awal alur

        return $data;
    }
}