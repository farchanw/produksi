$( document ).ajaxStop(function() {
    $('.support-live-select2').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),// fix select2 search input focus bug
        })
    })
});






function initCustomSelect2() {
    $('.support-live-select2-value-appendable').each(function () {

        if ($(this).hasClass('select2-hidden-accessible')) return;

        $(this).select2({
            tags: true,               // allow typing custom text
            selectOnClose: true,
            placeholder: "Select or type...",
            dropdownParent: $(this).parent(),
            createTag: function(params) {
                let term = $.trim(params.term);
                if (term === '') return null; // ignore empty
                return {
                    id: term,
                    text: term,
                    newTag: true
                };
            }
        });

        $(this).off('select2:select').on('select2:select', function (e) {
            const data = e.params.data;
            if (!$(this).find("option[value='" + data.id + "']").length) {
                const newOption = new Option(data.text, data.id, true, true);
                $(this).append(newOption).trigger('change');
            }
        });
    });
}

// Initialize on page load
$(document).ready(initCustomSelect2);

// Re-initialize after AJAX (if repeatable fields are added)
$(document).ajaxStop(initCustomSelect2);



// Resolve dependent subcategory select2
//const selectSelectors =  'select[name="category"].support-live-select2-value-appendable';
$(document).on('shown.bs.modal', '#editModal', function () {
    const $cat = $(this).find('select[name="category"]');
    const $sub = $(this).find('select[name="subcategory"]');

    const originalCategory = $cat[0].dataset.originalValue;
    const originalSubcategory = $sub[0].dataset.originalValue;

    // Save sub original value
    $sub.data('original-sub', originalSubcategory);

    // Set category → triggers loading subcategories
    if (originalCategory) {
        $cat.val(originalCategory).trigger('change');
    }
});

$(document).on('change', '#editModal select[name="category"]', function () {
    const $modal = $(this).closest('#editModal');
    const $sub = $modal.find('select[name="subcategory"]');

    const categoryId = $(this).val();
    const originalSub = $sub.data('original-sub');

    $sub.empty().trigger('change');

    if (!categoryId) return;

    $.getJSON('inventory-consumable-fetch-category-subcategories-default',
        { category_id: categoryId },
        function (data) {

            data.forEach(item => {
                const isSelected = originalSub && originalSub == item.value;
                const option = new Option(item.text, item.value, false, isSelected);
                $sub.append(option);
            });

            $sub.trigger('change');
        }
    );
});







// Resolve fetch items
$(document).on('change', 'select[name="subcategory"]', function () {
    const categoryId = $(this).val();
    const $sub = $('select[name="item_id"]');

    $sub.empty().trigger('change');

    if (!categoryId) return;

    $.getJSON('inventory-consumable-fetch-items-by-category-default', { category_id: categoryId }, function (data) {
        data.forEach(function (item) {
            const option = new Option(item.text, item.value, false, false);
            $sub.append(option);
        });

        $sub.trigger('change');
    });
});
