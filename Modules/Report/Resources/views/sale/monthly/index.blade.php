@extends('layouts.app')

@section('title')
    {{ $page_title }}
@endsection

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
                        <div class="row justify-content-center">
                            <x-form.selectbox labelName="Warehouse" name="warehouse_id" required="required" col="col-md-3" class="selectpicker">
                                <option value="0" selected>All Warehouses</option>
                                @if (!$warehouses->isEmpty())
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                @endif
                            </x-form.selectbox>
                        </div>
                    </form>
                    <div class="col-md-12" id="report">

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
<script>
$(document).ready(function(){

    daily_report(warehouse_id=0,year='{{ date("Y") }}',date='{{ date("m") }}');

    $(document).on('click','.previous',function(){
        var year = $('#prev_year').val();
        var warehouse_id = $('#warehouse_id option:selected').val();
        daily_report(warehouse_id,year);
    });
    $(document).on('click','.next',function(){
        var year = $('#next_year').val();
        var warehouse_id = $('#warehouse_id option:selected').val();
        daily_report(warehouse_id,year);
    });

    $('#warehouse_id').change(function(){
        var warehouse_id = $('#warehouse_id option:selected').val();
        daily_report(warehouse_id,year='{{ date("Y") }}');
    });

    function daily_report(warehouse_id,year)
    {
        $.ajax({
            url:"{{ url('monthly-sale-report') }}",
            type:"POST",
            data:{warehouse_id:warehouse_id,year:year,_token:_token},
            success:function(data){
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