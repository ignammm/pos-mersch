<div
    {{ $attributes->merge([
        'class' =>
            'w-full rounded-2xl shadow-sm border border-gray-200 p-5 bg-white',
    ]) }}>
    {{ $slot }}
</div>
