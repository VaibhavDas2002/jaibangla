<style type="text/css">
    .has-error
    {
      border-color:#cc0000;
      background-color:#ffff99;
    }
    .preloader1{
      position: fixed;
      top:40%;
      left: 52%;
      z-index: 999;
    }
    .preloader1 {
      background: transparent !important;
    }
    #loadingDi {
      position:absolute;
      top:0px;
      right:0px;
      width:100%;
      height:100%;
      background-color:#fff;
      background-image:url('images/ajaxgif.gif');
      background-repeat:no-repeat;
      background-position:center;
      z-index:10000000;
      opacity: 0.4;
      filter: alpha(opacity=40); /* For IE8 and earlier */
    }
    .loadingDivModal{
      position:absolute;
      top:0px;
      right:0px;
      width:100%;
      height:100%;
      background-color:#fff;
      background-image:url('images/ajaxgif.gif');
      background-repeat:no-repeat;
      background-position:center;
      z-index:10000000;
      opacity: 0.4;
      filter: alpha(opacity=40); /* For IE8 and earlier */
    }
    #updateDiv {
      border: 1px solid #d9d9d9;
      padding: 8px;  
      box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
    }
    #name_div {
        color:#0275d8;
        font-weight: 400;
    }
    #av_name_response {
        color:#5cb85c;
        font-weight: 400;
    }
    /* #failed_reason_id{
        color:#d9534f;
        
    } */
  </style>
   @extends('layouts.app-template-form-validation')
   @section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
              Confirmation of bank account validation
            </h1>
            <ol class="breadcrumb">
              <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
            </ol>
        </section>
        <section class="content">
            <div class="box box-default">
                <div class="box-body">
                    <div id="loadingDi"></div>
                    <div class="panel panel-default">
                        <div class="panel-heading" style="font-size: 14px; font-weight: bold; font-style: italic;"><span id="panel-icon">Enter Filter Criteria</div>
                        <div class="panel-body" style="padding: 5px;">
                            <div class="row">
                                <div class="col-md-12">
                                    @if (($message = Session::get('success')) )
                                        <div class="alert alert-success alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }} </strong>
                                        </div>
                                    @endif
                                    @if (($message = Session::get('message')))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    @if (($message = Session::get('msg1')))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                            <div class="col-md-3">
                                                <label class=" control-label">Scheme <span class="text-danger">*</span></label>
                                                <select class="form-control select2" name="scheme_type" id='scheme_type' required>
                                                  <option value="">--Select Scheme--</option>
                                                  @foreach ($schemes as $scheme)
                                                  <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                                                  @endforeach
                                                </select>
                                                <span class="text-danger" id="error_scheme_type"></span>
                                            </div>
                                            @if($mapLevel=='SubdivVerifier')
                                                <div class="col-md-3">
                                                    <label class=" control-label" >Municipality</label>
                                                    <select name="filter_1" id="filter_1" class="form-control select2 full-width js-municipality" >
                                                    <option value="">-----All----</option>
                                                    @foreach ($urban_bodys as $urban_body)
                                                    <option value="{{$urban_body->urban_body_code}}" > {{$urban_body->urban_body_name}}</option>
                                                    @endforeach
                                                    </select>
                                                    <span class="text-danger" id="error_mun_type"></span>
                                                </div> 
                                                <div class="col-md-3">
                                                    <label class=" control-label" >Wards</label>
                                                    <select name="filter_2" id="filter_2" class="form-control select2 full-width js-wards" >
                                                    <option value="">-----All----</option>
                                                    </select>
                                                </div> 
                                                <input type="hidden" name="local_body" id="local_body" value={{$local_body_code}}>  
                                            @elseif($mapLevel=='BlockVerifier')
                                                <div class="col-md-3">
                                                    <label class=" control-label" >Gram Panchayat</label>
                                                    <select name="filter_1" id="filter_1" class="form-control select2 full-width" >
                                                    <option value="">-----All----</option>
                                                    @foreach ($gps as $gp)
                                                        <option value="{{$gp->gram_panchyat_code}}" > {{$gp->gram_panchyat_name}}</option>
                                                    @endforeach
                                                    </select>
                                                    <span class="text-danger" id="error_gp_type"></span>
                                                </div> 
                                                <input type="hidden" name="local_body" id="local_body" value={{$local_body_code}}>
                                            @elseif($mapLevel=='DistrictApprover')
                                                <input type="hidden" name="local_body" id="local_body" value="">
                                                
                                            @endif
                                            <input type="hidden" name="mapLevel" id="mapLevel" value={{$mapLevel}}>
                                            <input type="hidden" name="district_code" id="district_code" value="{{$district_code}}">
                                            <div class="col-md-4" style="margin-top: 24px;">
                                                <button class="btn btn-primary" name="search_btn" id="search_btn" type="button" disabled><i class="fa fa-search"></i> Search</button>&nbsp;&nbsp;&nbsp;
                                                {{-- <button class="btn btn-default" name="reset_btn" id="reset_btn" type="button" disabled><i class="fa fa-refresh"></i> Reset</button> --}}
                                                <button class="btn btn-success" name="bengla_btn" id="bengla_btn" type="button"><i class="fa fa-download"></i>বাংলা</button>
                                                <button class="btn btn-warning" name="english_btn" id="english_btn" type="button"><i class="fa fa-download"></i>English</button>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>    
                    </div>
                    <div id="res_div" style="display: none;">
                        <div class="panel panel-default">
                            <div class="panel-heading" id="panel_head" style="font-size: 14px; font-weight: bold; font-style: italic;">List of Beneficiary</div>
                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                <div class="table-responsive">
                                    <table id="example" class="table display" cellspacing="0" width="100%"> 
                                        <thead style="font-size: 12px;">
                                          <th width="5%">Beneficiary ID</th>
                                          <th width="10%">Beneficiary Name</th>
                                          <th width="10%">Mobile No</th>
                                          <th width="10%">Beneficiary Account No</th>
                                          <th width="10%">Beneficiary IFSC</th>
                                          <th width="10%">Block/Municipality Name</th>
                                          <th width="10%">GP/Ward Name</th>
                                          <th width="5%">Action</th>
                                          <th width="15%">Download Application Form</th>
                                        </thead>
                                        <tbody style="font-size: 14px;"></tbody>   
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="modal fade" id="modalUpdatebank" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 style="text-align: center;" class="text-dark" id='heading'>Confirmation of bank account validation</h4>
                    </div>
                    <div class="modal-body">
                        <div class="loadingDivModal"></div>
                        <div class="" id="updateDiv">
                            <table class="table table-bordered table-responsive table-condensed table-striped" style="font-size: 14px;">
                                <tr>
                                    <td>
                                        <strong>Beneficiary ID : </strong>
                                        <span id="application_id"></span>
                                    </td>
                                    <td>
                                        <strong>Gender: </strong>
                                        <span id="gender_div"></span>
                                    </td>    
                                </tr>
                                <tr>
                                     <td>
                                        <strong>Caste: </strong>
                                        <span id="caste_div"></span>
                                    </td> 
                                    <td>
                                        <strong>Mobile No:</strong>
                                        <span id="mobile_div"></span>
                                    </td>
                                </tr> 
                                <tr id="bank_details">
                                    <td>
                                        <strong>Existing Bank Name: </strong>
                                        <span id="exist_bank_name"></span>
                                    </td>
                                    <td>
                                        <strong>Existing Branch Name:</strong>
                                        <span id="exist_branch_name"></span>
                                    </td>
                                </tr>  
                                <tr id="ifsc_code_div">
                                    <td>
                                        <strong>Existing IFSC Name: </strong>
                                        <span id="exist_bank_ifsc"></span>
                                    </td>
                                    <td>
                                        <strong>Existing Bank Code:</strong>
                                        <span id="exist_bank_code"></span>
                                    </td>
                                </tr> 
                                <tr >
                                    <td>
                                        <strong>Beneficiary Name: </strong>
                                        <span id="name_div" ></span>
                                    </td>
                                    <td>
                                        <strong>Name Response From Bank:</strong>
                                        <span id="av_name_response"></span>
                                    </td>
                                </tr>                       
                            </table>
                            <div style="text-align: center; font-size: 16px;" class="text-danger" id='failed_reason'>
                                Failed Reason: <span id="failed_reason_id"></span>
                            </div>
                            
                            <div style="font-size:15px; font-weight: bold; font-style: italic;" class="text-warning" align="center">
                                Please select which one do you want to process?
                            </div>
                            <div style="padding: 5px 5px 5px 50px; border: 1px solid whitesmoke; border-radius: 5px; margin: 5px 0px; background-color: whitesmoke;" class="row" id='process_selection'>
                                <label style="cursor: pointer; margin-bottom: 5px;" id="radio_button">
                                    <input type="radio" name="process_type" class="process_type_radio" value="1">
                                    Bank name may be taken as Beneficiary Name as Bank Name is correct
                                </label><br>
                                 <label style="cursor: pointer; margin-bottom: 5px;">
                                    <input type="radio" name="process_type" class="process_type_radio" value="2">
                                    Passbook Correction Required.
                                </label> <br>
                                <label style="cursor: pointer; margin-bottom: 5px;">
                                    <input type="radio" name="process_type" class="process_type_radio" value="3">
                                    Bank Account is of other Family Members, New Account Number required.
                                </label>
                                <label style="cursor: pointer; margin-bottom: 5px;">
                                    <input type="radio" name="process_type" class="process_type_radio" value="4">
                                    Bank account is of completely of other person out of family. New Account Number required.
                                </label>
                            </div>
                            <span id="error_process_type" class="text-danger"></span>
                        </div>
                        <br>
                        <input type="hidden" name="pension_id" id="pension_id" value="">
                        <input type="hidden" name="update_scheme_id" id="update_scheme_id" value="">
                        <input type="hidden" name="old_bank_ifsc" id="old_bank_ifsc" value="">
                        <input type="hidden" name="old_bank_code" id="old_bank_code" value="">
                        <input type="hidden" name="pay_mode" id="pay_mode" value="">
                        <input type="hidden" name="new_bank_is_required" id="new_bank_is_required" value="" />
                        <table class="table table-bordered table-responsive table-condensed" style="width:100%; font-size: 14px;" id="update_details">
                            <tr><th colspan="4" style="text-align: center; font-size: 12px; font-weight: bold;">All mandatory fields(<span class="text-danger">*</span>)are required.</th></tr>
                            <tr id="bank_row">
                                <th>Bank IFSC Code: <span class="text-danger">*</span></th>
                                <td>
                                    <input type="text" value="" name="bank_ifsc" onkeyup="this.value = this.value.toUpperCase();" id="bank_ifsc">
                                        <img src="{{ asset('images/ajaxgif.gif') }}" width="60px" id="ifsc_loader" style="display: none;">
                                        <span id="error_bank_ifsc_code" class="text-danger">
                                </td>
                                <th>Bank Name: <span class="text-danger">*</span></th>
                                <td>
                                    <input type="text" value="" name="bank_name_new" maxlength="200" id="bank_name" readonly>
                                    <span id="error_name_of_bank" class="text-danger">
                                </td>
                            </tr>
                            <tr id="bank_name_row">
                                <th>Bank Branch Name: <span class="text-danger">*</span></th>
                                <td>
                                    <input type="text" value="" name="branch_name_new" id="branch_name" readonly>
                                    <span id="error_bank_branch" class="text-danger"></span>
                                </td>
                                <th>Bank Account Number: <span class="text-danger">*</span></th>
                                <td>
                                    <input type="text" value="" name="bank_code_new" maxlength='20' id="bank_code" onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;">
                                    <span id="error_bank_code" class="text-danger"></span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="required" style="font-size: 14px;">Scan Copy of Present Bank Passbook<span class="text-danger">*</span></th>
                                <td id="bank_passbook_text"> 
                                    <input type="file"  name="upload_bank_passbook" accept=".jpg,.jpeg,.png,.pdf" id="upload_bank_passbook" value="">
                                    <span style="font-size: 14px;" id="error_passbook_file" class="text-danger"></span>
                                </td>
                                <th scope="row" class="required" style="font-size: 14px;">Scan Copy of Present Aadhaar Card<span class="text-danger">*</span></th>
                                <td id="aadhaar_card_text"> 
                                    <input type="file"  name="upload_aadhar_card" accept=".jpg,.jpeg,.png,.pdf" id="upload_aadhar_card" value="">
                                    <span style="font-size: 14px;" id="error_aadhaar_file" class="text-danger"></span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="required" style="font-size: 14px;">Scan Copy of Application Form<span class="text-danger">*</span></th>
                                <td> 
                                    <input type="file"  name="upload_application_form" accept=".jpg,.jpeg,.png,.pdf" id="upload_application_form" value="">
                                    <span style="font-size: 14px;" id="error_application_file" class="text-danger"></span>
                                </td>
                                <th scope="row" class="required" style="font-size: 14px;">Remarks<span class="text-danger">*</span></th>
                                <td>
                                    <input type="text" value="" name="remarks" id="remarks" style="width:100%;">
                                    <span id="error_remarks" class="text-danger"></span>
                                </td>
                            </tr>
                        </table>
                        <div style="padding: 5px 5px 5px 50px; border: 1px solid whitesmoke; border-radius: 5px; margin: 5px 0px; background-color: whitesmoke;" class="row" id="av_name_msg_div">
                            <div class="col-md-12">
                                <span id="av_name_msg"></span>
                            </div>
                        </div>
                        <div class="row" id="update_btn">                
                            <div class="col-md-12" style="text-align: center;"><input type="button" name="submit" value="Update" id="verifySubmit" class="btn btn-success btn-lg"></div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>    
    </div>
   @endsection
   {{-- @section('script') --}}
   <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
    <script src="{{ URL::asset('js/confirmation_of_bank_account_validation.js') }}"></script> 
   <script src="{{ URL::asset('js/confirmation_of_bank_account_validation_bangla.js') }}"></script>

   <script>
        $(document).ready(function(){
            // confirmation_of_bank_account_validation_bangla();
            var interval = setInterval(function () {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
            }, 100);
            $('#loadingDi').hide();
            $('#update_details').hide();
            $('#aadhar_div').hide();
            $('#aadhar_row').hide();
            // $('#av_name_taken').hide();
            // $('#self_declaration').hide();
            $('#av_name_msg_div').hide();
            $('#search_btn').removeAttr('disabled');
            
            var error_scheme_type = '';
            $('#search_btn').click(function(){
                if($.trim($('#scheme_type').val()).length == 0){
                    error_scheme_type = 'Scheme name is required';
                    $('#error_scheme_type').text(error_scheme_type);
                }
                else{
                    error_scheme_type = '';
                    $('#error_scheme_type').text(error_scheme_type);
                }
                if( error_scheme_type != ''){
                    return false;
                }else{
                    $('#loadingDi').show();
                    $('#res_div').show();
                    var msg = 'Scheme : '+$( "#scheme_type option:selected" ).text();
                    $('#panel_head').text(msg);
                    if ( $.fn.DataTable.isDataTable('#example') ) {
                        $('#example').DataTable().destroy();
                    }
                    $('#example tbody').empty();
                    var table=$('#example').DataTable( {
                        dom: 'Blfrtip',
                        "scrollX": true,
                        "paging": true,
                        "searchable": true,
                        "ordering":false,
                        "bFilter": true,
                        "bInfo": true,
                        "pageLength":25,
                        'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
                        "serverSide": true,
                        "processing":true,
                        "bRetrieve": true,
                        "oLanguage": {
                            "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
                        },
                        "ajax": 
                        {
                            url: "{{ url('validation-correction-pending-listing') }}",
                            type: "post",
                            data:function(d){
                            d.scheme_id = $('#scheme_type').val(),
                            d.payment_mode = $('#failed_type').val(),
                            d.filter_1 = $('#filter_1').val(),
                            d.filter_2 = $('#filter_2').val(),
                            d.mapLevel = $('#mapLevel').val(),
                            d.local_body = $('#local_body').val(),
                            d.district_code = $('#district_code').val(),
                            d._token= "{{csrf_token()}}"
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                            $('#loadingDi').hide();
                            $('.preloader1').hide();
                            ajax_error(jqXHR, textStatus, errorThrown);
                            }
                        },
                        "initComplete":function(){
                            $('#loadingDi').hide();
                            //console.log('Data rendered successfully');
                        },
                        "columns": [
                            { "data": "ben_id" },
                            { "data": "ben_name" },
                            { "data": "mobile_no" },
                            { "data": "last_accno" },
                            { "data": "last_ifsc"},
                            { "data": "block_ulb_name"},
                            { "data": "gp_ward_name"},
                            { "data": "view" },
                            { "data": "download" },
                        ],
                        "buttons": [
                                        {
                                        extend: 'pdf',
                                        footer: true,
                                        pageSize:'A4',
                                        //orientation: 'landscape',
                                        pageMargins: [ 40, 60, 40, 60 ],
                                        exportOptions: {
                                                columns: [0,1,2,3,4,5,6],

                                            }
                                        },
                                        {
                                            extend: 'excel',
                                            footer: true,
                                            pageSize:'A4',
                                            //orientation: 'landscape',
                                            pageMargins: [ 40, 60, 40, 60 ],
                                            exportOptions: {
                                                    columns: [0,1,2,3,4,5,6],
                                                    stripHtml: false,
                                                }
                                        },
                                    ],
                        });
                    }
                    // if($('#filter_1').val() !=''){
                    //     $('#bengla_btn').removeAttr('disabled');
                    //     $('#english_btn').removeAttr('disabled');
                    // }
                });
                $('.js-municipality').change(function() {
                    municipality=$('.js-municipality').val();  
                    loadGPWard_1(municipality);
                    // console.log('on change municipality:'+municipality);   
                });
                function loadGPWard_1(municipality) {  
                    $('.js-wards').empty().append('<option value="">-- Select --</option>');   
                    loadwards1(municipality, '../api/gpward/', '.js-wards');
                }    
                $('.modalEncloseClose').click(function(){
                $('.encolser_modal').modal('hide');
                }); 
                var error_gp_type = '';
                var error_mun_type = '';
                $('#bengla_btn').click(function(){
                    if($('#mapLevel').val()=='BlockVerifier'){
                        if($.trim($('#filter_1').val()).length == 0){
                            error_gp_type = 'Gram Panchyat is required';
                            $('#error_gp_type').text(error_gp_type);
                        }
                        else{
                            error_gp_type = '';
                            $('#error_gp_type').text(error_gp_type);
                        }
                        if($.trim($('#scheme_type').val()).length == 0){
                            error_scheme_type = 'Scheme name is required';
                            $('#error_scheme_type').text(error_scheme_type);
                        }
                        else{
                            error_scheme_type = '';
                            $('#error_scheme_type').text(error_scheme_type);
                        }
                        if( error_gp_type != '' || error_scheme_type != ''){
                            return false;
                        }else{
                            var scheme_id = $('#scheme_type').val();
                            var gp_mun = $('#filter_1').val();
                            bulk_bengla_download(scheme_id,gp_mun);
                        }
                    }
                    if($('#mapLevel').val()=='SubdivVerifier'){
                        if($.trim($('#filter_1').val()).length == 0){
                            error_mun_type = 'Municipality is required';
                            $('#error_mun_type').text(error_mun_type);
                        }
                        else{
                            error_mun_type = '';
                            $('#error_mun_type').text(error_mun_type);
                        }
                        if($.trim($('#scheme_type').val()).length == 0){
                            error_scheme_type = 'Scheme name is required';
                            $('#error_scheme_type').text(error_scheme_type);
                        }
                        else{
                            error_scheme_type = '';
                            $('#error_scheme_type').text(error_scheme_type);
                        }
                        if( error_mun_type != '' || error_scheme_type != ''){
                            return false;
                        }else{
                            var scheme_id = $('#scheme_type').val();
                            var gp_mun = $('#filter_1').val();
                            bulk_bengla_download(scheme_id,gp_mun);
                        }
                    }
                });

                $('#english_btn').click(function(){
                    if($('#mapLevel').val()=='BlockVerifier'){
                        if($.trim($('#filter_1').val()).length == 0){
                            error_gp_type = 'Gram Panchyat is required';
                            $('#error_gp_type').text(error_gp_type);
                        }
                        else{
                            error_gp_type = '';
                            $('#error_gp_type').text(error_gp_type);
                        }
                        if($.trim($('#scheme_type').val()).length == 0){
                            error_scheme_type = 'Scheme name is required';
                            $('#error_scheme_type').text(error_scheme_type);
                        }
                        else{
                            error_scheme_type = '';
                            $('#error_scheme_type').text(error_scheme_type);
                        }
                        if( error_gp_type != '' || error_scheme_type != ''){
                            return false;
                        }else{
                            var scheme_id = $('#scheme_type').val();
                            var gp_mun = $('#filter_1').val();
                            bulk_english_download(scheme_id,gp_mun);
                        }
                    }
                    if($('#mapLevel').val()=='SubdivVerifier'){
                        if($.trim($('#filter_1').val()).length == 0){
                            error_mun_type = 'Municipality is required';
                            $('#error_mun_type').text(error_mun_type);
                        }
                        else{
                            error_mun_type = '';
                            $('#error_mun_type').text(error_mun_type);
                        }
                        if($.trim($('#scheme_type').val()).length == 0){
                            error_scheme_type = 'Scheme name is required';
                            $('#error_scheme_type').text(error_scheme_type);
                        }
                        else{
                            error_scheme_type = '';
                            $('#error_scheme_type').text(error_scheme_type);
                        }
                        if( error_mun_type != '' || error_scheme_type != ''){
                            return false;
                        }else{
                            var scheme_id = $('#scheme_type').val();
                            var gp_mun = $('#filter_1').val();
                            bulk_english_download(scheme_id,gp_mun);
                        }
                    }
                });

        });
        function benDownloadFunction(value, scheme_id){
            $('#loadingDi').show();
            $.ajax({
                type: 'post',
                url: "{{ route('validation-correction-form-download') }}",
                data: { scheme_id:scheme_id,  id:value, _token: '{{ csrf_token() }}' },
                success: function (response) {
                    $('#loadingDi').hide();
                    // console.log(response);
                    if (response.status == 1) {
                        $.alert({
                            title: response.title,
                            type: response.type,
                            icon: response.icon,
                            content: response.msg
                        });
                    }
                    else {
                        const data = response.data;
                          console.log(JSON.stringify(data));
                        // confirmation_of_bank_account_validation(data);
                        confirmation_of_bank_account_validation_bangla(data); 
                    }
                },
                complete: function(){
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#loadingDi').hide();
                ajax_error(jqXHR, textStatus, errorThrown); 
            }
        });
        }
        function engDownloadFunction(value, scheme_id){
            $('#loadingDi').show();
            $.ajax({
                type: 'post',
                url: "{{ route('validation-correction-form-download') }}",
                data: { scheme_id:scheme_id,  id:value, _token: '{{ csrf_token() }}' },
                success: function (response) {
                    $('#loadingDi').hide();
                    // console.log(response);
                    if (response.status == 1) {
                        $.alert({
                            title: response.title,
                            type: response.type,
                            icon: response.icon,
                            content: response.msg
                        });
                    }
                    else {
                        const data = response.data;
                        //  console.log(JSON.stringify(data));
                         confirmation_of_bank_account_validation(data);
                       
                    }
                },
                complete: function(){
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#loadingDi').hide();
                ajax_error(jqXHR, textStatus, errorThrown); 
            }
        });
        }
        function bulk_bengla_download(scheme_id,gp_mun){
            $('#loadingDi').show();
            $.ajax({
                type: 'post',
                url: "{{ route('validation-correction-form-download') }}",
                data: { scheme_id:scheme_id,  gp_mun:gp_mun, _token: '{{ csrf_token() }}' },
                success: function (response) {
                    $('#loadingDi').hide();
                    // console.log(response);
                    if (response.status == 1) {
                        $.alert({
                            title: response.title,
                            type: response.type,
                            icon: response.icon,
                            content: response.msg
                        });
                    }
                    else {
                        const data = response.data;
                        //  console.log(JSON.stringify(data));
                        // confirmation_of_bank_account_validation(data);
                        confirmation_of_bank_account_validation_bangla(data); 
                    }
                },
                complete: function(){
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#loadingDi').hide();
                ajax_error(jqXHR, textStatus, errorThrown); 
            }
        });
        }
        function bulk_english_download(scheme_id,gp_mun){
            $('#loadingDi').show();
            $.ajax({
                type: 'post',
                url: "{{ route('validation-correction-form-download') }}",
                data: { scheme_id:scheme_id,  gp_mun:gp_mun, _token: '{{ csrf_token() }}' },
                success: function (response) {
                    $('#loadingDi').hide();
                    // console.log(response);
                    if (response.status == 1) {
                        $.alert({
                            title: response.title,
                            type: response.type,
                            icon: response.icon,
                            content: response.msg
                        });
                    }
                    else {
                        const data = response.data;
                        //  console.log(JSON.stringify(data));
                        // confirmation_of_bank_account_validation(data);
                        confirmation_of_bank_account_validation(data); 
                    }
                },
                complete: function(){
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#loadingDi').hide();
                ajax_error(jqXHR, textStatus, errorThrown); 
            }
        });
        }
        function editFunction(value, scheme_id){
        $('.process_type_radio').prop('checked', false);  
        $('#update_details').hide();  
        $('#av_name_msg_div').hide();
        $('#upload_bank_passbook').val('');
        $('#upload_aadhar_card').val('');
        $('#upload_application_form').val('');
        $('#bank_ifsc').val('');
        $('#bank_code').val('');
        $('#bank_name').val('');
        $('#branch_name').val('');
        $('#remarks').val('');
        $('#loadingDi').show();
        $('#update_btn').hide();
        $.ajax({
            type: 'post',
            url: "{{ route('validation-correction-pending-view') }}",
            data: { scheme_id:scheme_id,  id:value, _token: '{{ csrf_token() }}' },
            success: function (response) {
                $('#loadingDi').hide();
                //   console.log(response);
                if (response.status == 1) {
                    $.alert({
                        title: response.title,
                        type: response.type,
                        icon: response.icon,
                        content: response.msg
                    });
                }
                else {
                     $('#name_div').text(response.ben_name);
                    // $('#father_div').text(response.father_name);
                     $('#mobile_div').text(response.mobile_no);
                     $('#dob_div').text(response.dob);
                     $('#gender_div').text(response.gender);
                     $('#av_name_response').text(response.av_name_response);
                     $('#caste_div').text(response.caste);
                     $('#update_scheme_id').val(response.scheme_id);
                     $('#pension_id').val(response.id);
                     $('#exist_bank_name').text(response.bank_name);
                     $('#exist_bank_ifsc').text(response.bank_ifsc);
                     $('#exist_bank_code').text(response.bank_code);
                     $('#exist_branch_name').text(response.branch_name);
                     $('#application_id').text(response.id);
                     $('#failed_reason_id').text(response.failed_reason);
                     $('.loadingDivModal').hide();
                     $('#modalUpdatebank').modal('show');
                     if(response.av_name_response == ''){
                        $('#radio_button').hide();
                        $('#av_name_response').text('No name response from bank');
                        $('#failed_reason').show();
                     }else{
                        $('#radio_button').show();
                        $('#failed_reason').hide();
                     }
                     $('#aadhar_no').text(response.aadhaar_no);
                     $('#av_name_msg').text(response.av_name_msg)
                     
                }
            },
            complete: function(){
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('#loadingDi').hide();
            ajax_error(jqXHR, textStatus, errorThrown); 
        }
        });
    }
    $(document).on('change', '.process_type_radio', function() {
        var process_type = $(this).val();
        if (process_type == 1) {
            $('#update_details').show();
            $('#update_btn').show();
            $('#bank_row').hide();
            $('#bank_name_row').hide();
            // $('#av_name_taken').hide();
            // $('#self_declaration').show();
            $('#av_name_msg_div').show();
            $("#new_bank_is_required").val(41);
        } else if (process_type == 2) {
            $('#update_details').show();
            $('#update_btn').show();
            $('#bank_row').hide();
            $('#bank_name_row').hide();
            // $('#av_name_taken').hide();
            // $('#self_declaration').show();
            $('#av_name_msg_div').hide();
            $("#new_bank_is_required").val(42);
        } else if (process_type == 3) {
            $('#update_details').show();
            $('#update_btn').show();
            $('#bank_row').show();
            $('#bank_name_row').show();
            // $('#av_name_taken').show();
            // $('#self_declaration').show();
            $('#av_name_msg_div').hide();
            $("#new_bank_is_required").val(43);
            $('#aadhar_div').show();
            $('#aadhar_row').show();
        } else if (process_type == 4) {
            $('#update_details').show();
            $('#update_btn').show();
            $('#bank_row').show();
            $('#bank_name_row').show();
            // $('#av_name_taken').show();
            // $('#self_declaration').show();
            $('#av_name_msg_div').hide();
            $("#new_bank_is_required").val(44);
            $('#aadhar_div').show();
            $('#aadhar_row').show();
        }else {
            // Handle default action if needed
        }
    });

    $(document).on('click', '#verifySubmit', function(){     
      var isCustomValid = customValidation();
    if (!isCustomValid) {
        return false;
    }
    else
    {
      var old_bank_ifsc=$('#old_bank_ifsc').val();
      var old_bank_accno=$('#old_bank_code').val();
      var bank_ifsc=$('#bank_ifsc').val();
      var bank_account_number=$('#bank_code').val();
      var upload_bank_passbook = $('#upload_bank_passbook')[0].files;
      var upload_aadhar_card = $('#upload_aadhar_card')[0].files;
      var upload_application_form = $('#upload_application_form')[0].files;
      $.confirm({
        type: 'orange',
        title: 'Confirmation!',
        content: 'Are you sure want to update this beneficiary ?',
        icon: 'fa fa-warning',
        buttons: {
          confirm: {
            text: 'Confirm',
            btnClass: 'btn-blue',
            keys: ['enter', 'shift'],
            action: function(){
              // alert('OK');
              var beneficiary_Id = $('#pension_id').val();
              var updateSchemeId = $('#update_scheme_id').val();
              var new_bank_ifsc = $('#bank_ifsc').val();
              var new_bank_code = $('#bank_code').val();
              var new_bank_name = $('#bank_name').val();
              var new_branch_name = $('#branch_name').val();
              var process_type = $('#new_bank_is_required').val();
              var remarks = $('#remarks').val();
              var token =  '{{csrf_token()}}';
              var fd= new  FormData();
              fd.append('id', beneficiary_Id);
              fd.append('scheme_id', updateSchemeId);
              fd.append('bank_ifsc', new_bank_ifsc);
              fd.append('bank_code', new_bank_code);
              fd.append('bank_name', new_bank_name);
              fd.append('branch_name', new_branch_name);
              fd.append('upload_bank_passbook', upload_bank_passbook[0]);
              fd.append('upload_aadhar_card',upload_aadhar_card[0]);
              fd.append('upload_application_form',upload_application_form[0]);
              fd.append('process_type', process_type);
              fd.append('remarks',remarks);
              fd.append('_token', token);
              $('.loadingDivModal').show();
              $.ajax({
                type: 'POST',
                url: "{{ route('validation-correction-pending-post') }}",
                data: fd,
                processData: false,
                contentType: false,
                success: function (response) {
                  $('.loadingDivModal').hide();
                  if (response.status == 1) {
                    $.alert({
                      title: response.title,
                      type: response.type,
                      icon: response.icon,
                      content: response.msg
                    });
                    $('#modalUpdatebank').modal('hide');
                    $('#res_div').hide();
                    $('#scheme_type').val('').trigger('change');
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                  }
                  else {
                    var html = '';
                    html += '<ul>';
                    if(Array.isArray(response.msg)){
                      $.each( response.msg, function( key, value ) {
                        html += '<li>'+value+'</li>';
                      });
                    }
                    else {
                      html = '<li>'+response.msg+'</li>';
                    }
                    html += '<ul>';
                    $.alert({
                      title: response.title,
                      type: response.type,
                      icon: response.icon,
                      content: html
                    });
                  }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                  $('.loadingDivModal').hide();
                  ajax_error(jqXHR, textStatus, errorThrown);
                }
              });
            }
          },
          cancel: function () {
          },
        }
      });
    }
  });

  $(document).on('blur', '#bank_ifsc', function(){
    $ifsc_data = $.trim($('#bank_ifsc').val());
    $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
    if($ifscRGEX.test($ifsc_data))
    {
      $('#bank_ifsc').removeClass('has-error');
      $('#error_bank_ifsc_code').text('');
      $('#ifsc_loader').show();
      $.ajax({
        type: 'POST',
        url: "{{ url('legacy/getBankDetails') }}",
        data: {
          ifsc: $ifsc_data,
          _token: '{{ csrf_token() }}',
        },
        success: function (data) {
          $('#ifsc_loader').hide();
          if (!data || data.length === 0) {
            $('#error_bank_ifsc_code').text('No data found with the IFSC');
            $('#bank_ifsc').addClass('has-error');
            return;
          }
          data = JSON.parse(data);
        // console.log(data);
          $('#bank_name').val(data.bank);
          $('#branch_name').val(data.branch);
        },
        error: function (ex) {
          $('#ifsc_loader').hide();
          $('#error_bank_ifsc_code').text('Data fetch error');
          $('#bank_ifsc').addClass('has-error');
        }
      });

    }else{
      $('#error_bank_ifsc_code').text('IFSC format invalid please check the code');
      $('#bank_ifsc').addClass('has-error');
    }
  });
  function customValidation() {
    var process_type = $('#new_bank_is_required').val();
    var error_remarks ='';
    var error_passbook_file = '';
    var error_aadhaar_file = '';
    var error_application_file = '';
    if (process_type == 3 || process_type == 4) {
      var error_name_of_bank =''; 
      var error_bank_branch =''; 
      var error_bank_code =''; 
      var error_bank_ifsc_code =''; 

        // Add your validation logic here
        if($.trim($('#bank_name').val()).length == 0)
        {
            error_name_of_bank = 'Name of Bank is required';
            $('#error_name_of_bank').text(error_name_of_bank);
            $('#bank_name').addClass('has-error');
        }
        else
        {
          error_name_of_bank = '';
          $('#error_name_of_bank').text(error_name_of_bank);
          $('#bank_name').removeClass('has-error');
        }
        if($.trim($('#branch_name').val()).length == 0)
        {
          error_bank_branch = 'Bank Branch is required';
          $('#error_bank_branch').text(error_bank_branch);
          $('#branch_name').addClass('has-error');
        }
        else
        {
          error_bank_branch = '';
          $('#error_bank_branch').text(error_bank_branch);
          $('#branch_name').removeClass('has-error');
        }
        if($.trim($('#bank_code').val()).length == 0)
        {
        error_bank_code = 'Bank Account Number is required';
        $('#error_bank_code').text(error_bank_code);
        $('#bank_code').addClass('has-error');
        }
        else
        {
        error_bank_code = '';
        $('#error_bank_code').text(error_bank_code);
        $('#bank_code').removeClass('has-error');
        }
        if($.trim($('#bank_ifsc').val()).length == 0)
        {
        error_bank_ifsc_code = 'IFS Code is required';
        $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
        $('#bank_ifsc').addClass('has-error');
        }
        else
        {
        error_bank_ifsc_code = '';
        $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
        $('#bank_ifsc').removeClass('has-error');
        }
        $ifsc_data = $.trim($('#bank_ifsc').val());
        $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
        if($ifscRGEX.test($ifsc_data))
        {
          error_bank_ifsc_code = '';
          $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
          $('#bank_ifsc').removeClass('has-error');
        }
        else{
          error_bank_ifsc_code = 'Please check IFS Code format';
          $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
          $('#bank_ifsc').addClass('has-error');    
        }

        if($.trim($('#upload_bank_passbook').val()).length == 0)
        {
            error_passbook_file = 'Document is required';
            $('#error_passbook_file').text(error_passbook_file);
            $('#upload_bank_passbook').addClass('has-error');
        }
        else
        {
            error_passbook_file = '';
          $('#error_passbook_file').text(error_passbook_file);
          $('#upload_bank_passbook').removeClass('has-error');
        }
        if($.trim($('#upload_aadhar_card').val()).length == 0)
        {
            error_aadhaar_file = 'Aadhaar Document is required';
            $('#error_aadhaar_file').text(error_aadhaar_file);
            $('#upload_aadhar_card').addClass('has-error');
        }
        else
        {
            error_aadhaar_file = '';
          $('#error_aadhaar_file').text(error_aadhaar_file);
          $('#upload_aadhar_card').removeClass('has-error');
        }

        if($.trim($('#upload_application_form').val()).length == 0)
        {
            error_application_file = 'Document is required';
            $('#error_application_file').text(error_application_file);
            $('#upload_application_form').addClass('has-error');
        }
        else
        {
            error_application_file = '';
          $('#error_application_file').text(error_application_file);
          $('#upload_application_form').removeClass('has-error');
        }

        if($.trim($('#remarks').val()).length == 0)
        {
            error_remarks = 'Remarks is required';
            $('#error_remarks').text(error_remarks);
            $('#remarks').addClass('has-error');
        }
        else
        {
            error_remarks = '';
          $('#error_remarks').text(error_remarks);
          $('#remarks').removeClass('has-error');
        }
        

       if(error_name_of_bank !='' || error_bank_branch !=''||  error_bank_code !='' || error_bank_ifsc_code !='' || error_remarks !='' || error_passbook_file !='' || error_aadhaar_file !='' || error_application_file !='') {
            return false; // Validation failed
        }
    }
    if (process_type == 1 || process_type == 2 ) {
        if($.trim($('#upload_bank_passbook').val()).length == 0)
        {
            error_passbook_file = 'Bank Passbook is required';
            $('#error_passbook_file').text(error_passbook_file);
            $('#upload_bank_passbook').addClass('has-error');
        }
        else
        {
            error_passbook_file = '';
          $('#error_passbook_file').text(error_passbook_file);
          $('#upload_bank_passbook').removeClass('has-error');
        }
        if($.trim($('#upload_aadhar_card').val()).length == 0)
        {
            error_aadhaar_file = 'Aadhaar Document is required';
            $('#error_aadhaar_file').text(error_aadhaar_file);
            $('#upload_aadhar_card').addClass('has-error');
        }
        else
        {
            error_aadhaar_file = '';
          $('#error_aadhaar_file').text(error_aadhaar_file);
          $('#upload_aadhar_card').removeClass('has-error');
        }

        if($.trim($('#upload_application_form').val()).length == 0)
        {
            error_application_file = 'Application Form is required';
            $('#error_application_file').text(error_application_file);
            $('#upload_application_form').addClass('has-error');
        }
        else
        {
            error_application_file = '';
          $('#error_application_file').text(error_application_file);
          $('#upload_application_form').removeClass('has-error');
        }

        if($.trim($('#remarks').val()).length == 0)
        {
            error_remarks = 'Remarks is required';
            $('#error_remarks').text(error_remarks);
            $('#remarks').addClass('has-error');
        }
        else
        {
            error_remarks = '';
          $('#error_remarks').text(error_remarks);
          $('#remarks').removeClass('has-error');
        }

        
        if( error_remarks !='' || error_passbook_file !='' || error_aadhaar_file !='' || error_application_file !='') {
            return false; // Validation failed
        }
    }
    return true;
}

   </script> 
   {{-- @stop --}}