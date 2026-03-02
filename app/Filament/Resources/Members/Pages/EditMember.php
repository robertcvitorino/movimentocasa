<?php

namespace App\Filament\Resources\Members\Pages;

use App\Actions\Member\SyncMemberUserAction;
use App\Filament\Resources\Members\MemberResource;
use App\Models\Member;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditMember extends EditRecord
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        /** @var Member $record */
        app(SyncMemberUserAction::class)->execute($record, $data);

        return $record;
    }
}
