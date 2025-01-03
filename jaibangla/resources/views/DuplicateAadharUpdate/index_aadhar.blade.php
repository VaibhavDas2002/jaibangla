<style type="text/css">
  .has-error {
    border-color: #cc0000;
    background-color: #ffff99;
  }

  .preloader1 {
    position: fixed;
    top: 40%;
    left: 52%;
    z-index: 999;
  }

  .preloader1 {
    background: transparent !important;
  }

  #loadingDi {
    position: absolute;
    top: 0px;
    right: 0px;
    width: 100%;
    height: 100%;
    background-color: #fff;
    background-image: url('images/ajaxgif.gif');
    background-repeat: no-repeat;
    background-position: center;
    z-index: 10000000;
    opacity: 0.4;
    filter: alpha(opacity=40);
    /* For IE8 and earlier */
  }

  .loadingDivModal {
    position: absolute;
    top: 0px;
    right: 0px;
    width: 100%;
    height: 100%;
    background-color: #fff;
    background-image: url('images/ajaxgif.gif');
    background-repeat: no-repeat;
    background-position: center;
    z-index: 10000000;
    opacity: 0.4;
    filter: alpha(opacity=40);
    /* For IE8 and earlier */
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
      De-duplicate Aadhar Card or Mobile Number
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
                    @if($mapLevel=='Subdiv')
                    <div class="col-md-3">
                      <label class=" control-label">Municipality</label>
                      <select name="filter_1" id="filter_1" class="form-control select2 full-width js-municipality">
                        <option value="">-----All----</option>
                        @foreach ($urban_bodys as $urban_body)
                        <option value="{{$urban_body->urban_body_code}}"> {{$urban_body->urban_body_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label class=" control-label">Wards</label>
                      <select name="filter_2" id="filter_2" class="form-control select2 full-width js-wards">
                        <option value="">-----All----</option>
                      </select>
                    </div>

                    @elseif($mapLevel=='Block')
                    <div class="col-md-3">
                      <label class=" control-label">Gram Panchayat</label>
                      <select name="filter_1" id="filter_1" class="form-control select2 full-width">
                        <option value="">-----All----</option>
                        @foreach ($gps as $gp)
                        <option value="{{$gp->gram_panchyat_code}}"> {{$gp->gram_panchyat_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    @endif
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
                    <th>Application ID</th>
                    <th>Applicant Name</th>
                    <th>Block/Municipality</th>
                    <th>GP/Ward</th>
                    <th>Aadhar No</th>
                    <th>Mobile No</th>
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

  <!-- Update Aadhar Details Modal -->
  <!-- Modal -->
  <div class="modal fade" id="modalUpdateAadhar" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update Aadhar Details</h4>
        </div>
        <div class="modal-body">
          <div class="loadingDivModal"></div>
          <div class="" id="updateDiv">
            <!-- <div class="panel-heading">Enter Bank Details</div>
                <div class="panel-body"> -->
            <div class="row">
              <div class="col-md-12">
                <h4 style="text-align: center;" class="text-primary">Application ID: <span id="application_id"></span></h4>
              </div>
            </div>
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
            </table>
            <input type="hidden" name="pension_id" id="pension_id" value="">
            <input type="hidden" name="update_scheme_id" id="update_scheme_id" value="">
            <input type="hidden" name="old_aadhar_no" id="old_aadhar_no" value="">
            <div class="table-responsive">
              <table class="table table-bordered table-responsive table-condensed" style="width:100%; font-size: 14px;">
                <tr>
                  <th>Aadhar Card Number: <span class="text-danger">*</span></th>
                  <td>
                    <input type="text" value="" id="new_aadhar_no" name="new_aadhar_no" maxlength="12" style="font-size: 16px;" onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;"> <br />
                    <span id="error_aadhar_no" class="text-danger"></span>
                  </td>
                </tr>
                <tr>
                  <th>Upload Aadhar Card: <span class="text-danger">*</span></th>
                  <td>
                    <input type="file" name="doc_6" id="doc_6">
                    <small style="font-weight: normal;" id='file_msg'></small> <br/>
                    <span class="text-danger" id="error_doc_6"></span>
                  </td>
                </tr>
                <tr>
                  <th>Remarks: </th>
                  <td>
                    <input type="text" name="remarks" id="remarks" class="form-control" value="" maxlength="100">
                    <small style="font-weight: normal;">Max 100 character allowed</small>
                  </td>
                </tr>
              </table>
            </div>
            <div class="row">
              <div class="col-md-12" style="text-align: center;"><input type="button" name="submit" value="Update" id="verifySubmit" class="btn btn-success btn-lg"></div>
            </div>
            <!-- </div> -->
          </div>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

  <!-- Update Mobile Number Modal -->
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
          <div class="" id="updateDiv">
            <!-- <div class="panel-heading">Enter Bank Details</div>
                <div class="panel-body"> -->
            <div class="row">
              <div class="col-md-12">
                <h4 style="text-align: center;" class="text-primary">Application ID: <span id="mob_application_id"></span></h4>
              </div>
            </div>
            <div id="benMobileDetails"></div>
            <input type="hidden" name="mob_pension_id" id="mob_pension_id" value="">
            <input type="hidden" name="mob_scheme_id" id="mob_scheme_id" value="">
            <input type="hidden" name="old_mobile_no" id="old_mobile_no" value="">
            <div class="table-responsive">
              <table class="table table-bordered table-responsive table-condensed" style="width:100%; font-size: 14px;">
                <tr>
                  <th>Mobile Number: <span class="text-danger">*</span></th>
                  <td>
                    <input type="text" value="" id="new_mobile_no" name="new_mobile_no" maxlength="10" style="font-size: 16px;" onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;"> <br />
                    <span id="error_mobile_no" class="text-danger"></span>
                  </td>
                </tr>
                <tr>
                  <th>Remarks: </th>
                  <td>
                    <input type="text" name="mob_remarks" id="mob_remarks" class="form-control" value="" maxlength="100">
                    <small style="font-weight: normal;">Max 100 character allowed</small>
                  </td>
                </tr>
              </table>
            </div>
            <div class="row">
              <div class="col-md-12" style="text-align: center;"><input type="button" name="submit" value="Update" id="verifySubmitMobile" class="btn btn-success btn-lg"></div>
            </div>
            <!-- </div> -->
          </div>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

    <!-- Update No Mobile Number Modal -->
  <!-- Modal -->
  <div class="modal fade" id="modalUpdateNoMobile" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update No Mobile Number</h4>
        </div>
        <div class="modal-body">
          <div class="loadingDivModal"></div>
          <div class="" id="updateDiv">
            <!-- <div class="panel-heading">Enter Bank Details</div>
                <div class="panel-body"> -->
            <div class="row">
              <div class="col-md-12">
                <h4 style="text-align: center;" class="text-primary">Application ID: <span id="no_mob_application_id"></span></h4>
              </div>
            </div>
            <div id="benNoMobileDetails"></div>
            <input type="hidden" name="no_mob_pension_id" id="no_mob_pension_id" value="">
            <input type="hidden" name="no_mob_scheme_id" id="no_mob_scheme_id" value="">
            <input type="hidden" name="old_no_mobile_no" id="old_no_mobile_no" value="">
            <div class="table-responsive">
              <table class="table table-bordered table-responsive table-condensed" style="width:100%; font-size: 14px;">
                <tr>
                  <th>Mobile Number: <span class="text-danger">*</span></th>
                  <td>
                    <input type="text" value="" id="new_no_mobile_no" name="new_no_mobile_no" maxlength="10" style="font-size: 16px;" onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;"> <br />
                    <span id="error_no_mobile_no" class="text-danger"></span>
                  </td>
                </tr>
                <tr>
                  <th>Remarks: </th>
                  <td>
                    <input type="text" name="no_mob_remarks" id="no_mob_remarks" class="form-control" value="" maxlength="100">
                    <small style="font-weight: normal;">Max 100 character allowed</small>
                  </td>
                </tr>
              </table>
            </div>
            <div class="row">
              <div class="col-md-12" style="text-align: center;"><input type="button" name="submit" value="Update" id="verifySubmitNoMobile" class="btn btn-success btn-lg"></div>
            </div>
            <!-- </div> -->
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
  $(document).ready(function() {
    // Live Clock
    var interval = setInterval(function() {
      var momentNow = moment();
      $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
      $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);

    $('#loadingDi').hide();
    $('#submit_btn').removeAttr('disabled');
    $('#reset_btn').removeAttr('disabled');

    var error_scheme_type = '';
    $('#submit_btn').click(function() {
      if ($.trim($('#scheme_type').val()).length == 0) {
        error_scheme_type = 'Scheme name is required';
        $('#error_scheme_type').text(error_scheme_type);
      } else {
        error_scheme_type = '';
        $('#error_scheme_type').text(error_scheme_type);
      }

      if (error_scheme_type != '') {
        return false;
      } else {
        $('#loadingDi').show();
        $('#res_div').show();
        var msg = 'Scheme : ' + $("#scheme_type option:selected").text();
        $('#panel_head').text(msg);
        if ($.fn.DataTable.isDataTable('#example')) {
          $('#example').DataTable().destroy();
        }
        var table = $('#example').DataTable({
          dom: 'Blfrtip',
          "scrollX": true,
          "paging": true,
          "searchable": true,
          "ordering": false,
          "bFilter": true,
          "bInfo": true,
          "pageLength": 20,
          'lengthMenu': [
            [10, 20, 25, 50, 100, -1],
            [10, 20, 25, 50, 100, 'All']
          ],
          "serverSide": true,
          "processing": true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
          },
          "ajax": {
            url: "{{ url('getDuplicateAadharListView') }}",
            type: "post",
            data: function(d) {
              d.scheme_id = $('#scheme_type').val(),
                d.filter_1 = $('#filter_1').val(),
                d.filter_2 = $('#filter_2').val(),
                d._token = "{{csrf_token()}}"
            },
            error: function(jqXHR, textStatus, errorThrown) {
              $('#loadingDi').hide();
              $('.preloader1').hide();
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete": function() {
            $('#loadingDi').hide();
            //console.log('Data rendered successfully');
          },
          "columns": [{
              "data": "id"
            },
            {
              "data": "name"
            },
            {
              "data": "block_ulb_name"
            },
            {
              "data": "gp_ward_name"
            },
            {
              "data": "aadhar_no"
            },
            {
              "data": "mobile_no"
            },
            {
              "data": "view"
            },
          ],

          "buttons": [{
              extend: 'pdf',
              footer: true,
              pageSize: 'A4',
              //orientation: 'landscape',
              pageMargins: [40, 60, 40, 60],
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5],

              }
            },
            {
              extend: 'excel',
              footer: true,
              pageSize: 'A4',
              //orientation: 'landscape',
              pageMargins: [40, 60, 40, 60],
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5],
                stripHtml: false,
              }
            },
            //'pdf','excel','print'
          ],
        });
      }
    });
    $('.js-municipality').change(function() {
      municipality = $('.js-municipality').val();
      loadGPWard_1(municipality);
      // console.log('on change municipality:'+municipality);   
    });

    function loadGPWard_1(municipality) {
      $('.js-wards').empty().append('<option value="">-- Select --</option>');
      loadwards1(municipality, '../api/gpward/', '.js-wards');
    }

    function loadwards1(municipality, path, selectInputClass) {
      var selectedVal = municipality;
      if (selectedVal == -1) {
        return;
      }
      // alert(path +'1/'+ selectedVal);
      $.ajax({
        type: 'GET',
        url: path + '1/' + selectedVal,
        success: function(datas) {
          if (!datas || datas.length === 0) {
            //alert("sucess with 0 data");
            return;
          }
          //alert('success url:'paths);
          for (var i = 0; i < datas.length; i++) {
            $(selectInputClass).append($('<option>', {
              //value: datas[i].name,
              value: datas[i].id,
              text: datas[i].name,
              id: datas[i].id
            }));
          }
        },
        error: function(ex) {
          //alert('error url:'paths);
        }
      });
    }

    $('#doc_6').change(function(){
      var card_file=document.getElementById("doc_6");
      if(card_file.value!="")
      {
        var attachment;
        attachment = card_file.files[0];
        // console.log(attachment.type)
        var type = attachment.type;
        // if(attachment.size>1048576)
        // {
        //   document.getElementById("error_file").innerHTML="<i class='fa fa-warning'></i> Unaccepted document file size. Max size 1024 KB. Please try again";
        //   $('#file_stop_payment').val('');
        //   return false;
        // }
        if (type != 'image/jpeg' && type != 'image/png' && type != 'application/pdf') {
          document.getElementById("error_doc_6").innerHTML="<i class='fa fa-warning'></i> Unaccepted document file format. Only jpeg,jpg,png and pdf. Please try again";
          $('#doc_6').val('');
          return false;
        }
        else{
          $('#doc_6').show();
          document.getElementById("error_doc_6").innerHTML="";
        }
      }
    });
  });

  function editAadharFunction(value, scheme_id) {
    $('#loadingDi').show();
    $.ajax({
      type: 'post',
      url: "{{ route('getDuplicateAadharBenModalView') }}",
      data: {
        scheme_id: scheme_id,
        id: value,
        _token: '{{ csrf_token() }}'
      },
      success: function(response) {
        $('#loadingDi').hide();
        // console.log(response);
        if (response.status == 1) {
          $.alert({
            title: response.title,
            type: response.type,
            icon: response.icon,
            content: response.msg
          });
        } else {
          $('#update_scheme_id').val('');
          $('#pension_id').val('');
          $('#old_aadhar_no').val('');
          $('#new_aadhar_no').val('');
          $('#doc_6').val('');
          $('#remarks').val('');
          $('#new_aadhar_no').removeClass('has-error');
          $('#doc_6').removeClass('has-error');
          $('#error_aadhar_no').text('');
          $('#error_doc_6').text('');
          $('#name_div').text(response.ben_name);
          $('#father_div').text(response.father_name);
          $('#dob_div').text(response.dob);
          $('#gender_div').text(response.gender);
          $('#caste_div').text(response.caste);
          $('#update_scheme_id').val(response.scheme_id);
          $('#pension_id').val(response.id);
          $('#old_aadhar_no').val(response.aadhar_no);
          $('#application_id').text(response.id);
          var file_msg = '(Image type must be '+response.doc_type+' and image size max '+response.doc_size_kb+' KB)';
          $('#file_msg').text(file_msg);
          $('.loadingDivModal').hide();
          $('#modalUpdateAadhar').modal('show');
        }
      },
      complete: function() {},
      error: function(jqXHR, textStatus, errorThrown) {
        $('#loadingDi').hide();
        ajax_error(jqXHR, textStatus, errorThrown);
      }
    });
  }

  function editMobileFunction(value, scheme_id) {
    $('#loadingDi').show();
    $.ajax({
      type: 'post',
      url: "{{ route('getDuplicateMobileBenModalView') }}",
      data: {
        scheme_id: scheme_id,
        id: value,
        _token: '{{ csrf_token() }}'
      },
      success: function(response) {
        $('#loadingDi').hide();
        // console.log(response);
        if (response.status == 1) {
          $.alert({
            title: response.title,
            type: response.type,
            icon: response.icon,
            content: response.msg
          });
        } else {
          $('#mob_scheme_id').val('');
          $('#mob_pension_id').val('');
          $('#old_mobile_no').val('');
          $('#new_mobile_no').val('');
          $('#mob_remarks').val('');
          $('#new_mobile_no').removeClass('has-error');
          $('#error_mobile_no').text('');
          var benMobileDetails_msg = '';
          benMobileDetails_msg += '<b>Name - '+response.ben_name+'<br> Father\'s Name - '+response.father_name+'<br>Gender - '+response.gender+'<br>Caste - '+response.caste+'</b>';
          $('#benMobileDetails').html(benMobileDetails_msg);
          $('#mob_scheme_id').val(response.scheme_id);
          $('#mob_pension_id').val(response.id);
          $('#old_mobile_no').val(response.mobile_no);
          $('#mob_application_id').text(response.id);
          $('.loadingDivModal').hide();
          $('#modalUpdateMobile').modal('show');
        }
      },
      complete: function() {},
      error: function(jqXHR, textStatus, errorThrown) {
        $('#loadingDi').hide();
        ajax_error(jqXHR, textStatus, errorThrown);
      }
    });
  }

  function editNoMobileFunction(value, scheme_id) {
    $('#loadingDi').show();
    $.ajax({
      type: 'post',
      url: "{{ route('getNoMobileBenModalView') }}",
      data: {
        scheme_id: scheme_id,
        id: value,
        _token: '{{ csrf_token() }}'
      },
      success: function(response) {
        $('#loadingDi').hide();
        // console.log(response.id);
        if (response.status == 1) {
          $.alert({
            title: response.title,
            type: response.type,
            icon: response.icon,
            content: response.msg
          });
        } else {
          $('#no_mob_scheme_id').val('');
          $('#no_mob_pension_id').val('');
          $('#old_no_mobile_no').val('');
          $('#new_no_mobile_no').val('');
          $('#mob_remarks').val('');
          $('#no_mob_remarks').removeClass('has-error');
          $('#error_no_mobile_no').text('');
          var benMobileDetails_msg = '';
          benMobileDetails_msg += '<b>Name - '+response.ben_name+'<br> Father\'s Name - '+response.father_name+'<br>Gender - '+response.gender+'<br>Caste - '+response.caste+'</b>';
          $('#benNoMobileDetails').html(benMobileDetails_msg);
          $('#no_mob_scheme_id').val(response.scheme_id);
          $('#no_mob_pension_id').val(response.id);
          $('#old_no_mobile_no').val(response.mobile_no);
          $('#no_mob_application_id').text(response.id);
          $('.loadingDivModal').hide();
          $('#modalUpdateNoMobile').modal('show');
        }
      },
      complete: function() {},
      error: function(jqXHR, textStatus, errorThrown) {
        $('#loadingDi').hide();
        ajax_error(jqXHR, textStatus, errorThrown);
      }
    });
  }

  $(document).on('click', '#verifySubmit', function() {
    var error_aadhar_no = '';
    var error_doc_6 = '';
    var aadhar_no = $('#new_aadhar_no').val();
    if ($.trim($('#new_aadhar_no').val()).length == 0) {
      error_aadhar_no = 'Aadhar Card Number is required';
      $('#error_aadhar_no').text(error_aadhar_no);
      $('#new_aadhar_no').addClass('has-error');
    } else if ($.trim($('#new_aadhar_no').val()).length != 12) {
      error_aadhar_no = 'Aadhar Card Number must be 12 digits';
      $('#error_aadhar_no').text(error_aadhar_no);
      $('#new_aadhar_no').addClass('has-error');
    } else {
      var valid = validate(aadhar_no);
      if (valid) {
        error_aadhar_no = '';
        $('#error_aadhar_no').text(error_aadhar_no);
        $('#new_aadhar_no').removeClass('has-error');
      } else {
        error_aadhar_no = 'Aadhaar Number Invalid';
        $('#error_aadhar_no').text(error_aadhar_no);
        $('#new_aadhar_no').addClass('has-error');
      }

    }

    var file_sp = document.getElementById("doc_6");
    var file_attachment = file_sp.files[0];
    if(file_sp.value!='') {
      error_doc_6 = '';
        $('#error_doc_6').text(error_doc_6);
        $('#doc_6').removeClass('has-error');
    } else {
      error_doc_6 = 'Aadhar Card is required';
      $('#error_doc_6').text(error_doc_6);
      $('#doc_6').addClass('has-error');
    }

    if (error_aadhar_no != '' || error_doc_6 != '') {
      return false;
    } else {
      // alert('OK');
      $.confirm({
        type: 'orange',
        title: 'Confirmation!',
        content: 'Are you sure want to update aadhar no of this beneficiary ?',
        icon: 'fa fa-warning',
        buttons: {
          confirm: {
            text: 'Confirm',
            btnClass: 'btn-blue',
            keys: ['enter', 'shift'],
            action: function() {
              // alert('OK');
              var beneficiary_Id = $('#pension_id').val();
              var updateSchemeId = $('#update_scheme_id').val();
              var old_aadhar_no = $('#old_aadhar_no').val();
              var new_aadhar_no = $('#new_aadhar_no').val();
              var remarks = $('#remarks').val();
              var formData = new FormData();
              var files = $('#doc_6')[0].files;
              formData.append('doc_6', files[0]);
              formData.append('id', beneficiary_Id);
              formData.append('scheme_id', updateSchemeId);
              formData.append('new_aadhar_no', new_aadhar_no);
              formData.append('old_aadhar_no', old_aadhar_no);
              formData.append('remarks', remarks);
              formData.append('_token', '{{ csrf_token() }}');
              $('.loadingDivModal').show();
              $.ajax({
                type: 'POST',
                url: "{{ route('updateDeDuplicateBenAadharDetails') }}",
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response) {
                  $('.loadingDivModal').hide();
                  if (response.status == 1) {
                    $.alert({
                      title: response.title,
                      type: response.type,
                      icon: response.icon,
                      content: response.msg
                    });
                    $('#modalUpdateAadhar').modal('hide');
                    $('#res_div').hide();
                    // $('#scheme_type').val('').trigger('change');
                    $('#submit_btn').trigger('click');
                    $("html, body").animate({
                      scrollTop: 0
                    }, "slow");
                  } else {
                    var html = '';
                    html += '<ul>';
                    if (Array.isArray(response.msg)) {
                      $.each(response.msg, function(key, value) {
                        html += '<li>' + value + '</li>';
                      });
                    } else {
                      html = '<li>' + response.msg + '</li>';
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
                error: function(jqXHR, textStatus, errorThrown) {
                  $('.loadingDivModal').hide();
                  ajax_error(jqXHR, textStatus, errorThrown);
                }
              });
            }
          },
          cancel: function() {},
        }
      });
    }
  });

  $(document).on('click', '#verifySubmitMobile', function() {
    var error_mobile_no = '';
    var mobile_no = $.trim($('#new_mobile_no').val());
    if ($.trim($('#new_mobile_no').val()).length == 0) {
      error_mobile_no = 'Mobile Number is required';
      $('#error_mobile_no').text(error_mobile_no);
      $('#new_mobile_no').addClass('has-error');
    } else if ($.trim($('#new_mobile_no').val()).length != 10) {
      error_mobile_no = 'Mobile Number must be 10 digits';
      $('#error_mobile_no').text(error_mobile_no);
      $('#new_mobile_no').addClass('has-error');
    } else if (Number(mobile_no) < 1000000000) {
      error_mobile_no = 'Please enter valid mobile number';
      $('#error_mobile_no').text(error_mobile_no);
      $('#new_mobile_no').addClass('has-error');
    } else {
      error_mobile_no = '';
      $('#error_mobile_no').text(error_mobile_no);
      $('#new_mobile_no').removeClass('has-error');
    }

    if (error_mobile_no != '') {
      return false;
    } else {
      // alert('OK');
      $.confirm({
        type: 'orange',
        title: 'Confirmation!',
        content: 'Are you sure want to update mobile no of this beneficiary ?',
        icon: 'fa fa-warning',
        buttons: {
          confirm: {
            text: 'Confirm',
            btnClass: 'btn-blue',
            keys: ['enter', 'shift'],
            action: function() {
              // alert('OK');
              var beneficiary_Id = $('#mob_pension_id').val();
              var updateSchemeId = $('#mob_scheme_id').val();
              var old_mobile_no = $('#old_mobile_no').val();
              var new_mobile_no = $('#new_mobile_no').val();
              var remarks = $('#mob_remarks').val();
              var formData = new FormData();
              formData.append('id', beneficiary_Id);
              formData.append('scheme_id', updateSchemeId);
              formData.append('new_mobile_no', new_mobile_no);
              formData.append('old_mobile_no', old_mobile_no);
              formData.append('remarks', remarks);
              formData.append('_token', '{{ csrf_token() }}');
              $('.loadingDivModal').show();
              $.ajax({
                type: 'POST',
                url: "{{ route('updateDeDuplicateBenMobileNoDetails') }}",
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response) {
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
                    // $('#scheme_type').val('').trigger('change');
                    $('#submit_btn').trigger('click');
                    $("html, body").animate({
                      scrollTop: 0
                    }, "slow");
                  } else {
                    var html = '';
                    html += '<ul>';
                    if (Array.isArray(response.msg)) {
                      $.each(response.msg, function(key, value) {
                        html += '<li>' + value + '</li>';
                      });
                    } else {
                      html = '<li>' + response.msg + '</li>';
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
                error: function(jqXHR, textStatus, errorThrown) {
                  $('.loadingDivModal').hide();
                  ajax_error(jqXHR, textStatus, errorThrown);
                }
              });
            }
          },
          cancel: function() {},
        }
      });
    }
  }); //verifySubmitNoMobile

  $(document).on('click', '#verifySubmitNoMobile', function() {
    var error_no_mobile_no = '';
    var mobile_no = $.trim($('#new_no_mobile_no').val());
    if ($.trim($('#new_no_mobile_no').val()).length == 0) {
      error_no_mobile_no = 'Mobile Number is required';
      $('#error_no_mobile_no').text(error_no_mobile_no);
      $('#new_no_mobile_no').addClass('has-error');
    } else if ($.trim($('#new_no_mobile_no').val()).length != 10) {
      error_no_mobile_no = 'Mobile Number must be 10 digits';
      $('#error_no_mobile_no').text(error_no_mobile_no);
      $('#new_no_mobile_no').addClass('has-error');
    } else if (Number(mobile_no) < 1000000000) {
      error_no_mobile_no = 'Please enter valid mobile number';
      $('#error_no_mobile_no').text(error_no_mobile_no);
      $('#new_no_mobile_no').addClass('has-error');
    } else {
      error_no_mobile_no = '';
      $('#error_no_mobile_no').text(error_no_mobile_no);
      $('#new_no_mobile_no').removeClass('has-error');
    }

    if (error_no_mobile_no != '') {
      return false;
    } else {
      // alert('OK');
      $.confirm({
        type: 'orange',
        title: 'Confirmation!',
        content: 'Are you sure want to update mobile no of this beneficiary ?',
        icon: 'fa fa-warning',
        buttons: {
          confirm: {
            text: 'Confirm',
            btnClass: 'btn-blue',
            keys: ['enter', 'shift'],
            action: function() {
              // alert('OK');
              var beneficiary_Id = $('#no_mob_pension_id').val();
              var updateSchemeId = $('#no_mob_scheme_id').val();
              var old_mobile_no = $('#old_no_mobile_no').val();
              var new_mobile_no = $('#new_no_mobile_no').val();
              var remarks = $('#no_mob_remarks').val();
              var formData = new FormData();
              formData.append('id', beneficiary_Id);
              formData.append('scheme_id', updateSchemeId);
              formData.append('new_no_mobile_no', new_mobile_no);
              formData.append('old_no_mobile_no', old_mobile_no);
              formData.append('remarks', remarks);
              formData.append('_token', '{{ csrf_token() }}');
              $('.loadingDivModal').show();
              $.ajax({
                type: 'POST',
                url: "{{ route('updateNoBenMobileDetails') }}",
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response) {
                  $('.loadingDivModal').hide();
                  if (response.status == 1) {
                    $.alert({
                      title: response.title,
                      type: response.type,
                      icon: response.icon,
                      content: response.msg
                    });
                    $('#modalUpdateNoMobile').modal('hide');
                    $('#res_div').hide();
                    // $('#scheme_type').val('').trigger('change');
                    $('#submit_btn').trigger('click');
                    $("html, body").animate({
                      scrollTop: 0
                    }, "slow");
                  } else {
                    var html = '';
                    html += '<ul>';
                    if (Array.isArray(response.msg)) {
                      $.each(response.msg, function(key, value) {
                        html += '<li>' + value + '</li>';
                      });
                    } else {
                      html = '<li>' + response.msg + '</li>';
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
                error: function(jqXHR, textStatus, errorThrown) {
                  $('.loadingDivModal').hide();
                  ajax_error(jqXHR, textStatus, errorThrown);
                }
              });
            }
          },
          cancel: function() {},
        }
      });
    }
  });

  // Verhoeff algorithm for checking aadhar no
  var d = [
    [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
    [1, 2, 3, 4, 0, 6, 7, 8, 9, 5],
    [2, 3, 4, 0, 1, 7, 8, 9, 5, 6],
    [3, 4, 0, 1, 2, 8, 9, 5, 6, 7],
    [4, 0, 1, 2, 3, 9, 5, 6, 7, 8],
    [5, 9, 8, 7, 6, 0, 4, 3, 2, 1],
    [6, 5, 9, 8, 7, 1, 0, 4, 3, 2],
    [7, 6, 5, 9, 8, 2, 1, 0, 4, 3],
    [8, 7, 6, 5, 9, 3, 2, 1, 0, 4],
    [9, 8, 7, 6, 5, 4, 3, 2, 1, 0]
  ];

  // permutation table p
  var p = [
    [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
    [1, 5, 7, 6, 2, 8, 3, 0, 9, 4],
    [5, 8, 0, 3, 7, 9, 6, 1, 4, 2],
    [8, 9, 1, 6, 0, 4, 3, 5, 2, 7],
    [9, 4, 5, 3, 1, 2, 6, 8, 7, 0],
    [4, 2, 8, 6, 5, 7, 3, 9, 0, 1],
    [2, 7, 9, 3, 8, 0, 6, 4, 1, 5],
    [7, 0, 4, 6, 9, 1, 3, 2, 5, 8]
  ];

  // inverse table inv
  var inv = [0, 4, 3, 2, 1, 5, 6, 7, 8, 9];

  // converts string or number to an array and inverts it
  function invArray(array) {

    if (Object.prototype.toString.call(array) == "[object Number]") {
      array = String(array);
    }

    if (Object.prototype.toString.call(array) == "[object String]") {
      array = array.split("").map(Number);
    }

    return array.reverse();

  }

  // generates checksum
  function generate(array) {

    var c = 0;
    var invertedArray = invArray(array);

    for (var i = 0; i < invertedArray.length; i++) {
      c = d[c][p[((i + 1) % 8)][invertedArray[i]]];
    }

    return inv[c];
  }

  // validates checksum
  function validate(array) {

    var c = 0;
    var invertedArray = invArray(array);

    for (var i = 0; i < invertedArray.length; i++) {
      c = d[c][p[(i % 8)][invertedArray[i]]];
    }

    return (c === 0);
  }
  // End Verhoeff algorithm for checking aadhar no

  function ajax_error(jqXHR, textStatus, errorThrown) {
    var msg = "<strong>Failed to Load data.</strong><br/>";
    if (jqXHR.status !== 422 && jqXHR.status !== 400) {
      msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
    } else {
      if (jqXHR.responseJSON.hasOwnProperty('exception')) {
        msg += "Exception: <strong>" + jqXHR.responseJSON.exception_message + "</strong>";
      } else {
        msg += "Error(s):<strong><ul>";
        $.each(jqXHR.responseJSON, function(key, value) {
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