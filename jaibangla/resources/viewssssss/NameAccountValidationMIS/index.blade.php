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
        .box {
            width: 800px;
            margin: 0 auto;
        }

        .active_tab1 {
            background-color: #fff;
            color: #333;
            font-weight: 600;
        }

        .inactive_tab1 {
            background-color: #f5f5f5;
            color: #333;
            cursor: not-allowed;
        }

        .has-error {
            border-color: #cc0000;
            background-color: #ffff99;
        }

        .select2 {
            width: 100% !important;
        }

        .select2 .has-error {
            border-color: #cc0000;
            background-color: #ffff99;
        }

        .modal_field_name {
            float: left;
            font-weight: 700;
            margin-right: 1%;
            padding-top: 1%;
            margin-top: 1%;
        }

        .modal_field_value {
            margin-right: 1%;
            padding-top: 1%;
            margin-top: 1%;
        }

        .row {
            margin-right: 0px !important;
            margin-left: 0px !important;
            margin-top: 1% !important;
        }

        .section1 {
            border: 1.5px solid #9187878c;
            margin: 2%;
            padding: 2%;
        }

        .color1 {
            margin: 0% !important;
            background-color: #5f9ea061;
        }

        .modal-header {
            background-color: #7fffd4;
        }

        .required-field::after {
            content: "*";
            color: red;
        }

        .imageSize {
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
                    <!-- <div class="col-md-12">
         
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
             
            </div> -->
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form method="post" id="register_form" action="{{url('wcd20210202ReportPost')}}" class="submit-once">
                        {{ csrf_field() }}
                        <div class="tab-content" style="margin-top:16px;">

                            <div class="tab-pane active" id="personal_details">
                                <div class="panel panel-default">
                                    <div class="panel-heading" id ="name_val_heading">
                                        <h4><b>Bank Name/Account Validation Failed MIS Report </b></h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label class="required-field">Scheme</label>
                                                <select name="scheme_id" id="scheme_id" class="form-control" tabindex="6">
                                                    <option value="">--All --</option>
                                                    @foreach ($scheme_list as $scheme)
                                                    <option value="{{$scheme->id}}" @if(old('scheme_id')==$scheme->id) selected @endif> {{$scheme->scheme_name}}</option>
                                                    @endforeach
                                                </select>
                                                <span id="error_scheme_id" class="text-danger"></span>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="required-field">Validation Type</label>
                                                <select name="failed_type" id="failed_type" class="form-control" tabindex="6">
                                                    <option value="">--Select Type--</option>
                                                    <option value="1">Name Validation Failed</option>
                                                    <option value="2">Account Validation Failed</option>
                                                </select>
                                                <span id="error_failed_type" class="text-danger"></span>
                                            </div>
                                            {{-- <div class="form-group col-md-4">
                                                <label >Score Type</label>
                                                <select name="score_type" id="score_type" class="form-control" tabindex="6">
                                                    <option value="">--Select Type--</option>
                                                    <option value="1">Less then equal 25</option>
                                                    
                                                </select>
                                                <span id="error_failed_type" class="text-danger"></span>
                                            </div> --}}

                                            @if($district_visible)
                                            <div class="form-group col-md-4">
                                                <label class="">District</label>
                                                <select name="district" id="district" class="form-control" tabindex="6">
                                                    <option value="">--All --</option>
                                                    @foreach ($districts as $district)
                                                    <option value="{{$district->district_code}}" @if(old('district')==$district->district_code) selected @endif> {{$district->district_name}}</option>
                                                    @endforeach
                                                </select>
                                                <span id="error_district" class="text-danger"></span>

                                            </div>
                                            @else
                                            <input type="hidden" name="district" id="district" value="{{$district_code_fk}}" />
                                            @endif
                                            @if($is_urban_visible)
                                            <div class="form-group col-md-4" id="divUrbanCode">
                                                <label class="">Rural/ Urban</label>

                                                <select name="urban_code" id="urban_code" class="form-control" tabindex="11">
                                                    <option value="">--All --</option>
                                                    @foreach(Config::get('constants.rural_urban') as $key=>$val)
                                                    <option value="{{$key}}" @if( old('urban_code')==$key) selected @endif>{{$val}}</option>
                                                    @endforeach

                                                </select>
                                                <span id="error_urban_code" class="text-danger"></span>
                                            </div>
                                            @else
                                            <input type="hidden" name="urban_code" id="urban_code" value="{{$rural_urban_fk}}" />

                                            @endif



                                            <div class="col-md-12" align="center">

                                                <button type="button" id="submitting" value="Submit" class="btn btn-success success btn-lg modal-search form-submitted">Search </button>

                                                <div class=""><img src="{{ asset('images/ZKZg.gif')}}" id="submit_loader1" width="50px" height="50px" style="display:none;"></div>

                                                <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                                            </div>
                                            <br />
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-content" style="margin-top:16px;">


                                    <div class="alert print-error-msg" style="display:none;" id="errorDiv">
                                        <button type="button" class="close" aria-label="Close" onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
                                        <ul></ul>
                                    </div>



                                    <div class="tab-pane active" id="search_details" style="display:none;">
                                        <div class="panel panel-default">
                                            <div class="panel-heading" id="heading_msg">
                                                <h4><b>Search Result</b></h4>
                                            </div>
                                            <div class="panel-body">

                                                <div class="pull-right" id="report_generation_text">Report Generated on:<b><?php echo date("l jS \of F Y h:i:s A"); ?></b></div>

                                                <button class="btn btn-info exportToExcel" type="button">Export to Excel</button><br /><br /><br />
                                                <div id="divScrool">
                                                    <table id="example" class="table table-striped table-bordered table2excel" style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <td colspan="21" align="center" style="display:none;" id="heading_excel">Heading</td>
                                                            </tr>
                                                            <tr rowspan="2" id="name_tr">
                                                                <th rowspan="2" style="vertical-align: middle;">Sl No.(A)</th>
                                                                <th rowspan="2" style="vertical-align: middle;" id="location_id">District</th>
                                                                {{-- <th rowspan="2" style="vertical-align: middle;">Total Name Mismatch (C)</th> --}}
                                                                <th rowspan="2" style="vertical-align: middle;">Yet To Be Action Pending (C)</th>
                                                                <th colspan="2">Processed With Minor Missmatch (D)</th>
                                                                <th colspan="2">Processed With New Bank Info (E)</th>
                                                                <th colspan="2">Rejected(F)</th>
                                                                <th rowspan="2" style="vertical-align: middle;">De-activated(G)</th>
                                                            </tr>

                                                            <tr  id="name_trt"> 
                                                                <th>Yet To Be Approved</th> 
                                                                <th>Approved</th> 
                                                                <th>Yet To Be Approved</th> 
                                                                <th>Approved</th> 
                                                                <th>Send For Rejection</th>  
                                                                <th>Request Approved</th> 
                                                            </tr>

                                                            <tr id="account_tr">
                                                                <th id="">Sl No.(A)</th>
                                                                <th id="location_id">District</th>
                                                                {{-- <th>Total Account Mismatch (C)</th> --}}
                                                                <th>Yet To Be Action Pending (C)</th>
                                                                <th>Yet To Be Approved (D)</th>
                                                                <th>Approved (E)</th>
                                                                <th >De-activated(F)</th>
                                                            </tr>

                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                        <tfoot>
                                                            <tr id="fotter_id"></tr>
                                                            <tr>
                                                                <td colspan="7" align="center" style="display:none;" id="fotter_excel">Heading</td>
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

                </form>
        </div>
        <!-- /.box -->
    </div>
    <!--/.col (left) -->

    </div>


    </section>


    </div>


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
var base_date='{{$base_date}}';
var c_date='{{$c_date}}';
//alert(base_date);

$(document).ready(function(){
  $('.sidebar-menu li').removeClass('active');
  $('.sidebar-menu #lk-main').addClass("active"); 
  $('.sidebar-menu #dupBankmis').addClass("active"); 
  //loadDataTable();
  $(".exportToExcel").click(function(e){
    var failed_type = $("#failed_type").val();
    if(failed_type == 1) {
        $("#account_tr").remove();
        filename = "Jai Bangla Bank Name Validation MIS Report"; 
    } else if(failed_type == 2) {
        $("#name_tr, #name_trt").remove();
        filename = "Jai Bangla Bank Account Validation MIS Report"; 
    }
    var exportOptions = {
        exclude: ".noExl",
        name: "Worksheet Name", // If no header is selected, use a default name
        filename: filename, 
        fileext: ".xls" // file extension
    };
    $(".table2excel").table2excel(exportOptions); 
	});
  $("#from_date").on('blur',function(){ 
      var from_date = $('#from_date').val();
      if(from_date!=''){
       //alert(from_date);
       document.getElementById("to_date").setAttribute("min", from_date);
      }
      else{
        //alert(c_date);
        document.getElementById("to_date").setAttribute("min", base_date);
      }
    });
  
    $('#district').change(function() {
      var district=$(this).val();
      //alert(district);
        $('#urban_code').val('');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
    });

    $('#urban_code').change(function() {
       var urban_code=$(this).val();
        if(urban_code==''){
          $('#muncid').html('<option value="">--All --</option>'); 
        }
        $('#muncid').html('<option value="">--All --</option>'); 
        $('#block').html('<option value="">--All --</option>');
        $('#gp_ward').html('<option value="">--All --</option>');
        select_district_code= $('#district').val();
        if(select_district_code==''){
               alert('Please Select District First');
               $("#district").focus();
               $("#urban_code").val('');
        }
        else{
        select_body_type= urban_code;
        var htmlOption='<option value="">--All--</option>';
        $("#gp_ward_div").show();
        if(select_body_type==2){
            $("#blk_sub_txt").text('Block');
            $("#gp_ward_txt").text('GP');
            $("#municipality_div").hide();
            $.each(blocks, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        }else if(select_body_type==1){
            $("#blk_sub_txt").text('Subdivision');
            $("#gp_ward_txt").text('Ward');
            $("#municipality_div").show();
            $.each(subDistricts, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        } 
        else{
          $("#blk_sub_txt").text('Block/Subdivision');
        }   
        $('#block').html(htmlOption);
        }

    });

    
$('#block').change(function() {
      var block=$(this).val();
      var district=$("#district").val();
      var urban_code=$("#urban_code").val();
      if(district==''){
        $('#urban_code').val('');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
        alert('Please Select District First');
        $("#district").focus();
        
    }
    if(urban_code==''){
        alert('Please Select Rural/Urban First');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
        $("#urban_code").focus();
    }
    if(block!=''){
        var rural_urbanid= $('#urban_code').val();
      if(rural_urbanid==1){
       var sub_district_code=$(this).val();
       if(sub_district_code!=''){
        $('#muncid').html('<option value="">--All --</option>');
        select_district_code= $('#district').val();
        var htmlOption='<option value="">--All--</option>';
          $.each(ulbs, function (key, value) {
                if((value.district_code==select_district_code) && (value.sub_district_code==sub_district_code)){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        $('#muncid').html(htmlOption);
       }
       else{
          $('#muncid').html('<option value="">--All --</option>');
       }   
       } 
       else if(rural_urbanid==2){
          $('#muncid').html('<option value="">--All --</option>');
          $("#municipality_div").hide();
          var block_code=$(this).val();
          select_district_code= $('#district').val();

          var htmlOption='<option value="">--All--</option>';
          $.each(gps, function (key, value) {
                if((value.district_code==select_district_code) && (value.block_code==block_code)){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
          });
          $('#gp_ward').html(htmlOption);
          $("#gp_ward_div").show();


       }
       else{
          $('#muncid').html('<option value="">--All --</option>');
          $("#municipality_div").hide();
       } 
    }
    else{
        $('#muncid').html('<option value="">--All --</option>');
         $('#gp_ward').html('<option value="">--All --</option>');
    }
    
    });


$('#muncid').change(function() {
      var muncid=$(this).val();
      var district=$("#district").val();
      var urban_code=$("#urban_code").val();
      if(district==''){
        $('#urban_code').val('');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
        alert('Please Select District First');
        $("#district").focus();
        
    }
    if(urban_code==''){
        alert('Please Select Rural/Urban First');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
        $("#urban_code").focus();
    }
    if(muncid!=''){
        var rural_urbanid= $('#urban_code').val();
      if(rural_urbanid==1){
       var municipality_code=$(this).val();
       if(municipality_code!=''){
        $('#gp_ward').html('<option value="">--All --</option>');
        var htmlOption='<option value="">--All--</option>';
          $.each(ulb_wards, function (key, value) {
                if(value.urban_body_code==municipality_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        $('#gp_ward').html(htmlOption);
       }
       else{
          $('#gp_ward').html('<option value="">--All --</option>');
       }   
       } 
    
       else{
          $('#gp_ward').html('<option value="">--All --</option>');
          $("#gp_ward_div").hide();
       } 
    }
    else{
       $('#gp_ward').html('<option value="">--All --</option>');
    }
    
    });
 $('.modal-search').on('click',function(){

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
  if($.trim($('#failed_type').val()).length == 0){
    error_failed_type = 'Failed Type is required';
    $('#error_failed_type').text(error_failed_type);
  }
  else{
    error_failed_type = '';
    $('#error_failed_type').text(error_failed_type);
  }
  if (error_scheme_id != '' || error_failed_type != '') {
    return false;
  }
  else {
    loadDataTable();
 }
   
  
});
});

function loadDataTable(){
  var scheme_id=$('#scheme_id').val();
   var district=$('#district').val();
  var urban_code=$('#urban_code').val();
  var block=$('#block').val();
  var gp_ward=$('#gp_ward').val();
  var muncid=$('#muncid').val();
  var failed_type = $('#failed_type').val();
  var score_type = $('#score_type').val();
  
     $("#submit_loader1").show();
     $("#submitting").hide();
     $('#search_details').hide();
        $.ajax({
                type: 'post',
                dataType:'json',
                url: '{{ url('name-account-validation-getData') }}',
                data: {
                  scheme_id: scheme_id,
                  failed_type: failed_type,
                  score_type: score_type,
                  district: district,
                  urban_code: urban_code,
                  block: block,
                  gp_ward: gp_ward,
                  muncid: muncid,
                  _token: '{{ csrf_token() }}',
                },
                success: function (data) {
                 //console.log(data);
                 // alert(data);
                  if(data.return_status){
                    $('#search_details').show();
                    $("#heading_msg").html("<h4><b>"+data.heading_msg+"</b></h4>");
                    $("#heading_excel").html("<b>"+data.heading_msg+"</b>");
                    $("#fotter_excel").html("<b>"+$('#report_generation_text').text()+"</b>");
                    $("#location_id").text(data.column+'(B)');
                    $("#example > tbody").html("");
                   var table = $("#example tbody");
                   var slno=1;
                   var fotter_1=0;var fotter_2=0;var fotter_3=0;var fotter_4=0;var fotter_5=0;var fotter_6=0;var fotter_7=0;var fotter_8=0;var fotter_9=0;
                   if(data.failed_type == 1){
                    $('#name_tr').show();
                    $('#name_trt').show();
                    $('#account_tr').hide();
                   }else{
                    $('#account_tr').show();
                    $('#name_tr').hide();
                    $('#name_trt').hide();
                   }
                   if(data.failed_type == 1){
                            $.each(data.row_data, function(i, item) {
                            //var total_name_mismatch = isNaN(parseInt(item.total_name_mismatch)) ? 0 : parseInt(item.total_name_mismatch);
                            var total_yet_to_be_action_pending= isNaN(parseInt(item.total_yet_to_be_action_pending)) ? 0 : parseInt(item.total_yet_to_be_action_pending);
                            var total_minor_yet_to_approved = isNaN(parseInt(item.total_minor_yet_to_approved)) ? 0 : parseInt(item.total_minor_yet_to_approved);
                            var total_minor_approved = isNaN(parseInt(item.total_minor_approved)) ? 0 : parseInt(item.total_minor_approved);
                            var total_bank_yet_to_approved = isNaN(parseInt(item.total_bank_yet_to_approved)) ? 0 : parseInt(item.total_bank_yet_to_approved);
                            var total_bank_approved = isNaN(parseInt(item.total_bank_approved)) ? 0 : parseInt(item.total_bank_approved);
                            var total_send_rejection = isNaN(parseInt(item.total_send_rejection)) ? 0 : parseInt(item.total_send_rejection);
                            var total_request_approved = isNaN(parseInt(item.total_request_approved)) ? 0 : parseInt(item.total_request_approved);
                            var total_deactivated = isNaN(parseInt(item.total_deactivated)) ? 0 : parseInt(item.total_deactivated);
                            //var total_pending = tot_dup-(total_edit_differ+total_edit_same+total_rejected);
                            // fotter_1=fotter_1+total_name_mismatch;
                            fotter_2=fotter_2+total_minor_yet_to_approved;
                            fotter_3=fotter_3+total_minor_approved;
                            fotter_4=fotter_4+total_bank_yet_to_approved;
                            fotter_5=fotter_5+total_bank_approved;
                            fotter_6=fotter_6+total_send_rejection;
                            fotter_7=fotter_7+total_request_approved;
                            fotter_8=fotter_8+total_yet_to_be_action_pending;
                            fotter_9=fotter_9+total_deactivated;
                            table.append("<tr><td>"+(i+1)+"</td><td>"+item.location_name+"</td><td>"+total_yet_to_be_action_pending+"</td><td>"+total_minor_yet_to_approved+"</td><td>"+total_minor_approved+"</td><td>"+total_bank_yet_to_approved+"</td><td>"+total_bank_approved+"</td><td>"+total_send_rejection+"</td><td>"+total_request_approved+"</td><td>"+total_deactivated+"</td></tr>");
                            //slno++;

                        });
                        $("#example > tfoot #fotter_id").html("<th></th><th>Total</th><th>"+fotter_8+"</th><th>"+fotter_2+"</th><th>"+fotter_3+"</th><th>"+fotter_4+"</th><th>"+fotter_5+"</th><th>"+fotter_6+"</th><th>"+fotter_7+"</th><th>"+fotter_9+"</th>");
                   }else{
                    $.each(data.row_data, function(i, item) {
                            // var total_account_mismatch = isNaN(parseInt(item.total_account_mismatch)) ? 0 : parseInt(item.total_account_mismatch);
                            var total_yet_to_be_action_pending= isNaN(parseInt(item.total_yet_to_be_action_pending)) ? 0 : parseInt(item.total_yet_to_be_action_pending);
                            var total_yet_to_approved = isNaN(parseInt(item.total_yet_to_approved)) ? 0 : parseInt(item.total_yet_to_approved);
                            var total_approved = isNaN(parseInt(item.total_approved)) ? 0 : parseInt(item.total_approved);
                            var total_deactivated = isNaN(parseInt(item.total_deactivated)) ? 0 : parseInt(item.total_deactivated);
                            //var total_pending = tot_dup-(total_edit_differ+total_edit_same+total_rejected);
                            // fotter_1=fotter_1+total_account_mismatch;
                            fotter_2=fotter_2+total_yet_to_be_action_pending;
                            fotter_3=fotter_3+total_yet_to_approved;
                            fotter_4=fotter_4+total_approved;
                            fotter_5=fotter_5+total_approved;
                            table.append("<tr><td>"+(i+1)+"</td><td>"+item.location_name+"</td><td>"+total_yet_to_be_action_pending+"</td><td>"+total_yet_to_approved+"</td><td>"+total_approved+"</td><td>"+total_deactivated+"</td></tr>");
                            //slno++;
                        });
                        $("#example > tfoot #fotter_id").html("<th></th><th>Total</th><th>"+fotter_2+"</th><th>"+fotter_3+"</th><th>"+fotter_4+"</th><th>"+fotter_5+"</th>"); 
                   }
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