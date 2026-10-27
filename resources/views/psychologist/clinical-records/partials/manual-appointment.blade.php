<section class="border border-gray-200 bg-white p-5 md:col-span-2">
    <h2 class="mb-2 text-lg font-bold">
        ثبت دستی جلسه
    </h2>

    <p class="mb-5 text-sm text-gray-600">
        ثبت جلسه، وضعیت حضور را تعیین نمی‌کند و مسیر درمان را فعال نمی‌کند.
        تاریخ و ساعت بر اساس زمان تهران هستند.
    </p>

    <form
        method="POST"
        action="{{ route('psychologist.clinical-records.manual-appointments.store', $client) }}"
        class="space-y-4"
    >
        @csrf

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="manual-date" class="mb-1 block">
                    تاریخ جلسه ـ میلادی
                </label>

                <input
                    id="manual-date"
                    name="date"
                    type="date"
                    required
                    value="{{ old('date', now('Asia/Tehran')->format('Y-m-d')) }}"
                    class="w-full rounded-lg border-gray-300"
                >

                @error('date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="manual-time" class="mb-1 block">
                    ساعت شروع
                </label>

                <input
                    id="manual-time"
                    name="time"
                    type="time"
                    required
                    value="{{ old('time') }}"
                    class="w-full rounded-lg border-gray-300"
                >

                @error('time')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="manual-duration" class="mb-1 block">
                    مدت جلسه ـ دقیقه
                </label>

                <input
                    id="manual-duration"
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
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="manual-session-type" class="mb-1 block">
                    شیوه برگزاری
                </label>

                <select
                    id="manual-session-type"
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
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="manual-payment-method" class="mb-1 block">
                    وضعیت مالی
                </label>

                <select
                    id="manual-payment-method"
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
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="manual-amount" class="mb-1 block">
                    مبلغ ـ تومان
                </label>

                <input
                    id="manual-amount"
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
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
