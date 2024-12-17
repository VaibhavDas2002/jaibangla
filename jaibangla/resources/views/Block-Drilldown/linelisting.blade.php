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

@extends('Block-Drilldown.base')
@section('action-content')

  <section class="content">


    <!-- Content Wrapper. Contains page content -->
    <div class="">
      <!-- Content Header (Page header) -->
      <section class="content-header">

        <h1>
          Report
         <!--  <small>Preview</small> -->
        </h1>
        <!-- <h2>Line Listing of {{$message}}</h2> -->
        <div class='row'><div class='col-md-6'><h3><u>Filter Criterias:</u></h3></div>
        <div class='col-md-6'><h4 style='text-align:end;text-transform: uppercase;'><strong>Date:</strong><?php echo date('d/m/Y'); ?></h4></div></div>
       
        <div class='row'>
         
         <div class='col-md-6'><h5 ><span class='criteria1'>District:</span> {{$district_name }}</h5></div>
        
        <div class='col-md-6'><h5><span class='criteria1'>Block:</span> {{$block_name}}</h5></div>
       
         @if($level1a==1)
        <div class='col-md-6'><h5 ><span class='criteria1'>Scheme Name:</span>Jai Johar(for ST)</h5></div> 
        @elseif($level1a==2)
        <div class='col-md-6'><h5 ><span class='criteria1'>Scheme Name:</span>Manabik</h5></div> 
        @elseif($level1a==3)
        <div class='col-md-6'><h5 ><span class='criteria1'>Scheme Name:</span>Taposili Bandhu(for SC)</h5></div> 
        @endif
      </div>
        
        
      </section>

      <!-- Main content -->
      <section class="content">
        
       <table id="example" class="display" cellspacing="0" width="100%">

        <thead>

              <tr role="row" style="font-size: 12px;">
                <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
                <th  width="7%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Aplication ID</th>
                <th width="22%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Name</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">DOB</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Gender</th>
                 <th width="19%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">GP Name</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Assembly Name</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Ration Card No</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank Details</th> 
               
                <th width="17%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th>

               
                
              </tr>
            </thead>
            <tbody>

              <?php
              
              $totalSubmittedtotal= 0;

               ?>

              

              @foreach($results as $result)
              <?php
              
              //$totalSubmittedtotal=$totalSubmittedtotal+1;

               ?>
                <tr role="row" class="odd"> 
                <td>{{$result->getBenidAttribute()}} </td>
                <td class="sorting_1" style="text-align: center;">{{$result->getName()}}</td>
                <td class="sorting_1" style="text-align: center;">{{$result->dob}}</td>
                <td class="sorting_1" style="text-align: center;">{{$result->gender}}</td>
                <td class="sorting_1" style="text-align: center;">{{$result->gp_ward_name}}</td>
                <td class="sorting_1" style="text-align: center;">{{$result->assembly_name}}</td>
                <td class="sorting_1" style="text-align: center;">{{$result->ration_card_cat}}-{{$result->ration_card_no}}</td>
                <td class="sorting_1" style="text-align: center; white-space:pre;" ><h6>IFSC: {{$result->bank_ifsc}}<pre>AC No:{{$result->bank_code}}  </h6></td>
                  <td class="sorting_1" style="text-align: center;">
                  <form class="row" method="POST" action="{{ route('nhmemployee.showSingleEmployeeReport', ['id' => $result->id]) }}">
                        <!-- <input type="hidden" name="_method" value="DELETE"> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="pr1" value="{{ $pr1}}">
                        <button type="submit" class="btn btn-info btn-margin" >
                          View
                        </button>
                   </form>
                  </td>
                  

                  <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <td> <input type="checkbox" name="approvalcheck[]" onchange="document.getElementById('bulk_approve').disabled = !this.checked;" value="{{ $result->id }}"></td> -->

              </tr>
               @endforeach
            
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
 
</section>

<!-- /.row -->

<!-- </section> -->
<!-- /.content -->
<!-- </div> -->
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- <script src="{{ asset("js/select2.full.min.js") }}"></script> -->
<!---data table------->
<!-- <script src="{{ asset("js/jquery-1.12.4.js") }}"></script>
<script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("js/dataTables.buttons.min.js") }}"></script>
<script src="{{ asset("js/buttons.flash.min.js") }}"></script>
<script src="{{ asset("js/jszip.min.js") }}"></script>
<script src="{{ asset("js/pdfmake.min.js") }}"></script>
<script src="{{ asset("js/vfs_fonts.js") }}"></script>
<script src="{{ asset("js/buttons.html5.min.js") }}"></script>
<script src="{{ asset("js/buttons.print.min.js") }}"></script> -->

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
   
    $('#example').DataTable( {
      //dom: 'Bfrtip',
      dom: 'Blfrtip',
      "paging": true,
      "pageLength":20,
      'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      "columnDefs": [ { targets: [6], "visible":false}],            
     
      buttons: [
       {
           extend: 'pdf',
           //title: 'Line Listing Report of  {{$message}}',
           title: 'Report',
           messageTop:'Filter Criteria:\n District:{{$district_name}}\n Block:{{$block_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: <?php if($level1a==1){echo 'Jai Johar(for ST)';}elseif($level1a==2){echo 'Manabik';}elseif($level1a==3){echo 'Taposili Bandhu(for SC)';}?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7],

            }
       },
       {
           extend: 'print',
           //title: 'Line Listing Report of  {{$message}}',
           title: 'Report',
           messageTop:'Filter Criteria:\n District:{{$district_name}}\n Block:{{$block_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: <?php if($level1a==1){echo 'Jai Johar(for ST)';}elseif($level1a==2){echo 'Manabik';}elseif($level1a==3){echo 'Taposili Bandhu(for SC)';}?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7],
                stripHtml: false,
            }
       },
       {
           extend: 'excel',
           title: 'Report',
           //title: 'Line Listing Report of  {{$message}}',
           messageTop:'Filter Criteria:\n District:{{$district_name}}\n Block:{{$block_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: <?php if($level1a==1){echo 'Jai Johar(for ST)';}elseif($level1a==2){echo 'Manabik';}elseif($level1a==3){echo 'Taposili Bandhu(for SC)';}?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7],
                stripHtml: true,
            }
       },
        {
           extend: 'copy',
           title: 'Report',
           //title: 'Line Listing Report of  {{$message}}',
           messageTop:'Filter Criteria:\n District:{{$district_name}}\n Block:{{$block_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: <?php if($level1a==1){echo 'Jai Johar(for ST)';}elseif($level1a==2){echo 'Manabik';}elseif($level1a==3){echo 'Taposili Bandhu(for SC)';}?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7],
                stripHtml: false,
            }
       },
       {
           extend: 'csv',
           title: 'Report',
          // title: 'Line Listing Report of  {{$message}}',
           messageTop:'Filter Criteria:\n District:{{$district_name}}\n Block:{{$block_name}}\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: <?php if($level1a==1){echo 'Jai Johar(for ST)';}elseif($level1a==2){echo 'Manabik';}elseif($level1a==3){echo 'Taposili Bandhu(for SC)';}?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7],
                stripHtml: true,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    } );
  } );
</script>
