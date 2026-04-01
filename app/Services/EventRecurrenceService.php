<?php

namespace App\Services;

use App\Enums\EventRecurrenceType;
use App\Models\Event;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class EventRecurrenceService
{
    /**
     * @return array<int, array{id: string, start: CarbonImmutable, end: CarbonImmutable}>
     */
    public function occurrencesInRange(Event $event, CarbonImmutable $rangeStart, CarbonImmutable $rangeEnd): array
    {
        $start = CarbonImmutable::instance($event->start_datetime);
        $end = CarbonImmutable::instance($event->end_datetime);

        if (! $event->is_recurring || ! $event->recurrence_type) {
            return $this->toSingleOccurrence($event, $start, $end, $rangeStart, $rangeEnd);
        }

        $until = $event->recurrence_until
            ? CarbonImmutable::parse($event->recurrence_until)->endOfDay()
            : $rangeEnd;

        $occurrences = [];
        $currentStart = $start;
        $currentEnd = $end;
        $index = 0;

        while ($currentStart->lte($rangeEnd) && $currentStart->lte($until)) {
            if ($currentEnd->gte($rangeStart)) {
                $occurrences[] = [
                    'id' => sprintf('%s-%s', $event->getKey(), $currentStart->timestamp),
                    'start' => $currentStart,
                    'end' => $currentEnd,
                ];
            }

            [$currentStart, $currentEnd] = $this->nextOccurrence(
                $event->recurrence_type,
                $currentStart,
                $currentEnd,
            );

            if (++$index > 500) {
                break;
            }
        }

        return $occurrences;
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    protected function nextOccurrence(EventRecurrenceType $type, CarbonImmutable $start, CarbonImmutable $end): array
    {
        return match ($type) {
            EventRecurrenceType::Daily => [$start->addDay(), $end->addDay()],
            EventRecurrenceType::Weekly => [$start->addWeek(), $end->addWeek()],
            EventRecurrenceType::Monthly => [$start->addMonth(), $end->addMonth()],
            EventRecurrenceType::Yearly => [$start->addYear(), $end->addYear()],
        };
    }

    /**
     * @return array<int, array{id: string, start: CarbonImmutable, end: CarbonImmutable}>
     */
    protected function toSingleOccurrence(
        Event $event,
        CarbonImmutable $start,
        CarbonImmutable $end,
        CarbonImmutable $rangeStart,
        CarbonImmutable $rangeEnd,
    ): array {
        if ($start->gt($rangeEnd) || $end->lt($rangeStart)) {
            return [];
        }

        return [[
            'id' => (string) $event->getKey(),
            'start' => $start,
            'end' => $end,
        ]];
    }
}
