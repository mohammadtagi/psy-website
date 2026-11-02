@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('website/style/care-manual-picker.css') }}?v=1">
    @endpush
    <script src="{{ asset('website/js/care-manual-picker.js') }}?v=1" defer></script>
@endonce
<section id="manual-session" class="cr-panel cr-wide">
    <h2 class="mb-2 text-lg font-bold">
        ثبت دستی جلسه
    </h2>

    <p class="mb-5 text-sm text-gray-600">
        جلسه‌ای که زمان پایان آن گذشته باشد، برگزارشده ثبت می‌شود؛
        سایر جلسات در انتظار برگزاری خواهند بود.
        ثبت دستی جلسه، مسیر درمان را فعال نمی‌کند.
        تاریخ و ساعت بر اساس زمان تهران هستند.

    </p>

    <form
        method="POST"
        action="{{ route('psychologist.clinical-records.manual-appointments.store', $client) }}"
        class="space-y-4"
    >
        @csrf

        <div class="cr-form-grid">
            <div><label for="manual-date" class="mb-1 block">
                    تاریخ جلسه ـ شمسی
                </label>

                <div class="cmp-date-wrap">
                    <input
                        id="manual-date"
                        aria-invalid="{{ ($errors->has('date_jalali') || $errors->has('date')) ? 'true' : 'false' }}"
                        aria-describedby="manual-date-feedback"
                        name="date_jalali"
                        type="text"
                        dir="ltr"
                        required
                        maxlength="10"
                        placeholder="مثلاً ۱۴۰۵/۰۸/۰۵"
                        value="{{ old('date_jalali', \App\Support\PersianDate::format(now('Asia/Tehran'), 'yyyy/MM/dd')) }}"
                        class="w-full rounded-lg border-gray-300"
                    >

                    <button hidden id="cmp-open" class="cmp-calendar" type="button" aria-label="انتخاب تاریخ و ساعت جلسه" aria-haspopup="dialog" title="انتخاب تاریخ و ساعت">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M7 3v4M17 3v4M3 11h18M7 15h2m3 0h2m3 0h1M7 18h2m3 0h2"/></svg>
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    تاریخ را به صورت سال/ماه/روز وارد کنید.
                </p>

                <div id="manual-date-feedback">
                    @error('date_jalali')
                    <p role="alert" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @error('date')
                    <p role="alert" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <div>
                <label for="manual-time" class="mb-1 block">
                    ساعت شروع
                </label>

                <input
                    id="manual-time"
                    aria-invalid="{{ ($errors->has('time')) ? 'true' : 'false' }}"
                    aria-describedby="manual-time-feedback"
                    name="time"
                    type="time"
                    required
                    value="{{ old('time') }}"
                    class="w-full rounded-lg border-gray-300"
                >

                @error('time')
                <p id="manual-time-feedback" role="alert" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="manual-duration" class="mb-1 block">
                    مدت جلسه ـ دقیقه
                </label>

                <input
                    id="manual-duration"
                    aria-invalid="{{ ($errors->has('duration_minutes')) ? 'true' : 'false' }}"
                    aria-describedby="manual-duration-feedback"
                    name="duration_minutes"
                    type="number"
                    min="1"
                    max="480"
                    step="1"
                    required
                    value="{{ old('duration_minutes', $manualDefaultDuration) }}"
                    class="w-full rounded-lg border-gray-300"
                >

                @error('duration_minutes')
                <p id="manual-duration-feedback" role="alert" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="manual-session-type" class="mb-1 block">
                    شیوه برگزاری
                </label>

                <select
                    id="manual-session-type"
                    aria-invalid="{{ ($errors->has('session_type')) ? 'true' : 'false' }}"
                    aria-describedby="manual-session-type-feedback"
                    name="session_type"
                    required
                    class="w-full rounded-lg border-gray-300"
                >
                    <option
                        value="online"
                        @selected(old('session_type', 'online') === 'online')
                    >
                        آنلاین
                    </option>

                    <option
                        value="in_person"
                        @selected(old('session_type') === 'in_person')
                    >
                        حضوری
                    </option>
                </select>

                @error('session_type')
                <p id="manual-session-type-feedback" role="alert" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="manual-payment-method" class="mb-1 block">
                    وضعیت مالی
                </label>

                <select
                    id="manual-payment-method"
                    aria-invalid="{{ ($errors->has('manual_payment_method')) ? 'true' : 'false' }}"
                    aria-describedby="manual-payment-method-feedback"
                    name="manual_payment_method"
                    required
                    class="w-full rounded-lg border-gray-300"
                >
                    <option
                        value="paid_outside"
                        @selected(old('manual_payment_method', 'paid_outside') === 'paid_outside')
                    >
                        پرداخت‌شده خارج از سایت
                    </option>

                    <option
                        value="free"
                        @selected(old('manual_payment_method') === 'free')
                    >
                        رایگان
                    </option>
                </select>

                @error('manual_payment_method')
                <p id="manual-payment-method-feedback" role="alert" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="manual-amount" class="mb-1 block">
                    مبلغ ـ تومان
                </label>

                <input
                    id="manual-amount"
                    aria-invalid="{{ ($errors->has('amount')) ? 'true' : 'false' }}"
                    aria-describedby="manual-amount-feedback"
                    name="amount"
                    type="number"
                    min="0"
                    max="4294967295"
                    step="1"
                    required
                    value="{{ old('amount', $manualDefaultAmount) }}"
                    class="w-full rounded-lg border-gray-300"
                >

                <p class="mt-1 text-xs text-gray-500">
                    مبلغ قابل تغییر است. برای جلسه رایگان، مبلغ ذخیره‌شده
                    همیشه صفر خواهد بود. تغییر مدت، مبلغ را خودکار تغییر نمی‌دهد.
                </p>

                @error('amount')
                <p id="manual-amount-feedback" role="alert" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button
            type="submit"
            class="rounded-lg bg-indigo-600 px-5 py-2 text-white"
        >
            ثبت جلسه
        </button>
    </form>
</section>
