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

        
       
        <div class='row'>
            <div>
             @if ( ($message = Session::get('message')))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
               
              </div>
              @endif
           @if(count($errors) > 0)
      <div class="alert alert-danger alert-block">
        <ul>
          @foreach($error as $error)
          <li><strong> {{ $error }}</strong></li>
          @endforeach
        </ul>
      </div>
      @endif
            </div>
       
      </div>
        
        
      </section>

      <!-- Main content -->
      <section class="content">
      
         <div class="row" style="">
         <div class="form-group col-md-4">
         <label class=" control-label">Filter::1 Blank Aadhaar/Mobile</label>
         <select name="filter_status_new" id="filter_status_new" class="form-control full-width" >
                  <option value="">-----All----</option>
                  <option value="1">Aadhaar Number Blank or Aadhar number is not 12 Digit</option>  
                  <option value="2">Mobile Number Blank or Mobile number is not 10 Digit</option> 
        
                </select>
          </div>
         <div class="form-group col-md-3">
            <label class="control-label" >Select Filter Criteria :Urban/Rural</label>
              <select name="rural_urban_code" id="rural_urban_code" class="form-control select2 full-width" >
                  <option value="">-----Select----</option>
                  @foreach(Config::get('constants.rural_urban') as $key=>$val)
                  <option value="{{$key}}" @if( old('urban_code') == $key)  selected  @endif >{{$val}}</option>
                  @endforeach     

              </select>
          </div> 
          <div class="form-group col-md-3">
            <label class="control-label" >Select Filter Criteria :<span id="blk_sub_txt">Block/Sub Division</span></label>
              <select name="created_by_local_body_code" id="created_by_local_body_code" class="form-control select2 full-width client-js-localbody1" >
                  <option value="">-----Select----</option>
                  

              </select>
          </div> 
           
        
           
          
          <div class="form-group col-md-4">
            <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
            <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button>
          </div>
        </div>
       
        <form class="row" method="POST" action="{{ route('bulkApprovewcdEdit') }}" class="submit-once">
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <input type="hidden" id="scheme_id" name="scheme_id" value="{{ $scheme_id }}">
        <input type="hidden" name="dist_code" id="dist_code" value="{{ $district_code }}" >
        <div >
              <button  style="border:1px solid black ;margin: 0% 0% 2% 0%;" type="submit" name="bulk_approve" id="bulk_approve" value="approve" class="btn btn-info col-sm-3 col-xs-5 btn-margin" disabled>
                         Approve
              </button></div>
       
       <table id="example" class="display" cellspacing="0" width="100%"> 

        <thead>

                <tr role="row" class="sorting_asc" style="font-size: 12px;">
                <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
                <th  width="7%">Aplication ID</th>
                <th width="12%">Applicant Name</th>
                <th width="12%">Aadhaar Number</th>
                <th width="12%">Mobile Number</th>
                <th width="12%">DOB</th>
                <th width="12%">Gender</th>
                <th width="12%">Block/Munc Name</th>
                <th width="12%">GP/Ward Name</th>                
                <th width="17%">Action</th>
                <th width="17%">Check</th>
                
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
    var base_url='{{ url('/') }}';
    $("#bulk_approve").hide();
   var rural_urban_code=$("#rural_urban_code").val();
   var created_by_local_body_code=$("#created_by_local_body_code").val();
   var filter_status_new = $('#filter_status_new').val();
  fill_datatable(rural_urban_code,created_by_local_body_code,filter_status_new);
  function fill_datatable(rural_urban_code = '',created_by_local_body_code = '',filter_status_new = ''){
       var scheme_id=$("#scheme_id").val();
        var dataTable=$('#example').DataTable( {
      //dom: 'Bfrtip',
      paging: true,
      pageLength:100,
      lengthMenu: [[20, 50,100,500,1000, -1], [20, 50,100,500,1000, 'All']],
      processing: true,
      serverSide: true,
      ajax:{
            url: "{{ url('workflowwcdEdit') }}",
            type: "GET",
            data:function(d){
                 d.rural_urban_code= rural_urban_code,
                 d.created_by_local_body_code= created_by_local_body_code,
                 d.scheme_id= scheme_id,
                 d._token= "{{csrf_token()}}"
            },
            error: function (ex) {
              //console.log(ex);
              alert('Session time out..Please login again');
              //window.location.href=base_url;
           }                       
      },
      columns: [
                
        { "data": "application_id" },
        { "data": "name" },
        { "data": "mask_aadhaar_no" },
        { "data": "mask_mobile_no" },
        { "data": "dob"},
        { "data": "gender" },
        { "data": "block_ulb_name" },
        { "data": "gp_ward_name" },
        { "data": "view" },
        { "data": "check" }
               

      ],          

    
    } );


   }

    $('#filter').click(function(){
        var rural_urban_code = $('#rural_urban_code').val();
        var created_by_local_body_code = $('#created_by_local_body_code').val();
        var filter_status_new = $('#filter_status_new').val();
        $('#example').DataTable().destroy();
        fill_datatable(rural_urban_code,created_by_local_body_code,filter_status_new);
        
    });

    $('#reset').click(function(){
        $('#rural_urban_code').val('');
        $('#created_by_local_body_code').val('');
        $('#filter_status_new').val('');
        //$('#filter_2').val('');
        $('#example').DataTable().destroy();
        fill_datatable();
    });
    
    $('#rural_urban_code').change(function() {
       var urban_code=$(this).val();
        if(urban_code==''){
          $('#created_by_local_body_code').html('<option value="">--All --</option>'); 
        }
        $('#created_by_local_body_code').html('<option value="">--All --</option>'); 
        select_district_code= $('#dist_code').val();
       
        
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

  } );
  function controlCheckBox(){
    console.log('ok');
    var anyBoxesChecked = false;
    $(' input[type="checkbox"]').each(function() {
      if ($(this).is(":checked")) {
        anyBoxesChecked = true;
      }
    });
    if (anyBoxesChecked == true) {
      $("#bulk_approve").show();
      document.getElementById('bulk_approve').disabled = false;
    } else{
      $("#bulk_approve").hide();
      document.getElementById('bulk_approve').disabled = true;
    }
  }
</script>

</body>
</html>