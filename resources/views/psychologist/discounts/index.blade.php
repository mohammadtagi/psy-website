<x-layouts.app>
    <link rel="stylesheet" href="{{ asset('website/style/care-discounts.css') }}">
    <main class="dc dc-index mx-auto max-w-7xl px-4 py-8">
        <div class="dc-header mb-6 flex flex-wrap items-center justify-between gap-4">
            <div><h1 class="text-2xl font-bold">کدهای تخفیف</h1><p class="dc-intro">مدیریت اعتبار، محدودیت مصرف و کدهای عمومی یا اختصاصی مراجعان</p></div>

            <a
                href="{{ route('psychologist.discounts.create') }}"
                class="rounded-lg bg-indigo-700 px-4 py-2 text-white"
            >
                ایجاد کد تخفیف
            </a>
        </div>

        @include('psychologist.discounts._messages')

        <p class="dc-table-hint">در نمایشگر کوچک، جدول را به چپ و راست پیمایش کنید.</p>
            <div class="dc-table overflow-x-auto rounded-lg border bg-white" tabindex="0" role="region" aria-label="جدول قابل پیمایش">
            <table class="w-full text-right text-sm">
                <thead class="bg-gray-100">
                <tr>
                    <th scope="col" class="p-3">کد</th>
                    <th scope="col" class="p-3">تخفیف</th>
                    <th scope="col" class="p-3">مراجع</th>
                    <th scope="col" class="p-3">انقضا ـ تهران</th>
                    <th scope="col" class="p-3">مصرف موفق</th>
                    <th scope="col" class="p-3">وضعیت</th>
                    <th scope="col" class="p-3">جزئیات</th>
                </tr>
                </thead>

                <tbody>
                @forelse ($discounts as $discount)
                    @php
                        $expired = $discount->expires_at->isPast();

                        $full = $discount->usage_limit !== null
                            && $discount->redemptions_count >= $discount->usage_limit;

                        $label = ! $discount->is_active
                            ? 'غیرفعال'
                            : ($expired
                                ? 'منقضی'
                                : ($full ? 'ظرفیت تکمیل' : 'فعال'));
                    @endphp

                    <tr class="border-t">
                        <td class="p-3">
                                <span dir="ltr" class="font-mono">
                                    {{ $discount->code }}
                                </span>
                        </td>

                        <td class="p-3">
                            {{ number_format($discount->value) }}
                            {{ $discount->type === 'percent' ? 'درصد' : 'تومان' }}
                        </td>

                        <td class="p-3">
                            @if ($discount->client)
                                {{ trim(
                                    $discount->client->first_name . ' ' .
                                    $discount->client->last_name
                                ) ?: 'نام ثبت نشده' }}

                                <span class="mt-1 block" dir="ltr">
                                        {{ $discount->client->mobile }}
                                    </span>
                            @else
                                عمومی
                            @endif
                        </td>

                        <td class="p-3">
                            {{ \App\Support\PersianDate::format(
                                $discount->expires_at,
                                'yyyy/MM/dd HH:mm'
                            ) }}
                        </td>

                        <td class="p-3">
                            {{ $discount->redemptions_count }}
                            /
                            {{ $discount->usage_limit ?? 'نامحدود' }}
                        </td>

                        <td class="p-3"><span class="dc-badge {{ ! $discount->is_active ? 'dc-inactive' : ($expired ? 'dc-expired' : ($full ? 'dc-full' : 'dc-active')) }}">{{ $label }}</span></td>

                        <td class="p-3">
                            <a
                                href="{{ route('psychologist.discounts.show', $discount) }}"
                                class="text-indigo-700 underline"
                            >
                                مشاهده
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-gray-500">
                            هنوز کد تخفیفی ایجاد نشده است.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $discounts->links() }}
        </div>
    </main>
</x-layouts.app>
