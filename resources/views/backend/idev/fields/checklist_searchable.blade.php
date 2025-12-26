@php 
$preffix_method = (isset($method)) ? $method . "_" : "";
// Ensure value is always an array for checkbox comparison
$selectedValues = isset($field['value'])
    ? (is_array($field['value']) ? $field['value'] : [$field['value']])
    : [];
@endphp

<div class="{{ $field['class'] ?? 'form-group' }}">
    <div class="form-field-checklist-searchable-container-{{ $field['name'] }}">
        <label>
            {{ $field['label'] ?? 'Label '.$key }}
            @if(isset($field['required']) && $field['required'])
                <small class="text-danger">*</small>
            @endif
        </label>

        <div class="form-control mt-2" style="max-height: 8rem; overflow-y: auto">
            <div style="display: grid; grid-template-columns: auto 2.5rem 2.5rem; gap: .75rem;">
                <input placeholder="Search..." class="form-control form-field-checklist-searchable-filter">
                <button type="button" class="btn btn-sm btn-primary form-field-checklist-searchable-select-all" title="Select All">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5z"/>
                        <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0"/>
                    </svg>
                </button>
                <button type="button" class="btn btn-sm btn-danger form-field-checklist-searchable-select-none" title="Delete All Selection">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                    </svg>
                </button>
            </div>
            
            <div class="form-field-checklist-searchable-check-item-list">
                @foreach($field['options'] as $key => $opt)
                    <div class="form-check">
                        <input 
                            class="form-check-input"
                            type="checkbox"
                            id="{{ $preffix_method }}{{ ($field['name'] ?? 'name_'.$key) }}_{{ $key }}"
                            name="{{ $field['name'] ?? 'name_'.$key }}[]"
                            value="{{ $opt['value'] }}"
                            data-text-for-searchable="{{ $opt['text'] }}"
                            @if(in_array($opt['value'], $selectedValues)) checked @endif
                        >
                        <label class="form-check-label fw-normal" for="{{ $preffix_method }}{{ ($field['name'] ?? 'name_'.$key) }}_{{ $key }}">
                            {{ $opt['text'] }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
