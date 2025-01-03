<style type="text/css">
  .preloader1{
    position: fixed;
    top:40%;
    left: 52%;
    z-index: 999;
  }
  .preloader1 {
    background: transparent !important;
  }
  .disabledContent {
    pointer-events: none;
    opacity: 0.4;
  }
  /*
    Clearable text inputs
  */
  .clearable{
    background: #fff url(http://i.stack.imgur.com/mJotv.gif) no-repeat right -10px center;
    border: 1px solid #999;
    padding: 3px 18px 3px 4px; /* Use the same right padding (18) in jQ! */
    border-radius: 3px;
    transition: background 0.4s;
  }
  .clearable.x  { background-position: right 5px center; } /* (jQ) Show icon */
  .clearable.onX{ cursor: pointer; } /* (jQ) hover cursor style */
  .clearable::-ms-clear {display: none; width:0; height:0;} /* Remove IE default X */
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
  <div class="content-wrapper">
    <div class="preloader1" align="center" id="loader_img"><img src="images/ZKZg.gif" width="100px"></div>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Sending Mail To User
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
          <div class="box-title">Enter Details</div>
        </div> -->
        <div class="box-body">
          @if (($message = Session::get('success')) )
          <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }} </strong>
          </div>
          @endif
          @if (($message = Session::get('error')) )
          <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }} </strong>
          </div>
          @endif
          <div class="panel panel-default">
            <div class="panel-heading">Enter Mailing Details</div>
            <div class="panel-body" style="padding: 5px;">
              <div class="row">
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="col-md-3">
                        <label class="control-label">User Role/Designation</label>
                        <select class="form-control select2" name="designation" id='designation' required>
                          <option value="">--Select Designation--</option>
                          @foreach ($designations as $designation)
                          <option value="{{$designation->designation_id}}">{{$designation->designation_id}}</option>
                          @endforeach
                        </select>
                        <span class="text-danger" id="error_desig"></span>
                      </div>
                      <div class="col-md-3">
                        <label class="control-label">Scheme</label>
                        <select class="form-control select2" name="scheme" id='scheme' required>
                          <option value="">--Select Scheme--</option>
                          @foreach ($schemes as $scheme)
                          <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                          @endforeach
                        </select>
                        <span class="text-danger" id="error_scheme"></span>
                      </div>
                      <div class="col-md-3" id="district_div">
                        <label class=" control-label">District</label>
                        <select name="district" id="district" class="form-control select2 js-district">
                          <option value="">--Select District--</option>
                          @foreach ($districts as $district)
                          <option value="{{$district->district_code}}"> {{$district->district_name}}</option>
                          @endforeach
                        </select>
                        <span id="error_district" class="text-danger"></span>
                      </div>
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
                      <div class="col-md-3">
                        <label class=" control-label">Which Report ?</label>
                        <select name="report" id="report" class="form-control select2">
                          <option value="">--Select Report--</option>                
                        </select>
                        <span class="text-danger" id="error_report"></span>
                      </div>
                      <div class="col-md-6" style="margin-top: 26px;text-align: center;">
                        <button class="btn btn-warning" id="generate_report" type="button" style="width: 150px;" disabled>Generate Report</button>&nbsp;
                        <button class="btn btn-info" id="reset" type="button" style="width: 150px;">Reset</button>
                      </div>
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>

          <div class="panel panel-default">
            <div class="panel-heading">
              <div class="row">
                <div class="col-md-6">User Email Address and Mail Body</div>
                <div class="col-md-6" align="right">Add Email Using Mobile No. <input type="text" name="mobile_no" id="mobile_no" class="clearable" onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;" maxlength="10" placeholder="Mobile No.(10 digits)" autocomplete="off"> <img src="images/ZKZg.gif" width="25px" id="mobile_loader"></div>
              </div>
            </div>
            <div class="panel-body" style="padding: 5px;">
              <form class="form-horizontal" role="form" method="POST" action="{{ route('store-sending-mail-to-user') }}" id="submit_form" enctype="multipart/form-data">
                {{csrf_field()}}
                <div id="row" class="res_div">
                  <div class="col-md-12">
                    <span style="float: right; font-weight: bold; font-style: italic;" class="text-primary"><input type="checkbox" name="select_all" id="select_all"> Select All Email Address</span>
                    <label class=" control-label">Email Address</label>
                    <select name="email_addr[]" id="email_addr" class="form-control select2" multiple>
                      <!-- <option value="">--Select Email Address--</option>                 -->
                    </select>
                    <span class="text-danger" id="error_email_addr"></span>
                  </div>
                  <div class="col-md-12">
                    <label class=" control-label">Subject</label>
                    <input type="text" name="subject" id="subject" class="form-control">
                    <span class="text-danger" id="error_subject"></span>
                  </div>
                  <div class="col-md-12" style="margin-top: 15px;">
                    <textarea id="mail_body" name="mail_body" rows="10" cols="80" required placeholder="Place some text here"></textarea>
                    <span class="text-danger" id="error_body"></span>
                  </div>
                  <div class="col-md-12" style="margin-top: 15px;">
                    <button type="button" name="add" id="add" class="btn btn-success btn-sm" title="Click to add files"><i class="glyphicon glyphicon-paperclip"></i> <b>Attachments</b></button>
                    <span style="font-weight: bold; font-style: italic;">No. of Files : </span><span class="text-success" id="no_of_file" style="font-weight: bold;">0</span>
                    <span class="text-danger" id="error_file" style="padding-left: 50px;"></span>

                    <table id="dynamic_field" class="table table-responsive table-striped table-hover table-borderless"></table>
                  </div>
                  <div class="col-md-12" align="center">
                    <button class="btn btn-primary" id="submit_btn" type="submit" style="width: 200px;">Send <i class="fa fa-arrow-circle-right"></i></button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script type="text/javascript" src="{{ asset("/bower_components/AdminLTE/plugins/ckeditor/ckeditor.js") }}"></script>
<script>
  $(document).ready(function() {
    /**
     * Clearable text inputs
     */
    function tog(v){return v ? "addClass" : "removeClass";} 
    $(document).on("input", ".clearable", function(){
        $(this)[tog(this.value)]("x");
    }).on("mousemove", ".x", function( e ){
        $(this)[tog(this.offsetWidth-18 < e.clientX-this.getBoundingClientRect().left)]("onX");
    }).on("touchstart click", ".onX", function( ev ){
        ev.preventDefault();
        $(this).removeClass("x onX").val("").change();
    });

    CKEDITOR.replace('mail_body');
    $('#loader_img').hide();
    $('#mobile_loader').hide();
    $('#submit_btn').removeAttr('disabled');
    $('#district').attr('disabled',true);
    $('#is_rural_urban').attr('disabled',true);
    $('#block_ulb').attr('disabled',true);
    
    $('#reset').click(function(){
      $('#designation').val('').trigger( "change" );
      $('#scheme').val('').trigger( "change" );
      $('#district').val('').trigger( "change" );
      $('#is_rural_urban').val('').trigger( "change" );
      $('#block_ulb').val('').trigger( "change" );
      $("#email_addr").empty();
      $('#mobile_no').val('').trigger('change');
      // $("#email_addr > option").removeAttr("selected");
      // $("#email_addr").trigger("change");
    });

    $('#designation').change(function(){
      var d = $('#designation').val();
      $('#district').val('').trigger( "change" );
      $('#is_rural_urban').val('').trigger( "change" );
      $('#block_ulb').val('').trigger( "change" );
      $("#email_addr").empty();
      $('#mobile_no').val('').trigger('change');
      
      if (d == 'Approver') {
        $('#district').removeAttr('disabled');
        $('#is_rural_urban').val('');
        $('#is_rural_urban').attr('disabled',true);
        $('#block_ulb').val('');
        $('#block_ulb').attr('disabled',true);
      }
      else if(d == 'Verifier' || d == 'Operator'){
        $('#district').removeAttr('disabled');
        $('#is_rural_urban').removeAttr('disabled');
        $('#block_ulb').removeAttr('disabled');
      }
      else {
        $('#district').val('');
        $('#is_rural_urban').val('');
        $('#block_ulb').val('');
        $('#district').attr('disabled',true);
        $('#is_rural_urban').attr('disabled',true);
        $('#block_ulb').attr('disabled',true);
      }
    });

    $('#designation').change(function() {
      getUserEmail();
    });
    
    $('#scheme').change(function() {
      getUserEmail();
    });
    
    $('.js-district').change(function() {
      load_block_ulb();
      getUserEmail();
    });

    $('.client-js-urban').change(function() {
      if ($('.client-js-urban').val() != '') {
        if ($('.js-district').val() == '') {
          $.alert({
            title: 'Alert!',
            content: 'Please select district first!',
          });
          $('.client-js-urban').val('');
        }
        else {
          load_block_ulb();
          getUserEmail();
        }
      }
    });

    $('#block_ulb').change(function() {
      getUserEmail();
    });

    function load_block_ulb(){
      if (($('.client-js-urban').val() != '') && ($('.js-district').val() != '')) {
        var select_district_code= $('#district').val();
        var select_body_type= $('.client-js-urban').val();
        var htmlOption='<option value="">Loading...</option>';
        $('.client-js-localbody').empty().append(htmlOption);
        $('.content').addClass('disabledContent');
        $('#loader_img').show();
        loadItems10(select_district_code, select_body_type, 'api/ruralurban/', '.client-js-localbody');
      }
      else{
        var htmlOption='<option value="">--Select Block/Municipality--</option>';
        $('.client-js-localbody').empty().append(htmlOption);
      }
    }

    function getUserEmail(){
      var desig = $('#designation').val();
      var scheme = $('#scheme').val();
      var district = $('#district').val();
      var rural_urban = $('#is_rural_urban').val();
      var block_ulb = $('#block_ulb').val();

      if(desig == 'Verifier' || desig == 'Operator'){
        if (desig != '' && scheme != '' && district != '') {
          getEmailAjaxCall(desig, scheme, district, rural_urban, block_ulb);
        }
      }
      else {
        if (desig != '' && scheme != '') {
          getEmailAjaxCall(desig, scheme, district, rural_urban, block_ulb);
        }
      }
    }

    function getEmailAjaxCall(desig, scheme, district, rural_urban, block_ulb){
      $('.content').addClass('disabledContent');
      $('#loader_img').show();
      $.ajax({
        url: "{{ route('get_user_email_address') }}",
        method: 'post',
        data: {
          designation: desig,
          scheme_id: scheme,
          district_code: district,
          is_rural: rural_urban,
          block_ulb: block_ulb,
          _token:"{{csrf_token()}}"
        },
        success: function (datas) {
          $('#loader_img').hide();
          $('.content').removeClass('disabledContent');
          //console.log(JSON.stringify(datas.query));
          $('#email_addr').empty();
          for (var k = datas.result.length - 1; k >= 0; k--) {
            $('#email_addr').append('<option value="'+datas.result[k].email+'">'+datas.result[k].email+'</option>');
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $('#loader_img').hide();
          $('.content').removeClass('disabledContent');
          ajax_error(jqXHR, textStatus, errorThrown);
        }
      });
    }

    var i=1;  
    $('#add').click(function(){ 
      i++;
      $('#dynamic_field').append('<tr id="row'+i+'" style="display: none;"><td width="80%"><span id="name'+i+'"></span> <input type="file" id='+i+' name="mail_file[]" onchange="validateFile('+i+')" style="display:none;" /></td><td width="20%"><span id="size'+i+'"></span> <button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove btn-xs"><b>x</b></button></td></tr>');
      $('#'+i).trigger('click');
    });

    $(document).on('click', '.btn_remove', function(){
      var button_id = $(this).attr("id");
      var file = document.getElementById(button_id).files[0];
      if (file != undefined) { 
        file_no = document.getElementById('no_of_file').innerHTML;
        var file_no = Number(file_no) - 1;
        document.getElementById('no_of_file').innerHTML = file_no;
      }
      $('#row'+button_id+'').remove();  
    });

    $('#mobile_no').on('keyup', function(){
      if ($('#mobile_no').val().length == 10) {
        $('#mobile_loader').show();
        var mobile_no = $('#mobile_no').val();
        $.ajax({
          url: "{{ route('get_email_using_mobile_no') }}",
          method: 'post',
          data: {
            mobile_no: mobile_no,
            _token:"{{csrf_token()}}"
          },
          success: function (datas) {
            $('#mobile_loader').hide();
            if (datas.is_exists == 1) {
              if ($("#email_addr option[value='"+datas.email+"']").length == 0) {
                $('#email_addr').append('<option value="'+datas.email+'" selected>'+datas.email+'</option>');
              }
            }
            else {
              $.alert({
                title: 'Email',
                content: datas.email,
              });
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $('#mobile_loader').hide();
            ajax_error(jqXHR, textStatus, errorThrown);
          }
        });
      }
    });

    $('#select_all').click(function(){
      if($("#select_all").is(':checked') ){
        $("#email_addr > option").prop("selected","selected");// Select All Options
        $("#email_addr").trigger("change");// Trigger change to select 2
      }else{
        $("#email_addr > option").removeAttr("selected");
        $("#email_addr").trigger("change");// Trigger change to select 2
      }
    });

    var error_email_addr = '';
    var error_subject = '';
    var error_body = '';
    var error_file = '';
    $('#submit_btn').click(function(e){
      e.preventDefault();
      var options = $('#email_addr > option:selected');
      if(options.length == 0){
        error_email_addr = 'Email address is required';
        $('#error_email_addr').text(error_email_addr);
      }
      // if($('#email_addr').has('option').length == 0){
      //   error_email_addr = 'Email address is required';
      //   $('#error_email_addr').text(error_email_addr);
      // }
      else{
        error_email_addr = '';
        $('#error_email_addr').text(error_email_addr);
      }

      if($.trim($('#subject').val()).length == 0){
        error_subject = 'Subject is required';
        $('#error_subject').text(error_subject);
      }
      else{
        error_subject = '';
        $('#error_subject').text(error_subject);
      }
      var messageLength = CKEDITOR.instances['mail_body'].getData().replace(/<[^>]*>/gi, '').length;
      if(!messageLength){
        error_body = 'Mail body is required';
        $('#error_body').text(error_body);
      }
      else{
        error_body = '';
        $('#error_body').text(error_body);
      }

      var count = document.getElementById('no_of_file').innerHTML;
      if(count >= 3){
        error_file = 'You can send maximum 2 files';
        $('#error_file').text(error_file);
      }
      else{
        error_file = '';
        $('#error_file').text(error_file);
      }  

      if( error_email_addr != '' || error_subject != '' || error_body != '' || error_file != ''){
        return false;
      }
      else{
        $.confirm({
          title: 'Confirm!!',
          type: 'orange',
          icon: 'fa fa-warning',
          content: '<b>Are you sure ?</b>',
          buttons: {
            ok: function () {
              $('#submit_form').submit();
            },
            cancel: function () {
            }
          }
        });
      }
    });

  });

  function validateFile(id) {
    // console.log('call validate');
    var file = document.getElementById(id).files[0];
    var t = file.type.split('/').pop().toLowerCase();
    var size_KB = file.size / 1024;
    document.getElementById('size'+id).innerHTML = size_KB.toFixed(0)+' KB';
    document.getElementById('name'+id).innerHTML = file.name;

    file_no = document.getElementById('no_of_file').innerHTML;
    var file_no = Number(file_no) + 1;
    document.getElementById('no_of_file').innerHTML = file_no;
    if (file != undefined) {
        document.getElementById("row"+id).style.display = "";
    }
    return true;
  }

  function loadItems10(dist_code, element, path, selectInputClass) {
    $.ajax({
      type: 'GET',
      url: path + element +'/'+dist_code,
      success: function (datas) {
        $('#loader_img').hide();
        $('.content').removeClass('disabledContent');
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
        $('#loader_img').hide();
        $('.content').removeClass('disabledContent');
        ajax_error(jqXHR, textStatus, errorThrown);
      }
    });
  }

</script>