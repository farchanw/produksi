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
                        Login bertindak sebagai <i>{{ Auth::user()->role->name }}</i> 
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body p-3">



                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="produktivitas-kerja-produksi-tab" data-bs-toggle="tab" data-bs-target="#produktivitas-kerja-produksi-section" type="button" role="tab" aria-controls="produktivitas-kerja-produksi-section" aria-selected="true">Produktivitas Kerja Produksi</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="yield-hasil-produksi-tab" data-bs-toggle="tab" data-bs-target="#yield-hasil-produksi-section" type="button" role="tab" aria-controls="yield-hasil-produksi-section" aria-selected="false">Yield Hasil Produksi</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="hasil-vs-kapasitas-tab" data-bs-toggle="tab" data-bs-target="#hasil-vs-kapasitas-section" type="button" role="tab" aria-controls="hasil-vs-kapasitas-section" aria-selected="false">Hasil vs Kapasitas</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="produksi-bulanan-tab" data-bs-toggle="tab" data-bs-target="#produksi-bulanan-section" type="button" role="tab" aria-controls="produksi-bulanan-section" aria-selected="false">Produksi Bulanan</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="hasil-oee-tab" data-bs-toggle="tab" data-bs-target="#hasil-oee-section" type="button" role="tab" aria-controls="hasil-oee-section" aria-selected="false">Hasil OEE</button>
                            </li>
                        </ul>
                        <div class="tab-content container my-3" id="myTabContent">
                            <div class="tab-pane fade show active" id="produktivitas-kerja-produksi-section" role="tabpanel" aria-labelledby="produktivitas-kerja-produksi-tab">
                                produktivitas-kerja-produksi
                            </div>
                            <div class="tab-pane fade" id="yield-hasil-produksi-section" role="tabpanel" aria-labelledby="yield-hasil-produksi-tab">
                                yield-hasil-produksi
                            </div>
                            <div class="tab-pane fade" id="hasil-vs-kapasitas-section" role="tabpanel" aria-labelledby="hasil-vs-kapasitas-tab">
                                hasil-vs-kapasitas</div>
                            <div class="tab-pane fade" id="produksi-bulanan-section" role="tabpanel" aria-labelledby="produksi-bulanan-tab">
                                produksi-bulanan</div>
                            <div class="tab-pane fade" id="hasil-oee-section" role="tabpanel" aria-labelledby="hasil-oee-tab">
                                hasil-oee</div>
                        </div>



                    </div>
                </div>


                <!--
                <div class="card mb-4">
                    <div class="card-body p-3">
                        <h3>InventoryConsumable</h3>

                    </div>
                </div>
                -->


            </div>
        </div>
    </div>
</div>


@if(isset($import_scripts))
@foreach($import_scripts as $isc)
<script src="{{$isc['source']}}"></script>
@endforeach
@endif
@endsection