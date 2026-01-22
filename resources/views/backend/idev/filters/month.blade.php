@php
$prefix_repeatable = (isset($repeatable)) ? true : false;
$input_id = (isset($filter['name'])) ? $filter['name'] : 'id_'.$key;
$input_name = (isset($filter['name'])) ? $filter['name'] : 'name_'.$key;
$preffix_method = (isset($method)) ? $method."_" : "";
@endphp

<div class="{{ (isset($filter['class'])) ? $filter['class'] : 'form-group' }}">
    <small>{{ (isset($filter['label'])) ? $filter['label'] : 'Label '.$key }}</small>
    <input
        type="month"
        id="{{ $preffix_method }}{{ $input_id }}"
        name="{{ $input_name }}"
        class="form-control @if($prefix_repeatable) filter-repeatable @endif"
        value="{{ $filter['selected_value'] ?? '' }}"
    >
</div>

@if (request('from_ajax') && request('from_ajax') == true)
<div class="push-script">
@else
@push('scripts')
@endif
<script>
$('#{{ $preffix_method }}{{ $input_id }}').on('change', function() {
    updateFilter();
});
</script>
@if (request('from_ajax') && request('from_ajax') == true)
</div>
@else
@endpush
@endif
