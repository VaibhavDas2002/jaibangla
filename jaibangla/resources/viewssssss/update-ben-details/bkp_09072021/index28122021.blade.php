





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
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
  <div class="content-wrapper">
    <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" width="150px" id="loader_img"></div>
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
                        <div class="col-md-3" id="fname_div">
                          <label class=" control-label">First Name <span class="text-danger">*</span></label>
                          <input type="text" name="ben_fname" id="ben_fname" class="form-control">
                          <span class="text-danger" id="error_ben_fname"></span>
                        </div>
                        <div class="col-md-3" id="mname_div">
                          <label class=" control-label">Middle Name</label>
                          <input type="text" name="ben_mname" id="ben_mname" class="form-control">
                          <span class="text-danger" id="error_ben_mname"></span>
                        </div>
                        <div class="col-md-3" id="lname_div">
                          <label class=" control-label">Last Name</label>
                          <input type="text" name="ben_lname" id="ben_lname" class="form-control">
                          <span class="text-danger" id="error_ben_lname"></span>
                        </div>
                        <div class="col-md-3" id="benid_div">
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
          
          <div id="res_div">
            <div class="panel panel-default">
              <div class="panel-heading" id="panel_head">List of Beneficiary</div>
              <div class="panel-body" style="padding: 5px; font-size: 14px;">
                <div class="table-responsive">
                  <table id="example" class="display" cellspacing="0" width="100%"> 
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
              <form method="POST" action="{{ url('resume-ben-payment') }}" id="resume_form">
                {{csrf_field()}}
                <input type="text" name="ben_id" id="resume_ben_id">
                <input type="text" name="lot_generate_no" id="lot_generate_no">
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
                      <button class="btn btn-primary" type="submit" id="final_resume_button">Resume Payment</button>
                  </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              <!-- <button type="button" class="btn btn-primary"><i class="fa fa-print"></i> Resume</button> -->
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

    $('#loader_img').hide();
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
          "bInfo": true,
          "pageLength":30,
          'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="150px"></div>'
          },
          "ajax": 
          {
            url: "{{ url('search-by-name') }}",
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
              $('.preloader1').hide();
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete":function(){
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

    $(document).on('click', '.resume_button', function() {
      var val = $(this).val();
      console.log(val);
      $('#show_month').text('');
      $('#modal-default').modal('show');
      var arr = val.split('_');
      $('#resume_ben_id').val(arr[0]);
      $('#lot_generate_no').val(arr[1]);
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
      if ($('#resume_ben_id').val() != '' && $('#lot_generate_no').val() != '') {
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
                $('#resume_form').submit();
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

  });

  function editFunction(value){
    if ($('#select_item_update_'+value).val() == '') {
      $.alert({
        title: 'Alert!!',
        type: 'red',
        icon: 'fa fa-warning',
        content: '<strong>Please select option which one do you want to update?</strong>'
      });
    }
    else {
      $('#myForm_'+value).submit();
    }
  }

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