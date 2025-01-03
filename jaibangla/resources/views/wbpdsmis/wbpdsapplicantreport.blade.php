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

   <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
   <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">

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
        
              <input type="hidden" id="type" name="type" value="{{$type}}"/>
              <input type="hidden" id="code" name="code" value="{{$code}}"/>
              <input type="hidden" id="scheme_id" name="scheme_id" value="{{$scheme_id}}"/>
            <div class="tab-content" style="margin-top:16px;">

              




             <div class="tab-pane active" id="personal_details">
              <div class="panel panel-default">
               
               <div class="panel-body">

               
               <div class="row">
      <div class="col-md-2">
        <a href="{{$backurl}}"><img width="50px;" style="pull-left" src="{{ asset("images/back.png") }}" alt="Back" /></a></div>
              <div class="col-md-8">
                <h3 style="text-align: center;">Scheme:<span style="color:red;">{{$scheme_name}}</span></h3>
                @if($designation_id=='HOD' || $designation_id=='Approver')
                <h3 style="text-align: center;">District:<span style="color:red;">{{$district_name}}</span></h3>
                <h3 style="text-align: center;">{{$blksubdivtxt}}:<span style="color:red;">{{$location_name}}</span></h3>
                @endif
              </div>


            </div>
              
               
            <div class="form-group col-md-4">
         <label class=" control-label">Filter Type</label>
         <select name="filter_status" id="filter_status" class="form-control full-width" >
                  <option value="" selected>-----All----</option>
                  @if($designation_id=='HOD' || $designation_id=='Approver')
                  <option value="5">Name Match</option> 
                  @endif  
                  <option value="6">Name Not Match</option>   
                  <option value="7">No Valid Response Recieved from WBPDS</option>  
                </select>
          </div>
                            
          <div class="form-group col-md-4">
            <button type="button" name="filter" id="filter" class="btn btn-info" style="margin-top:20px;">Filter</button>
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

                                  <div class="pull-right" id="report_generation_text">Report Generated on:<b><?php echo date("l jS \of F Y h:i:s A"); ?></b></div>

<br/><br/><br/>
<div id="divScrool"> 
             <table id="example" class="table table-striped table-bordered" style="width:100%">
         <thead>
              
              <tr> 
              <th>Beneficiary ID</th>
              <th>AadharNO</th>
              <th>Name as in Aadhar</th>
              <th>Name as in Jai Bangla</th>
              <th>Name is Match?</th>
              @if($designation_id=='Verifier')
              <th>Action</th>
              @endif
              </tr>
             
            </thead>
        <tbody>
            
        </tbody>
        
    </table>
    <div class="row">
            
            <div class="col-sm-7">
               <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                
              </div>
            </div>
  </div>
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
<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>

<script>

  $(document).ready(function() {
    var base_url='{{ url('/') }}';
   var filter_status=$("#filter_status").val();
  fill_datatable(filter_status);
  function fill_datatable(filter_status = ''){
       var scheme_id=$("#scheme_id").val();
       var code=$("#code").val();
       var type=$("#type").val();
        var dataTable=$('#example').DataTable( {
      //dom: 'Bfrtip',
      paging: true,
      ordering: false,
      pageLength:100,
      lengthMenu: [[20, 50,100,500,1000, -1], [20, 50,100,500,1000, 'All']],
      processing: true,
      serverSide: true,
      ajax:{
            url: "{{ url('wbpdsapplicantreport') }}",
            type: "GET",
            data:function(d){
                 d.scheme_id= scheme_id,
                 d.code= code,
                 d.type= type,
                 d.filter_status= filter_status,
                 d._token= "{{csrf_token()}}"
            },
            error: function (ex) {
              //console.log(ex);
             //alert('Session time out..Please login again');
           // window.location.href=base_url;
           }                       
      },
      columns: [
                
        { "data": "application_id" },
        { "data": "aadhar_no_f" },
        { "data": "name_as_in_aadhar"},
        { "data": "jb_ben_name_new" },
        { "data": "is_match" },
        @if($designation_id=='Verifier')
        { "data": "view" },
        @endif
               

      ],          

    
    } );


   }

    $('#filter').click(function(){
        var filter_status = $('#filter_status').val();
        $('#example').DataTable().destroy();
        fill_datatable(filter_status);
      
    });

  } );
</script>
</body>
</html>


