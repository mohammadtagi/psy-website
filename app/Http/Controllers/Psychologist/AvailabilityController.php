<?php

namespace App\Http\Controllers\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use App\Services\Booking\AvailabilityService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Support\PersianDate;
use InvalidArgumentException;

class AvailabilityController extends Controller
{
    private const TIMEZONE = 'Asia/Tehran';

    public function __construct(
        private readonly AvailabilityService $availabilityService,
    ) {
    }

    public function index(Request $request): View
    {
        /** @var User $psychologist */
        $psychologist = $request->user();

        $today = CarbonImmutable::now(self::TIMEZONE)
            ->startOfDay()
            ->utc();

        $availabilities = Availability::query()
            ->where('psychologist_id', $psychologist->getKey())
            ->where('ends_at', '>', $today)
            ->withCount([
                'appointments as active_appointments_count' => function ($query): void {
                    $query->whereIn('status', [
                        Appointment::STATUS_PENDING_PAYMENT,
                        Appointment::STATUS_CONFIRMED,
                    ]);
                },
            ])
            ->orderBy('starts_at')
            ->paginate(30)
            ->withQueryString();

        return view('psychologist.availabilities.index', [
            'psychologist' => $psychologist,
            'availabilities' => $availabilities,
            'timezone' => self::TIMEZONE,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $psychologist */
        $psychologist = $request->user();

        $validated = $request->validate([
            'mode' => [
                'required',
                'string',
                'in:single,recurring',
            ],
            'from_date' => [
                'required',
                'string',
                'max:10',
            ],
            'to_date' => [
                'required',
                'string',
                'max:10',
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
            ],
            'weekdays' => [
                'nullable',
                'array',
            ],
            'weekdays.*' => [
                'integer',
                'distinct',
                'between:0,6',
            ],
        ]);
        try {
            $fromDate = PersianDate::toGregorian($validated['from_date']);
            $toDate = PersianDate::toGregorian($validated['to_date']);
        } catch (InvalidArgumentException) {
            throw ValidationException::withMessages([
                'from_date' => 'تاریخ واردشده باید به‌صورت شمسی معتبر باشد؛ مانند ۱۴۰۵/۰۷/۰۱.',
            ]);
        }

        if ($toDate < $fromDate) {
            throw ValidationException::withMessages([
                'to_date' => 'تاریخ پایان باید برابر یا بعد از تاریخ شروع باشد.',
            ]);
        }
        if ($validated['mode'] === 'single') {
            $this->availabilityService->createForDate(
                psychologist: $psychologist,
                date: $fromDate,                        // ✅ میلادی
                startTime: $validated['start_time'],
                endTime: $validated['end_time'],
            );
        } else {
            $this->availabilityService->createForDateRange(
                psychologist: $psychologist,
                fromDate: $fromDate,                    // ✅ میلادی
                toDate: $toDate,                        // ✅ میلادی
                weekdays: array_map(
                    'intval',
                    $validated['weekdays'] ?? [],
                ),
                startTime: $validated['start_time'],
                endTime: $validated['end_time'],
            );
        }


        return redirect()
            ->route('psychologist.availabilities.index')
            ->with('status', 'بازه حضور با موفقیت ایجاد شد.');
    }

    public function destroy(
        Request $request,
        Availability $availability,
    ): RedirectResponse {
        /** @var User $psychologist */
        $psychologist = $request->user();

        if (
            (int) $availability->psychologist_id
            !== (int) $psychologist->getKey()
        ) {
            abort(404);
        }

        DB::transaction(function () use (
            $psychologist,
            $availability,
        ): void {
            User::query()
                ->whereKey($psychologist->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $lockedAvailability = Availability::query()
                ->whereKey($availability->getKey())
                ->where('psychologist_id', $psychologist->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $hasActiveAppointment = Appointment::query()
                ->where('availability_id', $lockedAvailability->getKey())
                ->whereIn('status', [
                    Appointment::STATUS_PENDING_PAYMENT,
                    Appointment::STATUS_CONFIRMED,
                ])
                ->lockForUpdate()
                ->exists();

            if ($hasActiveAppointment) {
                throw ValidationException::withMessages([
                    'availability' => 'این بازه دارای نوبت فعال است و حذف نمی‌شود.',
                ]);
            }

            $lockedAvailability->update([
                'status' => Availability::STATUS_DISABLED,
            ]);
        }, 3);

        return redirect()
            ->route('psychologist.availabilities.index')
            ->with('status', 'بازه حضور غیرفعال شد.');
    }
}
