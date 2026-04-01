<?php

namespace App\Filament\Resources\Notices\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class NoticeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados do aviso')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Titulo')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    FileUpload::make('cover_image_path')
                        ->label('Imagem de capa')
                        ->image()
                        ->disk('public')
                        ->directory('notices/covers')
                        ->visibility('public')
                        ->downloadable()
                        ->openable()
                        ->columnSpanFull(),
                    Toggle::make('is_published')
                        ->label('Publicado')
                        ->default(false)
                        ->live(),
                    DateTimePicker::make('published_at')
                        ->label('Publicado em')
                        ->visible(fn (Get $get): bool => (bool) $get('is_published'))
                        ->helperText('Se vazio, usa data/hora atual ao salvar.'),
                    DateTimePicker::make('expires_at')
                        ->label('Expira em')
                        ->afterOrEqual('published_at'),
                    Textarea::make('content')
                        ->label('Conteudo')
                        ->required()
                        ->rows(8)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
