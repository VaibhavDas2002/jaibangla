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
 #search{
   margin-top:20px;
 }
.searchResult{
  display:none;
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
             <h3 class="box-title"><b>Government of West Bengal Jai Bangla Pension Scheme</b></h3>
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
               @foreach($errors->all() as $error)
               <li><strong> {{ $error }}</strong></li>
               @endforeach
              </ul>
            </div>
            @endif

            <div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">System Generated Duplicate Record Rejection</div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <h1>Please select the scheme from dropdown list and confirm to reject system generated duplicate records.</h1>
                    </div>
                    <div  class="col-md-12">   <br/><br/><br/>    </div>
                    <div  class="col-md-12">
                        <label for="scheme" class="col-md-3 control-label">Select Scheme</label>

                            <div class="col-md-4">
                                <select class="form-control" name="scheme_code"  id="scheme_code">
                                    <option value="">--Select--</option>
                                    
                                    <option value="10">Old Age Pension [WCD]</option>
                                    <option value="11">Widow Pension [WCD]</option>
                                    
                                </select>
                                <span id="error_scheme_code" class="text-danger"></span>
                            </div>
                            <br/><br/><br/>
                    </div>
                        
                    <div class="col-md-9 col-md-offset-3">
                        <button type="button" id="form_reject" class="btn btn-primary">Reject System Generated Duplicates</button>    
                        <form action="system-rejected-generate_excel" method="POST">  
                            <input name="_token" type="hidden" value="{{ csrf_token() }}">
                            <input type="hidden" name="rej_scheme_code" id="rej_scheme_code"/>
                            <input type="submit" id="generate_excel" class="btn btn-success" style="display:none" value="Generate Rejected Beneficiary Excel">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



    
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            
          </div>
          <!-- /.box -->
        </div>
      </div>      
</section>
  </div>
  
  @include('layouts.footer')
  
<!-- REQUIRED JS SCRIPTS -->

 <!-- jQuery 2.1.3 -->
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>


<script>
$('#scheme_code').on('change',function(){
    $('#generate_excel').hide();
    $('#rej_scheme_code').val($('#scheme_code').val());
});

$('#form_reject').on('click',function(){
    
    $('#generate_excel').hide();
    var error_comments = '';
    var error_scheme_code = '';
    
    if($.trim($('#scheme_code').val()).length == 0)
    {
        error_scheme_code = 'Scheme is required';
        $('#error_scheme_code').text(error_scheme_code);
        $('#scheme_code').addClass('has-error');
    }
    else
    {
        error_scheme_code = '';
        $('#error_scheme_code').text(error_scheme_code);
        $('#scheme_code').removeClass('has-error');
    }        
    if(error_scheme_code != '' )
    {
        return false;
    }
    else
    {
        if(confirm("Please confirm to reject duplicates")){
            $.ajax({
            type: 'GET',
            url: '{{ url('system-reject-duplicate') }}',
            data: {
                _token: '{{ csrf_token() }}',
                scheme_code: $('#scheme_code').val(),
            },
            success: function (datas) {
              alert("Duplicates rejected successfully. Please generate rejected beneficiary list in excel");
              $("#rej_scheme_code").val($('#scheme_code').val());
              $('#generate_excel').show();
            },
            error: function (ex) {
            }
            });
            
        }
        else{
            alert("Operation Cancelled");
        }
    }
});

// $('#generate_excel').on('click',function(){
//     alert('test');
//     if($.trim($('#scheme_code').val()).length != 0)
//     {
//         $.ajax({
//         type: 'GET',
//         url: '{{ url('system-rejected-generate_excel') }}',
//         data: {
//             _token: '{{ csrf_token() }}',
//             scheme_code: $('#scheme_code').val(),
//         },
//         success: function (datas) {
//             $('#generate_excel').show();
//         },
//         error: function (ex) {
//         }
//         });
//     }
// });

  
</script>
</body>
</html>


