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
  .color1{
    
    background-color: #dcdfdf;
  }
  .color1 h3{
  margin: 10px 0px 10px 0px !important;
  }
    .preloader1 {
      background: transparent !important;
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
    <section class="content-header">
      <h1>
        Re-activate Beneficiary
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
            <div class="panel-heading" style="font-size: 15px; font-weight: bold; font-style: italic;"><span id="panel-icon">Enter Filter Criteria</div>
            <div class="panel-body" style="padding: 5px;">
              <div class="row">
                <div class="col-md-12">
                  @if ($message = Session::get('success'))
                  <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }} </strong>
                  </div>
                  @endif
                  @if ($message = Session::get('message'))
                  <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                  </div>
                  @endif
                  @if ($message = Session::get('msg1'))
                  <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                  </div>
                  @endif
                  <div class="row">
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        <div class="col-md-3">
                            <label class=" control-label">Beneficiary ID <span class="text-danger">*</span></label>
                            <input type="text" name="ben_id" id="ben_id" class="form-control" value="1809540" readonly>
                            <span class="text-danger " id="error_ben_id"></span>
                           
                        </div>
                        <div class="col-md-3">
                            <label class=" control-label">Scheme <span class="text-danger">*</span></label>
                            <select class="form-control" name="scheme_id" id='scheme_id' required>
                            <option value="">--Select Scheme--</option>
                            @foreach ($schemes as $scheme)
                            <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}
                            </option>
                            @endforeach
                            </select>
                            <span class="text-danger" id="error_scheme_id"></span>
                        </div>
  
                    </div>
                  </div>
                  <div class="row">
                    <div style="text-align: center;">
                      <button class="btn btn-primary" name="submit_btn" id="submit_btn" type="button" disabled><i class="fa fa-search"></i>Search</button>&nbsp;
                      {{-- <button class="btn btn-default" name="reset_btn" id="reset_btn" type="button" disabled><i class="fa fa-refresh"></i> Reset</button> --}}
                    </div>
                  </div>
                  {{-- <hr />
                  <div class="row">
                    <div class="form-group col-md-offset-4 col-md-3 " style="display: none;" id="approve_rejdiv">
                      <button type="button" name="bulk_approve" class="btn btn-success btn-lg" id="bulk_approve" value="approve">
                        Approve</button>
                    </div>
                  </div> --}}
                </div>
              </div>
            </div>
          </div>
  
          <div class="alert print-error-msg" style="display:none;" id="errorDiv">
            <button type="button" class="close" aria-label="Close" onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
            <ul></ul>
          </div>
  
  
          <div id="res_div" style="display: none;">
            <div class="panel panel-default">
              <div class="panel-heading" id="panel_head" style="font-size: 14px; font-weight: bold; font-style: italic;">List of Beneficiary</div>
              <div class="panel-body" style="padding: 5px; font-size: 14px;">
                <div class="table-responsive">
                  <table id="example" class="table display" cellspacing="0" width="100%">
                    <thead style="font-size: 12px;">
                      <th>Application ID</th>
                      <th>Name</th>
                      <th>Block/Municipality</th>
                      <th>GP/Ward</th>
                      <th>Aadhar No.</th>
                      <th>Mobile No.</th>
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
  
    <div class="modal fade bd-example-modal-lg ben_view_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Beneficiary Details</h4>
          </div>
          <div class="modal-body ben_view_body">
            <div class="loadingDivModal"></div>
            <div class="panel panel-default" id="personal">
              <div class="panel-heading active" role="tab">
                <h4 class="panel-title">
                  <b>Personal Details</b> <span class="applicant_id_modal"></span>
                </h4>
              </div>
               <div class="panel-body" style="padding: 5px;">
                <div id="benMobileDetails"></div>
              </div> 
            </div>
  
            {{-- <div class="row color1" style="margin-bottom: 30px; " id="image">
                          <div class="col-md-12"><h3>Enclosure List(Self Attested)</h3></div>
                      
            </div> --}}
            {{-- <div class="row" id="stopPayment">
              <div class="col-md-4">
                                  <strong> Stop Payment Certificate:</strong> 
              </div>
              <div class="col-md-8" style="padding-bottom: 30px; ">
              <div id='imgLocation' class="card-body" >
              
                
              </div>
              <div id="modalDownload" ></div>
              </div>
            </div> --}}
  {{-- 
            <div class="card">
              <div class="panel-heading" role="tab">
                
                
              </div>
              
           
                
              </div> -->
            </div>
  
            <div class="card">
              <div id="modalDownload" ></div>
            </div> --}}
  
  
            <div class="panel panel-default">
             
              <div class="panel-body" style="padding: 5px;">
               
                <div class="form-group col-md-4">
                  <label class="" for="heading">Enter Remarks</label>
                  <textarea style="margin: 0px; width: 279px; height: 40px;" name="accept_reject_comments" id="accept_reject_comments" class="form-control" maxlength="100"></textarea>
                </div>
              </div>
            </div> 
  
            <form method="POST" action="#" target="_blank" name="fullForm" id="fullForm" style="text-align: center; align-content: center;">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="is_bulk" id="is_bulk" value="0" />
              <input type="hidden" id="id" name="id" />
              <input type="hidden" id="application_id" name="application_id" />
              <input type="hidden" name="applicantId[]" id="applicantId" value="" />
  
              <button type="button" class="btn btn-success btn-lg" id="verifyReject">Approve</button>
              <button style="display:none;" type="button" id="submitting" value="Submit" class="btn btn-success success" disabled>Processing Please Wait</button>
            </form> 
          </div>
        </div>
      </div>
    </div>
  
  </div>
  @endsection
  <script src="{{ asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
  <script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
  <script>
    $(document).ready(function() {
      // Live Clock
      var interval = setInterval(function() {
        var momentNow = moment();
        $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
        $('.time-part').html(momentNow.format('hh:mm:ss A'));
      }, 100);
      $('#loadingDiv').hide();
      $('#submit_btn').removeAttr('disabled');
      $('#reset_btn').removeAttr('disabled');
  
      // Master drop down 
      $('#urban_code').change(function() {
        var urban_code = $(this).val();
        // alert(urban_code);
        if (urban_code == '') {
          $('#muncid').html('<option value="">--All --</option>');
        }
        $('#block').html('<option value="">--All --</option>');
        select_district_code = $('#district').val();
        if (select_district_code == '') {
          alert('Please Select District First');
          $("#district").focus();
          $("#urban_code").val('');
        } else {
          select_body_type = urban_code;
          var htmlOption = '<option value="">--All--</option>';
          $("#gp_ward_div").show();
          if (select_body_type == 2) {
            $("#blk_sub_txt").text('Block');
            $.each(blocks, function(key, value) {
              if (value.district_code == select_district_code) {
                htmlOption += '<option value="' + value.id + '">' + value.text +
                  '</option>';
              }
            });
          } else if (select_body_type == 1) {
            $("#blk_sub_txt").text('Subdivision');
            $.each(subDistricts, function(key, value) {
              if (value.district_code == select_district_code) {
                htmlOption += '<option value="' + value.id + '">' + value.text +
                  '</option>';
              }
            });
          } else {
            $("#blk_sub_txt").text('Block/Subdivision');
          }
          $('#block').html(htmlOption);
        }
      });
  
      // End Master drop down
  
      // Filter
      // --------------- Filter Section -------------------- //
      $('#submit_btn').click(function() {
        var error_ben_id = '';
        var error_ben_id = '';
        if ($.trim($('#ben_id').val()).length == 0) {
            error_ben_id = 'Beneficiary ID is required';
          $('#error_ben_id').text(error_ben_id);
        } else {
            error_ben_id = '';
          $('#error_ben_id').text(error_ben_id);
        }
  
        if ($.trim($('#scheme_id').val()).length == 0) {
          error_scheme_id = 'Scheme name is required';
          $('#error_scheme_id').text(error_scheme_id);
        } else {
          error_scheme_id = '';
          $('#error_scheme_id').text(error_scheme_id);
        }
  
        if (error_scheme_id != '' || error_ben_id != '') {
          return false;
        } else {
          $('#loadingDi').show();
          $('#res_div').show();
          dataTable.ajax.reload();
        }
      });
      // End Filter
  
      var dataTable = '';
      if ($.fn.DataTable.isDataTable('#example')) {
        $('#example').DataTable().destroy();
      }
      var dataTable = $('#example').DataTable({
        dom: 'Blfrtip',
        "scrollX": true,
        "paging": true,
        "searchable": true,
        "ordering": false,
        "bFilter": true,
        "bInfo": true,
        "pageLength": 10,
        'lengthMenu': [
          [10, 20],
          [10, 20]
        ],
        "serverSide": true,
        "processing": true,
        "bRetrieve": true,
        "oLanguage": {
          "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
        },
        "ajax": {
          url: "{{ route('inactiveBeneficiaryDetails') }}",
          type: "post",
          data: function(d) {
              d.scheme_id = $('#scheme_id').val(),
              d.urban_code = $('#urban_code').val(),
              d.block = $('#block').val(),
              d.ben_id=$('#ben_id').val(),
              d._token = "{{ csrf_token() }}"
          },
          error: function(jqXHR, textStatus, errorThrown) {
            $('#loadingDi').hide();
            $('.preloader1').hide();
            ajax_error(jqXHR, textStatus, errorThrown);
            // $.alert({
            //   title: 'Error!!',
            //   type: 'red',
            //   icon: 'fa fa-warning',
            //   content: 'Loading Error! Session timeout, please logout and login again.'
            // });
          }
        },
        "initComplete": function() {
          $('#loadingDi').hide();
          var data = dataTable.rows().data();
          if (data.length == 0) {
            $('#res_div').hide();
          } else {
            $('#res_div').show();
          }
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
            "data": "aadhar_mask"
          },
          {
            "data": "mobile_mask"
          },
          {
            "data": "view"
          },
         
        ],
        "columnDefs": [{
          "targets": [6],
          "orderable": false,
          "searchable": false
        }],
  
        "buttons": [{
            extend: 'pdf',
            title: "Stop Payment Details Report  Generated On-@php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp ",
            messageTop: "Date: @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo $date;@endphp",
            footer: true,
            pageSize: 'A4',
            orientation: 'landscape',
            pageMargins: [40, 60, 40, 60],
            exportOptions: {
              columns: [0, 1, 2, 3, 4, 5],
            }
          },
          {
            extend: 'excel',
            title: "Stop Payment Details Report  Generated On-@php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp ",
            messageTop: "Date: @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo $date;@endphp",
            footer: true,
            pageSize: 'A4',
            //orientation: 'landscape',
            pageMargins: [40, 60, 40, 60],
            exportOptions: {
              columns: [0, 1, 2, 3, 4, 5],
              stripHtml: false
            }
          },
          //'pdf','excel','print'
        ],
      });
  
      // ------------------- Checkbox Operation ---------------------------//
      $('#example').on('length.dt', function(e, settings, len) {
        $("#check_all_btn").prop("checked", false);
      });
  
      $('#check_all_btn').on('change', function() {
        // alert('Check all click');
        var checked = $(this).prop('checked');
  
        dataTable.cells(null, 7).every(function() {
          var cell = this.node();
          $(cell).find('input[type="checkbox"][name="chkbx"]').prop('checked', checked);
        });
        var data = dataTable
          .rows(function(idx, data, node) {
            return $(node).find('input[type="checkbox"][name="chkbx"]').prop('checked');
          })
          .data()
          .toArray();
        //console.log(data);
        if (data.length === 0) {
          $("input.all_checkbox").removeAttr("disabled", true);
        } else {
          $("input.all_checkbox").attr("disabled", true);
        }
        var anyBoxesChecked = false;
        var applicantId = Array();
        $('input[type="checkbox"][name="chkbx"]').each(function(index, value) {
          if ($(this).is(":checked")) {
            anyBoxesChecked = true;
            applicantId.push(value.value);
          }
        });
  
        $("#fullForm #applicantId").val($.unique(applicantId));
        if (anyBoxesChecked == true) {
          $('#approve_rejdiv').show();
          $('.ben_view_button').attr('disabled', true);
          document.getElementById('bulk_approve').disabled = false;
          // document.getElementById('bulk_blkchange').disabled = false;
        } else {
          $('#approve_rejdiv').hide();
          $('.ben_view_button').removeAttr('disabled', true);
          document.getElementById('bulk_approve').disabled = true;
          // document.getElementById('bulk_blkchange').disabled = true;
        }
        // console.log(applicantId);
      });
      // ------------------- End Checkbox Operation -----------------------//
  
      // ------------------- View Button Click Section -----------------------//
      $(document).on('click', '.ben_view_button', function() {
        $('.loadingDivModal').show();
        $('#fullForm #application_id').val('');
        $('#fullForm #applicantId').val('');
        $('.ben_view_button').attr('disabled', true);
        var arrValue = $(this).val();
        var tempArr = arrValue.split('_');
        var benid = tempArr[0];
        var scheme_id = tempArr[1];
        $('#fullForm #application_id').val(arrValue);
        $("#fullForm #is_bulk").val(0);
        $('#opreation_type').val('A').trigger('change');
        $("#verifyReject").html("Active");
        $('.applicant_id_modal').html('');
        $('#accept_reject_comments').val('');
        $.ajax({
          type: 'post',
          url: "{{ route('inactiveModalView') }}",
          data: {
            _token: '{{csrf_token()}}',
            benid: benid,
            scheme_id: scheme_id
          },
          dataType: 'json',
          success: function(response) {
            // console.log(response);
            // console.log(JSON.stringify(response));
            $('.loadingDivModal').hide();
            $('#personal').show();
            $('#image_view').show();
            
            var benMobileDetails_msg = '';
            benMobileDetails_msg += '<span style="font-size:16px;">Name - ' + response.ben_arr.ben_name + '<br/> Father\'s Name - ' + response.ben_arr.father_name + '<br/> Gender - ' + response.ben_arr.gender + '<br/> Caste - ' + response.ben_arr.caste;
            benMobileDetails_msg += '</span>';
            $('#benMobileDetails').html(benMobileDetails_msg);
            
            $('.applicant_id_modal').html('(Beneficiary ID - ' + response.ben_arr.id + ')');
            $('#fullForm #id').val(response.id);
          },
          complete: function() {},
          error: function(jqXHR, textStatus, errorThrown) {
            $('.loadingDivModal').hide();
            $('.ben_view_button').removeAttr('disabled', true);
            $('.ben_view_modal').modal('hide');
            // ajax_error(jqXHR, textStatus, errorThrown);
            $.alert({
              title: 'Error!!',
              type: 'red',
              icon: 'fa fa-warning',
              content: 'Something wrong while fetching the beneficiary data!!',
            });
          }
        });
        $('.ben_view_modal').modal('show');
  
      });

    });
  
  
    function controlCheckBox() {
      var anyBoxesChecked = false;
      var applicantId = Array();
      $(' input[type="checkbox"]').each(function() {
        if ($(this).is(":checked")) {
          anyBoxesChecked = true;
          applicantId.push($(this).val());
        }
  
      });
      $("#fullForm #applicantId").val($.unique(applicantId));
      if (anyBoxesChecked == true) {
        $('#approve_rejdiv').show();
        $("#check_all_btn").attr("disabled", true);
        $('.ben_view_button').attr('disabled', true);
        document.getElementById('bulk_approve').disabled = false;
        // document.getElementById('bulk_blkchange').disabled = false;
      } else {
        $('#approve_rejdiv').hide();
        $('.ben_view_button').removeAttr('disabled', true);
        $("#check_all_btn").removeAttr("disabled", true);
        document.getElementById('bulk_approve').disabled = true;
        // document.getElementById('bulk_blkchange').disabled = true;
      }
      // console.log(applicantId);
    }
  
    // -------------------- Final Approve Section-------------------------- //
    $(document).on('click', '#verifyReject', function() {
      var opreation_type = $('#opreation_type').val();
      var accept_reject_comments = $('#accept_reject_comments').val();
      var is_bulk = $('#is_bulk').val();
      var single_app_id = $('#application_id').val();
      var applicantId = $('#applicantId').val();
      var op_text = $( "#opreation_type option:selected" ).text();
      var valid=1;
      if(valid==1){
        $.confirm({
          title: 'Confirm',
          type: 'blue',
          icon: 'fa fa-check',
          content: 'Are you sure want to Active these beneficiaries ?',
          buttons: {
            confirm: {
              text: 'confirm',
              btnClass: 'btn-blue',
              keys: ['enter', 'shift'],
              action: function(){
                $("#submitting").show();
                $("#verifyReject").hide();
                var id = $('#id').val();
                 var scheme_id = $('#scheme_id').val();
                $.ajax({
                  type: 'POST',
                  url: "{{ url('activatedBeneficiary') }}",
                  data: {
                    accept_reject_comments: accept_reject_comments,
                    scheme_id:scheme_id,

                    _token: '{{ csrf_token() }}',
                  },
                  success: function (data) {
                   // dataTable.ajax.reload();
                   var table_renew = $('#example').DataTable(); 
                   table_renew.ajax.reload( null, false );
                    //$('#example').DataTable().ajax.reload()
                    if(data.status==1){
                    
                      $('.ben_view_modal').modal('hide');
                      $('#approve_rejdiv').hide();
                      $.confirm({
                        title: 'Success',
                        type: 'green',
                        icon: 'fa fa-check',
                        content: data.msg,
                        buttons: {
                          Ok: function(){
                            $("#submitting").hide();
                            $("#verifyReject").show();
                            $("html, body").animate({ scrollTop: 0 }, "slow");
                          }
                        }
                      });
                    }
                    else{
                      $("#submitting").hide();
                      $("#verifyReject").show();
                      $('.ben_view_modal').modal('hide');
                      $('#approve_rejdiv').hide();
                      $.alert({
                        title: 'Error',
                        type: 'red',
                        icon: 'fa fa-warning',
                        content: data.msg
                      });
                    }
                  },
                  error: function (jqXHR, textStatus, errorThrown) {
                    $.confirm({
                      title: 'Error',
                      type: 'red',
                      icon: 'fa fa-warning',
                      content: 'Something went wrong in the approval!!',
                      buttons: {
                        Ok: function(){
                         // $("#verifyReject").show();
                        //  $("#submitting").hide();
                          location.reload();
                        }
                      }
                    });
                    // ajax_error(jqXHR, textStatus, errorThrown)
                  }           
                });
              }
            },
            Cancel: function () {
  
            },
          }
        });      
      }
    });
    // -------------------- Final Approve Section --------------------------// 
  
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