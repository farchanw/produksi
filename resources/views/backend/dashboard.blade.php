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
                        <h3>InventoryConsumable</h3>

                        <form id="filterForm" class="flex gap-3">
                            <select id="year">
                                @foreach($dataInventoryConsumablesChartYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>

                            <select id="item">
                                @foreach($dataInventoryConsumablesChartItems as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->sku }} - {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="button" onclick="loadChart()">View</button>
                        </form>

                        <canvas id="inventoryChart" height="120"></canvas>

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
let chart;

function loadChart() {
    const year   = document.getElementById('year').value;
    const itemId = document.getElementById('item').value;

    fetch(`inventory-consumable-chart-data-default?year=${year}&item_id=${itemId}`)
        .then(res => res.json())
        .then(data => {
            if (chart) {
                chart.destroy();
            }

            const ctx = document.getElementById('inventoryChart');

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Stock Level',
                        data: data.values,
                        tension: 0.3,
                        fill: true
                    }]
                }
            });
        });
}

window.addEventListener('load', () => {
    loadChart();
});
</script>
@endsection