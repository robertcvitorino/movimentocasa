<?php

namespace App\Filament\Resources\Members\Pages\Concerns;

use App\Models\Member;

trait InteractsWithMemberUserData
{
    /**
     * @return array{full_name: string, email: string, profile_photo_path: ?string, is_active: bool}
     */
    protected function extractUserData(array $data): array
    {
        return [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'profile_photo_path' => $data['user_profile_photo_path'] ?? null,
            'is_active' => (bool) ($data['user_is_active'] ?? true),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function extractMemberData(array $data): array
    {
        unset($data['user_profile_photo_path'], $data['user_is_active']);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function fillUserDataFromMemberRecord(Member $record, array $data): array
    {
        $user = $record->user;

        $data['full_name'] = $user?->name ?? $record->full_name;
        $data['email'] = $user?->email ?? $record->email;
        $data['user_profile_photo_path'] = $user?->profile_photo_path;
        $data['user_is_active'] = $user?->is_active ?? true;

        return $data;
    }
}
