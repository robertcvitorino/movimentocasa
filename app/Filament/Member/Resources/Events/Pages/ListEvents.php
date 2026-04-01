<?php

namespace App\Filament\Member\Resources\Events\Pages;

use App\Filament\Member\Resources\Events\EventResource;
use App\Filament\Member\Widgets\MemberAgendaCalendarWidget;
use Filament\Resources\Pages\ListRecords;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MemberAgendaCalendarWidget::class,
        ];
    }
}
