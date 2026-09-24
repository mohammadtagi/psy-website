<section class="border border-gray-200 bg-white p-5 md:col-span-2">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">
                یادداشت‌های جلسات
            </h2>

            <p class="mt-2 text تعداد یادداشت-600">
                تعداد یادداشت‌ها:
                {{ $record->notes->count() }}
            </p>
        </div>

        <a
            href="{{ route('psychologist.clinical-notes.create', $record) }}"
            class="border border-indigo-700 bg-indigo-700 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-800"
        >
            ثبت یادداشت
        </a>
    </div>

    @if ($record->notes->isEmpty())
        <p class="mt-5 text-sm text-gray-500">
            هنوز یادداشتی برای این پرونده ثبت نشده است.
        </p>
    @else
        <div class="mt-5 space-y-4">
            @foreach ($record->notes as $note)
                <article class="border border-gray-200 p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $note->session_at?->format('Y/m/d H:i') ?: 'تاریخ ثبت نشده' }}
                            </p>

                            @if ($note->appointment)
                                <p class="mt-1 text-xs text-gray-500">
                                    نوبت:
                                    {{ $note->appointment->starts_at?->format('Y/m/d H:i') }}
                                </p>
                            @else
                                <p class="mt-1 text-xs text-gray-500">
                                    یادداشت مستقل از نوبت
                                </p>
                            @endif
                        </div>

                        <div class="flex gap-3">
                            <a
                                href="{{ route('psychologist.clinical-notes.edit', $note) }}"
                                class="text-sm text-indigo-700 hover:underline"
                            >
                                ویرایش
                            </a>

                            <form
                                method="POST"
                                action="{{ route('psychologist.clinical-notes.destroy', $note) }}"
                                onsubmit="return confirm('آیا از حذف این یادداشت مطمئن هستید؟')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-sm text-red-700 hover:underline"
                                >
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>

                    @if ($note->summary)
                        <p class="mt-4 whitespace-pre-line text-sm leading-7 text-gray-700">
                            {{ $note->summary }}
                        </p>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</section>
