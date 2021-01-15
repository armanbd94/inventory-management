
@extends('layouts.app')

@section('title')
    {{ $page_title }}
@endsection

@push('stylesheet')
<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="dt-content">

    <!-- Grid -->
    <div class="row">
        <div class="col-xl-12 pb-3">
            <ol class="breadcrumb bg-white">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                <li class="active breadcrumb-item">{{ $sub_title }}</li>
              </ol>
        </div>
        <!-- Grid Item -->
        <div class="col-xl-12">

            <!-- Entry Header -->
            <div class="dt-entry__header">

                <!-- Entry Heading -->
                <div class="dt-entry__heading">
                    <h2 class="dt-page__title mb-0 text-primary"><i class="{{ $page_icon }}"></i> {{ $sub_title }}</h2>
                </div>
                <!-- /entry heading -->
                @if (permission('purchase-add'))
                <a href="{{ route('purchase.add') }}" class="btn btn-primary btn-sm" >
                    <i class="fas fa-plus-square"></i> Add New
                 </a>
                @endif
                

            </div>
            <!-- /entry header -->

            <!-- Card -->
            <div class="dt-card">

                <!-- Card Body -->
                <div class="dt-card__body">

                    <form id="form-filter">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="purchase_no">Product No</label>
                                <input type="text" class="form-control" name="purchase_no" id="purchase_no" placeholder="Enter purchase no">
                            </div>
                            <x-form.selectbox labelName="Supplier" name="supplier_id" col="col-md-3" class="selectpicker">
                                @if (!$suppliers->isEmpty())
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name.' - '.$supplier->phone }}</option>
                                    @endforeach
                                @endif
                            </x-form.selectbox>
                            <div class="form-group col-md-3">
                                <label for="from_date">From Date</label>
                                <input type="text" class="form-control date" name="from_date" id="from_date">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="to_date">To Date</label>
                                <input type="text" class="form-control date" name="to_date" id="to_date">
                            </div>
                            <x-form.selectbox labelName="Purchase Status" name="purchase_status" col="col-md-3" class="selectpicker">
                                    @foreach (PURCHASE_STATUS as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                            </x-form.selectbox>
                            <x-form.selectbox labelName="Payment Status" name="payment_status" col="col-md-3" class="selectpicker">
                                    @foreach (PAYMENT_STATUS as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                            </x-form.selectbox>
                            <div class="form-group col-md-6">
                               <button type="button" class="btn btn-danger btn-sm float-right" id="btn-reset"
                               data-toggle="tooltip" data-placement="top" data-original-title="Reset Data">
                                   <i class="fas fa-redo-alt"></i>
                                </button>
                               <button type="button" class="btn btn-primary btn-sm float-right mr-2" id="btn-filter"
                               data-toggle="tooltip" data-placement="top" data-original-title="Filter Data">
                                   <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="col-md-12 table-responsive">
                        <table id="dataTable" class="table table-striped table-bordered table-hover">
                            <thead class="bg-primary">
                                <tr>
                                    @if (permission('purchase-bulk-delete'))
                                    <th>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="select_all" onchange="select_all()">
                                            <label class="custom-control-label" for="select_all"></label>
                                        </div>
                                    </th>
                                    @endif
                                    <th>Sl</th>
                                    <th>Purchase No</th>
                                    <th>Supplier</th>
                                    <th>Total Items</th>
                                    <th>Total Qty</th>
                                    <th>Total Discount</th>
                                    <th>Ttotal Tax</th>
                                    <th>Total Cost</th>
                                    <th>Tax Rate</th>
                                    <th>Total Order Tax</th>
                                    <th>Total Order Discount</th>
                                    <th>Shipping Cost</th>
                                    <th>Grand Total</th>
                                    <th>Paid Amount</th>
                                    <th>Due Amount</th>
                                    <th>Purchase Status</th>
                                    <th>Payment Status</th>
                                    <th>Created By</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- /card body -->

            </div>
            <!-- /card -->

        </div>
        <!-- /grid item -->

    </div>
    <!-- /grid -->

</div>
@endsection

@push('script')
<script src="js/moment.min.js"></script>
<script src="js/bootstrap-datetimepicker.min.js"></script>
<script>
var table;
$(document).ready(function(){
    $('.date').datetimepicker({format:"YYYY-MM-DD"});
    table = $('#dataTable').DataTable({
        "processing": true, //Feature control the processing indicator
        "serverSide": true, //Feature control DataTable server side processing mode
        "order": [], //Initial no order
        "responsive": false, //Make table responsive in mobile device
        "bInfo": true, //TO show the total number of data
        "bFilter": false, //For datatable default search box show/hide
        "lengthMenu": [
            [5, 10, 15, 25, 50, 100, 1000, 10000, -1],
            [5, 10, 15, 25, 50, 100, 1000, 10000, "All"]
        ],
        "pageLength": 25, //number of data show per page
        "language": { 
            processing: `<i class="fas fa-spinner fa-spin fa-3x fa-fw text-primary"></i> `,
            emptyTable: '<strong class="text-danger">No Data Found</strong>',
            infoEmpty: '',
            zeroRecords: '<strong class="text-danger">No Data Found</strong>'
        },
        "ajax": {
            "url": "{{route('purchase.datatable.data')}}",
            "type": "POST",
            "data": function (data) {
                data.purchase_no     = $("#form-filter #purchase_no").val();
                data.supplier_id     = $("#form-filter #supplier_id").val();
                data.from_date       = $("#form-filter #from_date").val();
                data.to_date         = $("#form-filter #to_date").val();
                data.purchase_status = $("#form-filter #purchase_status").val();
                data.payment_status  = $("#form-filter #payment_status").val();
                data._token          = _token;
            }
        },
        "columnDefs": [{
                @if (permission('purchase-bulk-delete'))
                "targets": [0,20],
                @else 
                "targets": [19],
                @endif
                "orderable": false,
                "className": "text-center"
            },
            {
                @if (permission('purchase-bulk-delete'))
                "targets": [1,4,5,16,17,18,19],
                @else 
                "targets": [0,3,4,15,16,17,18],
                @endif
                "className": "text-center"
            },
            {
                @if (permission('purchase-bulk-delete'))
                "targets": [6,7,8,9,10,11,12,13,14,15],
                @else 
                "targets": [5,6,7,8,9,10,11,12,13,14],
                @endif
                "className": "text-right"
            }
        ],
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6' <'float-right'B>>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'<'float-right'p>>>",

        "buttons": [
            @if (permission('purchase-report'))
            {
                'extend':'colvis','className':'btn btn-secondary btn-sm text-white','text':'Column'
            },
            {
                "extend": 'print',
                'text':'Print',
                'className':'btn btn-secondary btn-sm text-white',
                "title": "Menu List",
                "orientation": "landscape", //portrait
                "pageSize": "A4", //A3,A5,A6,legal,letter
                "exportOptions": {
                    columns: function (index, data, node) {
                        return table.column(index).visible();
                    }
                },
                customize: function (win) {
                    $(win.document.body).addClass('bg-white');
                },
            },
            {
                "extend": 'csv',
                'text':'CSV',
                'className':'btn btn-secondary btn-sm text-white',
                "title": "Menu List",
                "filename": "purchase-list",
                "exportOptions": {
                    columns: function (index, data, node) {
                        return table.column(index).visible();
                    }
                }
            },
            {
                "extend": 'excel',
                'text':'Excel',
                'className':'btn btn-secondary btn-sm text-white',
                "title": "Menu List",
                "filename": "purchase-list",
                "exportOptions": {
                    columns: function (index, data, node) {
                        return table.column(index).visible();
                    }
                }
            },
            {
                "extend": 'pdf',
                'text':'PDF',
                'className':'btn btn-secondary btn-sm text-white',
                "title": "Menu List",
                "filename": "purchase-list",
                "orientation": "landscape", //portrait
                "pageSize": "A4", //A3,A5,A6,legal,letter
                "exportOptions": {
                    columns: [1, 2, 3]
                },
            },
            @endif 
            @if (permission('purchase-bulk-delete'))
            {
                'className':'btn btn-danger btn-sm delete_btn d-none text-white',
                'text':'Delete',
                action:function(e,dt,node,config){
                    multi_delete();
                }
            }
            @endif
        ],
    });

    $('#btn-filter').click(function () {
        table.ajax.reload();
    });

    $('#btn-reset').click(function () {
        $('#form-filter')[0].reset();
        $('#form-filter .selectpicker').selectpicker('refresh');
        table.ajax.reload();
    });



    $(document).on('click', '.delete_data', function () {
        let id    = $(this).data('id');
        let name  = $(this).data('name');
        let row   = table.row($(this).parent('tr'));
        let url   = "{{ route('purchase.delete') }}";
        delete_data(id, url, table, row, name);
    });

    function multi_delete(){
        let ids = [];
        let rows;
        $('.select_data:checked').each(function(){
            ids.push($(this).val());
            rows = table.rows($('.select_data:checked').parents('tr'));
        });
        if(ids.length == 0){
            Swal.fire({
                type:'error',
                title:'Error',
                text:'Please checked at least one row of table!',
                icon: 'warning',
            });
        }else{
            let url = "{{route('purchase.bulk.delete')}}";
            bulk_delete(ids,url,table,rows);
        }
    }




});

</script>
@endpush