<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>JB | JaiBangla
  </title>
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("frontend/img/favicon.ico") }}">
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
         Purohit Benficiaries Report
        </h1>
        <ol class="breadcrumb">
           <li><a href="#"><i class="fa fa-dashboard"></i> Purohit Monthly Financial Assistance Report</a></li>
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
                  <h3 class="box-title">Purohit Monthly Financial Assistance Report</h3>
                </div>
                
            </div>
          </div>
          <div class="box-body">
            @if(Auth::user()->designation_id == 'Approver')
            <form method="POST" action="{{ route('filter-purohit-monthly') }}" name="filter_form" id="filter_form">
              {{ csrf_field() }}
              <input type="hidden" name="dist_code" id="dist_code" value="{{$district_code}}">
              <div class="row">
                <div class="col-md-5">
                  <label for="block_ulb">Filter: Block/Municipality</label>
                  <select name="block_ulb" id="block_ulb" class="form-control select2">
                    <option value="">--Select--</option>
                    @foreach($block_ulb_list as $block_ulb)
                      <option value="{{$block_ulb->block_ulb_code}}">{{$block_ulb->block_ulb_name}}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3" style="padding-top: 20px;">
                  <input type="submit" name="submit" value="Filter" class="btn btn-info">
                  <a href="{{ route('purohit-monthly-report') }}" class="btn btn-default">Reset</a>
                </div>
              </div>
            </form>
            @endif
          
            <div style="font-size: 16px; font-weight: bold;" class="text-primary">
              <span style="float: right;">Date : @php print date('d/m/Y'); @endphp</span>
            </div>
            
        
               <table id="example" class="display table-responsive" cellspacing="0" width="100%">

                <thead>

                      <tr role="row">
                        <!-- <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">SL No.</th> -->

                        <th width="17%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">District</th>
                        @if(Auth::user()->designation_id == 'Approver')
                        <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Block/Municipality</th>
                        @endif
                        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Application Phase</th>
                        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Total Beneficiary</th>
                        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Pending Verifier</th>
                        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Pending Recommendation</th>
                        <th width="8%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Pending Approval</th>
                        <th width="8%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Total Approved</th>
                        <th width="9%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Total Rejected</th>
                        <th width="3%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending"></th>
                      </tr>
                    </thead>

                    <tbody>
                      @php $i = 1; @endphp
                      @foreach($report as $rep)
                      <tr>
                        <!-- <td>@php print $i++; @endphp</td> -->
                        <td>{{ $rep->district_name }}</td>
                        @if(Auth::user()->designation_id == 'Approver')
                        <td>{{ $rep->block_ulb_name }}</td>
                        @endif
                        <td>{{ $rep->app_phase }}</td>
                        <td>{{ $rep->total_ben }}</td>
                        <td>{{ $rep->pending_verifier }}</td>
                        <td>{{ $rep->pending_recommendation }}</td>
                        <td>{{ $rep->pending_approval }}</td>
                        <td>{{ $rep->approved }}</td>
                        <td>{{ $rep->rejected }}</td>
                        <td align="center">
                          @if(Auth::user()->designation_id == 'Approver')
                          <a href="{{ url('generate-excel-purohit/'.$rep->dist_code.'/'.$rep->block_ulb_code) }}" title="Download Approved List"><i class="fa fa-download" style="color: #000;"></i></a></td>
                          @elseif(Auth::user()->designation_id == 'HOD')
                          <a href="{{ url('generate-excel-purohit-hod/'.$rep->dist_code) }}"><i class="fa fa-download" style="color: #000;" title="Download Approved List"></i></a></td>
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
      "paging": true,
      "pageLength": 20,
      'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
                 
     
      buttons: [
       {
           extend: 'pdf',
           //title: 'Line Listing Report of  ',
           title: 'Purohit Monthly Beneficiary Report - Date- <?php echo date('d-m-Y');  ?>',
           messageTop:'Purohit Monthly Beneficiary Report\n Date:<?php echo date('d/m/Y');  ?>',
           text: '<b><i class="fa fa-file-pdf-o" style="color: #3e943d;"></i> PDF</b>',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8],

            }
       },
       {   
           extend: 'print',
           title: 'Purohit Monthly Beneficiary Report - Date- <?php echo date('d-m-Y');  ?>',
           messageTop:'Purohit Monthly Beneficiary Report \n Date:<?php echo date('d/m/Y');  ?>',
           text: '<b><i class="fa fa-print" style="color: #d44317;"></i> Print</b>',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8],
                stripHtml: true,
            }
       },
       {
           extend: 'excel',
           title: 'Purohit Monthly Beneficiary Report - Date- <?php echo date('d-m-Y');  ?>',
           messageTop:'Purohit Monthly Beneficiary Report\n Date:<?php echo date('d/m/Y');  ?>',
           text: '<b><i class="fa fa-file-excel-o" style="color: #161c9c;"></i> Excel</b>',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8],
                stripHtml: true,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    } );

    //$('[data-toggle="tooltip"]').tooltip();
    $('#filter_form').on('submit', function(){
      if ($('#block_ulb').val() == '') {
        alert('Select Filter');
        return false;
      }
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