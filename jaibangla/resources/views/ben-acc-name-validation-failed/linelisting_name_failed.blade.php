<style type="text/css">
  .required-field::after {
    content: "*";
    color: red;
  }

  .has-error {
    border-color: #cc0000;
    background-color: #ffff99;
  }

  .preloader1 {
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
    border: 0;
  }

  .panel-title>a,
  .panel-title>a:active {
    display: block;
    padding: 10px;
    color: #555;
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    word-spacing: 3px;
    text-decoration: none;
  }

  .panel-heading a:before {
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
  .modal { overflow: auto !important; }
  #enCloserTable tbody tr td {
    padding: 10px 10px 10px 10px;
  }

  .modal-open {
    overflow: visible !important;
  }
  .required:after {
      color: red;
      content:'*';
      font-weight: bold;
      margin-left: 5px;
      float:right;
      margin-top: 5px;
  }
  #loadingDivModal{
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
  
</style>

@extends('layouts.app-template-datatable_new')
@section('content')

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Name and Account Validation Failed
    </h1>

  </section>
  <section class="content">
    <div class="box box-default">
      <div class="box-body">
        <input type="hidden" name="dist_code" id="dist_code" value="{{ $dist_code }}" class="js-district_1">

        <div class="panel panel-default">
          <div class="panel-heading"><span id="panel-icon">Filter Here</div>
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
            <div class="form-group col-md-4">
                 <label class="required-field">Scheme</label>
                 <select name="scheme_id" id="scheme_id" class="form-control"  >
                   @foreach ($scheme_list as $scheme)
                  <option value="{{$scheme->id}}"> {{$scheme->scheme_name}}</option>
                  @endforeach
                </select>
                 <span id="error_scheme_id" class="text-danger"></span>
              </div>
              <div class="form-group col-md-4">
                 <label class="">Scheme</label>
                 <select name="application_type" id="application_type" class="form-control"  >
                 <option value="">-----All----</option>
                 
                  <option value="1" selected>Pending</option>
                  <option value="2" selected>Verified but Approval Pending</option>
                  <option value="2" selected>Verified but Approved</option>
                </select>
                 <span id="error_application_type" class="text-danger"></span>
              </div>
            @if($duty_level=='SubdivVerifier')
           
              <div class="col-md-4">
                <label class="control-label">Municipality </label>
                <select name="filter_1" id="filter_1" class="form-control js-municipality">
                  <option value="">-----All----</option>
                  @foreach ($ulb_gp as $urban_body)
                  <option value="{{$urban_body->urban_body_code}}"> {{$urban_body->urban_body_name}}</option>
                  @endforeach
                </select>

              </div>
              <div class="col-md-4">
                <label class="control-label" id="ward_div">Wards </label>
                <select name="filter_2" id="filter_2" class="form-control">
                  <option value="">-----Select----</option>
                </select>

              </div>

              

            @elseif($duty_level=='BlockVerifier')
           
              <div class="col-md-4">
                <label class="control-label">Gram Panchayat </label>
                <select name="filter_1" id="filter_1" class="form-control">
                  <option value="">-----All----</option>
                  @foreach ($ulb_gp as $gp)
                  <option value="{{$gp->gram_panchyat_code}}"> {{$gp->gram_panchyat_name}}</option>
                  @endforeach
                </select>

              </div>
              

              <div class="form-group col-md-3">
                <label class="control-label">&nbsp;</label><br />
                <button type="button" name="filter" id="filter" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <button type="button" name="excel_btn" id="excel_btn" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Get Excel</button>
                {{-- <button type="button" name="reset" id="reset" class="btn btn-warning">Reset</button> --}}
                
              </div>


            </div>
            @endif

          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading" id="panel_head">List of account validation beneficiaries</div>
          <div class="panel-body" style="padding: 5px; font-size: 14px;">
            <div id="loadingDiv">
            </div>
            <div class="table-responsive">
              {{-- <div class="form-group" style="font-weight:bold; font-size:25px;">
                <label class="control-label">Check All</label>
              <input type="checkbox" id='check_all_btn' style="width:48px;">
              </div> --}}
              <table id="example" class="display" cellspacing="0" width="100%">
                <thead style="font-size: 12px;">
                  <th>Sl No</th>
                  <th>Beneficiary ID</th>
                  <th>Beneficiary Name</th>
                  <th>Swasthya Sathi Card No. </th>
                  <th>Block/ Municipality Name</th> 
                  <th>Application Id</th>
                  <th>Mobile No</th>
                  <th>GP/Ward Name</th>
                  <th>Account No</th>
                  <th>IFSC Code</th>
                  <th>Failure Type</th>
                  <th>Failure Month</th>
                  <th>Action</th>
                </thead>
                <tbody style="font-size: 14px;"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>
</div>
    <!-- Update Bank Details Modal -->
    <!-- Modal -->
    <div class="modal fade ben_bank_modal" id="" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Name Validation Failed</h4>
            </div>
            <div class="modal-body">
              <div id="loadingDivModal"></div>
              <div class="panel panel-default">
                <!-- <div class="panel-heading">Update Mobile No. and Bank Details</div> -->
                <div class="panel-body">
                  <table class="table table-bordered table-striped table-condensed" style="font-size: 14px;">
                    <tbody>
                      <tr>
                        <th scope="row">Beneficiary Id</th>
                        <td id="ben_id_text"></td>
                        <th scope="row">Application Id</th>
                        <td id="app_id_text"></td>
                      </tr>
                      <tr>
                        <th scope="row">Beneficiary Name</th>
                        <td id="ben_name_text"></td>
                        <th scope="row">Father's Name</th>
                        <td id="father_name_text"></td>
                      </tr>
                      <tr>
                        <th scope="row">Gender</th>
                        <td id="gender_text"></td>
                        <th scope="row">Date of Birth :(DD-MM-YYYY)</th>
                        <td id="dob_text"></td>
                      </tr>
                      <tr>
                        <th scope="row">Caste</th>
                        <td id="caste_text"></td>
                        <th scope="row">Mobile No</th>
                        <td id="mobile_no_text"></td>
                      </tr>
                      <tr>
                        <th scope="row">Block/ Municipality</th>
                        <td id="block_ulb_name_text"></td>
                        <th scope="row">GP/ Ward</th>
                        <td id="gp_ward_name_text"></td>
                      </tr>
                      <tr>
                        <th scope="row">Bank A/c No.</th>
                        <td id="bank_account_number_text"></td>
                        <th scope="row">Bank Name</th>
                        <td id="bank_name_text"></td>
                      </tr>
                      <tr>
                        <th scope="row">Bank IFSC</th>
                        <td id="bank_ifsc_text"></td>
                        <th scope="row">Branch Name</th>
                        <td id="branch_name_text"></td>
                      </tr>
                    </tbody>
                  </table>
                  <input type="hidden"  id="benId" name="benId" value="">
                  <input type="hidden"  id="faildTableId" name="faildTableId" value="">
                  <input type="hidden"  id="nameStatusCode" name="nameStatusCode" value="">
                  <input type="hidden"  id="application_id" name="application_id" value="">
                  <input type="hidden"  id="old_bank_ifsc" name="old_bank_ifsc" value="">
                  <input type="hidden"  id="old_bank_accno" name="old_bank_accno" value="">
                  <!-- Showing Reason Section -->
                  {{-- <div class="text-danger" style="font-size: 16px; text-align: center;">Failed Reason :- <span id="failed_reason" style=" font-weight: bold;"></span></div> --}}
                  <div style="font-size: 16px; text-align: center; ">Name Response from Bank :- <span id="name_response" class="text-success"></span></div>

                  <p style="font-size: 12px; font-weight: bold; text-align:center; display: none;">All (<span style="color:firebrick"> * </span>) marks filled are mandatory</p>
                  <div style="padding: 5px 5px 5px 50px; border: 1px solid whitesmoke; border-radius: 5px; margin: 5px 0px; background-color: whitesmoke;" class="row">
                    <label style="cursor: pointer; margin-bottom: 5px;">
                      <input type="radio" name="process_type" id="process_type" value="1"> Minor mismatch, Keep existing bank information
                    </label><br/>
                    <label style="cursor: pointer; margin-bottom: 5px;">
                      <input type="radio" name="process_type" id="process_type" value="2"> Process with new bank information
                    </label><br/>
                    <label style="cursor: pointer; margin-bottom: 5px;">
                      <input type="radio" name="process_type" id="process_type" value="3"> Application is rejected due to major mismatch
                    </label>
                  </div>
                  <div id="radio_btn_confirm" style="font-size: 14px; font-weight: bold; font-style: italic;" class="text-warning" align="center">Please select which one do you want to process ?</div>
                  <table class="table table-bordered table-responsive" style="width:100%;">
                    <tbody>
                      <tr class="bankInfoHideShow" style="display: none;">
                        <th scope="row" class="required" style="font-size: 14px;">Bank Branch Name</th>
                        <td id="branch_text"><input type="text" value="" name="branch_name" id="branch_name" readonly>
                        <span style="font-size: 14px;" id="error_bank_branch" class="text-danger"></span></td>
                        <th scope="row" class="required" style="font-size: 14px;">Bank IFSC Code</th>
                        <td id="bank_ifsc_text"><input type="text" value="" name="bank_ifsc" onkeyup="this.value = this.value.toUpperCase();" id="bank_ifsc">
                        <img src="{{ asset('images/ajaxgif.gif') }}" width="60px" id="ifsc_loader" style="display: none;">
                        <span style="font-size: 14px;" id="error_bank_ifsc_code" class="text-danger"></span></td>  
                      </tr>
                      <tr class="bankInfoHideShow" style="display: none;">
                        <th scope="row" class="required" style="font-size: 14px;">Bank Name</th>
                        <td id="bank_text"><input type="text" value="" name="bank_name" maxlength="200" id="bank_name" readonly>
                        <span style="font-size: 14px;" id="error_name_of_bank" class="text-danger"></span></td>
                        <th scope="row" class="required" style="font-size: 14px;">Bank Account Number</th>
                        <td id="bank_acc_text"> <input type="text" value="" name="bank_account_number" maxlength='20' id="bank_account_number">
                        <span style="font-size: 14px;" id="error_bank_account_number" class="text-danger"></span></td>
                      </tr>
                      <!-- Document Update Section -->
                      <tr class="documentSectionHideShow" style="display: none;">
                        <th scope="row" class="required" style="font-size: 14px;"><span id="upload_text">Upload Bank Passbook</span></th>
                        <td id="bank_passbook_text"> 
                          <input type="file"  name="upload_bank_passbook" accept=".jpg,.jpeg,.png,.pdf" id="upload_bank_passbook" value="">
                          <span style="font-size: 14px;" id="error_file" class="text-danger"></span>
                        </td>
                        <th scope="row" style="font-size: 14px;">Copy Of Passbook</th>
                        <td  scope="row" class="encView">&nbsp;&nbsp;&nbsp;<a class="btn btn-xs btn-primary" href="javascript:void(0);" onclick="View_encolser_modal('Copy of Bank Pass book','10',1)">View</a></td>
                      </tr>
                      <tr class="remarkSectionHideShow" style="display: none;">
                        <th scope="row" class="required" style="font-size: 14px;">Remarks</th>
                        <td colspan="3"><input type="text" name="remarks" maxlength="100" id="remarks" class="form-control" value="" style="border-radius: 3px; border: 1px solid #737373;">
                        <span style="font-size: 14px;" id="error_remarks" class="text-danger"></span></td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="row" id="finalUpdatediv" style="display: none;">                
                    <div class="col-md-12" style="text-align: center;"><input type="button" name="submit" value="Update" id="" class="btn btn-success btn-lg verifySubmit"></div>
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

    <!-- Passbook View Modal -->
    <div class="modal encolser_modal" id="encolser_modal"  role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="encolser_name">Modal title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div id="encolser_content">  </div>
         
          <div class="modal-footer"  style="text-align: center">
            <button type="button"  class="btn btn-success modalEncloseClose" >Close</button>
     
              
               
           </div> 
        </div>
      </div>
    </div>
  
<!-- </div> -->


@endsection
@section('script')
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script>
  $(document).ready(function() { 
    $('#upload_bank_passbook').change(function(){
      var card_file=document.getElementById("upload_bank_passbook");
      if(card_file.value!="")
      {
        var attachment;
        attachment = card_file.files[0];
        // console.log(attachment.type)
        var type = attachment.type;
        if(attachment.size>512000)
        {
          document.getElementById("error_file").innerHTML="<i class='fa fa-warning'></i> Unaccepted document file size. Max size 500 KB. Please try again";
          $('#upload_bank_passbook').val('');
          return false;
        }
        else if (type != 'image/jpeg' && type != 'image/png' && type != 'application/pdf') {
          document.getElementById("error_file").innerHTML="<i class='fa fa-warning'></i> Unaccepted document file format. Only jpeg,jpg,png and pdf. Please try again";
          $('#upload_bank_passbook').val('');
          return false;
        }
        else{
          $('#file_upload_btn').show();
          document.getElementById("error_file").innerHTML="";
        }
      }
    });
    $('#excel_btn').click(function(){
      var token = "{{csrf_token()}}";
      var filter_1= $('#filter_1').val();
      var filter_2= $('#filter_2').val();
      var failed_type= 3;
      var pay_mode= $('#pay_mode').val();
      var  data= {'_token': token, 'filter_1': filter_1, 'filter_2': filter_2, 'failed_type': failed_type, 'pay_mode': pay_mode};
      redirectPost('getBankFailedexcel',data); 
    });
    $('.modalEncloseClose').click(function(){
      $('.encolser_modal').modal('hide');
    });
    
   // $('#loadingDiv').hide();
  $('.sidebar-menu li').removeClass('active');
  $('.sidebar-menu #bankTrFailed').addClass("active"); 
  $('.sidebar-menu #nameValTrFailed').addClass("active"); 

  $('.ben_bank_modal').on('hidden.bs.modal', function (e) {
    $('#bank_ifsc').val('');
    $('#bank_name').val('');
    $('#branch_name').val('');
    $('#bank_account_number').val('');
    $('#benId').val('');
    $('#faildTableId').val('');
    $('#old_bank_ifsc').val('');
    $('#old_bank_accno').val('');
    $('#upload_bank_passbook').val('');      
  });
  $(document).on('change', '#process_type', function() {
    var processVal = this.value;
    if (processVal == 1) {
      $('.documentSectionHideShow').hide();
      $('.remarkSectionHideShow').hide();
      $('.bankInfoHideShow').hide();
      // $('#upload_text').text('Upload Field Verificiation Report');
      $('#finalUpdatediv').show();
      $('#radio_btn_confirm').hide();
      $('#upload_bank_passbook').val('');
      $('#remarks').val('');
      $('#bank_ifsc').val('');
      $('#bank_name').val('');
      $('#branch_name').val('');
      $('#bank_account_number').val('');
    }
    else if(processVal == 2) {
      $('.documentSectionHideShow').show();
      $('.remarkSectionHideShow').show();
      $('.bankInfoHideShow').show();
      $('#upload_text').text('Upload Bank Passbook');
      $('#finalUpdatediv').show();
      $('#radio_btn_confirm').hide();
      $('#upload_bank_passbook').val('');
      $('#remarks').val('');
      $('#bank_ifsc').val('');
      $('#bank_name').val('');
      $('#branch_name').val('');
      $('#bank_account_number').val('');
    }
    else if (processVal == 3) {
      $('.documentSectionHideShow').hide();
      $('.remarkSectionHideShow').hide();
      $('.bankInfoHideShow').hide();
      // $('#upload_text').text('Upload Field Verificiation Report');
      $('#finalUpdatediv').show();
      $('#radio_btn_confirm').hide();
      $('#upload_bank_passbook').val('');
      $('#remarks').val('');
      $('#bank_ifsc').val('');
      $('#bank_name').val('');
      $('#branch_name').val('');
      $('#bank_account_number').val('');
    }
    else {
      $('.documentSectionHideShow').hide();
      $('.remarkSectionHideShow').hide();
      $('.bankInfoHideShow').hide();
      $('#upload_text').text('');
      $('#finalUpdatediv').hide();
      $('#radio_btn_confirm').show();
      $('#upload_bank_passbook').val('');
      $('#remarks').val('');
      $('#bank_ifsc').val('');
      $('#bank_name').val('');
      $('#branch_name').val('');
      $('#bank_account_number').val('');
    }
  });
  $(document).on('click', '.bank_edit_btn', function() {
    $('#loadingDiv').show();
    var editvalue=this.id;
    
    $.ajax({
      type: 'post',
      url: "{{route('editFailedNameDetails')}}",
      data: {_token:'{{csrf_token()}}',editvalue:editvalue},
      dataType: 'json',
      success: function (response) {
        $('#loadingDiv').hide();
        $('#loadingDivModal').hide();
        $("input:radio").attr("checked", false);
        $('#radio_btn_confirm').show();
        $('.documentSectionHideShow').hide();
        $('.remarkSectionHideShow').hide();
        $('.bankInfoHideShow').hide();
        $('#finalUpdatediv').hide();
        $('.ben_bank_modal').modal('show');
        
        $('#failed_reason').text(response.failed_reason);
        $('#ben_id_modal').text(response.ben_id);
        $('#ben_name_text').text(response.ben_name);
        $('#father_name_text').text(response.benfather_name);
        $('#gender_text').text(response.gender);
        $('#dob_text').text(response.dob);
        $('#caste_text').text(response.caste);
        $('#ben_id_text').text(response.ben_id);
        $('#app_id_text').text(response.application_id);
        // $('#mobile_no').val(response.mobile_no);
        // $('#bank_ifsc').val(response.bank_ifsc);
        // $('#bank_name').val(response.bank_name);
        // $('#branch_name').val(response.branch_name);
        // $('#bank_account_number').val(response.bank_code);
        $('#bank_ifsc_text').text(response.bank_ifsc);
        $('#bank_name_text').text(response.bank_name);
        $('#branch_name_text').text(response.branch_name);
        $('#bank_account_number_text').text(response.bank_code);
        $('#mobile_no_text').text(response.mobile_no);
        $('#block_ulb_name_text').text(response.block_ulb_name);
        $('#gp_ward_name_text').text(response.gp_ward_name);
        $('#benId').val(response.ben_id);
        $('#faildTableId').val(response.failedid);
        $('#nameStatusCode').val(response.status_code);
        $('#application_id').val(response.application_id);
        $('#old_bank_ifsc').val(response.bank_ifsc);
        $('#old_bank_accno').val(response.bank_code);
        if (response.name_response == '') {
          $('#name_response').html('<span style="font-size: small; font-style: italic;">No name received from bank.</span>');
        }
        else {
          $('#name_response').html('<span style="font-weight: bold;">'+response.name_response+'</span>');
        }
      },
      complete: function(){
        
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#loadingDiv').hide();
        $('#loadingDivModal').hide();
        ajax_error(jqXHR, textStatus, errorThrown)
        
      }
   
    });
  });

   var dataTable = "";
   if ( $.fn.DataTable.isDataTable('#example') ) {
          $('#example').DataTable().destroy();
        }
         dataTable=$('#example').DataTable( {
          dom: 'Blfrtip',
          "scrollX": true,
          "paging": true,
          "searchable": true,
          "ordering":false,
          "bFilter": true,
          "bInfo": true,
          "pageLength":10,
          'lengthMenu': [[10,20,30], [10,20,30]],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><h4 class="text-success" style="font-weight:bold;font-size:22px;">Processing...</h4></div>'
          },
          ajax:{
        url: "{{ url('getDataNameValidationFailed') }}", 
        type: "POST",
        data:function(d){
          d.filter_1= $('#filter_1').val(),
          d.filter_2= $('#filter_2').val(),
          d.failed_type= $('#failed_type').val(),
          d.pay_mode= $('#pay_mode').val(),
        
          d._token= "{{csrf_token()}}"
        },
      
        error: function (jqXHR, textStatus, errorThrown) {
          $('#loadingDiv').hide();
             ajax_error(jqXHR, textStatus, errorThrown)
            }                
      },
      "initComplete":function(record){
       // console.log(record.json)
        //console.log('Data rendered successfully');
        $('#loadingDiv').hide();
        
      //  $('#completed_bank').text(record.json.completed[0].count);
       // $('#pending_bank_edit').text(record.json.recordsTotal);
      },
      "columns": [
        { "data": "DT_RowIndex" },
        { "data": "id" },
        { "data": "name" },
        { "data": "ss_cardno" },
        { "data": "block_ulb_name" },
        { "data": "application_id" },
        { "data": "mobile_no"},
        { "data": "gp_ward_name"},
        { "data": "accno"},
        { "data": "ifsc" },
        { "data": "type" },
        { "data": "failure_month" },
        { "data": "action" },
          ],
      "columnDefs": [
        {
            "targets": [ 3,4,5 ],
            "visible": false,
            "searchable": true
        },
    //         {
    //   "targets": [ 7 ],
    //   "orderable": false,
    //   "searchable": true
    // }
        ],
      "buttons": [
      {
        extend : 'pdfHtml5',
        title: "Name Validation Failed Report  Report Generated On-@php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp ",   
        messageTop: "Date: @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp",
      
        footer: true,
        orientation : 'landscape',
        // pageSize : 'LEGAL',
        pageMargins: [ 40, 60, 40, 60 ],
        exportOptions: {
          columns: [0,1,2,3,4,5,6,7,8,9,10,11],
        }
      },
      {
        extend: 'excel',
        title: "Name Validation Failed Report  Report Generated On-@php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp ",   
        messageTop: "Date: @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp",
        footer: true,
        pageSize:'A4',
        //orientation: 'landscape',
        pageMargins: [ 40, 60, 40, 60 ],
        exportOptions: {
          format: {
            body: function (data, row, column, node ) {
              return column === 8 || column===3 ? "\0" + data : data;
            }
          },
          columns: [0,1,2,3,4,5,6,7,8,9,10,11],
          stripHtml: false,
        }
      },
          
      ],
    });

    $('#filter').click(function(){ 
      dataTable.ajax.reload();
    });
  
    $('.js-municipality').change(function() {
      var municipality=$('.js-municipality').val();  
      var district_code = $('#dist_code').val();
      var htmlOption='<option value="">----Select----</option>';
      if(municipality != ''){
        $.each(ulb_wards, function (key, value) {
          if((value.urban_body_code==municipality)){
            htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
          }
        });
      }
      else {
        htmlOption='<option value="">----Select----</option>';
      }
      $('#filter_2').html(htmlOption);  
    });

    $('#bank_ifsc').blur(function(){
      $ifsc_data = $.trim($('#bank_ifsc').val());
      $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
      if($ifscRGEX.test($ifsc_data))
      {
        $('#bank_ifsc').removeClass('has-error');
        $('#error_bank_ifsc_code').text('');
        $('#ifsc_loader').show();
        $('.verifySubmit').attr('disabled',true);
        $.ajax({
          type: 'POST',
          url: "{{ route('bankIfsc') }}",
          data: {
            ifsc: $ifsc_data,
            _token: '{{ csrf_token() }}',
          },
          success: function (data) {
            $('#ifsc_loader').hide();
            $('.verifySubmit').removeAttr('disabled',true);
            if(data.status==2){
              $.confirm({
                    title: 'IFSC Not Found!',
                    type:'blue',
                    icon: 'fa fa-info',
                    content: 'This ' + $ifsc_data+' IFSC is not registered in our system.',
                    
            
                });
                $('#bank_ifsc').val('');
                return false;
            }
            else{
              $('#bank_name').val(data.bank_details.bank);
            $('#branch_name').val(data.bank_details.branch);
            }
          
           
          },
          error: function (ex) {
            $('#ifsc_loader').hide();
            $('#error_bank_ifsc_code').text('Data fetch error');
            $('#bank_ifsc').addClass('has-error');
          }
        });

      }else{
        $('#error_bank_ifsc_code').text('IFSC format invalid please check the code');
        $('#bank_ifsc').addClass('has-error');
      }
  });


  // -------------------- Final Approve Section-------------------------- //
  $(document).on('click', '.verifySubmit', function() { 
    var error_name_of_bank =''; 
    var error_bank_branch =''; 
    var error_bank_account_number =''; 
    var error_bank_ifsc_code ='';
    var error_remarks = '';
    var error_file = ''; 

    if($.trim($('#bank_name').val()).length == 0)
    {
     error_name_of_bank = 'Name of Bank is required';
     $('#error_name_of_bank').text(error_name_of_bank);
     $('#bank_name').addClass('has-error');
    }
    else
    {
     error_name_of_bank = '';
     $('#error_name_of_bank').text(error_name_of_bank);
     $('#bank_name').removeClass('has-error');
    }

    if($.trim($('#branch_name').val()).length == 0)
    {
     error_bank_branch = 'Bank Branch is required';
     $('#error_bank_branch').text(error_bank_branch);
     $('#branch_name').addClass('has-error');
    }
    else
    {
     error_bank_branch = '';
     $('#error_bank_branch').text(error_bank_branch);
     $('#branch_name').removeClass('has-error');
    }

    if($.trim($('#bank_account_number').val()).length == 0)
    {
     error_bank_account_number = 'Bank Account Number is required';
     $('#error_bank_account_number').text(error_bank_account_number);
     $('#bank_account_number').addClass('has-error');
    }
    else
    {
     error_bank_account_number = '';
     $('#error_bank_account_number').text(error_bank_account_number);
     $('#bank_account_number').removeClass('has-error');
    }

    if($.trim($('#bank_ifsc').val()).length == 0)
    {
     error_bank_ifsc_code = 'IFS Code is required';
     $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
     $('#bank_ifsc').addClass('has-error');
    }
    else
    {
     error_bank_ifsc_code = '';
     $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
     $('#bank_ifsc').removeClass('has-error');
    }

    $ifsc_data = $.trim($('#bank_ifsc').val());
    $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
    if($ifscRGEX.test($ifsc_data))
    {
      error_bank_ifsc_code = '';
      $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
      $('#bank_ifsc').removeClass('has-error');
    }
    else{
      error_bank_ifsc_code = 'Please check IFS Code format';
      $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
      $('#bank_ifsc').addClass('has-error');    
    }
    if($.trim($('#remarks').val()).length == 0)
    {
     error_remarks = 'Please add some remarks';
     $('#error_remarks').text(error_remarks);
     $('#remarks').addClass('has-error');
    }
    else
    {
     error_remarks = '';
     $('#error_remarks').text(error_remarks);
     $('#remarks').removeClass('has-error');
    }

    if($('#upload_bank_passbook')[0].files.length == 0)
    {
     error_file = 'Please upload required document';
     $('#error_file').text(error_file);
     $('#upload_bank_passbook').addClass('has-error');
    }
    else
    {
     error_file = '';
     $('#error_file').text(error_file);
     $('#upload_bank_passbook').removeClass('has-error');
    }
    var isFinalUpdateHappens = 0;
    var processType = $('input[name="process_type"]:checked').val();
    if (processType == 1) {
      // if(error_remarks != ''/* || error_file != ''*/) { 
      //   return false;
      // }
      // else {
        isFinalUpdateHappens = 1;
      // }
    } 
    else if(processType == 2) {
      if(error_name_of_bank !='' || error_bank_branch !=''||  error_bank_account_number !='' || error_bank_ifsc_code !='' || error_remarks != '' || error_file != '') { 
        return false;
      }
      else {
        isFinalUpdateHappens = 1;
      }
    }
    else if (processType == 3) {
      // if(error_remarks != ''/* || error_file != ''*/) { 
      //   return false;
      // }
      // else {
        isFinalUpdateHappens = 1;
      // }
    }
    // Final Update Here
    if (isFinalUpdateHappens == 1) {
      var process_type = $('input[name="process_type"]:checked').val();
      var old_bank_ifsc=$('#old_bank_ifsc').val();
      var old_bank_accno=$('#old_bank_accno').val();
      var bank_ifsc=$('#bank_ifsc').val();
      var bank_account_number=$('#bank_account_number').val();
      if(process_type == 2) {
        if((bank_account_number == old_bank_accno) && (bank_ifsc == old_bank_ifsc)) {
          $.confirm({
            title: 'Alert!',
            type:'red',
            icon: 'fa fa-warning',
            content:'Account number and ifsc same as previous one',
          });
          return false;
        }
      }
      $.confirm({
        title: 'Confirmation!',
        type:'orange',
        icon: 'fa fa-check',
        content:'Are you sure want to process ?',
        buttons: {
          confirm: {
            text: 'confirm',
            btnClass: 'btn-blue',
            keys: ['enter', 'shift'],
            action: function(){
              var upload_bank_passbook = $('#upload_bank_passbook')[0].files;
              var bank_name=$('#bank_name').val();
              var branch_name=$('#branch_name').val();
              var remarks=$('#remarks').val();
              var benId=$('#benId').val();
              var application_id = $('#application_id').val();
              var faildTableId = $('#faildTableId').val();
              var nameStatusCode = $('#nameStatusCode').val();
              var token =  '{{csrf_token()}}'; 
              var fd= new  FormData();
              fd.append('benId', benId);
              fd.append('bank_ifsc', bank_ifsc);
              fd.append('bank_name', bank_name);
              fd.append('bank_account_number', bank_account_number);
              fd.append('branch_name', branch_name);
              fd.append('upload_bank_passbook', upload_bank_passbook[0]);
              fd.append('_token', token);
              fd.append('old_bank_ifsc',old_bank_ifsc);
              fd.append('old_bank_accno',old_bank_accno);
              fd.append('remarks',remarks);
              fd.append('application_id',application_id);
              fd.append('process_type',process_type);
              fd.append('faildTableId',faildTableId);
              fd.append('nameStatusCode',nameStatusCode);
              $('#loadingDivModal').show();
              $('.verifySubmit').attr('disabled',true);
              $.ajax({
                type: 'post',
                url: "{{route('updateFailedNameFromVerifier')}}",
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                  $('#loadingDivModal').hide();
                  $('.verifySubmit').removeAttr('disabled',true);
                  $('.ben_bank_modal').modal('hide');
                  dataTable.ajax.reload();
                  $.confirm({
                    title: response.title,
                    type:response.type,
                    icon: response.icon,
                    content:response.msg,
                    buttons: {
                      Ok: function(){
                        // $('.verifySubmit').removeAttr('disabled',true);
                        // $('.ben_bank_modal').modal('hide');
                        // dataTable.ajax.reload();
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                      }
                    }
                  });
                },
                complete: function(){
                //  $('.verifySubmit').removeAttr('disabled',true);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                  $('.verifySubmit').removeAttr('disabled',true);
                  $('#loadingDivModal').hide();
                  ajax_error(jqXHR, textStatus, errorThrown) 
                }
              });
            }
          },
          cancel: function () {
            
          }
        }
      });
    }
    else {
      $.confirm({
        title: 'Alert!',
        type:'red',
        icon: 'fa fa-warning',
        content:'Something went wrong!!',
      });
    }
  });
  // -------------------- Final Approve Section --------------------------//

});

function redirectPost(url, data , method = 'post'){
    var form = document.createElement('form');
    form.method = method;
    form.action = url;
    for (var name in data) {
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = name;
      input.value = data[name];
      form.appendChild(input);
    }
    $('body').append(form);
    form.submit();
  }
</script>
@stop