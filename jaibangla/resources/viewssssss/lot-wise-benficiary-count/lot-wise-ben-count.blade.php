
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



  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">

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
  <div class="preloader1"><img src="{{asset('images/ZKZg.gif')}}" width="100px" id="loader_img"></div>
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
          District Wise Lot Break Up
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> District Wise Beneficiary Count</a></li>
          <!-- <li class="active">Duplicate Approve</li> -->
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="box box-default">
          <!-- <div class="box-header with-border">
            <div class="row">
              <div class="col-sm-8">
                <h3 class="box-title">Lot Transaction IFMS</h3>
              </div>
            </div>
          </div> -->
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
                      <select class="form-control" name="select_scheme" id='select_scheme' required onchange="resetData();">
                        <option value="">--Select Scheme--</option>
                        @foreach($schemes as $scheme)
                        <option value="{{$scheme->id}}">{{$scheme->name}}</option>
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
                      <label class=" control-label multiple" style="font-weight: bold; margin-left: 5px; margin-top: 5px;">Lot No.</label>
                      <select class="form-control" name="lot_status" id='lot_status' required>
                        {{-- <option value="">--All--</option> --}}
                      
                      </select>
                    </div>

                    <div class="form-group col-md-2">
                      <label class=" control-label">&nbsp;</label>
                      <button class="btn btn-primary form-control" id="submit_btn" type="button"><i
                          class="fa fa-search"></i> Search</button>
                    </div>
                  </div>
                </div>
                <!-- <div align="center"><img src="../images/ZKZg.gif" width="100px" id="loader_img"></div> -->
                <div id="res_div" style="display:none;">
                  <div class="panel panel-default">
                    <div class="panel-heading" id="heading_msg"
                        style="font-size: 15px; font-weight: bold; font-style: italic;">Lot Wise Beneficiary Count</div>
                    <div class="panel-body" style="padding: 5px; font-size: 14px;">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered" cellspacing="0"
                                width="100%" style="font-size: 14px;">
                                <thead>
                                    <th>District Name</th>
                                    <th>Total Beneficiary</th>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                  <th></th>
                                  <th></th>
                                </tfoot>
                            </table>
                        </div>
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
          $('#res_div').show();
          if ($.fn.DataTable.isDataTable('#example')) {
                    $('#example').DataTable().destroy();
                }
                var table = $('#example').DataTable({
                    dom: 'Blfrtip',
                    "scrollX": false,
                    "paging": false,
                    "searchable": false,
                    "ordering": false,
                    "bFilter": false,
                    "bInfo": false,
                    "pageLength": 25,
                    'lengthMenu': [
                        [10, 20, 25, 50, 100, -1],
                        [10, 20, 25, 50, 100, 'All']
                    ],
                    "serverSide": true,
                    "processing": true,
                    "bRetrieve": true,
                    "oLanguage": {
                        "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
                    },
                    "ajax": {
                        url: "{{ route('lotWiseBeneficiaryCount') }}",
                        type: "post",
                        data: function(d) {
                            d.scheme_code = select_scheme,
                            d.lot_year = lot_year,
                            d.lot_month = lot_month,
                            d.lot_no = lot_status,
                            d._token = "{{ csrf_token() }}"
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $('#submit_btn').attr('disabled', false);
                            $('#loadingDiv').hide();
                            $('.preloader1').hide();
                            ajax_error(jqXHR, textStatus, errorThrown);
                        }
                    },
                    "initComplete": function() {
                        $('#loadingDiv').hide();
                        //console.log('Data rendered successfully');
                    },
                    "columns": [
                        {
                          "data": "district"
                        },
                        {
                          "data": "count"
                        },
                    ],
                    "buttons": [
                        {
                           extend: 'pdf',
                           footer: true,
                           pageSize:'A4',
                           //orientation: 'landscape',
                           pageMargins: [ 40, 60, 40, 60 ],
                           exportOptions: {
                                columns: [0,1],

                            }
                           },
                           {
                               extend: 'excel',
                               footer: true,
                               pageSize:'A4',
                               //orientation: 'landscape',
                               pageMargins: [ 40, 60, 40, 60 ],
                               exportOptions: {
                                    columns: [0,1],
                                    stripHtml: false,
                                }
                            },
                        // 'pdf'
                    ],
                    "footerCallback": function(row, data, start, end, display) {
                                var api = this.api(),
                                  data;
                          
                                // converting to interger to find total
                                var intVal = function(i) {
                                  return typeof i === 'string' ?
                                    i.replace(/[\$,]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                                };
                          
                                // computing column Total of the complete result 
                                var fotter_1 = api
                                  .column(1)
                                  .data()
                                  .reduce(function(a, b) {
                                    return intVal(a) + intVal(b);
                                  }, 0);                            
                                // Update footer by showing the total with the reference of the column index 
                                $(api.column(0).footer()).html('Total');
                                $(api.column(1).footer()).html(fotter_1);
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
      var scheme_id = $('#select_scheme').val();
      var fin_year = $('#lot_year').val();
      var month = $('#lot_month').val();
      if(fin_year != '' && month != ''){
        $.ajax({
            url: "{{ route('getLot') }}",
            method: 'post',
            data: {
              scheme_id: scheme_id,
              lot_year: fin_year,
              lot_month: month,
              _token:"{{csrf_token()}}"
            },
            success: function(result) {
              $('#lot_status').html('<option value="all"> All </option>');
              $.each(result, function (key, value) {
                $("#lot_status").append('<option value="' + value.lot_no + '">' + value.lot_no + '</option>');
              });

            },
            error: function (jqXHR, textStatus, errorThrown) {
              $('#loader_img').hide();
              $('#res_div').show();
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          });
      }
    }

    function resetData(){
      $('#lot_year').prop('selectedIndex',0);
      $('#lot_month').prop('selectedIndex',0);
      $('#lot_status').prop('selectedIndex',0);
    }
  </script>
  <!-- REQUIRED JS SCRIPTS -->

  <!-- Bootstrap 3.3.2 JS -->
  <script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript">
  </script>

  <!-- AdminLTE App -->
  <script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
  <script  src="{{ asset ("/bower_components/AdminLTE/moment/moment.js") }}" type="text/javascript" ></script>

</body>

</html>









