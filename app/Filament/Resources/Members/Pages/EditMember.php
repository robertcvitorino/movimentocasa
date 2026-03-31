<?php

namespace App\Filament\Resources\Members\Pages;

use App\Actions\Member\SyncMemberUserAction;
use App\Filament\Resources\Members\Pages\Concerns\InteractsWithMemberUserData;
use App\Filament\Resources\Members\MemberResource;
use App\Models\Member;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditMember extends EditRecord
{
    use InteractsWithMemberUserData;

    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [

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
