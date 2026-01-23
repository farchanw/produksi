$( document ).ajaxStop(function() {
    $('.support-live-select2').each(function () {
        $(this).select2({
            theme: 'bootstrap-5',
            dropdownParent: $(this).parent(),// fix select2 search input focus bug
        })
        
    })
});


document.addEventListener('change', function (event) {
    if (event.target.matches('select[name="master_subsection_id"]')) {
        const subsectionId = event.target.closest('form').querySelector('[name="master_subsection_id"]').value;
        fetch(`kpi-production-fetch-master-kpi-default?subsection_id=${subsectionId}`)
            .then(response => response.json())
            .then(data => {
                window.KpiProductionAspekKpiItemOptionsData = data;

                // update all matched selects options
                document.querySelectorAll('select[name^="kpi"]').forEach(el => {
                    el.innerHTML = '';
                    el.appendChild(new Option('Select...', ''));
                    data.forEach(opt => {
                        el.appendChild(new Option(opt.text, opt.value));
                    });
                });
            });

    }
});


function addAspekKpiItem(prefix = '', values = {}, optionsKpiMaster = window.KpiProductionAspekKpiItemOptionsData) {
    if (!optionsKpiMaster) {
        optionsKpiMaster = [{ value: '', text: 'Select...' }];
    }

    const container = document.querySelector(`.${prefix}repeatable-sections`);
    if (!container) return;

    const sections = container.querySelectorAll('.repeatable-kpi-field-sections');
    const newIndex = sections.length;

    const nodeTemplate = document.querySelector('#node-repeatable-aspek-kpi-item-template');
    const fragment = document.importNode(nodeTemplate.content, true);

    // 🔥 IMPORTANT: get the real section element
    const section = fragment.querySelector('.repeatable-kpi-field-sections');
    section.id = `${prefix}repeatable-${newIndex}`;

    // Update inputs/selects
    section.querySelectorAll('input, select').forEach(el => {
        const name = el.getAttribute('name');
        if (name) {
            el.setAttribute('name', name.replace(/\[\d+\]/, `[${newIndex}]`));
        }

        const keyMatch = el.getAttribute('name')?.match(/\[([a-zA-Z_]+)\]/);
        if (!keyMatch) return;
        const key = keyMatch[1];

        if (key === 'master_kpi_id' && optionsKpiMaster.length) {
            el.innerHTML = '';
            el.appendChild(new Option('Select...', ''));
            optionsKpiMaster.forEach(opt => {
                const optionEl = new Option(opt.text, opt.value);
                if (values[key] == opt.value) optionEl.selected = true;
                el.appendChild(optionEl);
            });
        } else {
            el.value = values[key] ?? '';
        }
    });

    // Fix remove button
    const removeBtn = section.querySelector('.remove-section button');
    removeBtn.setAttribute(
        'onclick',
        `removeAspekKpiItem('${prefix}', ${newIndex})`
    );

    container.appendChild(section);
}

function removeAspekKpiItem(prefix = '', index) {
    const container = document.querySelector(`.${prefix}repeatable-sections`);
    if (!container) return;

    const sections = container.querySelectorAll('.repeatable-kpi-field-sections');
    //if (sections.length <= 1) return;

    const target = sections[index];
    if (!target) return;

    const hasValue = Array.from(
        target.querySelectorAll('input, select')
    ).some(el => el.value && el.value.toString().trim() !== '');

    if (hasValue && !confirm('This item contains data. Are you sure you want to remove it?')) {
        return;
    }

    target.remove();
    reindexAspekKpiItem(prefix);
}

function reindexAspekKpiItem(prefix = '') {
    const container = document.querySelector(`.${prefix}repeatable-sections`);
    if (!container) return;

    const sections = container.querySelectorAll('.repeatable-kpi-field-sections');

    sections.forEach((section, i) => {
        section.id = `${prefix}repeatable-${i}`;

        section.querySelectorAll('input, select').forEach(el => {
            const name = el.getAttribute('name');
            if (name) {
                el.setAttribute(
                    'name',
                    name.replace(/\[\d+\]/, `[${i}]`)
                );
            }
        });

        const removeBtn = section.querySelector('.remove-section button');
        if (removeBtn) {
            removeBtn.setAttribute(
                'onclick',
                `removeAspekKpiItem('${prefix}', ${i})`
            );
        }
    });
}




function renderAspekKpiItem(selectElement, response) {
    const $form = selectElement.closest('form');
    const $container = $form.find('.dynamic-form-kpi-aspek-values');
    $container.empty();
    const template = document.getElementById('kpi-aspek-template');
    const editValues = $(`[name="edit_aspek_values"]`).val();
    
    let editValuesParsed = [];
    if ($form.attr('id')?.startsWith('form-edit-')) {
        if (editValues) {
            editValuesParsed = JSON.parse(editValues);
        }
    }

    if ($form.attr('id')?.startsWith('form-create-')) {
        $form.find('[name="kategori"]').val(window.formKategoriValue);
    }

    response.forEach((item, index) => {
        const clone = template.content.cloneNode(true);
        // get edit value match by item.id
        let editValue;

        if (editValuesParsed) {
            editValue = editValuesParsed.find((value) => Number(value.aspek_kpi_item_id) === Number(item.id)) || {};
        }

        // Fill display inputs
        $(clone).find('.item-nama').text(`${index+1}. ${item.nama}`);
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
/* 
$('[name="aspek_kpi_header_id"]').on('change', function () {
    const aspekKpiHeaderId = $(this).val();
    const $container = $('.dynamic-form-kpi-aspek-values');

    const kategori = $('input[name="kategori"]').val();
    const periode = $('input[name="periode"]').val();
    const kode = $('input[name="kode"]').val();

    $.ajax({
        url: 'kpi-production-fetch-aspek-kpi-item-default',
        method: 'GET',
        data: { 
            aspek_kpi_header_id: aspekKpiHeaderId,
            kategori: kategori,
            kode: kode,
            periode: periode,
         },
        success: function (response) {
            renderAspekKpiItem(response);
        }
    });
});
 */





// Modal Export Laporan PDF
const tmplModalExportLaporanPdf = /*html*/`
<div class="modal fade" id="modalExportLaporanPdf" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="get" id="formExportLaporanPdf" target="_blank">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title text-white">Cetak Laporan Bulanan PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="d-flex align-items-center mb-2">
                        <label class="me-2 mb-0" style="min-width:70px;">Nama</label>
                        <select id="laporan-personal-select-employee" name="nik" class="form-control form-control-sm support-live-select2"></select>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <label class="me-2 mb-0" style="min-width:70px;">Periode</label>
                        <input 
                            type="month"
                            name="periode"
                            class="form-control form-control-sm"
                            required
                        >
                    </div>
                </div>


                <div class="modal-footer mt-2">
                    <button type="button" class="btn btn-muted" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Cetak
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>`

$('body').append(tmplModalExportLaporanPdf);

 
// Modal Bulk Action
const tmplModalBulkAction = /*html*/`
<div class="modal fade" id="modalBulkAction" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" id="formBulkAction" target="_blank">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Bulk Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <span></span>
                </div>


                <div class="modal-footer mt-2">
                    <button type="button" class="btn btn-muted" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Submit
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>`

$('body').append(tmplModalBulkAction);












$( document ).ready(function() {
    const $kategori = $('[name="kategori"]');
    if ($kategori.length) {
        window.formKategoriValue = $kategori.val();
    }

    $('[name="kode"]').on('select2:select', function () {
        const $container = $('.dynamic-form-kpi-aspek-values');
        if(!$container.length) return;

        const selectElement = $(this);
        const kategori = $(this).closest('form').find('[name="kategori"]').val() || window.formKategoriValue;
        const periode = $(this).closest('form').find('[name="periode"]').val();
        const kode = $(this).closest('form').find('[name="kode"]').val();

        if((!kategori || !periode) && !kode) return;

        $container.empty();

        $.ajax({
            url: 'kpi-production-fetch-aspek-kpi-item-by-kode-default',
            method: 'GET',
            data: { 
                kategori: kategori,
                kode: kode,
                periode: periode,
            },
            success: function (response) {
                renderAspekKpiItem(selectElement, response);
            }
        });
    });



    // get employee list
    const currentKategori = window.formKategoriValue || ''
    if(currentKategori === 'personal') {
        $('[name="periode"]').on('change', function () {
            // fetch employee list
            const $form = $(this).closest('form');
            const $select = $form.find('[name="kode"]');
            const periode = $form.find('[name="periode"]').val();
            const $container = $('.dynamic-form-kpi-aspek-values');
            $.ajax({
                url: 'kpi-production-fetch-kpi-employee-default',
                method: 'GET',
                data: { 
                    periode: periode,
                    filter_by_exist_evaluasi: true
                },
                success: function (response) {
                    $select.empty(); 

                    // Add new options
                    response.forEach(opt => {
                        const newOption = new Option(opt.text, opt.value, false, false);
                        $select.append(newOption);
                    });

                    // Refresh Select2 to recognize new options
                    $select.trigger('change');

                }
            });
        });

        $('#modalExportLaporanPdf').on('show.bs.modal', function (event) {
            const $select = $(event.currentTarget.querySelector('#laporan-personal-select-employee'));
            $.ajax({
                url: 'kpi-production-fetch-kpi-employee-default',
                method: 'GET',
                data: {},
                success: function (response) {
                    $select.empty();

                    console.log($select)

                    // Add new options
                    response.forEach(opt => {
                        const newOption = new Option(`${opt.nik} - ${opt.nama}`, opt.nik, false, false);
                        $select.append(newOption);
                    });

                    // Refresh Select2 to recognize new options
                    $select.trigger('change');
                }
            });
        })

        $('#modalBulkAction').on('show.bs.modal', function (event) {
            // get all selected data
            const $table = $('.pc-container').find('table');
            const checkboxes = $table.find('input[type="checkbox"][class^="cb-list-"]:checked');

            const modalBody = $(event.currentTarget.querySelector('.modal-body'));
            modalBody.html(/*html*/`
                <div class="form-group">
                    <label class="form-label">
                        Pilih aksi untuk ${checkboxes.length} data yang dipilih:
                    </label>

                    <select name="bulk_action" class="form-control form-control-sm">
                        <option value="export-laporan">Cetak Laporan Bulanan PDF</option>
                    </select>
                </div>
            `);
        })

        $('#modalBulkAction').find('form').on('submit', function (event) {
            event.preventDefault();

            const $form = $(this);
            const $table = $('.pc-container').find('table');
            const checkboxes = $table.find('input[type="checkbox"][class^="cb-list-"]:checked');
            const values = checkboxes.map((_, cb) => cb.value).get();

            $('<input>', {
                type: 'hidden',
                name: 'kpi_evaluation_ids',
                value: JSON.stringify(values)
            }).appendTo($form);

            this.submit();
        });

    }


    document.getElementById('export-laporan-bulanan-pdf').addEventListener('click', function() {
        const baseUrl = this.getAttribute('data-base-url');
        document.getElementById('formExportLaporanPdf').setAttribute('action', baseUrl);
    });

    document.getElementById('bulk-action').addEventListener('click', function() {
        const baseUrl = this.getAttribute('data-base-url');
        document.getElementById('formBulkAction').setAttribute('action', baseUrl);

        const csrfInput = document.createElement('input')
        csrfInput.setAttribute('type', 'hidden')
        csrfInput.setAttribute('name', '_token')
        csrfInput.setAttribute('value', this.dataset.csrf)
        document.getElementById('formBulkAction').appendChild(csrfInput)
    });
})



