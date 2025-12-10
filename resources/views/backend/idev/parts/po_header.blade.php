<div>
    <h4>Purchase Order Header</h4>
    <hr>
    <form id="form-pohead" action="{{ route('purchase-order.update', request('parent')) }}" method="post">
        @method('PUT')
        @csrf
        <div class="row">
            @php $method = "pohead"; @endphp
            @foreach ($poHeadfields as $key => $field)
                @if (View::exists('backend.idev.fields.' . $field['type']))
                    @include('backend.idev.fields.' . $field['type'])
                @else
                    @include('easyadmin::backend.idev.fields.' . $field['type'])
                @endif
            @endforeach
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group my-2">
                    <button id="btn-for-form-pohead" type="button" class="btn btn-outline-secondary"
                        onclick="localSubmit()">Update</button>

                    <button id="btn-for-form-pohead" type="button" class="btn btn-outline-warning"
                        data-bs-toggle='modal' data-bs-target='#modalPreview'
                        onclick="goPreview()">Preview</button>
                </div>
            </div>
        </div>
    </form>
</div>


<div id="section-add-po-detail">
    <div>
        <span class="count-total-list-{{ $uri_key }} mt-2">0 Data</span>
        @if (in_array('create', $permissions))
            <a class="btn btn-secondary float-end text-white mx-1" data-bs-toggle="offcanvas"
                data-bs-target="#createForm-{{ $uri_key }}">
                Add Material
            </a>
        @endif
    </div>
</div>

@push('scripts')
<div class="modal modal-lg fade" tabindex="-1" role="dialog" id="modalPreview">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="section-preview-po">Loading...</div>
            </div>
            <div class="modal-footer">
                <div class="form-group my-2">
                    <button type="button" class="btn btn-outline-primary">Send Email</button>

                    <button type="button" class="btn btn-outline-danger">Print Pdf</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        $('#section-add-po-detail').insertBefore('#form-filter-list-purchase-order-detail');
        $(".title-header").text("PO "+$("#pohead_title").val())
    });

    function localSubmit() {
        $(".title-header").text("PO "+$("#pohead_title").val())

        softSubmit('form-pohead')
    }

    function goPreview() {
        const routeMail = "{{route('purchase-order.mail-template', request('parent'))}}"
        $.get(routeMail, function(response, status){
            $("#section-preview-po").html(response)
        });
    }
</script>
@endpush
