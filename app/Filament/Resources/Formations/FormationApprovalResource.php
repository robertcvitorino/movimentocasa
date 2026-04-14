<?php

namespace App\Filament\Resources\Formations;

use App\Enums\FormationApprovalStatus;
use App\Filament\Resources\Formations\Pages\ListFormationApprovals;
use App\Filament\Resources\Formations\Pages\ReviewFormationApproval;
use App\Models\Formation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FormationApprovalResource extends Resource
{
    protected static ?string $model = Formation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Formação';

    protected static ?string $modelLabel = 'Aprovação';

    protected static ?string $pluralModelLabel = 'Aprovações';

    protected static ?string $slug = 'formation-approvals';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('ViewAny:FormationApproval') ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('ViewAny:FormationApproval') ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('approval_status', FormationApprovalStatus::PendingReview)
            ->with(['ministry', 'creator', 'activeLessons', 'quiz.questions.options']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Formação')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('ministry.name')
                    ->label('Ministério')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Criado por')
                    ->placeholder('-'),

                TextColumn::make('submitted_for_review_at')
                    ->label('Enviado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('submitted_for_review_at', 'asc')
            ->recordUrl(fn (Formation $record): string => static::getUrl('view', ['record' => $record]))
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFormationApprovals::route('/'),
            'view'  => ReviewFormationApproval::route('/{record}'),
        ];
    }
}
