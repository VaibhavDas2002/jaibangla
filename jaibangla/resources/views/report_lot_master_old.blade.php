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
      <!-- <section class="content-header">

        
      </section> -->

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
       <h3>Report Lot Master</h3>
        
       
       <table id="example" class="display" cellspacing="0" width="100%"> 

        <thead>
          <tr role="row" class="sorting_asc" style="font-size: 12px;">
            <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Serial No</th>
            <th  width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Lot No</th>
            <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Year Month</th>
            <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Status</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of Beneficiary</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of IFMS Wrong Data</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Pmt Mandate</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of RBI Failed</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of RBI Success</th>      
            <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending" style="text-align: center">Action</th>      
          </tr>
        </thead>
        <tbody>
          @php $i=1; @endphp
            @foreach($reports as $report)
            <tr>
              <td>@php print $i++; @endphp</td>
              <td>{{ $report->lot_no }}</td>
              <td>{{ $report->lot_year }} {{ $report->lot_month }}</td>
              <td>
              @php
                if($report->push_to_ifms_status==1 and $report->dotdone_status=='' and $report->ack_status=='' and $report->wrongdata_status=='' and 
                 $report->voucher_no == '')
                  {print 'Pushed to IFMS';}
                elseif($report->push_to_ifms_status==1 and $report->dotdone_status==1 and $report->ack_status=='' and $report->wrongdata_status=='' and 
                 $report->voucher_no == '')
                  {print 'Pushed to IFMS<br/>Received by IFMS';}
                elseif($report->push_to_ifms_status==1 and $report->dotdone_status==1 and $report->ack_status==1 and $report->wrongdata_status=='' and 
                 $report->voucher_no == '')
                  {print 'Pushed to IFMS<br/>Received by IFMS<br/>Reference generated<br/>Ref# '.$report->ref_no.'<br/>No Wrong Data';}
                elseif($report->push_to_ifms_status==1 and $report->dotdone_status==1 and $report->ack_status==1 and $report->wrongdata_status==1 and 
                 $report->voucher_no == '')
                  {print 'Pushed to IFMS<br/>Received by IFMS<br/>Reference generated<br/>Ref# '.$report->ref_no.'<br/>Wrong Data received';}
                elseif($report->push_to_ifms_status==1 and $report->dotdone_status==1 and $report->ack_status==1 and $report->wrongdata_status=='' and 
                 $report->voucher_no > 0)
                  {print 'Pushed to IFMS<br/>Received by IFMS<br/>Reference generated<br/>Ref# '.$report->ref_no.'<br/>No Wrong Data<br/>RBI Report received';}
                elseif($report->push_to_ifms_status==1 and $report->dotdone_status==1 and $report->ack_status==1 and $report->wrongdata_status==1 and 
                 $report->voucher_no > 0)
                  {print 'Pushed to IFMS<br/>Received by IFMS<br/>Reference generated<br/>Ref# '.$report->ref_no.'<br/>Wrong Data received<br/>RBI Report received';}
                elseif($report->ref_no==-1 and $report->ack_status==-1)
                  {print 'Defunct Lot';} 
                elseif($report->lot_status=1 and $report->push_to_ifms_status=='' and $report->dotdone_status=='' and $report->ack_status=='' and 
                $report->wrongdata_status=='' and $report->voucher_no =='')
                  {print 'Lot ready to Push to IFMS';} 
                elseif($report->push_to_ifms_status==1 and $report->dotdone_status==1 and $report->ack_status=='' and $report->wrongdata_status==1 and 
                 $report->voucher_no == '')
                  {print 'Alert: IFMS Ack not received--Wrongdata received';} 
                elseif($report->lot_status=0 and $report->push_to_ifms_status=='' and $report->dotdone_status=='' and $report->ack_status=='' and 
                $report->wrongdata_status=='' and $report->voucher_no =='')
                  {print 'Alert: Lot Pushed--No confirmation from IFMS';} 
                elseif($report->lot_status=0 and $report->push_to_ifms_status=='' and $report->dotdone_status=='' and $report->ack_status=='' and 
                $report->wrongdata_status==1 and $report->voucher_no =='')
                  {print 'Alert: ONLY Wrongdata file received';} 
                /*elseif($report->repeat_lot==1)
                  {print 'Repeat lot generated vide lot no- '.$report->repeat_drn_part;}        */

              @endphp
              
              </td>
              <td style="text-align: center">{{ $report->ben_count }}</td>
              <td style="text-align: center">@php if($report->ifms_wrongdata_count == '') {print '0';} else {print $report->ifms_wrongdata_count;} @endphp</td>
              <td style="text-align: center">@php if($report->pmt_mandate == '' or $report->ack_status=='') {print '0';} else {print $report->pmt_mandate;} @endphp</td>
              <td style="text-align: center">@php if($report->rbi_failed_count == '') {print '0';} else {print $report->rbi_failed_count;} @endphp</td>
              <td style="text-align: center">@php if($report->rbi_success_count == '') {print '0';} else {print $report->rbi_success_count;} @endphp</td>
              @if($report->lot_status==1 and $report->ref_no!=-1 and $report->ack_status!=-1)
                  <form  method="POST" action="{{ route('push-to-ifms.export') }}" class="submit-once" onSubmit="if(!confirm('Please click on OK if you are sure to export the Lot to IFMS')){return false;}">
                      <td>  
                        <!-- <input type="hidden" name="_method" value="DELETE"> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="lot_no" value="{{$report->lot_no}}">
                         <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
                      
                        <button type="submit" class="btn btn-info btn-margin" >
                          Push to IFMS
                        </button>
                      </td>  
                    </form>
                @elseif($report->lot_status==0 and $report->push_to_ifms_status ==1 and $report->dotdone_status =='' and $report->ref_no!=-1 and $report->ack_status!=-1)
                    <form class="row" method="GET" action="{{ route('receive_status') }}">
                    <td>
                        <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
                        <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
                        <!-- <input type="hidden" name="_method" value="DELETE"> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      
                        <button type="submit" class="btn btn-success btn-margin">
                          IFMS received?
                        </button>
                      </td>
                    </form>
                  @elseif($report->lot_status==0 and $report->push_to_ifms_status ==1 and $report->dotdone_status==1 and $report->ack_status=='' and $report->ref_no!=-1 and $report->ack_status!=-1)
                  <form  method="GET" action="{{ route('ack_status') }}" class="submit-once" onSubmit="if(!confirm('Please click on OK if you are sure that the Lot has been billed in IFMS and submitted to treasury')){return false;}">
                  <td>
                      <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
                      <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
                      <!-- <input type="hidden" name="_method" value="DELETE"> -->
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    
                      <button type="submit" class="btn btn-danger btn-margin">
                        Submitted to Treasury
                      </button>
                    </td>
                  </form> 
                  @elseif($report->lot_status==0 and $report->push_to_ifms_status ==1 and $report->dotdone_status==1 and $report->ack_status==1 and $report->voucher_no =='' and $report->ref_no!=-1 and $report->ack_status!=-1)
                  @if($report->rbi_flag)
				  <form method="GET" action="{{ route('rbi_payment_status') }}" class="submit-once" onSubmit="if(!confirm('Please click on OK if you are sure to import the RBI Report')){return false;}">
				  <!--<form method="GET" action="{{ route('wrong_file_test') }}" class="submit-once" onSubmit="if(!confirm('Please click on OK if you are sure to import the RBI Report')){return false;}">-->
                  <td>
                      <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
                      <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
                      <!-- <input type="hidden" name="_method" value="DELETE"> -->
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    
                      <button type="submit" class="btn btn-warning btn-margin">
                        Import RBI Report
                      </button>
                    </td>
                  </form>
					@else
					<td style="text-align: center">
						RBI Report Pending
					</td>
				  @endif
				  @elseif($report->ref_no==-1 and $report->ack_status==-1)
				  <td style="text-align: center">
				  <i class="glyphicon glyphicon-remove"></i>
				  </td>
				  @else
				  <td style="text-align: center">
				  <i class="glyphicon glyphicon-ok"></i>
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

<!-- /.row -->

</section>
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
      //dom: 'Bfrtip',
      "paging": true,
      "pageLength":20,
      "lengthMenu": [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      // buttons: [
      // 'pdf','excel','print'
      // ]
    });
  });
</script>
<!-- REQUIRED JS SCRIPTS -->

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>

</body>
</html>