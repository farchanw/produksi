@php
    $prefix_repeatable = isset($repeatable) ? true : false;
    $preffix_method = isset($method) ? $method . '_' : '';
@endphp
<div class="{{ isset($field['class']) ? $field['class'] : 'form-group' }}">
    <div class="card-repeatable-aspek-kpi-item">
        <label for="s">{{ isset($field['label']) ? $field['label'] : 'Label ' . $key }}</label>
        <b class="float-end text-subtotal"></b>
        <div class="{{ $preffix_method }}repeatable-sections">
            @php
                $field_count = 0;
                $enable_action = $field['enable_action'];
            @endphp

            <div id="{{ $preffix_method }}repeatable-0" class="row {{ $preffix_method }}field-sections">

                <div class="col my-2">
                    <label>KPI</label>
                    <select name="master_kpi_id[]" class="form-control idev-form form-master_kpi_id">
                        <option value="">Pilih KPI...</option>
                        @foreach ($field['field_data']['master_kpi'] as $k)
                            <option value="{{ $k['value'] }}">{{ $k['text'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 my-2">
                    <label>Satuan</label>
                    <select name="satuan[]" class="form-control idev-form form-satuan">
                        <option value="">Pilih Satuan...</option>
                        <option value="angka">Angka</option>
                        <option value="persentase">Persentase</option>
                    </select>
                </div>
                <div class="col-md-1 my-2">
                    <label>Bobot</label>
                    <input type="number" name="bobot[]" value="" class="form-control idev-form form-target">
                </div>
                <div class="col-md-1 my-2">
                    <label>Target</label>
                    <input type="number" name="target[]" value="" class="form-control idev-form form-target">
                </div>

                <div class="col-md-1 remove-section">
                    <button type="button" class="btn btn-sm btn-circle btn-danger my-4 text-white" onclick="remove(0)">
                        <i class="ti ti-minus" data-toggle="tooltip" data-placement="top" title="Remove"> </i>
                    </button>
                </div>
            </div>
        </div>

        @if($enable_action)
            <div class="row">
                <div class="col-md-4">
                    <button type="button" class="btn btn-sm btn-secondary my-2 text-white rounded-1"
                        onclick="add('{{ $preffix_method }}')">
                        <i class="ti ti-plus" data-toggle="tooltip" data-placement="top" title="Add"> </i> 1 ITEM
                    </button>
                </div>
            </div>
        @endif
    </div>

</div>
@push('styles')
    <style>
        .offcanvas {
            width: 100%!important;
        }
        .btn-circle {
            border-radius: 50%;
        }
        .card-repeatable-aspek-kpi-item
        {
            padding: 4px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
    </style>
@endpush
