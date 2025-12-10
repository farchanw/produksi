<div>
    <h4>Receipt Order</h4>
    <hr>
    <form id="form-receipt-order-custom" action="{{ route('receipt-order.store') }}" method="post">
        @csrf
        <div class="row">
            @php $method = "create"; @endphp
            @foreach ($fields as $key => $field)
                @if (View::exists('backend.idev.fields.' . $field['type']))
                    @include('backend.idev.fields.' . $field['type'])
                @else
                    @include('easyadmin::backend.idev.fields.' . $field['type'])
                @endif
            @endforeach
            <div class="col-md-12">
                <table id="table-receipt-checking" class="table table-stripped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Material</th>
                            <th>Quantity Plan</th>
                            <th>Quantity Actual</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div id="section-attach-po"></div>
            </div>            
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group my-2">
                    <button id="btn-for-form-receipt-order-custom" type="button" class="btn btn-outline-secondary"
                        onclick="softSubmit('form-receipt-order-custom', 'list-receipt-order')">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<div class="modal modal-lg fade" tabindex="-1" role="dialog" id="modalPreview">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lampiran PO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="section-preview-po">Loading...</div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        
    })

    $( "#create_po_id" ).on( "change", function() {
        findPo($(this).val())
    } );

    function findPo(id) {
        let mUrl = "{{route('purchase-order-detail.by-po-number', 'idevChange')}}"
        mUrl = mUrl.replace("idevChange", id)
        
        $("#table-receipt-checking tbody").html("Searching..")
        $("#section-attach-po").html("")

        $.get(mUrl, function(response, status){
            $("#section-preview-po").html(response)
            var mHtml = ""
            $.each(response.details, function( index, detail ) {
                mHtml += `
                        <tr>
                            <td>${index+1}</td>
                            <td>${detail.material_name}</td>
                            <td>${detail.quantity}</td>
                            <td><input type='number' value='${detail.quantity}' name='qty_actuals[${detail.id}]'></td>
                            <td><input type='text' value='${detail.notes ?? ''}' name='mtl_notes[${detail.id}]'></td>
                        </tr>
                `
            });

            const attachPoHtml = `
                        <button type="button" class="btn btn-outline-dark float-end"
                        data-bs-toggle='modal' data-bs-target='#modalPreview'
                        onclick="goPreview(${id})">PO Attachment</button>
            `

            $("#table-receipt-checking tbody").html(mHtml)
            $("#section-attach-po").html(attachPoHtml)
        });
    }

    function goPreview(id) {
        let mUrl = "{{route('purchase-order.mail-template', 'idevChange')}}"
        mUrl = mUrl.replace("idevChange", id)

        $.get(mUrl, function(response, status){
            $("#section-preview-po").html(response)
        });
    }
</script>
@endpush
