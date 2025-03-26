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
            Approve Name validation failed with matching score between 0 to 25 but payment is on going
          </h1>
        </section>
        <section class="content">
          <div class="box box-default">
            <div class="box-body">
              <input type="hidden" name="dist_code" id="dist_code" value="{{ $dist_code }}" class="js-district_1">
              <div class="panel panel-default">
                <div class="panel-heading">Bank Details Yet To Be Approved</div>
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
                    <div class="form-group col-md-3">
                      <label class="control-label">Scheme<span class="text-danger">*</span></label>
                      <select class="form-control select2" name="scheme_type" id='scheme_type' required>
                        <option value="">--Select Scheme--</option>
                        @foreach ($schemes as $scheme)
                          <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                        @endforeach
                      </select>
                      <span class="text-danger" id="error_scheme_type"></span>
                    </div>
                    <div class="form-group col-md-3" id="name_type">
                      <label class="control-label" id="edited_txt">Edited Type by Verifier<span class="text-danger">*</span></label>
                      <select name="update_code" id="update_code" class="form-control">
                        <option value="">-----Select----</option>
                        <option value="141">Bank name may be taken as Beneficiary Name as Bank Name is correct</option>
                        <option value="142">Passbook Correction by Verifier</option>
                        <option value="143">Bank Account is of other Family Members, New Account Number modified by verifier</option>
                        <option value="144">Bank account is of completely of other person out of family, New Account Number modified by verifier</option>
                      </select>
                      <span class="text-danger" id="error_update_code"></span>
                    </div>
                    <div class="form-group col-md-3">
                      <label class="control-label">Rural/Urban </label>
                      <select name="filter_1" id="filter_1" class="form-control">
                        <option value="">-----Select----</option>
                        @foreach ($levels as $key=>$value)
                          <option value="{{$key}}"> {{$value}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group col-md-3">
                      <label class="control-label" id="blk_sub_txt">Block/Sub Division </label>
                      <select name="filter_2" id="filter_2" class="form-control">
                        <option value="">-----Select----</option>
                      </select>
                    </div>
                    <div class="form-group col-md-3" style="margin-top: 24px;">
                        <button type="button" name="filter" id="filter" class="btn btn-success"><i class="fa fa-search"></i> Search</button>&nbsp;&nbsp;
                        <button type="button" name="reset" id="reset" class="btn btn-warning"><i class="fa fa-refresh"></i> Reset</button>
                    </div>
                  </div>
                  <hr/>
                  <div class="row">
                    <div class="form-group col-md-offset-4 col-md-3 " style="display: none;" id="approve_rejdiv">
                      <button type="button" name="bulk_approve" class="btn btn-success btn-lg" id="bulk_approve" value="approve">
                        Approve
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel panel-default" id="res_div" style="display: none;">
                <div class="panel-heading" id="panel_head">List of New Edited Banking Information</div>
                <div class="panel-body" style="padding: 5px; font-size: 14px;">
                  <div class="table-responsive">
                    <table id="example" class="display" cellspacing="0" width="100%"> 
                      <thead style="font-size: 12px;">
                        <th width="5%">Beneficiary ID</th>
                        <th width="10%">Beneficiary Name</th>
                        <th width="10%">Mobile No</th>
                        <th width="10%">Beneficiary Account No</th>
                        <th width="10%">Beneficiary IFSC</th>
                        <th width="10%">Block/Municipality Name</th>
                        <th width="10%">Bank Response Name</th>
                        <th>Action</th>
                        <th id = 'check_box_id'>Check <input type="checkbox" id='check_all_btn' style="width:48px;"> </th>
                      </thead>
                      <tbody style="font-size: 14px;"></tbody>   
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal fade bd-example-modal-lg ben_view_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <h4 class="modal-title">Approve Name Validation Request</h4>
                </div>
                <div class="modal-body ben_view_body">
                  <p id="header_message" style="text-align: center; align-content: center; font-size: 15px; font-weight: bold;" class="text-success"></p>
                  <div class="panel-group singleInfo" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                      <div class="panel-heading active" role="tab" id="personal">
                        <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" class="loader_img" width="150px" id="loader_img_personal"></div>
                        <h4 class="panel-title">
                          <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapsePersonal" aria-expanded="true" aria-controls="collapsePersonal">Personal Details <span class="applicant_id_modal"></span></a> 
                        </h4> 
                      </div> 
                      <div id="collapsePersonal" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="personal">  
                        <div class="panel-body" style="padding: 5px;">
                          <table class="table table-bordered table-condensed" style="font-size: 14px;">  
                            <tbody>
                              <tr>
                                <th scope="row" width="20%">Applicant Name</th>
                                <td id='fullname' width="30%"></td>
                                <th scope="row" width="20%">Mobile No.</th>         
                                <td id="mobile_no" width="30%"></td>
                              </tr>
                              <tr>       
                                <th scope="row" width="20%">Gender</th>         
                                <td id="gender" width="30%"></td>
                                <th scope="row" width="20%">Caste</th>         
                                <td id="caste" width="30%"></td>   
                              </tr>
                              <tr>       
                                <th scope="row" width="20%">Name Response From Bank:</th>         
                                <td id="av_name_response" width="30%"></td>
                                <th></th>
                                <td></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div> 
                    </div>
                  </div>
                  <div class="panel-group singleInfo" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                      <div class="panel-heading active" role="tab" id="banking">
                        <h4 class="panel-title">
                          <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseBank" aria-expanded="true" aria-controls="collapseBank" id="panel_bank_name_text">Bank Details</a> 
                        </h4> 
                      </div> 
                      <div id="collapseBank" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="banking">  
                        <div class="panel-body" style="padding: 5px;">
                          <table class="table table-bordered table-condensed" style="font-size: 14px;">  
                            <tbody>
                              <tr>
                                <th scope="row" width="20%">Old Bank Name</th>
                                <td id='old_bank_name' width="30%"></td>
                                <th scope="row" width="20%">New Bank Name</th>         
                                <td id="new_bank_name" width="30%"></td>
                              </tr>
                              <tr>       
                                <th scope="row" width="20%">Old Branch Name</th>
                                <td id='old_branch_name' width="30%"></td>
                                <th scope="row" width="20%">New Branch Name</th>         
                                <td id='new_branch_name' width="30%"></td>
                              </tr>
                              <tr>
                                <th scope="row" width="20%">Old Account No.</th>         
                                <td id="old_acc_no" width="30%"></td> 
                                <th scope="row" width="20%">New Account No.</th>         
                                <td id='new_acc_no' width="30%"></td>         
                              </tr>
                              <tr>
                                <th scope="row" width="20%">Old IFSC</th>
                                <td id="old_ifsc" width="30%"></td>
                                <th scope="row" width="20%">New IFSC</th>
                                <td id="new_ifsc" width="30%"></td>
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
                        <h4 class="panel-title"> <a>Modified Enclosure Details</a> </h4> 
                      </div> 
                      <div id="collapseEnclosure" class="panel-collapse collapse in" role="tabpanel"
                                        aria-labelledby="enclosure">
                        <div class="panel-body" style="padding: 5px;">
                          <table class="table table-bordered table-condensed" style="font-size: 14px;" id="enCloserTable"><tbody></tbody></table>
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
                              <option value="A" selected>Approve</option>
                               <option value="T">Revert</option>  
                            </select>
                          </div> 
                          <div class="form-group col-md-4" style="display:none;" id="div_rejection">
                            <label for="reject_cause">Select Reverted Cause<span class="text-danger"> *</span></label>
                            <select name="reject_cause" id="reject_cause" class="form-control">
                              <option value="">--Select--</option>
                              <option value="Banking informtion">Banking informtion</option>
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
                    <button type="button" class="btn btn-success btn-lg" id="verifyReject">Approve</button>
                    <button style="display:none;" type="button" id="submitting" value="Submit" class="btn btn-success success" disabled>Processing Please Wait</button>
                  </form> 
                </div>
              </div>
            </div>
          </div>
          <div class="modal" id="encolser_modal"  role="dialog">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="encolser_name">Modal title</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div id="encolser_content">  </div>   
              </div>
            </div>
          </div>
        </section>
      </div>  
    @endsection 
@section('script')
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script src="js/jquery.min.js" type="text/javascript"></script>
<script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
<script>
  $(document).ready(function() {
    $('.sidebar-menu li').removeClass('active');
    $('.sidebar-menu #bankTrFailed').addClass("active"); 
    $('.sidebar-menu #accValTrFailedVerified').addClass("active"); 
    $('#opreation_type').val('A');
    $("#verifyReject").html("Approve");
    $('#div_rejection').hide();
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
              url: "{{ url('validation-correction-pending-approver-list') }}", 
              type: "post",
              data:function(d){
                  d.filter_1 = $('#filter_1').val(),
                  d.filter_2 = $('#filter_2').val(),
                  d.block_ulb_code = $('#block_ulb_code').val(),
                  d.gp_ward_code = $('#gp_ward_code').val(),
                  d.failed_type= $('#failed_type').val(),
                  d.scheme_type=$('#scheme_type').val(),
                  d.update_code=$('#update_code').val(),
                  d._token= "{{csrf_token()}}"
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
            },
            columns: [      
                    { "data": "ben_id" },
                    { "data": "ben_name" },
                    { "data": "mobile_no" },
                    { "data": "last_accno" },
                    { "data": "last_ifsc"},
                    { "data": "block_ulb_name"},
                    { "data": "bank_response_name"},
                    { "data": "view" },
                    { "data": "check" },
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
          if($.trim($('#scheme_type').val()).length == 0){
              error_scheme_type = 'Scheme is required';
          $('#error_scheme_type').text(error_scheme_type);
        }
        else{
          error_scheme_type = '';
          $('#error_scheme_type').text(error_scheme_type);
        }
        if($.trim($('#update_code').val()).length == 0){
          error_update_code = 'Edited Type is required';
          $('#error_update_code').text(error_update_code);
        }
        else{
          error_update_code = '';
          $('#error_update_code').text(error_update_code);
        }
        if( error_scheme_type != ''|| error_update_code != ''){
          return false;
        }
        else{
          var update_id = $('#update_code').val();
          if(update_id == 143 || update_id == 144){
            $('#check_box_id').hide();
          }else{
            $('#check_box_id').show();
          }
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
      dataTable.cells(null, 8).every( function () {
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
        var benid=$(this).val();
        $('#fullForm #application_id').val(benid);
        $("#fullForm #is_bulk").val(0);
        $('#opreation_type').val('A').trigger('change');
        $("#verifyReject").html("Approve");
        $('#div_rejection').hide();
        $(".singleInfo").show();
        $('.applicant_id_modal').html('');
        $('#accept_reject_comments').val('');
        $("#enCloserTable tbody").empty(); 
        $("#collapseBank").collapse('hide');
        $('#collapsePersonal').collapse('hide');
        $('.ben_view_body').addClass('disabledcontent');
        var name_validation_type=$('#update_code').val();
        $.ajax({
          type: 'post',
          url: "{{route('validation-correction-pending-approver-view')}}",
          data: {_token:'{{csrf_token()}}', 
          benid:benid,name_validation_type:name_validation_type
        },
          dataType: 'json',
          success: function (response) {
            //  console.log(JSON.stringify(response));
            $('#fullname').text(response.ben_name);
            $('#father_name').text(response.father_name);
            $('#mobile_no').text(response.mobile_no);
            $('#gender').text(response.gender);
            $('#dob').text(response.dob);
            $('#caste').text(response.caste);
            $('#old_acc_no').text(response.old_bank_code);  
            $('#old_bank_name').text(response.old_bank_name);
            $('#old_branch_name').text(response.old_branch_name);
            $('#old_ifsc').text(response.old_bank_ifsc);
            $('#new_acc_no').text(response.new_bank_code);  
            $('#new_bank_name').text(response.new_bank_name);
            $('#new_branch_name').text(response.new_branch_name);
            $("#enCloserTable tbody").html(response.html);  
            $('#new_ifsc').text(response.new_bank_ifsc);
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
        $("#verifyReject").html("Approve");
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
        $("#verifyReject").html("Approve");
        $('#div_rejection').hide();
        $("#reject_cause").val('');
      }
    });
    $(document).on('click', '#verifyReject', function() {   
      var reject_cause = $('#reject_cause').val();
      var opreation_type = $('#opreation_type').val();
      var accept_reject_comments = $('#accept_reject_comments').val();
      var is_bulk = $('#is_bulk').val();
      var single_app_id = $('#application_id').val();
      var applicantId = $('#applicantId').val();
      var scheme_id = $('#scheme_type').val();
      var name_validation_type = $('#update_code').val();
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
                url: "{{ url('validation-correction-pending-approver-post') }}",
                data: {
                  reject_cause: reject_cause,
                  opreation_type: opreation_type,
                  accept_reject_comments: accept_reject_comments,
                  application_id: id,
                  is_bulk: is_bulk,
                  scheme_id: scheme_id,
                  applicantId: applicantId,
                  single_app_id: single_app_id,
                  name_validation_type: name_validation_type,
                  _token: '{{ csrf_token() }}',
                },
                success: function (data) {
                  // console.log(data);
                  // console.log(JSON.stringify(data));
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
      select_district_code= $('#dist_code').val();
       
      var htmlOption='<option value="">--All--</option>';
      $('#gp_ward_code').html('<option value="">--All --</option>');
      if(filter_1==1){
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
  function View_encolser_modal(doc_name,doc_type,is_profile_pic,beneficiary_id,scheme_id){
            var type = 2;
            $('#encolser_name').html('');
            $('#encolser_content').html('');
            $('#encolser_name').html(doc_name+'('+beneficiary_id+')');
            $('#encolser_content').html('<img   width="50px" height="50px" src="images/ZKZg.gif"/>');

            $.ajax({
                        url: '{{ url('ajaxGetEncloser') }}',
                        type: "POST",
                        data: {
                        doc_type: doc_type,
                        is_profile_pic: is_profile_pic,
                        application_id: beneficiary_id,
                        scheme_id : scheme_id,
                        type : type,
                        _token: '{{ csrf_token() }}',
                        },
                        }).done(function( data, textStatus, jqXHR ) {
                          // console.log();
                        $('#encolser_content').html('');
                        $('#encolser_content').html(data.htmlText);
                        $("#encolser_modal").modal();
                    }).fail(function( jqXHR, textStatus, errorThrown ) {
                    $('#encolser_content').html('');
                        alert(sessiontimeoutmessage);
                    window.location.href=base_url;
                    });
  }
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