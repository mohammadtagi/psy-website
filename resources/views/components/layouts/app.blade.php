<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield(
            'title',
            (isset($title) && is_string($title))
                ? $title
                : config('app.name', 'سامانه روانشناسی')
        )
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="min-h-screen bg-gray-50 font-sans antialiased text-gray-800">
<header class="border-b border-gray-200 bg-white">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a
            href="{{ route('home') }}"
            class="text-lg font-bold text-indigo-600"
        >
            کریم محمدزاده
        </a>

        <nav
            aria-label="حساب کاربری"
            class="flex flex-wrap items-center gap-3"
        >
            @auth
                @php
                    $user = auth()->user();

                    $displayName = trim(
                        ($user->first_name ?? '') . ' ' .
                        ($user->last_name ?? '')
                    );

                    $roleLinks = match ($user->role) {
                        'admin' => [
                            [
                                'label' => 'داشبورد مدیر',
                                'route' => 'admin.dashboard',
                            ],
                        ],

                        'psychologist' => [
                            [
                                'label' => 'داشبورد',
                                'route' => 'psychologist.dashboard',
                            ],
                            [
                                'label' => 'نوبت‌ها',
                                'route' => 'psychologist.appointments.index',
                            ],
                            [
                                'label' => 'بازه‌های حضور',
                                'route' => 'psychologist.availabilities.index',
                            ],
                            [
                                'label' => 'مراجعان و پرونده‌ها',
                                'route' => 'psychologist.clients.index',
                            ],
                        ],

                        'client' => [
                            [
                                'label' => 'داشبورد',
                                'route' => 'client.dashboard',
                            ],
                            [
                                'label' => 'نوبت‌های من',
                                'route' => 'client.appointments.index',
                            ],
                            [
                                'label' => 'پروفایل',
'route' => 'client.profile.show',
                            ],
                            [
                                'label' => 'رزرو نوبت',
                                'route' => 'booking.index',
                            ],
                        ],

                        default => [],
                    };
                @endphp

                <span class="text-sm text-gray-600">
                    {{ $displayName !== '' ? $displayName : $user->mobile }}
                </span>

                @foreach ($roleLinks as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50"
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50"
                    >
                        خروج
                    </button>
                </form>
            @else
                <a
                    href="{{ route('auth.login') }}"
                    class="rounded-lg px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50"
                >
                    ورود
                </a>
            @endauth
        </nav>
    </div>
</header>

<div>
    @if (isset($slot) && ! is_array($slot))
        {{ $slot }}
    @endif

    @yield('content')
</div>

@stack('scripts')
</body>
</html>

