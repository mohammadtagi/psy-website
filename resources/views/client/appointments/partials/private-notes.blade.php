@php
    $failedForm = (string) old('_note_form', '');
@endphp

<section class="space-y-5 border border-gray-200 bg-white p-5">
    <header>
        <h2 class="text-lg font-semibold">یادداشت‌های خصوصی من</h2>

        <p class="mt-2 text-sm text-gray-600">
            این یادداشت‌ها فقط برای شما نمایش داده می‌شوند و مشاور به آن‌ها دسترسی ندارد.
        </p>
    </header>

    @if (session('status'))
        <div role="status" class="border border-green-200 bg-green-50 p-4">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div role="alert" class="border border-red-200 bg-red-50 p-4">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('client.appointments.session.notes.store', $appointment) }}"
        class="space-y-3"
    >
        @csrf

        <input type="hidden" name="_note_form" value="create">

        <label for="new_private_note" class="block font-medium">
            یادداشت جدید
        </label>

        <textarea
            id="new_private_note"
            name="body"
            rows="5"
            maxlength="10000"
            required
            class="w-full border border-gray-300 p-3"
        >{{ $failedForm === 'create' ? old('body') : '' }}</textarea>

        <button
            type="submit"
            class="bg-indigo-700 px-4 py-2 text-white"
        >
            ثبت یادداشت خصوصی
        </button>
    </form>

    <div class="space-y-4">
        @forelse ($notes as $note)
            @php
                $formKey = 'edit-' . $note->id;
                $isFailedEdit = $failedForm === $formKey;
            @endphp

            <article class="space-y-4 border border-gray-200 p-4">
                <div class="space-y-1 text-xs text-gray-500">
                    <p>
                        ثبت:
                        {{ \App\Support\PersianDate::format(
                            $note->created_at->copy()->setTimezone('Asia/Tehran'),
                            'yyyy/MM/dd HH:mm'
                        ) }}
                    </p>

                    @if ($note->updated_at->gt($note->created_at))
                        <p>
                            آخرین ویرایش:
                            {{ \App\Support\PersianDate::format(
                                $note->updated_at->copy()->setTimezone('Asia/Tehran'),
                                'yyyy/MM/dd HH:mm'
                            ) }}
                        </p>
                    @endif
                </div>

                <p class="whitespace-pre-wrap break-words text-gray-700">{{ $note->body }}</p>

                <details @if ($isFailedEdit) open @endif>
                    <summary class="cursor-pointer text-sm text-indigo-700">
                        ویرایش یادداشت
                    </summary>

                    <form
                        method="POST"
                        action="{{ route('client.appointments.session.notes.update', [
                            'appointment' => $appointment,
                            'note' => $note,
                        ]) }}"
                        class="mt-3 space-y-3"
                    >
                        @csrf
                        @method('PUT')

                        <input
                            type="hidden"
                            name="_note_form"
                            value="{{ $formKey }}"
                        >

                        <label for="private_note_{{ $note->id }}" class="block text-sm">
                            متن یادداشت
                        </label>

                        <textarea
                            id="private_note_{{ $note->id }}"
                            name="body"
                            rows="5"
                            maxlength="10000"
                            required
                            class="w-full border border-gray-300 p-3"
                        >{{ $isFailedEdit ? old('body') : $note->body }}</textarea>

                        <button
                            type="submit"
                            class="border border-indigo-700 px-4 py-2 text-sm text-indigo-700"
                        >
                            ذخیره تغییرات
                        </button>
                    </form>
                </details>

                <form
                    method="POST"
                    action="{{ route('client.appointments.session.notes.destroy', [
                        'appointment' => $appointment,
                        'note' => $note,
                    ]) }}"
                    onsubmit="return confirm('این یادداشت خصوصی حذف شود؟')"
                >
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="text-sm text-red-700">
                        حذف یادداشت
                    </button>
                </form>
            </article>
        @empty
            <p class="text-sm text-gray-500">
                هنوز یادداشت خصوصی برای این جلسه ثبت نکرده‌اید.
            </p>
        @endforelse
    </div>

    {{ $notes->links() }}
</section>
