<?php 

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>Jb | Jai Bangla</title>

  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
      <link href="{{ asset("css/select2.min.css") }}" rel="stylesheet">
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet" type="text/css" />  
  
   
   
   <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
   <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">

   

   
   <style>
   .errorField{
    border-color: #990000;
  }
  .searchPosition{
    margin:70px;
  }
  .submitPosition{
    margin: 25px 0px 0px 0px;
  }
  .required-field::after {
      content: "*";
      color: red;
}
  
  .typeahead { border: 2px solid #FFF;border-radius: 4px;padding: 8px 12px;max-width: 300px;min-width: 290px;background: rgba(66, 52, 52, 0.5);color: #FFF;}
  .tt-menu { width:300px; }
  ul.typeahead{margin:0px;padding:10px 0px;}
  ul.typeahead.dropdown-menu li a {padding: 10px !important;  border-bottom:#CCC 1px solid;color:#FFF;}
  ul.typeahead.dropdown-menu li:last-child a { border-bottom:0px !important; }
  .bgcolor {max-width: 550px;min-width: 290px;max-height:340px;background:url("world-contries.jpg") no-repeat center center;padding: 100px 10px 130px;border-radius:4px;text-align:center;margin:10px;}
  .demo-label {font-size:1.5em;color: #686868;font-weight: 500;color:#FFF;}
  .dropdown-menu>.active>a, .dropdown-menu>.active>a:focus, .dropdown-menu>.active>a:hover {
    text-decoration: none;
    background-color: #1f3f41;
    outline: 0;
  }
  table.dataTable thead th, table.dataTable thead td{
    padding:10px 13px;
  }
  table.dataTable tfoot th, table.dataTable tfoot td{
    padding:10px 5px;
  }

  .criteria1{
    text-transform: uppercase;
    font-weight: bold;
  }
  
  #example_length{
    margin-left: 40%;
    margin-top: 2px;
  }
  @keyframes spinner {
  to {transform: rotate(360deg);}
}
 
.spinner:before {
  content: '';
  box-sizing: border-box;
  position: absolute;
  top: 50%;
  left: 50%;
  width: 20px;
  height: 20px;
  margin-top: -10px;
  margin-left: -10px;
  border-radius: 50%;
  border: 2px solid #ccc;
  border-top-color: #333;
  animation: spinner .6s linear infinite;
}
.select2{
    width:100%!important;
  }
  .select2 .has-error {
    border-color:#cc0000;
   background-color:#ffff99;
}
</style>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- Google Font -->
<link rel="stylesheet"
href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

</head>
<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    
    <!-- Main Header -->
    @include('layouts.header')
    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <b>Stop payment and Rejection for the Scheme {{$scheme_name}}</b>
        
       
        <div class='row'>
        @if ( ($message = Session::get('message')))
                <div class="alert alert-success alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }}</strong>
                  

                </div>
      @endif
      @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }}</strong>
                  

                </div>
      @endif
      @if ( ($error = Session::get('error')))
                <div class="alert alert-danger alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $error }}</strong>
                  

                </div>
      @endif
      @if(count($errors) > 0)
      <div class="alert alert-danger alert-block">
        <ul>
          @foreach($errors as $error)
          <li><strong> {{ $error }}</strong></li>
          @endforeach
        </ul>
      </div>
      @endif
      </div>
        
        
      </section>

      <!-- Main content -->
      <section class="content">
            @if($verifier_type=='Department')
              <div class="form-group col-md-4">
            <label class="required-field control-label" >Select District</label>
              <select name="dist_code" id="dist_code" class="form-control full-width js-district_1" >
                  <option value="">-----Select----</option>
                   @foreach ($district_list as $dist_item)
                          <option value="{{$dist_item->district_code}}" > {{$dist_item->district_name}}</option>
                  @endforeach

              </select>
              <span id="error_dist_code" class="text-danger"></span>
              </div> 
            
            @else      
        <input type="hidden" name="dist_code" id="dist_code" value="{{ $district_code }}" class="js-district_1">
          @endif
         <div class="row" style="">
         <div class="form-group col-md-4">
                 <label class="required-field">Type</label>
                 <select name="report_type" id="report_type" class="form-control">
                <option value="1">Payment Validation Failed</option>
                <!-- <option value="2">Duplicate Bank</option>-->
                 <!-- <option value="3">Duplicate Aadhaar</option>-->
                 <option value="4">Deactivated</option>
                 <option value="5">Name Validation Rejection from Bank</option>
                 <option value="6">Duplicate Bank Rejection</option>  
                 <option value="7">Name Validation Rejection from PDS</option>

                </select>
                 <span id="error_report_type" class="text-danger"></span>
              </div>
          
          @if($verifier_type=='Block')
          <div class="form-group col-md-4">
            <label class=" control-label" >Gram Panchayat</label>
              <select name="gp_ward_code" id="gp_ward_code" class="form-control full-width" >
                  <option value="">-----Select----</option>
                   @foreach ($gps as $gp)
                          <option value="{{$gp->gram_panchyat_code}}" > {{$gp->gram_panchyat_name}}</option>
                  @endforeach

              </select>
          </div> 
          <input type="hidden" name="block_ulb_code" value="" id="block_ulb_code">
          <input type="hidden" name="rural_urban_code"  id="rural_urban_code" value="{{$is_rural}}">
          <input value="{{$created_by_local_body_code}}" type="hidden" name="created_by_local_body_code"  id="created_by_local_body_code">
           @endif
           @if($verifier_type=='Subdiv')
        <div class="form-group col-md-3">
            <label class=" control-label" >Municipality</label>
              <select name="block_ulb_code" id="block_ulb_code" class="form-control select2 full-width js-municipality" >
                  <option value="">-----Select----</option>
                   @foreach ($urban_bodys as $urban_body)
                          <option value="{{$urban_body->urban_body_code}}" > {{$urban_body->urban_body_name}}</option>
                  @endforeach

              </select>
          </div> 
          <div class="form-group col-md-3">
            <label class=" control-label" >Wards</label>
              <select name="gp_ward_code" id="gp_ward_code" class="form-control select2 full-width js-wards" >
                  <option value="">-----Select----</option>
                  

              </select>
          </div> 
          <input type="hidden" name="rural_urban_code"  id="rural_urban_code" value="{{$is_rural}}">
          <input value="{{$created_by_local_body_code}}" type="hidden" name="created_by_local_body_code"  id="created_by_local_body_code">
           @endif
           @if($designation_id=='Approver' || $verifier_type=='Department')
         
       
        <div class="form-group col-md-3">
            <label class=" control-label" >Urban/Rural</label>
              <select name="rural_urban_code" id="rural_urban_code" class="form-control" >
                  <option value="">-----All----</option>
                   @foreach(Config::get('constants.rural_urban') as $key=>$val)
                  <option value="{{$key}}">{{$val}}</option>
                  @endforeach     

              </select>
          </div> 
          <div class="form-group col-md-3">
            <label class=" control-label" ><span id="blk_sub_txt">Block/Sub Division</span></label>
              <select name="created_by_local_body_code" id="created_by_local_body_code" class="form-control select2 full-width js-wards" >
                  <option value="">-----Select----</option>
                  

              </select>
          </div>
         
           @endif
          
          <div class="form-group col-md-4">
            <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
            <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button>
          </div>
        </div>
        </form>
        
          <form action="stop-list-excel" method="post" id="excel_form">
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <input type="hidden" name="scheme_id" id="scheme_id" value="{{$scheme_id}}">
           <input type="submit" name="submit" class="btn btn-success" id="excel-download" value="Export to Excel" 
           style="display:{{$download_excel==0?'none':''}}"/>
          </form>  
           
       <table id="example" class="display" cellspacing="0" width="100%"> 

        <thead>

                <tr role="row" class="sorting_asc" style="font-size: 12px;">
                <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
                <th  width="7%">Beneficiary ID</th>
                <th width="12%">Beneficiary Name</th>
                <th width="12%">Mobile Number</th>
                <th width="12%">Bank IFSC</th>
                <th width="12%">Bank Account Number</th>
                @if($verifier_type=='Department')
                <th width="12%">District</th>
                @endif
                @if($verifier_type=='Subdiv' || $verifier_type=='District')
                <th width="12%">Block/Munc Name</th>
                @endif
                <th width="12%">GP/Ward Name</th>  
                <th width="17%">Status</th>
               
              </tr>
            </thead>
            <tbody>

            
              

             

               
            
            </tbody>
            <!-- <tfoot> -->
           
            </form>
            <!-- </tfoot> -->

            
          
          
    </table>
 <div class="row">
            
            <div class="col-sm-7">
               <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                
              </div>
            </div>
  </div>
  </div>
 
</div>
<div id="modalConfirm" class="modal fade">
  
  
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header flex-column">
								
			
			</div>
			<div class="modal-body">
      <h4 class="modal-title w-100">Do you really want to Approve?</h4>	
       
         
			</div>
			<div class="modal-footer justify-content-center">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-info" id="confirm_yes" >OK</button>
         <button type="button" id="submittingapprove" value="Submit" class="btn btn-success btn-lg"
                          disabled>Submitting please wait</button>
			</div>
		</div>
	</div>
 
</div>
</form>
<!-- /.row -->

</section>
<!-- /.content -->
</div>

<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>


<script>

  $(document).ready(function() {  
    //alert('ok');
    var base_url='{{ url('/') }}';
    var dist_code=$("#dist_code").val();
    var created_by_local_body_code=$("#created_by_local_body_code").val();
    var block_ulb_code=$("#block_ulb_code").val();
    var gp_ward_code=$("#gp_ward_code").val();
    var report_type=$("#report_type").val();
   if(dist_code!=''){
     fill_datatable(dist_code,created_by_local_body_code,block_ulb_code,gp_ward_code,report_type);
   }
  function fill_datatable(dist_code = '',created_by_local_body_code = '',block_ulb_code = '',gp_ward_code = '',report_type = ''){
    //console.log(process_type);
       var scheme_id=$("#scheme_id").val();
        var dataTable=$('#example').DataTable( {
      //dom: 'Bfrtip',
      paging: true,
      pageLength:100,
      ordering: false,
      lengthMenu: [[20, 50,100,500,1000, -1], [20, 50,100,500,1000, 'All']],
      processing: true,
      serverSide: true,
      ajax:{
            url: "{{ url('stop-list') }}",
            type: "GET",
            data:function(d){
                 d.dist_code= dist_code,
                 d.created_by_local_body_code= created_by_local_body_code,
                 d.block_ulb_code= block_ulb_code,
                 d.gp_ward_code= gp_ward_code,
                 d.scheme_id= scheme_id,
                 d.report_type= report_type,
                 d._token= "{{csrf_token()}}"
            },
            error: function (ex) {
              //console.log(ex);
            // alert('Session time out..Please login again');
             //window.location.href=base_url;
           }                       
      },
      columns: [
                
        { "data": "id" },
        { "data": "name" },
        { "data": "mobile_no" },
        { "data": "bank_ifsc" },
        { "data": "bank_code" },
        @if($verifier_type=='Department')
        { "data": "district_name" },
        @endif
        @if($verifier_type=='Subdiv' || $verifier_type=='District')
        { "data": "block_ulb_name" },
        @endif
        { "data": "gp_ward_name" },
        { "data": "status" },
       // { "data": "check" },
               

      ],          

    
    } );


   }

    $('#filter').click(function(){
        var block_ulb_code = $('#block_ulb_code').val();
        var gp_ward_code = $('#gp_ward_code').val();
        var report_type = $('#report_type').val();
        var dist_code = $('#dist_code').val();
        var created_by_local_body_code = $('#created_by_local_body_code').val();
        var error_report_type='';
        var error_dist_code='';
        if(report_type=='')
        {
          error_report_type = 'Type is required';
          $('#error_report_type').text(error_report_type);
          $('#report_type').addClass('has-error');
        }
        else
        {
          error_report_type = '';
          $('#error_report_type').text(error_report_type);
          $('#report_type').removeClass('has-error');
        }
        if(dist_code=='')
        {
          error_dist_code = 'District is required';
          $('#error_dist_code').text(error_dist_code);
          $('#dist_code').addClass('has-error');
        }
        else
        {
          error_dist_code = '';
          $('#error_dist_code').text(error_dist_code);
          $('#dist_code').removeClass('has-error');
        }
        if(error_report_type=='' && error_dist_code==''){
          //console.log(process_type);
          $('#example').DataTable().destroy();
          fill_datatable(dist_code,created_by_local_body_code,block_ulb_code,gp_ward_code,report_type);
          $("#excel-download").show();
        }
        else{
          $("#excel-download").hide();
          $('#example').DataTable().destroy();
        }
        
       
    });
    $('#dist_code').change(function() {
    $('#rural_urban_code').val('');
    $('#created_by_local_body_code').val('');
    });
    $('#block_ulb_code').change(function() {
      var municipality_code=$(this).val();
       if(municipality_code!=''){
        $('#gp_ward').html('<option value="">--All --</option>');
        var htmlOption='<option value="">--All--</option>';
          $.each(ulb_wards, function (key, value) {
                if(value.urban_body_code==municipality_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        $('#gp_ward_code').html(htmlOption);
       }
       else{
          $('#gp_ward_code').html('<option value="">--All --</option>');
       } 
    });
    $('#rural_urban_code').change(function() {
       var urban_code=$(this).val();
        if(urban_code==''){
          $('#created_by_local_body_code').html('<option value="">--All --</option>'); 
        }
        $('#created_by_local_body_code').html('<option value="">--All --</option>'); 
        select_district_code= $('#dist_code').val();
       //console.log(select_district_code);
        
        select_body_type= urban_code;
        var htmlOption='<option value="">--All--</option>';
        if(select_body_type==2){
            $("#blk_sub_txt").text('Block');
            $.each(blocks, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        }else if(select_body_type==1){
            $("#blk_sub_txt").text('Subdivision');
            $.each(subDistricts, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        } 
        else{
          $("#blk_sub_txt").text('Block/Subdivision');
        }   
        $('#created_by_local_body_code').html(htmlOption);
        

    });
      $('#reset').click(function(){
        $('#application_type').val('');
        $('#gp_code').val('');
        $('#gp_code').val('');
        $('#example').DataTable().destroy();
        fill_datatable();
    });
    $('#excel-download').on('click',function(){ 
      var dist_code=$("#dist_code").val();
      var created_by_local_body_code=$("#created_by_local_body_code").val();
      var report_type=$("#report_type").val();
      var ds_phase=$("#ds_phase").val();
      var rural_urbanid=$("#rural_urbanid").val();
      var urban_body_code=$("#urban_body_code").val();
      var block_ulb_code=$("#block_ulb_code").val();
      var gp_ward_code=$("#gp_ward_code").val();
      //alert(dist_code);
      $('#excel_form').append('<input type="hidden" name="dist_code" id="dist_code" value="'+dist_code+'">');
      $('#excel_form').append('<input type="hidden" name="created_by_local_body_code" id="created_by_local_body_code" value="'+created_by_local_body_code+'">');
      $('#excel_form').append('<input type="hidden" name="report_type" id="report_type" value="'+report_type+'">');
      $('#excel_form').append('<input type="hidden" name="ds_phase" id="ds_phase" value="'+ds_phase+'">');
      $('#excel_form').append('<input type="hidden" name="rural_urbanid" id="rural_urbanid" value="'+rural_urbanid+'">');
      $('#excel_form').append('<input type="hidden" name="urban_body_code" id="urban_body_code" value="'+urban_body_code+'">');
      $('#excel_form').append('<input type="hidden" name="block_ulb_code" id="block_ulb_code" value="'+block_ulb_code+'">');
      $('#excel_form').append('<input type="hidden" name="gp_ward_code" id="gp_ward_code" value="'+gp_ward_code+'">');
      $("#excel_form").submit();
});
  } );

</script>

</body>
</html>