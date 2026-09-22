<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\User;
use App\Services\Booking\BookingService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use App\Support\PersianDate;

class BookingController extends Controller
{
    private const TIMEZONE = 'Asia/Tehran';

    public function __construct(
        private readonly BookingService $bookingService,
    ) {
    }

    public function index(): View
    {
        $psychologist = $this->psychologist();
        $durationMinutes = $this->requestedDuration();

        $today = CarbonImmutable::now(self::TIMEZONE)->startOfDay();

        $availabilities = Availability::query()
            ->where('psychologist_id', $psychologist->getKey())
            ->where('status', Availability::STATUS_ACTIVE)
            ->where('ends_at', '>', $today->utc())
            ->orderBy('starts_at')
            ->get();

        $candidateDates = [];

        foreach ($availabilities as $availability) {
            $firstDay = CarbonImmutable::instance($availability->starts_at)
                ->setTimezone(self::TIMEZONE)
                ->startOfDay();

            $localEnd = CarbonImmutable::instance($availability->ends_at)
                ->setTimezone(self::TIMEZONE);

            if ($firstDay->lt($today)) {
                $firstDay = $today;
            }

            for (
                $day = $firstDay;
                $day->lt($localEnd);
                $day = $day->addDay()
            ) {
                $candidateDates[$day->format('Y-m-d')] = true;
            }
        }

        $dates = collect(array_keys($candidateDates))
            ->sort()
            ->filter(fn (string $date): bool => $this->bookingService
                    ->availableSlotsForDate(
                        psychologist: $psychologist,
                        date: $date,
                        durationMinutes: $durationMinutes,
                    ) !== [])
            ->values();

        return view('booking.index', [
            'psychologist' => $psychologist,
            'dates' => $dates,
            'durationMinutes' => $durationMinutes,
        ]);
    }


    public function show(string $date): View
    {
        $psychologist = $this->psychologist();

        $validatedDate = $this->validateDate($date);

        $durationMinutes = $this->requestedDuration();

        $slots = $this->bookingService->availableSlotsForDate(
            psychologist: $psychologist,
            date: $validatedDate,
            durationMinutes: $durationMinutes,
        );

        return view('booking.show', [
            'psychologist' => $psychologist,
            'date' => $validatedDate,
            'durationMinutes' => $durationMinutes,
            'slots' => $slots,
            'sessionTypes' => [
                [
                    'value' => 'online',
                    'label' => 'آنلاین',
                    'amount' => 0,
                ],
                [
                    'value' => 'in_person',
                    'label' => 'حضوری',
                    'amount' => 0,
                ],
            ],
            'isAuthenticated' => Auth::check(),
        ]);
    }

    public function store(Request $request, string $date): RedirectResponse
    {
        $validatedDate = $this->validateDate($date);

        $validated = $request->validate([
            'availability_id' => [
                'required',
                'integer',
                'exists:availabilities,id',
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
            ],
            'duration_minutes' => [
                'required',
                'integer',
                'in:45,60',
            ],
            'session_type' => [
                'required',
                'in:online,in_person',
            ],
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
            ],
            'birth_date_jalali' => [
                'required',
                'string',
                'max:10',
            ],

        ]);
        try {
            $birthDate = PersianDate::birthDateToGregorian(
                $validated['birth_date_jalali'],
            );
        } catch (\InvalidArgumentException) {
            throw ValidationException::withMessages([
                'birth_date_jalali' =>
                    'تاریخ تولد شمسی معتبر وارد کنید؛ مانند ۱۳۷۵/۰۶/۱۵. تاریخ نباید در آینده باشد.',
            ]);
        }

        /** @var User $client */
        $client = $request->user();

        $client->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'birth_date' => $birthDate,
        ]);


        $psychologist = $this->psychologist();

        $startsAt = CarbonImmutable::createFromFormat(
            '!Y-m-d H:i',
            "{$validatedDate} {$validated['start_time']}",
            self::TIMEZONE,
        );

        if ($startsAt === false) {
            throw ValidationException::withMessages([
                'start_time' => 'زمان شروع معتبر نیست.',
            ]);
        }

        $appointment = $this->bookingService->book(
            client: $client,
            psychologist: $psychologist,
            data: [
                'availability_id' => $validated['availability_id'],
                'starts_at' => $startsAt->utc()->toIso8601String(),
                'duration_minutes' => (int) $validated['duration_minutes'],
                'session_type' => $validated['session_type'],
            ],
        );

        return redirect()
            ->route('booking.confirmation', $appointment)
            ->with(
                'status',
                'درخواست نوبت شما به‌صورت موقت ثبت شد. تأیید نهایی نوبت منوط به پرداخت موفق است.',
            );

    }
    public function confirmation(int $appointment): View
    {
        $client = request()->user();

        $appointmentModel = $client->clientAppointments()
            ->with([
                'psychologist:id,first_name,last_name',
                'availability:id',
            ])
            ->findOrFail($appointment);


        return view('booking.confirmation', [
            'appointment' => $appointmentModel,
        ]);
    }

    private function psychologist(): User
    {
        $psychologist = User::query()
            ->where('role', User::ROLE_PSYCHOLOGIST)
            ->first();

        if ($psychologist === null) {
            abort(404, 'روان‌شناس یافت نشد.');
        }

        return $psychologist;
    }

    private function validateDate(string $date): string
    {
        try {
            $parsed = CarbonImmutable::createFromFormat(
                '!Y-m-d',
                $date,
                self::TIMEZONE,
            );
        } catch (\InvalidArgumentException) {
            throw ValidationException::withMessages([
                'date' => 'تاریخ انتخاب‌شده معتبر نیست.',
            ]);
        }

        if (
            $parsed === false
            || $parsed->format('Y-m-d') !== $date
        ) {
            throw ValidationException::withMessages([
                'date' => 'تاریخ انتخاب‌شده معتبر نیست.',
            ]);
        }

        return $date;
    }


    private function requestedDuration(): int
    {
        $duration = (int) request()->query('duration', 45);

        if (! in_array($duration, [45, 60], true)) {
            return 45;
        }

        return $duration;
    }
}
