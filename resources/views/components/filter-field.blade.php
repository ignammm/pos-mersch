@props(['filter', 'wireModel', 'error'])

@php
    $name = $filter['name'];
    $label = $filter['label'];
    $type = $filter['type'];
    $options = $filter['options'] ?? [];
@endphp

@if ($type === 'text')
    <x-main-input name="{{ $name }}" :error="$error" label="{{ $label }}" wire:model.live.debounce.200ms="{{ $wireModel }}" />
@elseif($type === 'select')
{{-- Debe ser este select porque el de livewire no funciona --}}
    <x-select :inModal="true" name="{{ $name }}" :error="$error" label="{{ $label }}" wire:model.live.debounce.200ms="{{ $wireModel }}" :options="$options" :value="array_key_first($options)" />
@elseif($type === 'date')
    <x-input name="{{ $name }}" :error="$error" label="{{ $label }}" wire:model.live.debounce.200ms="{{ $wireModel }}" type="date" />
@else
    <x-input name="{{ $name }}" :error="$error" type="{{ $type }}" label="{{ $label }}"
        wire:model.live.debounce.200ms="{{ $wireModel }}" />
@endif
