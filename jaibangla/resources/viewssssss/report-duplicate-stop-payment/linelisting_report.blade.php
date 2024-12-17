<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>JB | JaiBangla
  </title>
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("images/favicon.ico") }}">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"> -->
  <!-- Theme style -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
      <link href="{{ asset("css/select2.min.css") }}" rel="stylesheet">
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet" type="text/css" />  
  
   
   
   <link rel="stylesheet" href="{{ asset("/css/jquery.dataTables.min.css")}}">
    <link rel="stylesheet" href="{{ asset("/css/buttons.dataTables.min.css")}}">

   

   
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

  button {
    font-size: 16px;
  }

  /*Modal*/
  .example-modal .modal {
    position: relative;
    top: auto;
    bottom: auto;
    right: auto;
    left: auto;
    display: block;
    z-index: 1;
  }

  .example-modal .modal {
    background: transparent !important;
  }

  #preloader1{
    position: fixed;
    top:40%;
    left: 52%;
    z-index: 999;
  }
  #preloader1 {
    background: transparent !important;
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
         Report Duplicate and Stop Payment Beneficiary
        </h1>
        <ol class="breadcrumb">
           <li><a href="#"><i class="fa fa-dashboard"></i> Duplicate and Stop Payment</a></li>
          <!-- <li class="active"></li> -->
        </ol>
      </section>

      <div id="preloader1" align="center" style="display: none;">
        <img src="images/ZKZg.gif" width="100px">
      </div>
      <!-- Main content -->
      <section class="content">

        <div class="box box-default">
          <div class="box-header with-border">
            <div class="row">
                <div class="col-sm-8">
                  <h3 class="box-title">Duplicate and Stop Payment Beneficiary</h3>
                </div>
                
            </div>
          </div>
          <div class="box-body">
          
            <div style="font-size: 16px; font-weight: bold;" class="text-primary">
              <span style="float: right;">Date : @php print date('d/m/Y'); @endphp</span>
              <span>Scheme : {{$scheme_name}}</span><br>
              @if($filter == 'all')
              <span class="text-danger">Given List From : 01/12/2020</span>
              @else
              <span class="text-danger">Given List Only For : <?php print date('F') ?></span>
              @endif
            </div>
            
        
            <table id="example" class="display table-responsive" cellspacing="0" width="100%">
              <thead>
                <tr role="row" style="font-size: 14px;">
                  <th width="30%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">District</th>
                  <th width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">No. of Duplicate Rejected</th>
                  <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Download List</th>
                  <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of Stop Payment</th>
                  <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Download List</th>
                </tr>
              </thead>
              <tbody style="font-size: 14px;">
                @php $dup_total = 0; $stop_total = 0; @endphp
                @foreach($reports as $report)
                @php 
                  $dup_total += $report->duplicate_reject_count;
                  $stop_total += $report->stop_payment_count;
                @endphp
                <tr>
                  <td>{{ $report->district }}</td>
                  <td>{{ $report->duplicate_reject_count }}</td>
                  <td>
                    @if($report->duplicate_reject_count != 0)
                    <form method="POST" action="{{ route('excel-report-duplicate-reject') }}">
                      {{ csrf_field() }}
                      <input type="hidden" name="district_code" id="district_code" value="{{ $report->district_code }}">
                      <input type="hidden" name="scheme_id" id="scheme_id" value="{{ $scheme_id }}">
                      <input type="hidden" name="date" id="date" value="{{ $date }}">
                      <button type="submit" name="duplicate_btn" id="duplicate_btn" class="btn btn-link btn-sm" title="Get Duplicate List Beneficiary">Download <i class="fa fa-download"></i></button>
                    </form>
                    
                    @endif
                  </td>
                  <td>{{ $report->stop_payment_count }}</td>
                  <td>
                    @if($report->stop_payment_count != 0)
                    <form method="POST" action="{{ route('excel-report-stop-payment') }}">
                      {{ csrf_field() }}
                      <input type="hidden" name="district_code" id="district_code" value="{{ $report->district_code }}">
                      <input type="hidden" name="scheme_id" id="scheme_id" value="{{ $scheme_id }}">
                      <input type="hidden" name="date" id="date" value="{{ $date }}">
                      <button type="submit" name="stop_btn" id="stop_btn" class="btn btn-link btn-sm" title="Get Stop Payment List Beneficiary">Download <i class="fa fa-download"></i></button>
                    </form>
                    
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
	      <tfoot>
		<tr>
                  <th>Total</th>
                  <th>@php print $dup_total; @endphp</th>
                  <th></th>
                  <th>@php print $stop_total; @endphp</th>
                  <th></th>
                </tr>
	      </tfoot>
            </table>
          </div>
        </div>
        
  </div>
  <!-- Footer -->
  @include('layouts.footer')
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
<script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("js/dataTables.buttons.min.js") }}"></script>
<script src="{{ asset("js/buttons.flash.min.js") }}"></script>
<script src="{{ asset("js/jszip.min.js") }}"></script>
<script src="{{ asset("js/pdfmake.min.js") }}"></script>
<script src="{{ asset("js/vfs_fonts.js") }}"></script>
<script src="{{ asset("js/buttons.html5.min.js") }}"></script>
<script src="{{ asset("js/buttons.print.min.js") }}"></script>


<script>

  $(document).ready(function() {
    
    $('#example').DataTable( {
      //dom: 'Bfrtip',
      dom: 'Blfrtip',
      "paging": false,
      "pageLength": 20,
      'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
                 
     
      buttons: [
       {
           extend: 'pdf',
           //title: 'Line Listing Report of  ',
           title: 'Report Duplicate and Stop Payment {{$scheme_name}} <?php echo date('d-m-Y');  ?>',
           messageTop:'Date:<?php echo date('d/m/Y');  ?>\n Scheme : {{$scheme_name}}',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,3],

            }
       },
       {   
           extend: 'print',
           title: 'Report Duplicate and Stop Payment {{$scheme_name}} <?php echo date('d-m-Y');  ?>',
           messageTop:'Date:<?php echo date('d/m/Y');  ?>\n Scheme : {{$scheme_name}}',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,3],
                stripHtml: true,
            }
       },
       {
           extend: 'excel',
           title: 'Report Duplicate and Stop Payment {{$scheme_name}} <?php echo date('d-m-Y');  ?>',
           messageTop:'Date:<?php echo date('d/m/Y');  ?>\n Scheme : {{$scheme_name}}',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,3],
                stripHtml: true,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    } );

    //$('[data-toggle="tooltip"]').tooltip();
  });

</script>
<!-- REQUIRED JS SCRIPTS -->

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>

</body>
</html>