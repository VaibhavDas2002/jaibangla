<style type="text/css">
    .required-field::after {
      content: "*";
      color: red;
    }
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
  
  .panel-heading {
    padding: 0;
      border:0;
  }
  .panel-title>a, .panel-title>a:active{
      display:block;
      padding:5px;
    color:#555;
    font-size:12px;
    font-weight:bold;
      text-transform:uppercase;
      letter-spacing:1px;
    word-spacing:3px;
      text-decoration:none;
  }
  .panel-heading  a:before {
     font-family: 'Glyphicons Halflings';
     content: "\e114";
     float: right;
     transition: all 0.5s;
  }
  .panel-heading.active a:before {
      -webkit-transform: rotate(180deg);
      -moz-transform: rotate(180deg);
      transform: rotate(180deg);
  } 
  #enCloserTable tbody tr td{
    padding:10px 10px 10px 10px;
  }
  
  .modal-open {
  overflow: visible !important;
  }
  .disabledcontent {
    opacity: 0.4;
    pointer-events: none;
  }
  </style>
    @extends('layouts.app-template-datatable_new')
    @section('content')
      <div class="content-wrapper">
        <section class="content-header">
          <h1>
            Sarasori Mukhyamantri (CMO Grievance) for Marked Beneficiary List
          </h1>
        </section>
        <section class="content">
          <div class="box box-default">
            <div class="box-body">
              <div class="panel panel-default">
                <div class="panel-heading">Enter Filter Criteria</div>
                <div class="panel-body" style="padding: 5px;">
                  <div class="row">
                    @if ( ($message = Session::get('success')))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    @if(count($errors) > 0)
                      <div class="alert alert-danger alert-block">
                          <ul>
                          @foreach($errors->all() as $error)
                              <li><strong> {{ $error }}</strong></li>
                          @endforeach
                          </ul>
                      </div>
                    @endif
                  </div>
                  <div class="row">
                    <div class="col-md-3">
                      <label class=" control-label">Scheme <span class="text-danger">*</span></label>
                      <select class="form-control" name="scheme_id" id='scheme_id' required>
                        {{-- <option value="">--Select Scheme--</option> --}}
                        @foreach ($schemes as $scheme)
                            <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}</option>
                        @endforeach
                      </select>
                      <span class="text-danger" id="error_scheme_id"></span>
                    </div>
                    <div class="col-md-3">
                      <label class=" control-label">Operation Type <span class="text-danger">*</span></label>
                      <select class="form-control" name="operation_type" id='operation_type' required>
                        {{-- <option value="">--Select Operation Type--</option> --}}
                        <option value="1">Pending</option>
                        <option value="2">Pushed To CMO</option>
                      </select>
                      <span class="text-danger" id="error_operation_type"></span>
                    </div>
                    <div class="col-md-3">  
                      <label class=" control-label">JB Application Status</label>
                      <select class="form-control" name="current_status" id='current_status'>
                        <option value="">--All--</option>
                        <option value="1">Approved</option>
                        <option value="2">Applied</option>
                        <option value="3">Verified</option>
                        <option value="4">Rejected</option>
                      </select>
                      <span class="text-danger" id="error_current_status"></span>
                    </div>
                    @if ($district_visible)
                      <div class="form-group col-md-3" id="district_div">
                        <label class="">District </label>
                        <select name="district" id="district" class="form-control" tabindex="6">
                          <option value="">--All --</option>
                          @foreach ($districts as $district)
                              <option value="{{ $district->district_code }}"
                                  @if (old('district') == $district->district_code) selected @endif>
                                  {{ $district->district_name }}</option>
                          @endforeach
                          <option value="100" @if (old('district') == '100') selected @endif>Not Available</option>
                        </select>
                        <span id="error_district" class="text-danger"></span>
                      </div>
                    @else
                      <input type="hidden" name="district" id="district"
                              value="{{ $district_code_fk }}" />
                    @endif
                    {{-- @if ($is_urban_visible)
                      <div class="form-group col-md-4" id="divUrbanCode">
                        <label class="">Rural/ Urban</label>
                        <select name="filter_1" id="filter_1" class="form-control" tabindex="11">
                          <option value="">--All --</option>
                          @foreach (Config::get('constants.rural_urban') as $key => $val)
                              <option value="{{ $key }}" @if (old('urban_code') == $key) selected @endif>
                                  {{ $val }}</option>
                          @endforeach
                        </select>
                      </div>
                    @else
                      <input type="hidden" name="filter_1" id="filter_1"
                              value="{{ $rural_urban_fk }}" />
                    @endif --}}
                    {{-- @if ($block_visible)
                      <div class="form-group col-md-4" id="divBodyCode">
                        <label class="" id="blk_sub_txt">Block/Sub Division</label>
                        <select name="filter_2" id="filter_2" class="form-control"
                              tabindex="16">
                          <option value="">--All --</option>
                        </select>
                        <span id="error_block" class="text-danger"></span>
                      </div>
                    @else
                      <input type="hidden" name="block" id="block"
                              value="{{ $block_munc_corp_code_fk }}" />
                    @endif    --}}
                  </div>
                  <div class="row">
                    <center>
                      <div>
                        <button class="btn btn-primary" name="filter" id="filter"
                                type="button"><i class="fa fa-search"></i>
                                Search</button>&nbsp;
                            {{-- <button class="btn btn-info" name="excel_btn" id="excel_btn" type="button"><i class="fa fa-file-excel-o"></i> Export To Excel</button> --}}
                      </div>
                    </center>
                  </div>
                  <hr/>
                  <div class="row">
                    <div class="form-group col-md-offset-4 col-md-3 " style="display: none;" id="approve_rejdiv">
                      <button type="button" name="bulk_approve" class="btn btn-success btn-lg" id="bulk_approve" value="approve">
                        Push To CMO
                      </button>
                      {{-- <button type="button" name="bulk_revert" class="btn btn-info btn-lg" id="bulk_revert" value="revert">
                        Revert
                      </button> --}}
                    </div>
                  </div>
                </div>
              </div>
              <div id="res_div" style="display: none;">
                <div class="panel panel-default">
                  <div class="panel-heading" id="heading_msg" style="font-size: 15px; font-weight: bold; font-style: italic;">List of Grievance</div>
                  <div class="panel-body" style="padding: 5px; font-size: 14px;">
                    <div class="table-responsive">
                      <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 14px;">
                        <thead>
                          <th>Grevience ID</th>
                          <th>JB Beneficiary ID</th>
                          <th>Caller Name</th>
                          <th>JB Beneficiary Name</th>
                          <th>Caller Mobile No.</th>
                          <th>JB Beneficiary Mobile No.</th>
                          <th>JB Address</th>
                          <th>CMO Address</th>
                          <th>JB Beneficiary Current Status</th>
                          <th>Action</th>
                          <th >Check <span id="checkbox_all_span"><input type="checkbox" id='check_all_btn' style="width:48px;"></span> </th>
                          
                        </thead>
                        <tbody></tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal fade bd-example-modal-lg ben_view_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"      aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                  </button>
                  <h4 class="modal-title">Sarasori Mukhyamantri (CMO Grievance) Marked Beneficiary Details</h4>
                </div>
                <div class="modal-body ben_view_body">
                  <p id="header_message" style="text-align: center; align-content: center; font-size: 15px; font-weight: bold;" class="text-success"></p>
                  <div class="panel-group singleInfo" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                      <div class="panel-heading active" role="tab" id="personal">
                        <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" class="loader_img" width="150px" id="loader_img_personal"></div>
                        <h4 class="panel-title">
                          <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapsePersonal" aria-expanded="true" aria-controls="collapsePersonal">CMO GRIEVANCE DETAILS (ID: <span class="grievance_id_modal"></span>)</a> 
                        </h4> 
                      </div> 
                      <div id="collapsePersonal" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="personal">  
                        <div class="panel-body" style="padding: 5px;">
                          <div class="row">
                            <div class="col-md-12" style="margin-bottom: 10px;">
                              <div class="col-md-4">
                                <strong>Caller Name : </strong>
                                <span style="font-size: 14px;" id='caller_name'></span>
                              </div>
                              <div class="col-md-4">
                                  <strong>Caller Mobile No. : </strong>
                                  <span style="font-size: 14px;" id='cmo_mobile_no'></span>
                              </div>
                              <div class="col-md-4">
                                <strong>Grievance Age: </strong>
                                <span style="font-size: 14px;" id='cmo_age'></span>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12" style="margin-bottom: 10px;">
                              <div class="col-md-4">
                                <strong>Grievance District : </strong>
                                <span style="font-size: 14px;" id='cmo_dist_name'></span>
                              </div>
                              <div class="col-md-4">
                                <strong>Grievance Block/Municipality : </strong>
                                <span style="font-size: 14px;" id='cmo_block_ulb_name'></span>
                              </div>
                              <div class="col-md-4">
                                <strong>Complain Date: </strong>
                                <span style="font-size: 14px;" id='complain_date'></span>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12" style="margin-bottom: 10px;">
                              <div class="col-md-12">
                                <strong>Complain Description: </strong>
                                <span style="font-size: 14px;" id='complain_description'></span>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12" style="margin-bottom: 10px;">
                              <div class="col-md-12">
                                <strong>Verifier Process with ATR: </strong>
                                <span style="font-size: 14px;" id='atr'></span>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12" style="margin-bottom: 10px;">
                              <div class="col-md-12">
                                <strong>Remarks: </strong>
                                <span style="font-size: 14px;" id='remarks'></span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div> 
                    </div>
                  </div>
                  <div class="panel-group singleInfo" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                      <div class="panel-heading active" role="tab" id="banking">
                        <h4 class="panel-title">
                          <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseBank" aria-expanded="true" aria-controls="collapseBank" id="panel_bank_name_text">ATR Tagging Beneficiary Details</a> 
                        </h4> 
                      </div> 
                      <div id="collapseBank" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="banking">  
                        <div class="panel-body" style="padding: 5px;">
                          <table class="table table-bordered table-condensed" style="font-size: 14px;">  
                            <tbody>
                              <tr>
                                  <th scope="row" width="20%">Beneficiary ID</th>
                                  <td id='ben_id' width="30%"></td>
                                  <th scope="row" width="20%">Beneficiary Name</th>         
                                  <td id="ben_name" width="30%"></td>
                              </tr>
                              <tr>
                                  <th scope="row" width="20%">Mobile No.</th>         
                                  <td id="jb_mobile" width="30%"></td> 
                                  <th scope="row" width="20%">District Name</th>
                                  <td id='jb_dist_name' width="30%"></td>
                              </tr>
                              <tr>                
                                  <th scope="row" width="20%">Block/Municipality Name</th>         
                                  <td id='jb_block_ulb_name' width="30%"></td>
                                  <th scope="row" width="20%">Current Status</th>         
                                  <td id='jb_status' width="30%"></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div> 
                    </div>
                  </div>
                  <div class="panel-group">  
                    <div class="panel panel-default">
                      <div class="panel-heading" role="tab" id="headingFour">   
                        <h4 class="panel-title"> <a>Action</a> </h4> 
                      </div> 
                      <div id="collapse4" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingFour">  
                        <div class="panel-body" style="padding: 5px;"> 
                          <div class="form-group col-md-4">
                            <label for="opreation_type">Select Operation<span class="text-danger"> *</span></label>
                            <select name="opreation_type" id="opreation_type" class="form-control opreation_type">
                              <option value="A" selected>Push To CMO</option>
                                {{-- <option value="T">Revert</option>   --}}
                            </select>
                          </div> 
                          <div class="form-group col-md-4">
                            <label class="" for="heading">Enter Remarks</label>
                            <textarea style="margin: 0px; width: 279px; height: 40px;" name="accept_reject_comments" id="accept_reject_comments" class="form-control" maxlength="100"></textarea>
                          </div>
                        </div> 
                      </div> 
                    </div>  
                  </div>
                  <form method="POST" action="#" target="_blank" name="fullForm" id="fullForm" style="text-align: center; align-content: center;">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="is_bulk" id="is_bulk" value="0" />
                    <input type="hidden" id="id" name="id"/>
                    <input type="hidden" id="application_id" name="application_id"/>
                    <input type="hidden" name="applicantId[]" id="applicantId" value="" />
                    <button type="button" class="btn btn-success btn-lg" id="verifyReject">Push to CMO</button>
                    <button style="display:none;" type="button" id="submitting" value="Submit" class="btn btn-success success" disabled>Processing Please Wait</button>
                  </form> 
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>  
    @endsection 
  @section('script')
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
{{-- <script src="js/jquery.min.js" type="text/javascript"></script> --}}
<script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
<script>
  $(document).ready(function() {
    $('.sidebar-menu li').removeClass('active');
    $('.sidebar-menu #bankTrFailed').addClass("active"); 
    $('.sidebar-menu #accValTrFailedVerified').addClass("active"); 
    $('#opreation_type').val('A');
    // $("#verifyReject").html("Approve");
    $('#div_rejection').hide();
    var scheme_code = $('#scheme_id').val();
    var district = $('#district').val();
    var urban_code = $('#urban_code').val();
    var dataTable = "";
      if ( $.fn.DataTable.isDataTable('#example') ) {
        $('#example').DataTable().destroy();
      }
      $('#example tbody').empty();
      var dataTable=$('#example').DataTable( {
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
              url: "{{ url('cmo-grievance-hod-listing') }}", 
              type: "post",
              data:function(d){
                d.scheme_code = scheme_code,
                d.district = $('#district').val(),
                d.operation_type = $('#operation_type').val(),
                d.urban_code = $('#filter_1').val(),
                d.block = $('#filter_2').val(),
                d.gp_ward = $('#gp_ward').val(),
                d.muncid = $('#muncid').val(),
                d.current_status = $('#current_status').val(),
                d._token = "{{ csrf_token() }}"
                  },
              error: function (jqXHR, textStatus, errorThrown) {
                  $('#loadingDiv').hide();
                $('.preloader1').hide();
                ajax_error(jqXHR, textStatus, errorThrown);
              }
            },
            "initComplete":function(){
              $('#loadingDiv').hide();
              //console.log('Data rendered successfully');
              var check_value = $('#operation_type').val();
              // alert(check_value);
              if (check_value == 1) {
                $('#checkbox_all_span').show();
              } else {
                $('#checkbox_all_span').hide();
                dataTable.column(10).visible(false);
              }
            },
           "columns": [
                        {
                            "data": "grievance_id"
                        },
                        {
                            "data": "id"
                        },
                        {
                            "data": "caller_name"
                        },
                        {
                            "data": "name"
                        },
                        {
                            "data": "grievance_mobile"
                        },
                        {
                            "data": "mobile_no"
                        },
                        {
                            "data": "address"
                        },
                        {
                            "data": "cmo_address"
                        },
                        {
                          "data": "current_status"
                        },
                        {
                            "data": "action"
                        },  
                        {
                            "data": "check"
                        },   
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
          $('#filter').click(function(){
          if($.trim($('#scheme_id').val()).length == 0){
            error_scheme_id = 'Scheme is required';
            $('#error_scheme_id').text(error_scheme_id);
          }
          else{
            error_scheme_id = '';
            $('#error_scheme_id').text(error_scheme_id);
          }
          if($.trim($('#operation_type').val()).length == 0){
            error_operation_type = 'Type is required';
            $('#error_operation_type').text(error_operation_type);
          }
          else{
            error_operation_type = '';
            $('#error_operation_type').text(error_operation_type);
          }
          

          if( error_scheme_id != ''|| error_operation_type != '' ){
            return false;
          }
          else{
            $('#loadingDiv').show();
            $('#res_div').show();
            var msg = 'Beneficiary Details';
            $('#panel_head').text(msg);
            dataTable.ajax.reload();
          }
        });
        $('#example').on( 'page.dt', function () {
          $('#approve_rejdiv').hide();
      });
      $('#example').on( 'length.dt', function ( e, settings, len ) {
        $("#check_all_btn").prop("checked", false); 
      });
    $('#check_all_btn').on('change', function () {
        var checked = $(this).prop('checked');
        dataTable.cells(null, 10).every( function () {
          var cell = this.node();
          $(cell).find('input[type="checkbox"][name="chkbx"]').prop('checked', checked); 
        } );
        var data = dataTable
        .rows( function ( idx, data, node ) {
            return $(node).find('input[type="checkbox"][name="chkbx"]').prop('checked');
        } )
        .data()
        .toArray();
        if(data.length === 0){
          $("input.all_checkbox").removeAttr("disabled", true);
        }
        else{
          $("input.all_checkbox").attr("disabled", true);
        }
        var anyBoxesChecked = false;
        var applicantId=Array();
        $('input[type="checkbox"][name="chkbx"]').each(function( index,value ) { 
          if ($(this).is(":checked")) {
            anyBoxesChecked = true;
            applicantId.push(value.value);
          }
        });
      
        $("#fullForm #applicantId").val($.unique(applicantId));
        if (anyBoxesChecked == true) {
          $('#approve_rejdiv').show();
          $('.ben_view_button').attr('disabled',true);
          document.getElementById('bulk_approve').disabled = false;
        } 
        else{
          $('#approve_rejdiv').hide();
          $('.ben_view_button').removeAttr('disabled',true);
          document.getElementById('bulk_approve').disabled = true;
        }
    });

    $(document).on('click', '.ben_view_button', function() {
        $('#loader_img_personal').show();
        $('.ben_view_button').attr('disabled',true);
        var val = $(this).val();
        var array = val.split("_");
        var grievance_id = array[0];
        var scheme_id = array[1];
        $('#fullForm #application_id').val(grievance_id);
        $("#fullForm #is_bulk").val(0);
        $('#opreation_type').val('A').trigger('change');
        $("#verifyReject").html("Push To CMO");
        $('#div_rejection').hide();
        $(".singleInfo").show();
        $('.applicant_id_modal').html('');
        $('#accept_reject_comments').val('');
        $("#collapseBank").collapse('hide');
        $('#collapsePersonal').collapse('hide');
        $('.ben_view_body').addClass('disabledcontent');
        $.ajax({
          type: 'post',
          url: "{{route('cmo-grievance-hod-view')}}",
          data: {_token:'{{csrf_token()}}', 
          grievance_id:grievance_id,scheme_id:scheme_id
        },
          dataType: 'json',
          success: function (response) {
            //  console.log(JSON.stringify(response));
            $('.grievance_id_modal').text(response.grievance_id);
            $('#caller_name').text(response.caller_name);
            $('#cmo_mobile_no').text(response.grievance_mobile);
            $('#cmo_age').text(response.cmo_age);
            $('#complain_description').text(response.complain_description);
            $('#cmo_dist_name').text(response.cmo_dist_name);  
            $('#cmo_block_ulb_name').text(response.cmo_block_name);
            $('#complain_date').text(response.complain_date);
            $('#atr').text(response.atr);
            $('#remarks').text(response.remarks);
            $('#ben_id').text(response.jb_id);  
            $('#ben_name').text(response.jb_name);
            $('#jb_mobile').text(response.jb_mobile);
            $('#jb_status').text(response.next_level_role_id);
            $('#jb_dist_name').text(response.jb_dist_name);
            $('#jb_block_ulb_name').text(response.jb_block_ulb_name);
            $('.ben_view_body').removeClass('disabledcontent');
            $("#collapseBank").collapse('show');
            $('#loader_img_personal').hide();
            $('.ben_view_button').removeAttr('disabled',true);
            $('.applicant_id_modal').html('(Beneficiary ID - '+response.id+' )');
            $('#fullForm #id').val(response.id);
            $('#header_message').text(response.header_msg);
            
            if(response.av_name_response == ''){
              $('#av_name_response').text('No name response from bank');
            }else{
              $('#av_name_response').text(response.av_name_response);
            }
          },
          complete: function(){
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $('.ben_view_body').removeClass('disabledcontent');
            $('#loader_img_personal').hide();
            $('.ben_view_button').removeAttr('disabled',true);
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
        $(document).on('click', '#bulk_approve', function() {
        $(".singleInfo").hide();
        $("#fullForm #is_bulk").val(1);
        $('#opreation_type').val('A').trigger('change');
        $("#verifyReject").html("Push To CMO");
        $("#opreation_type option[value='T']").remove();
        $("#opreation_type option[value='R']").remove();
        $('#div_rejection').hide();
        $('#fullForm #id').val('');
        $('#fullForm #application_id').val('');
        $('#accept_reject_comments').val('');
        $('#header_message').hide();
        benid="";
        $('.ben_view_modal').modal('show');
      });
    $(document).on('click', '.opreation_type', function() {
      if($(this).val()=='T' || $(this).val()=='R'){
        $('#div_rejection').show();
        if($(this).val()=='T')
        $("#verifyReject").html("Revert");
        else if($(this).val()=='R')
        $("#verifyReject").html("Reject");
      }
      else{
        $("#verifyReject").html("Push To CMO");
        $('#div_rejection').hide();
        $("#reject_cause").val('');
      }
    });
    $(document).on('click', '#verifyReject', function() {   
      var reject_cause = $('#reject_cause').val();
      var opreation_type = $('#opreation_type').val();
      var accept_reject_comments = $('#accept_reject_comments').val();
      var is_bulk = $('#is_bulk').val();
      var grievance_id = $('#application_id').val();
      var applicantId = $('#applicantId').val();
      var scheme_id = $('#scheme_id').val();
      var valid=1;
      if(opreation_type=='R' || opreation_type=='T'){
        var valid=0;
        if(reject_cause!=''){
          var valid=1;      
        }
        else{
          $.alert({
            title: 'Error!!',
            type: 'red',
            icon: 'fa fa-warning',
            content: '<strong>Please Select Cause</strong>',
          });
          return false;
        }
      }
      if(valid==1){
        $.confirm({
          title: 'Warning',
          type: 'orange',
          icon: 'fa fa-warning',
          content: '<strong>Are you sure to proceed?</strong>',
          buttons: {
            Ok: function(){
              $("#submitting").show();
              $("#verifyReject").hide();
              var id = $('#id').val();
             
              $.ajax({
                type: 'POST',
                url: "{{ url('cmo-grievance-hod-post') }}",
                data: {
                  reject_cause: reject_cause,
                  opreation_type: opreation_type,
                  accept_reject_comments: accept_reject_comments,
                  application_id: id,
                  is_bulk: is_bulk,
                  scheme_id: scheme_id,
                  applicantId: applicantId,
                  grievance_id: grievance_id,
                  _token: '{{ csrf_token() }}',
                },
                success: function (data) {
                  // console.log(data);
                  console.log(JSON.stringify(data));
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
                }           
              });
            },
            Cancel: function () {
  
            },
          }
        });      
      }
    });
    $('#reset').click(function(){
      $('#filter_1').val('').trigger('change');
      $('#filter_2').val('').trigger('change');
      $('#block_ulb_code').val('').trigger('change');
      $('#gp_ward_code').val('').trigger('change');
      $('#failed_type').val('').trigger('change');
      $('#pay_mode').val('').trigger('change');
      dataTable.ajax.reload();
    });
    $('#filter_1').change(function() {
      var filter_1=$(this).val();
       
      $('#filter_2').html('<option value="">--All --</option>');
      $('#block_ulb_code').html('<option value="">--All --</option>');
      select_district_code= $('#district').val();
       
      var htmlOption='<option value="">--All--</option>';
      $('#gp_ward_code').html('<option value="">--All --</option>');
      if(filter_1==1){
        // alert(subDistricts);
        $.each(subDistricts, function (key, value) {
            if((value.district_code==select_district_code)){
                htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
            }
        });
        $("#blk_sub_txt").text('Subdivision');
        $("#gp_ward_txt").text('Ward');
        $("#municipality_div").show();
        $("#gp_ward_div").show();
      }
      else if(filter_1==2){
       // console.log(filter_1);
        $.each(blocks, function (key, value) {
          if((value.district_code==select_district_code)){
              htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
          }
        });
        $("#blk_sub_txt").text('Block');
        $("#gp_ward_txt").text('GP');
        $("#municipality_div").hide();
        $("#gp_ward_div").show();
      }
      else{
        $("#blk_sub_txt").text('Block/Subdivision');
        $("#gp_ward_txt").text('GP/Ward');
        $("#municipality_div").hide();
      }
      $('#filter_2').html(htmlOption);
       
    });
    $('#filter_2').change(function() {
      var rural_urbanid= $('#filter_1').val();
      $('#gp_ward_code').html('<option value="">--All --</option>');
      if(rural_urbanid==1){
        var sub_district_code=$(this).val();
        if(sub_district_code!=''){
          $('#block_ulb_code').html('<option value="">--All --</option>');
          select_district_code= $('#dist_code').val();
          var htmlOption='<option value="">--All--</option>';
          $.each(ulbs, function (key, value) {
            if((value.district_code==select_district_code) && (value.sub_district_code==sub_district_code)){
              htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
            }
          });
          $('#block_ulb_code').html(htmlOption);
        }
        else{
          $('#block_ulb_code').html('<option value="">--All --</option>');
        }   
      } 
      else if(rural_urbanid==2){
        $('#muncid').html('<option value="">--All --</option>');
        $("#municipality_div").hide();
        var block_code=$(this).val();
        select_district_code= $('#dist_code').val();
        var htmlOption='<option value="">--All--</option>';
        $.each(gps, function (key, value) {
          if((value.district_code==select_district_code) && (value.block_code==block_code)){
            htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
          }
        });
        $('#gp_ward_code').html(htmlOption);
        $("#gp_ward_div").show();
      }
      else{
        $('#block_ulb_code').html('<option value="">--All --</option>');
      } 
    });
    $('#block_ulb_code').change(function() {
      var muncid=$(this).val();
      var district=$("#dist_code").val();
      var urban_code=$("#filter_1").val();
      if(district==''){
        $('#filter_1').val('');
        $('#filter_2').html('<option value="">--All --</option>');
        $('#block_ulb_code').html('<option value="">--All --</option>'); 
      }
      if(urban_code==''){
        // alert('Please Select Rural/Urban First');
        $('#filter_2').html('<option value="">--All --</option>');
        $('#block_ulb_code').html('<option value="">--All --</option>'); 
        $("#filter_1").focus();
      }
      if(muncid!=''){
        var rural_urbanid= $('#filter_1').val();   
        if(rural_urbanid==1){
          $('#gp_ward_code').html('<option value="">--All --</option>');
          var htmlOption='<option value="">--All--</option>';
          $.each(ulb_wards, function (key, value) {
            if(value.urban_body_code==muncid){
              htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
            }
          });
          $('#gp_ward_code').html(htmlOption);
          //console.log(htmlOption);
        } 
        else{
          $('#gp_ward_code').html('<option value="">--All --</option>');
          $("#gp_ward_div").hide();
        } 
      }
      else{
        $('#gp_ward_code').html('<option value="">--All --</option>');
      }  
    });
  });
  function controlCheckBox(){
    var anyBoxesChecked = false;
     var applicantId=Array();
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
       $('.ben_view_button').attr('disabled',true);
      document.getElementById('bulk_approve').disabled = false;
      // document.getElementById('bulk_blkchange').disabled = false;
    } else{
      $('#approve_rejdiv').hide();
      $('.ben_view_button').removeAttr('disabled',true);
      $("#check_all_btn").removeAttr("disabled", true);
      document.getElementById('bulk_approve').disabled = true;
      // document.getElementById('bulk_blkchange').disabled = true;
    }
    // console.log(applicantId);
  }
</script>
@stop