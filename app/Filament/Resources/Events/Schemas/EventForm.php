<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Filament\Support\AgendaEventForm;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components(AgendaEventForm::schema());
    }
}
