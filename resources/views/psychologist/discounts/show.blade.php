<x-layouts.app>
    <main class="mx-auto max-w-5xl px-4 py-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl font-bold">
                کد تخفیف
                <bdi class="font-mono">{{ $discount->code }}</bdi>
            </h1>

            <a
                href="{{ route('psychologist.discounts.index') }}"
                class="text-indigo-700"
            >
                بازگشت به فهرست
            </a>
        </div>

        @include('psychologist.discounts._messages')

        <section class="rounded-lg border bg-white p-5">
            <h2 class="mb-5 text-lg font-semibold">مشخصات کد</h2>

            <dl class="grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-sm text-gray-500">مقدار تخفیف</dt>
                    <dd class="mt-1 font-medium">
                        {{ number_format($discount->value) }}
                        {{ $discount->type === 'percent' ? 'درصد' : 'تومان' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-gray-500">وضعیت فعال‌سازی</dt>
                    <dd class="mt-1 font-medium">
                        {{ $discount->is_active ? 'فعال' : 'غیرفعال' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-gray-500">مراجع مجاز</dt>
                    <dd class="mt-1 font-medium">
                        @if ($discount->client)
                            {{ trim(
                                $discount->client->first_name . ' ' .
                                $discount->client->last_name
                            ) ?: 'نام ثبت نشده' }}

                            <bdi class="mt-1 block">
                                {{ $discount->client->mobile }}
                            </bdi>
                        @else
                            عمومی
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-gray-500">انقضا به وقت تهران</dt>
                    <dd class="mt-1 font-medium">
                        {{ \App\Support\PersianDate::format(
                            $discount->expires_at,
                            'yyyy/MM/dd HH:mm'
                        ) }}

                        @if ($discount->expires_at->isPast())
                            <span class="block text-sm text-red-700">
                                اعتبار کد پایان یافته است.
                            </span>
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-gray-500">مصرف موفق / سقف کل</dt>
                    <dd class="mt-1 font-medium">
                        {{ $discount->redemptions_count }}
                        /
                        {{ $discount->usage_limit ?? 'نامحدود' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-gray-500">سقف مصرف هر مراجع</dt>
                    <dd class="mt-1 font-medium">
                        {{ $discount->per_client_limit ?? 'نامحدود' }}
                    </dd>
                </div>
            </dl>

            <form
                method="POST"
                action="{{ route('psychologist.discounts.status', $discount) }}"
                class="mt-6 border-t pt-5"
            >
                @csrf
                @method('PATCH')

                <input
                    type="hidden"
                    name="is_active"
                    value="{{ $discount->is_active ? '0' : '1' }}"
                >

                <button
                    type="submit"
                    class="rounded-lg border border-indigo-700 px-4 py-2 text-indigo-700"
                >
                    {{ $discount->is_active ? 'غیرفعال‌کردن کد' : 'فعال‌کردن کد' }}
                </button>

                <p class="mt-3 text-xs leading-6 text-gray-500">
                    فعال‌کردن مجدد، تاریخ انقضا یا سقف مصرف را تغییر نمی‌دهد.
                    غیرفعال‌کردن نیز مصرف‌های موفق قبلی را حذف نمی‌کند.
                </p>
            </form>
        </section>

        <section class="mt-8">
            <h2 class="mb-4 text-lg font-semibold">سوابق مصرف موفق</h2>

            <div class="overflow-x-auto rounded-lg border bg-white">
                <table class="w-full text-right text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">مراجع</th>
                        <th class="p-3">کد پیگیری نوبت</th>
                        <th class="p-3">مبلغ اصلی</th>
                        <th class="p-3">تخفیف</th>
                        <th class="p-3">مبلغ نهایی</th>
                        <th class="p-3">زمان مصرف ـ تهران</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse ($redemptions as $redemption)
                        <tr class="border-t">
                            <td class="p-3">
                                {{ trim(
                                    $redemption->client->first_name . ' ' .
                                    $redemption->client->last_name
                                ) ?: 'نام ثبت نشده' }}

                                <bdi class="mt-1 block">
                                    {{ $redemption->client->mobile }}
                                </bdi>
                            </td>

                            <td class="p-3">
                                <bdi class="break-all font-mono">
                                    {{ $redemption->appointment->tracking_code }}
                                </bdi>
                            </td>

                            <td class="p-3">
                                {{ number_format($redemption->original_amount) }}
                                تومان
                            </td>

                            <td class="p-3">
                                {{ number_format($redemption->discount_amount) }}
                                تومان
                            </td>

                            <td class="p-3">
                                {{ number_format($redemption->final_amount) }}
                                تومان
                            </td>

                            <td class="p-3">
                                {{ \App\Support\PersianDate::format(
                                    $redemption->created_at,
                                    'yyyy/MM/dd HH:mm'
                                ) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-500">
                                هنوز مصرف موفقی ثبت نشده است.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $redemptions->links() }}
            </div>
        </section>
    </main>
</x-layouts.app>
