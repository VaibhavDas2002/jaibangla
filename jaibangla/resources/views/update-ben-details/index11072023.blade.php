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
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
  <div class="content-wrapper">
    <!-- <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" width="150px" id="loader_img"></div> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Update Beneficiary
      </h1>
      <ol class="breadcrumb">
        <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
      </ol>
    </section>
    <section class="content">
      <div class="box box-default">
        <div class="box-body">
          <div id="loadingDiv"></div>
          <div class="panel panel-default">
            <div class="panel-heading"><span id="panel-icon">Enter Beneficiary Details</div>
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
                  <!-- <form class="form-horizontal" role="form" method="POST" action="{{ route('search-by-name') }}" id="submit_form"> -->
                    {{csrf_field()}}
                    <div class="row">
                      <div class="col-md-12">
                        <div class="col-md-3">
                          <label class=" control-label">Search Using Id/Name <span class="text-danger">*</span></label>
                          <select class="form-control select2" name="select_type" id='select_type' required>
                            <option value="">--Select--</option>
                            <option value="b_id">Beneficiary Id</option>
                            <option value="b_name">Beneficiary Name</option>
                          </select>
                          <span class="text-danger" id="error_select_type"></span>
                        </div>
                        <div class="col-md-3" id="fname_div" style="display: none;">
                          <label class=" control-label">First Name <span class="text-danger">*</span></label>
                          <input type="text" name="ben_fname" id="ben_fname" class="form-control">
                          <span class="text-danger" id="error_ben_fname"></span>
                        </div>
                        <div class="col-md-3" id="mname_div" style="display: none;">
                          <label class=" control-label">Middle Name</label>
                          <input type="text" name="ben_mname" id="ben_mname" class="form-control">
                          <span class="text-danger" id="error_ben_mname"></span>
                        </div>
                        <div class="col-md-3" id="lname_div" style="display: none;">
                          <label class=" control-label">Last Name</label>
                          <input type="text" name="ben_lname" id="ben_lname" class="form-control">
                          <span class="text-danger" id="error_ben_lname"></span>
                        </div>
                        <div class="col-md-3" id="benid_div" style="display: none;">
                          <label class=" control-label">Beneficiary Id<span class="text-danger">*</span></label>
                          <input type="text" name="bene_id" id="bene_id" class="form-control" onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;">
                          <span class="text-danger" id="error_ben_id"></span>
                        </div>
                        <div class="col-md-3">
                          <label class=" control-label">Scheme <span class="text-danger">*</span></label>
                          <select class="form-control select2" name="scheme_type" id='scheme_type' required>
                            <option value="">--Select Scheme--</option>
                            @foreach ($schemes as $scheme)
                            <option value="{{$scheme->Scheme->id}}">{{$scheme->Scheme->scheme_name}}</option>
                            @endforeach
                          </select>
                          <span class="text-danger" id="error_scheme_type"></span>
                        </div>
                        {{-- <div class="col-md-3">
                          <label class=" control-label">Rural/Urban</label>
                          <select name="is_rural_urban" id="is_rural_urban" class="form-control select2 client-js-urban">
                            <option value="">--Select Rural/Urban--</option>
                            <option value="2">Rural</option>
                            <option value="1">Urban</option>
                          </select>
                          <span class="text-danger" id="error_is_rural_urban"></span>
                        </div>
                        <div class="col-md-3">
                          <label class=" control-label">Block/Municipality</label>
                          <select name="block_ulb" id="block_ulb" class="form-control select2 client-js-localbody">
                            <option value="">--Select Block/Municipality--</option>                
                          </select>
                          <span class="text-danger" id="error_block_ulb"></span>
                        </div> --}}
                    
                      </div>
                    </div>
                    <br/>
                    <div class="row">
                      <div class="col-md-12" align="center">
                        <button class="btn btn-primary" id="submit_btn" type="button" style="width: 200px;" disabled><i class="fa fa-search"></i> Search</button>
                      </div>
                    </div>
                  <!-- </form> -->
                </div>
              </div>
            </div>
          </div>
          
          <div id="res_div" style="display: none;">
            <div class="panel panel-default">
              <div class="panel-heading" id="panel_head">List of Beneficiary</div>
              <div class="panel-body" style="padding: 5px; font-size: 14px;">
                <div class="table-responsive">
                  <table id="example" class="table display" cellspacing="0" width="100%"> 
                    <thead style="font-size: 12px;">
                      <th width="5%">Beneficiary ID</th>
                      <th width="10%">Beneficiary Name</th>
                      <th width="10%">Father Name</th>
                      <th width="10%">Block/ULB</th> 
                      <th width="10%">Voter ID Card</th>
                      <th width="10%">Ration Card</th>
                      <th width="20%">Bank Details</th>
                      <th width="20%">Edit(Select which do you want to update ?)</th>
                      <th width="5%">Action</th>
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
    <!-- /.content -->

    <!-- Modal -->
    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Resume Beneficiary Payment</h4>
            </div>
            <div class="modal-body">
              <!-- <form method="POST" action="{{ url('resume-ben-payment') }}" id="resume_form"> -->
                <!-- {{csrf_field()}} -->
                <div class="loadingDivModal"></div>
                <input type="hidden" name="ben_id" id="resume_ben_id" value="">
                <input type="hidden" name="lot_generate_no" id="lot_generate_no" value="">
                <input type="hidden" name="schemeIdResume" id="schemeIdResume" value="">
                <div class="form-group">
                    <div style="font-size: 15px; font-weight: bold; font-style: italic; text-align: right;" id="modify_div_display" class="text-danger">This beneficiary under RBI modification</div>
                </div>

                  <div class="form-group">
                      <label for="resume_month">From which month you want to resume ?</label>
                      <select class="form-control" id="resume_month" name="resume_month" required>
                          <option value="">--Select month--</option>
                          @php $month = date("Y-m-d"); @endphp
                          <option value='@php print date("ym", strtotime("$month -1 month")); @endphp'>@php print date("F-Y", strtotime("$month +0 month")); @endphp</option>
                          <option value='@php print date("ym", strtotime("$month +0 month")); @endphp'>@php print date("F-Y", strtotime("$month +1 month")); @endphp</option>
                          <option value='@php print date("ym", strtotime("$month +1 month")); @endphp'>@php print date("F-Y", strtotime("$month +2 month")); @endphp</option>
                      </select>
                      <span class="text-danger" id="error_resume_month"></span>
                  </div>
                  <div class="form-group" align="center">
                      <button class="btn btn-success btn-lg" type="button" id="final_resume_button">Resume</button>
                  </div>
              <!-- </form> -->
            </div>
            <!-- <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary"><i class="fa fa-print"></i> Resume</button>
            </div> -->
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Update Bank Details Modal -->
    <!-- Modal -->
    <div class="modal fade" id="modalUpdatebank" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Update Bank Details ( Beneficiary ID: <span id="ben_id"></span> )</h4>
            </div>
            <div class="modal-body">
              <div class="loadingDivModal"></div>
              <div class="panel panel-default">
                <div class="panel-heading">Update Mobile No. and Bank Details</div>
                <div class="panel-body">
                  <table class="table table-bordered table-responsive table-condensed table-striped" style="font-size: 14px;">
                    <tr>
                      <td>
                        <strong>Name : </strong>
                        <span id="name_div"></span>
                      </td>
                      <td>
                        <strong>Gender: </strong>
                        <span id="gender_div"></span>
                      </td>    
                    </tr>
                    <tr>
                      <td>
                        <strong>DOB (DD-MM-YYYY): </strong>
                        <span id="dob_div"></span>  
                      </td>
                      <td>
                        <strong>Father's Name :</strong>
                        <span id="father_div"></span>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <strong>Caste:</strong>
                        <span id="caste_div"></span>
                      </td>
                      <td></td>
                    </tr>                       
                  </div>
                  <input type="hidden" name="pension_id" id="pension_id" value="">
                  <input type="hidden" name="update_scheme_id" id="update_scheme_id" value="">
                  <input type="hidden" name="old_bank_ifsc" id="old_bank_ifsc" value="">
                  <input type="hidden" name="old_bank_code" id="old_bank_code" value="">
                  <table class="table table-bordered table-responsive table-condensed" style="width:100%; font-size: 14px;" >
                    <tr>
                      <th>Mobile Number: <span class="text-danger">*</span></th>
                      <td>
                        <input type="text" value="" name="mobile_no" maxlength="10" id="mobile_no"> 
                        <span id="error_mobile_no" class="text-danger"></span>
                      </td>
                      <th>Bank IFSC Code: <span class="text-danger">*</span></th>
                      <td>
                        <input type="text" value="" name="bank_ifsc" onkeyup="this.value = this.value.toUpperCase();" id="bank_ifsc">
                        <img src="{{ asset('images/ajaxgif.gif') }}" width="60px" id="ifsc_loader" style="display: none;">
                        <span id="error_bank_ifsc_code" class="text-danger">
                      </td>
                    </tr>
                    <tr>
                      <th>Bank Name: <span class="text-danger">*</span></th>
                      <td>
                        <input type="text" value="" name="bank_name" maxlength="200" id="bank_name" readonly>
                        <span id="error_name_of_bank" class="text-danger">
                      </td>
                      <th>Bank Branch Name: <span class="text-danger">*</span></th>
                      <td>
                        <input type="text" value="" name="branch_name" id="branch_name" readonly>
                        <span id="error_bank_branch" class="text-danger"></span>
                      </td>
                    </tr>
                    <tr>
                      <th>Bank Account Number: <span class="text-danger">*</span></th>
                      <td>
                        <input type="text" value="" name="bank_code" maxlength='20' id="bank_code">
                        <span id="error_bank_code" class="text-danger"></span>
                      </td>
                      <th></th>
                      <td></td>
                    </tr>
                    <tr>
                      <th>Remarks <span class="text-danger">*</span></th>
                      <td colspan="3">
                        <input type="text" name="remarks" id="remarks" class="form-control"  maxlength="200">
                        <span id="error_remarks" class="text-danger"></span>
                      </td>  
                    </tr>
                  </table>
                  <div class="row">                
                    <div class="col-md-12" style="text-align: center;"><input type="button" name="submit" value="Update" id="verifySubmit" class="btn btn-success btn-lg"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Stop Payment Modal -->
    <!-- Modal -->
    <div class="modal fade" id="modalStopPayment" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">De-activate Beneficiary</h4>
            </div>
            <div class="modal-body">
              <div class="loadingDivModal"></div>
              <div class="panel panel-default">
                <div class="panel-heading">Enter Stop Payment Details</div>
                <div class="panel-body">
                  <div id="personal_info"></div>
                  <input type="hidden" name="benId" id="benId" value="">
                  <input type="hidden" name="stop_scheme_id" id="stop_scheme_id" value="">
                  <table class="table table-bordered table-responsive table-condensed" style="width:100%; font-size: 14px;" >
                    <tr>
                      <th>Document Upload <span class="text-danger">*</span></th>
                      <td>
                        <input type="file" name="file_stop_payment" class="form-control" id="file_stop_payment" accept=".pdf,.jpg,.png,.jpeg">
                        <small class="text-info" style="font-weight: normal;"> (Only jpeg,jpg,png,pdf file and maximum size should be less than 500 KB)</small>
                        <span class="text-danger" id="error_file" style="font-size: 12px; font-weight: bold;"></span> 
                      </td>
                      <th>Select Reason/ Documents for Stop Payment <span class="text-danger">*</span></th>
                      <td>
                        <select class="form-control" name="stop_payment_reason" id="stop_payment_reason" class="form-control">
                        </select>
                        <span id="error_reason" class="text-danger">
                      </td>
                    </tr>
                    <tr>
                      <th>Remarks <span class="text-danger">*</span></th>
                      <td colspan="3">
                        <input type="text" name="stop_remarks" id="stop_remarks" class="form-control"  maxlength="200">
                        <span id="error_stop_remarks" class="text-danger"></span>
                      </td>  
                    </tr>
                  </table>
                  <div class="row">                
                    <div class="col-md-12" style="text-align: center;"><input type="button" name="submit" value="Update" id="stopSubmit" class="btn btn-success btn-lg"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Stop Payment Modal -->
    <!-- Modal -->
    <div class="modal fade" id="modalUpdateMobile" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Update Mobile Number</h4>
            </div>
            <div class="modal-body">
              <div class="loadingDivModal"></div>
              <div class="panel panel-default">
                <div class="panel-heading">Enter Mobile Number</div>
                <div class="panel-body">
                  <div id="mobile_personal_info"></div>
                  <input type="hidden" name="mobilebenId" id="mobilebenId" value="">
                  <input type="hidden" name="old_mobile_no_update" id="old_mobile_no_update" value="">
                  <input type="hidden" name="mobile_scheme_id" id="mobile_scheme_id" value="">
                  <table class="table table-bordered table-responsive table-condensed" style="width:100%; font-size: 14px;" >
                    <tr>
                      <th width="25%">Mobile Number: <span class="text-danger">*</span></th>
                      <td width="75%">
                        <input type="text" value="" name="mobile_no_update" maxlength="10" id="mobile_no_update" class="form-control" onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;"> 
                        <span id="error_mobile_no_update" class="text-danger"></span>
                      </td>
                    </tr>
                    <tr>
                      <th width="25%">Remarks <span class="text-danger">*</span></th>
                      <td width="75%">
                        <input type="text" name="mobile_remarks" id="mobile_remarks" class="form-control"  maxlength="200">
                        <span id="error_mobile_remarks" class="text-danger"></span>
                      </td>  
                    </tr>
                  </table>
                  <div class="row">                
                    <div class="col-md-12" style="text-align: center;"><input type="button" name="submit" value="Update" id="mobileSubmit" class="btn btn-success btn-lg"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

  </div>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
  $(document).ready(function(){
    // Live Clock
    var interval = setInterval(function () {
    var momentNow = moment();
      $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
      $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);

    $('#loadingDiv').hide();
    $('#submit_btn').removeAttr('disabled');
    $('#fname_div').hide();
    $('#mname_div').hide();
    $('#lname_div').hide();
    $('#benid_div').hide();
    $('#res_div').hide();

    $('#select_type').change(function(){
      if ($('#select_type').val() == 'b_id') {
        $('#fname_div').hide();
        $('#mname_div').hide();
        $('#lname_div').hide();
        $('#benid_div').show();
      }
      else if ($('#select_type').val() == 'b_name') {
        $('#fname_div').show();
        $('#mname_div').show();
        $('#lname_div').show();
        $('#benid_div').hide();
      }
      else {
        $('#fname_div').hide();
        $('#mname_div').hide();
        $('#lname_div').hide();
        $('#benid_div').hide();
      }
    });

    var error_select_type = '';
    var error_ben_fname = '';
    var error_ben_mname = '';
    var error_ben_lname = '';
    var error_ben_id = '';
    var error_scheme_type = '';
    var error_is_rural_urban = '';
    var error_block_ulb = '';
    $('#submit_btn').click(function(){
      if($.trim($('#select_type').val()).length == 0){
        error_select_type = 'Select type is required';
        $('#error_select_type').text(error_select_type);
      }
      else{
        error_select_type = '';
        $('#error_select_type').text(error_select_type);
      }

      if($.trim($('#scheme_type').val()).length == 0){
        error_scheme_type = 'Scheme name is required';
        $('#error_scheme_type').text(error_scheme_type);
      }
      else{
        error_scheme_type = '';
        $('#error_scheme_type').text(error_scheme_type);
      }

      /*if($.trim($('#is_rural_urban').val()).length == 0){
        error_is_rural_urban = 'Select rural/urban is required';
        $('#error_is_rural_urban').text(error_is_rural_urban);
      }
      else{
        error_is_rural_urban = '';
        $('#error_is_rural_urban').text(error_is_rural_urban);
      }

      if($.trim($('#block_ulb').val()).length == 0){
        error_block_ulb = 'Select block/municipality is required';
        $('#error_block_ulb').text(error_block_ulb);
      }
      else{
        error_block_ulb = '';
        $('#error_block_ulb').text(error_block_ulb);
      }*/

      if ($('#select_type').val() == 'b_id') {
        if($.trim($('#bene_id').val()).length == 0){
          error_ben_id = 'Beneficiary id is required';
          $('#error_ben_id').text(error_ben_id);
        }
        else{
          error_ben_id = '';
          $('#error_ben_id').text(error_ben_id);
        }
      }
      if ($('#select_type').val() == 'b_name') {
        if($.trim($('#ben_fname').val()).length == 0){
          error_ben_fname = 'Beneficiary first name is required';
          $('#error_ben_fname').text(error_ben_fname);
        }
        else{
          error_ben_fname = '';
          $('#error_ben_fname').text(error_ben_fname);
        }
      }

      if( error_select_type != '' || error_scheme_type != '' /*|| error_is_rural_urban !='' || error_block_ulb !='' */|| error_ben_id != '' || error_ben_fname != ''){
        return false;
      }
      else{
        $('#loadingDiv').show();
        $('#res_div').show();

        if ( $.fn.DataTable.isDataTable('#example') ) {
          $('#example').DataTable().destroy();
        }
        var table=$('#example').DataTable( {
          dom: 'Bfrtip',
          "scrollX": true,
          "paging": false,
          "searchable": false,
          "ordering":false,
          "bFilter": false,
          "bInfo": false,
          "pageLength":30,
          'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          // "oLanguage": {
          //   "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="150px"></div>'
          // },
          "ajax": 
          {
            url: "{{ url('search-using-id-name') }}",
            type: "post",
            data:function(d){
              d.select_type= $('#select_type').val(),
              d.scheme_id=$('#scheme_type').val(),
              d.ben_id = $('#bene_id').val(),
              d.ben_fname = $('#ben_fname').val(),
              d.ben_mname = $('#ben_mname').val(),
              d.ben_lname = $('#ben_lname').val(),
              d.is_rural_urban = $('#is_rural_urban').val(),
              d.block_ulb = $('#block_ulb').val(),
              d._token= "{{csrf_token()}}"
            },
            error: function (jqXHR, textStatus, errorThrown) {
              $('#loadingDiv').hide();
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete":function(){
            $('#loadingDiv').hide();
            //console.log('Data rendered successfully');
          },
          "columns": [
            { "data": "id"},
            { "data": "ben_name"},
            { "data": "f_name"},
            { "data": "block_ulb_name"},
            { "data": "epic_voter_id"},
            { "data": "ration_card"},
            { "data": "bank_details"},
            { "data": "edit"},
            { "data": "action"},
          ],
      
          "buttons": [
            //'pdf','excel','print'
          ],
        });
      }
    });

    $('.client-js-urban').change(function() {
      @php
      $getDistCode=\App\Configduty::where('user_id',Auth::user()->id)->value('district_code');

      @endphp
      select_district_code= "{{$getDistCode}}";
      select_body_type= $('.client-js-urban').val();
      var htmlOption='<option value="">Loading...</option>';
      $('.client-js-localbody').empty().append(htmlOption);
      loadItems10(select_district_code, select_body_type, 'api/ruralurban/', '.client-js-localbody');
    });

    $(document).on('click', '.pause_button', function() {
      var val = $(this).val();
      // console.log(val);
      var arr = val.split('_');
      var benId = arr[0];
      var schemeIdLpp = arr[1];
      $.confirm({
        title: 'Confirm!',
        type: 'orange',
        icon: 'fa fa-warning',
        content: '<strong>Are you want to pause this beneficiary ?</strong>',
        buttons: {
          confirm: function () {
            $('#loadingDiv').show();
            $.ajax({
              type: 'post',
              url: "{{ route('lppPausePaymentDetails') }}",
              data: { id:benId, scheme_id:schemeIdLpp, _token: '{{ csrf_token() }}' },
              success: function (response) {
                $('#loadingDiv').hide();
                // console.log(response);
                if (response.status == 1) {
                  $.alert({
                    title: response.title,
                    type: response.type,
                    icon: response.icon,
                    content: response.msg
                  });
                  $('#res_div').hide();
                  $('#select_type').val('').trigger('change');
                  $('#scheme_type').val('').trigger('change');
                  $("html, body").animate({ scrollTop: 0 }, "slow");
                }
                else {
                  $.alert({
                    title: response.title,
                    type: response.type,
                    icon: response.icon,
                    content: response.msg
                  });
                }
              },
              complete: function(){
              },
              error: function (jqXHR, textStatus, errorThrown) {
                $('#loadingDiv').hide();
                ajax_error(jqXHR, textStatus, errorThrown); 
              }
            });
          },
          cancel: function () {
          }
        }
      });
    });

    $(document).on('click', '.resume_button', function() {
      var val = $(this).val();
      console.log(val);
      $('#show_month').text('');
      $('#modal-default').modal('show');
      $('.loadingDivModal').hide();
      var arr = val.split('_');
      $('#resume_ben_id').val(arr[0]);
      $('#lot_generate_no').val(arr[1]);
      $('#schemeIdResume').val(arr[2]);
      if (arr[1] == -1) {
          document.getElementById('modify_div_display').style.display = '';
          $('#modify_div_display').text('*This beneficiary under IFMS modification');
      }
      else if (arr[1] == -2) {
          document.getElementById('modify_div_display').style.display = '';
          $('#modify_div_display').text('*This beneficiary under RBI modification');
      }
      else if (arr[1] == -3) {
          document.getElementById('modify_div_display').style.display = '';
          $('#modify_div_display').text('*This beneficiary under SBI modification');
      }
      else{
          document.getElementById('modify_div_display').style.display = 'none';
          $('#modify_div_display').text('');   
      }  
    });

    var error_resume_month='';
    $(document).on('click', '#final_resume_button', function(e) {
      e.preventDefault();
      if ($('#resume_ben_id').val() != '' && $('#lot_generate_no').val() != '' && $('#schemeIdResume').val() != '') {
        if($('#resume_month').val() == ''){
          //alert('Month Required');
          style="border-color:#cc0000; background-color:#ffff99;"
          $('#resume_month').css({'border-color':'#cc0000','background-color':'#ffff99'});
          $('#error_resume_month').text('Please select month');
        }
        else {
          $('#resume_month').removeAttr('style');
          $('#error_resume_month').text('');
          $.confirm({
            title: 'Confirm!',
            type: 'orange',
            icon: 'fa fa-warning',
            content: '<strong>Are you want to resume this beneficiary ?</strong>',
            buttons: {
              confirm: function () {
                $('.loadingDivModal').show();
                var Lppid = $('#resume_ben_id').val();
                var lotGene = $('#lot_generate_no').val();
                var resumeScheme = $('#schemeIdResume').val();
                var resumeMonth = $('#resume_month').val();
                // $('#resume_form').submit();
                $.ajax({
                  type: 'post',
                  url: "{{ route('lppResumePaymentDetails') }}",
                  data: { ben_id:Lppid, scheme_id:resumeScheme, lot_generate_no:lotGene, resume_month:resumeMonth, _token: '{{ csrf_token() }}' },
                  success: function (response) {
                    $('.loadingDivModal').hide();
                    // console.log(response);
                    if (response.status == 1) {
                      $.alert({
                        title: response.title,
                        type: response.type,
                        icon: response.icon,
                        content: response.msg
                      });
                      $('#modal-default').modal('hide');
                      $('#res_div').hide();
                      $('#select_type').val('').trigger('change');
                      $('#scheme_type').val('').trigger('change');
                      $("html, body").animate({ scrollTop: 0 }, "slow");
                    }
                    else {
                      $.alert({
                        title: response.title,
                        type: response.type,
                        icon: response.icon,
                        content: response.msg
                      });
                    }
                  },
                  complete: function(){
                  },
                  error: function (jqXHR, textStatus, errorThrown) {
                    $('.loadingDivModal').hide();
                    ajax_error(jqXHR, textStatus, errorThrown); 
                  }
                });
              },
              cancel: function () {
              }
            }
          });
        }
      }
      else {
        $('#modal-default').modal('hide');
        $.confirm({
          title: 'Alert!!',
          type: 'red',
          icon: 'fa fa-warning',
          content: '<strong>Something went wrong!!</strong>'
        });
      }
    });

    $('#file_stop_payment').change(function(){
      var card_file=document.getElementById("file_stop_payment");
      if(card_file.value!="")
      {
        var attachment;
        attachment = card_file.files[0];
        // console.log(attachment.type)
        var type = attachment.type;
        if(attachment.size>512000)
        {
          document.getElementById("error_file").innerHTML="<i class='fa fa-warning'></i> Unaccepted document file size. Max size 500 KB. Please try again";
          $('#file_stop_payment').val('');
          return false;
        }
        else if (type != 'image/jpeg' && type != 'image/png' && type != 'application/pdf') {
          document.getElementById("error_file").innerHTML="<i class='fa fa-warning'></i> Unaccepted document file format. Only jpeg,jpg,png and pdf. Please try again";
          $('#file_stop_payment').val('');
          return false;
        }
        else{
          $('#file_upload_btn').show();
          document.getElementById("error_file").innerHTML="";
        }
      }
    });

  });

  function editFunction(value, scheme_id){
    //alert(value);
    if ($('#select_item_update_'+value).val() == '') {
      $.alert({
        title: 'Alert!!',
        type: 'red',
        icon: 'fa fa-warning',
        content: '<strong>Please select option which one do you want to update?</strong>'
      });
    }
    else {
      // $('#myForm_'+value).submit();
      var op = $('#select_item_update_'+value ).val();
      if (op == 'bank') {
        $('#loadingDiv').show();
        $.ajax({
          type: 'post',
          url: "{{ route('getModalDataUpdateStop') }}",
          data: { op_type:op, scheme_id:scheme_id, id:value, _token: '{{ csrf_token() }}' },
          success: function (response) {
            $('#loadingDiv').hide();
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
              $('#name_div').text(response.ben_name);
              $('#father_div').text(response.father_name);
              $('#dob_div').text(response.dob);
              $('#gender_div').text(response.gender);
              $('#caste_div').text(response.caste);
              $('#ben_id').text(response.id);
              $('#update_scheme_id').val(response.scheme_id);
              $('#pension_id').val(response.id);
              $('#mobile_no').val(response.mobile_no);
              $('#bank_name').val(response.bank_name);
              $('#bank_ifsc').val(response.bank_ifsc);
              $('#bank_code').val(response.bank_code);
              $('#branch_name').val(response.branch_name);
              $('#old_bank_ifsc').val(response.bank_ifsc);
              $('#old_bank_code').val(response.bank_code);
              $('.loadingDivModal').hide();
              $('#modalUpdatebank').modal('show');
            }
          },
          complete: function(){
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $('#loadingDiv').hide();
            ajax_error(jqXHR, textStatus, errorThrown); 
          }
        });
      }
      else if (op == 'stop_payment') {
        $('#loadingDiv').show();
        $.ajax({
          type: 'post',
          url: "{{ route('getModalDataUpdateStop') }}",
          data: { op_type:op, id:value, scheme_id:scheme_id, _token: '{{ csrf_token() }}' },
          success: function (response) {
            $('#loadingDiv').hide();
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
              $('#stop_remarks').val('');
              $('#stop_payment_reason').html('<option value="">-- Select --</option>');
              for (var  i = 0; i < response.doc_type.length; i++) {
                $('#stop_payment_reason').append($('<option>', {
                  value: response.doc_type[i].id,
                  text: response.doc_type[i].doc_name
                }),'</option>');
              }
              $('#personal_info').html('<table class="table table-bordered table-condensed table-responsive table-striped" style="font-size: 14px;"><tr><td><b>Beneficiary ID: </b>'+response.id+'</td><td><b>Name: </b>'+response.ben_name+'</td></tr><tr><td><b>Father Name: </b>'+response.father_name+'</td><td><b>DOB (DD-MM-YYYY): </b>'+response.dob+'</td></tr><tr><td><b>Gender: </b>'+response.gender+'</td><td><b>Caste: </b>'+response.caste+'</td></tr></table>');
              $('#benId').val(response.id);
              $('#stop_scheme_id').val(response.scheme_id);
              $('#file_stop_payment').val('');
              $('.loadingDivModal').hide();
              $('#modalStopPayment').modal('show');
            }
          },
          complete: function(){
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $('#loadingDiv').hide();
            ajax_error(jqXHR, textStatus, errorThrown); 
          }
        });
      }
      else if (op == 'update_mobile') {
        $('#loadingDiv').show();
        $.ajax({
          type: 'post',
          url: "{{ route('getModalDataUpdateStop') }}",
          data: { op_type:op, scheme_id:scheme_id, id:value, _token: '{{ csrf_token() }}' },
          success: function (response) {
            $('#loadingDiv').hide();
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
              $('#mobile_remarks').val('');
              $('#mobile_personal_info').html('<table class="table table-bordered table-condensed table-responsive table-striped" style="font-size: 14px;"><tr><td><b>Beneficiary ID: </b>'+response.id+'</td><td><b>Name: </b>'+response.ben_name+'</td></tr><tr><td><b>Father Name: </b>'+response.father_name+'</td><td><b>DOB (DD-MM-YYYY): </b>'+response.dob+'</td></tr><tr><td><b>Gender: </b>'+response.gender+'</td><td><b>Caste: </b>'+response.caste+'</td></tr></table>');
              $('#mobilebenId').val(response.id);
              $('#mobile_scheme_id').val(response.scheme_id);
              $('#mobile_no_update').val(response.mobile_no);
              $('#old_mobile_no_update').val(response.mobile_no);
              $('.loadingDivModal').hide();
              $('#modalUpdateMobile').modal('show');
            }
          },
          complete: function(){
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $('#loadingDiv').hide();
            ajax_error(jqXHR, textStatus, errorThrown); 
          }
        });
      }
    }
  }

  $(document).on('click', '#verifySubmit', function(){     
    var error_name_of_bank =''; 
    var error_bank_branch =''; 
    var error_bank_code =''; 
    var error_bank_ifsc_code =''; 
    var error_mobile_no ='';
    var error_remarks = '';

    if($.trim($('#mobile_no').val()).length == 0)
    {
     error_mobile_no = 'Mobile Number is required';
     $('#error_mobile_no').text(error_mobile_no);
     $('#mobile_no').addClass('has-error');
    }
    else if($.trim($('#mobile_no').val()).length !=10)
    {
     error_mobile_no = 'Mobile Number must be 10 digit';
     $('#error_mobile_no').text(error_mobile_no);
     $('#mobile_no').addClass('has-error');
    }
    else
    {
     error_mobile_no = '';
     $('#error_mobile_no').text(error_mobile_no);
     $('#mobile_no').removeClass('has-error');
    } 

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

    
    if(error_mobile_no != '' || error_name_of_bank !='' || error_bank_branch !=''||  error_bank_code !='' || error_bank_ifsc_code !='' || error_remarks != '') {
      return false;
    }
    else
    {
      var new_ifsc = $('#bank_ifsc').val();
      var new_acc = $('#bank_code').val();
      var old_ifsc = $('#old_bank_ifsc').val();
      var old_acc = $('#old_bank_code').val();
      if ((new_acc == old_acc) && (new_ifsc == old_ifsc)) {
        $.alert({
            title : 'Alert',
            type : 'red',
            icon : 'fa fa-warning',
            content : 'Bank A/c no same as previous one. Please enter new A/c no.'
        });
        return false;
      }
      else {
        // alert('OK');
        var beneficiary_Id = $('#pension_id').val();
        var updateSchemeId = $('#update_scheme_id').val();
        var new_bank_ifsc = $('#bank_ifsc').val();
        var new_bank_code = $('#bank_code').val();
        var new_bank_name = $('#bank_name').val();
        var new_branch_name = $('#branch_name').val();
        var new_mobile_no = $('#mobile_no').val();
        var remarks = $('#remarks').val();
        $('.loadingDivModal').show();
        $.ajax({
          type: 'POST',
          url: "{{ route('updateBenBankDetails') }}",
          data: {
            id: beneficiary_Id,
            scheme_id: updateSchemeId,
            bank_name: new_bank_name,
            branch_name: new_branch_name,
            bank_ifsc: new_bank_ifsc,
            bank_code: new_bank_code,
            mobile_no: new_mobile_no,
            remarks: remarks,
            _token: '{{ csrf_token() }}',
          },
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
              $('#select_type').val('').trigger('change');
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
    }
  });

  $(document).on('click', '#stopSubmit', function(){
    var error_file =''; 
    var error_reason =''; 
    var error_stop_remarks ='';

    if($.trim($('#file_stop_payment').val()).length == 0)
    {
     error_file = 'Document is required';
     $('#error_file').text(error_file);
     $('#file_stop_payment').addClass('has-error');
    }
    else
    {
     error_file = '';
     $('#error_file').text(error_file);
     $('#file_stop_payment').removeClass('has-error');
    }

    if($.trim($('#stop_payment_reason').val()).length == 0)
    {
     error_reason = 'Reason is required';
     $('#error_reason').text(error_reason);
     $('#stop_payment_reason').addClass('has-error');
    }
    else
    {
     error_reason = '';
     $('#error_reason').text(error_reason);
     $('#stop_payment_reason').removeClass('has-error');
    }

    if($.trim($('#stop_remarks').val()).length == 0)
    {
     error_stop_remarks = 'Remarks is required';
     $('#error_stop_remarks').text(error_stop_remarks);
     $('#stop_remarks').addClass('has-error');
    }
    else
    {
     error_stop_remarks = '';
     $('#error_stop_remarks').text(error_stop_remarks);
     $('#stop_remarks').removeClass('has-error');
    }

    if(error_file != '' || error_reason !='' || error_stop_remarks !='') {
      return false;
    }
    else
    {
      // alert('OK');
      var stopRemarks = $('#stop_remarks').val();
      var stopReason = $('#stop_payment_reason').val();
      var stopId = $('#benId').val();
      var stopSchemeid = $('#stop_scheme_id').val();
      var formData = new FormData();
      var files = $('#file_stop_payment')[0].files;
      formData.append('file_stop_payment', files[0]);
      formData.append('stop_remarks', stopRemarks);
      formData.append('stop_payment_reason', stopReason);
      formData.append('scheme_id', stopSchemeid);
      formData.append('id', stopId);
      formData.append('_token', '{{ csrf_token() }}');
      $('.loadingDivModal').show();
      $.ajax({
        type: 'POST',
        url: "{{ route('stopPaymentBenDetails') }}",
        data: formData,
        dataType: 'json',
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
            $('#modalStopPayment').modal('hide');
            $('#res_div').hide();
            $('#select_type').val('').trigger('change');
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
  });

  $(document).on('click', '#mobileSubmit', function(){
    var error_mobile_no_update ='';
    var error_mobile_remarks ='';

    if($.trim($('#mobile_no_update').val()).length == 0)
    {
     error_mobile_no_update = 'Mobile no. is required';
     $('#error_mobile_no_update').text(error_mobile_no_update);
     $('#mobile_no_update').addClass('has-error');
    }
    else
    {
     error_mobile_no_update = '';
     $('#error_mobile_no_update').text(error_mobile_no_update);
     $('#mobile_no_update').removeClass('has-error');
    }

    if($.trim($('#mobile_remarks').val()).length == 0)
    {
     error_mobile_remarks = 'Remarks is required';
     $('#error_mobile_remarks').text(error_mobile_remarks);
     $('#mobile_remarks').addClass('has-error');
    }
    else
    {
     error_mobile_remarks = '';
     $('#error_mobile_remarks').text(error_mobile_remarks);
     $('#mobile_remarks').removeClass('has-error');
    }

    if(error_mobile_no_update != '' || error_mobile_remarks !='') {
      return false;
    }
    else
    {
      // alert('OK');
      var new_update_mobile = $('#mobile_no_update').val();
      var old_update_mobile = $('#old_mobile_no_update').val();
      var mobileRemarks = $('#mobile_remarks').val();
      var mobileSchemeId = $('#mobile_scheme_id').val();
      var mobilebenId = $('#mobilebenId').val();
      var formData = new FormData();
      formData.append('mobile_no', new_update_mobile);
      formData.append('mobile_remarks', mobileRemarks);
      formData.append('scheme_id', mobileSchemeId);
      formData.append('id', mobilebenId);
      formData.append('_token', '{{ csrf_token() }}');
      if (new_update_mobile == old_update_mobile) {
        $.confirm({
          title: 'Confirm',
          type: 'orange',
          icon: 'fa fa-info',
          content: 'Your entered mobile no is same as pervious mobile no. Do you want to proceed ?',
          buttons: {
            confirm: {
              text: 'Confirm',
              btnClass: 'btn-blue',
              keys: ['enter', 'shift'],
              action: function(){
                mobileUpdate(formData);
              }
            },
            cancel: function () {
            },
          }
        });
      }
      else {
        mobileUpdate(formData);
      }
    }
  });

  function mobileUpdate(formData){
    $('.loadingDivModal').show();
    $.ajax({
      type: 'POST',
      url: "{{ route('updateMobileBenDetails') }}",
      data: formData,
      dataType: 'json',
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
          $('#modalUpdateMobile').modal('hide');
          $('#res_div').hide();
          $('#select_type').val('').trigger('change');
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

  function loadItems10(dist_code, element, path, selectInputClass) {
    $.ajax({
      type: 'GET',
      url: path + element +'/'+dist_code,
      success: function (datas) {
        if (!datas || datas.length === 0) {
        //alert("sucess with 0 data");
        return;
        }
        //alert('success url:'paths);
        $(selectInputClass).empty().append('<option value="">Select Block/Municipality</option>');
        for (var  i = 0; i < datas.length; i++) {
          $(selectInputClass).append($('<option>', {
            value: datas[i].code,
            text: datas[i].name
          }));
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        ajax_error(jqXHR, textStatus, errorThrown);
      }
    });
  }

  function ajax_error(jqXHR, textStatus, errorThrown){
    var msg = "<strong>Failed to Load data.</strong><br/>";
    if (jqXHR.status !== 422 && jqXHR.status !== 400) {
      msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
    } 
    else {
      if (jqXHR.responseJSON.hasOwnProperty('exception')) {
        msg += "Exception: <strong>" + jqXHR.responseJSON.exception_message + "</strong>";
      } 
      else {
        msg += "Error(s):<strong><ul>";
        $.each(jqXHR.responseJSON, function (key, value) {
          msg += "<li>" + value + "</li>";
        });
        msg += "</ul></strong>";
      }
    }
    $.alert({
      title: 'Error!!',
      type: 'red',
      icon: 'fa fa-warning',
      content: msg,
    });
  }
</script>