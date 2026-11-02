@php
    $failedForm = (string) old('_note_form', '');
@endphp
<section class="ccs-card ccs-notes" id="ccs-notes" data-session-url="{{ route('client.appointments.session.show', $appointment) }}" aria-labelledby="ccs-notes-title">
    <div class="ccs-heading"><h2 id="ccs-notes-title">یادداشت‌های خصوصی من</h2><span class="ccs-muted ccs-small">{{ $notes->total() }} یادداشت</span></div>
    <p class="ccs-muted ccs-small">این یادداشت‌ها فقط برای شما نمایش داده می‌شوند و مشاور به آن‌ها دسترسی ندارد.</p>
    <p class="ccs-feedback" role="status" aria-live="polite" tabindex="-1">{{ session('status') }}</p>
    @if ($errors->any())
        <div class="ccs-error" role="alert"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('client.appointments.session.notes.store', $appointment) }}" data-note-action="create" class="ccs-create">
        @csrf
        <input type="hidden" name="_note_form" value="create">
        <label for="new_private_note">یادداشت جدید</label>
        <p id="ccs-create-hint" class="ccs-muted ccs-small">موضوع‌ها یا سؤال‌هایی که می‌خواهید به خاطر بسپارید؛ حداکثر ۱۰٬۰۰۰ نویسه. ذخیره خودکار نیست.</p>
        <textarea id="new_private_note" name="body" rows="3" maxlength="10000" required aria-describedby="ccs-create-hint">{{ $failedForm === 'create' ? old('body') : '' }}</textarea>
        <p class="ccs-form-message" role="status" aria-live="polite" tabindex="-1"></p>
        <button type="submit" class="ccs-button">ثبت یادداشت</button>
    </form>
    <div class="ccs-note-list">
    @forelse ($notes as $note)
        @php
            $formKey = 'edit-' . $note->id;
            $isFailedEdit = $failedForm === $formKey;
        @endphp
        <article class="ccs-note" id="ccs-note-{{ $note->id }}">
            <div class="ccs-muted ccs-small">
                <p>ثبت: {{ \App\Support\PersianDate::format($note->created_at->copy()->setTimezone('Asia/Tehran'), 'yyyy/MM/dd HH:mm') }}</p>
                @if ($note->updated_at->gt($note->created_at))
                    <p>ویرایش: {{ \App\Support\PersianDate::format($note->updated_at->copy()->setTimezone('Asia/Tehran'), 'yyyy/MM/dd HH:mm') }}</p>
                @endif
            </div>
            <p class="ccs-text">{{ $note->body }}</p>
            <details @if ($isFailedEdit) open @endif>
                <summary>ویرایش یادداشت</summary>
                <form method="POST" action="{{ route('client.appointments.session.notes.update', ['appointment' => $appointment, 'note' => $note]) }}" data-note-action="edit">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_note_form" value="{{ $formKey }}">
                    <label for="private_note_{{ $note->id }}">متن یادداشت · حداکثر ۱۰٬۰۰۰ نویسه</label>
                    <textarea id="private_note_{{ $note->id }}" name="body" rows="4" maxlength="10000" required>{{ $isFailedEdit ? old('body') : $note->body }}</textarea>
                    <p class="ccs-form-message" role="status" aria-live="polite" tabindex="-1"></p>
                    <div class="ccs-actions"><button type="submit" class="ccs-button">ذخیره تغییرات</button><button type="button" class="ccs-secondary" data-cancel-edit hidden>انصراف از ویرایش</button></div>
                </form>
            </details>
            <details class="ccs-delete">
                <summary>حذف یادداشت</summary>
                <p class="ccs-small">این یادداشت برای همیشه حذف می‌شود و قابل بازگشت نیست.</p>
                <form method="POST" action="{{ route('client.appointments.session.notes.destroy', ['appointment' => $appointment, 'note' => $note]) }}" data-note-action="delete">
                    @csrf
                    @method('DELETE')
                    <p class="ccs-form-message" role="status" aria-live="polite" tabindex="-1"></p>
                    <button type="submit" class="ccs-danger">تأیید حذف دائمی</button>
                </form>
            </details>
        </article>
    @empty
        <p class="ccs-empty">هنوز یادداشتی برای این جلسه ندارید. می‌توانید اولین یادداشت را از کادر بالا اضافه کنید.</p>
    @endforelse
    </div>
    <div class="ccs-pagination">{{ $notes->links() }}</div>
</section>
