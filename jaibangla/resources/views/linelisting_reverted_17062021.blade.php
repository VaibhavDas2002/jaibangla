<?php 

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>
  </title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

  <!-- Font Awesome -->
  <link href="{{ asset("/css/font-awesome.min.css")}}" rel="stylesheet" type="text/css" />
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"> -->
  
  <!-- Ionicons -->
  <link href="{{ asset('css/ionicons.min.css') }}" rel="stylesheet">
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"> -->
  <!-- Theme style -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
      <link href="{{ asset("css/select2.min.css") }}" rel="stylesheet">
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet" type="text/css" />  
  
   
  <!--  
   <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
   <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css"> -->

<!--data table--->
<link rel="stylesheet" href="{{ asset("/css/jquery.dataTables.min.css")}}">
<link rel="stylesheet" href="{{ asset("/css/buttons.dataTables.min.css")}}">
<link href="{{ asset("css/select2.min.css") }}" rel="stylesheet">

<!---data table end---->

   

   
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
<!-- <link rel="stylesheet"
href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic"> -->

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
             @if ( ($message = Session::get('success')))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
               
              </div>
              @endif
            
            </div>
       
      </div>
        
        
      </section>

      <!-- Main content -->
      <section class="content">
        @if($duty_level=='BlockVerifier')
        <input type="hidden" name="dist_code" value="{{ $dist_code }}" class="js-district_1">
         <div class="row" style="">

          <div class="form-group col-md-4">
            <label class=" control-label" >Select Filter Criteria :Gram Panchayat</label>
              <select name="filter_1" id="filter_1" class="form-control select2 full-width" >
                  <option value="">-----Select----</option>
                   @foreach ($gps as $gp)
                          <option value="{{$gp->gram_panchyat_code}}" > {{$gp->gram_panchyat_name}}</option>
                  @endforeach

              </select>
          </div>          
          <div class="form-group col-md-4">
            <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
            <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button>
          </div>
        </div>
        @endif
       
       <table id="example" class="display" cellspacing="0" width="100%"> 

        <thead>

                <tr role="row" class="sorting_asc" style="font-size: 12px;">
                <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
                <th  width="7%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Aplication ID</th>
                <th width="12%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Applicant Name</th>
                <!--//sayantika-21-04-2020 start-->
                <th width="12%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Gram Panchayat Name</th>
                <!--//sayantika-21-04-2020 end-->
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank A/C No</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank Name</th>
                 <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank Branch</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">IFSC Code</th>               
                <th width="17%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th>
                <!-- <th width="17%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Check</th> -->
                
              </tr>
            </thead>
            <tbody>

            
              

             

               
            
            </tbody>
            <!-- <tfoot> -->
           
          
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
<!-- <script src="{{ URL::asset('js/site.js') }}"></script> -->

<script>
  $('.select2').select2();
</script>
<!---data table------->
   <!--  <script src="{{ asset("js/jquery-1.12.4.js") }}"></script> -->
    <script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("js/dataTables.buttons.min.js") }}"></script>
    <script src="{{ asset("js/buttons.flash.min.js") }}"></script>
    <script src="{{ asset("js/jszip.min.js") }}"></script>
    <script src="{{ asset("js/pdfmake.min.js") }}"></script>
    <script src="{{ asset("js/vfs_fonts.js") }}"></script>
    <script src="{{ asset("js/buttons.html5.min.js") }}"></script>
    <script src="{{ asset("js/buttons.print.min.js") }}"></script>

<!---data table end--->

<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script> -->


<script>

  $(document).ready(function() {
   


  fill_datatable();
  function fill_datatable(filter_1 = ''){

        var dataTable=$('#example').DataTable( {
      //dom: 'Bfrtip',
      dom: 'Blfrtip',
      paging: true,
      pageLength:100,
      lengthMenu: [[20, 50,100,500,1000, -1], [20, 50,100,500,1000, 'All']],
      processing: true,
      serverSide: true,
      ajax:{
            url: "{{ url('revert') }}",
            type: "POST",
            data:function(d){
                 d.filter_1= filter_1,
                 d._token= "{{csrf_token()}}"
            }                
      },
      columns: [
                
        { "data": "id" },
        { "data": "name" },
        //sayantika-21-04-2020 start
        { "data": "gp_ward_name"},
        //sayantika-21-04-2020 end
        { "data": "bank_code"},
        { "data": "bank_name" },
        { "data": "branch_name" },
        { "data": "bank_ifsc" },
        { "data": "view" },
       // { "data": "check" },
               

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


   }

    $('#filter').click(function(){
        var filter_1 = $('#filter_1').val();
        if(filter_1 != '')
        {
            $('#example').DataTable().destroy();
            fill_datatable(filter_1);
        }
        else{
          alert('Please select two Filter Criterias');
        }
    });

      $('#reset').click(function(){
        $('#filter_1').val('');
        
        //sayantika-21-04-2020 start
        $('#select2-filter_1-container').text('---Select---');
        //sayantika-21-04-2020 end
        
        //$('#filter_2').val('');
        $('#example').DataTable().destroy();
        fill_datatable();
    });


  } );
</script>

</body>
</html>