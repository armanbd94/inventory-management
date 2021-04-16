@extends('layouts.app')

@section('title')
    {{ $page_title }}
@endsection

@push('stylesheet')
    
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
                @if (permission('product-access'))
                <a href="{{ route('product') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-list"></i> Manage Product 
                </a>
                @endif
                
            </div>
            <!-- /entry header -->

            <!-- Card -->
            <div class="dt-card">

                <!-- Card Body -->
                <div class="dt-card__body">

                    <form id="form-barcode">
                        <div class="row">
                            
                            <x-form.selectbox labelName="Product" name="product" col="col-md-3" class="selectpicker">
                                @if (!$products->isEmpty())
                                    @foreach ($products as $product)
                                        <option value="{{ $product->code }}" data-name="{{ $product->name }}" 
                                            data-price="{{ $product->price }}" data-barcode="{{ $product->barcode_symbology }}">{{ $product->name.' - '.$product->code }}</option>
                                    @endforeach
                                @endif
                            </x-form.selectbox>
                            <div class="form-group col-md-3">
                                <label for="barcode_qty">No. Of Barcode</label>
                                <input type="text" class="form-control" name="barcode_qty" id="barcode_qty" placeholder="Enter barcode qty">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="row_qty">Quantity Each Row</label>
                                <input type="text" class="form-control" name="row_qty" id="row_qty" placeholder="Enter row qty">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="row_qty">Print With</label>
                                <div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="product_name" >
                                        <label class="custom-control-label" for="product_name">Product Name</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="price" >
                                        <label class="custom-control-label" for="price">Price</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="row_qty">Barcode Size</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="width" id="width" placeholder="Width">
                                    <input type="text" class="form-control" name="height" id="height" placeholder="Height">
                                    <select name="unit" id="unit" class="form-control selectpicker">
                                        <option value="mm">mm</option>
                                        <option value="px">px</option>
                                        <option value="in">in</option>
                                        <option value="cm">cm</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-3" style="padding-top: 22px;">
                               <button type="button" class="btn btn-primary btn-sm" id="generate_barcode"
                               data-toggle="tooltip" data-placement="top" data-original-title="Generate Barcode">
                                   <i class="fas fa-barcode"></i> Generate Barcode
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="row" id="barcode-section">

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
<script src="js/jquery.printarea.js"></script>
<script>
$(document).ready(function(){

    $(document).on('click', '#generate_barcode', function () {
        var code        = $('#product option:selected').val();
        var barcode_symbology    = $('#product option:selected').data('barcode');;
        var name        = '';
        var price       = '';
        var barcode_qty = $('#barcode_qty').val();
        var row_qty     = $('#row_qty').val();
        var width       = $('#width').val();
        var height      = $('#height').val();
        var unit        = $('#unit option:selected').val();
        if($('#product_name').prop('checked') == true)
        {
            name = $('#product option:selected').data('name');
        }
        if($('#price').prop('checked') == true)
        {
            price = $('#product option:selected').data('price');
        }
        $.ajax({
            url: "{{ url('generate-barcode') }}",
            type: "POST",
            data: {code:code, name:name, price:price, barcode_qty:barcode_qty, barcode_symbology:barcode_symbology,
                row_qty:row_qty, width:width, height:height, unit:unit,_token:_token},
            beforeSend: function(){
                $('#generate_barcode').addClass('kt-spinner kt-spinner--md kt-spinner--light');
            },
            complete: function(){
                $('#generate_barcode').removeClass('kt-spinner kt-spinner--md kt-spinner--light');
            },
            success: function (data) {
                $('#form-barcode').find('.is-invalid').removeClass('is-invalid');
                $('#form-barcode').find('.error').remove();
                if (data.status == false) {
                    $.each(data.errors, function (key, value) {
                        if(key == 'code'){
                            $('#form-barcode select#product').parent().addClass('is-invalid');
                            $('#form-barcode #product').parent().append(
                            '<small class="error text-danger">' + value + '</small>');
                        }
                        $('#form-barcode input#' + key).addClass('is-invalid');
                        $('#form-barcode select#' + key).parent().addClass('is-invalid');
                            $('#form-barcode #' + key).parent().append(
                            '<small class="error text-danger">' + value + '</small>');
                        
                    });
                } else {
                    $('#barcode-section').html('');
                    $('#barcode-section').html(data);
                }

            },
            error: function (xhr, ajaxOption, thrownError) {
                console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
            }
        });
    });

    $(document).on('click','#print-barcode',function()
    {
        var mode = 'popup'; //popup
        var close = mode == 'popup';
        var options = {
            mode:mode,
            popClose:close
        };
        $('#printableArea').printArea(options);
    })

});


</script>
@endpush