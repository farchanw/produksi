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