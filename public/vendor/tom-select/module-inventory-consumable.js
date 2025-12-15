function initTomSelect(select) {
    console.log(select)
    if (select.tomselect) return; // already initialized

    new TomSelect(select, {
        create: true,           // allow custom values
        maxItems: 1,
        persist: false,
        placeholder: 'Select...',
        dropdownParent: 'body'  // IMPORTANT for Bootstrap modal
    });
}


$(document).on('shown.bs.offcanvas', '.offcanvas', function () {

    const offcanvas = this;

    offcanvas.querySelectorAll(
        'select[name="category"], select[name="subcategory"]'
    ).forEach(select => {

        if (!select.tomselect) {
            new TomSelect(select, {
                create: true,
                maxItems: 1,
                persist: false,
                placeholder: 'Select...'
            });
        }
    });

    const cat = offcanvas.querySelector('select[name="category"]');
    const sub = offcanvas.querySelector('select[name="subcategory"]');

    // EDIT MODE ONLY
    if (sub?.dataset.originalValue) {
        sub.dataset.restoreSub = String(sub.dataset.originalValue);
    }

    if (cat?.dataset.originalValue) {
        cat.tomselect.setValue(String(cat.dataset.originalValue), true);
        cat.dispatchEvent(new Event('change', { bubbles: true }));
    }
});


$(document).on('hidden.bs.offcanvas', '.offcanvas', function () {

    const offcanvas = this;

    const cat = offcanvas.querySelector('select[name="category"]');
    const sub = offcanvas.querySelector('select[name="subcategory"]');

    // remove JS-only restore state
    if (sub) {
        delete sub.dataset.restoreSub;
    }

    // reset tomselect runtime state
    if (sub?.tomselect) {
        sub.tomselect.clear(true);
        sub.tomselect.clearOptions();
    }

    if (cat?.tomselect) {
        cat.tomselect.clear(true);
    }
});




document.addEventListener('change', e => {

    if (!e.target.matches('select[name="category"]')) return;

    const cat = e.target;
    const container = cat.closest('.offcanvas, .modal');
    const sub = container.querySelector('select[name="subcategory"]');

    if (!sub || !sub.tomselect) return;

    const ts = sub.tomselect;

    const categoryId = String(cat.value);
    const restoreSub = sub.dataset.restoreSub
        ? String(sub.dataset.restoreSub)
        : null;

    ts.clear(true);
    ts.clearOptions();

    if (!categoryId) return;

    fetch(
        'inventory-consumable-category-fetch-category-subcategories-default?category_id=' + categoryId
    )
        .then(r => r.json())
        .then(rows => {

            const options = rows.map(row => ({
                value: String(row.value), // ✅ FIXED
                text: row.text
            }));

            ts.addOptions(options);
            ts.refreshOptions(false);

            if (restoreSub) {
                ts.setValue(restoreSub, true);
            }
        });
});
