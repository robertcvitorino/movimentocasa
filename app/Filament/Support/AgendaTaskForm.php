<?php

namespace App\Filament\Support;

use App\Enums\TaskPriority;
use App\Enums\TaskResponsibleType;
use App\Models\Member;
use App\Models\Ministry;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class AgendaTaskForm
{
    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('Dados da tarefa')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Titulo')
                        ->required()
                        ->maxLength(255),
                    Select::make('priority')
                        ->label('Prioridade')
                        ->options(
                            collect(TaskPriority::cases())
                                ->mapWithKeys(fn (TaskPriority $priority) => [$priority->value => $priority->label()])
                                ->all()
                        )
                        ->default(TaskPriority::Medium->value)
                        ->required(),
                    DateTimePicker::make('start_datetime')
                        ->label('Inicio')
                        ->required(),
                    DateTimePicker::make('end_datetime')
                        ->label('Fim')
                        ->required()
                        ->afterOrEqual('start_datetime'),
                    Select::make('ministry_id')
                        ->label('Ministerio')
                        ->options(fn (): array => Ministry::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->preload(),
                    Select::make('responsible_type')
                        ->label('Responsavel')
                        ->options(
                            collect(TaskResponsibleType::cases())
                                ->mapWithKeys(fn (TaskResponsibleType $type) => [$type->value => $type->label()])
                                ->all()
                        )
                        ->default(TaskResponsibleType::Member->value)
                        ->live()
                        ->required(),
                    Select::make('responsible_member_id')
                        ->label('Pessoa responsavel')
                        ->options(fn (): array => Member::query()->orderBy('full_name')->pluck('full_name', 'id')->all())
                        ->searchable()
                        ->preload()
                        ->visible(fn (Get $get): bool => $get('responsible_type') === TaskResponsibleType::Member->value)
                        ->required(fn (Get $get): bool => $get('responsible_type') === TaskResponsibleType::Member->value),
                    Select::make('responsible_ministry_id')
                        ->label('Ministerio responsavel')
                        ->options(fn (): array => Ministry::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->preload()
                        ->visible(fn (Get $get): bool => $get('responsible_type') === TaskResponsibleType::Ministry->value)
                        ->required(fn (Get $get): bool => $get('responsible_type') === TaskResponsibleType::Ministry->value),
                    FileUpload::make('attachment_path')
                        ->label('Anexo')
                        ->disk('public')
                        ->directory('tasks/attachments')
                        ->visibility('public')
                        ->downloadable()
                        ->openable()
                        ->preserveFilenames(),
                    Textarea::make('description')
                        ->label('Descricao')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeResponsibility(array $data): array
    {
        $responsibleType = TaskResponsibleType::tryFrom((string) ($data['responsible_type'] ?? TaskResponsibleType::Member->value))
            ?? TaskResponsibleType::Member;

        $data['responsible_type'] = $responsibleType->value;

        if ($responsibleType === TaskResponsibleType::Member) {
            $data['responsible_ministry_id'] = null;
        } else {
            $data['responsible_member_id'] = null;
        }

        return $data;
    }
}
