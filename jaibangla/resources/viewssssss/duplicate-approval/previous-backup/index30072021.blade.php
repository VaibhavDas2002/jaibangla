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
        Reject Duplicate Approved Beneficiary
      </h1>
      <ol class="breadcrumb">
        <li class="active"><i class="fa fa-clock-o"></i> Date :> <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span></li>
      </ol>
    </section>
    <section class="content">
      <div class="box box-default">
        <!-- <div class="box-header with-border">
          <div class="row">
            <div class="col-sm-8">
              <h3 class="box-title">Lot Transaction IFMS</h3>
            </div>
          </div>
        </div> -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
              @if (($message = Session::get('success')) && ($id =Session::get('id')))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{{ $message }} with Application ID: {{$id}}</strong>
                </div>
              @endif
              @if (($message = Session::get('message')))
                  <div class="alert alert-danger alert-block">
                      <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
                  </div>
              @endif
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group col-md-4">
                    <label class=" control-label">Scheme</label>
                    <select class="form-control select2" name="scheme"  id="scheme">
                      <option value="">--Select--</option>
                      @foreach($schemes as $scheme)
                        <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}</option>
                      @endforeach
                    </select>
                    <span class="text-danger" id="error_scheme"></span>
                  </div>
                  <div class="form-group col-md-4">
                    <label class="control-label">Select Fiter Criteria</label>
                    <select class="form-control select2" name="filter"  id="filter">
                      <option value="">--Select--</option>
                      <option value="ration">By Ration Card</option>
                      <option value="voter">By Voter Card</option>
                    </select>
                    <span class="text-danger" id="error_filter"></span>
                  </div>
                  <div class="form-group col-md-2">
                    <label class=" control-label">&nbsp;</label>
                    <button class="btn btn-primary form-control" id="submit_btn" type="button"><i class="fa fa-search"></i> Search</button>
                  </div>
                </div>
              </div>

              <div id="res_div">
                <div class="panel panel-default">
                  <div class="panel-heading" id="panel_head" style="font-size: 16px;"></div>
                  <div class="panel-body" style="padding: 0px 0px 0px 0px;">
                    <div id="sbilot_data"></div>
                  </div>
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

  $('#loader_img').hide();
  $('#res_div').hide();
  error_scheme='';
  error_filter='';
  $('#submit_btn').click(function(){
    if($.trim($('#scheme').val()).length == 0){
      error_scheme = 'Scheme is Required';
      $('#error_scheme').text(error_scheme);
    }
    else{
      error_scheme = '';
      $('#error_scheme').text(error_scheme);
    }

    if($.trim($('#filter').val()).length == 0){
      error_filter = 'Filter Criteria is Required';
      $('#error_filter').text(error_filter);
    }
    else{
      error_filter = '';
      $('#error_filter').text(error_filter);
    }

    if( error_scheme != '' || error_filter !=''){
      return false;
    }
    else{
      let scheme=$('#scheme').val();
      let filter=$('#filter').val();
      // return true;
      var msg = 'Scheme : '+$( "#scheme option:selected" ).text()+' , Filter Criteria : '+$( "#filter option:selected" ).text();
      $('#loader_img').show();
      $('#res_div').hide();
      $('#submit_btn').attr('disabled','disabled');
      // $('#submit_btn').html('<img src="images/ZKZg.gif" width="20px" id="loader_img"> <span class="text-primary"><b>Loading..</b></span>');
      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $.ajax({
        url: "{{ route('show-duplicate-approval') }}",
        method: 'post',
        data: {
          scheme: scheme,
          filter: filter,
          _token:"{{csrf_token()}}"
        },
        success: function(result) {
          $('#submit_btn').removeAttr('disabled');
          // $('#submit_btn').html('<i class="fa fa-search"></i> Submit');
          $('#loader_img').hide();
          $('#res_div').show();
          $('#sbilot_data').html('');
          $('#sbilot_data').html(result);
          $('#panel_head').text(msg);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $('#loader_img').hide();
          $('#res_div').show();
          $('#submit_btn').removeAttr('disabled');
          // $('#submit_btn').html('<i class="fa fa-search"></i> Submit');
          ajax_error(jqXHR, textStatus, errorThrown);
        }
      });
    }
  });
});

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