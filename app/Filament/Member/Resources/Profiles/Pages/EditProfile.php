<?php

namespace App\Filament\Member\Resources\Profiles\Pages;

use App\Actions\Member\SyncMemberUserAction;
use App\Filament\Member\Resources\Profiles\ProfileResource;
use App\Models\Member;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProfile extends EditRecord
{
    protected static string $resource = ProfileResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Member $record */
        $record = $this->getRecord();

        $data['user_profile_photo_path'] = $record->user?->profile_photo_path;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Member $record */
        $memberData = collect($data)
            ->except(['user_profile_photo_path'])
            ->all();

        $record->update($memberData);

        app(SyncMemberUserAction::class)->execute($record, [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'profile_photo_path' => $data['user_profile_photo_path'] ?? null,
            'is_active' => $record->user?->is_active ?? true,
        ]);

        return $record;
    }
}
