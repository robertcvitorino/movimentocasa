<?php

namespace App\Filament\Support;

use App\Enums\EventAudienceType;
use App\Enums\EventRecurrenceType;
use App\Models\Member;
use App\Models\Ministry;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class AgendaEventForm
{
    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('Dados do evento')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Titulo')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('location')
                        ->label('Local')
                        ->maxLength(255),
                    DateTimePicker::make('start_datetime')
                        ->label('Inicio')
                        ->required(),
                    DateTimePicker::make('end_datetime')
                        ->label('Fim')
                        ->required()
                        ->afterOrEqual('start_datetime'),
                    ColorPicker::make('color')
                        ->label('Cor do evento')
                        ->default('#2563eb'),
                    Textarea::make('description')
                        ->label('Descricao')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
            Section::make('Recorrencia')
                ->columns(3)
                ->schema([
                    Toggle::make('is_recurring')
                        ->label('Evento recorrente')
                        ->live(),
                    Select::make('recurrence_type')
                        ->label('Tipo de recorrencia')
                        ->options(
                            collect(EventRecurrenceType::cases())
                                ->mapWithKeys(fn (EventRecurrenceType $type) => [$type->value => $type->label()])
                        )
                        ->visible(fn (Get $get): bool => (bool) $get('is_recurring'))
                        ->required(fn (Get $get): bool => (bool) $get('is_recurring')),
                    DatePicker::make('recurrence_until')
                        ->label('Recorre ate')
                        ->visible(fn (Get $get): bool => (bool) $get('is_recurring'))
                        ->required(fn (Get $get): bool => (bool) $get('is_recurring')),
                ]),
            Section::make('Destinatarios')
                ->columns(2)
                ->schema([
                    Select::make('audience_type')
                        ->label('Quem sera convidado')
                        ->options(
                            collect(EventAudienceType::cases())
                                ->mapWithKeys(fn (EventAudienceType $type) => [$type->value => $type->label()])
                        )
                        ->default(EventAudienceType::General->value)
                        ->live()
                        ->dehydrated(false)
                        ->required(),
                    Select::make('ministry_ids')
                        ->label('Ministerios')
                        ->options(fn (): array => Ministry::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->visible(fn (Get $get): bool => $get('audience_type') === EventAudienceType::Ministry->value)
                        ->dehydrated(fn (Get $get): bool => $get('audience_type') === EventAudienceType::Ministry->value),
                    Select::make('member_ids')
                        ->label('Membros')
                        ->options(fn (): array => Member::query()->orderBy('full_name')->pluck('full_name', 'id')->all())
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->visible(fn (Get $get): bool => $get('audience_type') === EventAudienceType::Members->value)
                        ->dehydrated(fn (Get $get): bool => $get('audience_type') === EventAudienceType::Members->value),
                ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{audience_type: EventAudienceType, ministry_ids: array<int, int>, member_ids: array<int, int>}
     */
    public static function extractAudienceData(array &$data): array
    {
        $audienceType = EventAudienceType::tryFrom((string) ($data['audience_type'] ?? EventAudienceType::General->value))
            ?? EventAudienceType::General;

        $ministryIds = collect($data['ministry_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();

        $memberIds = collect($data['member_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();

        unset($data['audience_type'], $data['ministry_ids'], $data['member_ids']);

        if (! ($data['is_recurring'] ?? false)) {
            $data['recurrence_type'] = null;
            $data['recurrence_until'] = null;
        }

        return [
            'audience_type' => $audienceType,
            'ministry_ids' => $ministryIds,
            'member_ids' => $memberIds,
        ];
    }
}
