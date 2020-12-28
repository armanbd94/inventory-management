<div class="modal fade" id="store_or_update_modal" tabindex="-1" role="dialog" aria-labelledby="model-1"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">

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

                        <input type="hidden" name="update_id" id="update_id" />

                        <div class="col-md-9">
                            <div class="row">
                              <x-form.textbox labelName="Name" name="name" required="required" col="col-md-6"
                                placeholder="Enter name" />
                            <x-form.selectbox labelName="Barcode Symbology" name="barcode_symbology" required="required" col="col-md-6"
                                class="selectpicker">
                                @foreach (BARCODE_SYMBOLOGY as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </x-form.selectbox>
                            <div class="form-group col-md-6 required">
                              <label for="">Barcode</label>
                              <div class="input-group">
                                <input type="text" name="code" id="code" class="form-control">
                                <div class="input-group-prepend bg-primary">
                                    <span class="input-group-text bg-primary" id="generate_barcode">
                                        <i class="fas fa-retweet text-white"></i>
                                    </span>
                                </div>
                              </div>
                              
                            </div>
                            <x-form.selectbox labelName="Brand" name="brand_id" col="col-md-6" class="selectpicker">
                                @if (!$brands->isEmpty())
                                @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->title }}</option>
                                @endforeach
                                @endif
                            </x-form.selectbox>
                            <x-form.selectbox labelName="Category" name="category_id" required="required" col="col-md-6"
                                class="selectpicker">
                                @if (!$categories->isEmpty())
                                @foreach ($categories as $catgory)
                                <option value="{{ $catgory->id }}">{{ $catgory->name }}</option>
                                @endforeach
                                @endif
                            </x-form.selectbox>
                            <x-form.selectbox labelName="Unit" name="unit_id" col="col-md-6" required="required"
                                class="selectpicker" onchange="populate_unit(this.value)">
                                @if (!$units->isEmpty())
                                @foreach ($units as $unit)
                                @if ($unit->base_unit == null)
                                <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                @endif
                                @endforeach
                                @endif
                            </x-form.selectbox>
                            
                            <x-form.selectbox labelName="Purchase Unit" name="purchase_unit_id" required="required" col="col-md-6"
                                class="selectpicker"></x-form.selectbox>
                            <x-form.selectbox labelName="Sale Unit" name="sale_unit_id" required="required" col="col-md-6"
                                class="selectpicker"></x-form.selectbox>

                                <x-form.textbox labelName="Cost" name="cost" required="required" col="col-md-6"
                                placeholder="0.00" />

                                <x-form.textbox labelName="Price" name="price" required="required" col="col-md-6"
                                placeholder="0.00" />

                                <x-form.textbox labelName="Quantity" name="qty" col="col-md-6"
                                placeholder="0.00" />

                                <x-form.textbox labelName="Alert Quantity" name="alert_qty" col="col-md-6"
                                placeholder="0.00" />


                                <div class="form-group col-md-6">
                                  <label for="">Tax</label>
                                  <select name="tax_id" id="tax_id" class="form-control selectpicker" required="required" data-live-search="true" 
                                  data-live-search-placeholder="Search">
                                    <option value="">No Tax</option>
                                    @if (!$taxes->isEmpty())
                                    @foreach ($taxes as $tax)
                                    <option value="{{ $tax->id }}">{{ $tax->name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                </div>

                                <x-form.selectbox labelName="Tax Method" name="tax_method" required="required" col="col-md-6"
                                class="selectpicker">
                                @foreach (TAX_METHOD as $key => $method)
                                <option value="{{ $key }}" {{ $key == 1 ? 'selected' : '' }}>{{ $method }}</option>
                                @endforeach
                            </x-form.selectbox>

                            <x-form.textarea labelName="Description" name="description" col="col-md-6" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group col-md-12 required">
                                <label for="image">Product Image</label>
                                <div class="col-md-12 px-0 text-center">
                                    <div id="image">

                                    </div>
                                </div>
                                <input type="hidden" name="old_image" id="old_image">
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
