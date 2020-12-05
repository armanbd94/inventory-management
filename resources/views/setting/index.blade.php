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

            </div>
            <!-- /entry header -->

            <!-- Card -->
            <div class="dt-card">

                <!-- Card Body -->
                <div class="dt-card__body">
                    <div class="dt-card__body tabs-container tabs-vertical">

                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs flex-column" role="tablist">
                          <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#general-setting" 
                            role="tab" aria-controls="general-setting" aria-selected="true">General Setting
                            </a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#mail-setting"
                             role="tab" aria-controls="mail-setting" aria-selected="true">Mail Setting
                            </a>
                          </li>
                        </ul>
                        <!-- /tab navigation -->
        
                        <!-- Tab Content -->
                        <div class="tab-content">
        
                          <!-- Tab Pane -->
                          <div id="general-setting" class="tab-pane active">
                            <div class="card-body">
                                <form id="general-form" class="col-md-12" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <x-form.textbox labelName="Title" name="title" required="required" value="{{ config('settings.title') }}"
                                        col="col-md-8" placeholder="Enter title"/>
                                        <x-form.textarea labelName="Address" name="address" required="required" value="{{ config('settings.address') }}"
                                        col="col-md-8" placeholder="Enter address"/>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="form-group col-md-6 required">
                                                    <label for="logo">Logo</label>
                                                    <div class="col-md-12 px-0 text-center">
                                                        <div id="logo">

                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="old_logo" id="old_logo" value="{{ config('settings.logo') }}">
                                                </div>
                                                <div class="form-group col-md-6 required">
                                                    <label for="logo">Favicon</label>
                                                    <div class="col-md-12 px-0 text-center">
                                                        <div id="favicon">

                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="old_favicon" id="old_favicon" value="{{ config('settings.favicon') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <x-form.textbox labelName="Currency Code" name="currency_code" required="required" value="{{ config('settings.currency_code') }}"
                                        col="col-md-8" placeholder="Enter currency code"/>

                                        <x-form.textbox labelName="Currency Symbol" name="currency_symbol" required="required" value="{{ config('settings.currency_symbol') }}"
                                        col="col-md-8" placeholder="Enter currency symbol"/>

                                        <div class="form-group col-md-8">
                                            <label for="">Currency Position</label><br>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="prefix" name="currency_position" value="prefix" class="custom-control-input"
                                                    {{ config('settings.currency_position') == 'prefix' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="prefix">prefix</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="suffix" name="currency_position" value="suffix" class="custom-control-input"
                                                {{ config('settings.currency_position') == 'suffix' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="suffix">Suffix</label>
                                            </div>
                                        </div>

                                        <x-form.selectbox labelName="Timezone" name="timezone" col="col-md-8" class="selectpicker">
                                            @foreach ($zones_array as $zone)
                                                <option value="{{ $zone['zone'] }}" {{ config('settings.timezone') == $zone['zone'] ? 'selected' : '' }}>
                                                    {{ $zone['diff_from_GMT'].' - '.$zone['zone'] }}
                                                </option>
                                            @endforeach
                                        </x-form.selectbox>
                                        <x-form.selectbox labelName="Date Format" name="date_format" col="col-md-8" class="selectpicker">
                                            @foreach ($zones_array as $zone)
                                                <option value="F j, Y" {{ config('settings.date_format') == 'F j, Y' ? 'selected' : '' }}>
                                                    {{ date('F j, Y') }}
                                                </option>
                                                <option value="M j, Y" {{ config('settings.date_format') == 'M j, Y' ? 'selected' : '' }}>
                                                    {{ date('M j, Y') }}
                                                </option>
                                                <option value="j F, Y" {{ config('settings.date_format') == 'j F, Y' ? 'selected' : '' }}>
                                                    {{ date('j F, Y') }}
                                                </option>
                                                <option value="j M, Y" {{ config('settings.date_format') == 'j M, Y' ? 'selected' : '' }}>
                                                    {{ date('j M, Y') }}
                                                </option>
                                                <option value="Y-m-d" {{ config('settings.date_format') == 'Y-m-d' ? 'selected' : '' }}>
                                                    {{ date('Y-m-d') }}
                                                </option>
                                                <option value="Y-M-d" {{ config('settings.date_format') == 'Y-M-d' ? 'selected' : '' }}>
                                                    {{ date('Y-M-d') }}
                                                </option>
                                                <option value="Y/m/d" {{ config('settings.date_format') == 'Y/m/d' ? 'selected' : '' }}>
                                                    {{ date('Y/m/d') }}
                                                </option>
                                                <option value="m/d/Y" {{ config('settings.date_format') == 'm/d/Y' ? 'selected' : '' }}>
                                                    {{ date('m/d/Y') }}
                                                </option>
                                                <option value="d/m/Y" {{ config('settings.date_format') == 'd/m/Y' ? 'selected' : '' }}>
                                                    {{ date('d/m/Y') }}
                                                </option>
                                                <option value="d.m.Y" {{ config('settings.date_format') == 'd.m.Y' ? 'selected' : '' }}>
                                                    {{ date('d.m.Y') }}
                                                </option>
                                                <option value="d-m-Y" {{ config('settings.date_format') == 'd-m-Y' ? 'selected' : '' }}>
                                                    {{ date('d-m-Y') }}
                                                </option>
                                                <option value="d-M-Y" {{ config('settings.date_format') == 'd-M-Y' ? 'selected' : '' }}>
                                                    {{ date('d-M-Y') }}
                                                </option>
                                            @endforeach
                                        </x-form.selectbox>

                                        <x-form.textbox labelName="Invoice Prefix" name="invoice_prefix" required="required" 
                                        value="{{ config('settings.invoice_prefix') }}"
                                        col="col-md-8" placeholder="Enter invoice prefix"/>

                                        <x-form.textbox labelName="Invoice Number" name="invoice_number" required="required" 
                                        value="{{ config('settings.invoice_number') }}"
                                        col="col-md-8" placeholder="Enter invoice number"/>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <button type="reset" class="btn btn-danger btn-sm">Reset</button>
                                        <button type="button" class="btn btn-primary btn-sm" id="general-save-btn" onclick="save_data('general')">Save</button>
                                    </div>
                                </form>
                            </div>
                          </div>
                          <!-- /tab pane-->
        
                          <!-- Tab Pane -->
                          <div id="mail-setting" class="tab-pane">
                            <div class="card-body">
                                <form id="mail-form" class="col-md-12" method="post">
                                    @csrf
                                    <div class="row">
                                        <x-form.selectbox labelName="Mail Driver (Protocol)" name="mail_mailer" required="required" col="col-md-8" class="selectpicker">
                                            @foreach (MAIL_MAILER as $driver)
                                                <option value="{{ $driver }}" {{ config('settings.mail_mailer') == $driver ? 'selected' : '' }}>
                                                    {{ $driver }}
                                                </option>
                                            @endforeach
                                        </x-form.selectbox>
                                        <x-form.textbox labelName="Host Name" name="mail_host" required="required" value="{{ config('settings.mail_host') }}"
                                        col="col-md-8" placeholder="Enter host name"/>
                                        <x-form.textbox labelName="Mail Address" name="mail_username" required="required" value="{{ config('settings.mail_username') }}"
                                        col="col-md-8" placeholder="Enter mail username"/>
                                        <x-form.textbox labelName="Password" name="mail_password" required="required" value="{{ config('settings.mail_password') }}"
                                        col="col-md-8" placeholder="Enter mail password"/>
                                        <x-form.textbox labelName="Mail From Name" name="mail_from_name" required="required" value="{{ config('settings.mail_from_name') }}"
                                        col="col-md-8" placeholder="Enter mail password"/>
                                        <x-form.textbox labelName="Port" name="mail_port" required="required" value="{{ config('settings.mail_port') }}"
                                        col="col-md-8" placeholder="Enter mail port"/>

                                        <x-form.selectbox labelName="Encryption" name="mail_encryption" required="required" col="col-md-8" class="selectpicker">
                                            @foreach (MAIL_ENCRYPTION as $key => $value)
                                            <option value="{{ $value }}" {{ config('settings.mail_encryption') == $value ? 'selected' : '' }}>
                                                {{ $key }}
                                            </option>
                                        @endforeach
                                        </x-form.selectbox>

                                    </div>

                                    <div class="form-group col-md-12">
                                        <button type="reset" class="btn btn-danger btn-sm">Reset</button>
                                        <button type="button" class="btn btn-primary btn-sm" id="mail-save-btn" onclick="save_data('mail')">Save</button>
                                    </div>
                                </form>
                            </div>
                          </div>
                          <!-- /tab pane-->
        
                        </div>
                        <!-- /tab content -->
        
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
<script src="js/spartan-multi-image-picker-min.js"></script>
<script>
$(document).ready(function(){
    $('#logo').spartanMultiImagePicker({
        fieldName: 'logo',
        maxCount: 1,
        rowHeight: '200px',
        groupClassName: 'col-md-12 com-sm-12 com-xs-12',
        maxFileSize: '',
        dropFileLabel: 'Drop Here',
        allowExt: 'png|jpg|jpeg',
        onExtensionErr: function(index, file){
            Swal.fire({icon:'error',title:'Oops...',text: 'Only png, jpg and jpeg file format allowed!'});
        }
    });
    $('#favicon').spartanMultiImagePicker({
        fieldName: 'favicon',
        maxCount: 1,
        rowHeight: '200px',
        groupClassName: 'col-md-12 com-sm-12 com-xs-12',
        maxFileSize: '',
        dropFileLabel: 'Drop Here',
        allowExt: 'png',
        onExtensionErr: function(index, file){
            Swal.fire({icon:'error',title:'Oops...',text: 'Only png file format allowed!'});
        }
    });

    $('input[name="logo"],input[name="favicon"]').prop('required',true);

    $('.remove-files').on('click', function(){
        $(this).parents('.col-md-12').remove();
    });

    @if(config('settings.logo'))
    $('#logo img.spartan_image_placeholder').css('display','none');
    $('#logo .spartan_remove_row').css('display','none');
    $('#logo .img_').css('display','block');
    $('#logo .img_').attr('src','{{ asset("storage/".LOGO_PATH.config("settings.logo")) }}');
    @endif

    @if(config('settings.favicon'))
    $('#favicon img.spartan_image_placeholder').css('display','none');
    $('#favicon .spartan_remove_row').css('display','none');
    $('#favicon .img_').css('display','block');
    $('#favicon .img_').attr('src','{{ asset("storage/".LOGO_PATH.config("settings.logo")) }}');
    @endif

});

function save_data(form_id) {
    let form = document.getElementById(form_id+'-form');
    let formData = new FormData(form);
    let url;
    if(form_id == 'general'){
        url = "{{ route('general.setting') }}";
    }else{
        url = "{{ route('mail.setting') }}";
    }
    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        dataType: "JSON",
        contentType: false,
        processData: false,
        cache: false,
        beforeSend: function(){
            $('#'+form_id+'-save-btn').addClass('kt-spinner kt-spinner--md kt-spinner--light');
        },
        complete: function(){
            $('#'+form_id+'-save-btn').removeClass('kt-spinner kt-spinner--md kt-spinner--light');
        },
        success: function (data) {
            $('#'+form_id+'-form').find('.is-invalid').removeClass('is-invalid');
            $('#'+form_id+'-form').find('.error').remove();
            if (data.status == false) {
                $.each(data.errors, function (key, value) {
                    $('#'+form_id+'-form input#' + key).addClass('is-invalid');
                    $('#'+form_id+'-form textarea#' + key).addClass('is-invalid');
                    $('#'+form_id+'-form select#' + key).parent().addClass('is-invalid');
                $('#'+form_id+'-form #' + key).parent().append(
                    '<small class="error text-danger">' + value + '</small>');
                });
            } else {
                notification(data.status, data.message);
            }
        },
        error: function (xhr, ajaxOption, thrownError) {
            console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
        }
    });
}
</script>
@endpush