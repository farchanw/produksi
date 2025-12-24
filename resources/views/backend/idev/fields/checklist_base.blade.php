@php 
$preffix_method = (isset($method)) ? $method . "_" : "";
// Ensure value is always an array for checkbox comparison
$selectedValues = isset($field['value'])
    ? (is_array($field['value']) ? $field['value'] : [$field['value']])
    : [];
@endphp

<div class="{{ $field['class'] ?? 'form-group' }}">
    <label>
        {{ $field['label'] ?? 'Label '.$key }}
        @if(isset($field['required']) && $field['required'])
            <small class="text-danger">*</small>
        @endif
    </label>

    <div class="form-control mt-2" style="max-height: 8rem; overflow-y: auto">
        @foreach($field['options'] as $key => $opt)
            <div class="form-check">
                <input 
                    class="form-check-input"
                    type="checkbox"
                    id="{{ $preffix_method }}{{ ($field['name'] ?? 'name_'.$key) }}_{{ $key }}"
                    name="{{ $field['name'] ?? 'name_'.$key }}[]"
                    value="{{ $opt['value'] }}"
                    @if(in_array($opt['value'], $selectedValues)) checked @endif
                >
                <label class="form-check-label fw-normal" for="{{ $preffix_method }}{{ ($field['name'] ?? 'name_'.$key) }}_{{ $key }}">
                    {{ $opt['text'] }}
                </label>
            </div>
        @endforeach
    </div>
</div>
