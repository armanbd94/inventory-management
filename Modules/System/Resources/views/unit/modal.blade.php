<div class="modal fade" id="store_or_update_modal" tabindex="-1" role="dialog" aria-labelledby="model-1" aria-hidden="true">
    <div class="modal-dialog" role="document">

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
                    <x-form.textbox labelName="Unit Name" name="unit_name" required="required" col="col-md-12" placeholder="Enter unit name"/>
                    <x-form.textbox labelName="Unit Code" name="unit_code" required="required" col="col-md-12" placeholder="Enter unit code"/>
                    <x-form.selectbox labelName="Base Unit" name="base_unit" col="col-md-12" class="selectpicker">
                      
                    </x-form.selectbox>
                    <x-form.textbox labelName="Operator" name="operator" col="col-md-12" placeholder="Enter operator"/>
                    <x-form.textbox labelName="Operation Value" name="operation_value" col="col-md-12" placeholder="Enter operation value"/>
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