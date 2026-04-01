<?php

namespace App\Filament\Pages;

use App\Enums\RoleName;
use Filament\Pages\Page;

class AgendaDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Agenda';

    protected static ?string $title = 'Agenda';

    protected static ?string $slug = 'agenda-dashboard';

    protected string $view = 'filament.pages.agenda-dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        return static::canManage();
    }

    protected static function canManage(): bool
    {
        return auth()->user()?->hasAnyRole([
            RoleName::SystemAdmin->value,
            RoleName::GeneralCoordinator->value,
            RoleName::MinistryCoordinator->value,
        ]) ?? false;
    }
}
