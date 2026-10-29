<?php

namespace App\Http\Controllers\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\User;
use App\Support\PersianDate;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;
use Illuminate\Support\Str;

class DiscountCodeController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isPsychologist(), 403);

        $discounts = DiscountCode::query()
            ->with('client:id,first_name,last_name,mobile')
            ->withCount('redemptions')
            ->latest('id')
            ->paginate(20);

        return view('psychologist.discounts.index', [
            'discounts' => $discounts,
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()?->isPsychologist(), 403);

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $query = DiscountCode::normalizeCode(
            (string) ($validated['q'] ?? ''),
        );

        $clients = null;

        if ($query !== '') {
            $clientsQuery = User::query()
                ->where('role', User::ROLE_CLIENT);

            if (preg_match('/\A[0-9]+\z/', $query)) {
                $clientsQuery->where('mobile', 'like', "%{$query}%");
            } else {
                $words = preg_split(
                    '/\s+/u',
                    $query,
                    -1,
                    PREG_SPLIT_NO_EMPTY,
                );

                foreach ($words as $word) {
                    $pattern = '%' . str_replace(
                            ['!', '%', '_'],
                            ['!!', '!%', '!_'],
                            $word,
                        ) . '%';

                    $clientsQuery->where(function ($names) use ($pattern): void {
                        $names
                            ->whereRaw(
                                "first_name LIKE ? ESCAPE '!'",
                                [$pattern],
                            )
                            ->orWhereRaw(
                                "last_name LIKE ? ESCAPE '!'",
                                [$pattern],
                            );
                    });
                }
            }

            $clients = $clientsQuery
                ->select('id', 'first_name', 'last_name', 'mobile')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->orderBy('id')
                ->paginate(10)
                ->withQueryString();
        }

        // در بازگشت از خطای اعتبارسنجی، انتخاب قبلی حفظ شود.
        $selectedClient = null;
        $oldClientId = $request->old('client_id');

        if (
            is_scalar($oldClientId)
            && ctype_digit((string) $oldClientId)
        ) {
            $selectedClient = User::query()
                ->where('role', User::ROLE_CLIENT)
                ->find((int) $oldClientId);
        }

        return view('psychologist.discounts.create', [
            'query' => $query,
            'clients' => $clients,
            'selectedClient' => $selectedClient,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isPsychologist(), 403);

        // ارقام فارسی و عربی در فیلدهای عددی نیز پذیرفته شوند.
        foreach ([
                     'code',
                     'value',
                     'client_id',
                     'usage_limit',
                     'per_client_limit',
                     'expires_date',
                     'expires_time',
                 ] as $field) {
            $input = $request->input($field);

            if (is_string($input)) {
                $normalized = DiscountCode::normalizeCode($input);

                $request->merge([
                    $field => $normalized === '' ? null : $normalized,
                ]);
            }
        }

        $validated = $request->validate([
            'code_mode' => [
                'required',
                Rule::in(['random', 'manual']),
            ],
            'code' => [
                'exclude_if:code_mode,random',
                'required_if:code_mode,manual',
                'string',
                'regex:/\A[A-Z0-9_-]{3,50}\z/',
                Rule::unique('discount_codes', 'code'),
            ],

            'type' => [
                'required',
                Rule::in([
                    DiscountCode::TYPE_PERCENT,
                    DiscountCode::TYPE_FIXED,
                ]),
            ],
            'value' => [
                'required',
                'integer',
                'min:1',
                'max:1000000000',
            ],
            'client_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(
                    fn ($query) => $query->where('role', User::ROLE_CLIENT),
                ),
            ],
            'usage_limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000000000',
            ],
            'per_client_limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000000000',
            ],
            'expires_date' => ['required', 'string', 'max:10'],
            'expires_time' => [
                'required',
                'string',
                'regex:/\A(?:[01][0-9]|2[0-3]):[0-5][0-9]\z/',
            ],
        ], [
            'code_mode.required' => 'روش تعیین کد تخفیف را انتخاب کنید.',
            'code_mode.in' => 'روش تعیین کد معتبر نیست.',
            'code.required_if' => 'کد تخفیف را به انگلیسی وارد کنید.',
            'code.regex' =>
                'کد باید ۳ تا ۵۰ کاراکتر شامل حروف انگلیسی، عدد، خط تیره یا زیرخط باشد.',
            'code.unique' => 'این کد قبلاً ثبت شده است.',
            'type.required' => 'نوع تخفیف را انتخاب کنید.',
            'type.in' => 'نوع تخفیف معتبر نیست.',
            'value.required' => 'مقدار تخفیف را وارد کنید.',
            'value.integer' => 'مقدار تخفیف باید عدد صحیح باشد.',
            'value.min' => 'مقدار تخفیف باید بیشتر از صفر باشد.',
            'client_id.exists' => 'مراجع انتخاب‌شده معتبر نیست.',
            'usage_limit.min' => 'سقف مصرف کل باید حداقل یک باشد.',
            'per_client_limit.min' => 'سقف مصرف هر مراجع باید حداقل یک باشد.',
            'expires_date.required' => 'تاریخ انقضا الزامی است.',
            'expires_time.required' => 'ساعت انقضا الزامی است.',
            'expires_time.regex' => 'ساعت را به شکل ۱۴:۳۰ وارد کنید.',
        ]);

        if (
            $validated['type'] === DiscountCode::TYPE_PERCENT
            && (int) $validated['value'] > 100
        ) {
            throw ValidationException::withMessages([
                'value' => 'درصد تخفیف باید بین ۱ تا ۱۰۰ باشد.',
            ]);
        }

        try {
            $gregorian = PersianDate::toGregorian(
                $validated['expires_date'],
            );
        } catch (InvalidArgumentException) {
            throw ValidationException::withMessages([
                'expires_date' => 'تاریخ شمسی واردشده معتبر نیست.',
            ]);
        }

        $expiresAt = CarbonImmutable::createFromFormat(
            '!Y-m-d H:i',
            $gregorian . ' ' . $validated['expires_time'],
            'Asia/Tehran',
        );

        if (
            $expiresAt === false
            || $expiresAt->lte(CarbonImmutable::now('Asia/Tehran'))
        ) {
            throw ValidationException::withMessages([
                'expires_date' => 'زمان انقضا باید در آینده باشد.',
            ]);
        }
        if ($validated['code_mode'] === 'random') {
            do {
                $validated['code'] = 'OFF-' . Str::upper(Str::random(10));
            } while (
                DiscountCode::query()
                    ->where('code', $validated['code'])
                    ->exists()
            );
        }

        $discount = DiscountCode::query()->create([
            'code' => $validated['code'],
            'type' => $validated['type'],
            'value' => (int) $validated['value'],
            'client_id' => $validated['client_id'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'per_client_limit' => $validated['per_client_limit'] ?? null,
            'expires_at' => $expiresAt->utc(),
            'is_active' => true,
        ]);

        return redirect()
            ->route('psychologist.discounts.show', $discount)
            ->with('status', 'کد تخفیف با موفقیت ایجاد شد.');
    }

    public function show(
        Request $request,
        DiscountCode $discount,
    ): View {
        abort_unless($request->user()?->isPsychologist(), 403);

        $discount->load('client:id,first_name,last_name,mobile');
        $discount->loadCount('redemptions');

        $redemptions = $discount->redemptions()
            ->with([
                'client:id,first_name,last_name,mobile',
                'appointment:id,tracking_code,status',
            ])
            ->latest('id')
            ->paginate(20);

        return view('psychologist.discounts.show', [
            'discount' => $discount,
            'redemptions' => $redemptions,
        ]);
    }

    public function updateStatus(
        Request $request,
        DiscountCode $discount,
    ): RedirectResponse {
        abort_unless($request->user()?->isPsychologist(), 403);

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        DB::transaction(function () use ($discount, $validated): void {
            // همان قفلی که سرویس ثبت مصرف می‌گیرد.
            $locked = DiscountCode::query()
                ->whereKey($discount->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $locked->update([
                'is_active' => (bool) $validated['is_active'],
            ]);
        }, 3);

        return redirect()
            ->route('psychologist.discounts.show', $discount)
            ->with('status', 'وضعیت کد تخفیف به‌روزرسانی شد.');
    }
}
