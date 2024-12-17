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
  .collapsable-head {
    /*border-radius: 5px; */
    border-bottom: 1px solid powderblue;
    background-color: antiquewhite; 
    text-align: center; 
    padding: 5px; 
    cursor: pointer;
  }
  .collapsable-head span {
    float: left; 
    margin-top: 4px;
  }
  .collapsable-head font {
    font-size: 15px; 
    font-weight: bold;
  }
  .main-collapsable {
    border: 1px solid powderblue; 
    border-radius: 4px;
    margin-top: 5px;
  }
  table {
    border-top: 1px solid #e6e6e6;
  }
  .collapsable-body {
    padding: 5px;
  }
  .panel-heading {
    cursor: pointer;
  }
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
  <div class="content-wrapper">
    <div class="preloader1" align="center" id="loader_img"><img src="images/ZKZg.gif" width="150px"></div>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Beneficiary Application Status
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('backendlogin') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <!-- <li class="active"></li> -->
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="box box-default">
        <!-- <div class="box-header with-border">
          <div class="box-title">Details</div>
        </div> -->
        <div class="box-body">
          <div class="panel panel-default">
            <div class="panel-heading"><span id="panel-icon"><i class='fa fa-minus'></i></span> &nbsp;&nbsp;Enter Beneficiary Details</div>
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
                  <!-- <form class="form-horizontal" role="form" method="POST" action="{{ route('list-app-status') }}" id="submit_form"> -->
                    {{csrf_field()}}
                    <input type="hidden" name="map_level" id="map_level" value="{{$mapping_level}}">
                    <input type="hidden" name="dist_code" id="dist_code" value="{{$district_code}}">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="col-md-3" id="select_type_div">
                          <label class=" control-label">Search Using Id/Name</label>
                          <select class="form-control select2" name="select_type" id='select_type' required @if($mapping_level=="Department"||$mapping_level=="State") disabled @endif>
                            <option value="">--Select--</option>
                            <option value="b_id" @if($mapping_level=="Department"||$mapping_level=="State") selected @endif>Beneficiary Id</option>
                            <option value="b_name">Beneficiary Name</option>
                          </select>
                          <span class="text-danger" id="error_select_type"></span>
                        </div>
                        <div class="col-md-3" id="fname_div">
                          <label class=" control-label">First Name</label>
                          <input type="text" name="ben_fname" id="ben_fname" class="form-control" min="3">
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
                        <div class="@if($mapping_level=="Department"||$mapping_level=="State") col-md-6 @else col-md-3 @endif" id="benid_div">
                          <label class=" control-label">Beneficiary Id</label>
                          <input type="text" name="ben_id" id="ben_id" class="form-control" onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;">
                          <span class="text-danger" id="error_ben_id"></span>
                        </div>
		        @if(Auth::user()->designation_id_old != "Admin")
                        <div class="@if($mapping_level=="Department"||$mapping_level=="State") col-md-6 @else col-md-3 @endif">
                          <label class=" control-label">Scheme</label>
                          <select class="form-control select2" name="scheme_type" id='scheme_type' required>
                            <option value="">--Select Scheme--</option>
                            @foreach ($schemes as $scheme)
                            <option value="{{$scheme->Scheme->id}}">{{$scheme->Scheme->scheme_name}}</option>
                            @endforeach
                          </select>
                          <span class="text-danger" id="error_scheme_type"></span>
                        </div>
			@endif
                        <div class="col-md-3" id="rural_urban_div">
                          <label class=" control-label">Rural/Urban</label>
                          <select name="is_rural_urban" id="is_rural_urban" class="form-control select2 client-js-urban">
                            <option value="">--Select Rural/Urban--</option>
                            <option value="2">Rural</option>
                            <option value="1">Urban</option>
                          </select>
                          <span class="text-danger" id="error_is_rural_urban"></span>
                        </div>
                        <div class="col-md-3" id="block_ulb_div">
                          <label class=" control-label">Block/Municipality</label>
                          <select name="block_ulb" id="block_ulb" class="form-control select2 client-js-localbody">
                            <option value="">--Select Block/Municipality--</option>                
                          </select>
                          <span class="text-danger" id="error_block_ulb"></span>
                        </div>
                    
                      </div>
                    </div>
                    <br/>
                    <div class="row">
                      <div class="@if(Auth::user()->designation_id_old=="Admin") col-md-6 @else col-md-12 @endif" align="center">
                        <button class="btn btn-primary" id="submit_btn" type="button" style="width: 200px;" disabled><i class="fa fa-search"></i> Search</button>
                      </div>
                    </div>
                  <!-- </form> -->
                </div>
              </div>
            </div>
          </div>
          
          <!-- Result Div -->
          <div id="res_div">
            
          </div>
          
        
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>
<script>
  $(document).ready(function() {
    $('.panel-heading').click(function(){
      $('.panel-body').toggle('slow');
      if ($('#panel-icon').html() == "<i class=\"fa fa-plus\"></i>")
        $('#panel-icon').html("<i class=\"fa fa-minus\"></i>");
      else
        $('#panel-icon').html("<i class=\"fa fa-plus\"></i>");
    });
    
    // Form Submit Related Work
    $('#loader_img').hide();
    $('#submit_btn').removeAttr('disabled');
    $('#submit_btn').html('<i class="fa fa-search"></i> Submit');
    $('#fname_div').hide();
    $('#mname_div').hide();
    $('#lname_div').hide();
    $('#benid_div').hide();

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

    var map_l = $('#map_level').val();
    if (map_l == 'State' || map_l == 'Department') {
      $('#select_type_div').hide();
      $('#fname_div').hide();
      $('#mname_div').hide();
      $('#lname_div').hide();
      $('#benid_div').show();
      $('#rural_urban_div').hide();
      $('#block_ulb_div').hide();
    }
    else if (map_l == 'District') {
      $('#select_type_div').show();
      $('#fname_div').hide();
      $('#mname_div').hide();
      $('#lname_div').hide();
      $('#benid_div').hide();
      $('#rural_urban_div').show();
      $('#block_ulb_div').show();
    }
    else if (map_l == 'Block' || map_l == 'Subdiv') {
      $('#select_type_div').show();
      $('#fname_div').hide();
      $('#mname_div').hide();
      $('#lname_div').hide();
      $('#benid_div').hide();
      $('#rural_urban_div').hide();
      $('#block_ulb_div').hide();
    }
    else {
      
    }

    var ajaxData = "";
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
        if($.trim($('#ben_id').val()).length == 0){
          error_ben_id = 'Beneficiary id is required';
          $('#error_ben_id').text(error_ben_id);
        }
        else{
          error_ben_id = '';
          $('#error_ben_id').text(error_ben_id);
        }
      }
      if ($('#select_type').val() == 'b_name') {
        if($.trim($('#ben_fname').val()).length < 3){
          error_ben_fname = 'First name is required (Min 3 characters)';
          $('#error_ben_fname').text(error_ben_fname);
        }
        else{
          error_ben_fname = '';
          $('#error_ben_fname').text(error_ben_fname);
        }
      }

      if (map_l == 'State' || map_l == 'Department') {
        if( /*error_scheme_type != '' ||*/ error_ben_id != ''){
          return false;
        }
        else{
          var ajaxData = {
            ben_id: $('#ben_id').val(),
            scheme_id: $('#scheme_type').val(),
            _token:"{{csrf_token()}}"
          };
          submit_form(ajaxData);
        }
      }
      else if (map_l == 'District') {
        if( error_select_type != '' || error_scheme_type != '' || error_ben_id != '' || error_ben_fname != ''/* || error_block_ulb != '' || error_is_rural_urban != ''*/){
          return false;
        }
        else{
          var ajaxData = {
            select_type: $('#select_type').val(),
            ben_id: $('#ben_id').val(),
            scheme_id: $('#scheme_type').val(),
            ben_fname: $('#ben_fname').val(),
            ben_mname: $('#ben_mname').val(),
            ben_lname: $('#ben_lname').val(),
            is_rural_urban: $('#is_rural_urban').val(),
            block_ulb: $('#block_ulb').val(),
            _token:"{{csrf_token()}}"
          };
          submit_form(ajaxData);
        }
      }
      else if (map_l == 'Block' || map_l == 'Subdiv') {
        if( error_select_type != '' || error_scheme_type != '' || error_ben_id != '' || error_ben_fname != ''){
          return false;
        }
        else{
          var ajaxData = {
            select_type: $('#select_type').val(),
            ben_id: $('#ben_id').val(),
            scheme_id: $('#scheme_type').val(),
            ben_fname: $('#ben_fname').val(),
            ben_mname: $('#ben_mname').val(),
            ben_lname: $('#ben_lname').val(),
            _token:"{{csrf_token()}}"
          };
          submit_form(ajaxData);
        }
      }
      else {
        
      }
      
    });

    // Calling For Block/Municipality
    if ($('#dist_code').val() != '') {
      $('.client-js-urban').change(function() {
        if ($('.client-js-urban').val() != '' ) {
          var select_district_code= $('#dist_code').val();
          var select_body_type= $('.client-js-urban').val();
          var htmlOption='<option value="">Loading...</option>';
          $('.client-js-localbody').empty().append(htmlOption);
          loadItems10(select_district_code, select_body_type, 'api/ruralurban/', '.client-js-localbody');
        }
        else{
          var htmlOption='<option value="">--Select Rural/Urban--</option>';
          $('.client-js-localbody').empty().append(htmlOption);
        }
      });
    }

    // Function for submit function
    function submit_form(ajaxData){
      // $('#loader_img').show();
      $('#res_div').hide();
      $('#submit_btn').attr('disabled','disabled');
      $('#submit_btn').html('<img src="images/ZKZg.gif" width="20px" id="loader_img"> Submitting please wait...');

      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $.ajax({
        url: "{{ route('result-app-status') }}",
        method: 'post',
        data: ajaxData,
        success: function(result) {
          $('#panel-icon').html('<i class="fa fa-plus"></i>');
          $('.panel-body').hide('slow');
          $('#submit_btn').removeAttr('disabled');
          $('#submit_btn').html('<i class="fa fa-search"></i> Submit');
          // $('#loader_img').hide();
          $('#res_div').show();
          $('#res_div').html('');
          $('#res_div').html(result);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          // $('#loader_img').hide();
          $('#res_div').show();
          $('#submit_btn').removeAttr('disabled');
          $('#submit_btn').html('<i class="fa fa-search"></i> Submit');
          ajax_error(jqXHR, textStatus, errorThrown);
        }
      });
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