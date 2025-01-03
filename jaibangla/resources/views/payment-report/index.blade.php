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
  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
     <link href="{{ asset("css/select2.min.css") }}" rel="stylesheet">

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

   <!-- bootstrap wysihtml5 - text editor -->
  <!-- <link rel="stylesheet" href="{{ asset("/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}"> -->

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />

  <style>
  .box
  {
   width:800px;
   margin:0 auto;
  }
  .active_tab1
  {
   background-color:#fff;
   color:#333;
   font-weight: 600;
  }
  .inactive_tab1
  {
   background-color: #f5f5f5;
   color: #333;
   cursor: not-allowed;
  }
  .has-error
  {
   border-color:#cc0000;
   background-color:#ffff99;
  }
  .select2{
    width:100%!important;
  }
  .select2 .has-error {
    border-color:#cc0000;
   background-color:#ffff99;
}
.modal_field_name{
  float:left;
  font-weight: 700;
  margin-right:1%;
  padding-top:1%;
  margin-top:1%;
}

.modal_field_value{
  margin-right:1%;
  padding-top:1%;
  margin-top:1%;
}
.row{
  margin-right: 0px!important;
  margin-left: 0px!important;
  margin-top: 1%!important;
}

.section1{
    border: 1.5px solid #9187878c;
    margin: 2%;
    padding: 2%;
}
.color1{
  margin: 0%!important;
  background-color: #5f9ea061;
}

.modal-header{
  background-color: #7fffd4;
}
.required-field::after {
    content: "*";
    color: red;
}
 .imageSize{
  font-size: 9px;
  color: #333;
 }
 #divScrool {
overflow-x: scroll;
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
  <section class="content">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div> <!-- class="box box-primary" -->
           

            <div>
             @if ( ($message = Session::get('success')) && ($id =Session::get('id')))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }} with Application ID: {{$id}}</strong>
               
               
              </div>
              @endif
               @if ($message = Session::get('error') )
              <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
               
               
              </div>
              @endif
            @if(count($errors) > 0)
            <div class="alert alert-danger alert-block">
              <ul>
               @foreach($errors->all() as $error)
               <li><strong> {{ $error }}</strong></li>
               @endforeach
              </ul>
            </div>
            @endif
             <!--   @if ($message = Session::get('failure'))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
              </div>
              @endif -->
            </div>
            <!-- /.box-header -->
            <!-- form start -->
        

     


            <div class="tab-content" style="margin-top:16px;">

              




             <div class="tab-pane active" id="personal_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Payment Report</b></h4></div>
               <div class="panel-body">

               

               <div class="row">

                <div class="form-group col-md-3">
                    <label class="required-field">Payment Mode</label>
                    <select class="form-control select2" name="mode" id='mode' required >
                        <option value="">--Select Mode--</option>
                        <option value="SBI">SBI</option>
                        <option value="IFMS">IFMS</option>
                      </select> 
                    <span id="error_payment_mode" class="text-danger"></span>
   
                   </div>
              
                
               
               <div class="form-group col-md-3" id='select_scheme'>
                 <label class="required-field">Scheme</label>
                 <select name="scheme_id" id="scheme_id" class="form-control" tabindex="6" >
                  <option value="">--Please Select--</option>
                   @foreach ($schemes as $scheme)
                  <option value="{{$scheme->id}}"> {{$scheme->scheme_name}}</option>
                  @endforeach
                </select>
                 <span id="error_scheme_id" class="text-danger"></span>

                </div>


                <div class="form-group col-md-3" id='Fin_yr'>
                  <label class="required-field">Payment of which year</label>
                <!--  <div class=""> -->
                  <select class="form-control  full-width js-reportlevel1a"  name="fin_year" id='fin_year'>
                       
                  @foreach(Config::get('constants.fin_year') as $key=>$val)
                      <option value="{{$key}}">{{$val}}</option>
                      @endforeach
                      <!-- <option value="State">State</option> -->
                      
                          
                  </select>
                  <span id="error_fin_year" class="text-danger"></span>
              </div>

              <div class="form-group col-md-3" id='Fin_Month'>
                <label class="required-field">Payment of which month</label>
              <!--  <div class=""> -->
                <select class="form-control  full-width js-reportlevel1a"  name="fin_month" id='fin_month'>
                     
                @foreach(Config::get('constants.monthlist') as $key=>$val)
                    <option value="{{$key}}">{{$val}}</option>
                    @endforeach
                    <!-- <option value="State">State</option> -->
                    
                        
                </select>
                <span id="error_fin_month" class="text-danger"></span>
            </div>

            <div class="form-group col-md-3" id='Lot_yr'>
              <label class="required-field">Payment lot pushed on year</label>
            <!--  <div class=""> -->
              <select class="form-control  full-width js-reportlevel1a"  name="lot_year" id='lot_year'>
                   
              @foreach(Config::get('constants.fin_year') as $key=>$val)
                  <option value="{{$key}}">{{$val}}</option>
                  @endforeach
                  <!-- <option value="State">State</option> -->
                  
                      
              </select>
              <span id="error_lot_year" class="text-danger"></span>
          </div>



            <div class="form-group col-md-3" id='Lot_Month'>
              <label class="required-field">Payment lot pushed on month</label>
            <!--  <div class=""> -->
              <select class="form-control  full-width js-reportlevel1a"  name="lot_month" id='lot_month'>
                   
              @foreach(Config::get('constants.monthlist') as $key=>$val)
                  <option value="{{$key}}">{{$val}}</option>
                  @endforeach
                  <!-- <option value="State">State</option> -->
                  
                      
              </select>
              <span id="error_lot_month" class="text-danger"></span>
            </div>


              @if($district_visible)
               <div class="form-group col-md-3" id='select_district'>
                 <label class="">District</label>
                 <select name="district" id="district" class="form-control" tabindex="6" >
                  <option value="">--All  --</option>
                   @foreach ($districts as $district)
                  <option value="{{$district->district_code}}"  @if(old('district')== $district->district_code)  selected  @endif> {{$district->district_name}}</option>
                  @endforeach
                </select>
                 <span id="error_district" class="text-danger"></span>

                </div>
                @else
                <input type="hidden" name="district" id="district" value="{{$district_code_fk}}"/>
                @endif
                <!-- @if($is_urban_visible)
              <div class="form-group col-md-3" id="divUrbanCode">
                <label class="">Rural/ Urban</label>
                
                <select name="urban_code" id="urban_code" class="form-control" tabindex="11" >
                  <option value="">--All  --</option>
                  @foreach(Config::get('constants.rural_urban') as $key=>$val)
                  <option value="{{$key}}" @if( old('urban_code') == $key)  selected  @endif >{{$val}}</option>
                  @endforeach     
                   
                </select>
                  <span id="error_urban_code" class="text-danger"></span>
              </div>
              @else
            <input type="hidden" name="urban_code" id="urban_code" value="{{$rural_urban_fk}}"/>

              @endif -->
              
               
               
                
            
              
             
              </div>
              <div class="row">
               
                 
              
                  <br />
                  <br />
                <div class="col-md-12" align="center">

                  <button type="button"  id="submitting" value="Submit" class="btn btn-success success btn-lg modal-search form-submitted" >Search </button>
                 
                 <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" id="submit_loader1" width="50px" height="50px" style="display:none;" ></div>
                
                 <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                </div>
                <br />
               </div>
              </div>
             </div>

       <div class="tab-content" style="margin-top:16px;">

              
 <div class="alert print-error-msg"  style="display:none;" id="errorDiv">
               <button type="button" class="close"  aria-label="Close" onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
               <ul></ul>
               </div>
             <div class="tab-pane active" id="search_details" style="display:none;">
              <div class="panel panel-default">
               <div class="panel-heading" id="heading_msg"><h4><b>Search Result</b></h4></div>
               <div class="panel-body">

                                  <div class="pull-right" id="report_generation_text">Report Generated on:<b><?php echo date("l jS \of F Y h:i:s A"); ?></b></div>

<button class="btn btn-info exportToExcel" type="button" >Export to Excel</button>&nbsp;&nbsp;&nbsp; <br/><br/>
{{-- @if($designation_id=='Approver')
<form action="applicationListNoaadharExcel" method="post" id="formexcel">
         <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
         <input type="hidden" name="scheme_id" id="scheme_id">

         <input type="submit" name="submit" id="exportallexcel" class="btn btn-success" value="Export All Data to Excel"/>

  </form>  
  @endif
  <br/> --}}
<div id="divScrool"> 
             <table id="example" class="table table-striped table-bordered table2excel" style="width:100%">
         <thead >
              <tr id="header_id">
              <td colspan="6" align="center" style="display:none;" id="heading_excel">Heading</td>
              </tr> 
              
               </thead>
        <tbody>
            
        </tbody>
        <tfoot>
        <tr id="fotter_id"></tr>
        <tr>
              <td colspan="6" align="center" style="display:none;" id="fotter_excel">Heading</td>
        </tr> 
        </tfoot>
    </table>
    </div>
                
              
                 
              
               </div>
              </div>
             </div>       


               </div>
              </div>
             </div>





            </div>

  



          </div>
          <!-- /.box -->
        </div>
        <!--/.col (left) -->
        
      </div>
     <!--  @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
      @endif -->
      <!-- /.row -->

      
</section>

    <!-- Main content -->
   <!--  <section class="content">

      Your Page Content Here



    </section> -->
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Footer -->
  @include('layouts.footer')
  
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

 <!-- jQuery 2.1.3 -->
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js") }}" type="text/javascript" ></script>
<script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js") }}" type="text/javascript" ></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
<script src="{{ URL::asset('js/site.js') }}"></script>

<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("js/jquery.table2excel.js") }}"></script>

<script>

var c_date='{{$c_date}}';
var c_time='{{time()}}';

$(document).ready(function(){
    $('.sidebar-menu li').removeClass('active');
    $('.sidebar-menu #lb-aadhar').addClass("active"); 
    $('.sidebar-menu #noaadharMisReport').addClass("active"); 
  //loadDataTable();
  $(".exportToExcel").click(function(e){
      // alert('ok');
			$(".table2excel").table2excel({
    // exclude CSS class
    exclude: ".noExl",
    name: "Worksheet Name",
    filename: "Jai Bangla Payment Mis Report "+c_time, //do not include extension
    fileext: ".xls" // file extension
  }); 
	});
    $("#select_scheme").hide();
    $("#select_district").hide();
    $("#divUrbanCode").hide();
    $("#Fin_yr").hide();
    $("#Fin_Month").hide();
    $("#Lot_Month").hide();
    $("#Lot_yr").hide();

    $("#mode").on('change', function(){

        $("#select_scheme").show();
        $("#select_district").show();
        $("#divUrbanCode").show();
        $("#Fin_yr").show();
        $("#Fin_Month").show();
        $("#Lot_Month").show();
        $("#Lot_yr").show();
});

 $('.modal-search').on('click',function(){
  var error_scheme_id = '';
  var error_payment_mode = '';
  var error_fin_year = '';
  var error_fin_month = '';
  var error_lot_month ='';
  var error_lot_year ='';

  if($.trim($('#scheme_id').val()).length == 0)
  {
   error_scheme_id = 'Scheme is required';
   $('#error_scheme_id').text(error_scheme_id);
   $('#scheme_id').addClass('has-error');
  }
  else
  {
   error_scheme_id = '';
   $('#error_scheme_id').text(error_scheme_id);
   $('#scheme_id').removeClass('has-error');
  }
  if($.trim($('#mode').val()).length == 0)
  {
    error_payment_mode = 'Payment Mode is required';
    $('#error_payment_mode').text(error_payment_mode);
    $('#mode').addClass('has-error');
  }
 else
  {
     error_payment_mode = '';
     $('#error_payment_mode').text(error_payment_mode);
     $('#mode').removeClass('has-error');
  }
  if($.trim($('#fin_year').val()).length == 0)
  {
    error_fin_year = 'Finnancial Year is required';
    $('#error_fin_year').text(error_fin_year);
    $('#fin_year').addClass('has-error');
  }
 else
  {
    error_fin_year = '';
     $('#error_fin_year').text(error_fin_year);
     $('#fin_year').removeClass('has-error');
  }
  if($.trim($('#fin_month').val()).length == 0)
  {
    error_fin_month = 'Finnancial Month is required';
    $('#error_fin_month').text(error_fin_month);
    $('#fin_month').addClass('has-error');
  }
 else
  {
    error_fin_month = '';
     $('#error_fin_month').text(error_fin_month);
     $('#fin_month').removeClass('has-error');
  }
  if($.trim($('#lot_month').val()).length == 0)
  {
    error_lot_month = 'lot Month is required';
    $('#error_lot_month').text(error_lot_month);
    $('#lot_month').addClass('has-error');
  }
 else
  {
    error_lot_Month = '';
     $('#error_lot_month').text(error_lot_month);
     $('#lot_month').removeClass('has-error');
  }
  if($.trim($('#lot_year').val()).length == 0)
  {
    error_lot_year = 'lot year is required';
    $('#error_lot_year').text(error_lot_year);
    $('#lot_year').addClass('has-error');
  }
 else
  {
    error_lot_year = '';
     $('#error_lot_year').text(error_lot_year);
     $('#lot_year').removeClass('has-error');
  }
  


  if( error_scheme_id=='' || error_payment_mode != '' || error_fin_year != '' || error_fin_month != '' || error_lot_Month != '' || error_lot_year !='' ){
  loadDataTable();
  if(scheme_id!=''){
        $('#formexcel #scheme_id').val($('#scheme_id').val());
  }
  }
  else{
  return false;
  }
});
});
function loadDataTable(){
  var scheme_id=$('#scheme_id').val();
  var mode=$('#mode').val();
  var district=$('#district').val();
  var urban_code=$('#urban_code').val();
  var fin_year=$('#fin_year').val();
  var fin_month=$('#fin_month').val();
  var block=$('#block').val();
  var gp_ward=$('#gp_ward').val();
  var muncid=$('#muncid').val();
  var lot_month=$('#lot_month').val();
  var lot_year=$('#lot_year').val();
     $("#submit_loader1").show();
     $("#submitting").hide();
     $('#search_details').hide();
     if(mode=='SBI')
     {
      $.ajax({
                type: 'post',
                dataType:'json',
                url: '{{ url('get-payment-data-sbi') }}',
                data: {
                  scheme_id: scheme_id,
                  mode:mode,
                  district: district,
                  urban_code: urban_code,
                  fin_year:fin_year,
                  fin_month:fin_month,
                  lot_month:lot_month,
                  lot_year:lot_year,
                  block: block,
                  gp_ward: gp_ward,
                  muncid: muncid,
                  _token: '{{ csrf_token() }}',
                },
                success: function (data) {
                 
                  //alert(data.title);
                  if(data.return_status){
                    $('#search_details').show();
                    $("#heading_msg").html("<h4><b>"+data.heading_msg+"</b></h4>");
                    $("#heading_excel").html("<b>"+data.heading_msg+"</b>");
                    $("#fotter_excel").html("<b>"+$('#report_generation_text').text()+"</b>");
                    $("#location_id").text(data.column+'(B)');
                    $("#example > tbody").html("");
                   var table = $("#example tbody");
                   var slno=1;
                   var fotter_1=0;var fotter_2=0;var fotter_3=0;var fotter_4=0;var fotter_5=0;var fotter_6=0;
                   $.each(data.row_data, function(i, item) {
                    // var total = isNaN(parseInt(item.total)) ? 0 : parseInt(item.total);
                     var send_to_sbi = isNaN(parseInt(item.pushed_sbi)) ? 0 : parseInt(item.pushed_sbi);
                     var sbi_under_process = isNaN(parseInt(item.sbi_under_process)) ? 0 : parseInt(item.sbi_under_process);
                     var sbi_success = isNaN(parseInt(item.sbi_success)) ? 0 : parseInt(item.sbi_success); 
                     var sbi_failed = isNaN(parseInt(item.sbi_failed)) ? 0 : parseInt(item.sbi_failed);
                     var send_to_bank_amount = isNaN(parseInt(item.send_to_bank_amount)) ? 0 : parseInt(item.send_to_bank_amount);
                     var amount_disbursed = isNaN(parseInt(item.amount_disbursed)) ? 0 : parseInt(item.amount_disbursed); 
                    // var total = action_pending+approval_pending+approved+rejected;             
                     fotter_1=fotter_1+send_to_sbi;
                     fotter_2=fotter_2+sbi_under_process;
                     fotter_3=fotter_3+sbi_success;
                     fotter_4=fotter_4+sbi_failed;
                     fotter_5=fotter_5+send_to_bank_amount;
                     fotter_6=fotter_6+amount_disbursed;
                     table.append("<tr><td>"+(i+1)+"</td><td>"+item.location_name+"</td><td>"+item.pushed_sbi+"</td><td>"+item.sbi_under_process+"</td><td>"+item.sbi_success+"</td><td>"+item.sbi_failed+"</td><td>"+item.send_to_bank_amount+"</td><td>"+item.amount_disbursed+"</td></tr>");
                      //slno++;

                  });
                  $("#example> thead #header_id").html("<th>SL No.</th><th>"+data.column+"(A)</th><th>Beneficiary Send To SBI(B)</th><th>Response Pending(C)</th><th>Success(D)</th><th>Failure(E)</th><th>Send To Bank Amount(F)</th><th>Total Amount Disbursed(G)</th>");
                  $("#example> tfoot #fotter_id").html("<th></th><th>Total</th><th>"+fotter_1+"</th><th>"+fotter_2+"</th><th>"+fotter_3+"</th><th>"+fotter_4+"</th><th>"+fotter_5+"</th><th>"+fotter_6+"</th>");
                  //$('#example tbody').empty();
                   $("#example").show();
                  //  generateTableHeader(headerData);
                 
               
                  }
                  else{
                     $('#search_details').hide();
                     $("#example").hide();
                     printMsg(data.return_msg,'0','errorDiv');
                  }
                  $("#submit_loader1").hide();
                  $("#submitting").show();

                },
                error: function (ex) {
                  //console.log(ex);
                  $("#submit_loader1").hide();
                  //$("#submitting").hide();
                  $("#submitting").show();
                 /// alert('Something wrong..may be session timeout. please logout and then login again');
                //  location.reload();
                   
                }
              });
     }else{
      $.ajax({
                type: 'post',
                dataType:'json',
                url: '{{ url('get-payment-data-ifms') }}',
                data: {
                  scheme_id: scheme_id,
                  mode:mode,
                  district: district,
                  fin_year:fin_year,
                  fin_month:fin_month,
                  lot_month:lot_month,
                  lot_year:lot_year,
                  urban_code: urban_code,
                  block: block,
                  gp_ward: gp_ward,
                  muncid: muncid,
                  _token: '{{ csrf_token() }}',
                },
                success: function (data) {
                 
                  //alert(data.title);
                  if(data.return_status){
                    $('#search_details').show();
                    $("#heading_msg").html("<h4><b>"+data.heading_msg+"</b></h4>");
                    $("#heading_excel").html("<b>"+data.heading_msg+"</b>");
                    $("#fotter_excel").html("<b>"+$('#report_generation_text').text()+"</b>");
                    $("#location_id").text(data.column+'(B)');
                    $("#example > tbody").html("");
                   var table = $("#example tbody");
                   var slno=1;
                   var fotter_1=0;var fotter_2=0;var fotter_3=0;var fotter_4=0;var fotter_5=0;var fotter_6=0;var fotter_7=0;
                   $.each(data.row_data, function(i, item) {
                    // var total = isNaN(parseInt(item.total)) ? 0 : parseInt(item.total);
                     var pushed_ifms = isNaN(parseInt(item.pushed_ifms)) ? 0 : parseInt(item.pushed_ifms);
                     var ifms_returned = isNaN(parseInt(item.ifms_returned)) ? 0 : parseInt(item.ifms_returned);
                     var mandate_generated = isNaN(parseInt(item.mandate_generated)) ? 0 : parseInt(item.mandate_generated); 
                     var amount_booked = isNaN(parseInt(item.amount_booked)) ? 0 : parseInt(item.amount_booked); 
                     var rbi_failed = isNaN(parseInt(item.rbi_failed)) ? 0 : parseInt(item.rbi_failed); 
                     var rbi_success = isNaN(parseInt(item.rbi_success)) ? 0 : parseInt(item.rbi_success); 
                     var amount_paid = isNaN(parseInt(item.amount_paid)) ? 0 : parseInt(item.amount_paid);
                     //var total = action_pending+approval_pending+approved+rejected;             
                     fotter_1=fotter_1+pushed_ifms;
                     fotter_2=fotter_2+ifms_returned;
                     fotter_3=fotter_3+mandate_generated;
                     fotter_4=fotter_4+amount_booked;
                     fotter_5=fotter_5+rbi_failed;
                     fotter_6=fotter_6+rbi_success;
                     fotter_7=fotter_7+amount_paid;
                     table.append("<tr><td>"+(i+1)+"</td><td>"+item.location_name+"</td><td>"+item.pushed_ifms+"</td><td>"+item.ifms_returned+"</td><td>"+item.mandate_generated+"</td><td>"+item.amount_booked+"</td><td>"+item.rbi_failed+"</td><td>"+item.rbi_success+"</td><td>"+item.amount_paid+"</td></tr>");
                      //slno++;

                  });
                  $("#example> thead #header_id").html("<th>SL No.</th><th>"+data.column+"(A)</th><th>Beneficiary Send To IFMS(B)</th><th>Discarded From IFMS(C)</th><th>Payment Mandate Generated(D)</th><th>Ammount Booked(G)(E)</th><th>Success(RBI)(G)</th><th>Failure(RBI)</th><th>Total Ammount Disbursed(H)</th>");
                  $("#example > tfoot #fotter_id").html("<th></th><th>Total</th><th>"+fotter_1+"</th><th>"+fotter_2+"</th><th>"+fotter_3+"</th><th>"+fotter_4+"</th><th>"+fotter_5+"</th><th>"+fotter_6+"</th><th>"+fotter_7+"</th>");
                  //$('#example tbody').empty();
                   $("#example").show();
                  
               
                  }
                  else{
                     $('#search_details').hide();
                     $("#example").hide();
                     printMsg(data.return_msg,'0','errorDiv');
                  }
                  $("#submit_loader1").hide();
                  $("#submitting").show();

                },
                error: function (ex) {
                  //console.log(ex);
                  $("#submit_loader1").hide();
                  //$("#submitting").hide();
                  $("#submitting").show();
                 /// alert('Something wrong..may be session timeout. please logout and then login again');
                //  location.reload();
                   
                }
              });
     }
       
   
}


// function generateTableHeader(headerData) {
//   const tableHeaderRow = document.querySelector('#example thead tr');

//   // Clear any existing header cells
//   tableHeaderRow.innerHTML = '';

//   // Loop through the headerData array and create header cells
//   headerData.forEach(columnName => {
//     const th = document.createElement('th');
//     th.textContent = columnName;
//     tableHeaderRow.appendChild(th);
//   });
// }

// Example data for the table header
//const headerData = ['Sl no.', 'District Name', 'Send To SBI', 'Response Pending','Success','Failure','Send To Bank Amount','Total Amount Disbursed'];

// Call the function to generate the dynamic table header


function printMsg (msg,msgtype,divid) {
            $("#"+divid).find("ul").html('');
            $("#"+divid).css('display','block');
			if(msgtype=='0'){
				//alert('error');
				$("#"+divid).removeClass('alert-success');
				//$('.print-error-msg').removeClass('alert-warning');
				$("#"+divid).addClass('alert-warning');
			}
			else{
				$("#"+divid).removeClass('alert-warning');
				$("#"+divid).addClass('alert-success');
			}
			if(Array.isArray(msg)){
            $.each( msg, function( key, value ) {
                $("#"+divid).find("ul").append('<li>'+value+'</li>');
            });
			}
			else{
				$("#"+divid).find("ul").append('<li>'+msg+'</li>');
			}
  }
   function closeError(divId){
   $('#'+divId).hide();
  }
  
</script>
</body>
</html>


