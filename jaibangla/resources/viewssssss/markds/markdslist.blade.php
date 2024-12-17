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
        <b>{{$type_des}} for the Scheme {{$scheme_name}}</b>
        @if($type==2 || $type==3 || $type==4)
        <div class='row pull-right'>
        <a href="oap-wcd" title="Back to Form"> 
                <img width="50px;" style="pull-right" src="{{ asset("images/back.png") }}" alt="Back to Form" />
        </a>
        </div>
        <br/>
        <br/>
        <br/>
        @endif
       
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
       
      <input type="hidden" name="action_type" id="action_type" value="1"/>
        <input type="hidden" name="dist_code" id="dist_code" value="{{$created_by_dist_code }}" class="js-district_1">
        
         <div class="row" style="">
         
         <div class="form-group col-md-4">
                 <label class="required-field">Application Type</label>
                 <select name="application_type" id="application_type" class="form-control"  >
                  
                 <option value="1" selected>Pending</option>

                 
                  <option value="2">Marked as Duare Sarkar Phase {{$camp_roman}}</option>

                </select>
                 <span id="error_application_type" class="text-danger"></span>
              </div>
          
          
          
          
          
         
           
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
         
          
         
           
         
        
         
          <div class="form-group col-md-4">
            <button type="button" name="filter" id="filter" class="btn btn-info" style="margin-top:30px;">Search</button>
            <button type="button" name="reset" id="reset" class="btn btn-default" style="margin-top:30px;">Reset</button>
          </div>
        </div>
       
       
        
       
          <br/><br/>
          <form method="post" id="register_form" 
                    class="submit-once">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="scheme_id" id="scheme_id" value="{{$scheme_id}}">

       

              
          
       <table id="example" class="display" cellspacing="0" width="100%"> 

        <thead>

                <tr role="row" class="sorting_asc" style="font-size: 12px;">
                <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
                <th  width="7%">Beneficiary ID</th>
                <th  width="7%">Aadhaar Number</th>
                <th width="12%">Beneficiary Name</th>
                <th width="12%">Mobile Number</th>
                <th width="12%">DOB</th>
                <th width="12%">Block/Munc Name</th>
                <th width="12%">GP/Ward Name</th>  
                <th width="17%">Action</th>
                
              </tr>
            </thead>
            <tbody>

            
              

             

               
            
            </tbody>
            <!-- <tfoot> -->
           
           
            <!-- </tfoot> -->

            
          
          
    </table>
</form>
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
      <form method="post"  action="{{url('DsmarkPost')}}" 
                    class="submit-once" name="form" id="sm-form">
      <input type="hidden" id="scheme_id" name="scheme_id" value="{{$scheme_id}}">
      <input type="hidden" id="ds_mark_phase" name="ds_mark_phase" value="{{$ds_mark_phase}}">
      <input type="hidden" id="type" name="type" value="{{$type}}">

      <input type="hidden" id="beneficiary_id" name="beneficiary_id"/>
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <h4 class="modal-title w-100">Do you really want to Mark as  {{$camp_roman}} for the Beneficiary(<span id="application_text_approve"></span>)?</h4>	
      
      <div class="row">
      <div class="form-group col-md-12" >
        <label class="">{{$camp_roman}} Registration No.</label>
        <input type="text" name="ds_registration_no" id="ds_registration_no" autocomplete="off" class="form-control" placeholder="Registration No." maxlength="25" value="{{ old('ds_registration_no') }}"    />
        <span id="error_ds_registration_no" class="text-danger"></span>
       
       </div>
      </div>
      
			<div class="modal-footer justify-content-center">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-info" id="confirm_yes" >Mark</button>
         <button type="button" id="submittingapprove" value="Submit" class="btn btn-success btn-lg"
                          disabled>Submitting please wait</button>
			</div>
</form>
		</div>
	</div>
 
</div>

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
    $("#confirm").hide();
    $("#submittingapprove").hide();
    $(".NumOnly").keyup(function(event) {
              
              $(this).val($(this).val().replace(/[^\d].+/, ""));
                  if ((event.which < 48 || event.which > 57)) {
                      event.preventDefault();
                  }
    }); 
    var base_url='{{ url('/') }}';
    var created_by_local_body_code=$("#created_by_local_body_code").val();
    var application_type=$("#application_type").val();
    var process_type=$("#process_type").val();
  fill_datatable(created_by_local_body_code,application_type,process_type);
  function fill_datatable(created_by_local_body_code = '',application_type = '',process_type = ''){
   // console.log(process_type);
       var scheme_id=$("#scheme_id").val();
        var dataTable=$('#example').DataTable( {
      
      oLanguage: {
      "sSearch": "Search using Aadhaar Number/Beneficiary Id:"
      },
      paging: true,
      pageLength:100,
      ordering: false,
      lengthMenu: [[20, 50,100,500,1000, -1], [20, 50,100,500,1000, 'All']],
      processing: true,
      serverSide: true,
      ajax:{
            url: "{{ url('markdslist') }}",
            type: "GET",
            data:function(d){
                 d.created_by_local_body_code= created_by_local_body_code,
                 d.scheme_id= scheme_id,
                 d.type= $('#type').val(),
                 d.application_type= application_type,
                 d.process_type= process_type,
                 d.type= '{{$type}}',
                 d.ds_mark_phase= '{{$ds_mark_phase}}',
                 d._token= "{{csrf_token()}}"
            },
            error: function (ex) {
              //console.log(ex);
             alert('Session time out..Please login again');
             window.location.href=base_url;
           }                       
      },
      
      columns: [
        { "data": "id"},
        { "data": "aadhar_no", visible: false},
        { "data": "name" },
        { "data": "mobile_no" },
        { "data": "dob"},
        { "data": "block_ulb_name" },
        { "data": "gp_ward_name" },
        { "data": "view" },
       
       // { "data": "check" },
               

      ],
      
    
    } );


   }

    $('#filter').click(function(){
        var created_by_local_body_code = $('#created_by_local_body_code').val();
        var application_type = $('#application_type').val();
        var process_type = $('#process_type').val();
        var designation_id_old = $('#designation_id_old').val();
        var error_application_type='';
        var error_process_type='';
        if(application_type=='')
        {
          error_application_type = 'Application Type is required';
          $('#error_application_type').text(error_application_type);
          $('#application_type').addClass('has-error');
        }
        else
        {
          error_application_type = '';
          $('#error_application_type').text(error_application_type);
          $('#application_type').removeClass('has-error');
        }
        if(designation_id_old=='Approver'){
        if(process_type=='')
        {
          error_process_type = 'Process Type is required';
          $('#error_application_type').text(error_process_type);
          $('#process_type').addClass('has-error');
        }
        else
        {
          error_process_type = '';
          $('#error_process_type').text(error_process_type);
          $('#process_type').removeClass('has-error');
        }
      }
      else{
        error_process_type=''
      }
        if(error_application_type=='' && error_process_type==''){
          //console.log(process_type);
          $('#example').DataTable().destroy();
          fill_datatable(created_by_local_body_code,application_type,process_type);
        }
        
       
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
        $('#process_type').val('');
        $('#gp_code').val('');
        $('#gp_code').val('');
        $('#example').DataTable().destroy();
        fill_datatable();
    });
    $(document).on('click', '.btn-sm', function() {
      $('#sm-form #beneficiary_id').val('');
      $('#application_text_approve').text('');
      $('.btn-sm').attr('disabled',false);
      var benid=$(this).val();
     
      $('#sm-form #beneficiary_id').val(benid);
      $('#application_text_approve').text(benid);
      $('#modalConfirm').modal();
    });

    $('#confirm_yes').on('click',function(e){
      var ds_mark_phase=$('#ds_mark_phase').val();
       var pass_ds_registration_no=1;
       /*
        var ds_registration_no=$('#ds_registration_no').val();
         if(ds_registration_no==''){
          alert('Please Enter Camp Registration No.');
          return false;
         }
         else{
          if($.trim($('#ds_registration_no').val()).length < 24)
          {
            alert('Please Enter Valid Camp Registration No.');
            return false;
          }
          else{
          var pass_ds_registration_no=1;
          }
         }
      
      */

       if(pass_ds_registration_no==1){
        e.preventDefault();
        var error_sm_mobile_no ='';
        $("#confirm_yes").hide();
        $("#submittingapprove").show();
        $("#sm-form").submit();
       }
       
       
      });
  } );
  function controlCheckBox(){
    //console.log('ok');
    var anyBoxesChecked = false;
    $(' input[type="checkbox"]').each(function() {
      if ($(this).is(":checked")) {
        anyBoxesChecked = true;
      }
    });
    if (anyBoxesChecked == true) {
      $("#confirm").show();
      document.getElementById('confirm').disabled = false;
    } else{
      $("#confirm").hide();
      document.getElementById('confirm').disabled = true;
    }
  }
</script>

</body>
</html>