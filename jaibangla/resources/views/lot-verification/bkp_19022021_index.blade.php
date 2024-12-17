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

.wrapper1{
text-align: left!important;
margin-left: 30%;
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

@extends('lot-verification.base')
@section('action-content')
<section class="content">
  <div>
  @if ( ($message = Session::get('success')) )
    <div class="alert alert-success alert-block">
      <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{{ $message }}</strong>
    </div>
  @endif
           
  </div>
  <div class="">
    <!-- Content Wrapper. Contains page content -->
   
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Lot To be Pushed To IFMS
         <!--  <small>Preview</small> -->
        </h1>
        
        
        
      </section>

      <!-- Main content -->
      <section class="content">
      
       
       <table id="example" class="display" cellspacing="0" width="100%">

        <thead>

              <tr role="row" style="font-size: 12px;">
                <!-- <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Employee Code</th> -->
                <th  width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Lot Number</th>
                <th  width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Child Lot Number</th>
                <th width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Scheme Name</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Month</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Year</th>

                
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No of Beneficiary</th>
				<th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending" style="text-align: center">Action</th>

                <!-- <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th> -->
                
                
              </tr>
            </thead>
            <tbody>

              <?php
              
              $lot_nos=[];
              $i=0;

               ?>
              

              @foreach($datas as $result)
              <?php
              $lot_nos[$i]=$result->lot_no;
              $i++;
              //$totalSubmittedtotal=$totalSubmittedtotal+1;

               ?>
                <tr role="row" class="odd">
                  <td class="sorting_1" style="text-align: center;">
                    {{$result->lot_no}}
                    </td>
                 <td class="sorting_1" style="text-align: center;">
                    {{$result->repeat_drn_part}}
                    </td>
                  
                   <td>{{$result->Scheme->scheme_name}} </td> 
                  
                  <td class="sorting_1" style="text-align: center;">
                   {{$result->lot_month}}
                   </td>
                    


                  <td class="sorting_1" style="text-align: center;">{{$result->lot_year}}</td>
                 
                  <td class="sorting_1" style="text-align: center;">
                 <form class="row" method="POST" action="{{ route('lot-verification.showlist') }}">
                        <input type="hidden" name="lot_no" value="{{$result->lot_no}}">
                        <input type="hidden" name="scheme_id" value="{{$result->scheme_id}}">

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      
                        <button type="submit" class="" >
                          {{$result->ben_count}}
                        </button>
                   </form>
                  </td>
                  <td style="text-align: center">
				  @if($result->lot_status==1 and $result->ref_no=='' and $result->ack_status=='')
                     <form method="POST" action="{{ route('checkLotDuplicate') }}" class="submit-once"  onSubmit="if(!confirm('Check Lot for Duplicate Records?')){return false;}">   
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                        <input type="hidden" name="lot_no" value="{{$result->lot_no}}">
                        <input type="hidden" name="scheme_id" value="{{$result->scheme_id}}">
						<input type="hidden" name="ben_count" value="{{$result->ben_count}}">
                        <!--@if($result->scheme_id == 3)
                        <input type="hidden" name="party_code" value="026">
                        @elseif($result->scheme_id == 1)
                        <input type="hidden" name="party_code" value="028">
                        @endif -->
                        
                        <button  style="border:1px solid black ;" type="submit" name="bulk_approve" id="bulk_approve" value="approve" class="btn btn-success btn-margin"><!--"btn btn-info col-md-5 btn-margin"-->
                                   Duplicate Check
                        </button>
                    </form>
					@endif
                  </td>

              </tr>

             
             
                
               
               @endforeach
            
            </tbody>
            <tfoot>
             <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Showing 1 to {{count($datas)}} of {{count($datas)}} entries</div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $datas->links() }}
                </div>
              </div>
            </div>
            </tfoot>

            
          
          
    </table>
 <!--    <div class="wrapper1">
       <form class="" method="POST" action="{{ route('push-to-ifms.forward') }}" class="submit-once"
       onSubmit="if(!confirm('Proceed To IFMS?')){return false;}">

               <input type="hidden" name="lot_numbers" value="{{ serialize($lot_nos) }}">
               <?php //echo $lot_nos; ?>
               <input type="hidden" name="_token" value="{{ csrf_token() }}">
             
              <button  style="border:1px solid black ;margin: 0% 0% 2% 0%; position: absolute;" type="submit" name="bulk_approve" id="bulk_approve" value="approve" class="btn btn-info col-md-3 btn-margin" >
                         Push To IFMS
              </button>

    </div>

  </form> -->

  <!-- </div> -->
    

  
  
 
<!-- </div> -->

<!-- /.row -->
</section>
</section>

<!-- /.content -->
<!-- </div> -->
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- <script src="{{ asset("js/select2.full.min.js") }}"></script> -->
<!---data table------->
<!--  <script src="{{ asset("js/jquery-1.12.4.js") }}"></script> -->
<!-- <script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("js/dataTables.buttons.min.js") }}"></script>
<script src="{{ asset("js/buttons.flash.min.js") }}"></script>
<script src="{{ asset("js/jszip.min.js") }}"></script>
<script src="{{ asset("js/pdfmake.min.js") }}"></script>
<script src="{{ asset("js/vfs_fonts.js") }}"></script>
<script src="{{ asset("js/buttons.html5.min.js") }}"></script>
<script src="{{ asset("js/buttons.print.min.js") }}"></script>
 -->
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
      dom: 'Bfrti',
      //"paging": true,
      //"pageLength":3,
      //'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
     // processing: true,
      //serverSide: true,
      // ajax:{
      //       url: "{{ url('push-to-ifms-list') }}",
      //       type: "POST",
      //       data:function(d){
      //           // d.filter_1= filter_1,
      //           // d.filter_2= filter_2,
      //            d._token= "{{csrf_token()}}"
      //       }                
      // },
      // columns: [
                
      //   { "data": "lot_no" },
      //   { "data": "scheme_name" },
      //   { "data": "lot_month"},
      //   { "data": "lot_year" },
      //   { "data": "ben_count" },
               

      // ],          
      
      buttons: [
       {
           extend: 'pdf',
           title: 'Lot To be Pushed To IFMS',
          // messageTop:'Filter Criteria:\n Data Entry Level:<?php //if($level1=='State'){echo ' State';}elseif($level1=='District'){echo ' District';}elseif($level1=='ULB'){echo ' ULB';}elseif($level1=='Block'){echo ' Block';} ?>\n <?php// if($level1=='State'){echo 'State: ';echo $state_name;}elseif($level1=='District'){echo 'District: ';echo $district_name;}elseif($level1=='ULB'){echo 'District: ';echo $district_name;echo'\n'; echo 'ULB: ';echo $urban_body_name;}elseif($level1=='Block'){echo 'District:';echo $district_name;echo'\n';echo 'Block: ';echo $taluka_name;} ?>\n <?php //if($level3!=null){ echo'Posting Level: ';echo $level3;} ?>\n <?php //if($level4!=null){ echo'Posting Place: ';echo $place_name;} ?>\n Date:<?php //echo date('d/m/Y');  ?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4],

            }
       },
       {
           extend: 'print',
           title: 'Lot To be Pushed To IFMS',
          // messageTop:'<strong><u>Filter Criteria:</u></strong><br> <strong>Data Entry Level:</strong><?php// if($level1=='State'){echo ' State';}elseif($level1=='District'){echo ' District';}elseif($level1=='ULB'){echo ' ULB';}elseif($level1=='Block'){echo ' Block';} ?><br> <?php// if($level1=='State'){echo '<strong>State:</strong> ';echo $state_name;}elseif($level1=='District'){echo '<strong>District:</strong> ';echo $district_name;}elseif($level1=='ULB'){echo '<strong>District:</strong> ';echo $district_name;echo'<br>'; echo '<strong>ULB:</strong> ';echo $urban_body_name;}elseif($level1=='Block'){echo '<strong>District:</strong>';echo $district_name;echo'<br>';echo '<strong>Block:</strong> ';echo $taluka_name;} ?><br> <?php// if($level3!=null){ echo'<strong>Posting Level:</strong> ';echo $level3;} ?><br> <?php //if($level4!=null){ echo'<strong>Posting Place:</strong> ';echo $place_name;} ?><br><strong>Date:</strong><?php// echo date('d/m/Y');  ?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                 columns: [0,1,2,3,4],
                stripHtml: false,
            }
       },
       {
           extend: 'excel',
           title: 'Lot To be Pushed To IFMS',
           // messageTop:'Filter Criteria:\n Data Entry Level:<?php //if($level1=='State'){echo ' State';}elseif($level1=='District'){echo ' District';}elseif($level1=='ULB'){echo ' ULB';}elseif($level1=='Block'){echo ' Block';} ?>\n <?php// if($level1=='State'){echo 'State: ';echo $state_name;}elseif($level1=='District'){echo 'District: ';echo $district_name;}elseif($level1=='ULB'){echo 'District: ';echo $district_name;echo'\n'; echo 'ULB: ';echo $urban_body_name;}elseif($level1=='Block'){echo 'District:';echo $district_name;echo'\n';echo 'Block: ';echo $taluka_name;} ?>\n <?php// if($level3!=null){ echo'Posting Level: ';echo $level3;} ?>\n <?php //if($level4!=null){ echo'Posting Place: ';echo $place_name;} ?>\n Date:<?php// echo date('d/m/Y');  ?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                 columns: [0,1,2,3,4],
                stripHtml: false,
            }
       },
        {
           extend: 'copy',
           title: 'Lot To be Pushed To IFMS',
          //  messageTop:'Filter Criteria:\n Data Entry Level:<?php //if($level1=='State'){echo ' State';}elseif($level1=='District'){echo ' District';}elseif($level1=='ULB'){echo ' ULB';}elseif($level1=='Block'){echo ' Block';} ?>\n <?php// if($level1=='State'){echo 'State: ';echo $state_name;}elseif($level1=='District'){echo 'District: ';echo $district_name;}elseif($level1=='ULB'){echo 'District: ';echo $district_name;echo'\n'; echo 'ULB: ';echo $urban_body_name;}elseif($level1=='Block'){echo 'District:';echo $district_name;echo'\n';echo 'Block: ';echo $taluka_name;} ?>\n <?php //if($level3!=null){ echo'Posting Level: ';echo $level3;} ?>\n <?php//if($level4!=null){ echo'Posting Place: ';echo $place_name;} ?>\n Date:<?php// echo date('d/m/Y');  ?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4],
                stripHtml: false,
            }
       },
       {
           extend: 'csv',
           title: 'Lot To be Pushed To IFMS',
           // messageTop:'Filter Criteria:\n Data Entry Level:<?php //if($level1=='State'){echo ' State';}elseif($level1=='District'){echo ' District';}elseif($level1=='ULB'){echo ' ULB';}elseif($level1=='Block'){echo ' Block';} ?>\n <?php //if($level1=='State'){echo 'State: ';echo $state_name;}elseif($level1=='District'){echo 'District: ';echo $district_name;}elseif($level1=='ULB'){echo 'District: ';echo $district_name;echo'\n'; echo 'ULB: ';echo $urban_body_name;}elseif($level1=='Block'){echo 'District:';echo $district_name;echo'\n';echo 'Block: ';echo $taluka_name;} ?>\n <?php //if($level3!=null){ echo'Posting Level: ';echo $level3;} ?>\n <?php //if($level4!=null){ echo'Posting Place: ';echo $place_name;} ?>\n Date:<?php// echo date('d/m/Y');  ?>',
           //message:'hi there\n a new line',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4],
                stripHtml: false,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    } );
  } );
</script>

