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

            const figure = document.getElementById('chart-data-inventory-consumable-item-default');
            if (selectItemId.value) {
                figure.querySelector('figcaption').innerText = selectItemId.options[selectItemId.selectedIndex].text;
            }
            const ctx = figure.querySelector('canvas');

            chartInventoryConsumable = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Pemakaian',
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


function renderStockTable() {
    const selectCategoryId = document.getElementById('data-inventory-consumable-stock-category');
    if (!selectCategoryId) return;
    const categoryId = selectCategoryId.value;

    document.getElementById('inventory-consumable-stock-tbody').innerHTML = /*html*/`
        <tr>
            <td colspan="3" class="font-bold text-center"> <i>[Loading...]</i> </td>
        </tr>
    `

    fetch(`inventory-consumable-fetch-items-stock-data-default?category_id=${categoryId ?? 0}`)
        .then(res => res.json())
        .then(data => {
            
            let rows = '';
            data.forEach(item => {
                rows += /*html*/`
                    <tr class="${Number(item.stock) <= Number(item.minimum_stock) ? 'text-danger' : ''}">
                        <td>${item.text}</td>
                        <td>${item.stock}</td>
                        <td>${item.satuan}</td>
                    </tr>
                `;
            });
            document.getElementById('inventory-consumable-stock-tbody').innerHTML = rows;
        })
        .catch(error => {
            document.getElementById('inventory-consumable-stock-tbody').innerHTML = /*html*/`
                <tr>
                    <td colspan="3" class="text-danger">ERROR: Failed to load stock data: ${error}</td>
                </tr>
            `
        });
}

if (document.getElementById('data-inventory-consumable-stock-category')) {
    document.getElementById('data-inventory-consumable-stock-category').onchange = renderStockTable
}

window.addEventListener('load', () => {
    chartInventoryConsumableLoad()
    renderStockTable()
});


$(document).on('change', 'select[name="category_id"]', function () {
    const categoryId = $(this).val();

    // Item
    const selectItem = $('select[name="item_id"]');
    selectItem.empty().trigger('change');

    if (!categoryId) return;

    selectItem.prop('disabled', true);
    selectItem.html('<option value=""> [Loading...] </option>');

    $.getJSON('inventory-consumable-fetch-items-by-category-default', { category_id: categoryId }, function (data) {
        selectItem.prop('disabled', false);
        selectItem.empty()
        data.forEach(function (item) {
            const option = new Option(item.text, item.value, false, false);
            selectItem.append(option);
        });

        selectItem.trigger('change');
    });
});



$( document ).ajaxStop(function() {
    $('.support-live-select2').each(function () {
        $(this).select2({
            theme: 'bootstrap-5',
            dropdownParent: $(this).parent(),// fix select2 search input focus bug
        })
        
    })
});


$(document).on('change select2:select select2:clear', '[name="item_id"]', function (e) {
    const form = this.closest('form');

    if (
        form.id !== 'form-edit-inventory-consumable-movement' &&
        form.id !== 'form-create-inventory-consumable-movement'
    ) {
        return;
    }

    const itemId = $(this).val();
    if (!itemId) return;

    const subCheckItemList = form.querySelector(
        '.form-field-checklist-searchable-check-item-list'
    );

    fetch(`inventory-consumable-fetch-item-subcategories-default?id=${itemId}`)
        .then(r => r.json())
        .then(data => {
            subCheckItemList.replaceChildren();

            data.forEach((dt, index) => {
                subCheckItemList.insertAdjacentHTML('beforeend', `
                    <div class="form-check">
                        <input 
                            class="form-check-input"
                            type="checkbox"
                            id="live_subcategory_edit_${index}"
                            name="subcategory_id[]"
                            value="${dt.value}"
                            data-text-for-searchable="${dt.text}"
                        >
                        <label 
                            class="form-check-label fw-normal" 
                            for="live_subcategory_edit_${index}">
                            ${dt.text}
                        </label>
                    </div>
                `);
            });
        });
});
