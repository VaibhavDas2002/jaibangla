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
  #updateDiv {
    border: 1px solid #d9d9d9;
    padding: 8px;  
    box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
  }
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
  <div class="content-wrapper">
    <!-- <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" width="150px" id="loader_img"></div> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Track Application Status
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
            <div class="panel-heading" style="font-size: 14px; font-weight: bold; font-style: italic;"><span id="panel-icon">Search using beneficiary id, mobile_no or aadhar no</div>
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
                        <label class=" control-label">Search Using <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="select_type" id='select_type' required>
                          <option value="">--Select--</option>
                          <option value="benId">Beneficiary Id</option>
                          <option value="benName">Beneficiary Name</option>
                          <option value="benMobile">Mobile No</option>
                          <option value="benAadhar">Aadhar No</option>
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
                        <input type="text" name="ben_id" id="ben_id" class="form-control" maxlength="20" onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;">
                        <span class="text-danger" id="error_ben_id"></span>
                      </div>
                      <div class="col-md-3" id="mobileno_div" style="display: none;">
                        <label class=" control-label">Mobile No<span class="text-danger">*</span></label>
                        <input type="text" name="mobile_no" id="mobile_no" class="form-control" maxlength="10" onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;">
                        <span class="text-danger" id="error_mobile_no"></span>
                      </div>
                      <div class="col-md-3" id="aadhar_div" style="display: none;">
                        <label class=" control-label">Aadhar No<span class="text-danger">*</span></label>
                        <input type="text" name="aadhar_no" id="aadhar_no" class="form-control" maxlength="12" onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;">
                        <span class="text-danger" id="error_aadhar_no"></span>
                      </div>
                      <div class="col-md-3">
                        <label class=" control-label">Scheme <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="scheme_id" id='scheme_id' required>
                          <option value="">--Select Scheme--</option>
                          @foreach ($schemes as $scheme)
                          <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                          @endforeach
                        </select>
                        <span class="text-danger" id="error_scheme_id"></span>
                      </div>
                      <div class="col-md-3" style="margin-top: 24px;">
                        <button class="btn btn-primary" name="submit_btn" id="submit_btn" type="button" disabled><i class="fa fa-search"></i> Search</button>&nbsp;
                        {{-- <button class="btn btn-default" name="reset_btn" id="reset_btn" type="button" disabled><i class="fa fa-refresh"></i> Reset</button> --}}
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
                      <th width="10%">Applicant Name</th>
                      <th width="10%">Father's Name</th>
                      <th width="20%">Address</th> 
                      <th width="20%">Banking Information</th>
                      <th width="20%">Payment Information</th>
                      <th width="15%">Current Status</th>
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
    $('#reset_btn').removeAttr('disabled');
    $('#fname_div').hide();
    $('#mname_div').hide();
    $('#lname_div').hide();
    $('#benid_div').hide();
    $('#mobileno_div').hide();
    $('#aadhar_div').hide();
    $('#res_div').hide();

    $('#select_type').change(function(){
      $('#ben_id').val('');
      $('#ben_fname').val('');
      $('#ben_mname').val('');
      $('#ben_lname').val('');
      $('#mobile_no').val('');
      $('#aadhar_no').val('');
      if ($('#select_type').val() == 'benId') {
        $('#fname_div').hide();
        $('#mname_div').hide();
        $('#lname_div').hide();
        $('#benid_div').show();
        $('#mobileno_div').hide();
        $('#aadhar_div').hide();
      }
      else if ($('#select_type').val() == 'benName') {
        $('#fname_div').show();
        $('#mname_div').show();
        $('#lname_div').show();
        $('#benid_div').hide();
        $('#mobileno_div').hide();
        $('#aadhar_div').hide();
      }
      else if ($('#select_type').val() == 'benMobile') {
        $('#fname_div').hide();
        $('#mname_div').hide();
        $('#lname_div').hide();
        $('#benid_div').hide();
        $('#mobileno_div').show();
        $('#aadhar_div').hide();
      }
      else if ($('#select_type').val() == 'benAadhar') {
        $('#fname_div').hide();
        $('#mname_div').hide();
        $('#lname_div').hide();
        $('#benid_div').hide();
        $('#mobileno_div').hide();
        $('#aadhar_div').show();
      }
      else {
        $('#fname_div').hide();
        $('#mname_div').hide();
        $('#lname_div').hide();
        $('#benid_div').hide();
        $('#mobileno_div').hide();
        $('#aadhar_div').hide();
      }
    });

    var error_select_type = '';
    var error_scheme_id = '';
    $('#submit_btn').click(function(){
      if($.trim($('#select_type').val()).length == 0){
        error_select_type = 'Select type is required';
        $('#error_select_type').text(error_select_type);
      }
      else{
        error_select_type = '';
        $('#error_select_type').text(error_select_type);
      }

      if($.trim($('#scheme_id').val()).length == 0){
        error_scheme_id = 'Scheme name is required';
        $('#error_scheme_id').text(error_scheme_id);
      }
      else{
        error_scheme_id = '';
        $('#error_scheme_id').text(error_scheme_id);
      }

      if( error_select_type != '' || error_scheme_id != ''){
        return false;
      }
      else{
        // alert('OK');
        $('#loadingDiv').show();
        var searchType = $('#select_type').val();
        var pensionId = $('#ben_id').val();
        var benFname = $('#ben_fname').val();
        var benMname = $('#ben_mname').val();
        var benLname = $('#ben_lname').val();
        var benMob = $('#mobile_no').val();
        var benAa = $('#aadhar_no').val();
        var schemeId = $('#scheme_id').val();
        var ajaxData = { 
          'searchType' : searchType, 
          'pensionId' : pensionId, 
          'benFname' : benFname, 
          'benMname' : benMname, 
          'benLname' : benLname, 
          'benMob' : benMob, 
          'benAa' : benAa, 
          'schemeId' : schemeId, 
          _token: "{{ csrf_token() }}" 
        };
        loadDataTable(ajaxData);
      }
    });

  });  
  function loadDataTable(ajaxData) {
    $('#loadingDiv').show();
    $('#res_div').show();
    $('#submit_btn').attr('disabled',true);

    if ( $.fn.DataTable.isDataTable('#example') ) {
      $('#example').DataTable().destroy();
    }
    var dataTable = $('#example').DataTable( {
      dom: 'Blfrtip',
      "scrollX": true,
      "paging": false,
      "searchable": false,
      "ordering":false,
      "bFilter": false,
      "bInfo": false,
      "pageLength":20,
      'lengthMenu': [[10, 20, 30, 50,100], [10, 20, 30, 50,100]],
      "serverSide": true,
      "processing":true,
      "bRetrieve": true,
      "oLanguage": {
        // "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="150px"></div>'
      },
      "ajax": 
      {
        url: "{{ url('getTrackApplicantDetails') }}",
        type: "post",
        data: ajaxData,
        error: function (jqXHR, textStatus, errorThrown) {
          $('#loadingDiv').hide();
          $('#submit_btn').removeAttr('disabled');
          ajax_error(jqXHR, textStatus, errorThrown);
        }
      },
      "initComplete":function(){
        $('#loadingDiv').hide();
        $('#submit_btn').removeAttr('disabled');
        //console.log('Data rendered successfully');
      },
      "columns": [
        // { "data": "DT_RowIndex" },
        { "data": "id" },
        { "data": "name" },
        { "data": "father_name" },
        { "data": "address" },
        { "data": "bank_info" },
        { "data": "payment_info"},
        { "data": "current_status" },
      ],

      "buttons": [
        // 'pdf','excel'
      ],
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