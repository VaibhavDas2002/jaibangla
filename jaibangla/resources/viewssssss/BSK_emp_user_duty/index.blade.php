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
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
  <div class="content-wrapper">
    <!-- <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" width="150px" id="loader_img"></div> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        BSK User Duty Management
      </h1>
      <ol class="breadcrumb">
        <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
      </ol>
    </section>
    <section class="content">
      <div class="box box-default">
        <div class="box-body">
          <div id="loadingDi"></div>
          
          <div id="res_div" style="display: block;">
            <div class="panel panel-default">
              <div class="panel-heading" id="panel_head" style="font-size: 16px; font-weight: bold; padding: 20px 15px;"><span style=" font-style: italic;">List of Users</span> <button id="addUser" class="btn btn-primary" style="float: right;">Add User and Assign Role</button></div>
              <div class="panel-body" style="padding: 5px; font-size: 14px;">
                <div class="table-responsive">
                  <table id="example" class="table display" cellspacing="0" width="100%"> 
                    <thead style="font-size: 12px;">
                      <th>Mapping Level</th>
                      <th width="40%">Location</th>
                      <th>Designation</th>
                      <th>Username</th>
                      <th>Mobile No</th>
                      <th>Email</th>
                      <th>Current Status</th>
                      <th>Action</th>
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

    <!-- Update Bank Details Modal -->
    <!-- Modal -->
    <div class="modal fade" id="modalAddUser" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Add User and Assign Role</h4>
            </div>
            <div class="modal-body">
              <div class="loadingDivModal"></div>
              <div class="panel" id="updateDiv">
                <div class="panel-body" style="padding: 0; margin: 0;">
                  <input type="hidden" name="dist_code" id="dist_code" value="{{ $dist_code }}" class="js-district">
                  <table class="table table-bordered table-responsive table-condensed" style="width:100%; font-size: 14px;" >
                    <tr>
                      <th>First Name: <span class="text-danger">*</span></th>
                      <td>
                        <input id="firstname" type="text" class="form-control" name="firstname" required autofocus maxlength="60"> 
                        <span id="error_first_name" class="text-danger"></span>
                      </td>
                    </tr>
                    <tr>
                      <th>Middle Name: </th>
                      <td>
                        <input id="middlename" type="text" class="form-control" name="middlename" required>  
                        <span id="error_middle_name" class="text-danger"></span>
                      </td>
                    </tr>
                    <tr>
                      <th>Last Name: </th>
                      <td>
                        <input id="lastname" type="text" class="form-control" name="lastname" required>  
                        <span id="error_last_name" class="text-danger"></span>
                      </td>
                    </tr>
                    <tr>
                      <th>UserName: <span class="text-danger">*</span></th>
                      <td>
                        <input id="username" type="text" class="form-control" name="username" required>  
                        <span id="error_username" class="text-danger"></span>
                      </td>
                    </tr>
                    <tr>
                      <th>Email: <span class="text-danger">*</span></th>
                      <td>
                        <input id="email" type="email" class="form-control" name="email" required>  
                        <span id="error_email" class="text-danger"></span>
                      </td>
                    </tr>
                    <tr>
                      <th>Mobile No: <span class="text-danger">*</span></th>
                      <td>
                        <input id="mobile_no" type="text" class="form-control" name="mobile_no" required maxlength="10">  
                        <span id="error_mobile" class="text-danger"></span>
                      </td>
                    </tr>
                    <tr>
                      <th>Role: <span class="text-danger">*</span></th>
                      <td>
                        <select class="form-control" name="designation_id_old" id="designation_id_old" required style="width: 100%;">
                            <!-- <option value="">--Select Role--</option> -->
                            @foreach ($designations as $designation)
                              <option selected value="{{$designation->id}}">{{$designation->name}}</option>
                            @endforeach
                        </select>  
                        <span id="error_designation" class="text-danger"></span>
                      </td>
                    </tr>
                    <tr>
                      <th>Scheme: <span class="text-danger">*</span></th>
                      <td>
                        <select  id="scheme" class="form-control select2" name="schemelist[]" multiple="multiple" required style="width: 100%;">
                          <!-- <option value="">--Select Scheme--</option> -->
                          @foreach ($schemes as $scheme)
                          <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                          @endforeach
                        </select>
                        <span id="error_scheme" class="text-danger"></span>
                      </td>
                    </tr>
                    <tr>
                      <th>Urban/Rural: <span class="text-danger">*</span></th>
                      <td>
                        <select name="urban_code" id="urban_code" class="form-control js-block-subdiv" style="width: 100%;">
                            <option value="">--Select  --</option>
                             @foreach ($levels as $key=>$value)
                            <option value="{{$key}}" > {{$value}}</option>
                            @endforeach
                        </select>
                        <span id="error_urban_code" class="text-danger"></span>
                      </td>
                    </tr>
                    <tr>
                      <th>Block/Sub Division: <span class="text-danger">*</span></th>
                      <td>
                        <select name="body_code" id="body_code" class="form-control js-localbody" style="width: 100%;">
                          <option value="">--Select Option --</option>
                        </select>
                        <span id="error_body_code" class="text-danger"></span>
                      </td>
                    </tr>
                  </table>
                  <div class="row" align="center">
                    <button type="button" class="btn btn-primary btn-lg" id="verifySubmit">Create</button>
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
<script src="{{ asset ("/js/site.js") }}"></script>
<script>
  $(document).ready(function(){
    // Live Clock
    var interval = setInterval(function () {
    var momentNow = moment();
      $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
      $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);

    $('#loadingDi').hide();
    var table='';
    if ( $.fn.DataTable.isDataTable('#example') ) {
      $('#example').DataTable().destroy();
    }
    table=$('#example').DataTable( {
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
        url: "{{ url('bskEmpUserGetData') }}",
        type: "post",
        data:function(d){
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
        { "data": "mapping_level" },
        { "data": "location" },
        { "data": "designation"},
        { "data": "username"},
        { "data": "mobile_no"},
        { "data": "email" },
        { "data": "current_status" },
        { "data": "action" },
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
        //'pdf','excel','print'
      ],
    });

    $('.js-block-subdiv').change(function() {
      district_code= $('.js-district').val();
      loadBlockSubdiv(this,district_code);
    });
  });

  $(document).on('click', '#addUser', function(){
    $('#firstname').val('');
    $('#middlename').val('');
    $('#lastname').val('');
    $('#mobile_no').val('');
    $('#username').val('');
    $('#email').val('');
    // $("#scheme option:selected").removeAttr("selected");
    $('#urban_code').val('');
    $('#body_code').val('');
    $('#modalAddUser').modal('show');
    $('.loadingDivModal').hide();
  });

  $(".NumOnly").keyup(function(event) {      
    $(this).val($(this).val().replace(/[^\d].+/, ""));
    if ((event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
  });
  
  function loadBlockSubdiv(element,service_category) { 
    $('.js-localbody').empty().append('<option value="">--  Select  --</option>');  
    loadItems(element,service_category, 'api/blocksubdiv/', '.js-localbody');
  }
  function loadItems(element,service_category, path, selectInputClass) {
    var selectedVal = $(element).val();
    if (selectedVal == -1) {
      return;
    }
    $.ajax({
    type: 'GET',
    url: path + selectedVal +'/'+ service_category,
    success: function (datas) {
      if (!datas || datas.length === 0) {
         return;
      }
      for (var  i = 0; i < datas.length; i++) {
        $(selectInputClass).append($('<option>', {
          value: datas[i].id,
          text: datas[i].name,
          id: datas[i].id
        }));
      }
    },
    error: function (ex) {
    }
    });
  }

  $(document).on('click', '#verifySubmit', function(){     
    var error_first_name =''; 
    var error_scheme =''; 
    // var error_last_name =''; 
    var error_username =''; 
    var error_mobile_no ='';
    var error_email = '';
    var error_designation = '';
    var error_urban_code= '';
    var error_body_code='';

    if($.trim($('#firstname').val()).length == 0)
    {
     error_first_name = 'First Name is required';
     $('#error_first_name').text(error_first_name);
     $('#firstname').addClass('has-error');
    }
    else
    {
     error_first_name = '';
     $('#error_first_name').text(error_first_name);
     $('#firstname').removeClass('has-error');
    }

    if($.trim($('#scheme').val()).length == 0)
    {
     error_scheme = 'Scheme is required';
     $('#error_scheme').text(error_scheme);
     $('#scheme').addClass('has-error');
    }
    else
    {
     error_scheme = '';
     $('#error_scheme').text(error_scheme);
     $('#scheme').removeClass('has-error');
    }

    // if($.trim($('#lastname').val()).length == 0)
    // {
    //  error_last_name = 'Last Name is required';
    //  $('#error_last_name').text(error_last_name);
    //  $('#lastname').addClass('has-error');
    // }
    // else
    // {
    //  error_last_name = '';
    //  $('#error_last_name').text(error_last_name);
    //  $('#lastname').removeClass('has-error');
    // }

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

    if($.trim($('#username').val()).length == 0)
    {
     error_username = 'Username is required';
     $('#error_username').text(error_username);
     $('#username').addClass('has-error');
    }
    else
    {
     error_username = '';
     $('#error_username').text(error_username);
     $('#username').removeClass('has-error');
    }

    if($.trim($('#email').val()).length == 0)
    {
     error_email = 'Email is required';
     $('#error_email').text(error_email);
     $('#email').addClass('has-error');
    }
    else
    {
     error_email = '';
     $('#error_email').text(error_email);
     $('#email').removeClass('has-error');
    }

    if($.trim($('#designation_id_old').val()).length == 0)
    {
     error_designation = 'Role is required';
     $('#error_designation').text(error_designation);
     $('#designation_id_old').addClass('has-error');
    }
    else
    {
     error_designation = '';
     $('#error_designation').text(error_designation);
     $('#designation_id_old').removeClass('has-error');
    }

    if($.trim($('#urban_code').val()).length == 0)
    {
     error_urban_code = 'Rural/Urban is required';
     $('#error_urban_code').text(error_urban_code);
     $('#urban_code').addClass('has-error');
    }
    else
    {
     error_urban_code = '';
     $('#error_urban_code').text(error_urban_code);
     $('#urban_code').removeClass('has-error');
    }

    if($.trim($('#body_code').val()).length == 0)
    {
     error_body_code = 'Block/ Sub-division is required';
     $('#error_body_code').text(error_body_code);
     $('#body_code').addClass('has-error');
    }
    else
    {
     error_body_code = '';
     $('#error_body_code').text(error_body_code);
     $('#body_code').removeClass('has-error');
    }

    if(error_first_name !='' || error_mobile_no !='' || error_email != '' || error_username != '' || error_designation != '' || error_urban_code != '' || error_body_code != '' || error_scheme != '') {
      // alert('error');
      return false;
    }
    else
    {
      // alert('Hi');
      $.confirm({
        type: 'orange',
        title: 'Confirmation!',
        content: 'Are you sure want to add this user ?',
        icon: 'fa fa-warning',
        buttons: {
          confirm: {
            text: 'Confirm',
            btnClass: 'btn-blue',
            keys: ['enter', 'shift'],
            action: function(){
              // alert('OK');
              var firstName = $('#firstname').val();
              var middleName = $('#middlename').val();
              var lastName = $('#lastname').val();
              var mobileNo = $('#mobile_no').val();
              var userName = $('#username').val();
              var email = $('#email').val();
              var role = $('#designation_id_old').val();
              var scheme = $('#scheme').val();
              var district_code = $('#dist_code').val();
              var urbanCode = $('#urban_code').val();
              var bodyCode = $('#body_code').val();
              $('.loadingDivModal').show();
              $.ajax({
                type: 'POST',
                url: "{{ route('bskAddUserEmp') }}",
                data: {
                  firstname: firstName,
                  middlename: middleName,
                  lastname: lastName,
                  mobile_no: mobileNo,
                  username: userName,
                  email: email,
                  designation_id_old: role,
                  schemelist: scheme,
                  urban_code : urbanCode,
                  body_code: bodyCode,
                  dist_code: district_code,
                  _token: '{{ csrf_token() }}',
                },
                success: function (response) {
                  $('.loadingDivModal').hide();
                  $('#example').DataTable().ajax.reload();
                  $('#modalAddUser').modal('hide');
                  if (response.status == 1) {
                    $.alert({
                      title: response.title,
                      type: response.type,
                      icon: response.icon,
                      content: response.msg
                    });
                    $('#modalAddUser').modal('hide');
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

  function clickToDisabled(value) {
    $.confirm({
      type: 'orange',
      title: 'Confirmation!',
      content: 'Are you sure want to disabled this user ?',
      icon: 'fa fa-warning',
      buttons: {
        confirm: {
          text: 'Confirm',
          btnClass: 'btn-blue',
          keys: ['enter', 'shift'],
          action: function(){
            $('#loadingDi').show();
            $.ajax({
              type: 'POST',
              url: "{{ route('bsk-enabledisable-config-duty-emp') }}",
              data: {
                id: value, _token: '{{ csrf_token() }}',
              },
              success: function (response) {
                $('#loadingDi').hide();
                $('#example').DataTable().ajax.reload();
                if (response.status == 1) {
                  $.alert({
                    title: response.title,
                    type: response.type,
                    icon: response.icon,
                    content: response.msg
                  });
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
              error: function (jqXHR, textStatus, errorThrown) {
                $('#loadingDi').hide();
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

  function clickToEnabled(value) {
    $.confirm({
      type: 'orange',
      title: 'Confirmation!',
      content: 'Are you sure want to enabled this user ?',
      icon: 'fa fa-warning',
      buttons: {
        confirm: {
          text: 'Confirm',
          btnClass: 'btn-blue',
          keys: ['enter', 'shift'],
          action: function(){
            $('#loadingDi').show();
            $.ajax({
              type: 'POST',
              url: "{{ route('bsk-enabledisable-config-duty-emp') }}",
              data: {
                id: value, _token: '{{ csrf_token() }}',
              },
              success: function (response) {
                $('#loadingDi').hide();
                $('#example').DataTable().ajax.reload();
                if (response.status == 1) {
                  $.alert({
                    title: response.title,
                    type: response.type,
                    icon: response.icon,
                    content: response.msg
                  });
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
              error: function (jqXHR, textStatus, errorThrown) {
                $('#loadingDi').hide();
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