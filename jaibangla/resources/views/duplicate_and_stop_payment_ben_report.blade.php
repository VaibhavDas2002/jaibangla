<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>JB | Jai Bangla</title>
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("frontend/img/favicon.ico") }}">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
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

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <style>
    #tbl_report tr th {
      width: 25%;
    }
    #tbl_report tr td {
      font-size: 16px;
    }
  </style>
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
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
        Duplicate and Deactivated Beneficiary Report
      </h1>
      <ol class="breadcrumb">
        <!-- li><a href="#"><i class="fa fa-dashboard"></i>  Level</a></li-->
        <li class="active"><i class="fa fa-dashboard"></i>Report Date: <?php echo date("d/m/Y") ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      <form method="GET" action="" onsubmit="return validate();" id="submit_form" name="submit_form">
        {{csrf_field()}}
        <div class="row">
          <div class="col-md-5">
            <label for="scheme">Scheme</label>
            <select id="scheme" name="scheme" required autofocus class="form-control">
              <option value="">--Select Scheme--</option>
              @foreach($schemes as $scheme)
                <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <button class="btn btn-info btn-lg" id="view_details" name="view_details">View Result</button>
          </div>
          <div class="col-md-2" id="details_load_img_div" style="display: none;">
             <img src="images/ZKZg.gif" width="50px"><span id="second"></span>
          </div>
        </div>
      </form>
      <br />
      <span id="demo"></span>
      <!-- Total Beneficiary Payment Done Current Month -->
      <div id="print_div">
      
      <div>
        <h4 class="text-success"><span>Scheme : <b id="scheme_text"></b></span></h4>
      </div> 
        
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Duplicate and Deactivated Beneficiary Report</h3>
        </div>
        <div class="box-body">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="col-sm-12">
                <table id="tbl_report" class="dataTables table table-bordered table-hover" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>District Name</th>
                      <th>No. of Duplicate Beneficiary deactivated</th>
                      <th>No. of Beneficiary Adjusted due to duplication</th>
                      <th>No. of Deactivated Beneficiaries</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($districts as $district)
                    <tr>
                      <td>@php print strtoupper($district->district_name) @endphp</td>
                      <td id="db_{{$district->district_code}}">0</td>
                      <td id="da_{{$district->district_code}}">0</td>
                      <td id="sp_{{$district->district_code}}">0</td>
                    </tr>
                    @endforeach
                    <tr>
                      <td><b>Total<b></td>
                      <td id="db_total">0</td>
                      <td id="da_total">0</td>
                      <td id="sp_total">0</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    
      </div> <!--End print div-->
      <div align="center">
        <button class="btn btn-success btn-lg" onclick="printDiv('print_div')"><i class="fa fa-print"></i> Print</button>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Footer -->
  @include('layouts.footer')
  
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

 <!-- jQuery 2.1.3 -->
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
<script>

  function validate() {
    if (document.getElementById('scheme').value == '') {
      alert('Please select scheme');
      document.getElementById('scheme').focus();
      return false;
    }
    
    document.getElementById('details_load_img_div').style.display = '';
    document.getElementById('view_details').setAttribute("disabled","disabled");
    return true;
  }

  function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
  }
  
  /*$('#submit_form').on('submit', function(e){
    e.preventDefault();
    var scheme = document.getElementById('scheme').value;
    
    $.ajax({
    type: 'GET',
    url: 'show-result-duplicate-reject'+'/'+scheme,
    success: function (datas) {
      if (!datas || datas.length === 0) {
        //alert("sucess with 0 data");
        return;
      }
      //console.log(JSON.stringify(datas));
      $('#scheme_text').text(datas.scheme_name);
      for (var  i = 0; i < datas.data.length; i++) {
        $('#db_'+datas.data[i].district_code).text(datas.data[i].duplicate);
        $('#da_'+datas.data[i].district_code).text(datas.data[i].approved);
        $('#sp_'+datas.data[i].district_code).text(datas.data[i].stop_payment);
      }
      document.getElementById('details_load_img_div').style.display = 'none';
      document.getElementById('scheme').value = '';
      document.getElementById('view_details').removeAttribute("disabled");
    },
    error: function (ex) {
      alert('Something went wrong!!');
    }
    });  
  });*/
  $('#submit_form').on('submit', function(e){
    e.preventDefault();
    var scheme = document.getElementById('scheme').value;
    getData(scheme);
  });

  function getData(scheme) {
    //alert('1');    
    loadItems11(scheme, '../api/getduplicateben/');
  }

  function loadItems11(scheme, path) {
    $.ajax({
    type: 'GET',
    url: path+scheme,
    success: function (datas) {
      if (!datas || datas.length === 0) {
        //alert("sucess with 0 data");
        return;
      }
     var duplicate_total=0;
     var approved_total =0;
     var stop_payment_total=0;
      //console.log(JSON.stringify(datas));
      $('#scheme_text').text(datas.scheme_name);
      for (var  i = 0; i < datas.data.length; i++) {
        $('#db_'+datas.data[i].district_code).text(datas.data[i].duplicate);
        $('#da_'+datas.data[i].district_code).text(datas.data[i].approved);
        $('#sp_'+datas.data[i].district_code).text(datas.data[i].stop_payment);
        duplicate_total +=datas.data[i].duplicate;
        approved_total +=datas.data[i].approved;
        stop_payment_total +=datas.data[i].stop_payment;
      }
      $('#db_total').text(duplicate_total);
      $('#da_total').text(approved_total);
      $('#sp_total').text(stop_payment_total);
      document.getElementById('details_load_img_div').style.display = 'none';
      document.getElementById('scheme').value = '';
      document.getElementById('view_details').removeAttribute("disabled");
    },
    error: function (ex) {
      alert('Something went wrong!!');
    }
    });
  }

</script>     
</body>
</html>
