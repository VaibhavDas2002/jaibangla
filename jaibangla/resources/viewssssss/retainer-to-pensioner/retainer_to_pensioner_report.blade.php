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

  #preloader{
    position: fixed;
    top:30%;
    left: 52%;
    z-index: 999;
    border:2px solid #000000;
    padding: 2%;
    background: aliceblue;
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
         Retainer To Pensioner Report
        </h1>
        <ol class="breadcrumb">
           <li><a href="#"><i class="fa fa-dashboard"></i> Retainer To Pensioner Report</a></li>
          <!-- <li class="active"></li> -->
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        
        <div class="box box-default">
          <div class="box-header with-border">
            <div class="row">
                <div class="col-sm-6">
                  <h3 class="box-title">Retainer To Pensioner Report</h3>
                </div>
                <div class="col-sm-6" align="right">
                  <font size="2" style="color: #e82a2a;">* If you want to export all data please select All from show entries</font>
                </div>
                
            </div>
          </div>
          <div class="box-body">
            <div class="text-primary" style="font-size: 16px;"><b>Date : @php print date('d/m/Y'); @endphp</b></div>
            <div class="table-responsive">
              <table id="example" class="display table-responsive" cellspacing="0" width="100%">
                <thead>
                  <tr role="row">
                    <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">ID</th>
                    <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Name</th>
                    <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Father's Name</th>
                    <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Block/ULB</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Voter ID Card</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Ration Card</th> 
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank IFSC</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Acc. No</th>
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Transferred on</th>                  
                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Pensioner ID</th>                  
                  
                  </tr>
                </thead>
              </table>
            </div>
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
      "processing": true,
      "serverSide": true,
      "ajax": "{{ route('retainer-to-pensioner-report') }}",
      "columns":[
          { "data": "id" },
          { "data": "ben_name" },
          { "data": "father_name" },
          { "data": "block_ulb_name" },
          { "data": "epic_voter_id" },
          { "data": "ration_card" },
          { "data": "bank_ifsc" },
          { "data": "bank_code" },
          { "data": "transferred_at" },
          { "data": "pensioner_id" },
      ],
      oLanguage: {sProcessing: "<div id='preloader'><img src='images/ZKZg.gif' width='60px'><h4><strong>Please Wait...</strong></h4><h5><strong>Report Date is Loading...</strong></h5></div>"},
      
      //dom: 'Bfrtip',
      dom: 'Blfrtip',
      "paging": true,
      "pageLength": 20,
      'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
                 
     
      buttons: [
       {
           extend: 'pdf',
           className: 'btn btn-primary',
           title: 'Retainer To Pensioner Report Date : <?php print date('d-m-Y'); ?>',
           "text": '<i class="fa fa-file-pdf-o" style="color: #17ad37;"></i> PDF',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8,9],

            }
       },
       {   
           extend: 'print',
           title: 'Retainer To Pensioner Report Date : <?php print date('d-m-Y'); ?>',
           "text": '<i class="fa fa-print" style="color: #d44317;"></i> Print',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           autoPrint: true,
           page : 'all',
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8,9],
                stripHtml: true,
            }
       },
       {
           extend: 'excel',
           title: 'Retainer To Pensioner Report Date : <?php print date('d-m-Y'); ?>',
           "text": '<i class="fa fa-file-excel-o" style="color: #161c9c;"></i> Excel',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8,9],
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