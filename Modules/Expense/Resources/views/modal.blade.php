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
                    <x-form.selectbox labelName="Expense Category" name="expense_category_id"  required="required" col="col-md-12" class="selectpicker">
                      @if (!$categories->isEmpty())
                          @foreach ($categories as $category)
                              <option value="{{ $category->id }}">{{ $category->name }}</option>
                          @endforeach
                      @endif
                  </x-form.selectbox>
                  <x-form.selectbox labelName="Warehouse" name="warehouse_id"  required="required" col="col-md-12" class="selectpicker">
                      @if (!$warehouses->isEmpty())
                          @foreach ($warehouses as $warehouse)
                              <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                          @endforeach
                      @endif
                  </x-form.selectbox>
                  <x-form.selectbox labelName="Account" name="account_id"  required="required" col="col-md-12" class="selectpicker">
                      @if (!$accounts->isEmpty())
                          @foreach ($accounts as $account)
                              <option value="{{ $account->id }}">{{ $account->name.' - '.$account->account_no }}</option>
                          @endforeach
                      @endif
                  </x-form.selectbox>
                    <x-form.textbox labelName="Amount" name="amount" required="required" col="col-md-12" placeholder="0"/>
                    <x-form.textarea labelName="Expense Note" name="note" required="required" col="col-md-12" placeholder="0"/>
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