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
      background-image:url('../images/ajaxgif.gif');
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
      background-image:url('../images/ajaxgif.gif');
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
        <section class="content-header">
            <h1>
              Bank Failed Correction
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
                                            <div class="col-md-3">
                                                <label class=" control-label">Failed Type<span class="text-danger">*</span></label>
                                                <select class="form-control select2" name="failed_type" id='failed_type' required>
                                                  <option value="">--All Type--</option>
                                                  <option value="SBI">SBI</option>
                                                  <option value="IFMS">IFMS</option>
                                                  <option value="RBI">RBI</option>
                                                </select>
                                                <span class="text-danger" id="error_failed_type"></span>
                                            </div>
                                            @if($mapLevel=='SubdivVerifier')
                                                <div class="col-md-3">
                                                    <label class=" control-label" >Municipality</label>
                                                    <select name="filter_1" id="filter_1" class="form-control select2 full-width js-municipality" >
                                                    <option value="">-----All----</option>
                                                    @foreach ($urban_bodys as $urban_body)
                                                    <option value="{{$urban_body->urban_body_code}}" > {{$urban_body->urban_body_name}}</option>
                                                    @endforeach
                                                    </select>
                                                </div> 
                                                <div class="col-md-3">
                                                    <label class=" control-label" >Wards</label>
                                                    <select name="filter_2" id="filter_2" class="form-control select2 full-width js-wards" >
                                                    <option value="">-----All----</option>
                                                    </select>
                                                </div> 
                                                <input type="hidden" name="local_body" id="local_body" value={{$local_body_code}}>  
                                            @elseif($mapLevel=='BlockVerifier')
                                                <div class="col-md-3">
                                                    <label class=" control-label" >Gram Panchayat</label>
                                                    <select name="filter_1" id="filter_1" class="form-control select2 full-width" >
                                                    <option value="">-----All----</option>
                                                    @foreach ($gps as $gp)
                                                        <option value="{{$gp->gram_panchyat_code}}" > {{$gp->gram_panchyat_name}}</option>
                                                    @endforeach
                                                    </select>
                                                </div> 
                                                <input type="hidden" name="local_body" id="local_body" value={{$local_body_code}}>
                                            @elseif($mapLevel=='DistrictApprover')
                                                <input type="hidden" name="local_body" id="local_body" value="">
                                                
                                            @endif
                                            <input type="hidden" name="mapLevel" id="mapLevel" value={{$mapLevel}}>
                                            <input type="hidden" name="district_code" id="district_code" value="{{$district_code}}">
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
                                  <th width="5%">Beneficiary ID</th>
                                  <th width="10%">Beneficiary Name</th>
                                  <th width="10%">Mobile No</th>
                                  <th width="10%">Beneficiary Account No</th>
                                  <th width="10%">Beneficiary IFSC</th>
                                  <th width="10%">Block/Municipality Name</th>
                                  <th width="10%">GP/Ward Name</th>
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
        <!-- Modal -->
         <!-- Modal -->
    <div class="modal fade" id="modalUpdatebank" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Update Bank Details</h4>
            </div>
            <div class="modal-body">
              <div class="loadingDivModal"></div>
              <div class="" id="updateDiv">
                <!-- <div class="panel-heading">Enter Bank Details</div>
                <div class="panel-body"> -->
                  <div class="row">
                    <div class="col-md-12">
                      <h4 style="text-align: center;" class="text-primary">Beneficiary ID: <span id="application_id"></span></h4>
                      <h4 style="text-align: center;" class="text-danger">Failed Reason: <span id="failed_reason"></span></h4> 
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
                        <strong>Caste:</strong>
                        <span id="caste_div"></span>
                      </td>
                      <td></td>
                    </tr>                       
                  </table>
                  <input type="hidden" name="pension_id" id="pension_id" value="">
                  <input type="hidden" name="update_scheme_id" id="update_scheme_id" value="">
                  <input type="hidden" name="old_bank_ifsc" id="old_bank_ifsc" value="">
                  <input type="hidden" name="old_bank_code" id="old_bank_code" value="">
                  <input type="hidden" name="pay_mode" id="pay_mode" value="">
                  <table class="table table-bordered table-responsive table-condensed" style="width:100%; font-size: 14px;" >
                    <tr>
                      <th>Mobile Number: <span class="text-danger">*</span></th>
                      <td>
                        <input type="text" value="" name="mobile_no" maxlength="10" id="mobile_no" > 
                        <span id="error_mobile_no" class="text-danger"></span>
                      </td>
                      <th>Bank IFSC Code: <span class="text-danger">*</span></th>
                      <td>
                        <input type="text" value="" name="bank_ifsc" maxlength="11" onkeyup="this.value = this.value.toUpperCase();" id="bank_ifsc">
                        <img src="{{ asset('images/ajaxgif.gif') }}" width="60px" id="ifsc_loader" style="display: none;">
                        <span id="error_bank_ifsc_code" class="text-danger">
                      </td>
                    </tr>
                    <tr>
                      <th>Bank Name: <span class="text-danger">*</span></th>
                      <td>
                        <input type="text" value="" name="bank_name" maxlength="200" id="bank_name" readonly>
                        <span id="error_name_of_bank" class="text-danger">
                      </td>
                      <th>Bank Branch Name: <span class="text-danger">*</span></th>
                      <td>
                        <input type="text" value="" name="branch_name" id="branch_name" readonly>
                        <span id="error_bank_branch" class="text-danger"></span>
                      </td>
                    </tr>
                    <tr>
                      <th>Bank Account Number: <span class="text-danger">*</span></th>
                      <td>
                        <input type="text" value="" name="bank_code" maxlength='20' id="bank_code">
                        <span id="error_bank_code" class="text-danger"></span>
                      </td>
                      <th></th>
                      <td></td>
                    </tr>
                    <tr>
                      <th scope="row" class="required" style="font-size: 14px;">Upload Bank Passbook<br>(Mandatory for change of account only)<span class="text-danger"></th>
                      <td id="bank_passbook_text"> 
                        <input type="file"  name="upload_bank_passbook" accept=".jpg,.jpeg,.png,.pdf" id="upload_bank_passbook" value="">
                        <span style="font-size: 14px;" id="error_file" class="text-danger"></span>
                      </td>
                      <th scope="row" style="font-size: 14px;">Copy Of Passbook</th>
                      <td  scope="row" class="encView">&nbsp;&nbsp;&nbsp;<a class="btn btn-xs btn-primary" href="javascript:void(0);" onclick="View_encolser_modal('Copy of Bank Pass book','10',1)">View</a></td>
                    </tr>
                  </table>
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
     
              
               <!-- </form>  -->
           </div> 
        </div>
      </div>
    </div>
        
    </div>
  @endsection
  <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
  <script>
    $(document).ready(function(){
        var interval = setInterval(function () {
        var momentNow = moment();
        $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
        $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);
        $('#loadingDi').hide();
        $('#submit_btn').removeAttr('disabled');
        var error_scheme_type = '';
        $('#submit_btn').click(function(){
            if($.trim($('#scheme_type').val()).length == 0){
                error_scheme_type = 'Scheme name is required';
                $('#error_scheme_type').text(error_scheme_type);
            }
            else{
                error_scheme_type = '';
                $('#error_scheme_type').text(error_scheme_type);
            }
            if($.trim($('#failed_type').val()).length == 0){
                error_failed_type = 'Failed Type is required';
                $('#error_failed_type').text(error_failed_type);
            }
            else{
                error_failed_type = '';
                $('#error_failed_type').text(error_failed_type);
            }
            if( error_scheme_type != '' || error_failed_type != ''){
                return false;
            }
            else{
            $('#loadingDi').show();
             $('#res_div').show();
                var msg = 'Scheme : '+$( "#scheme_type option:selected" ).text();
                $('#panel_head').text(msg);
                if ( $.fn.DataTable.isDataTable('#example') ) {
                    $('#example').DataTable().destroy();
                }
                $('#example tbody').empty();
                var table=$('#example').DataTable( {
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
                    url: "{{ url('getFailedBankListPaymentModeWise') }}",
                    type: "post",
                    data:function(d){
                    d.scheme_id = $('#scheme_type').val(),
                    d.payment_mode = $('#failed_type').val(),
                    d.filter_1 = $('#filter_1').val(),
                    d.filter_2 = $('#filter_2').val(),
                    d.mapLevel = $('#mapLevel').val(),
                    d.local_body = $('#local_body').val(),
                    d.district_code = $('#district_code').val(),
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
                    { "data": "ben_id" },
                    { "data": "ben_name" },
                    { "data": "mobile_no" },
                    { "data": "last_accno" },
                    { "data": "last_ifsc"},
                    { "data": "block_ulb_name"},
                    {"data":"gp_ward_name"},
                    { "data": "view" },
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
                            ],
                    });
                }
            });
            $('.js-municipality').change(function() {
                municipality=$('.js-municipality').val();  
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
            $('.modalEncloseClose').click(function(){
              $('.encolser_modal').modal('hide');
            }); 
    });
    function editFunction(value, scheme_id, failed_type){
        $('#loadingDi').show();
        $.ajax({
            type: 'post',
            url: "{{ route('getModalDataFailedBankEdit') }}",
            data: { scheme_id:scheme_id, failed_type:failed_type, id:value, _token: '{{ csrf_token() }}' },
            success: function (response) {
                $('#loadingDi').hide();
                  console.log(response);
                if (response.status == 1) {
                    $.alert({
                        title: response.title,
                        type: response.type,
                        icon: response.icon,
                        content: response.msg
                    });
                }
                else {
                     $('#name_div').text(response.ben_name);
                    // $('#father_div').text(response.father_name);
                    // $('#dob_div').text(response.dob);
                     $('#gender_div').text(response.gender);
                     $('#caste_div').text(response.caste);
                     $('#update_scheme_id').val(response.scheme_id);
                     $('#pension_id').val(response.id);
                     $('#mobile_no').val(response.mobile_no);
                     $('#bank_name').val(response.bank_name);
                     $('#bank_ifsc').val(response.bank_ifsc);
                     $('#bank_code').val(response.bank_code);
                     $('#branch_name').val(response.branch_name);
                     $('#application_id').text(response.id);
                     $('#failed_reason').text(response.failed_reason);
                     //$('#failed_type').val(response.failed_type);
                     var failed_type = response.failed_type;
                     if (failed_type == 'SBI') {
                      $('#mobile_no').prop('disabled', true);
                     }else{
                      $('#mobile_no').prop('disabled', false);
                     }
                     $('.loadingDivModal').hide();
                     $('#modalUpdatebank').modal('show');
                }
            },
            complete: function(){
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('#loadingDi').hide();
            ajax_error(jqXHR, textStatus, errorThrown); 
        }
        });
    }
    $(document).on('click', '#verifySubmit', function(){     
    var error_name_of_bank =''; 
    var error_bank_branch =''; 
    var error_bank_code =''; 
    var error_bank_ifsc_code =''; 
    var error_mobile_no ='';
    var error_file = '';

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

    if($.trim($('#bank_code').val()).length == 0)
    {
     error_bank_code = 'Bank Account Number is required';
     $('#error_bank_code').text(error_bank_code);
     $('#bank_code').addClass('has-error');
    }
    else
    {
     error_bank_code = '';
     $('#error_bank_code').text(error_bank_code);
     $('#bank_code').removeClass('has-error');
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

    

    if(error_name_of_bank !='' || error_bank_branch !=''||  error_bank_code !='' || error_bank_ifsc_code !='' ) {
      return false;
    }
    else
    {
      var old_bank_ifsc=$('#old_bank_ifsc').val();
      var old_bank_accno=$('#old_bank_code').val();
      var bank_ifsc=$('#bank_ifsc').val();
      var bank_account_number=$('#bank_code').val();
      var upload_bank_passbook = $('#upload_bank_passbook')[0].files;
      if((old_bank_ifsc!=bank_ifsc || bank_account_number!=old_bank_accno)&& upload_bank_passbook.length==0){
      $.confirm({
                    title: 'Required!',
                    type:'red',
                    icon: 'fa fa-warning',
                    content:'Please upload bank passbook copy.',
                });
                return false;
    }
      $.confirm({
        type: 'orange',
        title: 'Confirmation!',
        content: 'Are you sure want to update this beneficiary ?',
        icon: 'fa fa-warning',
        buttons: {
          confirm: {
            text: 'Confirm',
            btnClass: 'btn-blue',
            keys: ['enter', 'shift'],
            action: function(){
              // alert('OK');
              var beneficiary_Id = $('#pension_id').val();
              var updateSchemeId = $('#update_scheme_id').val();
              var new_bank_ifsc = $('#bank_ifsc').val();
              var new_bank_code = $('#bank_code').val();
              var new_bank_name = $('#bank_name').val();
              var new_branch_name = $('#branch_name').val();
              var new_mobile_no = $('#mobile_no').val();
              var failed_type = $('#failed_type').val();
              
              var token =  '{{csrf_token()}}';
              var fd= new  FormData();
              fd.append('id', beneficiary_Id);
              fd.append('scheme_id', updateSchemeId);
              fd.append('bank_ifsc', new_bank_ifsc);
              fd.append('bank_code', new_bank_code);
              fd.append('bank_name', new_bank_name);
              fd.append('branch_name', new_branch_name);
              fd.append('upload_bank_passbook', upload_bank_passbook[0]);
              fd.append('mobile_no', new_mobile_no);
              fd.append('failed_type', failed_type);
              fd.append('_token', token);
              $('.loadingDivModal').show();
              $.ajax({
                type: 'POST',
                url: "{{ route('updateFailedBankDetails') }}",
                data: fd,
                processData: false,
                contentType: false,
                success: function (response) {
                  $('.loadingDivModal').hide();
                  if (response.status == 1) {
                    $.alert({
                      title: response.title,
                      type: response.type,
                      icon: response.icon,
                      content: response.msg
                    });
                    $('#modalUpdatebank').modal('hide');
                    $('#res_div').hide();
                    $('#scheme_type').val('').trigger('change');
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
  $(document).on('blur', '#bank_ifsc', function() {
    let $ifsc_data = $.trim($('#bank_ifsc').val());
    
    // Set the trimmed value back to the input field
    $('#bank_ifsc').val($ifsc_data);
    
    const $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
    if($ifscRGEX.test($ifsc_data) && $ifsc_data.length === 11) {
        $('#bank_ifsc').removeClass('has-error');
        $('#error_bank_ifsc_code').text('');
        $('#ifsc_loader').show();

        $.ajax({
            type: 'POST',
            url: "{{ url('legacy/getBankDetails') }}",
            data: {
                ifsc: $ifsc_data,
                _token: '{{ csrf_token() }}',
            },
            success: function (data) {
                $('#ifsc_loader').hide();
                if (!data || data.length === 0) {
                    $('#error_bank_ifsc_code').text('No data found with the IFSC');
                    $('#bank_ifsc').addClass('has-error');
                    return;
                }
                data = JSON.parse(data);
                $('#bank_name').val(data.bank);
                $('#branch_name').val(data.branch);
            },
            error: function (ex) {
                $('#ifsc_loader').hide();
                $('#error_bank_ifsc_code').text('Data fetch error');
                $('#bank_ifsc').addClass('has-error');
            }
        });

    } else {
        $('#error_bank_ifsc_code').text('IFSC format invalid, please check the code');
        $('#bank_ifsc').addClass('has-error');
    }
});
  function View_encolser_modal(doc_name,doc_type,is_profile_pic){
    var id=$('#pension_id').val();
    var scheme_id=$('#update_scheme_id').val();
    $('#encolser_name').html('');
    $('#encolser_content').html('');
    $('#encolser_name').html(doc_name+'('+id+')');
    $('#encolser_content').html('<img   width="50px" height="50px" src="images/ZKZg.gif"/>');
    $('.loadingDivModal').show();
    $('.btnUpdate').attr('disabled',true);
    $.ajax({
      url: "{{ route('ajaxViewPassbookfailed') }}",
      type: "POST",
       data: {
       doc_type: doc_type,
       is_profile_pic: is_profile_pic,
       id: id,
       scheme_id:scheme_id,
       _token: '{{ csrf_token() }}',
       },
      }).done(function( data, textStatus, jqXHR ) {
        $('.btnUpdate').removeAttr('disabled',true);
        $('.loadingDivModal').hide();
      $('#encolser_content').html('');
      $('#encolser_content').html(data);
      $("#encolser_modal").modal();
      }).fail(function( jqXHR, textStatus, errorThrown ) {
        $('#encolser_content').html('');
        $('.btnUpdate').removeAttr('disabled',true);
        $('.loadingDivModal').hide();
        ajax_error(jqXHR, textStatus, errorThrown)
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