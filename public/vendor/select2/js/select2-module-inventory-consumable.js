$( document ).ajaxStop(function() {
    $('.support-live-select2').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),// fix select2 search input focus bug
        })
    })
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
















function initSelectAppendable($el) {
    if ($el.hasClass('select2-hidden-accessible')) return;

    $el.select2({
        dropdownParent: $el.closest('.modal'),
        width: '100%',
        tags: true,
        placeholder: 'Select...'
    });
}


$(document).on('shown.bs.modal', '.modal', function () {
    const $modal = $(this);

    const $cat = $modal.find('select[name="category"]');
    const $sub = $modal.find('select[name="subcategory"]');

    initSelectAppendable($cat);
    initSelectAppendable($sub);

    // store original values (edit mode)
    $sub.data('restore-sub', $sub.data('original-value'));
});

$(document).on(
    'change select2:select',
    'select[name="category"]',
    function () {

        const $cat   = $(this);
        const $modal = $cat.closest('.modal');
        const $sub   = $modal.find('select[name="subcategory"]');

        const categoryId = $cat.val();
        const restoreSub = $sub.data('restore-sub') ?? null;

        // clear old options
        $sub.empty().trigger('change');

        if (!categoryId) return;

        $.getJSON(
            'inventory-consumable-category-fetch-category-subcategories-default',
            { category_id: categoryId },
            function (rows) {

                rows.forEach(row => {
                    const selected = restoreSub == row.id;
                    $sub.append(
                        new Option(row.text, row.id, false, selected)
                    );
                });

                if (restoreSub) {
                    $sub.val(restoreSub).trigger('change');
                }
            }
        );
    }
);

$(document).on('shown.bs.modal', '#editModal', function () {
    const $modal = $(this);
    const $cat   = $modal.find('select[name="category"]');

    const originalCategory = $cat.data('original-value');

    if (originalCategory) {
        $cat.val(originalCategory).trigger('change');
    }
});
