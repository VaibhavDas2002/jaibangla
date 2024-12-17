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
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet"
    type="text/css" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- DataTables -->
    <link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset("/css/buttons.dataTables.min.css")}}">
    <link href="{{ asset('css/jquery-confirm.min.css') }}" rel="stylesheet">
    <!-- Theme style -->
  <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
  <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet"
    type="text/css" />

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<style>
#loadingDiv{
  position:absolute;
  top:0px;
  right:0px;
  width:100%;
  height:100%;
  background-color:#fff;
  background-image:url('images/ajaxgif.gif');
  background-repeat:no-repeat;
  background-position:center;
  z-index:10000000;
  opacity: 0.4;
  filter: alpha(opacity=40); /* For IE8 and earlier */
}

.dataTables_wrapper .dataTables_filter {
float: right;
text-align: right;
visibility: hidden;
}
table.dataTable thead .sorting_asc:after {
    content: "" !important;
}
thead .sorting:after {
    opacity: 0.2;
    content: "" !important;
}
   </style>
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
          Dashboard
        </h1>



        <ol class="breadcrumb">
          <!-- li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li-->
          <li class="active">Dashboard</li>
        </ol>
      </section>
      <div class="col-md-12">
        @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{{ $message }}</strong>
        </div>
        @endif
        @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{{ $message }}</strong>
        </div>
        @endif
      </div>
      <!-- Main content -->
      <section class="content">
        <div class="row">
         {{csrf_field()}}
        <div class="form-group col-md-3">
          <label class=" control-label">Select Scheme</label>
       
            @php
            $user_id = Auth::user()->id;
            $report = DB::select(DB::raw("select id,scheme_name from m_scheme where id in (select scheme_id from duty_assignement where user_id=" . $user_id . " and is_active=1)"));
            @endphp
          
          <select class="form-control select2 full-width js-reportlevel1a"  name="selectscheme" id='selectscheme'>
            <option value=" ">--Select Scheme--</option>
            @foreach($report as $report)

            <option value="{{$report->id}}" >{{$report->scheme_name}}</option>
            @endforeach
                  
          </select>
      </div>
      {{-- <div class="form-group col-md-3">
        <label class=" control-label">Select Financial Year</label>
     
        
        
        <select class="form-control select2 full-width js-reportlevel1a"  name="selectyear" id='selectyear'>
          <option value=" ">--Select Financial Year--</option>
          @foreach(Config::get('constants.financialyear') as $year)
          <option value="{{ $year}}">{{$year}}</option>
          @endforeach
                
        </select>
    </div> --}}
    <div class="form-group col-md-2">
      <label class=" control-label">&nbsp;</label>
   
      <button class="btn btn-primary form-control" id="pendingsearch">Search</button>
      
    
  </div>
          <div class="col-xs-12">
           


            <div class="box">
              <div class="box-header">
                <h3 class="box-title">Payment Pending</h3>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Sl No</th>
                      <th>Year-Month</th>
                      {{-- <th>Repeat </th> --}}
                      <th>Fresh </th>
                      <th>Adjustment </th>
                      <th>IFMS Error</th>
                      <th>RBI Error</th>
                      <th>SBI Error</th>
                    </tr>
                  
                </thead>
                    
                   
                 
                  <tbody>
                    <div id="loadingDiv"></div>
                   
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Sl No</th>
                      <th>Year-Month</th>
                      {{-- <th>Repeat </th> --}}
                      <th>Fresh </th>
                      <th>Adjustment </th>
                      <th>IFMS Error</th>
                      <th>RBI Error</th>
                      <th>SBI Error</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
            <!-- /.content -->
          </div>
        </div>
      </section>
      <!-- /.content-wrapper -->
    </div>
      <!-- Footer -->
      @include('layouts.footer')

      <!-- ./wrapper -->

      <!-- REQUIRED JS SCRIPTS -->

      <!-- jQuery 2.1.3 -->
      <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>

      <!-- Bootstrap 3.3.2 JS -->
      <script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript">
      </script>


  <!-- DataTables -->
  <script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js") }}" type="text/javascript" ></script>
  <script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js") }}" type="text/javascript" ></script>
  <script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
  
  <!-- AdminLTE App -->
      <script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>

      <!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->

     <script>
      $(function () {
        $("#loadingDiv").hide();
        $('#pendingsearch').click(function(){
          var selectscheme=$('#selectscheme').val();
      
          if(selectscheme==  " " ){
            $.alert({
                        title: 'Error!!',
                        type: 'red',
                        icon: 'fa fa-warning',
                        content: 'Please select scheme',
                    });

                    return false;
          }
          else{
            getPaymentPending();
          }
         
        });
        var table = "";
         table = $('#example1').DataTable();
       
      });

      

    function getPaymentPending(){
      //$("#loadingDiv").show();
     $('#pendingsearch').attr('disabled',true);
      $("#example1").dataTable().fnDestroy();
      var selectscheme=$('#selectscheme').val();
          var selectyear=$('#selectyear').val();
          //$("#example1").DataTable().fnDestroy();
        table = $('#example1').DataTable({
          "paging": true,
                    "lengthChange": true,
                    "searching": false,
                    
                    
                    "processing": true,
                     "serverSide": true,
                           
            "ajax": {
                url: "{{route('getPaymentPending')}}",
                type: "post",
                data: {'selectscheme': selectscheme,'selectyear':selectyear, '_token': $('input[name="_token"]').val()},
                dataSrc: "record_details"
            },
            "dataType": 'json',
            "columnDefs":
                    [
                        {className: "table-text", "targets": "_all"},
                        {
                            "targets": 0,
                            "data": "code",
                            "defaultContent": "",
                        },
                        {
                            "targets": 1,
                            "data": "Year",
                            'orderable': false
                            
                        },
                        {
                            "targets": 2,
                            "data": "pending_fresh_lot_ben_count",
                            
                            
                        },

                        {
                            "targets": 3,
                            "data": "pending_adj_lot_ben_count",
                            
                            
                        },
                        
                        {
                            "targets": 4,
                            "data": "pending_ifms_err_lot_ben_count",
                            
                            
                        },
                        
                        {
                            "targets": 5,
                            "data": "pending_rbi_err_lot_ben_count",
                            
                            
                        },
                        
                        {
                            "targets": 6,
                            "data": "pending_sbi_err_lot_ben_count",
                            
                            
                        }
                    ],

            "order": [[1, 'asc']],
            "footerCallback": function ( row, data, start, end, display ) {
              $('#pendingsearch').removeAttr('disabled',false);
          var api = this.api(), data;
          // Remove the formatting to get integer data for summation
          var intVal = function ( i ) {
          return typeof i === 'string' ?
          i.replace(/[\$,]/g, '')*1 :
          typeof i === 'number' ?
          i : 0;
          };
     
          var col_1 = api
                            .column( 1 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                    
                  var col_2 = api
                            .column( 2 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                  var col_3 = api
                            .column( 3 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                    
                  var col_4 = api
                            .column( 4 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                  
                 var col_5 = api
                            .column( 5 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                  var col_6 = api
                            .column( 6 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 ); 


                        $( api.column( 0 ).footer() ).html('Total');
                        $( api.column( 1 ).footer() ).html(col_1);
                        $( api.column( 2 ).footer() ).html(col_2);
                        $( api.column( 3 ).footer() ).html(col_3);
                        $( api.column( 4 ).footer() ).html(col_4);
                        $( api.column( 5 ).footer() ).html(col_5);
                        $( api.column( 6 ).footer() ).html(col_6);
          }
        });
        table.on('order.dt search.dt draw.dt', function () {
            table.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
                cell.innerHTML = table.page() * table.page.len() + (i + 1);
            });
        });
        
    }
    </script>

   
</body>

</html>