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




<script>
window.formPrefixMethod = '{{ $prefix_method }}';

$('[name="aspek_kpi_header_id"]').on('change', function () {
    const aspekKpiHeaderId = $(this).val();
    const $container = $('.dynamic-form-kpi-aspek-values');

    const kategori = $('input[name="kategori"]').val();
    const bulan = $('input[name="bulan"]').val();
    const tahun = $('input[name="tahun"]').val();
    const kode = $('input[name="kode"]').val();

    if (!aspekKpiHeaderId) {
        $container.empty();
        return;
    }

    $.ajax({
        url: 'kpi-production-fetch-aspek-kpi-item-default',
        method: 'GET',
        data: { 
            aspek_kpi_header_id: aspekKpiHeaderId,
            kategori: kategori,
            bulan: bulan,
            tahun: tahun,
            kode: kode,
         },
        success: function (response) {
            $container.empty();
            const template = document.getElementById('kpi-aspek-template');
           
            const editValues = $(`[name="${window.formPrefixMethod}aspek_values"]`).val();
            let editValuesParsed = {};
            if (editValues) {
                editValuesParsed = JSON.parse(editValues);
            }

            response.forEach((item, index) => {
                const clone = template.content.cloneNode(true);
                // get edit value match by item.id
                const editValue = editValuesParsed.find((value) => Number(value.aspek_kpi_item_id) === Number(item.id)) || {};

                // Fill display inputs
                $(clone).find('.item-nama').text(item.nama);
                $(clone).find('.item-sumber-data').text(item.sumber_data_realisasi);

                $(clone).find('.input-area-kinerja-utama').val(item.area_kinerja_utama);
                $(clone).find('.input-bobot').val(item.bobot);
                $(clone).find('.input-target').val(item.target);
                $(clone).find('.input-tipe').val(item.tipe);
                $(clone).find('.input-satuan').val(item.satuan);
                $(clone).find('.input-skor').val(editValue.skor || item.skor || '');
                $(clone).find('.input-skor-akhir').val(editValue.skor_akhir || item.skor_akhir || '');
                $(clone).find('.input-kpi-id').val(item.id);
                $(clone).find('.input-realisasi').val(editValue.realisasi || '');

                // Dynamically create hidden inputs for readonly/display values
                const $row = $(clone).find('.row');
                const fields = [
                    'area_kinerja_utama', 'bobot', 'target', 'tipe', 'satuan', 'skor', 'skor_akhir', 'aspek_kpi_item_id'
                ];


                fields.forEach(field => {
                    let value = '';
                    if (field === 'area_kinerja_utama') value = item.area_kinerja_utama;
                    if (field === 'bobot') value = item.bobot;
                    if (field === 'target') value = item.target;
                    if (field === 'tipe') value = item.tipe;
                    if (field === 'satuan') value = item.satuan;
                    if (field === 'skor') value = item.skor || '';
                    if (field === 'skor_akhir') value = item.skor_akhir || '';
                    if (field === 'aspek_kpi_item_id') value = item.id;

                    $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', `aspek_values_input[${index}][${field}]`)
                        .val(value)
                        .appendTo($row);
                });

                


                // Realisasi input is already editable, just set its name
                $(clone).find('.input-realisasi').attr('name', `aspek_values_input[${index}][realisasi]`);

                $container.append(clone);
            });
        }
    });
});

</script>


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
