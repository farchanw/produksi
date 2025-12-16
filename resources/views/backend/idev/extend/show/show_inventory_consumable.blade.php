
<div class="card">
    <div class='card-body'>

        <div class="table-responsive p-0">
            <table id="table-list-{{$uri_key}}" class="table table-hover">
                <thead>
                    <tr>
                        @foreach($table_headers as $header)
                        @php
                        $header_name = $header['name'];
                        $header_column = $header['column'];
                        @endphp
                        @if($header['order'])
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="white-space: nowrap;">{{$header_name}}
                            <button class="btn btn-sm btn-link" onclick="orderBy('list-{{$uri_key}}','{{$header_column}}')"><i class="ti ti-arrow-up"></i></button>
                        </th>
                        @else
                        <th style="white-space: nowrap;">{{$header_name}}
                        </th>
                        @endif
                        @endforeach
                        <th class="col-action"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataList as $data)
                    <tr>
                        @foreach($table_headers as $header)
                        @php
                        $header_column = $header['column'];
                        @endphp
                        <td data-sp-name="ajax-column-{{$header_column}}">
                            {!! $data[$header_column] !!}
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row">
                <div class="col-md-1 col-lg-1 col-2">
                    <select class="form-control form-control-sm" id="manydatas-show-{{$uri_key}}">
                        @foreach(['10', '20', '50', '100', 'All'] as $key => $showData)
                        <option value="{{$showData}}">{{$showData}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-11">
                    <div id="paginate-list-{{$uri_key}}"></div>
                </div>
            </div>
        </div>


    </div>
</div>