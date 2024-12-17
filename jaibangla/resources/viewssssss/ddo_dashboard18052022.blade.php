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
          Dashboard
          <div style="float: right;">
            @if(Auth::user()->id == 6193)
            <button id="deDuplicateBank" class="btn btn-danger btn-sm" style="margin-left: 5px;" data-toggle="tooltip" data-placement="bottom" title="De-duplication will be done on bank a/c & ifsc and payment will be stopped for all duplicate beneficiaries."><i class="fa fa-bank"></i> De-duplicate Bank Info</button>
            @endif
            @if(Auth::user()->id == 5994)
            <button id="getApprovedList" class="btn btn-success btn-sm" style="margin-left: 5px;"data-toggle="tooltip" data-placement="bottom" title="Get district wise total approved beneficiary."><i class="fa fa-users"></i> Current Approved List</button>
            @endif
          </div>
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
            <li class="active"><a href="#tab_1" data-toggle="tab"><span style="font-weight: bold; font-style: italic;" class="text-primary">Pending Beneficiary</span></a></li>

            @if(Auth::user()->id == 6193 || Auth::user()->id == 5994 || Auth::user()->id == 7261)
            {{-- <li><a href="#tab_5" data-toggle="tab"><span style="font-weight: bold; font-style: italic;" class="text-success">Standard Lot Pending</span></a></li> --}}
            @else
            <li><a href="#tab_2" data-toggle="tab"><span style="font-weight: bold; font-style: italic;" class="text-success">Repeat Lot Pending</span></a></li>
            @endif

            <li><a href="#tab_3" data-toggle="tab"><span style="font-weight: bold; font-style: italic;">Import IFMS Report Pending @foreach($ifms as $i)</span> <span class="label label-warning">{{$i->total}}</span>@endforeach</a></li>
            <li><a href="#tab_4" data-toggle="tab"><span style="font-weight: bold; font-style: italic;">Import SBI Report Pending</span> <span class="label label-warning">{{$sbi}}</span></a></li>
            {{-- <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                Dropdown <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Action</a></li>
                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Another action</a></li>
                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Something else here</a></li>
                <li role="presentation" class="divider"></li>
                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Separated link</a></li>
              </ul>
            </li>
            <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li> --}}
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
                    {{-- <div class="form-group col-md-3">
                    <label class=" control-label">Select Financial Year</label>
                 
                    
                    
                    <select class="form-control select2 full-width js-reportlevel1a"  name="selectyear" id='selectyear'>
                      <option value="">--Select Financial Year--</option>
                      @foreach(Config::get('constants.financialyear') as $year=>$val)
                      <option value="{{ $year}}">{{$val}}</option>
                      @endforeach
                            
                    </select>
                  </div> --}}
                  <div class="form-group col-md-2">
                    <button class="btn btn-primary form-control" id="pendingsearch" type="button"><i class="fa fa-search"></i> Search</button>
                  </div>
                  <div class="form-group col-md-2">
                    <img src="images/ZKZg.gif" width="50px" style="display: none;" id="load1">
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="table-responsive" id="tablePendingBeneficiary">
                    {{-- <table id="example" class="table table-bordered display compact" cellspacing="0" width="100%">
                      <thead>
                        <tr role="row">
                          <th width="15%">Month-Year</th>
                          <th width="10%">Fresh</th>
                          <th width="10%">Fresh Resumed</th>
                          <th width="10%">Adjustment</th>
                          <th width="10%">IFMS Error</th>
                          <th width="10%">RBI Error</th>
                          <th width="10%">SBI Error</th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
                      </tfoot>       
                    </table> --}}
                  </div>
                </div>
              </div>
            </div>

            <!-- Standard Lot -->
            <div class="tab-pane" id="tab_5">
              <div class="row">
                  <div class="col-md-12">
                    <div class="form-group col-md-3">
                      <!-- <label class=" control-label">Select Scheme</label> -->
                      
                    <select class="form-control"  name="sd_scheme" id='sd_scheme'>
                      <option value="">--Select Scheme--</option>
                      @foreach($report as $re)
        
                      <option value="{{$re->id}}" >{{$re->scheme_name}}</option>
                      @endforeach
                            
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <button class="btn btn-primary form-control" id="pendingStandardsearch" type="button"><i class="fa fa-search"></i> Search</button>
                  </div>
                  <div class="form-group col-md-2">
                    <img src="images/ZKZg.gif" width="50px" style="display: none;" id="load5">
                  </div>
                </div>

                <div class="col-md-12">
                  <!-- <div class="table-responsive"> -->
                    <table id="exampleStandard" class="table table-bordered display compact" cellspacing="0" width="100%">
                      <thead>
                        <tr role="row">
                          <th width="15%">Month-Year</th>
                          <th width="10%">Standard</th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr><th></th><th></th></tr>
                      </tfoot>       
                    </table>
                  <!-- </div> -->
                </div>
              </div>
            </div>

            <!-- /.tab-pane -->
            <div class="tab-pane" id="tab_2">
              <div class="row">
                  <div class="col-md-12">
                    <div class="form-group col-md-3">
                      <!-- <label class=" control-label">Select Financial Year</label> -->
                      <select class="form-control"  name="repeat_year" id='repeat_year'>
                        <option value="">--Select Financial Year--</option>
                        @foreach(Config::get('constants.fin_year') as $year=>$val)
                        <option value="{{ $year}}">{{$val}}</option>
                        @endforeach
                              
                      </select>
                    </div>
                    <div class="form-group col-md-3">
                    <!-- <label class=" control-label">Select Month</label> -->
                    <select class="form-control"  name="repeat_month" id='repeat_month'>
                      <option value="">--Select Month--</option>
                      @foreach(Config::get('constants.monthlist') as $month=>$val)
                      <option value="{{ $month}}">{{$val}}</option>
                      @endforeach
                            
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <!-- <label class=" control-label">&nbsp;</label> -->
                    <button class="btn btn-primary form-control" id="pendingrepeatsearch" type="button"><i class="fa fa-search"></i> Search</button>
                  </div>
                  <div class="form-group col-md-2">
                    <img src="images/ZKZg.gif" width="50px" style="display: none;" id="load2">
                  </div>
                </div>
                <div class="col-md-12">
                  <!-- <div class="table-responsive"> -->
                    <table id="exampleRepeat" class="table table-bordered table-condensed table-hover display compact" cellspacing="0" width="100%">
                      <thead>
                        <tr role="row" class="sorting_asc">
                          <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Scheme Name</th>
                          <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Month</th>
                          <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Year</th>
                          <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Lot No</th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr><th></th><th></th><th></th><th></th></tr>
                      </tfoot>       
                    </table>
                  <!-- </div> -->
                </div>
              </div>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="tab_3">
              <div class="row">
                  <div class="col-md-12">
                    <div class="form-group col-md-3">
                      <!-- <label class=" control-label">Select Financial Year</label> -->
                      <select class="form-control"  name="ifms_scheme" id='ifms_scheme'>
                        <option value="">--Select Scheme--</option>
                        @foreach($report as $r)
          
                        <option value="{{$r->id}}" >{{$r->scheme_name}}</option>
                        @endforeach
                              
                      </select>
                    </div>
                    <div class="form-group col-md-3">
                      <!-- <label class=" control-label">Select Financial Year</label> -->
                      <select class="form-control"  name="ifms_year" id='ifms_year'>
                        <option value="">--Select Financial Year--</option>
                        @foreach(Config::get('constants.fin_year') as $year=>$val)
                        <option value="{{ $year}}">{{$val}}</option>
                        @endforeach
                              
                      </select>
                    </div>
                    <div class="form-group col-md-3">
                    <!-- <label class=" control-label">Select Month</label> -->
                    <select class="form-control"  name="ifms_month" id='ifms_month'>
                      <option value="">--Select Month--</option>
                      @foreach(Config::get('constants.monthlist') as $month=>$val)
                      <option value="{{ $month}}">{{$val}}</option>
                      @endforeach
                            
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <!-- <label class=" control-label">&nbsp;</label> -->
                    <button class="btn btn-primary form-control" id="pendingifmsearch" type="button"><i class="fa fa-search"></i> Search</button>
                  </div>
                  <div class="form-group col-md-1">
                    <img src="images/ZKZg.gif" width="50px" style="display: none;" id="load4">
                  </div>
                </div>
                <div class="col-md-12">
                  <!-- <div class="table-responsive"> -->
                    <table id="exampleIfms" class="table table-bordered table-condensed table-hover display compact" cellspacing="0" width="100%">
                      <thead>
                        <tr role="row" class="sorting_asc">
                          <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Scheme Name</th>
                          <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Month</th>
                          <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Year</th>
                          <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No.of Lot</th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr><th></th><th></th><th></th><th></th></tr>
                      </tfoot>       
                    </table>
                  <!-- </div> -->
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab_4">
              <div class="row">
                  <div class="col-md-12">
                    <div class="form-group col-md-3">
                      <!-- <label class=" control-label">Select Financial Year</label> -->
                      <select class="form-control"  name="sbi_scheme" id='sbi_scheme'>
                        <option value="">--Select Scheme--</option>
                        @foreach($report as $r)
          
                        <option value="{{$r->id}}" >{{$r->scheme_name}}</option>
                        @endforeach
                              
                      </select>
                    </div>
                    <div class="form-group col-md-3">
                      <!-- <label class=" control-label">Select Financial Year</label> -->
                      <select class="form-control"  name="sbi_year" id='sbi_year'>
                        <option value="">--Select Financial Year--</option>
                        @foreach(Config::get('constants.fin_year') as $year=>$val)
                        <option value="{{ $year}}">{{$val}}</option>
                        @endforeach
                              
                      </select>
                    </div>
                    <div class="form-group col-md-3">
                    <!-- <label class=" control-label">Select Month</label> -->
                    <select class="form-control"  name="sbi_month" id='sbi_month'>
                      <option value="">--Select Month--</option>
                      @foreach(Config::get('constants.monthlist') as $month=>$val)
                      <option value="{{ $month}}">{{$val}}</option>
                      @endforeach
                            
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <!-- <label class=" control-label">&nbsp;</label> -->
                    <button class="btn btn-primary form-control" id="pendingsbisearch" type="button"><i class="fa fa-search"></i> Search</button>
                  </div>
                  <div class="form-group col-md-1">
                    <img src="images/ZKZg.gif" width="50px" style="display: none;" id="load3">
                  </div>
                </div>
                <div class="col-md-12">
                  <!-- <div class="table-responsive"> -->
                    <table id="exampleSbi" class="table table-bordered table-condensed table-hover display compact" cellspacing="0" width="100%">
                      <thead>
                        <tr role="row" class="sorting_asc">
                          <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Scheme Name</th>
                          <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Month</th>
                          <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending">Year</th>
                          <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No.of Lot</th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr><th></th><th></th><th></th><th></th></tr>
                      </tfoot>       
                    </table>
                  <!-- </div> -->
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

    
      <!-- /.col -->
    </div>
    <!-- /.row -->
    <!-- END CUSTOM TABS -->

    
     
    </section>
   
    <!-- /.content -->
  </div>
<!-- </div> -->

<!-- Modal -->
<div class="modal fade" id="modalDeDuplicateBank" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Mark Duplicate Bank Account</h4>
      </div>
      <div class="modal-body">
        <div class="loadingDivModal"></div>
        <div id="duplicate_info"></div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

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
        $('#load1').css('display','');
        var selectscheme=$('#selectscheme').val();
        var selectyear=$('#selectyear').val();
        $('#tablePendingBeneficiary').html('');
        $.ajax({
          url: "{{ url('getBeneficiaryPaymentPending') }}",
          type: "POST",
          data: { selectscheme:selectscheme, selectyear:selectyear, _token:"{{csrf_token()}}" }, 
          dataType: 'json',
          success:function(response){
            $('#load1').css('display','none');
            $('#tablePendingBeneficiary').html('');
            $('#tablePendingBeneficiary').html(response.html);
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $('#load1').css('display','none');
            ajax_error(jqXHR, textStatus, errorThrown);
          }
        });
      }   
    });

    $('#pendingrepeatsearch').click(function(){
      var repeat_year=$('#repeat_year').val();
      if(repeat_year==  "" ){
        $.alert({
          title: 'Error!!',
          type: 'red',
          icon: 'fa fa-warning',
          content: 'Please select financial year',
        });
      return false;
      }
      else {
        $('#load2').css('display','');
        if ( $.fn.DataTable.isDataTable('#exampleRepeat') ) {
          $('#exampleRepeat').DataTable().destroy();
         }
        //$('#example').empty();
        var table=$('#exampleRepeat').DataTable( {
          dom: 'Bfrtip',
          "scrollX": false,
          "paging": false,
          "searchable": false,
          "bFilter": false,
          "bInfo": false,
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
            url: "{{ url('getRepeatPending') }}",
            type: "post",
            data:function(d){
              d.repeat_year= $('#repeat_year').val(),
              d.repeat_month=$('#repeat_month').val(),
              d._token= "{{csrf_token()}}"
            },
            error: function (jqXHR, textStatus, errorThrown) {
              $('#load2').css('display','none');
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete":function(){
            console.log('Data rendered successfully');
            $('#load2').css('display','none');
          },
          "columns": [
            { "data": "scheme_name","defaultContent":"Null" },
            { "data": "lot_month","defaultContent":"Null" },
            { "data": "lot_year","defaultContent":"Null" },
            { "data": "lot_no","defaultContent":"Null" },
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
            var lot_no = api.column( 1 ).data();
      
            // Update footer
            $( api.column( 0 ).footer() ).html(
              "Total: "
            );
            $( api.column( 1 ).footer() ).html(
              ""
            );
            $( api.column( 2 ).footer() ).html(
              ""
            );
            $( api.column( 3 ).footer() ).html(
              'No. of Lot: '+lot_no.count()
            );
          },     
      
          "buttons": [
            //'pdf','excel','print'
          ],
        });
      }   
    });

    $('#pendingsbisearch').click(function(){
      var sbi_scheme=$('#sbi_scheme').val();
      if(sbi_scheme==  "" ){
        $.alert({
          title: 'Error!!',
          type: 'red',
          icon: 'fa fa-warning',
          content: 'Please select scheme',
        });
      return false;
      }
      else {
        $('#load3').css('display','');
        if ( $.fn.DataTable.isDataTable('#exampleSbi') ) {
          $('#exampleSbi').DataTable().destroy();
         }
        //$('#example').empty();
        var table=$('#exampleSbi').DataTable( {
          dom: 'Bfrtip',
          "scrollX": false,
          "paging": false,
          "searchable": false,
          "bFilter": false,
          "bInfo": false,
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
            url: "{{ url('import_sbi_pending') }}",
            type: "post",
            data:function(d){
              d.sbi_scheme = $('#sbi_scheme').val(),
              d.sbi_year= $('#sbi_year').val(),
              d.sbi_month=$('#sbi_month').val(),
              d._token= "{{csrf_token()}}"
            },
            error: function (jqXHR, textStatus, errorThrown) {
              $('#load3').css('display','none');
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete":function(){
            console.log('Data rendered successfully');
            $('#load3').css('display','none');
          },
          "columns": [
            { "data": "scheme_name","defaultContent":"Null" },
            { "data": "lot_month","defaultContent":"Null" },
            { "data": "lot_year","defaultContent":"Null" },
            { "data": "total_lot","defaultContent":"Null" },
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
            total_lot = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
            }, 0 );
      
            // Update footer
            $( api.column( 0 ).footer() ).html(
              "Total: "
            );
            $( api.column( 1 ).footer() ).html(
              ""
            );
            $( api.column( 2 ).footer() ).html(
              ""
            );
            $( api.column( 3 ).footer() ).html(
              total_lot
            );
          },     
      
          "buttons": [
            //'pdf','excel','print'
          ],
        });
      }   
    });

    $('#pendingifmsearch').click(function(){
      var ifms_scheme=$('#ifms_scheme').val();
      if(ifms_scheme==  "" ){
        $.alert({
          title: 'Error!!',
          type: 'red',
          icon: 'fa fa-warning',
          content: 'Please select scheme',
        });
      return false;
      }
      else {
        $('#load4').css('display','');
        if ( $.fn.DataTable.isDataTable('#exampleIfms') ) {
          $('#exampleIfms').DataTable().destroy();
         }
        //$('#example').empty();
        var table=$('#exampleIfms').DataTable( {
          dom: 'Bfrtip',
          "scrollX": false,
          "paging": false,
          "searchable": false,
          "bFilter": false,
          "bInfo": false,
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
            url: "{{ url('import_rbi_report_pending') }}",
            type: "post",
            data:function(d){
              d.ifms_scheme = $('#ifms_scheme').val(),
              d.ifms_year= $('#ifms_year').val(),
              d.ifms_month=$('#ifms_month').val(),
              d._token= "{{csrf_token()}}"
            },
            error: function (jqXHR, textStatus, errorThrown) {
              $('#load4').css('display','none');
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete":function(){
            console.log('Data rendered successfully');
            $('#load4').css('display','none');
          },
          "columns": [
            { "data": "scheme_name","defaultContent":"Null" },
            { "data": "lot_month","defaultContent":"Null" },
            { "data": "lot_year","defaultContent":"Null" },
            { "data": "total_lot","defaultContent":"Null" },
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
            total_lot = api
              .column( 3, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
            }, 0 );
      
            // Update footer
            $( api.column( 0 ).footer() ).html(
              "Total: "
            );
            $( api.column( 1 ).footer() ).html(
              ""
            );
            $( api.column( 2 ).footer() ).html(
              ""
            );
            $( api.column( 3 ).footer() ).html(
              total_lot
            );
          },     
      
          "buttons": [
            //'pdf','excel','print'
          ],
        });
      }   
    });

    // Standard Lot Pending
    $('#pendingStandardsearch').click(function(){
      var sd_scheme=$('#sd_scheme').val();
      if(sd_scheme==  "" ){
        $.alert({
          title: 'Error!!',
          type: 'red',
          icon: 'fa fa-warning',
          content: 'Please select scheme',
        });
      return false;
      }
      else{
        $('#load5').css('display','');
        var sd_scheme=$('#sd_scheme').val();
        
        if ( $.fn.DataTable.isDataTable('#exampleStandard') ) {
          $('#exampleStandard').DataTable().destroy();
         }
        //$('#example').empty();
        var table=$('#exampleStandard').DataTable( {
          dom: 'Bfrtip',
          "scrollX": false,
          "paging": false,
          "searchable": false,
          "bFilter": false,
          "bInfo": false,
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
            url: "{{ url('getStandardLotPending') }}",
            type: "post",
            data:function(d){
              d.sd_scheme= $('#sd_scheme').val(),
              d._token= "{{csrf_token()}}"
            },
            error: function (jqXHR, textStatus, errorThrown) {
              $('#load5').css('display','none');
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete":function(){
            console.log('Data rendered successfully');
            $('#load5').css('display','none');
          },
          "columns": [
            { "data": "year_month","defaultContent":"Null", orderable: false},
            { "data": "pending_sd","defaultContent":"0", orderable: false}
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
            pending_sd = api
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
              pending_sd
            );
        },     
      
          "buttons": [
            //'pdf','excel','print'
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

    $('#deDuplicateBank').click(function(){
      $('.preloader1').show();
      $.ajax({
        type: 'POST',
        url: "{{ route('markDuplicateAccountNumber') }}",
        data: { _token: '{{ csrf_token() }}' },
        success: function (response) {
          $('.preloader1').hide();
          // console.log(response);
          if (response.status == 1) {
            var html = '';
            html += '<table class="table table-bordered table-condensed table-responsive table-striped" style="font-size: 14px;"><tr><td><b>Scheme </b></td><td><b>Action</b></td></tr>';
            for (var  i = 0; i < response.datas.length; i++) {
              html += '<tr><td>'+response.datas[i].scheme_name+'</td><td><button class="btn btn-warning btn-sm markDuplicateBtn" value="'+response.datas[i].scheme_id+'"><i class="fa fa-edit"></i> De-duplicate</button></td></tr>';
            }
            html += '</table>';
            $('#duplicate_info').html('');
            $('#duplicate_info').html(html);
            $('.loadingDivModal').hide();
            $('#modalDeDuplicateBank').modal('show');
          }
          else {
            $.alert({
              title: response.title,
              type: response.type,
              icon: response.icon,
              content: response.msg
            });
          }
        },
        complete: function(){
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $('.preloader1').hide();
          ajax_error(jqXHR, textStatus, errorThrown); 
        }
      });
    });

    $(document).on('click', '.markDuplicateBtn', function(){
      var schemeId = $(this).val();
      $('.loadingDivModal').show();
      $.ajax({
        type: 'POST',
        url: "{{ route('markDeDuplicateSchemeWise') }}",
        data: { scheme_id: schemeId, _token: '{{ csrf_token() }}' },
        success: function (response) {
          $('.loadingDivModal').hide();
          $('#modalDeDuplicateBank').modal('hide');
          $.alert({
            title: response.title,
            type: response.type,
            icon: response.icon,
            content: response.msg
          });
        },
        complete: function(){
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $('.loadingDivModal').hide();
          ajax_error(jqXHR, textStatus, errorThrown); 
        }
      });
    });
    $(document).on('click', '#getApprovedList', function(){
      var  data= { '_token': '{{csrf_token()}}' };
      redirectPostExcel('{{route("getLppApprovedCount")}}', data, 'post');
    });
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