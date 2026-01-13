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
