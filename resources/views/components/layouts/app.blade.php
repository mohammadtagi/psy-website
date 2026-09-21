<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', (isset($title) && is_string($title)) ? $title : config('app.name', 'سامانه روانشناسی'))</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased text-gray-800">
<!-- نوار بالا (Navbar ساده) -->
<header class="border-b border-gray-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4">
            <span class="text-xl font-bold text-indigo-600">سامانه مدیریت</span>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-600">{{ auth()->user()?->name ?? auth()->user()?->mobile }}</span>
            <form method="POST" action="{{ route('logout') ?? '#' }}" class="inline">
                @csrf
                <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">
                    خروج
                </button>
            </form>
        </div>
    </div>
</header>

<!-- محتوای اصلی صفحات -->
<main>
    @if(isset($slot) && !is_array($slot))
        {{ $slot }}
    @endif
    @yield('content')
</main>
</body>
</html>
