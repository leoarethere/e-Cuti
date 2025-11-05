<?php

namespace App\Filament\Widgets;

use App\Models\Cuti;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget; // <-- Pastikan ini ada

class CutiStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Status-status final
        $finalStatuses = ['Disetujui', 'Ditolak (oleh SDM)', 'Ditolak (oleh Tata Usaha)', 'Ditolak (oleh Kepala)'];

        return [
            // Kartu 1: Total Pegawai
            Stat::make('Total Pegawai', Employee::count())
                ->description('Jumlah total pegawai terdaftar')
                ->icon('heroicon-o-users')
                ->color('primary'),

            // Kartu 2: Cuti Pending (Logika BARU)
            Stat::make('Pengajuan Pending', 
                    Cuti::whereNotIn('status_global', $finalStatuses)->count()
                )
                ->description('Cuti yang menunggu persetujuan')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            // Kartu 3: Cuti Disetujui (Logika BARU)
            Stat::make('Cuti Disetujui (Tahun Ini)', 
                    Cuti::where('status_global', 'Disetujui') // <-- Cari status 'Disetujui'
                        ->whereYear('tanggal_mulai', now()->year())
                        ->count()
                )
                ->description('Total cuti yang telah disetujui')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }

    // Fungsi dari Tahap 5 (menyembunyikan dari pegawai)
    public static function canView(): bool
    {
        return Auth::user()->role !== 'pegawai';
    }
}