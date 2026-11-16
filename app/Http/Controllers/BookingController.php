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
use App\Support\BookingAccount;
use App\Models\Appointment;
use App\Models\DiscountCode;

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

    public function store(Request $request, string $date): RedirectResponse|\Illuminate\Http\JsonResponse
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
                'in:45',
            ],
            'session_type' => [
                'required',
                'in:online,in_person',
            ],
        ]);

        /** @var User $client */
        $client = $request->user();

        // Legacy forms send all personal fields; popup requests use the saved account.
        if ($request->hasAny(['first_name', 'last_name', 'birth_date_jalali'])) {
            BookingAccount::save($request, $client);
        }

        if (! BookingAccount::complete($client)) {
            throw ValidationException::withMessages([
                'profile' => 'پیش از ثبت نوبت، نام، نام خانوادگی و تاریخ تولد حساب را تکمیل کنید.',
            ]);
        }


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

        try {
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
        } catch (ValidationException $exception) {
            $pendingAppointmentId = $exception
                ->errors()['pending_payment_appointment_id'][0]
                ?? null;

            if ($pendingAppointmentId !== null) {
                $pendingAppointment = $client->clientAppointments()
                    ->findOrFail((int) $pendingAppointmentId);

                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => 'شما یک نوبت پرداخت‌نشده دارید.',
                        'errors' => ['booking' => ['ابتدا پرداخت نوبت قبلی را تکمیل کنید.']],
                        'appointment_id' => $pendingAppointment->getKey(),
                        'confirmation_url' => route('booking.confirmation', $pendingAppointment),
                    ], 422)->header('Cache-Control', 'no-store');
                }

                return redirect()
                    ->route(
                        'booking.confirmation',
                        ['appointment' => $pendingAppointment],
                    )
                    ->withErrors([
                        'booking' =>
                            'شما یک نوبت پرداخت‌نشده دارید. ابتدا پرداخت همان نوبت را تکمیل کنید.',
                    ]);
            }


            throw $exception;
        }


        if ($request->wantsJson()) {
            return response()->json([
                'appointment_id' => $appointment->getKey(),
                'confirmation_url' => route('booking.confirmation', $appointment),
                'message' => 'نوبت موقت ثبت شد؛ تأیید نهایی منوط به پرداخت موفق است.',
            ], 201)->header('Cache-Control', 'no-store');
        }

        return redirect()
            ->route('booking.confirmation', $appointment)
            ->with(
                'status',
                'درخواست نوبت شما به‌صورت موقت ثبت شد. تأیید نهایی نوبت منوط به پرداخت موفق است.',
            );

    }
    public function confirmation(
        Request $request,
        Appointment $appointment,
    ): View {
        abort_unless(
            $request->user()?->isClient()
            && (int) $appointment->client_id
            === (int) $request->user()->getKey(),
            404,
        );

        $appointment->load([
            'psychologist:id,first_name,last_name',
            'availability:id',
        ]);

        $appliedDiscountCode = $appointment->discount_code_id !== null
            ? DiscountCode::query()
                ->whereKey($appointment->discount_code_id)
                ->value('code')
            : null;

        return view('booking.confirmation', [
            'appointment' => $appointment,
            'appliedDiscountCode' => $appliedDiscountCode,
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
        return 45;
    }
}
