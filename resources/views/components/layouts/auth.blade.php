@props([
    'title' => 'ورود به حساب کاربری',
])

    <!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }} | کریم محمدزاده</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
<main class="flex min-h-screen items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">
        <header class="mb-8 text-center">
            <a
                href="{{ url('/') }}"
                class="text-xl font-bold text-slate-900"
            >
                کریم محمدزاده
            </a>

            <p class="mt-2 text-sm text-slate-500">
                روان‌شناس بالینی
            </p>
        </header>

        <section
            aria-labelledby="auth-title"
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
        >
            <h1
                id="auth-title"
                class="text-xl font-bold text-slate-900"
            >
                {{ $title }}
            </h1>

            @isset($description)
                <div class="mt-3 text-sm leading-7 text-slate-600">
                    {{ $description }}
                </div>
            @endisset

            <div class="mt-6">
                {{ $slot }}
            </div>
        </section>

        <footer class="mt-6 text-center text-xs leading-6 text-slate-500">
            کد ورود خود را در اختیار دیگران قرار ندهید.
        </footer>
    </div>
</main>
</body>
</html>
