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
        Cases/FIR Summary Details        
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
        <form method="POST" action="{{url('postData')}}" >          
          <input type="hidden" name="_token" value="{{ csrf_token() }}">

              @if ($message = Session::get('success'))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
              </div>
              @endif
          @if(!$sitationreportData->isEmpty())
          @foreach($sitationreportData as $record)
          <div class="box box-info">

            <div class="box-header">
              <h3 class="box-title">General Report                
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
                <div class="form-group col-md-4">
                  <label for="dacoity">Dacoity</label>
                  <input class="form-control" id="dacoity" value="{{$record->dacoity}}" name="dacoity" placeholder="Dacoity" type="text">
                </div>
              
                <div class="form-group col-md-4">
                  <label for="robbery">Robbery</label>
                  <input class="form-control" id="robbery" value="{{$record->robbery}}" name="robbery" placeholder="Robbery" type="text">
                </div>
                <div class="form-group col-md-4">
                  <label for="burglary">Burglary</label>
                  <input class="form-control" id="burglary" value="{{$record->burglary}}" name="burglary" placeholder="Burglary" type="text">
                </div>
                <div class="form-group col-md-4">
                  <label for="theft">Theft</label>
                  <input class="form-control" id="theft" value="{{$record->theft}}" name="theft" placeholder="Theft" type="text">
                </div>

                <div class="form-group col-md-4">
                  <label for="murder"> Murder</label>
                  <input class="form-control" id="murder" value="{{$record->political_murder}}" name="murder" placeholder="Murder" type="text">
                </div>
                
                <div class="form-group col-md-4">
                  <label for="rioting">Rioting</label>
                  <input class="form-control" id="rioting" value="{{$record->rioting}}" name="rioting" placeholder="Rioting" type="text">
                </div>
                <div class="form-group col-md-4">
                  <label for="vandalism_in_hospital">Valdalism in Hospital</label>
                  <input class="form-control" id="vandalism_in_hospital" value="{{$record->vandalism_in_hospital}}" name="vandalism_in_hospital" placeholder="Valdalism in Hospital" type="text">
                </div>
                <div class="form-group col-md-4">
                  <label for="crime_against_women">Crime Against Women</label>
                  <input class="form-control"  id="crime_against_women" value="" name="crime_against_women" placeholder="Crime Against Women" type="text">
                </div>
                
                <div class="form-group col-md-4">
                  <label for="rape">Rape</label>
                  <input class="form-control" id="rape" value="{{$record->rape}}" name="rape" placeholder="Rape" type="text">
                </div>

                <div class="form-group col-md-4">
                  <label for="rape">Human Trafficking</label>
                  <input class="form-control" id="rape" value="" name="rape" placeholder="Human Trafficking" type="text">
                </div>

                <div class="form-group col-md-4">
                  <label for="pocso">POCSO Act </label>
                  <input class="form-control" id="pocso" value="{{$record->pocso}}" name="pocso" placeholder="POCSO" type="text">
                </div>

                <div class="form-group col-md-4">
                  <label for="assault_on_police">Assult on Police</label>
                  <input class="form-control" id="assault_on_police" value="{{$record->assault_on_police}}" name="assault_on_police" placeholder="Assult on Police" type="text">
                </div>
               
                <div class="form-group col-md-4">
                  <label for="ndps">NDPS Act</label>
                  <input class="form-control" id="ndps" value="{{$record->ndps}}" name="ndps" placeholder="NDPS" type="text">
                </div>
                <div class="form-group col-md-4">
                  <label for="ficn">FICN</label>
                  <input class="form-control" id="ficn" value="{{$record->ficn}}" name="ficn" placeholder="FICN" type="text">
                </div>
               
                
                <div class="form-group col-md-4">
                  <label for="rta">Road Traffic Accident</label>
                  <input class="form-control" id="rta" value="{{$record->rta}}" name="rta" placeholder="RTA" type="text">
                </div>

                <div class="form-group col-md-4">
                  <label for="rta">Bengal Excise Act</label>
                  <input class="form-control" id="rta" value="" name="bengal_excise_act" placeholder="RTA" type="text">
                </div>

                <div class="form-group col-md-4">
                  <label for="others">Others</label>
                  <input class="form-control" id="others" value="{{$record->others}}" name="others" placeholder="Others" type="text">
                </div>
                
              
            </div>
            
          </div><!-- ./box -->

        

          @endforeach

          @else
          
          <div class="box box-info">

            <div class="box-header">
              <h3 class="box-title">General Report                
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
                <div class="form-group col-md-4">
                  <label for="dacoity">Dacoity</label>
                  <input class="form-control" id="dacoity" value="" name="dacoity" placeholder="Dacoity" type="text">
                </div>
              
                <div class="form-group col-md-4">
                  <label for="robbery">Robbery</label>
                  <input class="form-control" id="robbery" value="" name="robbery" placeholder="Robbery" type="text">
                </div>
                <div class="form-group col-md-4">
                  <label for="burglary">Burglary</label>
                  <input class="form-control" id="burglary" value="" name="burglary" placeholder="Burglary" type="text">
                </div>
                <div class="form-group col-md-4">
                  <label for="theft">Theft</label>
                  <input class="form-control" id="theft" value="" name="theft" placeholder="Theft" type="text">
                </div>

                <div class="form-group col-md-4">
                  <label for="murder"> Murder</label>
                  <input class="form-control" id="murder" value="" name="murder" placeholder="Murder" type="text">
                </div>
                
                <div class="form-group col-md-4">
                  <label for="rioting">Rioting</label>
                  <input class="form-control" id="rioting" value="" name="rioting" placeholder="Rioting" type="text">
                </div>
                <div class="form-group col-md-4">
                  <label for="vandalism_in_hospital">Valdalism in Hospital</label>
                  <input class="form-control" id="vandalism_in_hospital" value="" name="vandalism_in_hospital" placeholder="Valdalism in Hospital" type="text">
                </div>
                <div class="form-group col-md-4">
                  <label for="crime_against_women">Crime Against Women</label>
                  <input class="form-control"  id="crime_against_women" value="" name="crime_against_women" placeholder="Crime Against Women" type="text">
                </div>
                
                <div class="form-group col-md-4">
                  <label for="rape">Rape</label>
                  <input class="form-control" id="rape" value="" name="rape" placeholder="Rape" type="text">
                </div>

                <div class="form-group col-md-4">
                  <label for="rape">Human Trafficking</label>
                  <input class="form-control" id="rape" value="" name="rape" placeholder="Human Trafficking" type="text">
                </div>

                <div class="form-group col-md-4">
                  <label for="pocso">POCSO Act </label>
                  <input class="form-control" id="pocso" value="" name="pocso" placeholder="POCSO" type="text">
                </div>

                <div class="form-group col-md-4">
                  <label for="assault_on_police">Assult on Police</label>
                  <input class="form-control" id="assault_on_police" value="" name="assault_on_police" placeholder="Assult on Police" type="text">
                </div>
               
                <div class="form-group col-md-4">
                  <label for="ndps">NDPS Act</label>
                  <input class="form-control" id="ndps" value="" name="ndps" placeholder="NDPS" type="text">
                </div>
                <div class="form-group col-md-4">
                  <label for="ficn">FICN</label>
                  <input class="form-control" id="ficn" value="" name="ficn" placeholder="FICN" type="text">
                </div>
               
                
                <div class="form-group col-md-4">
                  <label for="rta">Road Traffic Accident</label>
                  <input class="form-control" id="rta" value="" name="rta" placeholder="RTA" type="text">
                </div>

                <div class="form-group col-md-4">
                  <label for="rta">Bengal Excise Act</label>
                  <input class="form-control" id="rta" value="" name="bengal_excise_act" placeholder="RTA" type="text">
                </div>

                <div class="form-group col-md-4">
                  <label for="others">Others</label>
                  <input class="form-control" id="others" value="" name="others" placeholder="Others" type="text">
                </div>
                
              
            </div>
            
          </div><!-- ./box -->
        
         

          @endif
           

          <div class="box-footer">
                <button type="submit" class="btn btn-default" name="saveDraft" value="saveDraft">Save as Draft</button>
                <button type="submit" class="btn btn-info pull-right" name="submitReport" value="submitReport">Submit Report</button>
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
