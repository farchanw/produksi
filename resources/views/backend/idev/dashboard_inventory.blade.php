@extends('easyadmin::backend.parent')
@section('content')
    @push('mtitle')
        {{ $title }}
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/inventory-dashboard.css')}}" />
    @endpush

    <div class="pc-container">
        <div class="pc-content">

            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            Hi, <b>{{ Auth::user()->name }} </b>
                            @if (config('idev.enable_role', true))
                                You are logged in as <i>{{ Auth::user()->role->name }}</i>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <section class="row">
                <div class="col-12 col-lg-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            Filter
                        </div>
                        <div class="card-body">
                            <form id="form-filter" action="{{ url('inventory/dashboard-inventory-api') }}" method="get">
                                <div class="row my-3">
                                    <div class="col-md-2">
                                        <small for="">Tanggal Mulai</small>
                                        <input type="date" class="form-control" name="start_date"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <small for="">Tanggal Akhir</small>
                                        <input type="date" class="form-control" name="end_date"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <small for="">Gudang</small>
                                        <select name="warehouse_id" class="form-control">
                                            <option value="">Semua Gudang</option>
                                            @foreach ($mstGudang as $mg)
                                                <option value="{{ $mg->id }}">{{ $mg->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <br>
                                        <button class="btn btn-outline-secondary" id="btn-for-form-filter"
                                            onclick="setFilter('form-filter')" type="button">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <section class="row" id="section-summary" data-url="{{ url('inventory/dashboard-summary-api') }}">
                <div class="col-3 col-lg-3">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            Income
                        </div>
                        <div class="card-body p-4">
                            <h3 id="txt-summary-income">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-3 col-lg-3">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            Outcome
                        </div>
                        <div class="card-body p-4">
                            <h3 id="txt-summary-outcome">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-3 col-lg-3">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            Profit
                        </div>
                        <div class="card-body p-4">
                            <h3 id="txt-summary-profit">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-3 col-lg-3">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            Percentage
                        </div>
                        <div class="card-body p-4">
                            <h3 id="txt-summary-percentage">0</h3>
                        </div>
                    </div>
                </div>
            </section>

            <section class="row">
                <div class="col-9 col-lg-9">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            Kartu Stok
                        </div>
                        <div class="card-body">
                            <table class="table table-hover table-stock-card">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Material</th>
                                        <th rowspan="2">Warehouse</th>
                                        <th colspan="4" style="text-align: center;">Stok</th>
                                        <th rowspan="2">Terakhir Update</th>
                                    </tr>
                                    <tr>
                                        <th>Awal</th>
                                        <th>Masuk</th>
                                        <th>Keluar</th>
                                        <th>Terkini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-3 col-lg-3">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            Stock Limit
                        </div>
                        <div class="card-body p-2">
                            <div class="card-list-kp mt-2"></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/js/inventory-dashboard.js')}}"></script>
    @endpush
@endsection
