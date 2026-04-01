<?php

namespace App\Filament\Member\Widgets;

use App\Filament\Support\CalendarPresentation;
use App\Models\Event;
use App\Models\Task;
use App\Services\EventRecurrenceService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class MemberAgendaCalendarWidget extends CalendarWidget
{
    protected string|HtmlString|bool|null $heading = 'Agenda';

    protected bool $eventClickEnabled = false;

    protected ?string $locale = 'pt-BR';

    protected bool $useFilamentTimezone = true;

    protected array $options = CalendarPresentation::DEFAULT_OPTIONS;

    protected function getEvents(FetchInfo $info): Collection|array|Builder
    {
        $member = auth()->user()?->member;

        if (! $member) {
            return [];
        }

        $recurrenceService = app(EventRecurrenceService::class);
        $rangeStart = CarbonImmutable::parse($info->start);
        $rangeEnd = CarbonImmutable::parse($info->end);

        $events = Event::query()
            ->visibleToMember($member)
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
                        ->action('view')
                        ->extendedProp('occurrence_id', $occurrence['id'])
                );
            }
        }

        $tasks = Task::query()
            ->with(['ministry', 'responsibleMember', 'responsibleMinistry'])
            ->visibleToUser(auth()->user())
            ->where('start_datetime', '<=', $rangeEnd)
            ->where('end_datetime', '>=', $rangeStart)
            ->get();

        foreach ($tasks as $task) {
            $calendarEvents->push(
                CalendarEvent::make($task)
                    ->title('Tarefa: ' . $task->title)
                    ->start(Carbon::instance($task->start_datetime->toMutable()))
                    ->end(Carbon::instance($task->end_datetime->toMutable()))
                    ->backgroundColor($task->resolveCalendarColor())
                    ->action('view')
            );
        }

        return $calendarEvents;
    }
}
