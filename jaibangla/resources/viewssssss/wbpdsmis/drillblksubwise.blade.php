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
            <form method="post" id="register_form"   class="submit-once" >
              {{ csrf_field() }}
        

     


            <div class="tab-content" style="margin-top:16px;">

              




             <div class="tab-pane active" id="personal_details">
              <div class="panel panel-default">
               
               <div class="panel-body">

               
               <div class="row">
      <div class="col-md-2">
        <a href="{{$backurl}}"><img width="50px;" style="pull-left" src="{{ asset("images/back.png") }}" alt="Back" /></a></div>
              <div class="col-md-8">
                <h3 style="text-align: center;">Scheme:<span style="color:red;">{{$scheme_name}}</span></h3>
                <h3 style="text-align: center;">District:<span style="color:red;">{{$district_name}}</span></h3>

              </div>


            </div>
              
               
              
                            
             
             
                <br />
               </div>
              </div>
             </div>
             <form action="applicationListExcel" method="post">
             <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          </form>  
       <div class="tab-content" style="margin-top:16px;">

              
 <div class="alert print-error-msg"  style="display:none;" id="errorDiv">
               <button type="button" class="close"  aria-label="Close" onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
               <ul></ul>
               </div>



             <div class="tab-pane active" id="search_details" >
              <div class="panel panel-default">
               <div class="panel-heading" id="heading_msg"><h4><b>{{$heading_msg}}</b></h4></div>
               <div class="panel-body">

                                  <div class="pull-right" id="report_generation_text">Report Generated on:<b><?php echo $r_time; ?></b></div>

<br/><br/><br/>
<button class="btn btn-info exportToExcel" type="button" >Export to Excel</button><br/><br/><br/>
<div id="divScrool"> 
             <table id="example" class="table table-striped table-bordered table2excel" style="width:100%">
         <thead>
         <tr>
              <td colspan="5" align="center" style="display:none;" id="heading_excel">{{$heading_msg}}</td>
              </tr> 
              <tr> 
              <th>Sl No.</th>
              <th>Block/Sub Division</th>
              <th>Total Beneficiary</th>
              <th>Beneficiary having 12 digit Aadhaar no.</th>
              <th>Sent to WBPDS for Aadhaar Verification</th>
              <th>No Valid Response Recieved from WBPDS</th>
              <th>Response Recieved from WBPDS</th>
              <th>Name Matched</th>
              <th>Name Not Matched</th>
              </tr>
              @if(count($result)>0)
              @php
              $slno=1;
              $total1=0;$total2=0;$total3=0;$total4=0;$total5=0;$total6=0;$total7=0;
              @endphp
              @foreach($result as $arr)
              
              <tr>
               <td>{{$slno}}</td> 
               <td><a  href="{{ url('/wbpdsapplicantreport')}}?scheme_id={{$scheme_id}}&code={{$arr->location_id}}&type={{$arr->type}}">{{$arr->location_name}}</a></td> 
               <td>{{$arr->total_applicant}}</td> 
               <td>{{$arr->total_not_sent}}</td>
               <td>{{$arr->total_sent}}</td> 
               <td>{{$arr->total_not_received}}</td>
               <td>{{$arr->total_received}}</td> 
               <td>{{$arr->total_name_same}}</td> 
               <td>{{$arr->total_name_differ}}</td> 
               @php
               $slno=$slno+1;
               $total1=$total1+$arr->total_applicant;
               $total2=$total2+$arr->total_not_sent;
               $total3=$total3+$arr->total_sent;
               $total4=$total4+$arr->total_not_received;
               $total5=$total5+$arr->total_received;
               $total6=$total6+$arr->total_name_same;
               $total7=$total7+$arr->total_name_differ;
               @endphp
              </tr>
              @endforeach
              <tfoot>
                <tr>
                <th></th><th>Total</th>
                <th>{{$total1}}</th>
                <th>{{$total2}}</th>
                <th>{{$total3}}</th>
                <th>{{$total4}}</th>
                <th>{{$total5}}</th>
                <th>{{$total6}}</th>
                <th>{{$total7}}</th>
                </tr>
                <tr>
                <td colspan="9" align="center" style="display:none;" id="fotter_excel">Report Renerated on {{$r_time}}</td>
               </tr> 
              </tfoot>
              @else
              <tr><td colspan="9">No Result Found</td></tr>
              
              @endif
             
            </thead>
        <tbody>
            
        </tbody>
        
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

$(document).ready(function(){
  var c_time='{{$c_time}}';
  var scheme_name='{{$scheme_name}}';
  var district_name='{{$district_name}}';
  var scheme_name='{{$scheme_name}}';
  $('.sidebar-menu li').removeClass('active');
  $('.sidebar-menu #lk-main').addClass("active"); 
  $('.sidebar-menu #dupBankmis').addClass("active"); 
  $("#heading_excel").hide();
  $("#fotter_excel").hide();
  $(".exportToExcel").click(function(e){
       $("#heading_excel").show();
       $("#fotter_excel").show();
			$(".table2excel").table2excel({
    // exclude CSS class
    exclude: ".noExl",
    name: "Worksheet Name",
    filename: "Block/Sub Division wise Aadhaar POC WITH WBPDS of the District "+district_name+" for the Scheme "+scheme_name+"_"+c_time,
    fileext: ".xls" // file extension
  }); 
  $("#heading_excel").hide();
  $("#fotter_excel").hide();
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


