<?php

namespace App\Filament\Resources\Formations\Pages;

use App\Enums\FormationApprovalStatus;
use App\Enums\FormationStatus;
use App\Filament\Resources\Formations\FormationResource;
use App\Filament\Resources\Formations\Schemas\FormationForm;
use App\Models\Formation;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditFormation extends EditRecord
{
    protected static string $resource = FormationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('submit_for_review')
                ->label('Enviar para Aprovação')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Enviar para Aprovação')
                ->modalDescription('A formação será enviada para revisão. Após aprovação ela será publicada automaticamente.')
                ->modalSubmitActionLabel('Enviar')
                ->visible(fn (Formation $record): bool =>
                    $record->status === FormationStatus::Draft &&
                    in_array($record->approval_status, [null, FormationApprovalStatus::NeedsRefinement])
                )
                ->action(function (Formation $record): void {
                    $record->update([
                        'approval_status'         => FormationApprovalStatus::PendingReview,
                        'approval_notes'          => null,
                        'submitted_for_review_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Formação enviada para aprovação!')
                        ->body('Aguarde a revisão de um coordenador.')
                        ->success()
                        ->send();

                    $this->refreshFormData([
                        'approval_status',
                        'approval_notes',
                        'submitted_for_review_at',
                    ]);
                }),

            Action::make('awaiting_review')
                ->label('Aguardando Revisão')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->disabled()
                ->visible(fn (Formation $record): bool =>
                    $record->approval_status === FormationApprovalStatus::PendingReview
                ),

            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = FormationForm::normalizeLessonsFormData($data);
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
