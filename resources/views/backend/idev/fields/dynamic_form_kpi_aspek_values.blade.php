@php
    $prefix_repeatable = isset($repeatable);
    $prefix_method = isset($method) ? $method . '_' : '';
@endphp

<div class="{{ $field['class'] ?? 'form-group' }}">
    <label>{{ $field['label'] ?? 'Label ' . $key }}</label>
    <input type="hidden" name="aspek_values" id="aspek_values" value="{{ isset($field['value']) ? $field['value'] : '' }}">
    <input type="hidden" name="{{ $prefix_method }}aspek_values" id="{{ $prefix_method }}aspek_values" value="{{ isset($field['value']) ? $field['value'] : '' }}">

    <section class="dynamic-form-kpi-aspek-values"></section>
</div>

<template id="kpi-aspek-template">
    <div class="card">
        <div class="mb-3">
            <h5 class="text-secondary">
                <span class="item-nama"></span> -
                <i class="fw-normal">
                    (Sumber Data Realisasi: <span class="item-sumber-data"></span>)
                </i>
            </h5>

            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">Area Kinerja Utama</label>
                    <input type="text" class="form-control form-control-sm input-area-kinerja-utama" disabled>
                </div>

                <div class="col-md-1">
                    <label class="form-label">Bobot</label>
                    <input type="text" class="form-control form-control-sm input-bobot" disabled>
                </div>

                <div class="col-md-1">
                    <label class="form-label">Target</label>
                    <input type="text" class="form-control form-control-sm input-target" disabled>
                </div>

                <div class="col-md-1">
                    <label class="form-label">Tipe</label>
                    <input type="text" class="form-control form-control-sm input-tipe" disabled>
                </div>

                <div class="col-md-1">
                    <label class="form-label">Satuan</label>
                    <input type="text" class="form-control form-control-sm input-satuan" disabled>
                </div>

                <div class="col">
                    <label class="form-label fw-bold">Realisasi</label>
                    <input type="number" step="any" class="form-control form-control-sm input-realisasi" placeholder="Input realisasi" required>
                </div>

                <div class="col-md-1">
                    <label class="form-label">Skor</label>
                    <input type="text" class="form-control form-control-sm input-skor" disabled>
                </div>

                <div class="col-md-1">
                    <label class="form-label">Skor Akhir</label>
                    <input type="text" class="form-control form-control-sm input-skor-akhir" disabled>
                </div>

                <input type="hidden" class="input-kpi-id">
            </div>
        </div>
    </div>
</template>






@push('styles')
    <style>
        .offcanvas {
            width: 100%!important;
        }

        .dynamic-form-kpi-aspek-values {
            padding: 1rem;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
    </style>
@endpush
