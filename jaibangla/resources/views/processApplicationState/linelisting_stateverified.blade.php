<?php 

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>JB | Jai Bangla</title>

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
          @foreach($errors->all() as $error)
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
        <div class="form-group col-md-2">
            <label class="control-label" >Select District</label>
              <select name="district_state" id="district_state" class="form-control full-width" >
                  <option value="">-----Select----</option>
                   @foreach ($districts as $district)
                  <option value="{{$district->district_code}}"> {{$district->district_name}}</option>
                  @endforeach
              </select>
          </div>          
          <div class="form-group col-md-3" >
            <label class=" control-label" >Rural/ Urban</label>
              <select name="urban_code_state" id="urban_code_state" class="form-control full-width" >
                  <option value="">-----Select----</option>
                 @foreach ($levels as $key=>$value)
                          <option value="{{$key}}" > {{$value}}</option>
                  @endforeach
              </select>
          </div>
          <div class="form-group col-md-3">
            <label class=" control-label" id="blk_sub_txt">Block/Sub Division</label>
              <select name="block_subdiv_state" id="block_subdiv_state" class="form-control" >
                  <option value="">-----Select----</option>
              </select>
          </div>          
          <div class="form-group col-md-3" style="margin-top:20px;">
            <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
            <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button>
          </div>
        </div>
       

        <form class="row" method="POST" action="{{ route('nhmemployee.MassEmployeeApproval') }}" class="submit-once">
       <div >
              <input type="hidden" name="_token" value="{{ csrf_token() }}">-
              <!-- <button  style="border:1px solid black ;margin: 0% 0% 2% 0%;" type="submit" name="bulk_approve" id="bulk_approve" value="approve" class="btn btn-info col-sm-3 col-xs-5 btn-margin" disabled>
                         Approve
              </button></div> -->
       
       <table id="example" class="display" cellspacing="0" width="100%"> 

        <thead>

                <tr role="row" class="sorting_asc" style="font-size: 12px;">
                <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
                <th  width="7%" >Aplication ID</th>
                <th width="12%">Applicant Name</th>
                <th width="12%">DOB</th>
                <th width="12%">Gender</th>
                 <th width="12%">Block/Municipality</th>
                <th width="12%">GP/Ward Name</th> 
                <th width="12%">Account No</th>                             
                <th width="17%">Action</th>
                
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
<script src="{{ asset("js/select2.full.min.js") }}"></script>
<script src="{{ URL::asset('js/site.js') }}"></script>
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>

<script>
  $('.select2').select2();
</script>

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
   
$('#district_state').change(function() {
      var district=$(this).val();
      //alert(district);
        $('#urban_code_state').val('');
        $('#block_subdiv_state').html('<option value="">--Select --</option>');
});    
$('#urban_code_state').change(function() {
       var urban_code=$(this).val();
        if(urban_code==''){
          $('#block_subdiv_state').html('<option value="">--Select --</option>'); 
        }
        $('#block_subdiv_state').html('<option value="">--Select --</option>');
        select_district_code= $('#district_state').val();
        if(select_district_code==''){
               alert('Please Select District First');
               $("#district_state").focus();
               $("#urban_code_state").val('');
        }
        else{
        select_body_type= urban_code;
        var htmlOption='<option value="">--Select--</option>';
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
        $('#block_subdiv_state').html(htmlOption);
        }

    });
     var dataTable = "";
   var dataTable=$('#example').DataTable( {
      //dom: 'Bfrtip',
      dom: 'Blfrtip',
      paging: true,
      ordering: false,
      pageLength:100,
      lengthMenu: [[20, 50,100,500,1000, -1], [20, 50,100,500,1000, 'All']],
      processing: true,
      serverSide: true,
      ajax:{
            url: "{{ url('workflow') }}",
            type: "POST",
            data:function(d){
                 d.district_code= $('#district_state').val(),
                 d.urban_code= $('#urban_code_state').val(),
                 d.block_subdiv_code= $('#block_subdiv_state').val(),
                 d._token= "{{csrf_token()}}"
            }                
      },
      columns: [
                
        { "data": "id" },
        { "data": "name" },
        { "data": "date_formated"},
        { "data": "gender" },
        { "data": "block_ulb_name" },
        { "data": "gp_ward_name" },
        { "data": "bank_code" },
        { "data": "view" },
               

      ],          

      buttons: [
       {
           extend: 'pdf',
           //title: 'Line Listing Report of ',
           // messageTop:'Filter Criteria:\n Level:<?php //if($level1=='State'){echo ' State';}elseif($level1=='District'){echo ' District';}elseif($level1=='ULB'){echo ' ULB';}elseif($level1=='Block'){echo ' Block';} ?>\n <?php //if($level1=='State'){echo 'State: ';echo $state_name;}elseif($level1=='District'){echo 'District: ';echo $district_name;}elseif($level1=='ULB'){echo 'District: ';echo $district_name;echo'\n'; echo 'ULB: ';echo $urban_body_name;}elseif($level1=='Block'){echo 'District:';echo $district_name;echo'\n';echo 'Block: ';echo $taluka_name;} ?>\n <?php //if($level3!=null){ echo'Posting Level: ';echo $level3;} ?>\n <?php //if($level4!=null){ echo'Posting Place: ';echo $place_name;} ?>\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: <?php //if($level1a==1){echo 'Jai Johar(for ST)';}elseif($level1a==2){echo 'Manabik';}elseif($level1a==3){echo 'Taposili Bandhu(for SC)';}?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5],

            }
       },
       {
           extend: 'print',
           //title: 'Line Listing Report of ',
           // messageTop:'<strong><u>Filter Criteria:</u></strong><br> <strong> Level:</strong><?php  //if($level1=='State'){echo ' State';}elseif($level1=='District'){echo ' District';}elseif($level1=='ULB'){echo ' ULB';}elseif($level1=='Block'){echo ' Block';} ?><br> <?php //if($level1=='State'){echo '<strong>State:</strong> ';echo $state_name;}elseif($level1=='District'){echo '<strong>District:</strong> ';echo $district_name;}elseif($level1=='ULB'){echo '<strong>District:</strong> ';echo $district_name;echo'<br>'; echo '<strong>ULB:</strong> ';echo $urban_body_name;}elseif($level1=='Block'){echo '<strong>District:</strong>';echo $district_name;echo'<br>';echo '<strong>Block:</strong> ';echo $taluka_name;} ?><br> <?php //if($level3!=null){ echo'<strong>Posting Level:</strong> ';echo $level3;} ?><br> <?php //if($level4!=null){ echo'<strong>Posting Place:</strong> ';echo $place_name;} ?><br><strong>Date:</strong><?php //echo date('d/m/Y');  ?>\n Scheme Name: <?php// if($level1a==1){echo 'Jai Johar(for ST)';}elseif($level1a==2){echo 'Manabik';}elseif($level1a==3){echo 'Taposili Bandhu(for SC)';}?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5],
                stripHtml: false,
            }
       },
       {
           extend: 'excel',
           //title: 'Line Listing Report of ',
            // messageTop:'Filter Criteria:\n  Level:<?php //if($level1=='State'){echo ' State';}elseif($level1=='District'){echo ' District';}elseif($level1=='ULB'){echo ' ULB';}elseif($level1=='Block'){echo ' Block';} ?>\n <?php //if($level1=='State'){echo 'State: ';echo $state_name;}elseif($level1=='District'){echo 'District: ';echo $district_name;}elseif($level1=='ULB'){echo 'District: ';echo $district_name;echo'\n'; echo 'ULB: ';echo $urban_body_name;}elseif($level1=='Block'){echo 'District:';echo $district_name;echo'\n';echo 'Block: ';echo $taluka_name;} ?>\n <?php//if($level3!=null){ echo'Posting Level: ';echo $level3;} ?>\n <?php //if($level4!=null){ echo'Posting Place: ';echo $place_name;} ?>\n Date:<?php //echo date('d/m/Y');  ?>\n Scheme Name: <?php //if($level1a==1){echo 'Jai Johar(for ST)';}elseif($level1a==2){echo 'Manabik';}elseif($level1a==3){echo 'Taposili Bandhu(for SC)';}?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5],
                stripHtml: false,
            }
       },
        {
           extend: 'copy',
           //title: 'Line Listing Report of ',
            // messageTop:'Filter Criteria:\n  Level:<?php// if($level1=='State'){echo ' State';}elseif($level1=='District'){echo ' District';}elseif($level1=='ULB'){echo ' ULB';}elseif($level1=='Block'){echo ' Block';} ?>\n <?php// if($level1=='State'){echo 'State: ';echo $state_name;}elseif($level1=='District'){echo 'District: ';echo $district_name;}elseif($level1=='ULB'){echo 'District: ';echo $district_name;echo'\n'; echo 'ULB: ';echo $urban_body_name;}elseif($level1=='Block'){echo 'District:';echo $district_name;echo'\n';echo 'Block: ';echo $taluka_name;} ?>\n <?php //if($level3!=null){ echo'Posting Level: ';echo $level3;} ?>\n <?php //if($level4!=null){ echo'Posting Place: ';echo $place_name;} ?>\n Date:<?php //echo date('d/m/Y');  ?>\n Scheme Name: <?php// if($level1a==1){echo 'Jai Johar(for ST)';}elseif($level1a==2){echo 'Manabik';}elseif($level1a==3){echo 'Taposili Bandhu(for SC)';}?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5],
                stripHtml: false,
            }
       },
       {
           extend: 'csv',
           //title: 'Line Listing Report of  ',
            // messageTop:'Filter Criteria:\n  Level:<?php //if($level1=='State'){echo ' State';}elseif($level1=='District'){echo ' District';}elseif($level1=='ULB'){echo ' ULB';}elseif($level1=='Block'){echo ' Block';} ?>\n <?php //if($level1=='State'){echo 'State: ';echo $state_name;}elseif($level1=='District'){echo 'District: ';echo $district_name;}elseif($level1=='ULB'){echo 'District: ';echo $district_name;echo'\n'; echo 'ULB: ';echo $urban_body_name;}elseif($level1=='Block'){echo 'District:';echo $district_name;echo'\n';echo 'Block: ';echo $taluka_name;} ?>\n <?php //if($level3!=null){ echo'Posting Level: ';echo $level3;} ?>\n <?php// if($level4!=null){ echo'Posting Place: ';echo $place_name;} ?>\n Date:<?php// echo date('d/m/Y');  ?>\n Scheme Name: <?php //if($level1a==1){echo 'Jai Johar(for ST)';}elseif($level1a==2){echo 'Manabik';}elseif($level1a==3){echo 'Taposili Bandhu(for SC)';}?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5],
                stripHtml: false,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    } );
  

    $('#filter').click(function(){
         dataTable.ajax.reload();
            
    });

      $('#reset').click(function(){
        $('#district_code').val('');
        $('#example').DataTable().destroy();
        fill_datatable();
    });


  } );
</script>

</body>
</html>