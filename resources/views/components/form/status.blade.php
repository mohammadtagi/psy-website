@props([
    'message' => null,
    'type' => 'success',
])

@if($message)
    <div
        role="{{ $type === 'error' ? 'alert' : 'status' }}"

        @class([
            'rounded-xl border px-4 py-3 text-sm leading-7',

            'border-emerald-200 bg-emerald-50 text-emerald-800'
                => $type === 'success',

            'border-red-200 bg-red-50 text-red-800'
                => $type === 'error',

            'border-sky-200 bg-sky-50 text-sky-800'
                => $type === 'info',
        ])
    >
        {{ $message }}
    </div>
@endif
