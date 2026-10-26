<x-layouts.app>
    <main class="mx-auto max-w-4xl space-y-5 px-4 py-8">
        <a
            href="{{ route('psychologist.clinical-records.show', $client) }}"
            class="text-sm text-indigo-700"
        >
            بازگشت به پرونده
        </a>

        <h1 class="text-2xl font-bold">ویرایش یادداشت محرمانه</h1>

        <form
            method="POST"
            action="{{ route('psychologist.clinical-notes.update', [
                'clinicalNote' => $note,
            ]) }}"
        >
            @csrf
            @method('PUT')

            @include('psychologist.clinical-notes.form')
        </form>
    </main>
</x-layouts.app>
