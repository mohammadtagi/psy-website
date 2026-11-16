@php
    $originalAmount = (int) $appointment->amount;
    $discountAmount = (int) ($appointment->discount_amount ?? 0);
    $finalAmount = max(0, $originalAmount - $discountAmount);

    $hasDiscount = $appointment->discount_code_id !== null;

    $canSettle = $isPendingPayment
        && $hasActiveHold
        && ! $hasSessionStarted;
@endphp

<div class="cbc-invoice space-y-5 rounded-lg bg-gray-50 p-4">
    <h2 class="font-semibold text-gray-900">فاکتور نوبت</h2>

    <dl class="space-y-3 text-sm">
        <div class="flex items-center justify-between gap-4">
            <dt class="text-gray-600">مبلغ اصلی جلسه</dt>

            <dd class="font-semibold text-gray-900">
                {{ number_format($originalAmount) }} تومان
            </dd>
        </div>

        <div class="flex items-center justify-between gap-4">
            <dt class="text-gray-600">تخفیف</dt>

            <dd class="font-semibold text-green-700">
                {{ number_format($discountAmount) }} تومان
            </dd>
        </div>

        <div class="cbc-total flex items-center justify-between gap-4 border-t pt-3">
            <dt class="font-semibold text-gray-900">مبلغ نهایی</dt>

            <dd class="font-bold text-gray-900">
                {{ number_format($finalAmount) }} تومان
            </dd>
        </div>
    </dl>

    @if ($hasDiscount)
        <div class="cbp-applied rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm">
            <span>کد اعمال‌شده:</span>

            <bdi class="break-all font-mono font-semibold">
                {{ $appliedDiscountCode ?? '—' }}
            </bdi>

            @if ($canSettle)
                <form
                    method="POST"
                    action="{{ route('booking.discount.destroy', $appointment) }}"
                    class="mt-3"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="min-h-11 rounded border border-red-300 px-4 py-2 text-red-700"
                    >
                        حذف کد تخفیف
                    </button>
                </form>
            @endif
        </div>
    @endif

    @if ($canSettle)
        <details class="cbp-discount" @if($errors->has('code') || old('code')) open @endif>
            <summary>{{ $hasDiscount ? 'تغییر کد تخفیف' : 'کد تخفیف دارید؟' }}</summary>
        <form
            method="POST"
            action="{{ route('booking.discount.store', $appointment) }}"
            class="border-t pt-4"
        >
            @csrf

            <label
                for="booking-discount-code"
                class="mb-2 block text-sm font-medium text-gray-900"
            >
                {{ $hasDiscount ? 'جایگزینی کد تخفیف' : 'کد تخفیف دارید؟' }}
            </label>

            <div class="flex flex-col gap-2 sm:flex-row">
                <input
                    id="booking-discount-code"
                    type="text"
                    name="code"
                    value="{{ old('code') }}"
                    maxlength="50"
                    dir="ltr"
                    autocomplete="off"
                    spellcheck="false"
                    required
                    class="min-w-0 flex-1 rounded-lg border border-gray-300 p-3"
                >

                <button
                    type="submit"
                    class="min-h-11 rounded-lg border border-indigo-600 px-4 py-2 text-indigo-700"
                >
                    {{ $hasDiscount ? 'جایگزینی کد' : 'اعمال کد' }}
                </button>
            </div>
        </form>

        </details>

        @if ($hasDiscount && $finalAmount === 0)
            <form
                method="POST"
                action="{{ route('booking.confirm-free', $appointment) }}"
            >
                @csrf

                <button
                    type="submit"
                    class="cbc-primary min-h-11 rounded-lg bg-green-700 px-5 py-3 font-medium text-white"
                >
                    تأیید نهایی رایگان نوبت
                </button>

                <p class="mt-2 text-xs leading-6 text-gray-600">
                    برای تأیید نهایی، دکمهٔ بالا را بزنید؛ پرداختی لازم نیست.
                </p>
            </form>
        @elseif ($hasDiscount)
            <div
                class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm leading-7 text-amber-900"
                role="status"
            >
                    پرداخت آنلاین با تخفیف جزئی در حال حاضر فعال نیست.
                    می‌توانید کد را حذف کنید و مبلغ اصلی جلسه را بپردازید،
                    یا در صورت داشتن کد معتبر با تخفیف کامل، آن را جایگزین کنید.
                    تا تأیید نهایی، نوبت موقت است و اعمال یا حذف کد مهلت آن را تمدید نمی‌کند.
            </div>
        @elseif (config('payment.online_enabled', true))
            <form
                method="POST"
                action="{{ route('payments.pay', $appointment) }}"
            >
                @csrf

                <button
                    type="submit"
                    class="cbc-primary min-h-11 rounded-lg bg-indigo-600 px-5 py-3 font-medium text-white"
                >
                    پرداخت {{ number_format($finalAmount) }} تومان و تأیید نوبت
                </button>

                <p class="mt-2 text-xs leading-6 text-gray-600">
                    انتقال به درگاه زرین‌پال
                </p>
            </form>
        @else
            <p class="text-sm text-amber-800">
                پرداخت آنلاین در حال حاضر غیرفعال است.
            </p>
        @endif
    @endif


</div>
