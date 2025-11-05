<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\Cuti; // Untuk email notifikasi
use Filament\Tables; // Untuk kalkulasi durasi
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

// === Komponen Form ===
use App\Notifications\CutiStatusUpdated;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;

// === Komponen Tabel ===
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CutiResource\Pages;

class CutiResource extends Resource
{
    protected static ?string $model = Cuti::class;

    // Navigasi
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $modelLabel = 'Pengajuan Cuti';
    protected static ?string $pluralModelLabel = 'Pengajuan Cuti';
    protected static ?int $navigationSort = 2;

    
    /**
     * Form Pengajuan Cuti (dari Tahap 3)
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('employee_id')
                    ->label('Nama Pegawai')
                    ->relationship('employee', 'nama')
                    ->searchable()
                    ->required(fn () => Auth::user()->role === 'admin')
                    ->visible(fn () => Auth::user()->role === 'admin'),

                Select::make('jenis_cuti')
                    ->label('Jenis Cuti yang Diambil')
                    ->options([
                        'Cuti Tahunan' => '1. Cuti Tahunan',
                        'Cuti Besar' => '2. Cuti Besar',
                        'Cuti Sakit' => '3. Cuti Sakit',
                        'Cuti Melahirkan' => '4. Cuti Melahirkan',
                        'Cuti Alasan Penting' => '5. Cuti Alasan Penting',
                        'Cuti Luar Tanggungan' => '6. Cuti diluar Tanggungan Negara',
                    ])
                    ->required(),

                DatePicker::make('tanggal_mulai')
                    ->required()
                    ->native(false),
                DatePicker::make('tanggal_akhir')
                    ->required()
                    ->native(false)
                    ->afterOrEqual('tanggal_mulai'),

                Textarea::make('alasan')
                    ->label('Alasan Cuti')
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('alamat_selama_cuti')
                    ->label('Alamat Selama Menjalankan Cuti')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Tabel List Cuti (Logika BARU)
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.nama')
                    ->label('Nama Pegawai')
                    ->sortable()
                    ->searchable()
                    ->visible(fn () => Auth::user()->role !== 'pegawai'),

                TextColumn::make('jenis_cuti')
                    ->searchable(),
                
                TextColumn::make('tanggal_mulai')
                    ->date()
                    ->sortable(),

                TextColumn::make('tanggal_akhir')
                    ->date()
                    ->sortable(),
                
                BadgeColumn::make('status_global')
                    ->label('Status')
                    ->colors([
                        'warning' => fn ($state) => $state !== 'Disetujui' && $state !== 'Ditolak',
                        'success' => 'Disetujui',
                        'danger' => fn ($state) => str_contains($state, 'Ditolak'),
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                // ===================================
                // === AKSI TAHAP 1: KETUA TIM SDM ===
                // ===================================
                Action::make('Proses SDM')
                    ->icon('heroicon-o-pencil')
                    ->color('info')
                    ->visible(function (Cuti $record) {
                        return Auth::user()->role === 'sdm' && $record->status_global === 'Menunggu Persetujuan SDM';
                    })
                    ->form([
                        Select::make('status_sdm')
                            ->label('Persetujuan SDM')
                            ->options(['approved' => 'Disetujui', 'rejected' => 'Ditolak'])
                            ->required(),
                        Textarea::make('tanggapan_sdm')
                            ->label('Catatan / Tanggapan SDM'),
                    ])
                    ->action(function (Cuti $record, array $data): void {
                        $record->status_sdm = $data['status_sdm'];
                        $record->tanggapan_sdm = $data['tanggapan_sdm'];

                        if ($data['status_sdm'] === 'approved') {
                            $record->status_global = 'Menunggu Persetujuan Tata Usaha';
                        } else {
                            $record->status_global = 'Ditolak (oleh SDM)';
                        }
                        $record->save();
                        
                        // !! NOTIFIKASI DIHAPUS DARI SINI !!
                    }),

                // =======================================
                // === AKSI TAHAP 2: KASUBBAG TATA USAHA ===
                // =======================================
                Action::make('Proses Tata Usaha')
                    ->icon('heroicon-o-pencil')
                    ->color('info')
                    ->visible(function (Cuti $record) {
                        return Auth::user()->role === 'tata_usaha' && $record->status_global === 'Menunggu Persetujuan Tata Usaha';
                    })
                    ->form([
                        Select::make('status_tata_usaha')
                            ->label('Persetujuan Kasubbag Tata Usaha')
                            ->options(['approved' => 'Disetujui', 'rejected' => 'Ditolak'])
                            ->required(),
                        Textarea::make('tanggapan_tata_usaha')
                            ->label('Catatan / Tanggapan'),
                    ])
                    ->action(function (Cuti $record, array $data): void {
                        $record->status_tata_usaha = $data['status_tata_usaha'];
                        $record->tanggapan_tata_usaha = $data['tanggapan_tata_usaha'];

                        if ($data['status_tata_usaha'] === 'approved') {
                            $record->status_global = 'Menunggu Persetujuan Kepala';
                        } else {
                            $record->status_global = 'Ditolak (oleh Tata Usaha)';
                        }
                        $record->save();
                    }),
                
                // =====================================
                // === AKSI TAHAP 3: KEPALA STASIUN  ===
                // =====================================
                Action::make('Proses Kepala')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(function (Cuti $record) {
                        return Auth::user()->role === 'kepala_stasiun' && $record->status_global === 'Menunggu Persetujuan Kepala';
                    })
                    ->form([
                        Select::make('status_kepala')
                            ->label('Keputusan Kepala Stasiun')
                            ->options(['approved' => 'Disetujui', 'rejected' => 'Ditolak'])
                            ->required(),
                        Textarea::make('tanggapan_kepala')
                            ->label('Catatan / Tanggapan'),
                    ])
                    ->action(function (Cuti $record, array $data): void {
                        $record->status_kepala = $data['status_kepala'];
                        $record->tanggapan_kepala = $data['tanggapan_kepala'];

                        $employee = $record->employee;

                        if ($data['status_kepala'] === 'approved') {
                            $durasiCuti = Carbon::parse($record->tanggal_mulai)
                                              ->diffInDays(Carbon::parse($record->tanggal_akhir)) + 1;
                            
                            if ($record->jenis_cuti === 'Cuti Tahunan') {
                                if ($employee->sisa_cuti_tahunan < $durasiCuti) {
                                    Notification::make()
                                        ->title('Gagal! Sisa Cuti Tahunan tidak mencukupi.')
                                        ->body("Pegawai {$employee->nama} hanya memiliki {$employee->sisa_cuti_tahunan} hari.")
                                        ->danger()
                                        ->send();
                                    return;
                                }
                                $employee->sisa_cuti_tahunan -= $durasiCuti;
                                $employee->save();
                            }

                            $record->status_global = 'Disetujui';
                        } else {
                            $record->status_global = 'Ditolak (oleh Kepala)';
                        }
                        
                        $record->save();
                        
                        // Notifikasi HANYA dikirim setelah keputusan final.
                        $employee->notify(new CutiStatusUpdated($record));
                    }),
                
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * "OTAK" FILTER (Logika BARU)
     */
    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery();

        switch ($user->role) {
            case 'pegawai':
                return $query->where('employee_id', $user->employee->id);
            
            case 'sdm':
                return $query->where('status_global', 'Menunggu Persetujuan SDM');
            
            case 'tata_usaha':
                return $query->where('status_global', 'Menunggu Persetujuan Tata Usaha');
            
            case 'kepala_stasiun':
                return $query->where('status_global', 'Menunggu Persetujuan Kepala');

            case 'admin':
                return $query;
            
            default:
                return $query->where('id', 0);
        }
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCutis::route('/'),
            'create' => Pages\CreateCuti::route('/create'),
            'edit' => Pages\EditCuti::route('/{record}/edit'),
        ];
    }
}