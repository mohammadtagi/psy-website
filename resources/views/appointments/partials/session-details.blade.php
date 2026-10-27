@php
    $startsAt = $appointment->starts_at
        ->copy()
        ->setTimezone('Asia/Tehran');

    $endsAt = $appointment->ends_at
        ->copy()
        ->setTimezone('Asia/Tehran');

    $meetingUrl = $appointment->meeting_url;

    $safeMeetingUrl = is_string($meetingUrl)
        && filter_var($meetingUrl, FILTER_VALIDATE_URL)
        && in_array(
            strtolower((string) parse_url($meetingUrl, PHP_URL_SCHEME)),
            ['http', 'https'],
            true
        );

    $canShowMeetingLink =
        $appointment->status === \App\Models\Appointment::STATUS_CONFIRMED
        && $appointment->session_type === \App\Models\Appointment::SESSION_TYPE_ONLINE
        && $safeMeetingUrl;
@endphp

<section class="border border-gray-200 bg-white p-5">
    <h2 class="text-lg font-semibold">اطلاعات جلسه</h2>

    <dl class="mt-4 grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <dt class="text-sm text-gray-500">کد پیگیری نوبت</dt>

            <dd class="mt-1">
        <span dir="ltr" class="inline-block break-all font-mono text-sm">
            {{ $appointment->tracking_code }}
        </span>
            </dd>
        </div>

        <div>
            <dt class="text-sm text-gray-500">تاریخ</dt>
            <dd>
                {{ \App\Support\PersianDate::format($startsAt, 'yyyy/MM/dd') }}
            </dd>
        </div>

        <div>
            <dt class="text-sm text-gray-500">ساعت</dt>
            <dd>{{ $startsAt->format('H:i') }} تا {{ $endsAt->format('H:i') }}</dd>
        </div>

        <div>
            <dt class="text-sm text-gray-500">مدت</dt>
            <dd>{{ $appointment->duration_minutes }} دقیقه</dd>
        </div>

        <div>
            <dt class="text-sm text-gray-500">شیوه برگزاری</dt>
            <dd>
                {{ $appointment->session_type === \App\Models\Appointment::SESSION_TYPE_ONLINE
                    ? 'آنلاین'
                    : 'حضوری' }}
            </dd>
        </div>

        <div>
            <dt class="text-sm text-gray-500">وضعیت</dt>
            <dd>{{ $appointment->sessionStatusLabel() }}</dd>
        </div>
    </dl>

    @if ($canShowMeetingLink)
        <a
            href="{{ $meetingUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="mt-5 inline-block border border-indigo-600 px-4 py-2 text-indigo-700"
        >
            ورود به جلسه آنلاین
        </a>
    @endif
</section>

@foreach ([
    'session_subject' => 'موضوع جلسه',
    'session_discussion' => 'موارد مطرح‌شده',
    'session_recommendations' => 'توصیه‌ها',
    'session_exercises' => 'تمرین‌ها',
] as $field => $label)
    <section class="border border-gray-200 bg-white p-5">
        <h2 class="font-semibold">{{ $label }}</h2>

        <p class="mt-3 whitespace-pre-wrap break-words text-gray-700">{{ filled($appointment->{$field}) ? $appointment->{$field} : 'هنوز ثبت نشده است.' }}</p>
    </section>
@endforeach
