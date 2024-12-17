<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>JB | Jai Bangla
  </title>
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("images/favicon.ico") }}">
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
  
   
   
  <!--data table--->
  <link rel="stylesheet" href="{{ asset("/css/jquery.dataTables.min.css")}}">
  <link rel="stylesheet" href="{{ asset("/css/buttons.dataTables.min.css")}}">

  <link href="{{ asset('css/jquery-confirm.min.css') }}" rel="stylesheet"> 

   
   <style>
      label.required:after {
                color: red;
                content:'*';
                font-weight: bold;
                margin-left: 5px;
                float:right;
                margin-top: 5px;
            }
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
.preloader1{
    position: fixed;
    top:40%;
    left: 52%;
    z-index: 999;
  }
  .preloader1 {
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
  <div class="content-wrapper">
    @if(Auth::user()->designation_id_old == 'Approver')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard <small>Approver</small>
      </h1>
      <ol class="breadcrumb">
        <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
      </ol>
    </section>
    <section class="content">
      @if ($message = Session::get('success'))
      <div class="alert alert-success alert-block successErrorMessage">
        <button type="button" class="close" data-dismiss="alert">×</button> 
              <strong>{{ $message }}</strong>
      </div>
      @elseif ($message = Session::get('danger'))
      <div class="alert alert-danger alert-block successErrorMessage">
        <button type="button" class="close" data-dismiss="alert">×</button> 
              <strong>{{ $message }}</strong>
      </div>
      @elseif ($message = Session::get('error'))
      <div class="alert alert-danger alert-block successErrorMessage">
        <button type="button" class="close" data-dismiss="alert">×</button> 
              <strong>{{ $message }}</strong>
      </div>
      @endif
      {{-- @php
        //Session::forget('sessionBankPending');
        //Session::forget('sessionApprovePending');
        //Session::forget('sessionDuplicateReject');
        $sessionBankPending=Session::get('sessionBankPending');
        $sessionApprovePending=Session::get('sessionApprovePending');
        $sessionDuplicateReject=Session::get('sessionDuplicateReject');

        $user_id = Auth::user()->id;
        $dist_code = \App\Configduty::where('user_id', $user_id)->value('district_code');

        if(empty($sessionBankPending)){
          $totalbankeditpending=\App\BeneficiaryPensions::where('bank_edited',0)->where('next_level_role_id',0)->whereIn('lot_generated',[-1,-2,-3])->where('created_by_dist_code',$dist_code)->count();
          $sessionBankPending=Session::put('sessionBankPending',$totalbankeditpending);
        }
        if( empty($sessionApprovePending)){
          $totalapprovepending=\DB::table('pension.beneficiary')->where('next_level_role_id','>',0)->where('created_by_dist_code',$dist_code)->count();
          $sessionApprovePending=Session::put('sessionApprovePending',$totalapprovepending);
        }
        if( empty($sessionDuplicateReject)){
          $totalduplicatereject=\DB::table('duplicate_approve_reject')->where('dist_code',$dist_code)->count();
          $sessionDuplicateReject=Session::put('sessionDuplicateReject',$totalduplicatereject);
        }
      @endphp --}}
      
      <div class="row" style="display: none;">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua" style="background: linear-gradient(to right, #00d2ff, #3a7bd5);"><i class="fa fa-fw fa-bank"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Total Bank Edit<br> Pending</span>
              <span class="info-box-number" id="bankEditText">Loading...<small></small></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green" style="background: linear-gradient(to right, #11998e, #38ef7d);"><i class="fa fa-thumbs-up" aria-hidden="true"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Total Approve<br> Pending</span>
              <span class="info-box-number" id="approvePendingText">Loading...<small></small></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow" style="background: linear-gradient(to right, #fc4a1a, #f7b733);"><i class="fa fa-eject" aria-hidden="true"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Total Duplicate<br> Reject</span>
              <span class="info-box-number" id="duplicateText">Loading...<small></small></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box" style="background: linear-gradient(to right, #ece9e6, #ffffff); box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <span style="margin-left: 20px;"><font style="font-size: 15px;"><b><u>Important Links</u></b></font></span><br>
            <ul style="font-weight: bold;">
              <li><a href="{{URL::to('/scheme-selection-common?type=V')}}" target="_blank">Approved List &nbsp;<i class="fa fa-external-link"></i></a></li>
              <li><a href="{{route('ben-payment-status')}}" target="_blank">Beneficiary Payment Status &nbsp;<i class="fa fa-external-link"></i></a></li>
              <li><a href="{{route('update-deactivate-beneficiary')}}" target="_blank">Update/Deactivate Beneficiary &nbsp;<i class="fa fa-external-link" aria-hidden="true"></i></a></li>
            </ul>
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <div class="col-md-12">
          <!-- Custom Tabs -->
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab">Beneficiary Approved/Reject</a></li>
              {{-- <li><a href="#tab_2" data-toggle="tab">Bank Edit Pending</a></li> --}}
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group col-md-3">
                       <label class=" control-label required"> Scheme</label> 
                      
                      <select class="form-control"  name="ben_scheme" id='ben_scheme'>
                        <option value="">--Select Scheme--</option>
                        @foreach($reports as $re)
          
                        <option value="{{$re->id}}" >{{$re->scheme_name}}</option>
                        @endforeach
                              
                      </select>
                    </div>
                    <div class="form-group col-md-2">
                      <label class=" control-label">&nbsp;</label>
                      <button class="btn btn-primary form-control" id="pendingsearch" type="button"><i class="fa fa-search"></i> Search</button>
                    </div>
                    <div class="form-group col-md-2">
                      <img src="images/ZKZg.gif" width="50px" style="display: none;" id="load1">
                    </div>
                  </div>

                  <div class="col-md-12 table-responsive" id="example_tab1">
                    <table id="example" class="display" width="100%">
                      <thead>
                        <tr role="row" class="sorting_asc">
                          <th width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Month-Year</th>
                          <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Total Applied</th>
                          <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Total Approved</th>
                          <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Approved Pending</th>
                          <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Rejected</th>
          
                        </tr>
                      </thead>
                      <tfoot>
                        <tr><th></th><th></th><th></th><th></th><th></th></tr>
                      </tfoot>       
                    </table>
                  </div>
                </div>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_2">
                <div class="row">
                    <div class="col-md-12">
                      <div class="form-group col-md-3">
                        <label class=" control-label required">Scheme</label> 
                        <select class="form-control"  name="bank_scheme" id='bank_scheme'>
                          <option value="">--Select Scheme Name--</option>
                          @foreach($reports as $r)
            
                          <option value="{{$r->id}}" >{{$r->scheme_name}}</option>
                          @endforeach
                                
                        </select>
                      </div>
                      <div class="form-group col-md-3">
                      <label class=" control-label required"> Level</label>
                      <select class="form-control"  name="bank_level" id='bank_level'>
                        <option value="">--Select Level--</option>
                        @foreach(Config::get('constants.rural_urban') as $ru=>$val)
                        <option value="{{ $ru}}">{{$val}}</option>
                        @endforeach
                              
                      </select>
                    </div>
                    <div class="form-group col-md-2">
                      <label class=" control-label">&nbsp;</label>
                      <button class="btn btn-primary form-control" id="pendingbanksearch" type="button"><i class="fa fa-search"></i> Search</button>
                    </div>
                    <div class="form-group col-md-2">
                      <img src="images/ZKZg.gif" width="50px" style="display: none;" id="load2">
                    </div>
                  </div>
                  <div class="col-md-12 table-responsive" id="example_tab2">
                    <table id="exampleBankEdit" class="display" width="100%">
                      <thead>
                        <tr>
                          <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending" style="border: 1px solid black; border-right-color: #fff;">Scheme Name</th>
                          <th width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" style="border: 1px solid black; border-right-color: #fff;">Block/Subdiv</th>
                          <th width="20%" colspan="2" style="text-align: center; border: 1px solid black; border-right-color: #fff;">IFMS</th>
                          <th width="20%" colspan="2" style="text-align: center; border: 1px solid black; border-right-color: #fff;">RBI</th>
                          <th width="20%" colspan="2" style="text-align: center;  border: 1px solid black;">SBI</th>
                        </tr>
                        <tr role="row" class="sorting_asc">
                          <th width="20%" style="border-left: 1px solid black;"></th>
                          <th width="20%"></th>
                          <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" style="border-left: 1px solid black;">Pending </th>
                          <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Rectified  </th>
                          <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" style="border-left: 1px solid black;">Pending </th>
                          <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Rectified  </th>
                          <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" style="border-left: 1px solid black;">Pending </th>
                          <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" style="border-right: 1px solid black;">Rectified  </th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
                      </tfoot>       
                    </table>
                  </div>
                </div>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      <!-- END CUSTOM TABS -->
    </section>
    <!-- /.content -->
    @else
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard <small>@php print Auth::user()->designation_id_old; @endphp</small>
      </h1>
      <ol class="breadcrumb">
        <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
      </ol>
    </section>
    <section class="content">
      <h5>@php print Auth::user()->designation_id_old; @endphp Dashboard</h5>
      @if ($message = Session::get('success'))
      <div class="alert alert-success alert-block successErrorMessage">
        <button type="button" class="close" data-dismiss="alert">×</button> 
              <strong>{{ $message }}</strong>
      </div>
      @elseif ($message = Session::get('danger'))
      <div class="alert alert-danger alert-block successErrorMessage">
        <button type="button" class="close" data-dismiss="alert">×</button> 
              <strong>{{ $message }}</strong>
      </div>
      @endif
    </section>
    @endif
    <!-- /.content -->
    
  </div>
  @include('layouts.footer')
</div>
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>

<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>
<script src="{{ URL::asset('js/site.js') }}"></script>

<script>
  $('.select2').select2();
</script>

<script src="{{ asset("js/jquery-1.12.4.js") }}"></script>
<script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("js/dataTables.buttons.min.js") }}"></script>
<script src="{{ asset("js/buttons.flash.min.js") }}"></script>
<script src="{{ asset("js/jszip.min.js") }}"></script>
<script src="{{ asset("js/pdfmake.min.js") }}"></script>
<script src="{{ asset("js/vfs_fonts.js") }}"></script>
<script src="{{ asset("js/buttons.html5.min.js") }}"></script>
<script src="{{ asset("js/buttons.print.min.js") }}"></script>
<script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
<script  src="{{ asset ("/bower_components/AdminLTE/moment/moment.js") }}" type="text/javascript" ></script>
<script>
  $(document).ready(function() {
    // Live Clock
    // getApproverDashboardData();
    var interval = setInterval(function () {
    var momentNow = moment();
      $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
      $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);
    var table='';
    $('#example_tab1').hide();
    $('#example_tab2').hide();
    $('#pendingsearch').click(function(){
      var ben_scheme=$('#ben_scheme').val();
      if(ben_scheme==  "" ){
        $.alert({
          title: 'Alert!!',
          // type: 'red',
          icon: 'fa fa-warning',
          content: '<strong>Please select scheme</strong>',
        });
      return false;
      }
      else{
        // $('#load1').css('display','');
        var ben_scheme=$('#ben_scheme').val();
        $('#example_tab1').show();
        
        if ( $.fn.DataTable.isDataTable('#example') ) {
          $('#example').DataTable().destroy();
         }
        // $('#example_tab1').hide();
        var table=$('#example').DataTable( {
          dom: 'Blfrtip',
          "scrollX": true,
          "paging": false,
          "searchable": true,
          "bFilter": true,
          // "bInfo": false,
          "pageLength":10,
          'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><img src=\'{{ asset("images/ZKZg.gif") }}\' width="100px"></div>'
          },
          "ajax": 
          {
            url: "{{ url('getApproverBenARPending') }}",
            type: "post",
            data:function(d){
              d.ben_scheme= $('#ben_scheme').val(),
              d._token= "{{csrf_token()}}"
            },
            error: function (jqXHR, textStatus, errorThrown) {
              // $('#load1').css('display','none');
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete":function(){
            console.log('Data rendered successfully');
            
           // $('#example_tab1').show();
            // $('#load1').css('display','none');
          },
          "columns": [
            { "data": "year_month","defaultContent":"Null" },
            { "data": "applied","defaultContent":"0" },
            { "data": "approved","defaultContent":"0" },
          
            { "data": "pending_approved","defaultContent":"0" },
            { "data": "rejected","defaultContent":"0" },
           
          ],

          "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over this page
            applied = api
              .column( 1, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
              approved = api
              .column( 2, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
              pending_approved = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
              rejected = api
              .column( 4, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 ); 
      
      
            // Update footer
            $( api.column( 0 ).footer() ).html(
              "Total: "
            );
            $( api.column( 1 ).footer() ).html(
              applied
            );
            $( api.column( 2 ).footer() ).html(
              approved
            );
           
            $( api.column( 3).footer() ).html(
              pending_approved
            );
            $( api.column( 4 ).footer() ).html(
              rejected
            );
           
        },     
      
          "buttons": [
            'pdf','excel'
          ],
        });
      }   
    });

    $('#pendingbanksearch').click(function(){
      var bank_scheme=$('#bank_scheme').val();
      var bank_level=$('#bank_level').val();
      if(bank_scheme==  ""  || bank_level==""){
        $.alert({
          title: 'Alert!!',
          // type: 'red',
          icon: 'fa fa-warning',
          content: '<strong>Please select scheme & level</strong>',
        });
      return false;
      }
      else {
        // $('#load2').css('display','');
        if ( $.fn.DataTable.isDataTable('#exampleBankEdit') ) {
          $('#exampleBankEdit').DataTable().destroy();
         }
        $('#example_tab2').show();
        var table=$('#exampleBankEdit').DataTable( {
          dom: 'Blfrtip',
          "scrollX": true,
          "paging": false,
          "searchable": true,
          "bFilter": true,
          // "bInfo": false,
          "pageLength":10,
          'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><img src=\'{{ asset("images/ZKZg.gif") }}\' width="100px"></div>'
          },
          "ajax": 
          {
            url: "{{ url('getBankEditPending') }}",
            type: "post",
            data:function(d){
              d.bank_scheme= $('#bank_scheme').val(),
              d.bank_level=$('#bank_level').val(),
              d._token= "{{csrf_token()}}"
            },
            error: function (jqXHR, textStatus, errorThrown) {
              // $('#load2').css('display','none');
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete":function(){
            
            console.log('Data rendered successfully');
           // $('#example_tab2').show();
            // $('#load2').css('display','none');
          },
          "columns": [
            { "data": "scheme_name","defaultContent":"Null" },
            { "data": "block_name","defaultContent":"Null" },
            { "data": "ifms_pending","defaultContent":"0" },
            { "data": "ifms_rectified","defaultContent":"0" },
            { "data": "rbi_pending","defaultContent":"0" },
            { "data": "rbi_rectified","defaultContent":"0" },
            { "data": "sbi_pending","defaultContent":"0" },
            { "data": "sbi_rectified","defaultContent":"0" },
          ],
          "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over this page
            ifms_pending = api
              .column( 2, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
            ifms_rectified = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
            rbi_pending = api
              .column( 4, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
            rbi_rectified = api
              .column( 5, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
            sbi_pending = api
              .column( 6, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
            sbi_rectified = api
              .column( 7, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
      
      
            // Update footer
            $( api.column( 0 ).footer() ).html(
              "Total: "
            );
            $( api.column( 2 ).footer() ).html(
              ifms_pending
            );
            $( api.column( 3 ).footer() ).html(
              ifms_rectified
            );
            $( api.column( 4 ).footer() ).html(
              rbi_pending
            );
           $( api.column( 5 ).footer() ).html(
              rbi_rectified
            );
            $( api.column( 6).footer() ).html(
              sbi_pending
            );
            $( api.column( 7 ).footer() ).html(
              sbi_rectified
            );
           
        },    
          "buttons": [
            'pdf','excel'
          ],
        });
      }   
    });

    function ajax_error(jqXHR, textStatus, errorThrown){
      var msg = "<strong>Failed to Load data.</strong><br/>";
      if (jqXHR.status !== 422 && jqXHR.status !== 400) {
        msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
      } 
      else {
        if (jqXHR.responseJSON.hasOwnProperty('exception')) {
          msg += "Exception: <strong>" + jqXHR.responseJSON.exception_message + "</strong>";
        } 
        else {
          msg += "Error(s):<strong><ul>";
          $.each(jqXHR.responseJSON, function (key, value) {
            msg += "<li>" + value + "</li>";
          });
          msg += "</ul></strong>";
        }
      }
      $.alert({
        title: 'Error!!',
        type: 'red',
        icon: 'fa fa-warning',
        content: msg,
      });
    }
  });

  function timestamp() {
    $.ajax({
        url: 'http://localhost/timestamp.php',
        success: function(data) {
            $('#timestamp').html(data);
        },
    });
}

function getApproverDashboardData(){
  $.ajax({
            type: 'post',
            url: "{{route('getApproverDashboardData')}}",
            data: {_token:"{{csrf_token()}}"},
           
            dataType: 'json',
            success: function (response) {
               
                $('#duplicateText').text(response.duplicatepending);
                $('#approvePendingText').text(response.approvepending);
                $('#bankEditText').text(response.bankedit)
            
           
            },
            complete: function(){
           
            },
            error: function (jqXHR, textStatus, errorThrown) {

            ajax_error(jqXHR, textStatus, errorThrown); 
            }
            });
}
</script>
<!-- REQUIRED JS SCRIPTS -->

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>

</body>
</html>