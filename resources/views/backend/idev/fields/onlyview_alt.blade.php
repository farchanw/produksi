@php
    $prefix = isset($method) ? "{$method}_" : '';
    $name   = $field['name']  ?? "id_{$key}";
    $label  = $field['label'] ?? "Label {$key}";
    $value  = $field['value'] ?? '';
    $text   = $field['text'] ?? $value;
    $class  = $field['class'] ?? 'form-group';
@endphp

<div class="{{ $class }}">
    <label>{{ $label }}</label>

    <div class="form-control form-control-disabled" disabled readonly>
        <span id="text_{{ $prefix . $name }}">
            {{ $text }}
        </span>
    </div>

    <input type="hidden" id="{{ $prefix . $name }}" name="{{ $name }}" value="{{ $value }}">
</div>
