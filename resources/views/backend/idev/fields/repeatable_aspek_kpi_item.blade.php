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
                    <i class="ti ti-plus"></i> Add Item
                </button>
            </div>
        @endif
    </div>
</div>

<template id="node-repeatable-aspek-kpi-item-template">
    <div id="{{ $prefix_method }}repeatable-0"
            class="row {{ $prefix_method }}field-sections">

        <div class="col my-2">
            <label>KPI</label>
            <select name="kpi[0][master_kpi_id]"
                    class="form-control idev-form">
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
                    class="form-control idev-form">
        </div>

        <div class="col-md-1 my-2">
            <label>Target</label>
            <input type="number"
                    name="kpi[0][target]"
                    class="form-control idev-form">
        </div>

        <div class="col-md-1 my-2">
            <label>Realisasi</label>
            <input type="number"
                    name="kpi[0][realisasi]"
                    class="form-control idev-form">
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

<script>
// create event delegate for onchange
document.addEventListener('change', function (e) {
    const target = e.target;
    if (target.name === 'master_subsection_id') {
        // set option kpi
        const subsectionId = target.value
        //const existingValues = field?.value || [];
        if (!subsectionId) return;

        // Fetch KPI options for this subsection
        fetch(`kpi-production-fetch-master-kpi-default?subsection_id=${subsectionId}`)
            .then(res => res.json())
            .then(options => {
                document.querySelectorAll('[name^="kpi"]').forEach(el => {
                    el.replaceChildren()
                });
                document.querySelectorAll('[name^="kpi"]').forEach(el => {
                    const defaultOption = new Option('Select...', '');
                    el.appendChild(defaultOption);
                    options.forEach(option => {
                        const optionEl = new Option(option.text, option.value);
                        el.appendChild(optionEl);
                    })
                });
            })
            .catch(err => console.error('Failed to fetch KPI options', err));


    }
});


function addAspekKpiItem(prefix = '', values = {}, optionsKpiMaster = []) {
    const container = document.querySelector(`.${prefix}repeatable-sections`);
    if (!container) return;

    const sections = container.querySelectorAll(`.${prefix}field-sections`);
    const newIndex = sections.length;

    // Clone the first section as a template
    const nodeTemplate = document.querySelector('#node-repeatable-aspek-kpi-item-template');
    const template = document.importNode(nodeTemplate.content, true);
    template.id = `${prefix}repeatable-${newIndex}`;

    // Update names and prepopulate values
    template.querySelectorAll('input, select').forEach(el => {
        // Update the name to new index
        const name = el.getAttribute('name');
        if (name) {
            el.setAttribute('name', name.replace(/\[\d+\]/, `[${newIndex}]`));
        }

        const keyMatch = el.getAttribute('name').match(/\[([a-zA-Z_]+)\]/);
        if (!keyMatch) return;
        const key = keyMatch[1];

        // Prepopulate input/select values
        if (key === 'master_kpi_id' && optionsKpiMaster.length > 0) {
            // Populate select dynamically
            el.innerHTML = ''; // clear existing options
            el.appendChild(new Option('Select...', ''));
            optionsKpiMaster.forEach(opt => {
                const optionEl = new Option(opt.text, opt.value);
                if (values[key] && values[key] == opt.value) {
                    optionEl.selected = true;
                }
                el.appendChild(optionEl);
            });
        } else if (values[key] !== undefined) {
            el.value = values[key];
        } else {
            el.value = '';
        }
    });

    // Update remove button
    const removeBtn = template.querySelector('.remove-section button');
    removeBtn.setAttribute(
        'onclick',
        `removeAspekKpiItem('${prefix}', ${newIndex})`
    );

    container.appendChild(template);
}


function removeAspekKpiItem(prefix = '', index) {
    const container = document.querySelector(`.${prefix}repeatable-sections`);
    if (!container) return;

    const sections = container.querySelectorAll(`.${prefix}field-sections`);
    if (sections.length <= 1) return;

    const target = document.getElementById(`${prefix}repeatable-${index}`);
    if (!target) return;

    const hasValue = Array.from(
        target.querySelectorAll('input, select')
    ).some(el => el.value && el.value.trim() !== '');

    if (hasValue && !confirm('This item contains data. Are you sure you want to remove it?')) {
        return;
    }

    target.remove();
    reindexAspekKpiItem(prefix);
}

function reindexAspekKpiItem(prefix = '') {
    const sections = document.querySelectorAll(`.${prefix}field-sections`);

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
        removeBtn.setAttribute(
            'onclick',
            `removeAspekKpiItem('${prefix}', ${i})`
        );
    });
}
</script>


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
