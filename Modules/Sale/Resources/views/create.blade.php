@extends('layouts.app')

@section('title')
    {{ $page_title }}
@endsection

@push('stylesheet')
<link rel="stylesheet" href="css/jquery-ui.css">
<style>
    .ui-menu .ui-menu-item{
        padding: 10px !important;
    }
</style>
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
                <a href="{{ route('sale') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                 </a>
            </div>
            <!-- /entry header -->

            <!-- Card -->
            <div class="dt-card">

                <!-- Card Body -->
                <div class="dt-card__body">

                    <form id="sale-form" method="POST" enctype="multipart/form-data">
                    @csrf
                        <div class="row">
                            
                            <x-form.selectbox labelName="Warehouse" name="warehouse_id" required="required" col="col-md-6" class="selectpicker">
                                @if (!$warehouses->isEmpty())
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                @endif
                            </x-form.selectbox>
                            <x-form.selectbox labelName="Customer" name="customer_id"  required="required" col="col-md-6" class="selectpicker">
                                @if (!$customers->isEmpty())
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }} 
                                            {{ $customer->company_name ? ' - '.$customer->company_name : '' }}</option>
                                    @endforeach
                                @endif
                            </x-form.selectbox>

                            <div class="form-group col-md-12">
                                <label for="product_code_name">Select Product</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="product_code_name" id="product_code_name" 
                                    placeholder="Type barcode or name and select product">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <table class="table table-bordered" id="product-list">
                                    <thead class="bg-primary">
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th class="text-center">Unit</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-right">Net Unit Price</th>
                                        <th class="text-right">Discount</th>
                                        <th class="text-right">Tax</th>
                                        <th class="text-right">Subtotal</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot class="bg-primary">
                                        <th colspan="3">Total</th>
                                        <th id="total-qty" class="text-center">0</th>
                                        <th></th>
                                        <th id="total-discount" class="text-right">0.00</th>
                                        <th id="total-tax" class="text-right">0.00</th>
                                        <th id="total" class="text-right">0.00</th>
                                        <th></th>
                                    </tfoot>
                                </table>
                            </div>
                            <x-form.selectbox labelName="Order Tax" name="order_tax_rate" col="col-md-4" class="selectpicker">
                                <option value="0" selected>No Tax</option>
                                @if (!$taxes->isEmpty())
                                    @foreach ($taxes as $tax)
                                        <option value="{{ $tax->rate }}">{{ $tax->name }}</option>
                                    @endforeach
                                @endif
                            </x-form.selectbox>

                            <div class="form-group col-md-4">
                                <label for="order_discount">Order Discount</label>
                                <input type="text" class="form-control" name="order_discount" id="order_discount">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="shipping_cost">Shipping Cost</label>
                                <input type="text" class="form-control" name="shipping_cost" id="shipping_cost">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="document">Attach Document</label>
                                <input type="file" class="form-control" name="document" id="document">
                            </div>
                            <x-form.selectbox labelName="Sale Status" name="sale_status"  required="required" col="col-md-4" class="selectpicker">
                                @foreach (SALE_STATUS as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </x-form.selectbox>
                            <div class="form-group col-md-4 d-none payment_status required">
                                <label for="payment_status">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="form-control selectpicker">
                                    <option value="">Select Please</option>
                                    <option value="1">Paid</option>
                                    <option value="2">Partial</option>
                                    <option value="3">Due</option>
                                </select>
                            </div>
                            <div class="payment col-md-12 d-none">
                                <div class="row">
                                    <x-form.selectbox labelName="Payment Method" name="payment_method" required="required"  col="col-md-4" class="selectpicker">
                                        @foreach (PAYMENT_METHOD as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </x-form.selectbox>
                                    
                                   
                                    <x-form.selectbox labelName="Account" name="account_id" required="required"  col="col-md-4" class="selectpicker">
                                        @if (!$accounts->isEmpty())
                                        @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name.' - '.$account->account_no }}</option>
                                        @endforeach
                                        @endif
                                    </x-form.selectbox>
                                    <div class="form-group col-md-4 payment_no d-none">
                                        <label for="payment_no"><span id="method-name"></span> No</label>
                                        <input type="text" class="form-control" name="payment_no" id="payment_no">
                                    </div>
                                    <x-form.textbox labelName="Received Amount" name="paying_amount" required="required" col="col-md-4"/>
                                    <div class="form-group col-md-4 required">
                                        <label for="paid_amount">Paying Amount</label>
                                        <input type="text" class="form-control" name="paid_amount" id="paid_amount">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="change_amount">Change Amount</label>
                                        <input type="text" class="form-control" name="change_amount" id="change_amount" readonly>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="shipping_cost">Note</label>
                                <textarea  class="form-control" name="note" id="note" cols="30" rows="3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead class="bg-primary">
                                        <th><strong>Items</strong><span class="float-right" id="item">0.00</span></th>
                                        <th><strong>Total</strong><span class="float-right" id="subtotal">0.00</span></th>
                                        <th><strong>Order Tax</strong><span class="float-right" id="order_total_tax">0.00</span></th>
                                        <th><strong>Order Discount</strong><span class="float-right" id="order_total_discount">0.00</span></th>
                                        <th><strong>Shipping Cost</strong><span class="float-right" id="shipping_total_cost">0.00</span></th>
                                        <th><strong>Grand Total</strong><span class="float-right" id="grand_total">0.00</span></th>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <input type="hidden" name="total_qty">
                                <input type="hidden" name="total_discount">
                                <input type="hidden" name="total_tax">
                                <input type="hidden" name="total_price">
                                <input type="hidden" name="item">
                                <input type="hidden" name="order_tax">
                                <input type="hidden" name="grand_total">
                            </div>
                            <div class="form-group col-md-12 text-center">
                                <button type="button" class="btn btn-danger btn-sm" id="reset-btn">Reset</button>
                                <button type="button" class="btn btn-primary btn-sm" id="save-btn">Save</button>
                            </div>
                        </div>
                    </form>

                </div>
                <!-- /card body -->

            </div>
            <!-- /card -->

        </div>
        <!-- /grid item -->

    </div>
    <!-- /grid -->
</div>

<!-- Start :: Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="model-1" aria-hidden="true">
    <div class="modal-dialog" role="document">

      <!-- Modal Content -->
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header bg-primary">
          <h3 class="modal-title text-white" id="model-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <!-- /modal header -->
        <form id="edit_form" method="post">
          @csrf
            <!-- Modal Body -->
            <div class="modal-body">
                <div class="row">
                    <x-form.textbox labelName="Quantity" name="edit_qty" required="required" col="col-md-12"/>
                    <x-form.textbox labelName="Unit Discount" name="edit_discount" col="col-md-12"/>
                    <x-form.textbox labelName="Unit Cost" name="edit_unit_cost" col="col-md-12"/>
                    @php 
                    $tax_name_all[] = 'No Tax';
                    $tax_rate_all[] = 0;
                    foreach ($taxes as $tax) {
                        $tax_name_all[] = $tax->name;
                        $tax_rate_all[] = $tax->rate;
                    }
                    @endphp
                    <div class="form-group col-md-12">
                        <label for="edit_tax_rate">Tax Rate</label>
                        <select name="edit_tax_rate" id="edit_tax_rate" class="form-control selectpicker">
                            @foreach ($tax_name_all as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="edit_unit">Product Unit</label>
                        <select name="edit_unit" id="edit_unit" class="form-control selectpicker"></select>
                    </div>
                </div>
            </div>
            <!-- /modal body -->

            <!-- Modal Footer -->
            <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary btn-sm" id="update-btn">Update</button>
            </div>
            <!-- /modal footer -->
        </form>
      </div>
      <!-- /modal content -->

    </div>
  </div>
<!-- End :: Edit Modal -->
@endsection

@push('script')
<script src="js/jquery-ui.js"></script>
<script>
$(document).ready(function(){

    $('#product_code_name').on('input',function(){
        var customer_id  = $('#customer_id option:selected').val();
        var warehouse_id = $('#warehouse_id option:selected').val();
        var temp_data = $('#product_code_name').val();
        if(!warehouse_id)
        {
            $('#product_code_name').val(temp_data.substring(0,temp_data.length - 1));
            notification('error','Please select warehouse');
        }else if(!customer_id){
            $('#product_code_name').val(temp_data.substring(0,temp_data.length - 1));
            notification('error','Please select customer');
        }
    });

    $('#product_code_name').autocomplete({
        source: function (request,response) {
            $.ajax({
                url: "{{ url('product-autocomplete-search') }}",
                type:"POST",
                dataType:"JSON",
                data:{_token:_token,search:request.term,warehouse_id:$('#warehouse_id option:selected').val()},
                success: function(data){
                    response(data);
                }
            });
        },
        minLength:1,
        response: function(event, ui) {
            if(ui.content.length == 1)
            {
                var data = ui.content[0].value;
                $(this).autocomplete('close');
                product_search(data);
            }
        },
        select: function(event,ui){
            var data = ui.item.value;
            product_search(data);
        }

    }).data('ui-autocomplete')._renderItem = function(ul, item){
        return $("<li class='ui-autocomplete-row'></li>")
        .data("item.autocomplete",item)
        .append(item.label)
        .appendTo(ul);
    };

    //array data depend on warehouse
    var product_array = [];
    var product_code  = [];
    var product_name  = [];
    var product_qty   = [];

    // array data with selection
    var product_price         = [];
    var product_discount     = [];
    var tax_rate             = [];
    var tax_name             = [];
    var tax_method           = [];
    var unit_name            = [];
    var unit_operator        = [];
    var unit_operation_value = [];

    //temporary array
    var temp_unit_name            = [];
    var temp_unit_operator        = [];
    var temp_unit_operation_value = [];

    var rowindex;
    var customer_group_rate;
    var row_product_price;
    var pos;

    $('#customer_id').on('change',function(){
        var id = $(this).val();
        $.get('{{ url("customer/group-data") }}/'+id,function(data){
            customer_group_rate = (data/100);
        });
    });

    //Edit Product
    $('#product-list').on('click','.edit-product', function(){
        rowindex = $(this).closest('tr').index();
        edit();
    });

    //Update Edit Product Data
    $('#update-btn').on('click',function(){
        var edit_discount  = $('#edit_discount').val();
        var edit_qty       = $('#edit_qty').val();
        var edit_unit_cost = $('#edit_unit_cost').val();

        if(parseFloat(edit_discount) > parseFloat(edit_unit_cost))
        {
            notification('error','Invalid discount input');
            return;
        }

        if(edit_qty < 1)
        {
            $('#edit_qty').val(1); 
            edit_qty = 1;
            notification('error','Quantity can\'t be less than 1');
        }

        var row_unit_operator = unit_operator[rowindex].slice(0,unit_operator[rowindex].indexOf(','));
        var row_unit_operation_value = unit_operation_value[rowindex].slice(0,unit_operation_value[rowindex].indexOf(','));
        row_unit_operation_value = parseFloat(row_unit_operation_value);
        var tax_rate_all = <?php echo json_encode($tax_rate_all); ?>;

        tax_rate[rowindex] = parseFloat(tax_rate_all[$('#edit_tax_rate option:selected').val()]);
        tax_name[rowindex] = $('#edit_tax_rate option:selected').text();

        if(row_unit_operator == '*')
        {
            product_price[rowindex] = $('#edit_unit_cost').val() / row_unit_operation_value;
        }else{
            product_price[rowindex] = $('#edit_unit_cost').val() * row_unit_operation_value;
        }

        product_discount[rowindex] = $('#edit_discount').val();
        var position = $('#edit_unit').val();
        var temp_operator = temp_unit_operator[position];
        var temp_operation_value = temp_unit_operation_value[position];
        $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('.sale-unit').val(temp_unit_name[position]);
        temp_unit_name.splice(position,1);
        temp_unit_operator.splice(position,1);
        temp_unit_operation_value.splice(position,1);

        temp_unit_name.unshift($('#edit_unit option:selected').text());
        temp_unit_operator.unshift(temp_operator);
        temp_unit_operation_value.unshift(temp_operation_value);

        unit_name[rowindex] = temp_unit_name.toString() + ',';
        unit_operator[rowindex] = temp_unit_operator.toString() + ',';
        unit_operation_value[rowindex] = temp_unit_operation_value.toString() + ',';
        checkQuantity(edit_qty,false);
    });

    $('#product-list').on('keyup','.qty',function(){
        rowindex = $(this).closest('tr').index();
        if($(this).val() < 1 && $(this).val() != ''){
            $('#product-list tbody tr:nth-child('+(rowindex + 1)+') .qty').val(1);
            notification('error','Qunatity can\'t be less than 1');
        }

        checkQuantity($(this).val(),true);
    });

    $('#product-list').on('click','.remove-product',function(){
        rowindex = $(this).closest('tr').index();
        product_price.splice(rowindex,1);
        product_qty.splice(rowindex,1);
        product_discount.splice(rowindex,1);
        tax_rate.splice(rowindex,1);
        tax_name.splice(rowindex,1);
        tax_method.splice(rowindex,1);
        unit_name.splice(rowindex,1);
        unit_operator.splice(rowindex,1);
        unit_operation_value.splice(rowindex,1);
        $(this).closest('tr').remove();
        calculateTotal();
    });

    var count = 1;
    function product_search(data)
    {
        $.ajax({
            url: "{{ url('product-search') }}",
            type:"POST",
            data:{data:data,_token:_token,warehouse_id:$('#warehouse_id option:selected').val(),type:'sale'},
            success: function(data)
            {

                var flag = 1;
                $('.product-code').each(function(i){
                    if($(this).val() == data.code){
                        rowindex = i;
                        var qty = parseFloat($('#product-list tbody tr:nth-child('+(rowindex + 1)+') .qty').val()) + 1;
                        $('#product-list tbody tr:nth-child('+(rowindex + 1)+') .qty').val(qty);
                        checkQuantity(String(qty),true);
                        flag = 0;
                    }
                });
                $('#product_code_name').val('');
                if(flag)
                {
                    temp_unit_name = data.unit_name.split(',');
                    var newRow = $('<tr>');
                    var cols = '';
                    cols += `<td>`+data.name+`</td>`;
                    
                    cols += `<td>`+data.code+`</td>`;
                    cols += `<td class="unit-name"></td>`;
                    cols += `<td><input type="text" class="form-control qty text-center" name="products[`+count+`][qty]"
                        id="products_`+count+`_qty" value="1"></td>`;

                    cols += `<td class="net_unit_price text-right"></td>`;
                    cols += `<td class="discount text-right"></td>`;
                    cols += `<td class="tax text-right"></td>`;
                    cols += `<td class="sub-total text-right"></td>`;
                    cols += `<td><button type="button" class="edit-product btn btn-sm btn-primary mr-2" data-toggle="modal"
                        data-target="#editModal"><i class="fas fa-edit"></i></button>
                        <button type="button" class="btn btn-danger btn-sm remove-product"><i class="fas fa-trash"></i></button></td>`;
                    cols += `<input type="hidden" class="product-id" name="products[`+count+`][id]"  value="`+data.id+`">`;
                    cols += `<input type="hidden" class="stock-qty" name="products[`+count+`][stock_qty]"  value="`+data.qty+`">`;
                    cols += `<input type="hidden" class="product-code" name="products[`+count+`][code]" value="`+data.code+`">`;
                    cols += `<input type="hidden" class="product-unit" name="products[`+count+`][unit]" value="`+temp_unit_name[0]+`">`;
                    cols += `<input type="hidden" class="net_unit_price" name="products[`+count+`][net_unit_price]">`;
                    cols += `<input type="hidden" class="discount-value" name="products[`+count+`][discount]">`;
                    cols += `<input type="hidden" class="tax-rate" name="products[`+count+`][tax_rate]" value="`+data.tax_rate+`">`;
                    cols += `<input type="hidden" class="tax-value" name="products[`+count+`][tax]">`;
                    cols += `<input type="hidden" class="subtotal-value" name="products[`+count+`][subtotal]">`;

                    newRow.append(cols);
                    $('#product-list tbody').append(newRow);

                    product_price.push(parseFloat(data.price) + parseFloat(data.price * customer_group_rate));
                    product_qty.push(data.qty);
                    product_discount.push('0.00');
                    tax_rate.push(parseFloat(data.tax_rate));
                    tax_name.push(data.tax_name);
                    tax_method.push(data.tax_method);
                    unit_name.push(data.unit_name);
                    unit_operator.push(data.unit_operator);
                    unit_operation_value.push(data.unit_operation_value);
                    rowindex = newRow.index();
                    checkQuantity(1,true);
                    count++;
                }

            }
        });
    }

    function checkQuantity(sale_qty,flag)
    {
        var row_product_code = $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('td:nth-child(2)').text();
        var operator = unit_operator[rowindex].split(',');
        var operation_value = unit_operation_value[rowindex].split(',');

        if(operator[0] == '*')
        {
            total_qty = sale_qty * operation_value[0];
        }else if(operator[0] == '/'){
            total_qty = sale_qty / operation_value[0];
        }
        if(total_qty > parseFloat(product_qty[rowindex])){
            notification('errpr','Quantity exceed stock quantity');
            if(flag)
            {
                sale_qty = sale_qty.substring(0,sale_qty.length - 1);
                $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('.qty').val(sale_qty);
            }else{
                edit();
                return;
            }
        }
        if(!flag)
        {
            $('#editModal').modal('hide');
            $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('.qty').val(sale_qty);
        }
        calculateProductData(sale_qty);

    }

    function edit()
    {
        var row_product_name = $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('td:nth-child(1)').text();
        var row_product_code = $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('td:nth-child(2)').text();
        $('#model-title').text(row_product_name+'('+row_product_code+')');

        var qty = $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('.qty').val();
        $('#edit_qty').val(qty);
        $('#edit_discount').val(parseFloat(product_discount[rowindex]).toFixed(2));

        unitConversion();
        $('#edit_unit_cost').val(row_product_price.toFixed(2));

        var tax_name_all = <?php echo json_encode($tax_name_all); ?>;
        var pos = tax_name_all.indexOf(tax_name[rowindex]);
        $('#edit_tax_rate').val(pos);

        temp_unit_name = (unit_name[rowindex]).split(',');
        temp_unit_name.pop();
        temp_unit_operator = (unit_operator[rowindex]).split(',');
        temp_unit_operator.pop();
        temp_unit_operation_value = (unit_operation_value[rowindex]).split(',');
        temp_unit_operation_value.pop();

        $('#edit_unit').empty();

        $.each(temp_unit_name, function(key,value){
            $('#edit_unit').append('<option value="'+key+'">'+value+'</option>');
        });
        $('.selectpicker').selectpicker('refresh');
    }

    function calculateProductData(quantity){ 
        unitConversion();
        $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('td:nth-child(6)').text((product_discount[rowindex] * quantity).toFixed(2));
        $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('.discount-value').val((product_discount[rowindex] * quantity).toFixed(2));
        $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('.tax-rate').val(tax_rate[rowindex].toFixed(2));
        $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('.unit-name').text(unit_name[rowindex].slice(0,unit_name[rowindex].indexOf(",")));

        if(tax_method[rowindex] == 1)
        {
            var net_unit_price = row_product_price - product_discount[rowindex];
            var tax = net_unit_price * quantity * (tax_rate[rowindex]/100);
            var sub_total = (net_unit_price * quantity) + tax;

        }else{
            var sub_total_unit = row_product_price - product_discount[rowindex];
            var net_unit_price = (100 / (100 + tax_rate[rowindex])) * sub_total_unit;
            var tax = (sub_total_unit - net_unit_price) * quantity;
            var sub_total = sub_total_unit * quantity;
        }
        $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('td:nth-child(5)').text(net_unit_price.toFixed(2));
        $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('.net_unit_price').val(net_unit_price.toFixed(2));
        $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('td:nth-child(7)').text(tax.toFixed(2));
        $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('.tax-value').val(tax.toFixed(2));
        $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('td:nth-child(8)').text(sub_total.toFixed(2));
        $('#product-list tbody tr:nth-child('+(rowindex + 1)+')').find('.subtotal-value').val(sub_total.toFixed(2));

        calculateTotal();
    }

    function unitConversion()
    {
        var row_unit_operator = unit_operator[rowindex].slice(0,unit_operator[rowindex].indexOf(','));
        var row_unit_operation_value = unit_operation_value[rowindex].slice(0,unit_operation_value[rowindex].indexOf(','));
        row_unit_operation_value = parseFloat(row_unit_operation_value);
        if(row_unit_operator == '*')
        {
            row_product_price = product_price[rowindex] * row_unit_operation_value;
        }else{
            row_product_price = product_price[rowindex] / row_unit_operation_value;
        }
    }

    function calculateTotal()
    {
        //sum of qty
        var total_qty = 0;
        $('.qty').each(function() {
            if($(this).val() == ''){
                total_qty += 0;
            }else{
                total_qty += parseFloat($(this).val());
            }
        });
        $('#total-qty').text(total_qty);
        $('input[name="total_qty"]').val(total_qty);

        //sum of discount
        var total_discount = 0;
        $('.discount').each(function() {
            total_discount += parseFloat($(this).text());
        });
        $('#total-discount').text(total_discount.toFixed(2));
        $('input[name="total_discount"]').val(total_discount.toFixed(2));

        //sum of tax
        var total_tax = 0;
        $('.tax').each(function() {
            total_tax += parseFloat($(this).text());
        });
        $('#total-tax').text(total_tax.toFixed(2));
        $('input[name="total_tax"]').val(total_tax.toFixed(2));

        //sum of subtotal
        var total = 0;
        $('.sub-total').each(function() {
            total += parseFloat($(this).text());
        });
        $('#total').text(total.toFixed(2));
        $('input[name="total_price"]').val(total.toFixed(2));

        calculateGrandTotal();
    }

    function calculateGrandTotal()
    {
        var item = $('#product-list tbody tr:last').index();
        var total_qty = parseFloat($('#total-qty').text());
        var subtotal = parseFloat($('#total').text());
        var order_tax = parseFloat($('select[name="order_tax_rate"]').val());
        var order_discount = parseFloat($('#order_discount').val());
        var shipping_cost = parseFloat($('#shipping_cost').val());

        if(!order_discount){
            order_discount = 0.00;
        }
        if(!shipping_cost){
            shipping_cost = 0.00;
        }

        item = ++item + '(' + total_qty + ')';
        order_tax = (subtotal - order_discount) * (order_tax / 100);
        var grand_total = (subtotal + order_tax + shipping_cost) - order_discount;

        $('#item').text(item);
        $('input[name="item"]').val($('#product-list tbody tr:last').index() + 1);
        $('#subtotal').text(subtotal.toFixed(2));
        $('#order_total_tax').text(order_tax.toFixed(2));
        $('input[name="order_tax"]').val(order_tax.toFixed(2));
        $('#order_total_discount').text(order_discount.toFixed(2));
        $('#shipping_total_cost').text(shipping_cost.toFixed(2));
        $('#grand_total').text(grand_total.toFixed(2));
        $('input[name="grand_total"]').val(grand_total.toFixed(2));
        if($('#payment_status option:selected').val() == 1)
        {
            $('#paying_amount').val('');
            $('#paid_amount').val(grand_total.toFixed(2));
        }
    }

    $('input[name="order_discount"]').on('input',function(){
        calculateGrandTotal();
    });
    $('input[name="shipping_cost"]').on('input',function(){
        calculateGrandTotal();
    });
    $('select[name="order_tax_rate"]').on('change',function(){
        calculateGrandTotal();
    });

    $('#payment_status').on('change',function(){
        var payment_status = $('#payment_status option:selected').val();
        if($(this).val() != 3){
            $('.payment').removeClass('d-none');
            $('#paying_amount').val($('input[name="grand_total"]').val());
            $('#paid_amount').val($('input[name="grand_total"]').val());
            $('#change_amount').val(parseFloat(0).toFixed(2));
        }else{
            $('#paying_amount').val('');
            $('#paid_amount').val('');
            $('.payment').addClass('d-none');
        }
    });

    $(document).on('change', '#payment_method', function () {
        if($('#payment_method option:selected').val() != 1)
        {
            var method = $('#payment_method option:selected').val() == 2 ? 'Cheque' : 'Mobile';
            $('#method-name').text(method);
            $('.payment_no').removeClass('d-none');
        }else{
            $('.payment_no').addClass('d-none');
        }
    });

    $('#paid_amount').on('input',function(){
        if(parseFloat($('#paid_amount').val()) > parseFloat($('#paying_amount').val()))
        {
            notification('error','Paying amount cannot be bigger than received amount');
            $('#paid_amount').val('');
        }else if(parseFloat($('#paid_amount').val()) > parseFloat($('#grand_total').text()))
        {
            notification('error','Paying amount cannot be bigger than grand total amount');
            $('#paid_amount').val('');
        }
        $('#change_amount').val((parseFloat($('#paying_amount').val()) - parseFloat($('#paid_amount').val())).toFixed(2));
    });

    $('#paying_amount').on('input',function(){
        $('#change_amount').val((parseFloat($('#paying_amount').val()) - parseFloat($('#paid_amount').val())).toFixed(2));
    });

    $('#sale_status').on('change',function(){
        if($('#sale_status option:selected').val() == 1)
        {
            $('.payment_status').removeClass('d-none');
        }else{
            $('.payment_status').addClass('d-none');
            $('.payment').addClass('d-none');
        }

    });

    $(document).on('click','#save-btn', function(e){
        e.preventDefault();

        var rownumber = $('#product-list tbody tr:last').index();
        if(rownumber < 0)
        {
            notification('error','Please add product to order table');
        }else{
            let form = document.getElementById('sale-form');
            let formData = new FormData(form);
            $.ajax({
                url: "{{route('sale.store')}}",
                type: "POST",
                data: formData,
                dataType: "JSON",
                contentType: false,
                processData: false,
                cache: false,
                beforeSend: function(){
                    $('#save-btn').addClass('kt-spinner kt-spinner--md kt-spinner--light');
                },
                complete: function(){
                    $('#save-btn').removeClass('kt-spinner kt-spinner--md kt-spinner--light');
                },
                success: function (data) {
                    $('#sale-form').find('.is-invalid').removeClass('is-invalid');
                    $('#sale-form').find('.error').remove();
                    if (data.status == false) {
                        $.each(data.errors, function (key, value) {
                            var key = key.split('.').join('_');
                            $('#sale-form input#' + key).addClass('is-invalid');
                            $('#sale-form textarea#' + key).addClass('is-invalid');
                            $('#sale-form select#' + key).parent().addClass('is-invalid');
                            $('#sale-form #' + key).parent().append(
                            '<small class="error text-danger">' + value + '</small>');
                                                        
                        });
                    } else {
                        notification(data.status, data.message);
                        if (data.status == 'success') {
                            window.location.replace('{{route("sale")}}');
                        }
                    }

                },
                error: function (xhr, ajaxOption, thrownError) {
                    console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
                }
            });
        }
    });


});
</script>
@endpush