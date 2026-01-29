@php
    $prefix_repeatable = isset($repeatable);
    $prefix_method = isset($method) ? $method . '_' : '';
@endphp

<div class="{{ $field['class'] ?? 'form-group' }}">
    <label>{{ $field['label'] ?? 'Label ' . $key }}</label>

    <div class="card-repeatable-aspek-kpi-item">
        <div class="{{ $prefix_method }}repeatable-sections">
            @php
                $enable_action = $field['enable_action'] ?? true;
            @endphp
        </div>

        @if ($enable_action)
            <div class="mt-2">
                <button type="button"
                        class="btn btn-sm btn-secondary text-white rounded-1"
                        onclick="addAspekKpiItem('{{ $prefix_method }}')">
                    <i class="ti ti-plus"></i> Add New
                </button>
            </div>
        @endif
    </div>
</div>

<template id="node-repeatable-aspek-kpi-item-template">
    <div id="{{ $prefix_method }}repeatable-0"
            class="row repeatable-kpi-field-sections {{ $prefix_method }}field-sections">
        
        <input type="hidden" name="kpi[0][id]" value="0">

        <div class="col my-2">
            <label>KPI</label>
            <select name="kpi[0][master_kpi_id]"
                    class="form-control form-control-sm idev-form">
                <option value="">Select...</option>
                @foreach ($field['field_data']['master_kpi'] as $k)
                    <option value="{{ $k['value'] }}">
                        {{ $k['text'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-1 my-2">
            <label>Bobot</label>
            <input type="number"
                    name="kpi[0][bobot]"
                    class="form-control form-control-sm idev-form">
        </div>

        <div class="col-md-1 my-2">
            <label>Target</label>
            <input type="number"
                    name="kpi[0][target]"
                    class="form-control form-control-sm idev-form">
        </div>

        <div class="col-md-1 my-2 remove-section">
            <button type="button"
                    class="btn btn-sm btn-circle btn-danger text-white mt-4"
                    onclick="removeAspekKpiItem('{{ $prefix_method }}', 0)">
                <i class="ti ti-minus"></i>
            </button>
        </div>
    </div>
</template>



@push('styles')
    <style>
        .offcanvas {
            width: 100%!important;
        }
        .btn-circle {
            border-radius: 50%;
        }
        .create_field-sections,
        .edit_field-sections
        {
            padding: 0;
            margin: .5rem 0;
        }
        .card-repeatable-aspek-kpi-item {
            padding: .5rem;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
    </style>
@endpush
