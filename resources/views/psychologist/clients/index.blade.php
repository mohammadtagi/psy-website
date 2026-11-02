<x-layouts.app>
    @section('title', 'مراجعان')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('website/style/care-clients.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('website/js/care-client-birth-picker.js') }}" defer></script>
    @endpush
    <main class="cc-page" aria-labelledby="cc-title">
        <header class="cc-header">
            <span class="cc-eyebrow">پنل مشاور</span>
            <h1 id="cc-title">مراجعان</h1>
            <p class="cc-muted">جست‌وجوی مراجع و دسترسی به پرونده او، در یک فضای مشخص.</p>
        </header>

        @if (session('status'))
            <div class="cc-alert cc-success" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="cc-alert cc-error" role="alert">
                <strong>اطلاعات واردشده نیاز به بررسی دارد.</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <section class="cc-panel cc-search" aria-labelledby="cc-search-title">
            <h2 id="cc-search-title">پیدا کردن مراجع</h2>
            <form method="GET" action="{{ route('psychologist.clients.index') }}">
                <label for="client-search">شماره موبایل، نام یا نام خانوادگی</label>
                <div class="cc-search-row">
                    <input id="client-search" name="q" type="search" value="{{ $query }}" maxlength="255" autocomplete="off" placeholder="بخشی از شماره یا نام مراجع" aria-describedby="cc-search-help{{ $errors->has('q') ? ' cc-q-error' : '' }}" @if($errors->has('q')) aria-invalid="true" @endif>
                    <button type="submit" class="cc-button cc-primary">جست‌وجو</button>
                    @if ($query !== '')
                        <a href="{{ route('psychologist.clients.index') }}" class="cc-button cc-secondary">پاک‌کردن جست‌وجو</a>
                    @endif
                </div>
                <p id="cc-search-help" class="cc-help">بدون جست‌وجو، فقط مراجعان دارای پرونده نمایش داده می‌شوند. جست‌وجو شامل حساب‌های مراجعان بدون پرونده نیز می‌شود.</p>
                @error('q')<p id="cc-q-error" class="cc-field-error">{{ $message }}</p>@enderror
            </form>
            <p class="cc-tip">برای ثبت مراجع جدید، ابتدا شماره موبایل کامل ۱۱ رقمی با شروع ۰۹ را جست‌وجو کنید.</p>
        </section>

        <section aria-labelledby="cc-results-title">
            <div class="cc-results-header">
                <div>
                    <h2 id="cc-results-title">{{ $query !== '' ? 'نتایج جست‌وجو' : 'مراجعان دارای پرونده' }}</h2>
                    @if ($query !== '')<p class="cc-muted">عبارت جست‌وجو: <bdi>{{ $query }}</bdi></p>@endif
                </div>
                <span class="cc-count">{{ number_format($clients->total()) }} مراجع</span>
            </div>

            @if ($clients->isNotEmpty())
                <div class="cc-grid">
                    @foreach ($clients as $client)
                        @php
                            $clientFullName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
                        @endphp
                        <article class="cc-card" aria-labelledby="cc-client-{{ $client->id }}">
                            <div class="cc-card-header">
                                <h3 id="cc-client-{{ $client->id }}">{{ $clientFullName ?: 'نام ثبت نشده' }}</h3>
                                <span class="cc-badge {{ $client->clinicalRecord ? 'cc-has-record' : '' }}">{{ $client->clinicalRecord ? 'دارای پرونده' : 'بدون پرونده' }}</span>
                            </div>
                            <dl class="cc-details">
                                <div><dt>شماره موبایل</dt><dd><bdi dir="ltr">{{ $client->mobile }}</bdi></dd></div>
                                <div><dt>تاریخ تولد</dt><dd>{{ $client->birth_date ? \App\Support\PersianDate::format(\Illuminate\Support\Carbon::parse($client->birth_date)) : 'ثبت نشده' }}</dd></div>
                            </dl>
                            <div class="cc-card-footer">
                                @if ($client->clinicalRecord)
                                    <a class="cc-button cc-secondary" href="{{ route('psychologist.clinical-records.show', $client) }}">مشاهده پرونده<span class="cc-sr-only"> — {{ $clientFullName ?: $client->mobile }}</span></a>
                                @else
                                    <a class="cc-button cc-primary" href="{{ route('psychologist.clinical-records.create', $client) }}">ایجاد پرونده<span class="cc-sr-only"> — {{ $clientFullName ?: $client->mobile }}</span></a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @elseif ($canCreateClient && $query !== '')
                <section class="cc-panel cc-create" aria-labelledby="cc-create-title">
                    <span class="cc-eyebrow">ثبت اولیه</span>
                    <h2 id="cc-create-title">ثبت مراجع جدید</h2>
                    <p class="cc-muted">این شماره موبایل در حساب‌ها ثبت نشده است. می‌توانید حساب مراجع را ایجاد کنید؛ ایجاد پرونده در مرحله بعد انجام می‌شود.</p>
                    <p class="cc-mobile">شماره موبایل حساب جدید <strong><bdi dir="ltr">{{ $query }}</bdi></strong></p>
                    <form method="POST" action="{{ route('psychologist.clients.store') }}">
                        @csrf
                        <input type="hidden" name="mobile" value="{{ $query }}">
                        @error('mobile')<p class="cc-field-error">{{ $message }}</p>@enderror
                        <div class="cc-form-grid">
                            <div>
                                <label for="new-client-first-name">نام <span class="cc-optional">(اختیاری)</span></label>
                                <input id="new-client-first-name" name="first_name" type="text" value="{{ old('first_name') }}" maxlength="255" autocomplete="off" @if($errors->has('first_name')) aria-invalid="true" aria-describedby="cc-first-error" @endif>
                                @error('first_name')<p id="cc-first-error" class="cc-field-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="new-client-last-name">نام خانوادگی <span class="cc-optional">(اختیاری)</span></label>
                                <input id="new-client-last-name" name="last_name" type="text" value="{{ old('last_name') }}" maxlength="255" autocomplete="off" @if($errors->has('last_name')) aria-invalid="true" aria-describedby="cc-last-error" @endif>
                                @error('last_name')<p id="cc-last-error" class="cc-field-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="cc-birth-field">
                                <label for="new-client-birth-date">تاریخ تولد شمسی <span class="cc-optional">(اختیاری)</span></label>
                                <input id="new-client-birth-date" name="birth_date" type="text" value="{{ old('birth_date') }}" placeholder="1370/01/01" dir="ltr" autocomplete="off" aria-describedby="cc-birth-help{{ $errors->has('birth_date') ? ' cc-birth-error' : '' }}" @if($errors->has('birth_date')) aria-invalid="true" @endif>
                                <button type="button" id="cc-birth-open" class="cc-button cc-secondary" hidden aria-haspopup="dialog">انتخاب از تقویم</button>
                                <p id="cc-birth-help" class="cc-help">به شکل سال/ماه/روز و با ارقام انگلیسی وارد کنید؛ مثال: <bdi dir="ltr">1370/01/01</bdi>.</p>
                                @error('birth_date')<p id="cc-birth-error" class="cc-field-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="cc-create-footer"><button type="submit" class="cc-button cc-primary">ایجاد حساب مراجع</button><span class="cc-help">این عملیات فقط حساب مراجع را ایجاد می‌کند.</span></div>
                    </form>
                </section>
            @else
                <div class="cc-panel cc-empty">
                    @if ($clients->total() > 0)
                        <h3>این صفحه نتیجه‌ای ندارد</h3>
                        <p class="cc-muted">برای مشاهده مراجعان به صفحه اول فهرست بروید.</p>
                        <a class="cc-button cc-secondary" href="{{ route('psychologist.clients.index', $query !== '' ? ['q' => $query] : []) }}">صفحه اول</a>
                    @elseif ($query !== '')
                        <h3>مراجعی با این مشخصات پیدا نشد</h3>
                        <p class="cc-muted">املای نام یا شماره را بررسی کنید و دوباره جست‌وجو کنید.</p>
                        <a class="cc-button cc-secondary" href="{{ route('psychologist.clients.index') }}">بازگشت به فهرست پرونده‌ها</a>
                    @else
                        <h3>هنوز مراجعی دارای پرونده نیست</h3>
                        <p class="cc-muted">برای یافتن حساب موجود یا ثبت مراجع جدید، از جست‌وجوی بالای صفحه استفاده کنید.</p>
                        <a class="cc-button cc-secondary" href="#client-search">جست‌وجوی مراجع</a>
                    @endif
                </div>
            @endif
            @if ($clients->hasPages())
                <div class="cc-pagination">{{ $clients->links() }}</div>
            @endif
        </section>
    </main>
</x-layouts.app>
