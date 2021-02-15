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
                                    <input type="text" class="form-control daterangepicker-filed" value="{{ date('Y-m-').'-01' }} To {{ date('Y-m-d') }}">
                                    <input type="hidden" name="start_date" value="{{ date('Y-m-').'-01' }}">
                                    <input type="hidden" name="end_date" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="form-group col-md-1" style="padding-top: 20px;">
                               <button type="button" class="btn btn-primary" id="btn-filter"
                               data-toggle="tooltip" data-placement="top" data-original-title="Filter Data">
                                   Search
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="col-md-12">
                        <div class="row" id="report">

                        </div>
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
<script src="/daterange/js/knockout-3.4.2.js"></script>
<script src="/daterange/js/daterangepicker.min.js"></script>
<script>
$(document).ready(function(){

    $('.daterangepicker-filed').daterangepicker({
        callback: function(startDate, endDate, period){
            var start_date = startDate.format('YYYY-MM-DD');
            var end_date   = endDate.format('YYYY-MM-DD');
            var title = start_date + ' To ' + end_date;
            $(this).val(title);
            $('input[name="start_date"]').val(start_date);
            $('input[name="end_date"]').val(end_date);
        }
    });
    report();
    $('#btn-filter').click(function () {
        report();
    });

    function report()
    {
        var start_date = $('input[name="start_date"]').val();
        var end_date   = $('input[name="end_date"]').val();
        $.ajax({
            url: "{{route('summary.report.details')}}",
            type: "POST",
            data: { start_date: start_date,end_date:end_date,_token: _token},
            success: function (data) {

                $('#report').html();
                $('#report').html(data);
            },
            error: function (xhr, ajaxOption, thrownError) {
                console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
            }
        });
    }




});
</script>
@endpush