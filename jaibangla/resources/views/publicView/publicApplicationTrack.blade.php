<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Jai Bangla | Government of West Bengal</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("images/favicon.ico") }}">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    <!-- Bootstrap 3.3.6 -->
    <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    {{-- <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet"> --}}

    <!-- Select2 -->
   
    <!-- Ionicons -->
    <!--link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"-->
    <link href="{{ asset('css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/AdminLTE/plugins/datepicker/datepicker3.css")}}" rel="stylesheet" type="text/css" />
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/select2/select2.min.css")}}">
    <!-- Theme style -->
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
      page. However, you can choose any other skin. Make sure you
      apply the skin class to the body tag so the changes take effect.
      -->
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app-template.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrapValidator.css') }}" />
    <link href="{{ asset('css/jquery-confirm.min.css') }}" rel="stylesheet">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/iCheck/flat/blue.css")}}">


    <!-- fancybox -->
    
     <link rel="stylesheet" href="{{ asset ("/bower_components/AdminLTE/dist/css/jquery.fancybox.css") }}"  type="text/css" >
      <link rel="stylesheet" href="{{ asset ("/bower_components/AdminLTE/dist/css/prettyPhoto.css") }}"  type="text/css" >

    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}">
    <link href="{{ asset('css/jquery-confirm.min.css') }}" rel="stylesheet">

     <style type="text/css">
  .full-width{
    width:100%!important;
  }
.bg-blue{
  background-image: linear-gradient(to right top, #0073b7, #0086c0, #0097c5, #00a8c6, #00b8c4)!important;
}
.bg-red{
  /*background-image: linear-gradient(to right bottom, #dd4b39, #db4546, #d74052, #d13d5e, #c93d68)!important;*/
 /* background-image: linear-gradient(to right bottom, #dd4b39, #e65347, #ef5b55, #f76463, #ff6d71)!important;*/
 background-image: linear-gradient(to right bottom, #dd4b39, #ec6f65, #d21a13, #de0d0b, #f3060d)!important;
}
.bg-yellow{
  background-image: linear-gradient(to right bottom, #dd4b39, #e65f31, #ed7328, #f1881e, #f39c12)!important;
}
.bg-green{
 /*background-image: linear-gradient(to right bottom, #00837d, #008d7b, #009674, #009e69, #00a65a)!important;*/
 background-image: linear-gradient(to right bottom, #04736d, #008f73, #00ab6a, #00c44f, #5ddc0c)!important;
}

.bg-verify{
  background-image: linear-gradient(to right top, #f39c12, #f8b005, #fac400, #fad902, #f8ee15)!important;
}
.info-box {
    display: block;
    min-height: 90px;
    background: #b6d0ca33!important;
    width: 100%;
    box-shadow: 0px 0px 15px 0px rgba(0, 0, 0, 0.30)!important;
    border-radius: 2px;
    margin-bottom: 15px;
}
.small-box .icon{
  margin-top: 7%;
}
.small-box>.inner {
    padding: 10px;
    color: white;
}

.small-box p {
    font-size: 18px!important;
}
/* .select2 .select2-container{
  width:100%!important;
}  */

.link-button {
  background: none;
  border: none;
  color: blue;
  text-decoration: underline;
  cursor: pointer;
  font-size: 1em;
  font-family: serif;
}
.link-button:focus {
  outline: none;
}
.link-button:active {
  color:red;
}
.small-box-footer-custom{
  position: relative;
    text-align: center;
    padding: 3px 0;
    color: #fff;
    color: rgba(255,255,255,0.8);
    display: block;
    z-index: 10;
    background: rgba(0,0,0,0.1);
    text-decoration: none;
    font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;
    font-weight: 400;
    width:100%;
}
.small-box-footer-custom:hover {
    color: #fff;
    background: rgba(0,0,0,0.15);
}
th.sorting::after,
th.sorting_asc::after,
th.sorting_desc::after {
   content:"" !important;
}
 .errorField{
    border-color: #990000;
  }
  .searchPosition{
    margin:70px;
  }
  .submitPosition{
    margin: 25px 0px 0px 0px;
  }

  
  .typeahead { border: 2px solid #FFF;border-radius: 4px;padding: 8px 12px;max-width: 300px;min-width: 290px;background: rgba(66, 52, 52, 0.5);color: #FFF;}
  .tt-menu { width:300px; }
  ul.typeahead{margin:0px;padding:10px 0px;}
  ul.typeahead.dropdown-menu li a {padding: 10px !important;  border-bottom:#CCC 1px solid;color:#FFF;}
  ul.typeahead.dropdown-menu li:last-child a { border-bottom:0px !important; }
  .bgcolor {max-width: 550px;min-width: 290px;max-height:340px;background:url("world-contries.jpg") no-repeat center center;padding: 100px 10px 130px;border-radius:4px;text-align:center;margin:10px;}
  .demo-label {font-size:1.5em;color: #686868;font-weight: 500;color:#FFF;}
  .dropdown-menu>.active>a, .dropdown-menu>.active>a:focus, .dropdown-menu>.active>a:hover {
    text-decoration: none;
    background-color: #1f3f41;
    outline: 0;
  }
  table.dataTable thead th, table.dataTable thead td{
    padding:10px 13px;
  }
  table.dataTable tfoot th, table.dataTable tfoot td{
    padding:10px 5px;
  }

  .criteria1{
    text-transform: uppercase;
    font-weight: bold;
  }
  
  #example_length{
    margin-left: 40%;
    margin-top: 2px;
  }
  @keyframes spinner {
  to {transform: rotate(360deg);}
}
 
.spinner:before {
  content: '';
  box-sizing: border-box;
  position: absolute;
  top: 50%;
  left: 50%;
  width: 20px;
  height: 20px;
  margin-top: -10px;
  margin-left: -10px;
  border-radius: 50%;
  border: 2px solid #ccc;
  border-top-color: #333;
  animation: spinner .6s linear infinite;
}

label.required:after {
                color: red;
                content:'*';
                font-weight: bold;
                margin-left: 5px;
                float:right;
                margin-top: 5px;
            }
#loadingDiv{
  position:absolute;
  top:0px;
  right:0px;
  width:100%;
  height:100%;
  background-color:#fff;
  background-image:url('images/ajaxgif.gif');
  background-repeat:no-repeat;
  background-position:center;
  z-index:10000000;
  opacity: 0.4;
  filter: alpha(opacity=40); /* For IE8 and earlier */
}
</style>
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css"> -->
<!--data table--->
<link rel="stylesheet" href="{{ asset("/css/jquery.dataTables.min.css")}}">
<link rel="stylesheet" href="{{ asset("/css/buttons.dataTables.min.css")}}">
  
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
 .bg_blue {
            background-color: #003399;
            width: 330px;
            height: 39px;
            border-radius: 12px;
        }

         .bg_blue h2 {
            color: #fff;
            font-weight: 600;
            margin-left: 45px;
            padding-top: 8px;
            font-size: 20px;
        }


    .paschimbanga_sarkar h2 {
    color: #115e28;
    font-size: 35px;
    font-weight: bold;
}

    .paschimbanga_sarkar h3 {
    letter-spacing: 3px;
    text-transform: uppercase;
    font-weight: 600;
    color: #341c90;
    margin-top: -6px;
    font-size: 20px;
}
#searchbtn
  {
   margin:5px auto;
  }
  #loader{
    margin:0px 0px 0px 350px;;
  }
  .select2{
    width:100%!important;
  }
  .select2 .has-error {
    border-color:#cc0000;
   background-color:#ffff99;
   }
  .requied{
  color:red;
}

@import url(https://fonts.googleapis.com/css?family=Cinzel:700);

/* Timeline */
.timeline,
.timeline-horizontal {
  list-style: none;
  /*padding: 20px;*/ /*OLD*/
  padding: 10px; /*NEW*/ 
  position: relative;
}
.timeline:before {
  top: 40px;
  bottom: 0;
  position: absolute;
  content: " ";
  width: 3px;
  background-color: #eeeeee;
  left: 50%;
  margin-left: -1.5px;
}
.timeline .timeline-item {
  margin-bottom: 20px;
  position: relative;
}
.timeline .timeline-item:before,
.timeline .timeline-item:after {
  content: "";
  display: table;
}
.timeline .timeline-item:after {
  clear: both;
}
.timeline .timeline-item .timeline-badge {
  color: #fff;
  /*width: 54px;*/
  /*height: 54px;*/
  width: 45px;
  height: 45px;
  line-height: 52px;
  font-size: 22px;
  text-align: center;
  position: absolute;
  top: 18px;
  left: 50%;
  margin-left: -25px;
  background-color: #bbdefb;
  border: 3px solid #ffffff;
  z-index: 100;
  border-top-right-radius: 50%;
  border-top-left-radius: 50%;
  border-bottom-right-radius: 50%;
  border-bottom-left-radius: 50%;
}
.timeline .timeline-item .timeline-badge i,
.timeline .timeline-item .timeline-badge .fa,
.timeline .timeline-item .timeline-badge .glyphicon {
  top: 2px;
  left: 0px;
}
.timeline .timeline-item .timeline-badge.primary {
  background-color: #bbdefb;
}
.timeline .timeline-item .timeline-badge.info {
  background-color: #26c6da;
}
.timeline .timeline-item .timeline-badge.success {
  background-color: #80DEEA;
}
.timeline .timeline-item .timeline-badge.warning {
  background-color: #a7ffeb;
}
.timeline .timeline-item .timeline-badge.danger {
  background-color: #42a5f5;
}
.timeline .timeline-item .timeline-panel {
  position: relative;
  height: 100px;
  width: 46%;
  float: left;
  right: 16px;
  border: 1px solid #c0c0c0;
  background: #ffffff;
  border-radius: 2px;
  /*padding: 20px;*/
  padding: 5px;
  -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
  box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
}
.timeline .timeline-item .timeline-panel:before {
  position: absolute;
  top: 26px;
  right: -16px;
  display: inline-block;
  border-top: 16px solid transparent;
  border-left: 16px solid #c0c0c0;
  border-right: 0 solid #c0c0c0;
  border-bottom: 16px solid transparent;
  content: " ";
}
.timeline .timeline-item .timeline-panel .timeline-title {
  margin-top: 0;
  /*font-size: 25px;*/
  font-size: 20px;
  font-family: 'Waiting for the Sunrise', cursive; 
  color: #0c0c0c
}
.timeline .timeline-item .timeline-panel .timeline-body > p,
.timeline .timeline-item .timeline-panel .timeline-body > ul {
  margin-bottom: 0;
  font-family: 'Cinzel',sans-serif;
  color: #a79898;
}
.timeline .timeline-item .timeline-panel .timeline-body > p + p {
  margin-top: 0px;
}
.timeline .timeline-item:last-child:nth-child(even) {
  float: right;
}
.timeline .timeline-item:nth-child(even) .timeline-panel {
  float: right;
  left: 16px;
}
.timeline .timeline-item:nth-child(even) .timeline-panel:before {
  border-left-width: 0;
  border-right-width: 14px;
  left: -14px;
  right: auto;
}
.timeline-horizontal {
  list-style: none;
  position: relative;
  padding: 20px 0px 20px 0px;
  display: inline-block;
}
.timeline-horizontal:before {
  height: 3px;
  top: auto;
  bottom: 26px;
  left: 56px;
  right: 0;
  width: 100%;
  margin-bottom: 20px;
}
.timeline-horizontal .timeline-item {
  display: table-cell;
  /*height: 280px;*/
  height: 180px;
  width: 20%;
  /*min-width: 320px;*/
  min-width: 260px;
  float: none !important;
  padding-left: 0px;
  /*padding-right: 20px;*/
  padding-right: 10px;
  margin: 0 auto;
  vertical-align: bottom;
}
.timeline-horizontal .timeline-item .timeline-panel {
  top: auto;
  /*bottom: 64px;*/
  bottom: 50px;
  display: inline-block;
  float: none !important;
  left: 0 !important;
  right: 0 !important;
  width: 100%;
  /*margin-bottom: 20px;*/
  margin-bottom: 10px;
}
.timeline-horizontal .timeline-item .timeline-panel:before {
  top: auto;
  bottom: -16px;
  left: 28px !important;
  right: auto;
  border-right: 16px solid transparent !important;
  border-top: 16px solid #c0c0c0 !important;
  border-bottom: 0 solid #c0c0c0 !important;
  border-left: 16px solid transparent !important;
}
.timeline-horizontal .timeline-item:before,
.timeline-horizontal .timeline-item:after {
  display: none;
}
.timeline-horizontal .timeline-item .timeline-badge {
  top: auto;
  bottom: 0px;
  left: 48px;
}
.preloader1 {
    position: fixed;
    top: 40%;
    left: 52%;
    z-index: 999;
  }
#loadingDivModal{
  position:absolute;
  top:0px;
  right:0px;
  width:100%;
  height:100%;
  background-color:#fff;
  background-image:url('../images/ajaxgif.gif');
  background-repeat:no-repeat;
  background-position:center;
  z-index:10000000;
  opacity: 0.4;
  filter: alpha(opacity=40); /* For IE8 and earlier */
}
#loaderDiv{
  position:absolute;
  top:0px;
  right:0px;
  width:100%;
  height:100%;
  background-color:#fff;
  background-image:url('../images/ajaxgif.gif');
  background-repeat:no-repeat;
  background-position:center;
  z-index:10000000;
  opacity: 0.4;
  filter: alpha(opacity=40); /* For IE8 and earlier */
}
.panel-title {
  position: relative;
}
  
.panel-title::after {
  content: "\f107";
  color: #333;
  top: -2px;
  right: 0px;
  position: absolute;
  font-family: "FontAwesome"
}

.panel-title[aria-expanded="true"]::after {
  content: "\f106";
}

/*
 * Added 12-27-20 to showcase full title clickthrough
 */

.panel-heading-full.panel-heading {
  padding: 0;
}

.panel-heading-full .panel-title {
  padding: 10px 15px;
}

.panel-heading-full .panel-title::after {
  top: 10px;
  right: 15px;
}
.panel-title>a, .panel-title>a:active{
  display:block;
  padding:5px;
  color:#555;
  font-size:12px;
  font-weight:bold;
  text-transform:uppercase;
  letter-spacing:1px;
  word-spacing:3px;
  text-decoration:none;
}
 .pb_wb h4 {
            font-size: 15px;
  }

.pb_wb h3 {
            margin-top: -8px;
            font-size: 17px;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-left: 35px;
            font-weight: 600;
            color: #341c90;
  }
  </style>

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
  <nav class="navbar navbar-default" style="background-color: #f7fcff; margin-bottom: 0;">
    
      {{-- <div class="navbar-header">
        <a class="navbar-brand" href="#" style="font-size: 20px; color: #fff;">Lakshmir Bhandar</a>
      </div> --}}
      <!-- <ul class="nav navbar-nav navbar-right">
        <li><a href="#" style="color: #fff;"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
      </ul> -->
      <div class="row">
        <div class="col-xs-3 col-sm-3 col-md-2">
            <img class="biswo" src="{{ asset('images/biswo.png') }}" alt="Alternate Text" width="100px" />
        </div>
        <div class="col-xs-9  col-sm-9 col-md-10" style="margin-top: 20px; ">
            <div class="col-md-6">
                <div class="paschimbanga_sarkar">
                    <h2>পশ্চিমবঙ্গ সরকার</h2>
                    <h3>Government Of Bengal</h3>
                </div>
            </div>
            <div class="col-md-6">
              <div class="bg_blue">
                  <h2>জয় বাংলা</h2>
              </div>
              <div class="pb_wb">
                  <h4>পশ্চিমবঙ্গ সরকারের সমস্ত সামাজিক পেনশন প্রকল্পের</h4>
                  <h3>One Umbrella Scheme</h3>
              </div>
          </div>
        </div>
    </div>
    
  </nav>
  <section class="content-header">
    <h1>
      Track Applicant & View Payment Status
    </h1>
    <!-- <ol class="breadcrumb">
      <span style="font-size: 12px; font-weight: bold;"><i class="fa fa-clock-o"> Date : </i><span
          class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
    </ol> -->
  </section>
  <section class="content">
    @if ( ($crud_status = Session::get('crud_status')))
      <div class="alert alert-{{$crud_status=='success'?'success':'danger'}} alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button> 
          <strong>{{ Session::get('crud_msg') }} @if($crud_status=='success') with Application ID: {{ Session::get('id') }}@endif</strong>
      </div>
    @endif
   
    <div class="box box-primary">
      <div class="box-header with-border">
        <span style="font-size: 15px; font-style: italic; font-weight: bold;">Track Applicant using Beneficiary Id/Mobile No./Aadhaar No.</span>
        <a href="{{url('/')}}"><img title="Back to Home" src="{{ asset("images/back.jpg") }}" style="float:right;margin-right:10px;height: 30px; width: 30px; border-width: 0px;" ></a>
      </div>

      @if(count($error_msg) > 0)
      <div class="alert alert-danger alert-block">
        <ul>
          @foreach($error_msg as $error_item)
          <li><strong> {{ $error_item }}</strong></li>
          @endforeach
        </ul>
      </div>
       @endif
      <div class="box-body">
        <div id="loaderDiv"></div>
        <form method="post" id="publick_track_applicant" action="{{url('track-applicant-public')}}" class="submit-once" >
          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
         
            <div class="row">
              
               

                <div class="col-md-2">
                  <label class=" control-label">Scheme <span class="text-danger">*</span></label>
                  <select class="form-control select2" name="scheme_code" id='scheme_code'>
                    <option value="">--Select--</option>
                    @foreach ($schemes as $scheme)
                    <option value="{{$scheme->id}}" @if($sel_scheme_code==$scheme->id) selected @endif>{{$scheme->scheme_name}}</option>
                    @endforeach
                  </select>
                  <span class="text-danger" id="error_scheme_code"></span>
                </div>
                <div class="col-md-2">
                  <label class=" control-label">Search Using <span class="text-danger">*</span></label>
                  <select class="form-control" name="select_type" id='select_type'>
                    <option value="">--Select--</option>

                    <option value="1" @if($sel_select_type==1) selected @endif>Beneficiary Id</option>
                    <option value="2" @if($sel_select_type==2) selected @endif>Mobile Number</option>
                    <option value="3" @if($sel_select_type==3) selected @endif>Aadhar Number</option>
                  </select>
                  <span class="text-danger" id="error_select_type"></span>
                </div>
                @php
                $sel_applicant_id_p='';
                if($sel_select_type==3)
                $sel_applicant_id_p='';
                else 
                $sel_applicant_id_p=$sel_applicant_id;
                @endphp
                <div class="col-md-2" style="margin-top:20px;">
                  <!-- <label class="required-field"><span class="requied">*</span></label> -->
                  <input type="text" name="applicant_id" id="applicant_id" class="form-control" placeholder="Enter Beneficiary Id" autocomplete="off" 
                  style="font-size: 16px; margin: 5px auto;" 
                  onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;" value="{{$sel_applicant_id_p}}"/>                                                      
                  <span id="error_applicant_id" class="text-danger"></span>
                </div>
                <div class="col-md-4" style="margin-top:20px;">
                  <div class="col-md-5">
                    <span class="refereshrecapcha">{!! captcha_img('flat') !!}</span>
                    <a href="{{ route('track-applicant-public') }}"><img src="{{ asset("images/refresh1.png") }}" style="height: 20px; width: 20px; border-width: 0px;" ></a> 
                  </div>
                  <div class="col-md-4">
                    <input type="text" id="captcha" name="captcha" placeholder="Enter Captcha" class="form-control" style="font-size: 16px;" >
                    <span id="error_captcha" class="text-danger"></span>

                  </div>
                  <div class="col-md-3"  id="search_div">
                    <button type="submit" class="btn btn-primary" name="submitted"><i class="fa fa-search"></i> Search</button> 
                  </div>
                </div>
                
               
               
              
            </div>
          </form>
            {{-- <div class="alert print-error-msg"  style="display:none;" id="crud_msg_Crud">
              <button type="button" class="close"  aria-label="Close" onclick="closeError('crud_msg_Crud')"><span aria-hidden="true">&times;</span></button>
              <ul></ul>
            </div> --}}

            <!-- Result Div Showing Timeline -->
            @if($form_submitted && $is_succes==1)
            <br/>
            <div id="ajaxData">
              <div class="panel panel-default">
                <div class="panel-heading" id="panel_head" style="font-size: 15px; font-weight: bold; font-style: italic; padding: 5px 15px;">List of Beneficiary</div>
                <div class="panel-body" style="padding: 5px; font-size: 14px;">
                  <div class="table-responsive">
                    <table id="example" class="table display" cellspacing="0" width="100%">
                      <thead style="font-size: 12px;">
                        <th>Beneficiary ID</th>
                        <th>Applicant Name</th>
                        <th>Address</th>
                        <th>Current Banking Information</th>
                        <th>Current Status</th>
                        <th>Payment Status</th>
                      </thead>
                      <tbody style="font-size: 14px;">
                        @if (count($row_list)>0)
                         @foreach ($row_list as $row_item)
                         <tr>
                         <td>{{$row_item['id']}}</td>
                         <td>{{$row_item['name']}}</td>
                         <td>{!! html_entity_decode($row_item['address']) !!}</td>
                         <td>{!! html_entity_decode($row_item['bank_info']) !!}</td>
                         <td>{!! html_entity_decode($row_item['current_status']) !!}</td>
                         <td><button class="btn btn-info btn-sm" name="view_status" class="view_status" value="{{$row_item['id']}}_{{$row_item['scheme_id']}}" onclick="viewPaymentStatusFunction(this.value);"><i class="fa fa-eye"></i> View</button></td>
                         </tr>
                         @endforeach 
                         @else
                         <tr><td colspan="6">No Record Found</td></tr>
                        @endif
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            @endif

      </div>
    </div>
    <div class="modal fade" id="ben_payment_view_modal" tabindex="-1">

     

      <div class="modal-dialog ">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title ">View Payment Status</h4>
          </div>
          <div class="modal-body">
           
            <div class="panel panel-default" id="payment_details_view_div" >
              <div class="panel-heading" style="font-size: 15px; font-weight: bold; font-style: italic; padding: 5px 15px;"><span id="panel-icon">View Payment Status</div>
        
              <div class="panel-body">
                <?php
                if (date('m') > 3) {
                  $currentFinYear = date('Y') . "-" . (date('Y') + 1);
                } else {
                  $currentFinYear = (date('Y') - 1) . "-" . date('Y');
                }
                ?>
        
                <div class="row">
                  <div class="col-md-12">
                    <input type="hidden" name="ben_id_hidden" id="ben_id_hidden" value="">
                    <input type="hidden" name="scheme_id_hidden" id="scheme_id_hidden" value="">
                    <input type="hidden" name="current_fin_year" id="current_fin_year" value="<?php echo $currentFinYear; ?>">
                    <div class="col-md-6">
                      <label>Which financial year you want to view payment status ?</label>
                    </div>
                    <div class="col-md-6">
                      <select class="" name="select_financial_year" id="select_financial_year" onchange="changeFinancialYear(this.value)" style="font-size: 16px; width: 150px;">
                        <?php
        
                        use Illuminate\Support\Facades\Config;
        
                        foreach (Config::get('constants.fin_year') as $key => $fin_year) {
                          //echo $fin_year;
                          if ($key == $currentFinYear) {
                            $selected = 'selected';
                          } else {
                            $selected = '';
                          }
                          echo '<option value="' . $key . '" ' . $selected . '>' . $fin_year . '</option>';
                        }
        
                        ?>
        
                      </select>
                    </div>
                  </div>
                </div>
                <hr />
                <div id="loader_data"><img  src="{{ asset('images/ZKZg.gif') }}" width="50px" height="50px"/></div>
                <div id="payment_details_view" class="table-responsive"></div>
              </div>
            </div>
           
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
           
          </div>
        </div>
      </div>
  
    </div>

  
  </section>
  <br/>
  <!-- Main Footer -->
  <div>
    <footer style="background: #fff; padding: 15px; color: #444; border-top: 1px solid #d2d6de; background-color: ghostwhite;">
      <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="http://nicwb.nic.in">NIC</a>.</strong> All rights reserved.
    </footer>
  </div>

 <!-- REQUIRED JS SCRIPTS -->
    <!-- jQuery 2.1.3 -->
    <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>

    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
   
    <!-- AdminLTE App -->
    <script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
    <!-- iCheck -->

    <!-- Bootstrap WYSIHTML5 -->


    
    <!-- Select2 -->

    <!-- fancybox -->

    <script src="{{ asset ("/bower_components/AdminLTE/dist/js/jquery.fancybox.min.js") }}" type="text/javascript"></script>

    <script src="{{ asset ("/bower_components/AdminLTE/dist/js/jquery.prettyPhoto.js") }}" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function(){
      $('#loaderDiv').hide(); 
      var sessiontimeoutmessage='{{$sessiontimeoutmessage}}';
      var base_url='{{ url('/') }}'; 
      $("#select_type").on('change', function(){
      var select_type =  $("#select_type").val();
      $("#applicant_id").val('');
      if(select_type == 3)
      {
        $("#applicant_id").attr('type', 'password');
        $("#applicant_id").attr("placeholder", "Aadhaar Number");
        $("#applicant_id").attr('maxlength', 12);

      } 
      else
      {
        if(select_type == 1){
        $("#applicant_id").attr("placeholder", "Beneficiary Id");
        $("#applicant_id").attr('maxlength', 12);
        }
        if(select_type == 2){
          $("#applicant_id").attr("placeholder", "Mobile Number");
          $("#applicant_id").attr('maxlength', 10);
        }
        $("#applicant_id").attr('type', 'text'); 
      }
      });      
 $("#publick_track_applicant").submit(function(e){
  e.preventDefault();
  var scheme_code=$("#scheme_code").val(); 
  var applicant_id=$("#applicant_id").val(); 
  var captcha=$("#captcha").val(); 
  var select_type=$("#select_type").val(); 
  var select_type_text=$("#select_type option:selected" ).text();
  console.log(select_type_text);
  if(select_type_text=='--Select--'){
    select_type_text='Beneficiary Id/Mobile No./Aadhaar No.';
  }
  var status1=status2=status3=status4=0;
  if(scheme_code=='' || typeof(scheme_code) === "undefined" || scheme_code===null){
    $('#error_scheme_code').text('Please Select Scheme');
    status1=0;
  }
  else{
    $('#error_scheme_code').text('');
      status1=1;
  }
  if(select_type=='' || typeof(select_type) === "undefined" || select_type===null){
    $('#error_select_type').text('Please Select Search using Criteria');
    status4=0;
  }
  else{
    $('#error_select_type').text('');
      status4=1;
  }
   if(applicant_id=='' || typeof(applicant_id) === "undefined" || applicant_id===null){
    $('#error_applicant_id').text('Please Enter '+select_type_text);
    status1=0;
  }
  else{
     if(select_type==2){
        if($('#applicant_id').val().length !=10)
        {
          error_applicant_id = 'Mobile Number must be 10 digit';
          $('#error_applicant_id').text(error_applicant_id);
          $('#applicant_id').addClass('has-error');
        }
        else
        {
          error_applicant_id = '';
          $('#error_applicant_id').text(error_applicant_id);
          $('#applicant_id').removeClass('has-error');
        }
    }
    else if(select_type==3){
        if($('#applicant_id').val().length !=12)
        {
          error_applicant_id = 'Aadhaar Number must be 12 digit';
          $('#error_applicant_id').text(error_applicant_id);
          $('#applicant_id').addClass('has-error');
        }
        else
        {
          error_applicant_id = '';
          $('#error_applicant_id').text(error_applicant_id);
          $('#applicant_id').removeClass('has-error');
        }
    }
    else{
    $('#error_applicant_id').text('');
      status2=1;
    }
  }
  if(captcha=='' || typeof(captcha) === "undefined" || captcha===null){
    $('#error_captcha').text('Please Enter Captcha');
    status3=0;
  }
  else{
    $('#error_captcha').text('');
      status3=1;
  }
  //alert(status1);  alert(status2);
  if(status1 && status2 && status3 && status4){
    e.target.submit();
  }             
  });

});
  // After click view button then showing payment status panel
  function viewPaymentStatusFunction(value) {
    // alert(value);
    var arr = value.split('_');
    var ben_id = arr[0];
    var scheme_id = arr[1];
    
    $('#ben_id_hidden').val("");
    $('#ben_id_hidden').val(ben_id);
    $('#scheme_id_hidden').val("");
    $('#scheme_id_hidden').val(scheme_id);
    var fin_year = $('#select_financial_year').val();
    var cur_fin_year = $('#current_fin_year').val();
    $('#select_financial_year').val(cur_fin_year);
    $("#ben_payment_view_modal").modal();
    callAjaxPaymentStatusFunction(ben_id, fin_year, scheme_id);
  }

  // Financial yearwise view payment status
  function changeFinancialYear(fin_year) {
    var ben_id = $('#ben_id_hidden').val();
    var scheme_id = $('#scheme_id_hidden').val();
    // var select_fin_year = $('#select_financial_year').val();
    callAjaxPaymentStatusFunction(ben_id, fin_year, scheme_id);
  }

  function callAjaxPaymentStatusFunction(ben_id, fin_year, scheme_id) {
    $('#loader_data').show();
    $.ajax({
      type: 'post',
      url: "{{ route('getPaymentStatusDetailsPublic') }}",
      data: {
        ben_id: ben_id,
        fin_year: fin_year,
        schemeId : scheme_id,
        _token: '{{ csrf_token() }}'
      },
      success: function(response) {
        $('#loader_data').hide();
        // console.log(response);
        $('#payment_details_view').html('');
        $('#payment_details_view').html(response.final_payment_table);

        if (response.ben_details.ben_mname) {
          var ben_mname = response.ben_details.ben_mname;
        }
        if (response.ben_details.ben_lname) {
          var ben_lname = response.ben_details.ben_lname;
        }

        var dataTable = "";
        if ($.fn.DataTable.isDataTable('#paymentTable')) {
          $('#paymentTable').DataTable().destroy();
        }
        dataTable = $('#paymentTable').dataTable({
          "paging": false,
          "scrollX": false,
          "ordering": false,
          "info": false,
          "dom": 'Bfrtip',
          "bFilter": false,
          "bInfo": false,
          "buttons": [{
              extend: 'pdf',
              title: 'Beneficiary ID -' + response.ben_details.id + ' Payment Details',
              orientation: 'landscape',
              messageTop: 'Name -' + response.ben_details.ben_fname + ' ' + ben_mname + ' ' + ben_lname + '\n Beneficiary ID -' + response.ben_details.id + '\n  IFSC -' + response.ben_details.bank_ifsc + '\n A/c No -' + response.ben_details.bank_code + '\n Mobile No -' + response.ben_details.mobile_no + '\n Aadhar Number -' + response.ben_details.aadhar_no + '\n Financial Year -' + fin_year,
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
              },
              text: '<i class="fa fa-file-pdf-o"></i> PDF'
            },
            {
              extend: 'excel',
              title: 'Beneficiary ID -' + response.ben_details.id + ' Payment Details',
              messageTop: 'Name -' + response.ben_details.ben_fname + ' ' + ben_mname + ' ' + ben_lname + '\n Beneficiary ID -' + response.ben_details.id + '\n  IFSC -' + response.ben_details.bank_ifsc + '\n A/c No -' + response.ben_details.bank_code + '\n Mobile No -' + response.ben_details.mobile_no + '\n Aadhar Number -' + response.ben_details.aadhar_no + '\n Financial Year -' + fin_year,
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
              },
              text: '<i class="fa fa-file-excel-o"></i> Excel'
            },
          ],
        });
      },
      complete: function() {
        $('#loader_data').hide();

      },
      error: function(jqXHR, textStatus, errorThrown) {
        $('#loader_data').hide();
        //$('#loadingDiv').hide();
        ajax_error(jqXHR, textStatus, errorThrown);
      }
    });
  }

  function getStatusUTRAndErrorFun(value) {
    // alert(value);
    var arr = value.split('_');
    var lot_no = arr[0];
    var pension_id =  arr[1];
    var fin_year = arr[2];
    var scheme_id = arr[3];
    // alert(lot_no+'   '+pension_id+'   '+fin_year+'   '+scheme_id);
    $('#loadingDiv').show();
    $.ajax({
      type: 'post',
      url: "{{ route('getStatusUTRAndErrorFunPublic') }}",
      data: {
        lot_no : lot_no,
        pension_id: pension_id,
        fin_year: fin_year,
        schemeId : scheme_id,
        _token: '{{ csrf_token() }}'
      },
      success: function(response) {
        $('#loadingDiv').hide();
       // console.log(response);
        $.alert({
          title: response.title,
          type: response.type,
          icon: response.icon,
          content: response.msg
        });
      },
      complete: function() {},
      error: function(jqXHR, textStatus, errorThrown) {
        $('#loadingDiv').hide();
        ajax_error(jqXHR, textStatus, errorThrown);
      }
    });
  }

  function ajax_error(jqXHR, textStatus, errorThrown) {
    var msg = "<strong>Failed to Load data.</strong><br/>";
    if (jqXHR.status !== 422 && jqXHR.status !== 400) {
      msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
    } else {
      if (jqXHR.responseJSON.hasOwnProperty('exception')) {
        msg += "Exception: <strong>" + jqXHR.responseJSON.exception_message + "</strong>";
      } else {
        msg += "Error(s):<strong><ul>";
        $.each(jqXHR.responseJSON, function(key, value) {
          msg += "<li>" + value + "</li>";
        });
        msg += "</ul></strong>";
      }
    }
    $.alert({
      title: 'Error!!',
      type: 'red',
      icon: 'fa fa-warning',
      content: msg,
    });
  }
  function refreshCaptcha(){
   
    $.ajax({
    url: '{{url('refereshcapcha')}}',
    type: 'get',
      dataType: 'html',        
      success: function(json) {
        $('.refereshrecapcha').html(json);
      },
      error: function(data) {
        alert('Try Again.');
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
				$("#"+divid).addClass('alert-danger');
			}
			else{
				$("#"+divid).removeClass('alert-danger');
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
