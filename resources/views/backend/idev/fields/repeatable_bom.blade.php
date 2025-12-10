@php
    $prefix_repeatable = isset($repeatable) ? true : false;
    $preffix_method = isset($method) ? $method . '_' : '';
@endphp
<div class="{{ isset($field['class']) ? $field['class'] : 'form-group' }}">
    <div class="card-repeatable-bom">
        <label for="s">{{ isset($field['label']) ? $field['label'] : 'Label ' . $key }}</label>
        <b class="float-end text-subtotal"></b>
        <div class="{{ $preffix_method }}repeatable-sections">
            @php
                $field_count = 0;
                $enable_action = $field['enable_action'];
            @endphp

            <div id="{{ $preffix_method }}repeatable-0" class="row {{ $preffix_method }}field-sections">

                <div class="col-md-4 my-2">
                    <label>Material</label>
                    <select name="material_id[]" class="form-control idev-form form-material">
                        <option value="">Select Material</option>
                        @foreach ($field['materials'] as $key => $material)
                            <option value="{{ $material->value }}" data-price="{{ $material->price }}"
                                data-unit="{{ $material->unit }}">{{ $material->text }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 my-2">
                    <label>Qty</label>
                    <input type="number" name="quantity[]" value="" class="form-control idev-form form-qty">
                </div>
                <div class="col-md-2 my-2">
                    <label>Satuan</label>
                    <select name="unit[]" class="form-control idev-form form-unit">
                        <option value="">Select</option>
                        @foreach ($field['units'] as $key => $unit)
                            <option value="{{ $unit['value'] }}">{{ $unit['text'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 my-2">
                    <label>Harga</label>
                    <div class="form-control">
                        <span class="text-total-price">0</span>
                    </div>
                    <input type="hidden" name="price_per_unit[]" value="0">
                    <input type="hidden" name="total_price[]" value="0">
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
        .btn-circle {
            border-radius: 50%;
        }
        .card-repeatable-bom
        {
            padding: 4px;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
    </style>
@endpush
