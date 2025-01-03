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
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"> -->
  <!-- Ionicons -->
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"> -->
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
            <div class="box-header with-border">
             <h3 class="box-title"><b>
             
              Get OTP
             
             </b></h3>
                <!-- <p><h3 class="box-title"><b>Bandhu Prakalpa (for SC)</b></h3></p> -->
            </div>

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
               @foreach($errors as $error)
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
            <form method="post" id="register_form" action="{{url('getOtp')}}"  class="submit-once" >
              {{ csrf_field() }}
      

            <div class="tab-content" style="margin-top:16px;">

              




             <div class="tab-pane active" id="personal_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Search Criteria</b></h4></div>
               <div class="panel-body">

               

               <div class="row">
                <div class="form-group col-md-4">
                 <label class="required-field">Enter Mobile No.</label>
                <input type="text" id="mobile_no" name="mobile_no" class="form-control" placeholder="Mobile Number" value="">
                 <span id="error_mobile_no" class="text-danger"></span>

                </div>
              </div>
              <div class="row">
               <div class="form-group col-md-4">
                 <label class="">Select Department</label>
                 <select name="department_id" id="department_id" class="form-control" tabindex="1" >
                  <option value="">--All  --</option>
                  @foreach($departments as $department)
                    <option value="{{$department->id}}">{{$department->name}}</option>
                    @endforeach
                 
                </select>
                 <span id="error_department_id" class="text-danger"></span>

                </div>
                <div class="form-group col-md-4">
                 <label class="required-field">Select Designation</label>
                 <select name="designation_id" id="designation_id" class="form-control" tabindex="1" >
                  <option value="">--All  --</option>
                  @foreach($designations as $designation)
                    <option value="{{$designation->name}}">{{$designation->name}}</option>
                    @endforeach
                 
                </select>
                 <span id="error_designation_id" class="text-danger"></span>

                </div>
               <div class="form-group col-md-4" id="scheme_id_div" style="display:none;">
                 <label class="required-field">Select Scheme</label>
                 <select name="scheme_id" id="scheme_id" class="form-control" tabindex="1" >
                  <option value="">--All  --</option>
                  @foreach($schemes as $scheme)
                    <option value="{{$scheme->id}}" >{{$scheme->scheme_name}}</option>
                    @endforeach
                 
                </select>
                 <span id="error_scheme_id" class="text-danger"></span>

                </div>
                
               
               <div class="form-group col-md-4" id="district_div" style="display:none;">
                 <label class="" id="district_label">District</label>
                 <select name="district" id="district" class="form-control js-district" tabindex="9" >
                  <option value="">--All  --</option>
                   @foreach ($districts as $district)
                  <option value="{{$district->district_code}}" > {{$district->district_name}}</option>
                  @endforeach
                </select>
                 <span id="error_district" class="text-danger"></span>

                </div>

                <div class="form-group col-md-4" id="urban_code_div" style="display:none;">
                <label class="" id="urban_code_label">Rural/ Urban</label>
                
                <select name="urban_code" id="urban_code" class="form-control js-block-subdiv" tabindex="10" >
                  <option value="">--All  --</option>
                  @foreach(Config::get('constants.rural_urban') as $key=>$val)
                  <option value="{{$key}}">{{$val}}</option>
                  @endforeach     
                   
                </select>
                  <span id="error_urban_code" class="text-danger"></span>
              </div>      
             
             
               <div class="form-group col-md-4" id="block_div" style="display:none;">
                <label class="" id="block_label">Block/Subdivision</label>
                
                <select name="block" id="block" class="form-control js-localbody" tabindex="11" >
                  <option value="">--All --</option>
                  
                   
                </select>
                  <span id="error_block" class="text-danger"></span>
              </div>
                
               
          
             
              

              
              
                 
               
 
                
              
                  <br />
                  <br />
               
                <div class="col-md-12" align="center">
                 
                  <button type="button" name="submit"  id="submitting" value="Submit" class="btn btn-success success btn-lg modal-search form-submitted" >Get Otp </button>
                
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



             <div class="tab-pane active" id="search_details" >
              <div class="panel panel-default">
               <div class="panel-heading" id="heading_msg"><h4><b>Search Result</b></h4></div>
               <div class="panel-body">
              
             <table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th id="location_id">UserName</th>
                <th>Email Id</th>
                <th>Designation</th>
                <th>Mobile Number</th>
                <th>OTP</th>
                <th>OTP Last Generated Time</th>
                <th>Department</th>
                <th>Scheme</th>
                <th>District</th>
                <th>Rural/Urban</th>
                <th>Block/Subdivision</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
        <tfoot>
            <tr>
                 <th id="location_id">UserName</th>
                <th>Email Id</th>
                <th>Designation</th>
                <th>Mobile Number</th>
                <th>OTP</th>
                <th>OTP Last Generated Time</th>
                <th>Department</th>
                <th>Scheme</th>
                <th>District</th>
                <th>Rural/Urban</th>
                <th>Block/Subdivision</th>
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

  



           </form>
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
<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>

<script src="{{ URL::asset('js/site.js') }}"></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("js/dataTables.buttons.min.js") }}"></script>
<script src="{{ asset("js/buttons.flash.min.js") }}"></script>
<script src="{{ asset("js/jszip.min.js") }}"></script>
<script src="{{ asset("js/pdfmake.min.js") }}"></script>
<script src="{{ asset("js/vfs_fonts.js") }}"></script>
<script src="{{ asset("js/buttons.html5.min.js") }}"></script>
<script src="{{ asset("js/buttons.print.min.js") }}"></script>

<script>
$(document).ready(function(){
      $('#department_id').change(function() {
      var department_id=$(this).val();
      if(department_id!=''){
        $('#designation_id option[value="Admin"]').prop('disabled', true);
         $('#designation_id option[value="Operator"]').prop('disabled', true);
         $('#designation_id option[value="Verifier"]').prop('disabled', true);
         $('#designation_id option[value="Approver"]').prop('disabled', true);
      }
      else{
        $('#designation_id option[value="Admin"]').prop('disabled', false);
         $('#designation_id option[value="Operator"]').prop('disabled', false);
         $('#designation_id option[value="Verifier"]').prop('disabled', false);
         $('#designation_id option[value="Approver"]').prop('disabled', false);
      }
    });
     $('#designation_id').change(function() {
      var designation_id=$(this).val();
      $("#scheme_id_div").hide();
      $("#district_div").hide();
      $("#urban_code_div").hide();
      $("#block_div").hide();
      $("#district_label").removeClass("required-field");
      $("#urban_code_label").removeClass("required-field");
      $("#block_label").removeClass("required-field");
      if(designation_id=='Operator'){
       $("#scheme_id_div").show();
       $("#district_div").show();
       $("#urban_code_div").show();
       $("#block_div").show();
       $("#district_label").addClass("required-field");
       $("#urban_code_label").addClass("required-field");
       $("#block_label").addClass("required-field");
      }
      else if(designation_id=='Verifier'){
       $("#scheme_id_div").show();
       $("#district_div").show();
       $("#urban_code_div").show();
       $("#block_div").show();
       $("#district_label").addClass("required-field");
       $("#urban_code_label").addClass("required-field");
       $("#block_label").addClass("required-field");

      }
      else if(designation_id=='Approver'){
       $("#scheme_id_div").show();
       $("#district_div").show();
       $("#urban_code_div").hide();
       $("#block_div").hide();
      $("#district_label").addClass("required-field");
      }
      else{
      $("#scheme_id_div").hide();
      $("#district_div").hide();
      $("#urban_code_div").hide();
      $("#block_div").hide();
      $("#scheme_id").val('');
      $("#district").val('');
      $("#urban_code").val('');
      $("#block").val('');
      }
    });
     $('#district').change(function() {
        $('#block').html('<option value="">--Select --</option>');
        $('#urban_code').val('');
    });
     $('#urban_code').change(function() {
       var urban_code=$(this).val();
        if(urban_code==''){
           $("#rural_urban_fk").val('');
           $("#block").val('');
        }
        $('#block').html('<option value="">--Select --</option>');
        select_district_code= $('#district').val();
        if(select_district_code==''){
               alert('Please Select District First');
               $("#district").focus();
               $("#urban_code").val('');
        }
        else{
        select_body_type= urban_code;
        var htmlOption='<option value="">--Select--</option>';
        if(select_body_type==2){
            $.each(blocks, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        }else if(select_body_type==1){
            $.each(ulbs, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        }    
        
        $('#block').html(htmlOption);
        }
    });
$('#block').change(function() {
      var block=$(this).val();
      var district=$("#district_code_fk").val();
      var urban_code=$("#rural_urban_fk").val();
      if(district==''){
        alert('Please Select District First');
        $("#block").val('');
        //$("#block_munc_corp_code_fk").val('');
        $("#district").focus();
    }
    if(urban_code==''){
        alert('Please Select Rural/Urban First');
        $("#block").val('');
        $("#block_munc_corp_code_fk").val('');
        $("#urban_code").focus();
    }
    if(block!=''){
      $("#block_munc_corp_code_fk").val(block);
    }
    else{
       $("#block_munc_corp_code_fk").val('');
    }
    });
$('.modal-search').on('click',function(){
  var mobile_no=$('#mobile_no').val();
  var department_id=$('#department_id').val();
  var designation_id=$('#designation_id').val();
  var scheme_id=$('#scheme_id').val();
  var district=$('#district').val();
  var urban_code=$('#urban_code').val();
  var block=$('#block').val();
  
     $("#submit_loader1").show();
     $("#submitting").hide();
     $('#search_details').hide();
        $.ajax({
                type: 'post',
                dataType:'json',
                url: '{{ url('getOtp_post') }}',
                data: {
                  mobile_no: mobile_no,
                  department_id: department_id,
                  designation_id: designation_id,
                  scheme_id: scheme_id,
                  district: district,
                  urban_code: urban_code,
                  block: block,
                  _token: '{{ csrf_token() }}',
                },
                success: function (data) {
                  $('#search_details').show();
                  //console.log(data.row_data);
                  if(data.return_status){
                    $("#heading_msg").html("<h4><b>"+data.heading_msg+"</b></h4>");
                    var fileter_status=data.fileter_status;
                    $("#example > thead").html("");
                    $("#example > tfoot").html("");
                    if ( $.fn.DataTable.isDataTable('#example') ) {
                    $('#example').DataTable().destroy();
                  }
                   if(fileter_status==1){
                       $("#example > thead").html("<tr><th>UserName</th><th>Email Id</th><th>Designation</th><th>Mobile Number</th><th>OTP</th><th>OTP Last Generated Time</th><th>Department</th><th>Scheme</th><th>District</th><th>Rural/Urban</th><th>Block/Subdivision</th></tr>");
                       $("#example > tfoot").html("<tr><th>UserName</th><th>Email Id</th><th>Designation</th><th>Mobile Number</th><th>OTP</th><th>OTP Last Generated Time</th><th>Department</th><th>Scheme</th><th>District</th><th>Rural/Urban</th><th>Block/Subdivision</th></tr>");
                    }
                    else{
                     $("#example > thead").html("<tr><th>UserName</th><th>>Email Id</th><th>Designation</th><th>Mobile Number</th><th>OTP</th><th>OTP Last Generated Time</th><th>Department</th><th>Scheme</th><th>District</th><th>Rural/Urban</th><th>Block/Subdivision</th></tr>");
                     $("#example > tfoot").html("<tr><th id='location_id'>Scheme</th><th>Total Approved Beneficiary</th><th>Beneficiary added to Lot</th><th>Pending Beneficiary Yet to to Lotted</th><th>Successfull Payments</th><th>Failed Payments</th><th>Pending Beneficiary</th></tr>");
                    }
                    $("#example > tbody").html("");
                   var table = $("#example tbody");
                   $.each(data.row_data, function(i, item) {
                     if(fileter_status==1){
                     table.append("<tr><td>"+item.username+"</td><td>"+item.email+"</td><td>"+item.designation_id+"</td><td>"+item.mobile_no+"</td><td>"+item.login_otp+"</td><td>"+item.otp_time+"</td><td>"+item.department_name+"</td><td>"+item.scheme_name+"</td><td>"+item.district_name+"</td><td>"+item.rural_urban_name+"</td><td>"+item.block_subdiv_name+"</td></tr>");
                     }
                     else{
                     table.append("<tr><td>"+item.username+"</td><td>"+item.email+"</td><td>"+item.designation_id+"</td><td>"+item.mobile_no+"</td><td>"+item.login_otp+"</td><td>"+item.otp_time+"</td><td>"+item.department_name+"</td><td>"+item.scheme_name+"</td><td>"+item.district_name+"</td><td>"+item.rural_urban_name+"</td><td>"+item.block_subdiv_name+"</td></tr>");
                     }
                  });
                  

                  //$('#example tbody').empty();
                   $("#example").show();
                   $('#example').dataTable({
                     "paging":   false,
                     "scrollX": true,
                     "ordering": false,
                     "info":     false,
                      "dom": 'Bfrtip',
                      "buttons": [
                                'copy',
                                {
                                    extend: 'excel',
                                    messageTop: data.heading_msg
                                }
                                
                            ]
                  });
                  }
                  else{
                     $("#example").hide();
                     console.log(data.return_msg);
                     printMsg(data.return_msg,'0','errorDiv');
                  }
                  $("#submit_loader1").hide();
                  $("#submitting").show();

                },
                error: function (ex) {
                  $("#submit_loader1").hide();
                  //$("#submitting").hide();
                  $("#submitting").show();
                  //alert('Something wrong..may be session timeout. please logout and then login again');
                  //location.reload();
                   
                }
              });
  
});
});
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


