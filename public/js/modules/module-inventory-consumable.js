/**
 * Load subcategories for a given category
 */
function loadSubcategories(category, sub, restoreValue = null) {
    const categoryId = category.value;
    if (!categoryId) {
        sub.innerHTML = '<option value="">Select subcategory</option>';
        return;
    }

    // Abort previous fetch if any
    if (sub.__abortController) sub.__abortController.abort();
    const controller = new AbortController();
    sub.__abortController = controller;

    sub.innerHTML = '<option value="">Loading...</option>';

    fetch(
        'inventory-consumable-category-fetch-category-subcategories-default?category_id=' + encodeURIComponent(categoryId),
        { signal: controller.signal }
    )
    .then(r => r.json())
    .then(rows => {
        sub.innerHTML = '<option value="">Select subcategory</option>';

        rows.forEach(row => {
            const opt = document.createElement('option');
            opt.value = row.value;
            opt.textContent = row.text;
            sub.appendChild(opt);
        });

        // Restore subcategory if it exists in new options
        if (restoreValue && sub.querySelector(`option[value="${restoreValue}"]`)) {
            sub.value = restoreValue;
        }
    })
    .catch(err => { if (err.name !== 'AbortError') console.error(err); });
}

/**
 * Wait until category value is set by other JS, then load subcategories
 */
function waitForCategoryAndLoad(category, sub, restoreValue = null) {
    const check = setInterval(() => {
        if (category.value) {
            clearInterval(check);
            loadSubcategories(category, sub, restoreValue);
        }
    }, 50); // check every 50ms
}

/**
 * OFFCANVAS OPEN
 */
// Offcanvas open
$(document).on('shown.bs.offcanvas', '.offcanvas', function () {
    const category = this.querySelector('select[name="category"]');
    const sub = this.querySelector('select[name="subcategory"]');
    if (!category || !sub) return;

    // Save edit-mode value
    const restoreValue = sub.value || null;

    // Reset placeholder
    sub.innerHTML = '<option value="">Select subcategory</option>';

    // Wait until category value is set by other JS
    const check = setInterval(() => {
        if (category.value) {
            clearInterval(check);
            // Trigger subcategory load
            loadSubcategories(category, sub, restoreValue);
        }
    }, 50);

    // Bind change for user interaction
    category.removeEventListener('change', category.__handler);
    category.__handler = () => loadSubcategories(category, sub);
    category.addEventListener('change', category.__handler);
});


/**
 * OFFCANVAS CLOSE / CLEANUP
 */
$(document).on('hidden.bs.offcanvas', '.offcanvas', function () {
    this.querySelectorAll('select').forEach(select => {
        // Abort pending fetch
        if (select.__abortController) select.__abortController.abort();
        delete select.__abortController;

        // Remove stored restore value
        delete select.__restoreValue;

        // Remove category handler
        if (select.__handler) {
            select.removeEventListener('change', select.__handler);
            delete select.__handler;
        }
    });
});




// Resolve fetch items
$(document).on('change', 'select[name="subcategory"]', function () {
    const categoryId = $(this).val();
    const $sub = $('select[name="item_id"]');

    $sub.empty().trigger('change');

    if (!categoryId) return;

    $sub.html('<option value="">Loading...</option>');


    $.getJSON('inventory-consumable-fetch-items-by-category-default', { category_id: categoryId }, function (data) {
        $sub.empty()
        data.forEach(function (item) {
            const option = new Option(item.text, item.value, false, false);
            $sub.append(option);
        });

        $sub.trigger('change');
    });
});



