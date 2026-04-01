<?php

namespace App\Filament\Resources\Notices\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NoticesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titulo')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_published')
                    ->label('Publicado')
                    ->boolean(),
                TextColumn::make('published_at')
                    ->label('Publicado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Expira em')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(),
                TextColumn::make('creator.name')
                    ->label('Criado por')
                    ->toggleable(),
                TextColumn::make('likes_count')
                    ->label('Curtidas')
                    ->badge(),
                TextColumn::make('comments_count')
                    ->label('Comentarios')
                    ->badge(),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Publicado'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}

