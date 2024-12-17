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
            
             Sbi Lot and File Movement Status
            
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
            <form method="post" id="register_form" action="{{url('lot-file-movement-check-sbi')}}"  class="submit-once" >
              {{ csrf_field() }}
       


            <div class="tab-content" style="margin-top:16px;">

              




             <div class="tab-pane active" id="personal_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Search Criteria</b></h4></div>
               <div class="panel-body">

               

               <div class="row">
               <div class="form-group col-md-4">
                 <label class="required-field">Select Scheme</label>
                 <select class="form-control"  name="scheme_id" id='scheme_id'>
                                    <option value="">--Select--</option>
                                    @foreach($schemes_arr as $scheme)
                                    <option value="{{$scheme->id}}" @if(!empty($fill_value_arr['scheme_id']) && $fill_value_arr['scheme_id'] == $scheme->id)  selected  @endif>{{$scheme->name}}</option>
                                    @endforeach
                                    <!-- <option value="State">State</option> -->
                                    
                                         
                </select>
                 <span id="error_scheme_code" class="text-danger"></span>

                </div>
                
                <div class="form-group col-md-4">
                 <label class="required-field">Financial Year</label>
                 <select class="form-control"  name="fin_year" id='fin_year'>
                                    <option value="">--Select Financial Year--</option>
                                      @foreach(Config::get('constants.fin_year') as $key=>$val)
                                    <option value="{{$key}}" @if(!empty($fill_value_arr['fin_year']) && $fill_value_arr['fin_year'] == $key)  selected  @endif>{{$val}}</option>
                                    @endforeach
                                    <!-- <option value="State">State</option> -->
                                    
                                         
                </select>
                 <span id="error_fin_year" class="text-danger"></span>

                </div>
                 <div class="form-group col-md-4">
                 <label class="required-field">Select Month</label>
                 <select class="form-control"  name="month" id='month'>
                                    <option value="">--Select Month--</option>
                                    @foreach(Config::get('constants.monthlist') as $key=>$val)
                                    <option value="{{$key}}" @if(!empty($fill_value_arr['month']) && $fill_value_arr['month'] == $key)  selected  @endif>{{$val}}</option>
                                    @endforeach
                                  
                                    
                                         
                  </select>
                 <span id="error_month" class="text-danger"></span>

                </div>
              
                            
             <div class="form-group col-md-4">
                 <label>Enter Lot No.</label>
                <input type="text" id="lot_no" name="lot_no" class="form-control" placeholder="Lot Number" value="@if (!empty($fill_value_arr['lot_no'])) {{$fill_value_arr['lot_no']}} @endif">
                 <span id="error_lot_no" class="text-danger"></span>

                </div>
                <div class="form-group col-md-4">
                 <label>Enter Debit Reference</label>
               <input type="text" id="debit_reference" name="debit_reference" class="form-control" placeholder="Debit Reference" value="@if (!empty($fill_value_arr['debit_reference'])) {{$fill_value_arr['debit_reference']}} @endif">
                 <span id="error_debit_reference" class="text-danger"></span>

                </div>
              
              
               
 
                
              
                  <br />
                  <br />
                <div class="col-md-12" align="center">

                  <button type="submit"  name="submit" id="submitting" value="Submit" class="btn btn-success success btn-lg modal-search form-submitted" >Submit </button>
                 
                 <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" id="submit_loader1" width="50px" height="50px" style="display:none;" ></div>
                
                 <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                </div>
                <br />
               </div>
              </div>
             </div>
 @if($isSubmitted)
       <div class="tab-content" style="margin-top:16px;">

              




             <div class="tab-pane active" id="search_details" >
              <div class="panel panel-default">
               <div class="panel-heading" id="heading_msg"><h4><b>Search Result</b></h4></div>
               <div class="panel-body">

               <div class="alert print-error-msg"  style="display:none;" id="errorDiv">
               <button type="button" class="close"  aria-label="Close" onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
               <ul></ul>
               </div>

             <table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Serial No.</th>
                <th>Lot No.</th>
                <th>Debit Reference</th>
                <th>Lot Status</th>
                <th colspan="3">To Process</th>
                <th colspan="2">Acknowledgement</th>
                <th colspan="2">Response</th>
                <th>Comments</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Jai Bangla</th>
                <th>Jai Bangla Picked</th>
                <th>SBI</th>
                <th>Jai Bangla Picked</th>
                <th>SBI</th>
                <th>Jai Bangla Picked</th>
                <th>SBI</th>
                
            </tr>
        </thead>
        <tbody>
        @foreach($searchResult as $result)
        @php
          $i=1;
        @endphp
           <tr>
               <td>{{$i}}</td>
                <td>{{$result['lot_no']}}</td>
                <td>{{$result['debit_reference']}}</td>
                <td>{{$result['lot_status']}}</td>
                <td>{{$result['exists_toprocess_local']}}</td>
                <td>{{$result['exists_toprocess_local_picked']}}</td>
                <td>{{$result['exists_toprocess_server']}}</td>
                <td>{{$result['exists_ack_local_picked']}}</td>
                <td>{{$result['exists_ack_server']}}</td>
                <td>{{$result['exists_response_local_picked']}}</td>
                <td>{{$result['exists_response_server']}}</td>
                <td>{{$result['message']}}</td>
            </tr>  
             @php
          $i=$i+1;
        @endphp
          @endforeach  
            
        </tbody>
       
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
       @endif
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
    $('#example').dataTable({
                     "paging":   false,
                     "ordering": false,
                     "info":     false,
                      "dom": 'Bfrtip',
                      "buttons": [
                                'copy',
                                {
                                    extend: 'excel'
                                },
                                {
                                    extend: 'pdf'
                                }
                                
                            ]
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


