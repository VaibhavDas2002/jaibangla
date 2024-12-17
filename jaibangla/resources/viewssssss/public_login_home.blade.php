<!DOCTYPE html>

<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>PS | Pension Scheme</title>
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
  <link rel="stylesheet" href="{{ asset("/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}">

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
/*-------header---------*/

    .header {
        width: 100%;
        float: left;
        background: url(images/about_bg.png);
    }

    .logo a {
        background: url(images/logo-4.png) left center no-repeat;
        display: block;
        padding-left: 13%;
        text-transform: uppercase;
        padding-bottom: 52px;
        padding-top: 0px;
        margin-top:20px;
        position: relative;
        
    }
    .logo a h1 {
        font-family: 'robotoblack';
        font-size: 25px;
        letter-spacing: 2px;
        padding: 0;
        margin: 0;
        line-height: 32px;
        position: relative;
       
        color: #0375c5;
    }
    .logo a h1 span {
        font-family: 'robotoregular';
        display: block;
        font-size: 15px;
        color: #2e2e2e;
        letter-spacing: 3px;
        position: relative;
        top: 0;
    }
    .logo a h1:focus{text-decoration:none;}

    .header-right{float:right; z-index:9;}

    .header-top-right{/*background:#e9e9e9;*/  /*background:url(../images/quick_links_bg.png) repeat; background:#f3b331;*/ padding-left:15px; padding-right:15px; margin-bottom:26px;}

    @media screen and (-webkit-min-device-pixel-ratio:0)
    { 
        .header-top-right{margin-bottom:28px;} 
    }

    .header-right-top{width:100%; float:right;}

    .header-right-top-left {
        width: 75%;
        float: right;
        margin-top: 0px;
    }
    .header-right-top-left ul{margin:0; padding:0;}

    .header-right-top-left ul{text-align:right;}
    .header-right-top-left li{color:#0375c5  ; font-size:14px; list-style-type:none; margin-right:10px; display:inline-block;}
    .header-right-top-left li:after{content:"|"; color:#0375c5  ; /*font-size:14px;*/ margin-left:10px;}
    .header-right-top-left li:last-child:after{content:"";}
    .header-right-top-left li a{color: #0375c5  ;font-size:14px;}
    .header-right-top-left li a:hover{color:#333;}

    .header-right-top-right{width:22%; float:right;}
    .header-right-top-right a{color:#1f1f1f; font-size:14px;}
    .header-right-top-right a:hover{color:#297920;}
    .header-right-top-right li{position:relative;}
    /*.header-right-top-right li:after{content:"|"; color:#00a65a; font-size:14px; position:absolute; top:12px;}
    .header-right-top-right li:last-child:after{content:"";}*/
    .header-right-top-right li.grievance{/*border-left:#a1a1a1 1px solid;border-right:#a1a1a1 1px solid;*/}
    .header-right-top-right li.grievance a{background:url(images/top_grievance_icon.png) 5px center no-repeat; padding-left:42px;}
    .header-right-top-right li a.login{background:url(images/top_login_icon.png) 10px center no-repeat; padding-left:35px;}
    .header-right-top-right li a.logout{background:url(images/top_logout_icon.png) 3px center no-repeat; padding-left:35px;}
    .header-right-top-right li a.contact{background:url(images/top_contact_icon1.png) 10px center no-repeat; padding-left:35px;}
    .header-right-top-right .dropdown-menu{z-index:10000;}

    .mobile_login_section{display:none;}

    /*-------header---------*/
/*-------footer---------*/

    .footer{background:url(images/footer_bg.jpg) center bottom no-repeat; background-size:cover; padding:15px 0px; float:left; width:100%; color:#fff; font-size:14px;position: sticky;}

    .footer_copy{ font-size:12px;}

    .footer_nav ul{padding:0; margin:0;}
    .footer_nav li{float:left; list-style-type:none;}
    .footer_nav li:after{content:"/"; color:#fff; font-size:14px; margin-left:10px; margin-right:10px}
    .footer_nav li:last-child:after{content:"";}
    .footer_nav a{color:#fff;}
    .footer_nav a:hover{color:#dedede;}

    .footer_text{font-size:14px; line-height:18px;  border-bottom:1px solid #fff; margin:8px 0 15px; /*padding-bottom:15px;*/}

    .footer_bottom_wrap{width:100%; float:left;}
    .nic_logo{background:#fff; float:right; font-family:Arial, sans-serif; color:#024c88; font-weight:bold; font-size:12px; padding:5px 8px; /*margin-right:15px;*/}
    .site_visitor{background:#fff; float:left; font-family:Arial, sans-serif; color:#024c88; font-weight:bold; font-size:12px; padding:5px 8px; /*margin-right:15px;*/}
    .modal-header h4 {

        color: #00a65a;
        font-family: 'robotobold';
        font-size: 22px;
        margin-top: 1px;

    }
    .modal-body p{color:#333333;}

    .loginoffice{
        border: 1px solid #0375c5 ;
        padding: 1px 10px;
        border-radius: 10px;
    }
    .header-right-top-left li.loginoffice:hover {
        color:#fff;
        background: #0375c5;
    }
    .header-right-top-left li.loginoffice:hover a{
        color:#fff;
    }

    /*-------footer---------*/
  </style>
  <script>
      window.history.forward();
    </script>

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
  <!-- Header content -->
  	<div class="header" style="width: 100%;background: url(images/about_bg.png);margin-top: 0px;">
	    <div class="container">
	      <div class="row">
	        <div class="col-md-6">
	          <div class="header-right-top">
	            <div class="row">
	              <a href="#">
	                <img src="images/biswo.png" width="80px;">
	                  <img src="images/jaibangla_logo.png" width="420px;" style="margin-top: 0px;">
	              </a>
	            </div>
	          </div>
	        </div>
	        <div class="col-md-6">
	          <div class="header-right-top-left" style="margin-top: 35px;">
	            <ul class="list-logout">
	              <li title="Toll Free Number"> <i class="fa fa-comments"></i><font size="3"><a href="#">FAQ</a></font></li>
	              <li title="Send Email"><i class="fa fa-envelope"></i><a href="#">How to Application</a></li>
	              <li title="Send SMS"><i class="fa fa-comments"></i><a href="#">About Us</a></a></li>
	              <li class="loginoffice"><a href="{{ url('pagelogout') }}" title="log In"><i class="fa fa-user"></i>Logout</a></li>
	            </ul>          
	          </div>
	        </div>
	      </div>    
	    </div>
    </div>
    <div class="clearfix"></div>
<div class="content">



  <!-- Content Wrapper. Contains page content -->
  <div class="">
    <!-- Content Header (Page header) -->
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-primary">
                <div class="panel-heading">Select Pension Scheme</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="">
                        {{ csrf_field() }} 

                        <div class="form-group{{ $errors->has('scheme') ? ' has-error' : '' }}">
                            <label for="scheme" class="col-md-4 control-label">Scheme Type</label>

                            <div class="col-md-6">
                                <select onchange="la(this.value)" class="form-control select2" name="scheme"  id="scheme">
                                    <option value="">--Select--</option>
                                    <option value="{{ url('publicloginpensionform') }}?pr1=sc">Toposili Bandhu(for SC)</option>
                                    <option value="{{ url('publicloginpensionform') }}?pr1=st">Jai Johar(for ST)</option>
                                    <option value="{{ url('publicloginmanabik') }}">Manabik</option>

                                    <option value="#">Old Age Pension</option>
                                    <option value="#">Widow Pension</option>
                                    <option value="#">Farmer's Old Age Pension</option>
                                    <option value="#">Old Age Pension for Fishermen</option>
                                    <option value="#">Old Age Pension for Artisans and Handloom Weavers</option>
                                    <option value="#">Lok Prasar Prakalpa</option>
                                                          
                                </select>
                                <span id="error_construction" class="text-danger"></span>
                            </div>
                        </div>

                        <script>
                            function la(src)
                            {
                                window.location=src;
                            }
                            
                        </script>

                        
                    </form>
                </div>
            </div>
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
  <!------- footer starts ------->
    
    <!-- <div class="clearfix"></div> -->
    <!------- footer end ------->
  <!-- Footer -->
  </div>
  
  
</div>
<div class="footer" style="position: fixed;bottom: 0;">
    <div class="col-lg-12">
        <div class="footer_copy col-md-5">
        <div style="text-align:left;">
                    Copyright &copy; 2019-2020 Govt of West Bengal - All Rights Reserved.</div>
        </div>
        <div class="col-lg-12 designed_by">
            <div class="footer_text"></div>

            <div class="footer_bottom_wrap">
                <div class="site_visitor">
                    Site Visitor :
                    <span id="Label2" style="color:#2F5B93;">######</span></div>
                <div class="nic_logo">
                    Designed and Developed by: <a href="http://www.nic.in/" target="_blank">
                        <img src="images/nic.png" alt="NIC" /></a>
                </div>
            </div>

        </div>
    </div>
</div>
  
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

 <!-- jQuery 2.1.3 -->
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
<!-----site.js-------------------->
<script src="{{ URL::asset('js/site.js') }}"></script>

<!-------------------------------->

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script>
  $('.select2').select2();
</script>



<!-- <script>
$(document).ready(function(){
  $(".form-control").click(function(){
    $(this).css("border-color", "green");
  });
});
</script> -->

</body>
</html>
