<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Cuti;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // <-- Pastikan ini ada

class CutiChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Cuti Disetujui (Tahun Ini)';
    
    // (Jika Anda di v2, 'color' mungkin tidak ada, bisa dihapus jika error)
    // protected static string $color = 'success'; 
    
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // 1. Query data cuti yang 'Disetujui' tahun ini
        $data = Cuti::select(
                DB::raw('MONTH(tanggal_mulai) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('tanggal_mulai', now()->year())
            ->where('status_global', 'Disetujui') // <-- PERBAIKAN DI SINI
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // 2. Siapkan array untuk 12 bulan (default 0)
        $totals = array_fill(0, 12, 0);

        // 3. Isi array dengan data dari query
        foreach ($data as $item) {
            $totals[$item->bulan - 1] = $item->total;
        }

        // 4. Buat label untuk 12 bulan
        $labels = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create(null, $i, 1)->format('M');
        }

        // 5. Kembalikan data
        return [
            'datasets' => [
                [
                    'label' => 'Cuti Disetujui',
                    'data' => $totals,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgb(75, 192, 192)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // atau 'line' jika Anda di v3+
    }

    // Fungsi dari Tahap 5 (menyembunyikan dari pegawai)
    public static function canView(): bool
    {
        return Auth::user()->role !== 'pegawai';
    }
}