/* LIST DRAWER FORM */
function loadSubcategories(category, sub, restoreValue = null) {
    const categoryId = category.value;
    if (!categoryId) {
        sub.innerHTML = '<option value="">Pilih Subkategori...</option>';
        return;
    }

    // Abort previous fetch if any
    if (sub.__abortController) sub.__abortController.abort();
    const controller = new AbortController();
    sub.__abortController = controller;

    sub.innerHTML = '<option value="">[ Loading... ]</option>';

    fetch(
        'inventory-consumable-category-fetch-category-subcategories-default?category_id=' + encodeURIComponent(categoryId),
        { signal: controller.signal }
    )
    .then(r => r.json())
    .then(rows => {
        sub.innerHTML = '<option value="">Pilih Subkategori...</option>';

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

function waitForCategoryAndLoad(category, sub, restoreValue = null) {
    const check = setInterval(() => {
        if (category.value) {
            clearInterval(check);
            loadSubcategories(category, sub, restoreValue);
        }
    }, 50); // check every 50ms
}

$(document).on('shown.bs.offcanvas', '.offcanvas', function () {
    const category = this.querySelector('select[name="category"]');
    const sub = this.querySelector('select[name="subcategory"]');
    if (!category || !sub) return;

    // Save edit-mode value
    const restoreValue = sub.value || null;

    // Reset placeholder
    sub.innerHTML = '<option value="">Pilih Subkategori...</option>';

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

$(document).on('change', 'select[name="subcategory"]', function () {
    const categoryId = $(this).val();
    const $sub = $('select[name="item_id"]');

    $sub.empty().trigger('change');

    if (!categoryId) return;

    $sub.html('<option value=""></option>');


    $.getJSON('inventory-consumable-fetch-items-by-category-default', { category_id: categoryId }, function (data) {
        $sub.empty()
        data.forEach(function (item) {
            const option = new Option(item.text, item.value, false, false);
            $sub.append(option);
        });

        $sub.trigger('change');
    });
});


/* DASHBOARD */
let chartInventoryConsumable;

function chartInventoryConsumableLoad() {
    const selectYear   = document.getElementById('chart-data-inventory-consumable-year');
    const selectItemId = document.getElementById('chart-data-inventory-consumable-item');

    if (!selectItemId) return

    const year = selectYear.value;
    const itemId = selectItemId.value;

    fetch(`inventory-consumable-chart-data-out-default?year=${year}&item_id=${itemId}`)
        .then(res => res.json())
        .then(data => {
            if (chartInventoryConsumable) {
                chartInventoryConsumable.destroy();
            }

            const ctx = document.getElementById('inventoryChart');

            chartInventoryConsumable = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Out Level',
                        data: data.values,
                        tension: 0.3,
                        fill: true
                    }]
                }
            });
        })
        .catch(error => {
            document.getElementById('inventoryChart').innerHTML = /*html*/`
                <div class="text-danger">ERROR: Failed to get data: ${error}</div>
            `
        });
}
if (document.getElementById('chart-data-inventory-consumable-year')) {
    document.getElementById('chart-data-inventory-consumable-year').onchange = chartInventoryConsumableLoad
}

if (document.getElementById('chart-data-inventory-consumable-item')) {
    document.getElementById('chart-data-inventory-consumable-item').onchange = chartInventoryConsumableLoad
}

/* STOCK INVENTORY CONSUMABLE */
function getSubcategoryOptions() {
    const selectCategoryId = document.getElementById('data-inventory-consumable-stock-category');
    if (!selectCategoryId) return;
    const categoryId = selectCategoryId.value;
    let options = '<option value="">Pilih Subkategori...</option>';
    fetch(`inventory-consumable-category-fetch-category-subcategories-default?category_id=${categoryId}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(subcat => {
                options += `<option value="${subcat.value}">${subcat.text}</option>`;
            });
            document.getElementById('data-inventory-consumable-stock-subcategory').innerHTML = options;
        })
        .catch(error => {
            document.getElementById('data-inventory-consumable-stock-subcategory').innerHTML = /*html*/`
                <option value="">ERROR: Failed to load subcategories: ${error}</option>
            `
        });
}

function renderStockTable() {
    const selectCategoryId = document.getElementById('data-inventory-consumable-stock-category');
    if (!selectCategoryId) return;
    const categoryId = selectCategoryId.value;
    const selectSubcategoryId = document.getElementById('data-inventory-consumable-stock-subcategory');
    if (!selectSubcategoryId) return;
    const subcategoryId = selectSubcategoryId.value;

    fetch(`inventory-consumable-fetch-items-stock-data-default?category_id=${categoryId ?? 0}&subcategory_id=${subcategoryId ?? 0}`)
        .then(res => res.json())
        .then(data => {
            
            let rows = '';
            data.forEach(item => {
                rows += /*html*/`
                    <tr class="${Number(item.stock) <= Number(item.minimum_stock) ? 'text-danger' : ''}">
                        <td>${item.text}</td>
                        <td>${item.stock}</td>
                    </tr>
                `;
            });
            document.getElementById('inventory-consumable-stock-tbody').innerHTML = rows;
        })
        .catch(error => {
            document.getElementById('inventory-consumable-stock-tbody').innerHTML = /*html*/`
                <tr>
                    <td colspan="2" class="text-danger">ERROR: Failed to load stock data: ${error}</td>
                </tr>
            `
        });
}

if (document.getElementById('data-inventory-consumable-stock-category')) {
    document.getElementById('data-inventory-consumable-stock-category').onchange = getSubcategoryOptions
}

if (document.getElementById('data-inventory-consumable-stock-subcategory')) {
    document.getElementById('data-inventory-consumable-stock-subcategory').onchange = renderStockTable
}

window.addEventListener('load', () => {
    chartInventoryConsumableLoad()
    getSubcategoryOptions()
    renderStockTable()
});


$(function () {
    if ($('#chart-data-inventory-consumable-year').length === 0) return;
    $('#chart-data-inventory-consumable-item').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Pilih...'
    });
});