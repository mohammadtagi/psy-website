@props([
    'name',
    'label',
    'id' => null,
    'type' => 'text',
    'value' => '',
    'hint' => null,
])

@php
    $inputId = $id ?? $name;
    $hasError = $errors->has($name);

    $describedBy = trim(
        ($hint ? $inputId . '-hint ' : '') .
        ($hasError ? $inputId . '-error' : '')
    );
@endphp

<div>
    <label
        for="{{ $inputId }}"
        class="mb-2 block text-sm font-medium text-slate-700"
    >
        {{ $label }}
    </label>

    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $value }}"
        @if($hasError) aria-invalid="true" @endif
        @if($describedBy) aria-describedby="{{ $describedBy }}" @endif

        {{ $attributes->class([
            'block w-full rounded-xl border bg-white px-4 py-3 text-base',
            'text-slate-900 outline-none transition placeholder:text-slate-400',
            'focus:ring-2',
            'border-slate-300 focus:border-teal-600 focus:ring-teal-100' => ! $hasError,
            'border-red-500 focus:border-red-500 focus:ring-red-100' => $hasError,
        ]) }}
    >

    @if($hint)
        <p
            id="{{ $inputId }}-hint"
            class="mt-2 text-xs leading-6 text-slate-500"
        >
            {{ $hint }}
        </p>
    @endif

    @error($name)
    <p
        id="{{ $inputId }}-error"
        role="alert"
        class="mt-2 text-sm text-red-600"
    >
        {{ $message }}
    </p>
    @enderror
</div>
