<?php

namespace App\Filament\Resources\CutiResource\Pages;

use App\Exports\CutiExport;
use Filament\Actions\Action;
use App\Exports\FormCutiExport;
use Filament\Actions\CreateAction;
use Maatwebsite\Excel\Facades\Excel;
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

            // === TOMBOL EXPORT EXCEL REKAP ===
            Action::make('exportExcel')
                ->label('Export Rekap Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $data = $this->getFilteredTableQuery()->with('employee')->get(); 
                    
                    return Excel::download(
                        new CutiExport($data), 
                        'rekap_cuti_' . date('Y-m-d_His') . '.xlsx'
                    );
                }),

            // === TOMBOL EXPORT PDF REKAP ===
            Action::make('exportPdfRekap')
                ->label('Export Rekap PDF')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->action(function () {
                    $data = $this->getFilteredTableQuery()->with('employee')->get(); 
                    
                    return Excel::download(
                        new CutiExport($data), 
                        'rekap_cuti_' . date('Y-m-d_His') . '.pdf',
                        ExcelTypes::DOMPDF
                    );
                }),
        ];
    }
}