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
      width: 8%;
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
        Cumulative Beneficiary Details
      </h1>
      <ol class="breadcrumb">
        <!-- li><a href="#"><i class="fa fa-dashboard"></i>  Level</a></li-->
        <li class="active"><i class="fa fa-dashboard"></i> Cumulative Beneficiary Details</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      <form method="POST" action="{{ route('result-cumulative-ben-details') }}" onsubmit="return validate();">
        {{csrf_field()}}
        <div class="row">
          <div class="col-md-4">
            <label for="scheme">Scheme</label>
            <select id="scheme" name="scheme" required class="form-control">
              <option value="0">--Select Scheme--</option>
              @foreach($schemes as $scheme)
                <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label for="month">Application Creation Month</label>
            <select class="form-control" name="month" id="month" required>
              <option value="0">--Select Application Creation Month--</option>
              <option value="January">January</option>
              <option value="February">February</option>
              <option value="March">March</option> 
              <option value="April">April</option>
              <option value="May">May</option>   
              <option value="June">June</option>   
              <option value="July">July</option>   
              <option value="August">August</option>      
              <option value="September">September</option>   
              <option value="October">October</option>   
              <option value="November">November</option>   
              <option value="December">December</option>    
            </select>
          </div>
          <div class="col-md-2">
            <button class="btn btn-info btn-lg" id="view_details" name="view_details">View Details</button>
          </div>
          <div class="col-md-2" id="details_loader_img_div" style="display: none;">
             <img src="images/ZKZg.gif" width="50px">
          </div>
        <!--</div>-->
        </div>
      </form>
      <br />

      <!-- Total Beneficiary Payment Done Current Month -->
      <div id="print_div">
      @if(isset($ben))
      <div>
        <h4 class="text-success"><span>Scheme : <b>{{$scheme_name}}</b></span><span> for the month of <b>{{$month}}</b></span></h4>
      </div>  
      <div>
        <h4>Total Beneficiary Payment Done: <b>{{$total_beneficiary_payment_done}}</b></h4>
      </div>
        
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Cumulative Beneficiary Details</h3>
        </div>
        <div class="box-body">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="col-sm-12">
                <table id="tbl_report" class="dataTables table table-bordered table-hover" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>Total application</th>
                      <td>{{$total_application}}</td>
                      <th>Current approved</th>
                      <td>{{$current_approved}}</td>
                      <th>Total rejection</th>
                      <td>{{$total_rejection}}</td> 
                    </tr>
                    <tr>
                      <th>Total post approval rejection</th>
                      <td>{{$total_post_approval_rejection}}</td> 
                      <th>Still pending @ Approver end</th>
                      <td>{{$still_pending_approver_end}}</td>
                      <th>Still pending @ Verifier end</th>
                      <td>{{$still_pending_verifier_end}}</td> 
                    </tr>
                    <tr>
                      <th>Payment done for approved ben</th>
                      <td>{{$payment_done_for_approved_ben}}</td> 
                      <th>Lot not generated for approved ben</th>
                      <td>{{$lot_not_generated_for_approved_ben}}</td> 
                      <th>IFMS error received for approved ben</th>
                      <td>{{$ifms_error_received_for_approved_ben}}</td>
                    </tr>
                    <tr>
                      <th>RBI error received for approved ben</th>
                      <td>{{$rbi_error_received_for_approved_ben}}</td>
                      <th>SBI error received for approved ben</th>
                      <td>{{$sbi_error_received_for_approved_ben}}</td>
                      <th>Duplicate rejection payment done</th> 
                      <td>{{$duplicate_rejection_payment_done}}</td>
                    </tr>
                    <tr>
                      <th>Duplicate rejection lot not generated</th>
                      <td>{{$duplicate_rejectionlot_not_generated}}</td> 
                      <th>Duplicate rejection lot IFMS error received</th>
                      <td>{{$duplicate_rejectionlot_ifms_error_received}}</td> 
                      <th>Duplicate rejection lot RBI error received</th>
                      <td>{{$duplicate_rejectionlot_rbi_error_received}}</td>
                    </tr>
                    <tr>
                      <th>Duplicate rejection lot SBI error received</th>
                      <td>{{$duplicate_rejectionlot_sbi_error_received}}</td> 
                      <th>Deactivated cases payment done</th>
                      <td>{{$deactivated_cases_payment_done}}</td> 
                      <th>Deactivated cases lot not generated</th> 
                      <td>{{$deactivated_cases_lot_not_generated}}</td>
                    </tr>
                    <tr>
                      <th>Deactivated cases lot IFMS error received</th>
                      <td>{{$deactivated_cases_lot_ifms_error_received}}</td> 
                      <th>Deactivated cases lot RBI error received</th>
                      <td>{{$deactivated_cases_lot_rbi_error_received}}</td>
                      <th>Deactivated cases lot SBI error received</th>
                      <td>{{$deactivated_cases_lot_sbi_error_received}}</td>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
      
      <!-- Total Successful Credit of Current Month -->
      @if(isset($pay))
      <div>
        <h4>Total Successful Credit: <b>{{$total_successful_credit}}</b></h4>
      </div>

      <div class="box box-success">
        <div class="box-header">
          <h3 class="box-title">Cumulative Payment Details</h3>
        </div>
        <div class="box-body">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="col-sm-12">
                <table id="tbl_report" class="dataTables table table-bordered table-hover" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>Total IFMS Payment generated</th>
                      <td>{{$total_ifms_payment_generated}}</td>
                      <th>Total beneficiaries affected</th>
                      <td>{{$total_beneficiaries_affected_ifms}}</td>
                      <th>IFMS payment failed</th>
                      <td>{{$ifms_payment_failed}}</td> 
                    </tr>
                    <tr>
                      <th>Payment processed but IFMS failed</th>
                      <td>{{$payment_processed_but_ifms_failed}}</td> 
                      <th>RBI Payment Failed</th>
                      <td>{{$rbi_payment_failed}}</td>
                      <th>Successful credit (a)</th>
                      <td>{{$successful_credit_rbi_a}}</td> 
                    </tr>
                    <tr>
                      <th>Total SBI Payment generated</th>
                      <td>{{$total_sbi_payment_generated}}</td> 
                      <th>Total beneficiaries affected</th>
                      <td>{{$total_beneficiaries_affected_sbi}}</td> 
                      <th>SBI payment failed</th>
                      <td>{{$sbi_payment_failed}}</td>
                    </tr>
                    <tr>
                      <th>Successful credit (b)</th>
                      <td>{{$successful_credit_sbi_b}}</td>
                      <th>Pending</th>
                      <td>{{$pending}}</td>
                      <th>Total Successful credit (a+b)</th> 
                      <td>{{$total_successful_credit}}</td>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif

      <div class="js_previous_month">
        
      </div>
    
      </div>
      @if(isset($ben))
      <div align="center" id="loader_img_div" style="display: none;">
        <img src="images/ZKZg.gif" width="100px">
      </div>
      <div align="center" id="pre_btn_div">
        <button class="btn btn-primary btn-lg" id="previous_btn" name="{{$scheme_id}}-{{$month}}" onclick="getPreviousData(this.name)">Pervious Month Beneficiary Details</button>
      </div>
      <div align="center" style="margin-top: 5px;">
        <button class="btn btn-success btn-lg" onclick="printDiv('print_div')"><i class="fa fa-print"></i> Print Details {{$scheme_name}} for the month of {{$month}}</button>
      </div>
      @endif
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

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
<script>
  function validate() {
    if (document.getElementById('scheme').value == 0) {
      alert('Please select scheme');
      return false;
    }
    if (document.getElementById('month').value == 0) {
      alert('Please select month');
      return false;
    }
    document.getElementById('view_details').setAttribute("disabled","disabled");
    document.getElementById('details_loader_img_div').style.display = '';
    return true;
  }
  function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
  }

  
  function getPreviousData(value){
  //alert(value);
  document.getElementById('pre_btn_div').style.display = 'none';
  document.getElementById('loader_img_div').style.display = '';
  var myarr = value.split("-");
  var scheme_id = myarr[0];
  var month = myarr[1];
  //alert(scheme_id);
  //alert(month);
  getData(scheme_id,month);
}

function getData(scheme_id, month) {
  //alert('1');    
  loadItems11(scheme_id, month, '../jaibangladev/api/getpreviousben/', '.js_previous_month');
}

function loadItems11(scheme_id, month, path, selectInputClass) {
  //alert('2');
  $.ajax({
  type: 'GET',
  url: path +scheme_id+'/'+month,
  success: function (datas) {
    if (!datas || datas.length === 0) {
      //alert("sucess with 0 data");
       return;
    }
    //alert(datas);
    //console.log(datas['pre_total_application']);
    var html = ''; 

    // Full Box
    html = "<div><h4>Cumulative Beneficiary Details Previous Months Payment Done: <b>"+datas['pre_total_beneficiary_payment_done']+"</b></h4></div>";
    html += '<div class="box box-warning"><div class="box-header"><h3 class="box-title">Cumulative Beneficiary Details</h3></div><div class="box-body"><div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap"><div class="row"><div class="col-sm-12"><table id="tbl_report" class="dataTables table table-bordered table-hover" cellspacing="0" width="100%"><thead>';
    
    html += "<tr><th>Total application</th><td>"+datas['pre_total_application']+"</td><th>Current approved</th><td>"+datas['pre_current_approved']+"</td><th>Total rejection</th><td>"+datas['pre_total_rejection']+"</td></tr>";
    
    html += "<tr><th>Total post approval rejection</th><td>"+datas['pre_total_post_approval_rejection']+"</td><th>Still pending @ Approver end</th><td>"+datas['pre_still_pending_approver_end']+"</td><th>Still pending @ Verifier end</th><td>"+datas['pre_still_pending_verifier_end']+"</td></tr>";

    html += "<tr><th>Payment done for approved ben</th><td>"+datas['pre_payment_done_for_approved_ben']+"</td><th>Lot not generated for approved ben</th><td>"+datas['pre_lot_not_generated_for_approved_ben']+"</td><th>IFMS error received for approved ben</th><td>"+datas['pre_ifms_error_received_for_approved_ben']+"</td></tr>";

    html += "<tr><th>RBI error received for approved ben</th><td>"+datas['pre_rbi_error_received_for_approved_ben']+"</td><th>SBI error received for approved ben</th><td>"+datas['pre_sbi_error_received_for_approved_ben']+"</td><th>Duplicate rejection payment done</th><td>"+datas['pre_duplicate_rejection_payment_done']+"</td></tr>";

    html += "<tr><th>Duplicate rejection lot not generated</th><td>"+datas['pre_duplicate_rejectionlot_not_generated']+"</td><th>Duplicate rejection lot IFMS error received</th><td>"+datas['pre_duplicate_rejectionlot_ifms_error_received']+"</td><th>Duplicate rejection lot RBI error received</th><td>"+datas['pre_duplicate_rejectionlot_rbi_error_received']+"</td></tr>";

    html += "<tr><th>Duplicate rejection lot SBI error received</th><td>"+datas['pre_duplicate_rejectionlot_sbi_error_received']+"</td><th>Deactivated cases payment done</th><td>"+datas['pre_deactivated_cases_payment_done']+"</td><th>Deactivated cases lot not generated</th><td>"+datas['pre_deactivated_cases_lot_not_generated']+"</td></tr>";

    html += "<tr><th>Deactivated cases lot IFMS error received</th><td>"+datas['pre_deactivated_cases_lot_ifms_error_received']+"</td><th>Deactivated cases lot RBI error received</th><td>"+datas['pre_deactivated_cases_lot_rbi_error_received']+"</td><th>Deactivated cases lot SBI error received</th><td>"+datas['pre_deactivated_cases_lot_sbi_error_received']+"</td></tr>";
    
    html += '</thead></table></div></div></div></div></div>';
    $(selectInputClass).html(html);
    document.getElementById('loader_img_div').style.display = 'none';
  },
  error: function (ex) {
     //alert('error url');
  }
  });
}
</script>     
</body>
</html>
