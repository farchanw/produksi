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
                    <div class="input-group input-group-sm">
                        <input type="number" step="any" 
                            class="form-control input-realisasi" 
                            placeholder="Input realisasi" 
                            required>

                        <button class="btn btn-primary btn-search-realisasi" 
                                type="button" 
                                data-bs-toggle="modal" 
                                data-bs-target="#realisasiModal">
                            <i class="ti ti-search"></i>
                        </button>
                    </div>
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
$(document).ready(function () {
    $('body').append(/*html*/`
        <div class="modal fade" id="realisasiModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pilih Realisasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>
    `);

    /*
    <button class="btn btn-light w-100 mb-2 select-realisasi" data-value="100000">
        100,000
    </button>
    */

    // on modal open
    $('#realisasiModal').on('show.bs.modal', function (e) {
        // get form from e
        const form = e.relatedTarget.closest('form');
        const kategori = $(form).find('[name="kategori"]').val();
        const periode = $(form).find('[name="periode"]').val();
        const kode = $(form).find('[name="kode"]').val();

        const modalBody = $(this).find('.modal-body');

        // console.log(kategori, periode, kode);

        modalBody.empty();

        if (window.kpiProductionSelectRealisasiData && window.kpiProductionSelectRealisasiData[`${kategori}-${periode}-${kode}`]) {
            window.kpiProductionSelectRealisasiData[`${kategori}-${periode}-${kode}`].forEach((item) => {
                modalBody.append(/*html*/`
                    <button class="btn btn-light w-100 mb-2 select-realisasi" data-value="${item.value}">
                        ${item.text}
                    </button>
                `);
            });
            return;
        }

        // show loading
        modalBody.html(`
            <div class="d-flex justify-content-center align-items-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);


        

        $.ajax({
            url: 'kpi-production-fetch-realisasi-default',
            method: 'GET',
            data: {
                kategori: kategori,
                periode: periode,
                kode: kode,
            },
            success: function (response) {
                modalBody.empty();
                // response is json of data
                if (response.length > 0) {
                    window.kpiProductionSelectRealisasiData = window.kpiProductionSelectRealisasiData || [];
                    window.kpiProductionSelectRealisasiData[`${kategori}-${periode}-${kode}`] = response;
                    response.forEach((item) => {
                        modalBody.append(/*html*/`
                            <button class="btn btn-light w-100 mb-2 select-realisasi" data-value="${item.value}">
                                ${item.text}
                            </button>
                        `);
                    });
                }
            },
            error: function (xhr, status, error) {
                modalBody.html(`
                    <div class="alert alert-danger">
                        Something went wrong
                    </div>
                `);
            }
        });
    });


})
window.activeRealisasiInput = null;

// When search button is clicked, remember which input it belongs to
document.addEventListener('click', function (e) {
    if (e.target.closest('.btn-search-realisasi')) {
        window.activeRealisasiInput = e.target
            .closest('.input-group')
            .querySelector('.input-realisasi');
    }

    // When selecting value from modal
    if (e.target.closest('.select-realisasi')) {
        window.activeRealisasiInput.value = e.target.dataset.value;

        // Close modal
        const modal = bootstrap.Modal.getInstance(
            document.getElementById('realisasiModal')
        );
        modal.hide();
    }

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
        
        .dynamic-form-kpi-aspek-values label,
        .dynamic-form-kpi-aspek-values input {
            font-size: .875rem;
        }

        .select-realisasi {
            background-color: #cacaca!important;
        }

    </style>
@endpush
