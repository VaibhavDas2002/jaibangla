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
             
               Block/Municipality Wise Drill-Down Report({{$payment_mode}}) under {{$district_name}}
             
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
            <form method="post" id="register_form" action="{{url('block-drill-down-consolidated-report')}}"  class="submit-once" >
              {{ csrf_field() }}
        
            <input type="hidden" name="payment_mode" id="payment_mode" value="{{$payment_mode}}">
            <input type="hidden" name="district_code" id="district_code" value="{{$district_code}}">

       


            <div class="tab-content" style="margin-top:16px;">

              




             <div class="tab-pane active" id="personal_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Search Criteria</b></h4></div>
               <div class="panel-body">

               

               <div class="row">
               <div class="form-group col-md-4">
                 <label class="">Select Scheme</label>
                 <select name="scheme_id" id="scheme_id" class="form-control" tabindex="1" >
                  <option value="">--All  --</option>
                  @foreach($schemes as $scheme)
                    <option value="{{$scheme->id}}" @if($fill_value_arr['scheme_id']==$scheme->id) selected @endif>{{$scheme->scheme_name}}</option>
                    @endforeach
                 
                </select>
                 <span id="error_scheme_id" class="text-danger"></span>

                </div>
                
               <div class="form-group col-md-4">
                 <label class="">Select Level</label>
                 <select name="rural_urban" id="rural_urban" class="form-control" tabindex="1" >
                   <option value="" @if($fill_value_arr['rural_urban']=='') selected @endif>--All--</option>
                   <option value="Rural" @if($fill_value_arr['rural_urban']=='Rural') selected @endif>Rural</option>
                   <option value="Urban" @if($fill_value_arr['rural_urban']=='Urban') selected @endif>Urban</option>
                 
                </select>
                 <span id="error_rural_urban" class="text-danger"></span>

                </div>
              
                            
             
             
               <div class="form-group col-md-4">
                 <label class="required-field">Select Year</label>
                 <select name="fin_year" id="fin_year" class="form-control" tabindex="6" >
                  <option value="">--Select Financial Year--</option>
                   @foreach(Config::get('constants.fin_year') as $key=>$val)
                    <option value="{{$key}}" @if($fill_value_arr['fin_year']==$key) selected @endif>{{$val}}</option>
                    @endforeach
                </select>
                 <span id="error_fin_year" class="text-danger"></span>

                </div>
                
               
              <div class="form-group col-md-4" id="divUrbanCode">
                <label class="required-field">Select Month</label>
                
                <select name="month" id="month" class="form-control" tabindex="11" >
                  <option value="">--Select Month--</option>
                  @foreach(Config::get('constants.monthlist') as $key=>$val)
                    <option value="{{$key}}" @if($fill_value_arr['month']==$key) selected @endif>{{$val}}</option>
                    @endforeach   
                   
                </select>
                  <span id="error_month" class="text-danger"></span>
              </div>
              
              
                <div class="form-group col-md-4" id="divBodyCode">
                <label class="required-field">Payment Option</label>
                
                <select name="payment_option" id="payment_option" class="form-control" tabindex="16" >
                  <option value="1" @if($fill_value_arr['payment_option']==1) selected @endif>Current Payments</option>
                    <option value="2" @if($fill_value_arr['payment_option']==2) selected @endif>Old Payments</option>
                  
                   
                </select>
                  <span id="error_payment_option" class="text-danger"></span>
              </div>

              
              
                 
               
 
                
              
                  <br />
                  <br />
                   @if( count($schemes)>0)
                <div class="col-md-12" align="center">

                  <button type="submit" name="submit"  id="submitting" value="Submit" class="btn btn-success success btn-lg modal-search form-submitted" >Search </button>
                 
                 <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" id="submit_loader1" width="50px" height="50px" style="display:none;" ></div>
                
                 <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                </div>
                 @endif
                <br />
               </div>
              </div>
             </div>

       <div class="tab-content" style="margin-top:16px;">
 @if( count($schemes)==0)

              <div class="alert alert-danger alert-block">
                            <ul>
                           
                            <li><strong> Not Applicable for any scheme for the payment mode {{$payment_mode}}</strong></li>
                           
                            </ul>
              </div>
            @endif
              
 <div class="alert print-error-msg"  style="display:none;" id="errorDiv">
               <button type="button" class="close"  aria-label="Close" onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
               <ul></ul>
               </div>



             <div class="tab-pane active" id="search_details" >
              <div class="panel panel-default">
               <div class="panel-heading" id="heading_msg"><h4><b>@if($isSubmitted) {{$heading_msg}} @else Search Result @endif</b></h4></div>
               <div class="panel-body">
              @if($last_modified!='')
              <div class="row pull-right">Last Modified:<span style="color:green;font-size:16px;">{{$last_modified}} </span></div><br/>
              @endif

             <table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                  <th width="8%" class="text-left">Level</th>
                  <th width="20%" class="text-left">Block/Municipality Name</th>
                   @if($payment_mode=='IFMS')
                  <th width="12%" class="text-left">Application Uploaded</th>     
                  <th width="12%" class="text-left">Application Verified</th>
                  <th width="12%" class="text-left">Application Approved</th>
                  <th width="12%" class="text-left">Pushed to IFMS</th>
                  <th width="12%" class="text-left">Payment Mandate Generated</th>       
                   @elseif($payment_mode=='SBI')
                  <th width="9%" class="text-left">Application Uploaded</th>     
                  <th width="9%" class="text-left">Application to be Verified</th>
                  <th width="9%" class="text-left">Application to be Approved</th>
                  <th width="9%" class="text-left">Application Approved</th>
                  <th width="9%" class="text-left">Application Uploaded <h6>Selected Month<h6></th>     
                  <th width="9%" class="text-left">Application to be Verified <h6>Selected Month<h6></th>
                  <th width="9%" class="text-left">Application to be Approved <h6>Selected Month<h6></th>
                  <th width="9%" class="text-left">Application Approved <h6>Selected Month<h6></th>
                  <th width="9%" class="text-left">Pushed to SBI</th>  
                    @endif   
            </tr>
        </thead>
         @if($isSubmitted && $inValid==0)
        <tbody>
       
        @foreach($searchResult as $result)
        @php
          $i=1;
        @endphp
           <tr>
            <td>{{$result->level}}</td>
            <td>{{$result->block_ulb_name}}</td>
                
                 @if($payment_mode=='IFMS')
                <td>{{$result->applied}}</td>
                <td>{{$result->verified}}</td>
                <td>{{$result->approved}}</td>
                <td>{{$result->pushed_ifms}}</td>
                <td>{{$result->mandate_generated}}</td>
               
                @elseif($payment_mode=='SBI')
                <td>{{$result->applied}}</td>
                <td>{{$result->to_be_verified}}</td>
                <td>{{$result->to_be_approved}}</td>
                <td>{{$result->approved}}</td>
                <td>{{$result->current_applied}}</td>
                <td>{{$result->current_to_be_verified}}</td>
                <td>{{$result->current_to_be_approved}}</td>
                <td>{{$result->current_approved}}</td>
                <td>{{$result->pushed_sbi }}</td>
                @endif
            </tr>  
             @php
          $i=$i+1;
        @endphp
          @endforeach  
        
        </tbody>
         
        <tfoot>
            <tr>
                <th width="8%" class="text-left"></th>
                <th width="8%" class="text-left"></th>
                @if($payment_mode=='IFMS')
                <th width="9%" class="text-left"></th>
                <th width="9%" class="text-left"></th>
                <th width="9%" class="text-left"></th>
                <th width="9%" class="text-left"></th>
                <th width="9%" class="text-left"></th>
             
              
                @elseif($payment_mode=='SBI')
                 <th width="10%" class="text-left"></th>
                <th width="10%" class="text-left"></th>
                <th width="10%" class="text-left"></th>
                <th width="10%" class="text-left"></th>
                <th width="10%" class="text-left"></th>
                <th width="10%" class="text-left"></th>
                <th width="10%" class="text-left"></th>
                <th width="10%" class="text-left"></th>
                <th width="10%" class="text-left"></th>
               
                @endif
              </tr>
        </tfoot>
         @endif
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
<script src="{{ asset("js/select2.full.min.js") }}"></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
<script src="{{ URL::asset('js/site.js') }}"></script>

<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
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
  var payment_mode="{{$payment_mode}}";
  if(payment_mode=='IFMS'){
    var collength=6;
  }
  else if(payment_mode=='SBI'){
    var collength=10;
  }
  //alert(collength);
    $('#example').dataTable({
                     "paging":   false,
                     "scrollX": true,
                     "ordering": false,
                     "info":     false,
                      "dom": 'Bfrtip',
                      "buttons": [
                                'copy',
                                {
                                    extend: 'excel'
                                }
                                
                    ],
                     "footerCallback": function ( row, data, start, end, display ) {
                        var api = this.api(), data;
                       //console.log(collength);
                        
                        var intVal = function ( i ) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '')*1 :
                                typeof i === 'number' ?
                                    i : 0;
                        };
                      $( api.column( 0 ).footer() ).html('Total');
                       $( api.column( 1 ).footer() ).html('');
                      for(var p=2; p<=collength;p++){
                        var k='total_'+p;
                         k = api
                            .column( p )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                            $( api.column( p ).footer() ).html(k);
                      }              
                    }
                } );
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

