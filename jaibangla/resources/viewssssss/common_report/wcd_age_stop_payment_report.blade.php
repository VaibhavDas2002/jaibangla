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
  .loadingDivModal{
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
    <div class="preloader1" align="center"><img src="images/ZKZg.gif" width="100px"></div>
    
    <!-- Main Header -->
    @include('layouts.header')
    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Age Cohort and Stop Payment Report
        </h1>    
      </section>
  <!-- Main content -->
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
    @endif

    <div class="row">
      <div class="col-md-12">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_1" data-toggle="tab">Age cohort wise beneficiaries</a></li>
            <li><a href="#tab_2" data-toggle="tab">Death/Ineligible beneficiaries : Stop Payment Report</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
              <div class="row">
                  <div class="col-md-12">
                    <div class="form-group col-md-3">
                      <!-- <label class=" control-label">Select Scheme</label> -->
                      
                      <select class="form-control"  name="selectscheme" id='selectscheme'>
                        <option value="">--Select Scheme--</option>
                        @foreach($report as $re)
          
                        <option value="{{$re->id}}" >{{$re->scheme_name}}</option>
                        @endforeach
                              
                      </select>
                    </div>
                  <div class="form-group col-md-1">
                    <button class="btn btn-primary form-control" id="pendingsearch" type="button"><i class="fa fa-search"></i> Search</button>
                  </div>
                  <div class="form-group col-md-2">
                    <img src="images/ZKZg.gif" width="50px" style="display: none;" id="load1">
                  </div>
                </div>

                <div class="col-md-12">
                  <div id="tableAgeCohortDeActivate" style="display: none;">
                    
                  </div>
                </div>
              </div>
            </div>

            <!-- /.tab-pane -->
            <div class="tab-pane" id="tab_2">
              <div class="row">
                  <div class="col-md-12">
                    <div class="form-group col-md-3">
                      <!-- <label class=" control-label">Select Scheme</label> -->
                      
                      <select class="form-control"  name="selectscheme1" id='selectscheme1'>
                        <option value="">--Select Scheme--</option>
                        @foreach($report as $re)
          
                        <option value="{{$re->id}}" >{{$re->scheme_name}}</option>
                        @endforeach
                              
                      </select>
                    </div>
                  <div class="form-group col-md-1">
                    <button class="btn btn-primary form-control" id="stopReportsearch" type="button"><i class="fa fa-search"></i> Search</button>
                  </div>
                  <div class="form-group col-md-2">
                    <img src="images/ZKZg.gif" width="50px" style="display: none;" id="load2">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="table-responsive" id="stopPaymentTable" style="display: none;">
                    <table id="example2" class="table table-bordered table-condensed table-hover table-striped" cellspacing="0" width="100%">
                      <thead style="font-size: 12px;">
                        <tr>
                          <th>District</th>
                          <th>Total Beneficiary</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                      <tfoot>
                        <tr>
                          <th></th><th></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /.tab-content -->
        </div>
        <!-- nav-tabs-custom -->
      </div>
      <!-- /.col -->

    
      <!-- /.col -->
    </div>
    <!-- /.row -->
    <!-- END CUSTOM TABS -->

    
     
    </section>
   
    <!-- /.content -->
  </div>
<!-- </div> -->

@include('layouts.footer')
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
    $('.preloader1').hide();
    $('[data-toggle="tooltip"]').tooltip();
    var table='';
    $('#pendingsearch').click(function(){
      var selectscheme=$('#selectscheme').val();
      if(selectscheme==  "" ){
        $.alert({
          title: 'Error!!',
          type: 'red',
          icon: 'fa fa-warning',
          content: 'Please select scheme',
        });
      return false;
      }
      else{
        $('#tableAgeCohortDeActivate').hide();
        $('#load1').css('display','');
        $.ajax({
          url: "{{ route('wcdAgeDiffStopPaymentGetData') }}",
          method: 'post',
          data: {
            selectscheme: selectscheme,
            _token:"{{csrf_token()}}"
          },
          success: function(result) {
            $('#load1').hide();
            $('#tableAgeCohortDeActivate').show();
            $('#tableAgeCohortDeActivate').html('');
            $('#tableAgeCohortDeActivate').html(result);
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $('#load1').hide();
            ajax_error(jqXHR, textStatus, errorThrown);
          }
        });
      }   
    });

    $('#stopReportsearch').click(function(){
      var selectscheme1=$('#selectscheme1').val();
      if(selectscheme1==  "" ){
        $.alert({
          title: 'Error!!',
          type: 'red',
          icon: 'fa fa-warning',
          content: 'Please select scheme',
        });
      return false;
      }
      else{
        $('#load2').css('display','');
        if ( $.fn.DataTable.isDataTable('#example2') ) {
          $('#example2').DataTable().destroy();
         }
        //$('#example').empty();
        var table=$('#example2').DataTable( {
          dom: 'Bfrtip',
          "scrollX": false,
          "paging": false,
          "searchable": false,
          "bFilter": false,
          "bInfo": false,
          "ordering": false,
          "pageLength":30,
          'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          // "oLanguage": {
          //   "sProcessing": '<div id="preloader1" align="center"><img src="images/ZKZg.gif" width="100px"></div>'
          // },
          "ajax": 
          {
            url: "{{ url('wcdStopPaymentReport') }}",
            type: "post",
            data:function(d){
              d.selectscheme1= $('#selectscheme1').val(),
              d._token= "{{csrf_token()}}"
            },
            error: function (jqXHR, textStatus, errorThrown) {
              $('#load2').css('display','none');
              $('#stopPaymentTable').show();
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete":function(){
            console.log('Data rendered successfully');
            $('#load2').css('display','none');
            $('#stopPaymentTable').show();
          },
          "columns": [
            { "data": "district_name" },
            { "data": "total" },
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
            total_1 = api
              .column( 1, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
            }, 0 );
      
            // Update footer
            $( api.column( 0 ).footer() ).html(
              "Total: "
            );
            $( api.column( 1 ).footer() ).html(
              total_1
            );
          },     
      
          "buttons": [
            { extend: 'excelHtml5', footer: true, title : 'Death/in-aligible stop payment' },
            { extend: 'pdfHtml5', footer: true,  title : 'Death/in-aligible stop payment' }
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
  function redirectPostExcel(url, data , method = 'post'){
    var form = document.createElement('form');
    form.method = method;
    form.action = url;
    for (var name in data) {
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = name;
      input.value = data[name];
      form.appendChild(input);
    }
    $('body').append(form);
    form.submit();
  }
  
</script>
<!-- REQUIRED JS SCRIPTS -->

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>

</body>
</html>