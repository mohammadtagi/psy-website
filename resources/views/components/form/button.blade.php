@props([
    'type' => 'submit',
    'variant' => 'primary',
])

<button
    type="{{ $type }}"

    {{ $attributes->class([
        'inline-flex w-full items-center justify-center rounded-xl',
        'px-4 py-3 text-sm font-semibold transition',
        'focus-visible:outline-none focus-visible:ring-2',
        'focus-visible:ring-teal-600 focus-visible:ring-offset-2',
        'disabled:cursor-not-allowed disabled:opacity-50',

        'bg-teal-700 text-white hover:bg-te-800' => $variant === 'primary',

        ' border border-slate-300 bg-white text-slate-700 hover:bg-slate-50'
            => $variant === 'secondary',
    ]) }}
>
    {{ $slot }}
</button>
