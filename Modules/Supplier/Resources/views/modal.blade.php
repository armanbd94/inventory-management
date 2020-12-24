<div class="modal fade" id="store_or_update_modal" tabindex="-1" role="dialog" aria-labelledby="model-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">

      <!-- Modal Content -->
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header bg-primary">
          <h3 class="modal-title text-white" id="model-1"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <!-- /modal header -->
        <form id="store_or_update_form" method="post">
          @csrf
            <!-- Modal Body -->
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="update_id" id="update_id"/>
                    <x-form.textbox labelName="Customer Name" name="name" required="required" col="col-md-6" placeholder="Enter customer name"/>
                    <x-form.textbox labelName="Company Name" name="company_name" col="col-md-6" placeholder="Enter company name"/>
                    <x-form.textbox labelName="Vat Number" name="vat_number" col="col-md-6" placeholder="Enter vat number"/>
                    <x-form.textbox labelName="Phone No." name="phone" col="col-md-6" placeholder="Enter phone"/>
                    <x-form.textbox type="email" labelName="Email" name="email" col="col-md-6" placeholder="Enter email"/>
                    <x-form.textbox labelName="Address" name="address" col="col-md-6" placeholder="Enter address"/>
                    <x-form.textbox labelName="City" name="city" col="col-md-6" placeholder="Enter city"/>
                    <x-form.textbox labelName="State" name="state" col="col-md-6" placeholder="Enter state"/>
                    <x-form.textbox labelName="Postal Code" name="postal_code" col="col-md-6" placeholder="Enter postal code"/>
                    <x-form.textbox labelName="Country" name="country" col="col-md-6" placeholder="Enter country"/>
                </div>
            </div>
            <!-- /modal body -->

            <!-- Modal Footer -->
            <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary btn-sm" id="save-btn"></button>
            </div>
            <!-- /modal footer -->
        </form>
      </div>
      <!-- /modal content -->

    </div>
  </div>