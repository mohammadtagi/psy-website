@extends('components.layouts.app')

@section('title', 'انتخاب زمان مشاوره')

@section('content')
    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <header class="mb-8">
            <p class="text-sm font-medium text-indigo-600">
                رزرو جلسه مشاوره
            </p>

            <h1 class="mt-2 text-2xl font-bold text-gray-900">
                انتخاب تاریخ جلسه
            </h1>

            <p class="mt-2 text-sm text-gray-600">
                ابتدا مدت جلسه و سپس یکی از تاریخ‌های دارای زمان آزاد را انتخاب کنید.
            </p>
        </header>

        @if ($dates->isEmpty())
            <section class="rounded-lg border border-gray-200 bg-white px-6 py-12 text-center shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">
                    در حال حاضر هیچ زمان آزادی برای رزرو وجود ندارد
                </h2>

                <p class="mt-2 text-sm text-gray-600">
                    لطفاً بعداً دوباره بررسی کنید.
                </p>
            </section>
        @else
            <section class="mb-8 border-b border-gray-200 pb-6">
                <h2 class="mb-3 text-sm font-semibold text-gray-900">
                    مدت جلسه
                </h2>

                <div class="flex flex-wrap gap-3">
                    <a
                        href="{{ route('booking.index', ['duration' => 45]) }}"
                        class="inline-flex min-h-11 items-center rounded-lg border px-4 py-2 text-sm font-medium transition
                        {{ request('duration', '45') === '45'
                            ? 'border-indigo-600 bg-indigo-50 text-indigo-700'
                            : 'border-gray-300 bg-white text-gray-700 hover:border-indigo-400' }}"
                    >
                        ۴۵ دقیقه
                        <span class="mr-2 text-xs text-gray-500">
                            ۷۶۰٬۰۰۰ تومان
                        </span>
                    </a>

                    <a
                        href="{{ route('booking.index', ['duration' => 60]) }}"
                        class="inline-flex min-h-11 items-center rounded-lg border px-4 py-2 text-sm font-medium transition
                        {{ request('duration') === '60'
                            ? 'border-indigo-600 bg-indigo-50 text-indigo-700'
                            : 'border-gray-300 bg-white text-gray-700 hover:border-indigo-400' }}"
                    >
                        ۶۰ دقیقه
                        <span class="mr-2 text-xs text-gray-500">
                            ۹۵۰٬۰۰۰ تومان
                        </span>
                    </a>
                </div>
            </section>

            <section>
                <h2 class="mb-4 text-lg font-semibold text-gray-900">
                    تاریخ‌های قابل رزرو
                </h2>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($dates as $date)
                        @php
                            $dateObject = \Carbon\CarbonImmutable::createFromFormat(
                                '!Y-m-d',
                                $date,
                                'Asia/Tehran'
                            );
                        @endphp

                        <a
                            href="{{ route('booking.show', ['date' => $date]) }}?duration={{ request('duration', 45) }}"
                            class="group rounded-lg border border-gray-200 bg-white p-5 shadow-sm transition hover:border-indigo-500 hover:shadow-md"
                        >
                            <span class="block text-sm text-gray-500">
                                {{ $dateObject->locale('fa')->dayName }}
                            </span>

                            <span class="mt-1 block text-lg font-semibold text-gray-900">
                                {{ $dateObject->format('Y/m/d') }}
                            </span>

                            <span class="mt-3 block text-sm font-medium text-indigo-600">
                                مشاهده زمان‌های آزاد
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </main>
@endsection
