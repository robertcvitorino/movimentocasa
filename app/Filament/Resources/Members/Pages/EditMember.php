<?php

namespace App\Filament\Resources\Members\Pages;

use App\Actions\Member\SendMemberPasswordResetAction;
use App\Actions\Member\SyncMemberUserAction;
use App\Filament\Resources\Members\Pages\Concerns\InteractsWithMemberUserData;
use App\Filament\Resources\Members\MemberResource;
use App\Models\Member;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class EditMember extends EditRecord
{
    use InteractsWithMemberUserData;

    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reset_password')
                ->label('Resetar senha')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Enviar redefinicao de senha')
                ->modalDescription('Um e-mail com o link para redefinir a senha sera enviado para o membro.')
                ->action(function (): void {
                    try {
                        /** @var Member $record */
                        $record = $this->getRecord();

                        app(SendMemberPasswordResetAction::class)->execute($record);

                        Notification::make()
                            ->title('E-mail de redefinicao enviado com sucesso.')
                            ->success()
                            ->send();
                    } catch (ValidationException $exception) {
                        Notification::make()
                            ->title(collect($exception->errors())->flatten()->first() ?? 'Nao foi possivel enviar o e-mail.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Member $record */
        $record = $this->getRecord();

        return $this->fillUserDataFromMemberRecord($record, $data);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Member $record */
        $record->update([
            ...$this->extractMemberData($data),
            'full_name' => $data['full_name'],
            'email' => $data['email'],
        ]);

        app(SyncMemberUserAction::class)->execute($record, $this->extractUserData($data));

        return $record;
    }
}
