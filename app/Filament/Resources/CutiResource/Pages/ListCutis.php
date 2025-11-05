<?php

namespace App\Filament\Resources\CutiResource\Pages;

use App\Exports\CutiExport;
use Filament\Actions\Action;

// === IMPOR YANG BENAR UNTUK V3 ===
use Filament\Actions\CreateAction; // <-- Ini dari Filament\Actions (v3)
use Maatwebsite\Excel\Facades\Excel; // <-- Ini dari Filament\Actions (v3)
use Maatwebsite\Excel\Excel as ExcelTypes;
use App\Filament\Resources\CutiResource;
use Filament\Resources\Pages\ListRecords; 

class ListCutis extends ListRecords
{
    protected static string $resource = CutiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(), 

            // === TOMBOL EXPORT EXCEL (v3) ===
            Action::make('exportExcel')
                ->label('Export Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    // === INI PERBAIKAN V3 ===
                    $data = $this->getFilteredTableQuery()->with('employee')->get(); 
                    
                    return Excel::download(
                        new CutiExport($data), 
                        'rekap_cuti.xlsx'
                    );
                }),

            // === TOMBOL EXPORT PDF (v3) ===
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    // === INI PERBAIKAN V3 ===
                    $data = $this->getFilteredTableQuery()->with('employee')->get(); 
                    
                    return Excel::download(
                        new CutiExport($data), 
                        'rekap_cuti.pdf',
                        ExcelTypes::DOMPDF
                    );
                }),
        ];
    }
}