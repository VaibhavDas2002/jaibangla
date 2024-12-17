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
                Old Age Pension Verified List
            </h1>
        </section>
        <section class="content">
            <div class="box box-default">
                <div class="box-body">
                    <input type="hidden" name="dist_code" id="dist_code" value="{{ $dist_code }}" class="js-district_1">
                    <div class="panel panel-default">
                        <div class="panel-heading">Filter Criteria</div>
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
                                    <label class="control-label">Rural/Urban<span class="text-danger">*</span> </label>
                                    <select name="filter_1" id="filter_1" class="form-control">
                                        <option value="">-----Select----</option>
                                        @foreach ($levels as $key=>$value)
                                        <option value="{{$key}}"> {{$value}}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger" id="error_filter_1"></span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="control-label" id="blk_sub_txt">Block/Sub Division<span class="text-danger">*</span> </label>
                                    <select name="filter_2" id="filter_2" class="form-control">
                                        <option value="">-----Select----</option>
                                    </select>
                                    <span class="text-danger" id="error_filter_2"></span>
                                </div>
                                <div class="form-group col-md-3">
                                  <label class="control-label" id="blk_sub_txt">Caste</label>
                                  <select name="caste" id="caste" class="form-control">
                                    <option value="">-----Select----</option>
                                    @foreach(Config::get('constants.caste') as $key=>$val)
                                      <option value="{{$key}}">{{$val}}</option>
                                    @endforeach 
                                  </select>
                                  <span class="text-danger" id="error_filter_2"></span>
                                </div>
                                <input type="hidden" name="scheme_type" id="scheme_type" value={{$scheme_id}}>
                                <div class="form-group col-md-3" style="margin-top: 24px;">
                                    <button type="button" name="filter" id="filter" class="btn btn-success"><i class="fa fa-search"></i> Search</button>&nbsp;&nbsp;
                                    <button type="button" name="reset" id="reset" class="btn btn-warning"><i class="fa fa-refresh"></i> Reset</button>
                                </div>
                            </div>  
                        </div>
                        
                    </div>
                    <div class="panel panel-default" id="res_div" style="display: none;">
                      <div class="panel-heading" id="panel_head">OAP Verified Beneficiary List</div>
                      <div class="table-responsive">
                          <table id="example" class="display" cellspacing="0" width="100%"> 
                              <thead style="font-size: 12px;">
                                  <th >Beneficiary ID</th>
                                  <th >Beneficiary Name</th>
                                  <th >Mobile No</th>
                                  <th>Caste</th>
                                  <th >Beneficiary Account No</th>
                                  <th >Beneficiary IFSC</th>
                                  <th >Block/Municipality Name</th>
                                  <th >GP/Ward Name</th>
                                  <th>Action</th>
                              </thead>
                              <tbody style="font-size: 14px;"></tbody>   
                          </table>
                      </div>
                  </div>
                </div>
            </div>
            <div class="modal fade  ben_view_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">Beneficiary Rejection</h4>
                        </div>
                        <div class="modal-body">
                            <h4 class="modal-title w-100" style="text-align: center; align-content: center; color:red;" >Beneficiary ID: <span id="ben_id">?</span></h4>	
                            <br>
                            <form method="POST" action="#" target="_blank" name="fullForm" id="fullForm" style="text-align: center; align-content: center;">
                                <div class="panel-group"> 
                                    <div class="panel panel-default">
                                        <div class="panel-body" style="padding: 5px;"> 
                                            <div class="form-group col-md-2">
                                                <label class="" for="heading">Enter Remarks:</label>
                                                <textarea style="margin: 0px; width: 300px; height: 60px;" name="accept_reject_comments" id="accept_reject_comments" class="form-control" maxlength="100"></textarea>
                                            </div>
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="app_id" id="app_id"  />
                                            <input type="hidden" id="schemes_id" name="schemes_id"/>
                                            {{-- <input type="hidden" id="created_dist_code" name="created_dist_code"/>
                                            <input type="hidden" name="created_local_body" id="created_local_body" /> --}}
                                        </div>
                                    </div>
                                </div>    
                                <button type="button" class="btn btn-info" id="verifyReject">Reject</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
  <script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
          $('.sidebar-menu li').removeClass('active');
          $('.sidebar-menu #bankTrFailed').addClass("active"); 
          $('.sidebar-menu #accValTrFailedVerified').addClass("active"); 
          $('#opreation_type').val('A');
          $('#div_rejection').hide();
          
          
          var error_filter_2 ='';
          var error_filter_1 = '';
          $('#filter').click(function(){
            if($.trim($('#filter_1').val()).length == 0){
              error_filter_1 = 'Rural/Urban is required';
                $('#error_filter_1').text(error_filter_1);
            }
            else{
              error_filter_1 = '';
                $('#error_filter_1').text(error_filter_1);
            }
            if($.trim($('#filter_2').val()).length == 0){
              error_filter_2 = 'Block/Subdivision is required';
                $('#error_filter_2').text(error_filter_2);
            }
            else{
              error_filter_2 = '';
                $('#error_filter_2').text(error_filter_2);
            }
            if( error_filter_1 != '' || error_filter_2 != ''){
                return false;
            }
            else{
            $('#loadingDiv').show();
            $('#res_div').show();
            if ( $.fn.DataTable.isDataTable('#example') ) {
                    $('#example').DataTable().destroy();
            }
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
              url: "{{ url('oap-wcd-verified-rejection-list') }}", 
              type: "post",
              data:function(d){
                  d.filter_1 = $('#filter_1').val(),
                  d.filter_2 = $('#filter_2').val(),
                  d.scheme_type=$('#scheme_type').val(),
                  d.caste = $('#caste').val(),
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
                    { "data": "beneficiary_id" },
                    { "data": "name" },
                    { "data": "mobile_no" },
                    { "data": "caste" },
                    { "data": "last_accno" },
                    { "data": "last_ifsc"},
                    { "data": "block_ulb_name"},
                    { "data":"gp_ward_name" },
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
              //'pdf','excel','print'
            ],
          });
            }
          });
          $('#example').on( 'page.dt', function () {
            $('#approve_rejdiv').hide();
          });
          $(document).on('click', '.ben_view_button', function() {
            $('#loader_img_personal').show();
            // $('.ben_view_button').attr('disabled',true);
            var benid=$(this).val();
            $('.ben_view_body').addClass('disabledcontent');
            $.ajax({
            type: 'post',
            url: "{{route('oap-wcd-verified-rejection-view')}}",
            data: {_token:'{{csrf_token()}}', 
            benid:benid,
            },
                dataType: 'json',
                success: function (response) {
                //   console.log(JSON.stringify(response));
                $('#ben_id').text(response.id);
                $('#app_id').val(response.id);
                $('#schemes_id').val(response.scheme_id);
                $('#created_dist_code').val(response.dist_code);
                $('#created_local_body').val(response.block_code);
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
        $(document).on('click', '.ben_view_details', function() {
        $('#loader_img_personal').show();
        $('.ben_view_details').attr('disabled', true);
        var benid = $(this).val();
        var redirectUrl = "{{ route('oap-wcd-verified-rejection_view_details') }}" + "?benid=" + benid;
        window.location.href = redirectUrl;
    });    
    $(document).on('click', '#verifyReject', function() {   
      var accept_reject_comments = $('#accept_reject_comments').val();
      var applicantId = $('#app_id').val();
      var scheme_id = $('#schemes_id').val();
      var district_code = $('#created_dist_code').val();
      var block_code = $('#created_local_body').val();
        $.confirm({
          title: 'Warning',
          type: 'orange',
          icon: 'fa fa-warning',
          content: '<strong>Are you sure to proceed?</strong>',
          buttons: {
            Ok: function(){
              $("#submitting").show();
              $("#verifyReject").hide();
              $.ajax({
                type: 'POST',
                url: "{{ url('oap-wcd-verified-rejection-post') }}",
                data: {
                    applicantId: applicantId,
                    scheme_id: scheme_id,
                    accept_reject_comments: accept_reject_comments,
                    district_code: district_code,
                    block_code: block_code,
                  _token: '{{ csrf_token() }}',
                },
                success: function (data) {
                  // console.log(data);
                  //console.log(JSON.stringify(data));
                 // dataTable.ajax.reload();
                 var table_renew = $('#example').DataTable(); 
                 table_renew.ajax.reload( null, false );
                 $('#accept_reject_comments').val('');
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
              // $("#verifyReject").show();  // Re-enable reject button
              // $("#submitting").hide(); 
            },
          }
        });      
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
     // ------------ Master DropDown Section Start-------------------- //
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
    </script>
  @stop