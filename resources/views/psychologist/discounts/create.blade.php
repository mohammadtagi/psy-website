<x-layouts.app>
    <link rel="stylesheet" href="{{ asset('website/style/care-discounts.css') }}">
    <link rel="stylesheet" href="{{ asset('website/style/care-discount-expiry-picker.css') }}">
    <script defer src="{{ asset('website/js/care-discount-expiry-picker.js') }}"></script>
    <main class="dc dc-create mx-auto max-w-3xl px-4 py-8">
        <div class="dc-header mb-6 flex items-center justify-between gap-4">
            <div><h1 class="text-2xl font-bold">ایجاد کد تخفیف</h1><p class="dc-intro">مخاطب کد، میزان تخفیف و محدودیت استفاده را مشخص کنید.</p></div>

            <a
                href="{{ route('psychologist.discounts.index') }}"
                class="dc-return text-indigo-700"
            >
                بازگشت به فهرست
            </a>
        </div>

        @include('psychologist.discounts._messages')

        <section class="mb-6 rounded-lg border bg-white p-5">
            <h2 class="font-semibold">جست‌وجوی مراجع برای کد اختصاصی</h2>

            <p class="mt-2 text-sm text-gray-600">
                برای کد عمومی نیازی به جست‌وجو نیست. جست‌وجو صفحه را دوباره بارگذاری می‌کند؛ آن را پیش از تکمیل فرم انجام دهید.
                برای کد اختصاصی، ابتدا جست‌وجو کنید و سپس فرم پایین را تکمیل کنید.
            </p>

            <form
                method="GET"
                action="{{ route('psychologist.discounts.create') }}"
                class="mt-4"
            >
                <label for="client-query" class="mb-2 block text-sm">
                    نام، نام خانوادگی یا شماره موبایل
                </label>

                <div class="flex gap-2">
                    <input
                        id="client-query"
                        type="search"
                        name="q"
                        value="{{ $query }}"
                        maxlength="100"
                        class="min-w-0 flex-1 rounded border p-2"
                    >

                    <button
                        type="submit"
                        class="rounded bg-indigo-700 px-4 py-2 text-white"
                    >
                        جست‌وجو
                    </button>
                </div>
            </form>
        </section>

        <form
            method="POST"
            action="{{ route('psychologist.discounts.store') }}"
            class="space-y-6 rounded-lg border bg-white p-5"
        >
            @csrf

            <fieldset class="space-y-3">
                <legend class="mb-3 font-semibold">۱. دامنه استفاده از کد</legend>

                <label class="flex items-center gap-2 rounded border p-3">
                    <input
                        type="radio"
                        name="client_id"
                        value=""
                        @checked(! old('client_id'))
                    >
                    عمومی؛ قابل استفاده توسط همه مراجعان
                </label>

                @if (
                    $selectedClient
                    && (! $clients || ! $clients->getCollection()->contains('id', $selectedClient->id))
                )
                    <label class="flex items-center gap-2 rounded border p-3">
                        <input
                            type="radio"
                            name="client_id"
                            value="{{ $selectedClient->id }}"
                            checked
                        >

                        <span>
                            {{ trim(
                                $selectedClient->first_name . ' ' .
                                $selectedClient->last_name
                            ) ?: 'نام ثبت نشده' }}
                            —
                            <bdi>{{ $selectedClient->mobile }}</bdi>
                        </span>
                    </label>
                @endif

                @if ($clients)
                    @forelse ($clients as $client)
                        <label class="flex items-center gap-2 rounded border p-3">
                            <input
                                type="radio"
                                name="client_id"
                                value="{{ $client->id }}"
                                @checked((string) old('client_id') === (string) $client->id)
                            >

                            <span>
                                {{ trim(
                                    $client->first_name . ' ' .
                                    $client->last_name
                                ) ?: 'نام ثبت نشده' }}
                                —
                                <bdi>{{ $client->mobile }}</bdi>
                            </span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-600">
                            مراجعی با این مشخصات پیدا نشد.
                        </p>
                    @endforelse

                    {{ $clients->links() }}
                @endif

                <p class="text-xs text-gray-500">
                    انتخاب مراجع یعنی کد فقط برای همان حساب قابل استفاده است.
                </p>
            </fieldset>

            <fieldset class="space-y-4 rounded-lg border p-4">
                <legend class="px-2 font-semibold">
                    ۲. روش تعیین کد تخفیف
                </legend>

                <label class="flex items-center gap-2">
                    <input
                        type="radio"
                        name="code_mode"
                        value="random"
                        @checked(old('code_mode', 'random') === 'random')
                    >
                    تولید خودکار کد تصادفی
                </label>

                <label class="flex items-center gap-2">
                    <input
                        type="radio"
                        name="code_mode"
                        value="manual"
                        @checked(old('code_mode', 'random') === 'manual')
                    >
                    ورود دستی توسط مشاور
                </label>

                <div>
                    <label
                        for="discount-code"
                        class="mb-2 block text-sm font-medium"
                    >
                        کد دلخواه برای حالت دستی
                    </label>

                    <input
                        id="discount-code"
                        name="code"
                        value="{{ old('code') }}"
                        maxlength="50"
                        dir="ltr"
                        autocomplete="off"
                        autocapitalize="characters"
                        spellcheck="false"
                        placeholder="مثلاً WELCOME20"
                        class="w-full rounded border p-2"
                    >

                    <p class="mt-2 text-xs leading-6 text-gray-500">
                        در حالت دستی، ۳ تا ۵۰ کاراکتر شامل حروف انگلیسی،
                        عدد، خط تیره یا زیرخط وارد کنید.
                        حروف کوچک به بزرگ تبدیل می‌شوند.
                        در حالت خودکار، این فیلد نادیده گرفته می‌شود
                        و کد پس از ثبت نمایش داده خواهد شد.
                    </p>
                </div>
            </fieldset>

            <h2 class="dc-section-title">۳. میزان تخفیف</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="discount-type" class="mb-2 block text-sm font-medium">
                        نوع تخفیف
                    </label>

                    <select
                        id="discount-type"
                        name="type"
                        class="w-full rounded border p-2"
                        required
                    >
                        <option value="percent" @selected(old('type', 'percent') === 'percent')>
                            درصدی
                        </option>

                        <option value="fixed" @selected(old('type') === 'fixed')>
                            مبلغ ثابت ـ تومان
                        </option>
                    </select>
                </div>

                <div>
                    <label for="discount-value" class="mb-2 block text-sm font-medium">
                        مقدار تخفیف
                    </label>

                    <input
                        id="discount-value"
                        name="value"
                        type="text"
                        inputmode="numeric"
                        value="{{ old('value') }}"
                        required
                        class="w-full rounded border p-2"
                    >
                </div>
            </div>

            <p class="text-sm text-gray-600">
                درصد باید بین ۱ تا ۱۰۰ باشد. مبلغ ثابت به تومان است.
                تخفیف بیشتر از مبلغ جلسه، مبلغ نهایی را صفر می‌کند.
            </p>

            <h2 class="dc-section-title">۴. پایان اعتبار</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="expires-date" class="mb-2 block text-sm font-medium">
                        تاریخ انقضای شمسی
                    </label>

                    <div class="dc-expiry-field">
                    <input
                        id="expires-date"
                        name="expires_date"
                        value="{{ old('expires_date') }}"
                        placeholder="۱۴۰۵/۰۸/۳۰"
                        maxlength="10"
                        dir="ltr"
                        required
                        class="w-full rounded border p-2"
                    >
                        <button type="button" id="dc-expiry-open" hidden aria-label="انتخاب تاریخ و ساعت انقضا" aria-haspopup="dialog" title="انتخاب تاریخ و ساعت انقضا">
                            <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M7 3v4M17 3v4M3 11h18M7 15h3M14 15h3M7 18h3"/></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="expires-time" class="mb-2 block text-sm font-medium">
                        ساعت انقضا به وقت تهران
                    </label>

                    <input
                        id="expires-time"
                        name="expires_time"
                        value="{{ old('expires_time', '23:59') }}"
                        placeholder="۲۳:۵۹"
                        maxlength="5"
                        dir="ltr"
                        required
                        class="w-full rounded border p-2"
                    >
                </div>
            </div>

            <p class="text-xs text-gray-500">
                اعتبار کد دقیقاً در ابتدای دقیقه واردشده پایان می‌یابد.
            </p>

            <h2 class="dc-section-title">۵. محدودیت مصرف</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="usage-limit" class="mb-2 block text-sm font-medium">
                        سقف مصرف کل
                    </label>

                    <input
                        id="usage-limit"
                        name="usage_limit"
                        type="text"
                        inputmode="numeric"
                        value="{{ old('usage_limit') }}"
                        class="w-full rounded border p-2"
                    >
                </div>

                <div>
                    <label for="client-limit" class="mb-2 block text-sm font-medium">
                        سقف مصرف هر مراجع
                    </label>

                    <input
                        id="client-limit"
                        name="per_client_limit"
                        type="text"
                        inputmode="numeric"
                        value="{{ old('per_client_limit') }}"
                        class="w-full rounded border p-2"
                    >
                </div>
            </div>

            <p class="text-xs text-gray-500">
                سقف خالی یعنی نامحدود؛ مقدار صفر پذیرفته نمی‌شود.
                فقط مصرف موفق محاسبه می‌شود، نه صرفاً واردکردن کد در فاکتور.
            </p>

            <button
                type="submit"
                class="rounded-lg bg-indigo-700 px-5 py-3 font-medium text-white"
            >
                ایجاد کد تخفیف
            </button>
        </form>
    </main>
</x-layouts.app>
