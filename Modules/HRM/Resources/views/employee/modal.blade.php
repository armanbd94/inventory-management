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
                  <div class="col-md-9">
                    <div class="row">
                      <input type="hidden" name="update_id" id="update_id"/>
                     
                      <x-form.textbox labelName="Name" name="name" required="required" col="col-md-6" placeholder="Enter name"/>
                      <x-form.textbox labelName="Phone No." name="phone" col="col-md-6" required="required" placeholder="Enter phone"/>
                      <x-form.selectbox labelName="Department" name="department_id" required="required" col="col-md-6" class="selectpicker">
                        @if (!$departments->isEmpty())
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        @endif
                      </x-form.selectbox>
                      <x-form.textbox labelName="Address" name="address" required="required" col="col-md-6" placeholder="Enter address"/>
                      <x-form.textbox labelName="City" name="city" required="required" col="col-md-6" placeholder="Enter city"/>
                      <x-form.textbox labelName="State" name="state" required="required" col="col-md-6" placeholder="Enter state"/>
                      <x-form.textbox labelName="Postal Code" name="postal_code" col="col-md-6" placeholder="Enter postal code"/>
                      <x-form.textbox labelName="Country" name="country" required="required" col="col-md-6" placeholder="Enter country"/>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <label for="image">Employee Image</label>
                            <div class="col-md-12 px-0 text-center">
                                <div id="image">
  
                                </div>
                            </div>
                            <input type="hidden" name="old_image" id="old_image">
                        </div>
                    </div>
                  </div>
                  </div>
                   
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