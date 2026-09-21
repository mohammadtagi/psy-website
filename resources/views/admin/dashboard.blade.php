<x-layouts.app>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-medium text-indigo-600">
                پنل مدیریت
            </p>

            <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900">
                داشبورد مدیر
            </h1>

            <p class="mt-2 text-gray-600">
                از این بخش می‌توانید کاربران، نوبت‌ها و اطلاعات سامانه را مدیریت کنید.
            </p>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            {{-- کارت کاربران --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">
                    کاربران
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    مدیریت مراجعان و کاربران سامانه
                </p>
            </div>

            {{-- کارت نوبت‌ها --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">
                    نوبت‌ها
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    مشاهده و مدیریت نوبت‌های ثبت‌شده
                </p>
            </div>

            {{-- کارت گزارش‌ها --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">
                    گزارش‌ها
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    گزارش فعالیت‌های سامانه
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
