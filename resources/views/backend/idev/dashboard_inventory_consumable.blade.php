@extends("easyadmin::backend.parent")
@section("content")
@push('mtitle')
{{$title}}
@endpush
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        Hi, <b>{{ Auth::user()->name }} </b> 
                        @if(config('idev.enable_role',true))
                        You are logged in as <i>{{ Auth::user()->role->name }}</i> 
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body p-3">
                        <h3>Inventory Consumables</h3>

                        <section style="display:grid; grid-template-columns:repeat(2, 1fr); gap:4rem; margin-bottom:1rem;">
                            <div class="card p-2">
                                <h4>Outs</h4>
                                <form id="filterForm" class="flex gap-3">
                                    <select id="chart-data-inventory-consumable-year">
                                        @foreach($dataInventoryConsumablesChartYears as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>

                                    <select id="chart-data-inventory-consumable-item">
                                        @foreach($dataInventoryConsumablesChartItems as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->sku }} - {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    {{-- <button type="button" onclick="chartInventoryConsumableLoad()">View</button> --}}
                                </form>

                                <canvas id="inventoryChart" height="120"></canvas>

                            </div>
                            <div class="card p-2">
                                <h4>Stocks</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dataInventoryConsumablesStock as $d)
                                        <tr>
                                            <td>{{ $d['name'] }}</td>
                                            <td>{{ $d['stock'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card p-2">

                            </div>
                        </section>

                    </div>
                </div>



                <div class="card mb-4">
                    <div class="card-body p-3">
                        <h3>InventoryConsumable</h3>

                    </div>
                </div>



                <div class="card mb-4">
                    <div class="card-body p-3">
                        <h3>InventoryConsumable</h3>

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
                type: 'line',
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
                <div style="color:red; font-weight:bold; ">Failed to get data: ${error}</div>
            `
        });
}

window.addEventListener('load', () => {
    chartInventoryConsumableLoad();
});

document.getElementById('chart-data-inventory-consumable-year').onchange = chartInventoryConsumableLoad
document.getElementById('chart-data-inventory-consumable-item').onchange = chartInventoryConsumableLoad

</script>
@endsection