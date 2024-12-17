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
       
        <input type="hidden" name="dist_code" value="{{ $dist_code }}" class="js-district">
         <div class="row" style="">
          <div class="form-group col-md-4" >
            <label class=" control-label" >Select Filter Criteria :Urban/Rural</label>
              <select name="filter_1" id="filter_1" class="form-control select2 full-width js-block-subdiv" >
                  <option value="">-----Select----</option>
                 @foreach ($levels as $key=>$value)
                          <option value="{{$key}}" > {{$value}}</option>
                  @endforeach
              </select>
          </div>
          <div class="form-group col-md-4">
            <label class=" control-label" >Select Filter Criteria :Block/Sub Division</label>
              <select name="filter_2" id="filter_2" class="form-control select2 full-width js-localbody" >
                  <option value="">-----Select----</option>
              </select>
          </div>          
          <div class="form-group col-md-4">
            <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
            <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button>
          </div>
        </div>
       

        
       <div >
              
              <button  style="border:1px solid black ;margin: 0% 0% 2% 0%;" name="bulk_blkchange" id="bulk_blkchange" value="Transfer Data" class="btn btn-info col-sm-3 col-xs-5 btn-margin">
                         Transfer Data
              </button>


            </div>
            <!-- Location Change code -->

            
       
       <table id="example" class="display" cellspacing="0" width="100%"> 

        <thead>

                <tr role="row" class="sorting_asc" style="font-size: 12px;">
                <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
                <th  width="7%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Aplication ID</th>
                <th width="12%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Applicant Name</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">DOB</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Gender</th>
                 <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">District Name</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Block/ULB Name</th>               
                <th width="17%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th>
                <th width="17%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Check</th>
                
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

<div class="modal fade" id="ben_change_loc_map_modal" tabindex="-1">
      <div class="modal-dialog ">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Change Applications Location Mapping</h4>
          </div>
          <div class="modal-body">
            <h4><b>Please take utmost care while changing the location mapping of applications..</b></h4><hr/>
              <table class="display">
                <tr><td class="form-group">
                <label class=" control-label">Location Type: </label>
                <!--  <div class=""> -->
                </td><td>
                <select class="form-control"  name="location_type" id='location_type'>
                    <option value="">--Select--</option>
                    <option value="2">Rural</option>
                    <option value="1">Urban</option>
                </select>
                <!--  </div> -->
                </td>
                <td>
                <label class=" control-label">  Block/Municipality: </label>
                <!--  <div class=""> -->
                </td><td>
                <select class="form-control full-width"  name="local_body" id='local_body'>
                    <option value=""> -- Please Select --</option>
                </select>
                <!--  </div> -->
                </td></tr>

              </table>    
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>

            <button type="button" class="btn btn-danger" id="updateLocation_Button" data-dismiss="modal">Update</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Change Location Mapping End -->

<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>
<script src="{{ URL::asset('js/site.js') }}"></script>

<script>
  $('.select2').select2();
</script>

<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
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
   


  fill_datatable();
  function fill_datatable(filter_1 = '', filter_2 = ''){

        var dataTable=$('#example').DataTable( {
      //dom: 'Bfrtip',
      dom: 'Blfrtip',
      paging: true,
      pageLength:100,
      lengthMenu: [[20, 50,100,500,1000, -1], [20, 50,100,500,1000, 'All']],
      processing: true,
      serverSide: true,
      ajax:{
            url: "{{ url('exchangeflow') }}",
            type: "POST",
            data:function(d){
                 d.filter_1= filter_1,
                 d.filter_2= filter_2,
                 d._token= "{{csrf_token()}}"
            }                
      },
      columns: [
                
        { "data": "id" },
        { "data": "name" },
        { "data": "dist_name"},
        { "data": "name" },
        { "data": "dist_name" },
        { "data": "block_ulb_name" },
        { "data": "view" },
        { "data": "check" },
               

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
        var filter_2 = $('#filter_2').val();

        if(filter_1 != '' && filter_2 != '')
        {
            $('#example').DataTable().destroy();
            fill_datatable(filter_1, filter_2);
        }
        else{
          alert('Please select two Filter Criterias');
        }
    });

      $('#reset').click(function(){
        $('#filter_1').val('');
        $('#filter_2').val('');
        $('#example').DataTable().destroy();
        fill_datatable();
    });


  } );
</script>

<script type="text/javascript">
  $('#bulk_blkchange').click(function(){
    $("#location_type option:selected").prop("selected",false);
    $('#local_body').empty();
    $('#local_body').append('<option value="">--Please Select--</option>');
    $("#select_all").prop("checked",false);
    var selected = new Array();
    $("input[type=checkbox]").each(function() {
      if($(this).is(":checked"))
      selected.push($(this).val());
    });
    var selected_json = JSON.stringify(selected);
    $('#ben_change_loc_map_modal').modal('show');
  });

  $('#location_type').change(function(){
    var location_type = $("#location_type").val();
    var district_code = "{{$dist_code}}";
    $('#local_body').empty();
    $('#local_body').append('<option value="">--Please Select--</option>');

    if(location_type !=""){
      $.ajax({
        type: 'POST',
        url: '{{ url('singleStepBenLocationDetails') }}',
        dataType: 'json',
        data: {
          _token: '{{ csrf_token() }}',
          urban_rural: location_type,
          district_code: district_code,
        },
        beforeSend: function(){
          $('#loaderdiv').show();
        },
        success: function (datas) {
          $.each(datas, function (index, value) {
              $('#local_body').append('<option value="' + value.id + '">' + value.name + '</option>');
          });
        },
        complete: function(){
          $('#loaderdiv').hide();
        },
        error: function (ex) {
          $('#loaderdiv').hide();
        }
      });
    }
  });

  $('#updateLocation_Button').click(function(){
   
    $("#select_all").prop("checked",false);
    var selected = new Array();
    $("input[type=checkbox]").each(function() {
    if($(this).is(":checked"))
    selected.push($(this).val());
    });
    var selected_json = JSON.stringify(selected);

    var location_type = $("#location_type").val();
    var location_code = $("#local_body").val();
    var location_name = $("#local_body option:selected").text();

    if(location_type!="" && location_code!=""){
      $.ajax({
        type: 'POST',
        url: '{{ url('singleStepBulkLocationChange') }}',
        dataType: 'json',
        data: {
          _token: '{{ csrf_token() }}',
          scheme: "{{ $scheme }}",
          location_type: location_type,
          location_code: location_code,
          location_name: location_name, 
          selectedApplication: selected_json,
        },
        beforeSend: function(){
          $('#loaderdiv').show();
        },
        complete: function(){
          $('#example').DataTable().ajax.reload();
          document.getElementById('bulk_approve').disabled = true;
          document.getElementById('bulk_blkchange').disabled = true;
          $('#loaderdiv').hide();
          alert("Location change of beneficiaries successful.");

        },
        error: function (ex) {
        }
      });
    }
  });
</script>

</body>
</html>