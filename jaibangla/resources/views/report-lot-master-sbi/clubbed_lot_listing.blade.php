<?php 

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>JB | Jai Bangla
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
  
   <script  src="{{ asset ("/bower_components/AdminLTE/moment/moment.js") }}" type="text/javascript" ></script>
   
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
        <h1>
          Clubbed Lot Transaction SBI
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Lot Transaction SBI</a></li>
          <!-- <li class="active">Duplicate Approve</li> -->
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
	         <div>
            
             @if ($message = Session::get('success'))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
              </div>
			 @elseif ($message = Session::get('danger'))
              <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
              </div>
              @endif
			</div>
       <!-- <h3>Clubbed Lot Report</h3> -->
        <div class="box box-default">
          <div class="box-header with-border">
            <div class="row">
                <div class="col-sm-6">
                  <h3 class="box-title">Clubbed Lot Report SBI</h3>
                </div>
                <div class="col-md-6">
                  <span class="text-danger" style="font-size: 14px; float: right; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
                </div>
                
            </div>
          </div>
          <div class="box-body">
       <div class="table-responsive">
       <table id="example" class="display table-bordered" cellspacing="0" width="100%"> 

        <thead>
          <tr role="row" class="sorting_asc" style="font-size: 12px;">
            <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Serial No</th>
            <th  width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Lot No</th>
            <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Clubbed Lot Year Month</th>
	    <th  width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Child Lot Details [Lot No,Month,Is Repeated]</th>
            <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Status</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Total Beneficiary in the lot-List</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of Beneficiary in the lot</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Failed Beneficiary List</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Failed</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Success Beneficiary List</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Success</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Billed Amount</th>      
            <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending" style="text-align: center">Action</th>      
          </tr>
        </thead>
        <tbody>
          @php $i=1; @endphp
            @foreach($reports as $report)
            <tr>
              <td>@php print $i++; @endphp</td>
              <td>{{ $report->lot_no }}</td>
              <td>{{$report->scheme_name}}</br>{{ $report->lot_year }} {{ $report->lot_month }}</td>
	      <td>
                <table>
                <?php  
                  $a = explode('[',$report->child_lot);
                  array_shift($a);
                  foreach ($a as $key) {
                      $b = explode(']', $key);
                      print '<tr><td>'.$b[0].'</td><tr>';
                  }
                ?>
                </table>  
              </td>
              <td>
              @php
                if($report->lot_status==0)
                  {print 'Ready for push to Bank.';}
                elseif($report->lot_status==1 )
                  {print 'Lot signed.';}  
                elseif($report->lot_status==2 )
                  {print 'File in server. will be pushed in next cycle';}
                elseif($report->lot_status==3 )
                  {print 'Pushed to SBI<br/>Acknowledgement Received from SBI';}
                elseif($report->lot_status==4 )
                  {print 'Pushed to SBI<br/>Acknowledgement Received from SBI <br/> Payment Response received form SBI.';}
                elseif($report->lot_status==5 )
                  {print 'All actions completed';}
                elseif($report->lot_status==10 )
                  {print 'Lot Signing Failed. Please Re-sign the LOT';}
                elseif($report->lot_status==20 )
                  {print 'Pushed to SBI Failed. Please Re-Push the LOT';}
                elseif($report->lot_status==30)
                  {print 'Pushed to SBI</br> Acknowledgement receive failed.';} 
                elseif($report->lot_status== 40 )
                  {print 'Pushed to SBI<br/>Acknowledgement Received from SBI<br/> Payment response receive failed.';} 
                elseif($report->lot_status==50)
                  {print 'Pushed to SBI<br/>Acknowledgement Received from SBI <br/> Payment Response received form SBI.<br/> Payment data not compiled. Please re-compile payment data.';} 
                else 
                  {print 'Lot has been stopped.';}               
              @endphp
              
              </td>

               <form method="POST" action="{{ route('lot_payment_xls_generate') }}">
                <td style="text-align: center">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
                  <input type="hidden" name="error_type" value="COUNT">
                  <button class="btn btn-xs btn-margin" onmouseover="$(this).toggleClass('btn-primary');" onmouseout="$(this).toggleClass('btn-primary');" style="font-size: 16px;" title="Total Beneficiary - {{ $report->credit_count }}">
                    Get Total list                  </button>
                </td>
              </form>
              <td style="text-align: center">{{ $report->credit_count }}</td>

               @if($report->failed_count != '')
              <form method="POST" action="{{ route('lot_payment_xls_generate') }}">
                <td style="text-align: center">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
                  <input type="hidden" name="error_type" value="E2">
                  <button class="btn btn-xs btn-margin" onmouseover="$(this).toggleClass('btn-danger');" onmouseout="$(this).toggleClass('btn-danger');" style="font-size: 16px;" title="SBI Failed - @php if($report->failed_count == '') {print '0';} else {print $report->failed_count;} @endphp">
                    @php if($report->failed_count == '') {print '0';} else {print "Get Failed List";} @endphp
                  </button>
                </td>
              </form>
              @else
              <td style="text-align: center">@php if($report->failed_count == '') {print '0';} else {print "Get Failed List";} @endphp</td>
              @endif
              <td style="text-align: center">@php if($report->failed_count == '') {print '0';} else {print $report->failed_count;} @endphp</td>

               @if($report->success_count != '')
              <form method="POST" action="{{ route('lot_payment_xls_generate') }}">
                <td style="text-align: center">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
                  <input type="hidden" name="error_type" value="S0">
                  <button class="btn btn-xs btn-margin" onmouseover="$(this).toggleClass('btn-success');" onmouseout="$(this).toggleClass('btn-success');" style="font-size: 16px; " title="SBI Success - @php if($report->success_count == '') {print '0';} else {print $report->success_count;} @endphp">
                    @php if($report->success_count == '') {print '0';} else {print "Get Success List";} @endphp
                  </button>
                </td>
              </form>
              @else
              <td style="text-align: center">@php if($report->success_count == '') {print '0';} else {print "Get Success List";} @endphp</td>
              @endif
              <td style="text-align: center">@php if($report->success_count == '') {print '0';} else {print $report->success_count;} @endphp</td>
              <td style="text-align: center">@php if($report->amount_debit == '') {print '0';} else {print $report->amount_debit;} @endphp</td>


              @if($report->lot_status==0 or $report->lot_status==10)
                  <form  method="POST" action="{{ route('clubbed-push-to-sbi-single-lot') }}" class="submit-once" onSubmit="if(!confirm('Please click on OK if you are sure to export the Lot to SBI')){return false;}">
                      <td>  
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
	                <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
                        <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
                        <input type="hidden" name="debit_ref" value="{{$report->debit_reference}}">
                        <button type="submit" class="btn btn-info btn-margin" >
                          Sign the Lot and Push to SBI
                        </button>
                      </td>  
                    </form>
                  @elseif($report->lot_status==1 or $report->lot_status==20)
                    <td>
                       Waiting for to be pushed to SBI server in next cycle.
                    </td>
                  </form>     
                  @elseif($report->lot_status==2 or $report->lot_status==30)
                    <td>
                       Waiting for reciveing of Lot Acknowledgement from SBI.
                    </td>
                  </form>   
                  @elseif($report->lot_status==3 or $report->lot_status==40)
                    <td>
                       Waiting for reciveing of Payment response from SBI.
                    </td>
                  </form> 
                  @elseif($report->lot_status==4 or $report->lot_status==50)
                    <form method="GET" action="{{ route('clubbed_sbi_payment_status') }}" class="submit-once" onSubmit="if(!confirm('Please click on OK if you are sure to import the SBI Payment Response')){return false;}">
				    <td>
                      <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
                      <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
                      <input type="hidden" name="debit_ref" value="{{$report->debit_reference}}">
                      <input type="hidden" name="lot_month" value="{{$report->lot_month}}">
                      <input type="hidden" name="lot_year" value="{{$report->lot_year}}">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <button type="submit" class="btn btn-warning btn-margin">
                        Import SBI Payment status
                      </button>
                    </td>
                  </form>
		@elseif($report->lot_status ==5)
				  <td style="text-align: center">
				  <i class="glyphicon glyphicon-ok"></i>
				  </td>
                @elseif($report->lot_status <0)
				  <td style="text-align: center">
				  <i class="glyphicon glyphicon-remove"></i>
				  </td>

                @endif
            </tr>   
            @endforeach   
        </tbody>
        <!-- <tfoot> -->
       
        <!-- </tfoot> -->       
    </table>
  </div>
 </div>
</div>
  </div>
 
</div>

<!-- /.row -->
</section>

</div>

<!-- /.content -->
</div>


<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>
<script src="{{ URL::asset('js/site.js') }}"></script>

<script>
  $('.select2').select2();
</script>

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
      // dom: 'Bfrtip',
      dom: 'Blfrtip',
      "paging": true,
      "pageLength":20,
      'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      
      buttons: [
       {
           extend: 'pdf',
           title: 'Clubbed Lot Report <?php echo date('d-m-Y'); ?>',
           text: '<b><i class="fa fa-file-pdf-o" style="color: #3e943d;"></i>PDF</b>',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,5,7,9,10],

            }
       },
       {
           extend: 'print',
           title: 'Clubbed Lot Report <?php echo date('d-m-Y'); ?>',
           text: '<b><i class="fa fa-print" style="color: #d44317;"></i>Print</b>',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                 columns: [0,1,2,3,5,7,9,10],
                stripHtml: false,
            }
       },
       {
           extend: 'excel',
           title: 'Clubbed Lot Report <?php echo date('d-m-Y'); ?>',
           text: '<b><i class="fa fa-file-excel-o" style="color: #161c9c;"></i>Excel</b>',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                 columns: [0,1,2,3,5,7,9,10],
                stripHtml: false,
            }
       },
       //  {
       //     extend: 'copy',
       //     title: 'Lot To be Pushed To IFMS',
       //     footer: true,
       //     pageSize:'A4',
       //     //orientation: 'landscape',
       //     pageMargins: [ 40, 60, 40, 60 ],
       //     exportOptions: {
       //          columns: [0,1,2,3,4,5],
       //          stripHtml: false,
       //      }
       // },
       {
           extend: 'csv',
           title: 'Clubbed Lot Report <?php echo date('d-m-Y'); ?>',
           text: '<b><i class="fa fa-file-text" style="color: #d1ab00;"></i>CSV</b>',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,5,7,9,10],
                stripHtml: false,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    } );
  });
</script>
<script>
  var interval = setInterval(function () {
  var momentNow = moment();
    $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
    $('.time-part').html(momentNow.format('hh:mm:ss A'));
  }, 100);
</script>
<!-- REQUIRED JS SCRIPTS -->

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>

</body>
</html>