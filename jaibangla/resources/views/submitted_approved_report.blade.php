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
        <h1>
          Download
          <small>Preview</small>
        </h1>
        <h2>Status Of Data Entry / Verification / Approved On the date of <?php echo date('d/m/Y'); ?></h2>
        
      </section>

      <!-- Main content -->
      <section class="content">

       <table id="example" class="display nowrap" cellspacing="0" width="100%">
        <thead>
              <tr role="row" style="font-size: 12px;">
                <th width="26%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">District</th>
                <th  width="12%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Application Submitted</th>
                <th width="12%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Verification Pending</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Verified</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Verification Rejected</th>

                
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Approved</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Approval Pending</th>
                <th width="12%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Approval Rejected</th>
                
              </tr>
            </thead>
            <tbody>
              <?php
              $totalSubmitted=$VerificationPending=$Verified=$Verification_Rejected=$Approved=$ApprovalPending=$ApprovalRejected=0;
              $totalSubmittedtotal=$totalVerificationPending=$totalVerified=$totalVerification_Rejected =$totalApproved=$totalApprovalPending=$totalApprovalRejected= 0;

               ?>
            	

            	@foreach($statues_result as $status)
           
                <tr role="row" class="odd">
                  <td>{{$status->district_name}} </td>
                  <td class="sorting_1" style="text-align: center;">
                    {{$status->district_level_not_verified + $status->block_not_verified + $status->ulb_level_not_verified+$status->district_level_verified + $status->block_level_verified + $status->ulb_level_verified+
                      $status->district_level_not_rejected + $status->block_level_rejected + $status->ulb_level_level_rejected

                    }} 
                  </td>
                  <td class="sorting_1" style="text-align: center;">{{$status->district_level_not_verified + $status->block_not_verified + $status->ulb_level_not_verified}}
                    </td>
                    
                  
                  <td class="sorting_1" style="text-align: center;">
                    {{$status->district_level_verified + $status->block_level_verified + $status->ulb_level_verified}}
                   </td>
                    


                  <td class="sorting_1" style="text-align: center;">{{$status->district_level_not_rejected + $status->block_level_rejected + $status->ulb_level_level_rejected}}</td>
                  <td class="sorting_1" style="text-align: center;">{{$status->district_level_approved + $status->block_level_approved + $status->ulb_level_approved}}</td>
                  <td class="sorting_1" style="text-align: center;">{{$status->district_level_approval_pending + $status->block_approval_pending + $status->ulb_level_approval_pending}}</td>
                  <td class="sorting_1" style="text-align: center;">{{$status->district_level_disapproved + $status->block_level_disapproved + $status->ulb_level_disapproved}}</td>

              </tr>

             
             
                <?php  $totalSubmittedtotal += $status->district_level_not_verified + $status->block_not_verified + $status->ulb_level_not_verified+$status->district_level_verified + $status->block_level_verified + $status->ulb_level_verified+
                    $status->district_level_not_rejected + $status->block_level_rejected + $status->ulb_level_level_rejected ;

                     $totalVerificationPending +=$status->district_level_not_verified + $status->block_not_verified + $status->ulb_level_not_verified;

                     $totalVerified += $status->district_level_verified + $status->block_level_verified + $status->ulb_level_verified;

                     $totalVerification_Rejected+= $status->district_level_not_rejected + $status->block_level_rejected + $status->ulb_level_level_rejected;

                     $totalApproved +=$status->district_level_approved + $status->block_level_approved + $status->ulb_level_approved;
                     $totalApprovalPending+=$status->district_level_approval_pending + $status->block_approval_pending + $status->ulb_level_approval_pending;
                     $totalApprovalRejected+=$status->district_level_disapproved + $status->block_level_disapproved + $status->ulb_level_disapproved;
                    ?>
              
               
               @endforeach
            
            </tbody>
            <tfoot>
              <tr>
                <td align="right"><strong>Total</strong></td>
                <td  align="center"><strong>{{$totalSubmittedtotal}}</strong></td>
                <td align="center"><strong>{{$totalVerificationPending}}</strong></td>
                <td align="center"><strong>{{$totalVerified}}</strong></td>
                <td align="center"><strong>{{$totalVerification_Rejected}}</strong></td>
                <td align="center"><strong>{{$totalApproved}}</strong></td>
                <td align="center"><strong>{{$totalApprovalPending}}</strong></td>
                <td align="center"><strong>{{$totalApprovalRejected}}</strong></td>
              </tr>
            </tfoot>

            
          
          
    </table>

  </div>
  
</div>
<!-- /.row -->

</section>
<!-- /.content -->
</div>


<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
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
    $('#example').DataTable( {
      dom: 'Bfrtip',
      "paging": false,
      buttons: [
      'pdf','excel','csv','print','copy'
      ]
    } );
  } );
</script>

</body>
</html>