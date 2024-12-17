<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>JB | Jai Bangla
  </title>
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("frontend/img/favicon.ico") }}">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet"
    type="text/css" />

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
  <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet"
    type="text/css" />



  <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css"> -->
  <!--data table--->
  <link rel="stylesheet" href="{{ asset("/css/jquery.dataTables.min.css")}}">
  <link rel="stylesheet" href="{{ asset("/css/buttons.dataTables.min.css")}}">

  <link href="{{ asset('css/jquery-confirm.min.css') }}" rel="stylesheet">


  <style>
    .errorField {
      border-color: #990000;
    }

    .searchPosition {
      margin: 70px;
    }

    .submitPosition {
      margin: 25px 0px 0px 0px;
    }


    .typeahead {
      border: 2px solid #FFF;
      border-radius: 4px;
      padding: 8px 12px;
      max-width: 300px;
      min-width: 290px;
      background: rgba(66, 52, 52, 0.5);
      color: #FFF;
    }

    .tt-menu {
      width: 300px;
    }

    ul.typeahead {
      margin: 0px;
      padding: 10px 0px;
    }

    ul.typeahead.dropdown-menu li a {
      padding: 10px !important;
      border-bottom: #CCC 1px solid;
      color: #FFF;
    }

    ul.typeahead.dropdown-menu li:last-child a {
      border-bottom: 0px !important;
    }

    .bgcolor {
      max-width: 550px;
      min-width: 290px;
      max-height: 340px;
      background: url("world-contries.jpg") no-repeat center center;
      padding: 100px 10px 130px;
      border-radius: 4px;
      text-align: center;
      margin: 10px;
    }

    .demo-label {
      font-size: 1.5em;
      color: #686868;
      font-weight: 500;
      color: #FFF;
    }

    .dropdown-menu>.active>a,
    .dropdown-menu>.active>a:focus,
    .dropdown-menu>.active>a:hover {
      text-decoration: none;
      background-color: #1f3f41;
      outline: 0;
    }

    table.dataTable thead th,
    table.dataTable thead td {
      padding: 10px 13px;
    }

    table.dataTable tfoot th,
    table.dataTable tfoot td {
      padding: 10px 5px;
    }

    .criteria1 {
      text-transform: uppercase;
      font-weight: bold;
    }

    #example_length {
      margin-left: 40%;
      margin-top: 2px;
    }

    @keyframes spinner {
      to {
        transform: rotate(360deg);
      }
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

    .select2 {
      width: 100% !important;
    }

    .select2 .has-error {
      border-color: #cc0000;
      background-color: #ffff99;
    }

    .preloader1 {
      position: fixed;
      top: 40%;
      left: 52%;
      z-index: 999;
    }

    .preloader1 {
      background: transparent !important;
    }

    .disabledcontent {
      pointer-events: none;
      opacity: 0.4;
    }

    .has-error {
      border-color: #cc0000;
      background-color: #ffff99;
    }

    .modal {
      text-align: center;
      padding: 0 !important;
    }

    .modal:before {
      content: '';
      display: inline-block;
      height: 100%;
      vertical-align: middle;
      margin-right: -4px;
    }

    .modal-dialog {
      display: inline-block;
      text-align: left;
      vertical-align: middle;
    }

    label.required:after {
      color: red;
      content: '*';
      font-weight: bold;
      margin-left: 5px;
      float: right;
      margin-top: 5px;
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
  <div class="preloader1"><img src="../images/ZKZg.gif" width="100px" id="loader_img"></div>
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
          Lot Transaction SBI
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Lot Transaction SBI</a></li>
          <!-- <li class="active">Duplicate Approve</li> -->
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="box box-default">
          {{-- <div class="box-header with-border">
            <div class="row">
              <div class="col-sm-8">
                <h3 class="box-title">Lot Transaction SBI</h3>
              </div>
            </div>
          </div> --}}
          <div class="box-body">
            <div class="row">
              <div class="col-md-12">
                @if (($message = Session::get('success')) )
                <div class="alert alert-success alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }} </strong>
                </div>
                @endif
                @if (($message = Session::get('message')))
                <div class="alert alert-danger alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }}</strong>
                </div>
                @endif
                @if (($message = Session::get('msg1')))
                <div class="alert alert-danger alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }}</strong>
                </div>
                @endif
                <div class="row">
                  <div class="col-md-12">

                    <div class="form-group col-md-3">
                      <label class=" control-label required">Scheme</label>
                      <select class="form-control" name="select_scheme" id='select_scheme' required
                        onchange="selectChange();">
                        <option value="">--Select Scheme--</option>
                        @foreach($reports as $report)
                        <option value="{{$report->id}}">{{$report->scheme_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group col-md-2">
                      <label class=" control-label required">Financial Year</label>
                      <select class="form-control" name="lot_year" id='lot_year' required onchange="selectChange();">
                        <option value="">--Select Financial Year--</option>
                        @foreach(Config::get('constants.fin_year') as $year)
                        <option value="{{ $year}}">{{$year}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group col-md-2">
                      <label class=" control-label required">Month</label>
                      <select class="form-control" name="lot_month" id='lot_month' required onchange="selectChange();">
                        <option value="">--Select Month--</option>
                        @foreach(Config::get('constants.monthlist') as $key=> $month)
                        <option value="{{ $key}}">{{$month}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group col-md-3">
                      <label class=" control-label" style="
                      font-weight: bold;
                      margin-left: 5px;
                     
                      margin-top: 5px;">Lot Status</label>
                      <select class="form-control" name="lot_status" id='lot_status' required>
                        <option value="">--Select Status--</option>

                        <option value="0">Sign the Lot and Push to SBI</option>
                        <option value="5">Import SBI Payment status</option>
                      </select>
                    </div>

                    <div class="form-group col-md-2">
                      <label class=" control-label">&nbsp;</label>
                      <button class="btn btn-primary form-control" id="submit_btn" type="button"><i
                          class="fa fa-search"></i> Submit</button>
                    </div>
                  </div>
                </div>
                <!-- <div align="center"><img src="../images/ZKZg.gif" width="100px" id="loader_img"></div> -->
                <div id="res_div" style="display:none;">
                  <div class="panel panel-default">
                    <div class="panel-heading" id="panel_head" style="font-size: 16px; font-weight: bold;"></div>
                    <div class="panel-body" style="padding: 0px 0px 0px 0px;">
                      <div id="sbilot_data"></div>
                    </div>
                  </div>
                </div>

                <div class="text-primary"><b></b></div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- Main Content -->
    </div>
    @include('layouts.footer')
    <!-- /.content wrapper -->
  </div>

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


  <script>
    $(document).ready(function() {
      $('#loader_img').hide();
    
      $('#submit_btn').click(function(){
        let select_scheme=$('#select_scheme').val();
        let lot_year=$('#lot_year').val();
        let lot_month=$('#lot_month').val();
        let lot_status=$('#lot_status').val();
        if (select_scheme == '' || lot_year == '' || lot_month == '') {
          //alert('Please select all the fields');
          $.alert({
            title: 'Error!!',
            type: 'red',
            icon: 'fa fa-warning',
            content: '<strong>Please select all required (*)  fields</strong>',
          });
     
          return false;
        }
        else {
          var msg = 'Scheme : '+$( "#select_scheme option:selected" ).text()+' , Financial Year : '+lot_year+' , Month : '+$( "#lot_month option:selected" ).text();
          $('#loader_img').show();
          $('#res_div').hide();
          $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          });
          $.ajax({
            url: "{{ route('lot-master-sbi-list') }}",
            method: 'post',
            data: {
              select_scheme: select_scheme,
              lot_year: lot_year,
              lot_month:lot_month,
              lot_status:lot_status,
              _token:"{{csrf_token()}}"
            },
            success: function(result) {
            
              $('#loader_img').hide();
              $('#res_div').show();
              $('#sbilot_data').html('');
              $('#sbilot_data').html(result);
              $('#panel_head').text(msg);
            },
            error: function (jqXHR, textStatus, errorThrown) {
              $('#loader_img').hide();
              $('#res_div').show();
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          });
        }
      });
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

    function selectChange(){
      var record_status=$('#record_status').val();
     console.log(record_status);
      if(record_status!=undefined){
        $('#res_div').show();
          let table_data='';
          table_data +='<table class="table-responsive table table-bordered">';
            table_data +='<tr>';
              table_data +='<td style="font-weight:bold; font-size:15px; text-align:center;"> No record found.';
                table_data +='</td>';
              table_data +='</tr>';
            table_data +='</table>';
            
            $('#panel_head').html('');
          $('#sbilot_data').html('');
          $('#sbilot_data').html(table_data);
      }
      
    }
  </script>
  <!-- REQUIRED JS SCRIPTS -->

  <!-- Bootstrap 3.3.2 JS -->
  <script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript">
  </script>

  <!-- AdminLTE App -->
  <script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>

</body>

</html>