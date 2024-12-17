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
   /*width:800px;*/
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
.filterDiv {
    border: 1px solid #d9d9d9; 
    border-left: 3px solid deepskyblue; 
    margin-bottom: 10px; 
    padding-bottom: 8px; 
    box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
  }
  .resultDiv {
    border: 1px solid #d9d9d9; 
    border-left: 3px solid seagreen; 
    /*margin-bottom: 10px; */
    padding: 8px;  
    box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
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
       Age cohort wise beneficiaries
      </h1>
      <ol class="breadcrumb">
        <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
      </ol>
    </section>
  <section class="content">
    <div class="box box-default">
        <div class="box-body">

          <input type="hidden" name="c_date" id="c_date" value="{{$c_date}}" >
             <input type="hidden" name="scheme_name" id="scheme_name" value="{{$scheme_name}}" >

          <!-- <div class="panel panel-default">
            <div class="panel-heading"><span id="panel-icon" style="font-style: italic; font-weight: bold;">Search Criteria</div>
            <div class="panel-body" style="padding: 5px;"> -->
              <div class="filterDiv">
                <!-- <div class="col-md-12"> -->

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
                  <div class="row">
              
                
            
               <div class="form-group col-md-4">
                 <label class="required-field">Scheme</label>
                 <select name="scheme_id" id="scheme_id" class="form-control" tabindex="1" >
                  <option value="">--Select Scheme  --</option>
                   @foreach ($scheme_list as $scheme)
                  <option value="{{$scheme->id}}"  @if(old('scheme_id')== $scheme->id)  selected  @endif> {{$scheme->scheme_name}}</option>
                  @endforeach
                </select>
                 <span id="error_scheme_id" class="text-danger"></span>

                </div>
               
              
              
                            
             
              @if($district_visible)
               <div class="form-group col-md-4">
                 <label class="">District</label>
                 <select name="district" id="district" class="form-control" tabindex="2" >
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
                @if($is_urban_visible)
              <div class="form-group col-md-4" id="divUrbanCode">
                <label class="">Rural/ Urban</label>
                
                <select name="urban_code" id="urban_code" class="form-control" tabindex="3" >
                  <option value="">--All  --</option>
                  @foreach(Config::get('constants.rural_urban') as $key=>$val)
                  <option value="{{$key}}" @if( old('urban_code') == $key)  selected  @endif >{{$val}}</option>
                  @endforeach     
                   
                </select>
                  <span id="error_urban_code" class="text-danger"></span>
              </div>
              @else
            <input type="hidden" name="urban_code" id="urban_code" value="{{$rural_urban_fk}}"/>

              @endif
              @if($block_visible)
                <div class="form-group col-md-4" id="divBodyCode">
                <label class="" id="blk_sub_txt">Block/Sub Division.</label>
                
                <select name="block" id="block" class="form-control" tabindex="4" >
                  <option value="">--All --</option>
                  
                   
                </select>
                  <span id="error_block" class="text-danger"></span>
              </div>
               @else
              <input type="hidden" name="block" id="block" value="{{$block_munc_corp_code_fk}}"/>

               @endif
              
           
               
                
        
              
             
              </div>
              <div class="row">
            
                 
              
                <div class="col-md-12" align="center">

                  <button type="button"  id="submitting" value="Submit" class="btn btn-success success btn-lg modal-search form-submitted" >Search </button>
                 
                 <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" id="submit_loader1" width="50px" height="50px" style="display:none;" ></div>
                
                 <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                </div>
                <br />
               </div>

               <div class="alert print-error-msg"  style="display:none;" id="errorDiv">
               <button type="button" class="close"  aria-label="Close" onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
               <ul></ul>
               </div>

                <!-- </div> -->
              </div>
            <!-- </div>
          </div> -->


          
          <div class="resultDiv" id="search_details" style="display:none;">
            <!-- <div class="panel panel-default">
              <div class="panel-heading" id="panel_head" style="font-style: italic; font-weight: bold;">Search Result</div>
              <div class="panel-body" style="padding: 5px; font-size: 14px;"> -->
              <div class="pull-right" id="report_generation_text">Report Generated on:<b><?php date_default_timezone_set('Asia/Kolkata'); echo date("l jS \of F Y h:i:s A"); ?></b></div>

              <button class="btn btn-info exportToExcel" type="button" >Export to Excel</button><br/><br/><br/>
              <div id="divScrool"> 
              <table id="example" class="table table-striped table-bordered table2excel" style="width:100%">
                <thead>
                </thead>
                <tbody>
                    
                </tbody>
                <tfoot>
                  <tr id="fotter_id"></tr>
                  <tr>
                    <td colspan="26" align="center" style="display:none;" id="fotter_excel">Heading</td>
                  </tr> 
                </tfoot>
              </table>
              </div>
                
              <!-- </div>
            </div> -->
          </div>
        </div>
      </div>
 
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
<script  src="{{ asset ("/bower_components/AdminLTE/moment/moment.js") }}" type="text/javascript" ></script>
<script>
var c_date='{{$c_date}}';
//alert(base_date);

$(document).ready(function(){
  $('.sidebar-menu li').removeClass('active');
  $('.sidebar-menu #lk-main').addClass("active"); 
  $('.sidebar-menu #mis-report').addClass("active"); 
  //loadDataTable();
  // Live Clock
    var interval = setInterval(function () {
    var momentNow = moment();
      $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
      $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);

  $(".exportToExcel").click(function(e){
     var c_date=$("#c_date").val();
     var scheme_name=$("#scheme_name").val();
			$(".table2excel").table2excel({
    // exclude CSS class
    exclude: ".noExl",
    name: "Worksheet Name",
    filename: "Age cohort wise beneficiaries Report for the scheme "+scheme_name+" as on "+c_date, //do not include extension
    fileext: ".xls" // file extension
  }); 
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
  $("#c_date").val('');
  $("#scheme_name").val('');
  var error_scheme_id='';
  if($.trim($('#scheme_id').val()).length == 0){
    error_scheme_id = 'Scheme name is required';
    $('#error_scheme_id').text(error_scheme_id);
  }
  else{
    error_scheme_id = '';
    $('#error_scheme_id').text(error_scheme_id);
  }
  if (error_scheme_id != '') {
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


     $("#submit_loader1").show();
     $("#submitting").hide();
     $('#search_details').hide();
        $.ajax({
                type: 'post',
                dataType:'json',
                url: '{{ url('differentAgeCohortReportGetData') }}',
                data: {
                  scheme_id: scheme_id,
                  district: district,
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
                    $("#heading_msg1").html("<h4><b>"+data.heading_msg1+"</b></h4>");
                    $("#heading_excel1").html("<b>"+data.heading_msg1+"</b>");
                    $("#heading_excel2").html("<b>"+data.heading_msg2+"</b>");
                    $("#fotter_excel").html("<b>"+$('#report_generation_text').text()+"</b>");
                    // $("#location_id").text(data.column);
                    $("#c_date").val(data.c_date);
                    $("#scheme_name").val(data.scheme_name);
                    $("#example > tbody").html("");
                    $("#example > thead").html("");
                   var table = $("#example tbody");
                   var tableHead = $("#example thead");
                   var slno=1;
                   var fotter_1=0;var fotter_2=0;var fotter_3=0;var fotter_4=0;var fotter_5=0;
                   var fotter_6=0;var fotter_7=0;var fotter_8=0;var fotter_9=0;var fotter_10=0;
                   var fotter_11=0;
                   // var total1=0;var total2=0;var total3=0;var total4=0;var total5=0;
                   // var total6=0;var total7=0;var total8=0;var total9=0;var total10=0;
                   if (data.scheme_id == 10) {
                    tableHead.append("<tr><th>Sl No.</th><th id='location_id'></th><th>Age Below 60 years</th><th>Age Between 60-69 years</th><th>Age Between 70-79 years</th><th>Age Between 80-89 years</th><th>Age Between 90-99 years</th><th>Age Above 100 years</th>  </tr>");
                   }
                   else if(data.scheme_id == 11) {
                    tableHead.append("<tr><th>Sl No.</th><th id='location_id'></th><th>Age Below 20 years</th><th>Age Between 20-29 years</th><th>Age Between 30-39 years</th><th>Age Between 40-49 years</th><th>Age Between 50-59 years</th><th>Age Between 60-69 years</th><th>Age Between 70-79 years</th><th>Age Between 80-89 years</th><th>Age Between 90-99 years</th><th>Age Above 100 years</th></tr>");
                   }
                   else if(data.scheme_id == 2) {
                    tableHead.append("<tr><th>Sl No.</th><th id='location_id'></th><th>Age Below 10 years</th><th>Age Between 10-19 years</th><th>Age Between 20-29 years</th><th>Age Between 30-39 years</th><th>Age Between 40-49 years</th><th>Age Between 50-59 years</th><th>Age Between 60-69 years</th><th>Age Between 70-79 years</th><th>Age Between 80-89 years</th><th>Age Between 90-99 years</th><th>Age Above 100 years</th></tr>");
                   }
                   $.each(data.row_data, function(i, item) {
                    if (data.scheme_id == 10) {
                      var total1 = isNaN(parseInt(item.age_below_60)) ? 0 : parseInt(item.age_below_60);
                      var total2 = isNaN(parseInt(item.age_60_70)) ? 0 : parseInt(item.age_60_70);
                      var total3 = isNaN(parseInt(item.age_70_80)) ? 0 : parseInt(item.age_70_80);
                      var total4 = isNaN(parseInt(item.age_80_90)) ? 0 : parseInt(item.age_80_90);
                      var total5 = isNaN(parseInt(item.age_90_100)) ? 0 : parseInt(item.age_90_100);
                      var total6 = isNaN(parseInt(item.age_above_100)) ? 0 : parseInt(item.age_above_100);

                      fotter_1=fotter_1+total1;
                      fotter_2=fotter_2+total2;
                      fotter_3=fotter_3+total3;
                      fotter_4=fotter_4+total4;
                      fotter_5=fotter_5+total5;
                      fotter_6=fotter_6+total6;

                      
                      table.append("<tr><td>"+(i+1)+"</td><td>"+item.location_name+"</td><td>"+total1+"</td><td>"+total2+"</td><td>"+total3+"</td><td>"+total4+"</td><td>"+total5+"</td><td>"+total6+"</td></tr>");
                    }
                    else if (data.scheme_id == 11) {
                      var total1 = isNaN(parseInt(item.age_below_20)) ? 0 : parseInt(item.age_below_20);
                      var total2 = isNaN(parseInt(item.age_20_30)) ? 0 : parseInt(item.age_20_30);
                      var total3 = isNaN(parseInt(item.age_30_40)) ? 0 : parseInt(item.age_30_40);
                      var total4 = isNaN(parseInt(item.age_40_50)) ? 0 : parseInt(item.age_40_50);
                      var total5 = isNaN(parseInt(item.age_50_60)) ? 0 : parseInt(item.age_50_60);
                      var total6 = isNaN(parseInt(item.age_60_70)) ? 0 : parseInt(item.age_60_70);
                      var total7 = isNaN(parseInt(item.age_70_80)) ? 0 : parseInt(item.age_70_80);
                      var total8 = isNaN(parseInt(item.age_80_90)) ? 0 : parseInt(item.age_80_90);
                      var total9 = isNaN(parseInt(item.age_90_100)) ? 0 : parseInt(item.age_90_100);
                      var total10 = isNaN(parseInt(item.age_above_100)) ? 0 : parseInt(item.age_above_100);

                      fotter_1=fotter_1+total1;
                      fotter_2=fotter_2+total2;
                      fotter_3=fotter_3+total3;
                      fotter_4=fotter_4+total4;
                      fotter_5=fotter_5+total5;
                      fotter_6=fotter_6+total6;
                      fotter_7=fotter_7+total7;
                      fotter_8=fotter_8+total8;
                      fotter_9=fotter_9+total9;
                      fotter_10=fotter_10+total10;

                      
                      table.append("<tr><td>"+(i+1)+"</td><td>"+item.location_name+"</td><td>"+total1+"</td><td>"+total2+"</td><td>"+total3+"</td><td>"+total4+"</td><td>"+total5+"</td><td>"+total6+"</td><td>"+total7+"</td><td>"+total8+"</td><td>"+total9+"</td><td>"+total10+"</td></tr>");
                    }
                    else if (data.scheme_id == 2) {
                      var total1 = isNaN(parseInt(item.age_below_10)) ? 0 : parseInt(item.age_below_10);
                      var total2 = isNaN(parseInt(item.age_10_20)) ? 0 : parseInt(item.age_10_20);
                      var total3 = isNaN(parseInt(item.age_20_30)) ? 0 : parseInt(item.age_20_30);
                      var total4 = isNaN(parseInt(item.age_30_40)) ? 0 : parseInt(item.age_30_40);
                      var total5 = isNaN(parseInt(item.age_40_50)) ? 0 : parseInt(item.age_40_50);
                      var total6 = isNaN(parseInt(item.age_50_60)) ? 0 : parseInt(item.age_50_60);
                      var total7 = isNaN(parseInt(item.age_60_70)) ? 0 : parseInt(item.age_60_70);
                      var total8 = isNaN(parseInt(item.age_70_80)) ? 0 : parseInt(item.age_70_80);
                      var total9 = isNaN(parseInt(item.age_80_90)) ? 0 : parseInt(item.age_80_90);
                      var total10 = isNaN(parseInt(item.age_90_100)) ? 0 : parseInt(item.age_90_100);
                      var total11 = isNaN(parseInt(item.age_above_100)) ? 0 : parseInt(item.age_above_100);

                      fotter_1=fotter_1+total1;
                      fotter_2=fotter_2+total2;
                      fotter_3=fotter_3+total3;
                      fotter_4=fotter_4+total4;
                      fotter_5=fotter_5+total5;
                      fotter_6=fotter_6+total6;
                      fotter_7=fotter_7+total7;
                      fotter_8=fotter_8+total8;
                      fotter_9=fotter_9+total9;
                      fotter_10=fotter_10+total10;
                      fotter_11=fotter_11+total11;

                      
                      table.append("<tr><td>"+(i+1)+"</td><td>"+item.location_name+"</td><td>"+total1+"</td><td>"+total2+"</td><td>"+total3+"</td><td>"+total4+"</td><td>"+total5+"</td><td>"+total6+"</td><td>"+total7+"</td><td>"+total8+"</td><td>"+total9+"</td><td>"+total10+"</td><td>"+total11+"</td></tr>");
                    }

                  });
                  
                  
                  if (data.scheme_id == 10) {
                    $("#example > tfoot #fotter_id").html("<th></th><th>Total</th><th>"+fotter_1+"</th><th>"+fotter_2+"</th><th>"+fotter_3+"</th><th>"+fotter_4+"</th><th>"+fotter_5+"</th><th>"+fotter_6+"</th>");
                   }
                   else if(data.scheme_id == 11) {
                    $("#example > tfoot #fotter_id").html("<th></th><th>Total</th><th>"+fotter_1+"</th><th>"+fotter_2+"</th><th>"+fotter_3+"</th><th>"+fotter_4+"</th><th>"+fotter_5+"</th><th>"+fotter_6+"</th><th>"+fotter_7+"</th><th>"+fotter_8+"</th><th>"+fotter_9+"</th><th>"+fotter_10+"</th>");
                   }
                   else if(data.scheme_id == 2) {
                    $("#example > tfoot #fotter_id").html("<th></th><th>Total</th><th>"+fotter_1+"</th><th>"+fotter_2+"</th><th>"+fotter_3+"</th><th>"+fotter_4+"</th><th>"+fotter_5+"</th><th>"+fotter_6+"</th><th>"+fotter_7+"</th><th>"+fotter_8+"</th><th>"+fotter_9+"</th><th>"+fotter_10+"</th><th>"+fotter_11+"</th>");
                   }
                  //$('#example tbody').empty();
                  $("#location_id").text(data.column);
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


