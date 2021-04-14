
@extends('layouts.app')

@section('title')
    {{ $page_title }}
@endsection

@push('stylesheet')
<link rel="stylesheet" href="daterange/css/daterangepicker.min.css">
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
            </div>
            <!-- /entry header -->

            <!-- Card -->
            <div class="dt-card">

                <!-- Card Body -->
                <div class="dt-card__body">

                    <form id="form-filter">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="name">Choose Your Date</label>
                                <div class="input-group">
                                    <input type="text" class="form-control daterangepicker-filed">
                                    <input type="hidden" name="from_date" id="from_date">
                                    <input type="hidden" name="to_date" id="to_date">
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="purchase_no">Purchase No</label>
                                <input type="text" class="form-control" name="purchase_no" id="purchase_no" placeholder="Enter sale no">
                            </div>
                            <x-form.selectbox labelName="Supplier" name="supplier_id" col="col-md-3" class="selectpicker">
                                @if (!$suppliers->isEmpty())
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name.' - '.$supplier->phone }}</option>
                                    @endforeach
                                @endif
                            </x-form.selectbox>
                            
                            <div class="form-group col-md-3"  style="padding-top:20px;">
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
                                    <th>Sl</th>
                                    <th>Supplier</th>
                                    <th>Purchase No</th>
                                    <th>Date</th>
                                    <th>Grand Total</th>
                                    <th>Paid Amount</th>
                                    <th>Due Amount</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr class="bg-primary">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="text-right">Total</th>
                                    <th class="text-right"></th>
                                    <th class="text-right"></th>
                                    <th class="text-right"></th>
                                </tr>
                            </tfoot>
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

@endsection

@push('script')
<script src="js/moment.min.js"></script>
<script src="/daterange/js/knockout-3.4.2.js"></script>
<script src="/daterange/js/daterangepicker.min.js"></script>
<script>
var table;
$(document).ready(function(){
    $('.daterangepicker-filed').daterangepicker({
        callback: function(startDate, endDate, period){
            var start_date = startDate.format('YYYY-MM-DD');
            var end_date   = endDate.format('YYYY-MM-DD');
            var title = start_date + ' To ' + end_date;
            $(this).val(title);
            $('input[name="from_date"]').val(start_date);
            $('input[name="to_date"]').val(end_date);
        }
    });
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
            "url": "{{route('supplier.report.datatable.data')}}",
            "type": "POST",
            "data": function (data) {
                data.purchase_no     = $("#form-filter #purchase_no").val();
                data.supplier_id     = $("#form-filter #supplier_id option:selected").val();
                data.from_date       = $("#form-filter #from_date").val();
                data.to_date         = $("#form-filter #to_date").val();
                data._token          = _token;
            }
        },
        "columnDefs": [{
                "targets": [6],
                "orderable": false,
                "className": "text-right"
            },
            {
                "targets": [2,3],
                "className": "text-center"
            },
            {
                "targets": [4,5,6],
                "className": "text-right"
            }
        ],
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6' <'float-right'B>>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'<'float-right'p>>>",

        "buttons": [
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
                "filename": "sale-list",
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
                "filename": "sale-list",
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
                "filename": "sale-list",
                "orientation": "landscape", //portrait
                "pageSize": "A4", //A3,A5,A6,legal,letter
                "exportOptions": {
                    columns: [1, 2, 3]
                },
            },

        ],
        "footerCallback" : function(row,data,start,end,display)
        {
            var api = this.api(), data;

            var intVal = function(i){
                return typeof i === 'string' ? i.replace(/[\$,]/g,'')*1 : typeof i === 'number' ?  i : 0;
            };

            for(var index=4;index <= 6;index++)
            {
                total = api.column(index).data().reduce(function (a,b){
                    return intVal(a) + intVal(b);
                },0);

                pageTotal = api.column(index, {page: 'current'}).data().reduce(function (a,b){
                    return intVal(a) + intVal(b);
                },0);

                $(api.column(index).footer()).html('= '+parseFloat(pageTotal).toFixed(2)+' ('+parseFloat(total).toFixed(2)+' Total)');
            }
        }
    });

    $('#btn-filter').click(function () {
        table.ajax.reload();
    });

    $('#btn-reset').click(function () {
        $('#form-filter')[0].reset();
        $('input[name="from_date"]').val('');
        $('input[name="to_date"]').val('');
        $('#form-filter .selectpicker').selectpicker('refresh');
        table.ajax.reload();
    });

});

</script>
@endpush