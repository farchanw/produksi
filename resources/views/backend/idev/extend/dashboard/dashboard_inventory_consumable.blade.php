@extends("easyadmin::backend.parent")
@section("content")
@push('mtitle')
{{$title}}
@endpush
<div class="pc-container">
    <div class="pc-content">

        <!-- PAGE HEADER -->
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

        <!-- DASHBOARD CONTENT -->
        <div class="row g-4 mb-4">

            <!-- OUT CHART -->
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-secondary text-white ">
                        <h5 class="mb-0 text-white">Pemakaian</h5>
                    </div>

                    <div class="card-body">
                        <form class="row g-2 mb-3">
                            <div class="col-md-3">
                                <select
                                    id="chart-data-inventory-consumable-year"
                                    class="form-select"
                                >

                                    @for ($year = now()->year; $year >= 1990; $year--)
                                        <option value="{{ $year }}" @if($year == now()->year) selected @endif>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col">
                                <select
                                    class="form-select"
                                    name="category_id"
                                >
                                    <option value="0">Pilih Kategori...</option>
                                    @foreach($dataInventoryConsumablesStockCategories as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col">
                                <select
                                    id="chart-data-inventory-consumable-item"
                                    class="form-select support-live-select2"
                                    name="item_id"
                                    disabled
                                >
                                    <option value="0">Pilih Item...</option>
                                </select>
                            </div>
                        </form>

                        <div class="ratio ratio-16x9">
                            <figure id="chart-data-inventory-consumable-item-default">
                                <figcaption class="font-bold fs-5"></figcaption>
                                <canvas></canvas>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STOCK TABLE -->
            <div class="col-12 col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0 text-white">Stock</h5>
                    </div>

                    <div class="card-body">
                        <form class="row g-2 mb-3">
                            <div class="col">
                                <select
                                    id="data-inventory-consumable-stock-category"
                                    class="form-select"
                                >
                                    <option value="">Semua Kategori</option>
                                    @foreach($dataInventoryConsumablesStockCategories as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        <style>
                            .dashboard-inventory-consumable-stock-table tr th:nth-child(1),
                            .dashboard-inventory-consumable-stock-table tr td:nth-child(1) {
                                width: 60%;
                            }

                            .dashboard-inventory-consumable-stock-table tr th:nth-child(2),
                            .dashboard-inventory-consumable-stock-table tr td:nth-child(2) {
                                width: 20%;
                            }

                            .dashboard-inventory-consumable-stock-table tr th:nth-child(3),
                            .dashboard-inventory-consumable-stock-table tr td:nth-child(3) {
                                width: 20%;
                            }
                        </style>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-scroll align-middle dashboard-inventory-consumable-stock-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>Stock</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody id="inventory-consumable-stock-tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="mb-8"></div>
    </div>
</div>

    @push('scripts')
        @if(isset($import_scripts))
        @foreach($import_scripts as $isc)
        <script src="{{$isc['source']}}"></script>
        @endforeach
        @endif

        @if(isset($import_styles))
        @foreach($import_styles as $ist)
        <link rel="stylesheet" href="{{$ist['source']}}">
        @endforeach
        @endif
    @endpush

@endsection