@extends('easyadmin::frontend.parent')
@push('mtitle')
    {{ $title }}
@endpush
@push('festyles')
    <link href="{{asset('assets/css/pos.css')}}" rel="stylesheet" />
@endpush
@section('contentfrontend')
    <div id="collect-url" class="pos-loader" data-url="{{$urlApi}}"></div>
    <div id="main-pos-apps" class="row">
        <div class="col-12 col-md-8 col-lg-8">
            <div class="input-group mb-3 ">
                <input type="text" name="" id="inp-search" class="form-control rounded-4" placeholder="Search Product...">
                <span class="input-group-text rounded-4 bg-white"><i class="fa fa-search"></i></span>
            </div>

            <h4>Kategori</h4>
            <div class="row mb-4 mt-2 section-categories"></div>

            <h4 class="text-label-category">Semua</h4>
            <div class="row section-products"></div>
        </div>

        <div class="col-12 col-md-4 col-lg-4">
            <div class="card border-0">
                <div class="card-header text-white bg-dark py-3">
                    <b>Histori Order</b>
                    <a href="{{url('pos/dashboard-pos')}}" class="text-white float-end"><i class="fa fa-home"></i></a>
                </div>
                <div class="card-body">
                    <table class="table table-stripped table-history">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <h6 class="text-center subtotal-price">TOTAL Rp 0</h6>

                    <button class="btn btn-danger w-100 my-2" type="button" data-bs-toggle='modal' data-bs-target='#modalCheckout'>Checkout</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBuy" tabindex="-1" aria-labelledby="modalBuyLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBuyLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="">Quantity</label>
                    <input type="number" name="" id="input-qty" class="form-control">
                    <label for="">Notes</label>
                    <input type="text" name="" id="input-notes" class="form-control">
                    <input type="hidden" name="" id="input-code">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" onclick="addItem()">Save</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalCheckout" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="modalBuyLabel">Checkout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 for="">Payment Method</h5>
                    <div class="mb-4">
                        <button type="button" class="btn btn-dark">Cash</button>    
                        <button type="button" class="btn btn-outline-dark">QRIS</button>    
                        <button type="button" class="btn btn-outline-dark">TRANSFER</button>    
                        <button type="button" class="btn btn-outline-dark">VA</button>    

                        <table id="summary-payment-table" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        Items
                                    </th>
                                    <th>
                                        Amount
                                    </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <hr>
                        <h5 for="">Summary</h5>
                        <table id="summary-total-table" class="table">
                            <tbody>
                                <tr class="bg-info">
                                    <td>Total</td>
                                    <td>
                                        <b id="text-summary-total" class="float-end text-dark"></b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="">Received</label>
                                        <input type="number" name="" class="form-control" id="inp-summary-received">
                                        <input type="hidden" id="inp-summary-total">
                                        <input type="hidden" id="inp-summary-changes">
                                    </td>
                                    <td>
                                        <b id="text-summary-received" class="float-end text-dark"></b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Changes</td>
                                    <td>
                                        <b id="text-summary-changes" class="float-end text-dark"></b>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h5 for="">Customer Info</h5>
                    <div class="mb-4 row">
                        <div class="col-md-6">
                            <input type="text" name="customer_name" class="form-control mb-2" placeholder="Name">
                        </div>
                        <div class="col-md-6">
                            <input type="number" name="" id="input-notes" class="form-control" placeholder="Phone">
                        </div>
                        <input type="hidden" name="" id="input-code">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('fescripts')
    <script src="{{asset('assets/js/fontawesome.js')}}"></script>
    <script src="{{asset('assets/js/pos.js')}}"></script>
@endpush
