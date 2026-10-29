@if (session('status'))
    <div
        class="mb-6 border-r-4 border-green-600 bg-green-50 p-4 text-sm text-green-800"
        role="status"
    >
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div
        class="mb-6 border-r-4 border-red-600 bg-red-50 p-4 text-sm text-red-800"
        role="alert"
    >
        <ul class="list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
