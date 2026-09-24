<x-layouts.app>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-medium text-indigo-600">
                پنل روان‌شناس
            </p>

            <h1 class="mt-2 text-2xl font-bold text-gray-900">
                جستجوی مراجع
            </h1>
        </div>

        @if (session('status'))
            <div class="mb-6 border-r-4 border-green-600 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <form
            method="GET"
            action="{{ route('psychologist.clients.index') }}"
            class="mb-8 max-w-xl"
        >
            <label for="client-search" class="mb-2 block text-sm font-medium text-gray-700">
                شماره موبایل، نام یا نام خانوادگی
            </label>

            <div class="flex flex-col gap-3 sm:flex-row">
                <input
                    id="client-search"
                    name="q"
                    type="search"
                    value="{{ $query }}"
                    inputmode="search"
                    autocomplete="off"
                    class="min-w-0 flex-1 border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    placeholder="بخشی از شماره یا نام مراجع"
                    aria-describedby="search-help"
                >

                <button
                    type="submit"
                    class="border border-indigo-700 bg-indigo-700 px-5 py-2 font-medium text-white hover:bg-indigo-800"
                >
                    جستجو
                </button>
            </div>

            <p id="search-help" class="mt-2 text-xs text-gray-500">
                می‌توانید بخشی از شماره موبایل یا نام و نام خانوادگی را وارد کنید.
            </p>

            @error('q')
            <p class="mt-2 text-sm text-red-700" role="alert">
                {{ $message }}
            </p>
            @enderror
        </form>

        @if ($query !== '')
            @if ($clients->isNotEmpty())
                <section aria-labelledby="client-results-title">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <h2 id="client-results-title" class="text-lg font-semibold text-gray-900">
                            نتایج جستجو
                        </h2>

                        <span class="text-sm text-gray-500">
                            {{ $clients->count() }} نتیجه
                        </span>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($clients as $client)
                            @php
                                $clientFullName = trim(
                                    ($client->first_name ?? '') . ' ' . ($client->last_name ?? '')
                                );
                            @endphp

                            <article class="border border-gray-200 bg-white p-5">
                                <h3 class="text-base font-semibold text-gray-900">
                                    {{ $clientFullName ?: 'نام ثبت نشده' }}
                                </h3>

                                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                                    <div>
                                        <dt class="text-gray-500">موبایل</dt>
                                        <dd class="mt-1 text-gray-900" dir="ltr">
                                            {{ $client->mobile }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-gray-500">تاریخ تولد</dt>
                                        <dd class="mt-1 text-gray-900">
                                            {{ $client->birth_date
                                                ? \App\Support\PersianDate::format(
                                                    \Illuminate\Support\Carbon::parse($client->birth_date)
                                                )
                                                : 'ثبت نشده' }}
                                        </dd>
                                    </div>
                                </dl>

                                <div class="mt-5 flex flex-wrap gap-3 border-t border-gray-100 pt-4">
                                    @if ($client->clinicalRecord)
                                        <a
                                            href="{{ route('psychologist.clinical-records.show', $client) }}"
                                            class="border border-indigo-700 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50"
                                        >
                                            مشاهده پرونده
                                        </a>
                                    @else
                                        <a
                                            href="{{ route('psychologist.clinical-records.create', $client) }}"
                                            class="border border-indigo-700 bg-indigo-700 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-800"
                                        >
                                            ایجاد پرونده
                                        </a>
                                    @endif
                                </div>


                            </article>
                        @endforeach
                    </div>
                </section>
            @elseif ($canCreateClient)
                <section
                    class="max-w-xl border border-gray-200 bg-white p-5"
                    aria-labelledby="new-client-title"
                >
                    <h2 id="new-client-title" class="text-lg font-semibold text-gray-900">
                        ثبت مراجع جدید
                    </h2>

                    <p class="mt-2 text-sm text-gray-600">
                        مراجعی با این شماره پیدا نشد. در صورت تمایل می‌توانید حساب او را ایجاد کنید.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('psychologist.clients.store') }}"
                        class="mt-6 space-y-5"
                    >
                        @csrf

                        <input
                            type="hidden"
                            name="mobile"
                            value="{{ $query }}"
                        >

                        <div>
                            <label
                                for="new-client-first-name"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                نام
                            </label>

                            <input
                                id="new-client-first-name"
                                name="first_name"
                                type="text"
                                value="{{ old('first_name') }}"
                                maxlength="255"
                                class="block w-full border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            >

                            @error('first_name')
                            <p class="mt-2 text-sm text-red-700" role="alert">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="new-client-last-name"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                نام خانوادگی
                            </label>

                            <input
                                id="new-client-last-name"
                                name="last_name"
                                type="text"
                                value="{{ old('last_name') }}"
                                maxlength="255"
                                class="block w-full border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            >

                            @error('last_name')
                            <p class="mt-2 text-sm text-red-700" role="alert">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="new-client-birth-date"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                تاریخ تولد
                            </label>

                            <input
                                id="new-client-birth-date"
                                name="birth_date"
                                type="text"
                                value="{{ old('birth_date') }}"
                                placeholder="۱۴۰۰/۰۱/۰۱"
                                inputmode="numeric"
                                dir="ltr"
                                class="block w-full border border-gray-300 px-3 py-2 text-left shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            >

                            <p class="mt-1 text-xs text-gray-500">
                                وارد کردن این مقدار اختیاری است.
                            </p>

                            @error('birth_date')
                            <p class="mt-2 text-sm text-red-700" role="alert">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div class="border-r-4 border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-600">
                            شماره موبایل:
                            <span dir="ltr">{{ $query }}</span>
                        </div>

                        @error('mobile')
                        <p class="text-sm text-red-700" role="alert">
                            {{ $message }}
                        </p>
                        @enderror

                        <button
                            type="submit"
                            class="border border-indigo-700 bg-indigo-700 px-5 py-2 font-medium text-white hover:bg-indigo-800"
                        >
                            ایجاد حساب مراجع
                        </button>
                    </form>
                </section>
            @else
                <p class="text-sm text-gray-600">
                    مراجع‌ای با این مشخصات پیدا نشد.
                </p>
            @endif
        @endif
    </div>
</x-layouts.app>
