<?php

namespace App\Services\Booking;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Support\PersianDate;

class AvailabilityService
{
    private const LOCAL_TIMEZONE = 'Asia/Tehran';

    /**
     * Create availability for one local calendar date.
     *
     * $date: Gregorian Y-m-d, representing a date in Tehran.
     * $startTime / $endTime: Tehran local time, H:i.
     *
     * @return Collection<int, Availability>
     */
    public function createForDate(
        User $psychologist,
        string $date,
        string $startTime,
        string $endTime,
    ): Collection {
        return $this->create(
            psychologist: $psychologist,
            input: [
                'from_date' => $date,
                'to_date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ],
            recurring: false,
        );
    }

    /**
     * Create availability across an inclusive local date range.
     *
     * Weekdays use Carbon numbering:
     * Sunday = 0, Monday = 1, ..., Saturday = 6.
     *
     * @param array<int, int> $weekdays
     * @return Collection<int, Availability>
     */
    public function createForDateRange(
        User $psychologist,
        string $fromDate,
        string $toDate,
        array $weekdays,
        string $startTime,
        string $endTime,
    ): Collection {
        return $this->create(
            psychologist: $psychologist,
            input: [
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'weekdays' => $weekdays,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ],
            recurring: true,
        );
    }

    /**
     * @param array<string, mixed> $input
     * @return Collection<int, Availability>
     */
    private function create(
        User $psychologist,
        array $input,
        bool $recurring,
    ): Collection {
        $rules = [
            'from_date' => ['required', 'date_format:Y-m-d'],
            'to_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:from_date',
            ],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
        ];

        if ($recurring) {
            $rules['weekdays'] = ['required', 'array', 'min:1', 'max:7'];
            $rules['weekdays.*'] = [
                'required',
                'integer',
                'distinct',
                Rule::in([0, 1, 2, 3, 4, 5, 6]),
            ];
        }

        $data = Validator::make($input, $rules)->validate();

        $from = CarbonImmutable::createFromFormat(
            '!Y-m-d',
            $data['from_date'],
            self::LOCAL_TIMEZONE,
        );

        $to = CarbonImmutable::createFromFormat(
            '!Y-m-d',
            $data['to_date'],
            self::LOCAL_TIMEZONE,
        );

        $today = CarbonImmutable::now(self::LOCAL_TIMEZONE)->startOfDay();

        if ($from->lt($today)) {
            throw ValidationException::withMessages([
                'from_date' => 'تاریخ شروع نمی‌تواند قبل از امروز باشد.',
            ]);
        }

        // Operational limit: at most 366 calendar dates per request.
        if ($from->diffInDays($to) > 365) {
            throw ValidationException::withMessages([
                'to_date' => 'در هر درخواست حداکثر ۳۶۶ روز قابل انتخاب است.',
            ]);
        }

        $startMinutes = $this->minutesFromMidnight($data['start_time']);
        $endMinutes = $this->minutesFromMidnight($data['end_time']);

        if ($endMinutes <= $startMinutes) {
            throw ValidationException::withMessages([
                'end_time' => 'ساعت پایان باید بعد از ساعت شروع در همان روز باشد.',
            ]);
        }

        // Availability boundaries may be arbitrary, but appointment starts
        // must be aligned to 00 or 30 minutes.
        $firstAllowedStart = (int) (ceil($startMinutes / 30) * 30);

        if ($firstAllowedStart + 45 > $endMinutes) {
            throw ValidationException::withMessages([
                'end_time' => 'این بازه برای یک جلسه ۴۵ دقیقه‌ای با شروع روی ساعت یا نیم‌ساعت کافی نیست.',
            ]);
        }

        $weekdays = $recurring
            ? array_map(
                static fn ($day): int => (int) $day,
                $data['weekdays'],
            )
            : [];

        $periods = [];

        for ($date = $from; $date->lte($to); $date = $date->addDay()) {
            if (
                $recurring
                && ! in_array($date->dayOfWeek, $weekdays, true)
            ) {
                continue;
            }

            $startsAt = $date->setTime(
                intdiv($startMinutes, 60),
                $startMinutes % 60,
            )->utc();

            $endsAt = $date->setTime(
                intdiv($endMinutes, 60),
                $endMinutes % 60,
            )->utc();

            $periods[] = [
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ];
        }

        if ($periods === []) {
            throw ValidationException::withMessages([
                'weekdays' => 'در محدوده انتخاب‌شده، تاریخی مطابق روزهای هفته وجود ندارد.',
            ]);
        }

        return DB::transaction(function () use (
            $psychologist,
            $periods,
            $recurring,
        ): Collection {
            // All booking and availability mutation services must acquire
            // this same lock before checking or changing occupied periods.
            $lockedPsychologist = User::query()
                ->whereKey($psychologist->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedPsychologist->isPsychologist()) {
                throw ValidationException::withMessages([
                    'psychologist_id' => 'کاربر انتخاب‌شده نقش روان‌شناس ندارد.',
                ]);
            }

            $now = CarbonImmutable::now('UTC');

            $seriesKey = $recurring
                ? (string) \Illuminate\Support\Str::uuid()
                : null;

            $created = collect();

            foreach ($periods as $period) {
                $startsAt = $period['starts_at'];
                $endsAt = $period['ends_at'];

                if ($startsAt->lte($now)) {
                    throw ValidationException::withMessages([
                        'start_time' => 'شروع تمام بازه‌های انتخاب‌شده باید در آینده باشد.',
                    ]);
                }

                $this->assertNoConflict(
                    psychologist: $lockedPsychologist,
                    startsAt: $startsAt,
                    endsAt: $endsAt,
                    now: $now,
                );

                $created->push(
                    Availability::query()->create([
                        'psychologist_id' => $lockedPsychologist->getKey(),
                        'series_key' => $seriesKey,
                        'starts_at' => $startsAt,
                        'ends_at' => $endsAt,
                        'status' => Availability::STATUS_ACTIVE,
                    ]),
                );
            }

            return $created;
        }, 3);
    }

    private function assertNoConflict(
        User $psychologist,
        CarbonImmutable $startsAt,
        CarbonImmutable $endsAt,
        CarbonImmutable $now,
    ): void {
        // Include disabled rows: the existing row must be edited or
        // reactivated instead of creating an overlapping duplicate.
        $availabilityConflict = Availability::query()
            ->where('psychologist_id', $psychologist->getKey())
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->lockForUpdate()
            ->first(['id']);

        if ($availabilityConflict !== null) {
            $this->throwConflict($startsAt);
        }

        $appointmentConflict = Appointment::query()
            ->where('psychologist_id', $psychologist->getKey())
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->where(function (Builder $query) use ($now): void {
                $query
                    ->whereIn('status', [
                        Appointment::STATUS_CONFIRMED,
                        Appointment::STATUS_COMPLETED,
                        Appointment::STATUS_NO_SHOW,
                    ])
                    ->orWhere(function (Builder $pending) use ($now): void {
                        $pending
                            ->where(
                                'status',
                                Appointment::STATUS_PENDING_PAYMENT,
                            )
                            ->where('hold_expires_at', '>', $now);
                    });
            })
            ->lockForUpdate()
            ->first(['id']);

        if ($appointmentConflict !== null) {
            $this->throwConflict($startsAt);
        }
    }

    private function throwConflict(CarbonImmutable $startsAt): never
    {
        $localStart = $startsAt->setTimezone(self::LOCAL_TIMEZONE);

        $formattedDate = PersianDate::format(
            $localStart,
            'yyyy/MM/dd',
        );

        throw ValidationException::withMessages([
            'availability' => sprintf(
                'بازه با شروع %s ساعت %s با برنامه موجود تداخل دارد.',
                $formattedDate,
                $localStart->format('H:i'),
            ),
        ]);
    }


    private function minutesFromMidnight(string $time): int
    {
        [$hour, $minute] = array_map('intval', explode(':', $time));

        return ($hour * 60) + $minute;
    }
}
