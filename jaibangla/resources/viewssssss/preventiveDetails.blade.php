<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>EM | Empployee Management</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

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

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
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
  <div class="content-wrapper" style="min-height: 956.3px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Preventive Arrest Details        
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Forms</a></li>
        <li class="active">Editors</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-12">
        <form method="POST" action="{{url('savePreventiveArrest')}}" >          
          <input type="hidden" name="_token" value="{{ csrf_token() }}">

              @if ($message = Session::get('success'))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
              </div>
              @endif
         
            <div class="box box-info">
              <div class="box-header">
                <h3 class="box-title">Preventive Warrent Details         
                </h3>
                <!-- tools box -->
                <div class="pull-right box-tools">
                  <button type="button" class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                    <i class="fa fa-minus"></i></button>
                 
                </div>
                <!-- /. tools -->
              </div>
              <!-- /.box-header -->
              <div class="box-body pad">
                <div class="form-group">
                    <label for="preventive_34_police_act">34 Police Act</label>
                    <input class="form-control" id="preventive_34_police_act" value="" name="preventive_34_police_act" placeholder="34 Police Act" type="text">
                </div>
                <div class="form-group">
                    <label for="preventive_290_ipc">290 IPC</label>
                    <input class="form-control" id="preventive_290_ipc" value="" name="preventive_290_ipc" placeholder="151/107 Cr.PC" type="text">
                </div>
                <div class="form-group">
                    <label for="preventive_151_107_crpc">151/107 Cr.PC</label>
                    <input class="form-control" id="preventive_151_107_crpc" value="" name="preventive_151_107_crpc" placeholder="Phensedyl/ Codeine Mixture(Ltr)" type="text">
                </div>

                <div class="form-group">
                    <label for="preventive_109_crpc">109 Cr.PC</label>
                    <input class="form-control" id="preventive_109_crpc" value="" name="preventive_109_crpc" placeholder="109 Cr.PC" type="text">
                </div>
                <div class="form-group">
                    <label for="preventive_110_crpc">110 Cr.PC</label>
                    <input class="form-control" id="preventive_110_crpc" value="" name="preventive_110_crpc" placeholder="110 Cr.PC" type="text">
                </div>

                <div class="form-group">
                    <label for="preventive_41_crpc">41 Cr.PC</label>
                    <input class="form-control" id="preventive_41_crpc" value="" name="preventive_41_crpc" placeholder="41 Cr.PC" type="text">
                </div>
                <div class="form-group">
                    <label for="wbgpc_act">WBGPC Act</label>
                    <input class="form-control" id="wbgpc_act" value="" name="wbgpc_act" placeholder="WBGPC Act" type="text">
                </div>



                <div class="form-group">
                    <label for="preventive_others">Others</label>
                    <input class="form-control" id="preventive_others" value="" name="preventive_others" placeholder="Phensedyl/ Codeine Mixture(Ltr)" type="text">
                </div>

                 <div class="form-group">
                    <label for="wbgpc_act">Total Preventive Arrest(Will be auto generated)</label>
                    <input class="form-control" id="wbgpc_act" value="" name="wbgpc_act" placeholder="WBGPC Act" type="text">
                </div>
                                           
              </div>
            </div><!-- ./box -->
           
         
          <div class="box-footer">
                
                <button type="submit" class="btn btn-info pull-right" name="submitReport" value="submitReport">Preventive Warrent Submit</button>
          </div>
          </form>
        </div>
        <!-- /.col-->
      </div>
      <!-- ./row -->     
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Footer -->
  @include('layouts.footer')
  
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

 <!-- jQuery 2.1.3 -->
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
