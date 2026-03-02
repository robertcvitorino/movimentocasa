<?php

namespace App\Filament\Member\Resources\Certificates\Tables;

use App\Models\Certificate;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class CertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('formation.title')->label('Formacao'),
                TextColumn::make('formation.ministry.name')->label('Ministerio'),
                TextColumn::make('certificate_code')->label('Codigo'),
                TextColumn::make('issued_at')->label('Emitido em')->dateTime('d/m/Y H:i'),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Baixar PDF')
                    ->url(fn (Certificate $record): string => Storage::disk('public')->url($record->pdf_path))
                    ->openUrlInNewTab(),
            ]);
    }
}
