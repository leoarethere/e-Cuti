<?php

namespace App\Filament\Resources\CutiResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\CutiResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\CreateRecord;

class CreateCuti extends CreateRecord
{
    protected static string $resource = CutiResource::class;

    /**
     * Method ini dipanggil SEBELUM form di-render
     * Untuk mengisi default value employee_id
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = Auth::user();
        
        // Jika bukan admin, isi employee_id otomatis
        if ($user->role !== 'admin') {
            $employee = Employee::where('user_id', $user->id)->first();
            
            if ($employee) {
                $data['employee_id'] = $employee->id;
            }
        }
        
        return $data;
    }

    /**
     * Method ini dipanggil SEBELUM data disimpan ke database
     * Ini adalah langkah terakhir sebelum INSERT
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        
        // 1. Logika untuk mengisi ID Pegawai (jika bukan admin)
        if ($user->role !== 'admin') {
            
            // Cari ID pegawai berdasarkan user_id yang login
            $employee = Employee::where('user_id', $user->id)->first();

            if ($employee) {
                $data['employee_id'] = $employee->id;
            } else {
                // Jika tidak ada employee terkait, tampilkan error
                Notification::make()
                    ->title('Error: Profil Pegawai Tidak Ditemukan')
                    ->body("Akun Anda ({$user->name}) belum terhubung dengan data pegawai. Silakan hubungi admin untuk menautkan akun Anda.")
                    ->danger()
                    ->persistent()
                    ->send();
                
                // Hentikan proses dan redirect kembali
                $this->halt();
            }
        }

        // 2. Atur status awal alur kerja
        $data['status_sdm'] = 'pending';
        $data['status_tata_usaha'] = 'pending';
        $data['status_kepala'] = 'pending';
        $data['status_global'] = 'Menunggu Persetujuan SDM';

        return $data;
    }

    /**
     * Method alternatif: Hook setelah form divalidasi
     */
    protected function afterValidate(): void
    {
        $user = Auth::user();
        
        // Double-check: Pastikan employee_id terisi
        if ($user->role !== 'admin') {
            $employee = Employee::where('user_id', $user->id)->first();
            
            if (!$employee) {
                Notification::make()
                    ->title('Akun Tidak Terhubung')
                    ->body('Silakan hubungi administrator untuk menautkan akun Anda dengan data pegawai.')
                    ->danger()
                    ->persistent()
                    ->send();
                    
                $this->halt();
            }
        }
    }
}