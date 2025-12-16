@extends("easyadmin::backend.parent")
@section("content")
@push('mtitle')
{{$title}}
@endpush
<div class="pc-container">
    <div class="pc-content">

        {{-- HEADER --}}
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-12">
                    <h5 class="mb-1">
                        Hi, <b>{{ Auth::user()->name }}</b>
                    </h5>
                    @if(config('idev.enable_role',true))
                        <small class="text-muted">
                            Login bertindak sebagai <i>{{ Auth::user()->role->name }}</i>
                        </small>
                    @endif
                </div>
            </div>
        </div>

        {{-- DASHBOARD CONTENT --}}
        <div class="row g-4">

            {{-- OUT CHART --}}
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Out</h5>
                    </div>

                    <div class="card-body">
                        <form class="row g-2 mb-3">
                            <div class="col-md-4">
                                <select
                                    id="chart-data-inventory-consumable-year"
                                    class="form-select"
                                >
                                    @foreach($dataInventoryConsumablesChartYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-8">
                                <select
                                    id="chart-data-inventory-consumable-item"
                                    class="form-select"
                                >
                                    @foreach($dataInventoryConsumablesChartItems as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->sku }} - {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        <div class="ratio ratio-16x9">
                            <canvas id="inventoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STOCK TABLE --}}
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Stock</h5>
                    </div>

                    <div class="card-body">
                        <form class="row g-2 mb-3">
                            <div class="col-md-6">
                                <select
                                    id="data-inventory-consumable-stock-category"
                                    class="form-select"
                                >
                                    <option value="0">-- Select Category --</option>
                                    @foreach($dataInventoryConsumablesStockCategories as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <select
                                    id="data-inventory-consumable-stock-subcategory"
                                    class="form-select"
                                >
                                    <option value="">-- Select Subcategory --</option>
                                </select>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-end">Stock</th>
                                    </tr>
                                </thead>
                                <tbody id="inventory-consumable-stock-tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@if(isset($import_scripts))
@foreach($import_scripts as $isc)
<script src="{{$isc['source']}}"></script>
@endforeach
@endif
<script>
/* CHART INVENTORY CONSUMABLE */
let chartInventoryConsumable;

function chartInventoryConsumableLoad() {
    const year   = document.getElementById('chart-data-inventory-consumable-year').value;
    const itemId = document.getElementById('chart-data-inventory-consumable-item').value;

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
                <div class="text-danger">Failed to get data: ${error}</div>
            `
        });
}
document.getElementById('chart-data-inventory-consumable-year').onchange = chartInventoryConsumableLoad
document.getElementById('chart-data-inventory-consumable-item').onchange = chartInventoryConsumableLoad

/* STOCK INVENTORY CONSUMABLE */
function getSubcategoryOptions() {
    const categoryId = document.getElementById('data-inventory-consumable-stock-category').value;
    let options = '<option value="">-- Select Subcategory --</option>';
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
                <option value="">Failed to load subcategories: ${error}</option>
            `
        });
}

function renderStockTable() {
    const categoryId = document.getElementById('data-inventory-consumable-stock-category').value;
    const subcategoryId = document.getElementById('data-inventory-consumable-stock-subcategory').value;

    fetch(`inventory-consumable-fetch-items-stock-data-default?category_id=${categoryId ?? 0}&subcategory_id=${subcategoryId ?? 0}`)
        .then(res => res.json())
        .then(data => {
            
            let rows = '';
            data.forEach(item => {
                console.log(item)
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
                    <td colspan="2" class="text-danger">Failed to load stock data: ${error}</td>
                </tr>
            `
        });
}

document.getElementById('data-inventory-consumable-stock-category').onchange = getSubcategoryOptions
document.getElementById('data-inventory-consumable-stock-subcategory').onchange = renderStockTable

window.addEventListener('load', () => {
    chartInventoryConsumableLoad()
    getSubcategoryOptions()
    renderStockTable()
});
</script>
@endsection