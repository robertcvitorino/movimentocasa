<?php

namespace App\Filament\Widgets;

use App\Filament\Support\AgendaEventForm;
use App\Filament\Support\CalendarPresentation;
use App\Models\Event;
use App\Services\EventRecurrenceService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Filament\Schemas\Schema;
use Guava\Calendar\Filament\Actions\CreateAction;
use Guava\Calendar\Filament\Actions\DeleteAction;
use Guava\Calendar\Filament\Actions\EditAction;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\DateClickInfo;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class AdminAgendaCalendarWidget extends CalendarWidget
{
    protected string|HtmlString|bool|null $heading = 'Calendario';

    protected bool $dateClickEnabled = true;

    protected bool $eventClickEnabled = true;

    protected ?string $defaultEventClickAction = 'edit';

    protected ?string $locale = 'pt-BR';

    protected array $options = CalendarPresentation::DEFAULT_OPTIONS;

    /**
     * @var array{audience_type: \App\Enums\EventAudienceType, ministry_ids: array<int, int>, member_ids: array<int, int>}
     */
    protected array $audienceData = [
        'audience_type' => \App\Enums\EventAudienceType::General,
        'ministry_ids' => [],
        'member_ids' => [],
    ];

    protected function getEvents(FetchInfo $info): Collection|array|Builder
    {
        $recurrenceService = app(EventRecurrenceService::class);
        $rangeStart = CarbonImmutable::parse($info->start);
        $rangeEnd = CarbonImmutable::parse($info->end);

        $events = Event::query()
            ->with(['ministries', 'members'])
            ->where('start_datetime', '<=', $rangeEnd)
            ->where(function ($query) use ($rangeStart): void {
                $query->where('end_datetime', '>=', $rangeStart)
                    ->orWhere(function ($recurrenceQuery) use ($rangeStart): void {
                        $recurrenceQuery->where('is_recurring', true)
                            ->whereDate('start_datetime', '<=', $rangeStart)
                            ->where(function ($untilQuery) use ($rangeStart): void {
                                $untilQuery->whereNull('recurrence_until')
                                    ->orWhereDate('recurrence_until', '>=', $rangeStart);
                            });
                    });
            })
            ->get();

        $calendarEvents = collect();

        foreach ($events as $event) {
            foreach ($recurrenceService->occurrencesInRange($event, $rangeStart, $rangeEnd) as $occurrence) {
                $calendarEvents->push(
                    CalendarEvent::make($event)
                        ->title($event->title)
                        ->start(Carbon::instance($occurrence['start']->toMutable()))
                        ->end(Carbon::instance($occurrence['end']->toMutable()))
                        ->backgroundColor($event->resolveCalendarColor())
                        ->action('edit')
                        ->extendedProp('occurrence_id', $occurrence['id'])
                );
            }
        }

        return $calendarEvents;
    }

    protected function onDateClick(DateClickInfo $info): void
    {
        $this->mountAction('createEvent');
    }

    public function eventSchema(Schema $schema): Schema
    {
        return $schema->components(AgendaEventForm::schema());
    }

    public function createEventAction(): CreateAction
    {
        return $this->createAction(Event::class, 'createEvent')
            ->label('Criar evento')
            ->createAnother(false)
            ->mountUsing(function (Schema $schema): void {
                $clickedDate = $this->getRawCalendarContextData('date');
                $start = filled($clickedDate)
                    ? CarbonImmutable::parse((string) $clickedDate)
                    : now();
                $end = $start->addHour();

                $schema->fill([
                    'start_datetime' => $start,
                    'end_datetime' => $end,
                ]);
            })
            ->mutateDataUsing(function (array $data): array {
                $this->audienceData = AgendaEventForm::extractAudienceData($data);
                $data['created_by'] = auth()->id();

                return $data;
            })
            ->after(function (Event $record): void {
                $record->syncAudience(
                    $this->audienceData['audience_type'],
                    $this->audienceData['ministry_ids'],
                    $this->audienceData['member_ids'],
                );

                $this->refreshRecords();
            });
    }

    public function editAction(): EditAction
    {
        return parent::editAction()
            ->mutateRecordDataUsing(function (array $data, Event $record): array {
                $record->loadMissing(['ministries', 'members']);

                $data['audience_type'] = $record->resolveAudienceType()->value;
                $data['ministry_ids'] = $record->ministries->pluck('id')->all();
                $data['member_ids'] = $record->members->pluck('id')->all();

                return $data;
            })
            ->mutateDataUsing(function (array $data): array {
                $this->audienceData = AgendaEventForm::extractAudienceData($data);

                return $data;
            })
            ->after(function (Event $record): void {
                $record->syncAudience(
                    $this->audienceData['audience_type'],
                    $this->audienceData['ministry_ids'],
                    $this->audienceData['member_ids'],
                );

                $this->refreshRecords();
            });
    }

    public function deleteAction(): DeleteAction
    {
        return parent::deleteAction();
    }

    public function getHeaderActions(): array
    {
        return [
            $this->createEventAction(),
        ];
    }
}
