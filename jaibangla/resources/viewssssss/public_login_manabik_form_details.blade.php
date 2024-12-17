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
                  <li title="Send Email"><i class="fa fa-envelope"></i><a href="#">How to Apply</a></li>
                  <li title="Send SMS"><i class="fa fa-comments"></i><a href="#">About Us</a></a></li>
                  <li class="loginoffice"><a href="{{ url('pagelogout') }}" title="log In"><i class="fa fa-user"></i>Logout</a></li>
                </ul>          
              </div>
            </div>
          </div>    
        </div>
    </div>
    <div></div>
<div class="content">



  <!-- Content Wrapper. Contains page content -->
  <div class="">
    <!-- Content Header (Page header) -->
  <section class="content">
      <div class="row">
        <!-- left column -->
        <div class="col-md-10 col-md-offset-1">
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
                <form method="POST" action="{{ route('nhmemployee.printSingleEmployee', ['id' => $id]) }}"  >
                       
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">


                      
                        <!-- <button type="submit" class="btn btn-danger col-md-2 btn-lg" style="float: right; margin-top:-33px; margin-right:15px;">
                          Print
                        </button> -->
                </form>      
               
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
            <form method="post" id="register_form" action="{{url('publicloginmanabik')}}" enctype="multipart/form-data" >
              {{ csrf_field() }}
            <ul class="nav nav-tabs">

             
             <li class="nav-item">
              <a class="nav-link active_tab1" style="border:1px solid #ccc" id="list_personal_details"><b>Personal Details</b></a>
             </li>

              <li class="nav-item">
              <a class="nav-link inactive_tab1"  id="list_id_details" style="border:1px solid #ccc" ><b>Personal Identification Number(S)</b></a>
             </li>

             <li class="nav-item">
              <a class="nav-link inactive_tab1" id="list_contact_details" style="border:1px solid #ccc"><b>Contact Details</b></a>
             </li>
             <li class="nav-item">
              <a class="nav-link inactive_tab1" id="list_bank_details" style="border:1px solid #ccc"><b>Bank Account Details</b></a>
             </li>
              
              <li class="nav-item">
              <a class="nav-link inactive_tab1" id="list_type_details" style="border:1px solid #ccc"><b>Type of Disability</b></a>
             </li>

             <li class="nav-item">
              <a class="nav-link inactive_tab1" id="list_experience_details" style="border:1px solid #ccc"><b>Enclosure List(Self Attested)</b></a>
             </li>  

             <li class="nav-item">
              <a class="nav-link inactive_tab1" id="list_decl_details" style="border:1px solid #ccc"><b>Self Declaration</b></a>
             </li>        
             

            <!--  <li class="active"><a data-toggle="tab" href="#list_id_details">Personal Identification Number(S)</a></li>
            <li><a data-toggle="tab" href="#list_login_details">Personal Details</a></li>
            <li><a data-toggle="tab" href="#list_personal_details">Contact Details</a></li>
            <li><a data-toggle="tab" href="#list_contact_details">Bank Account Details</a></li>
            <li><a data-toggle="tab" href="#list_experience_details">Enclosure List(Self Attested)</a></li> -->
            </ul>



            <div class="tab-content" style="margin-top:16px;">

              



             
             <div class="tab-pane active" id="personal_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Personal Details</b></h4></div>
               <div class="panel-body">

                <div class="form-group col-md-12"  >
                 <label class="">Beneficiary Name</label>
               
                </div>


               <input type="hidden" name="scheme_id" value="{{ $scheme_id }}">


                <div class="form-group col-md-4" >
                 <label class="required-field">First Name</label>
                 <input type="text" name="first_name" id="first_name" class="form-control txtOnly" placeholder="First Name" maxlength="200" value="{{ old('first_name') }}"  tabindex="1"  />
                 <span id="error_first_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label>Middle Name</label>
                 <input type="text" name="middle_name" id="middle_name" class="form-control txtOnly"  placeholder="Middle Name" maxlength="100" value="{{ old('middle_name') }}"  tabindex="2" />
                 <span id="error_middle_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="required-field">Last Name</label>
                 <input type="text" name="last_name" id="last_name" class="form-control txtOnly" placeholder="Last Name" maxlength="200" value="{{ old('last_name') }}" tabindex="3" />
                 <span id="error_last_name" class="text-danger"></span>
                </div>

                <div class="form-group col-md-4">
                 <label class="required-field">Gender</label>
                 <select class="form-control " name="gender" id="gender"  tabindex="4">
                    <option value="">--Select--</option>
                    <option value="Male" @if(old('gender') == "Male")  selected  @endif >Male</option>
                    <option value="Female" @if(old('gender') == "Female")  selected  @endif >Female</option>
                    <option value="Other" @if(old('gender') == "Other")  selected  @endif >Others</option>                                       
                  </select>
                 <span id="error_gender" class="text-danger"></span>
                </div>

                <div class="form-group col-md-4">
                 <label class="">Date of Birth</label>
                 <input type="date" name="dob" id="dob" class="form-control"  tabindex="5"/>
                 <!-- <input type="text" id="dob" name="dob"class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask placeholder="dd/mm/yyyy"> -->
                 <span id="error_dob" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="required-field">Age<span style="">(as on 01/01/2020)</span></label>
                 <input type="text" name="txt_age" id="txt_age" class="form-control NumOnly" placeholder="Age"  value="{{ old('txt_age') }}"  maxlength="3"  tabindex="6" />
                  <span id="error_txt_age" class="text-danger"></span>
                 
                </div>



                
                <div class="form-group col-md-12">
                 <label class="">Father's Name</label>
               
                </div>
              
                <div class="form-group col-md-4">
                 <label class="required-field">First Name</label>
                 <input type="text" name="father_first_name" id="father_first_name" class="form-control txtOnly" placeholder="First Name" maxlength="200" value="{{ old('father_first_name') }}" tabindex="7" />
                 <span id="error_father_first_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label>Middle Name</label>
                 <input type="text" name="father_middle_name" id="father_middle_name" class="form-control txtOnly"  placeholder="Middle Name" maxlength="100" value="{{ old('father_middle_name') }}" tabindex="8" />
                 <span id="error_father_middle_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="required-field">Last Name</label>
                 <input type="text" name="father_last_name" id="father_last_name" class="form-control txtOnly" placeholder="Last Name" maxlength="200" value="{{ old('father_last_name') }}" tabindex="9" />
                 <span id="error_father_last_name" class="text-danger"></span>
                </div>


                <div class="form-group col-md-12">
                 <label class="">Mother's Name</label>
               
                </div>
              
                <div class="form-group col-md-4">
                 <label class="required-field">First Name</label>
                 <input type="text" name="mother_first_name" id="mother_first_name" class="form-control txtOnly" placeholder="First Name" maxlength="200" value="{{ old('mother_first_name') }}" tabindex="10" />
                 <span id="error_mother_first_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label>Middle Name</label>
                 <input type="text" name="mother_middle_name" id="mother_middle_name" class="form-control txtOnly"  placeholder="Middle Name" maxlength="100" value="{{ old('mother_middle_name') }}" tabindex="11" />
                 <span id="error_mother_middle_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="required-field">Last Name</label>
                 <input type="text" name="mother_last_name" id="mother_last_name" class="form-control txtOnly" placeholder="Last Name" maxlength="200" value="{{ old('mother_last_name') }}" tabindex="12" />
                 <span id="error_mother_last_name" class="text-danger"></span>
                </div>
                
                
                <div class="form-group col-md-4">
                 <label class="required-field">Caste</label>
                 <select class="form-control" name="caste_category" id="caste_category"  tabindex="13">
               
                 <option value="SC">SC</option>
                 <option value="ST">ST</option>
                 <option value="GENERAL">GENERAL</option>
                 
                                                                        
                  </select>
                 <span id="error_caste_category" class="text-danger"></span>
                </div>

                <div class="form-group col-md-4">
                 <label class="required-field">Marital Status</label>
                 <select class="form-control" name="marital_status" id="marital_status" tabindex="14" >
                    <option value="">--Select--</option>
                    <option value="Unmarried"   @if(old('marital_status') == "Unmarried")  selected  @endif >Unmarried</option>
                    <option value="Married"     @if(old('marital_status') == "Married")  selected  @endif >Married</option>
                     <option value="Seperated"  @if(old('marital_status') == "Seperated")  selected  @endif >Seperated</option>
                     <option value="Widow"      @if(old('marital_status') == "Widow")  selected  @endif >Widow</option>
                     <option value="Widower"    @if(old('marital_status') == "Widower")  selected  @endif >Widower</option>
                  </select>
                 <span id="error_marital_status" class="text-danger"></span>
                </div>
                <div class="row" id="spouse_section" >


                <div class="form-group col-md-4">
                  &nbsp;
                </div>
  
                <div class="form-group col-md-12">
                 <label class="">Spouse Name, if applicable</label>
               
                </div>
              
                <div class="form-group col-md-4">
                 <label class="">First Name</label>
                 <input type="text" name="spouse_first_name" id="spouse_first_name" class="form-control txtOnly" placeholder="First Name" maxlength="200" value="{{ old('spouse_first_name') }}" tabindex="15" />
                 <span id="error_spouse_first_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label>Middle Name</label>
                 <input type="text" name="spouse_middle_name" id="spouse_middle_name" class="form-control txtOnly"  placeholder="Middle Name" maxlength="100" value="{{ old('spouse_middle_name') }}" tabindex="16" />
                 <span id="error_spouse_middle_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="">Last Name</label>
                 <input type="text" name="spouse_last_name" id="spouse_last_name" class="form-control txtOnly" placeholder="Last Name" maxlength="200" value="{{ old('spouse_last_name') }}" tabindex="17" />
                 <span id="error_spouse_last_name" class="text-danger"></span>
                </div>  

                </div>  

                 <div class="form-group col-md-4">
                 <label class="required-field">Monthly Family Income(Rs.)</label>
                 <input type="text" name="monthly_income" id="monthly_income" class="form-control price-field" placeholder="Monthly Family Income(Rs.)" maxlength="10" value="{{ old('monthly_income') }}" tabindex="17" >
                 <span id="error_monthly_income" class="text-danger"></span>
                </div>        
                          
               

              
                  <br />
                  <br />
                <div class="col-md-12" align="center">

                  
                 <button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-success btn-lg">Next</button>
                 <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                </div>
                <br />
               </div>
              </div>
             </div>

              <div class="tab-pane fade" id="id_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Personal Identification Number(S)</b></h4></div>
               <div class="panel-body">

<div class="row">
              
                <div class="form-group col-md-4">
                 <label class="required-field">Digital Ration Card No.</label>
                  <div class="row" >
                  <div class="col-md-4" >
                    
                    
                   <!--  <input style="margin-left:-15px; margin-right:-15px;" type="text" name="ration_card_cat" id="ration_card_cat" class="form-control special-char" placeholder="Category" maxlength="5" value="{{ old('ration_card_cat') }}"  tabindex="1" /> -->

                   <select class="form-control " name="ration_card_cat" id="ration_card_cat"  tabindex="1" style="margin-left:-15px; margin-right:-15px;">
                    <option value="">Category</option>
                    <option value="Test 1" @if(old('ration_card_cat') == "Test 1")  selected  @endif >Test 1</option>
                    <option value="Test 2" @if(old('ration_card_cat') == "Test 2")  selected  @endif >Test 2</option>
                    <option value="Test 3" @if(old('ration_card_cat') == "Test 3")  selected  @endif >Test 3</option>                                       
                    </select>
                   
                  </div>
                  
                  <div class="col-md-8">
                   
                      <input style="margin-left:-15px; margin-right:-15px;" type="text" name="ration_card_no" id="ration_card_no" class="form-control NumOnly" placeholder="Card Number" maxlength="10" value="{{ old('ration_card_no') }}"  maxlength="10"  tabindex="2">
                      
                  </div>
                
                </div>
                 <span id="error_ration_card_cat" class="text-danger"></span><br />
                 <span id="error_ration_card_no" class="text-danger"></span>
                </div>

                <div class="form-group col-md-4">
                 <label class="">AHL TIN</label>
                 <input type="text" name="ahl_tin" id="ahl_tin" class="form-control special-char"  placeholder="AHL TIN" maxlength="90" value="{{ old('ahl_tin') }}" tabindex="3" />
                 <span id="error_ahl_tin" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="">Aadhar No., if available</label>
                 <input type="text" name="aadhar_no" id="aadhar_no" class="form-control NumOnly" placeholder="Aadhar No." maxlength="12" value="{{ old('aadhar_no') }}"  tabindex="4" />
                 <span id="error_aadhar_no" class="text-danger"></span>
                </div>
 
</div>
<div class="row">
                <div class="form-group col-md-4">
                 <label class="required-field">EPIC/Voter Id.No.</label>
                 <input type="text" name="epic_voter_id" id="epic_voter_id" class="form-control"  placeholder="EPIC/Voter Id.No."  maxlength="20" value="{{ old('epic_voter_id') }}" tabindex="5" />
                 <span id="error_epic_voter_id" class="text-danger"></span>
                </div>

                <div class="form-group col-md-4">
                 <label class="">PAN, if available</label>
                 <input type="text" name="pan_no" id="pan_no" class="form-control special-char" placeholder="PAN" maxlength="10" value="{{ old('pan_no') }}" onkeyup="this.value = this.value.toUpperCase();" tabindex="6" />
                 <span id="error_pan_no" class="text-danger"></span>
                </div>

               

                <!--  <div class="form-group col-md-12">
                 <label>If BPL</label>                
                 <input type="checkbox" name="if_bpl" id="if_bpl" value="1" ><br>
                 <span id="error_if_bpl" class="text-danger"></span>
                </div> -->

                <div class="form-group col-md-4">
                 <label class="">BPL Seq No., if avaiable</label>
                 <input type="text" name="bpl_seq_no" id="bpl_seq_no" class="form-control special-char" placeholder="BPL Seq No." maxlength="12" value="{{ old('bpl_seq_no') }}" tabindex="7" >
                 <span id="error_bpl_seq_no" class="text-danger"></span>
                </div>

</div>
<div class="row">
                <div class="form-group col-md-4">
                 <label class="">BPL Id No., if avaiable</label>
                 <input type="text" name="bpl_id_no" id="bpl_id_no" class="form-control special-char" placeholder="BPL Id No." maxlength="12" value="{{ old('bpl_id_no') }}" tabindex="8" >
                 <span id="error_bpl_id_no" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="">BPL Total Score, if avaiable</label>
                 <input type="text" name="bpl_total_score" id="bpl_total_score" class="form-control NumOnly" placeholder="BPL Total Score" maxlength="20" value="{{ old('bpl_total_score') }}"  tabindex="9" >
                 <span id="bpl_total_score" class="text-danger"></span>
                </div>

                 <div class="form-group col-md-4">
                  &nbsp;
                </div>

                

       </div>         

              
                <br />

                 <br />
                <div class="col-md-12" align="center">

                <button type="button" name="previous_btn_id_details" id="previous_btn_id_details" class="btn btn-info btn-lg">Previous</button>
                 <button type="button" name="btn_id_details" id="btn_id_details" class="btn btn-success btn-lg">Next</button>
                 <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                </div>
                <br />
               </div>
              </div>
             </div>






             <div class="tab-pane fade" id="contact_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Contact Details</b></h4></div>
               <div class="panel-body">

                <div class="row">
                              
               <div class="form-group col-md-4">
                 <label class="required-field">State</label>
                 <input type="text" id="state" name="state" class="form-control" placeholder="" value="WEST BENGAL" readonly="true" tabindex="1">
                 <span id="error_state" class="text-danger"></span>
                </div>              
             

               <div class="form-group col-md-4">
                 <label class="required-field">District</label>
                 <select name="district" id="district" class="form-control  js-district" tabindex="2">
                  <option value="">--Select  --</option>
                   @foreach ($districts as $district)
                  <option value="{{$district->district_code}}"   > {{$district->district_name}}</option>
                  @endforeach
                </select>
                 <span id="error_district" class="text-danger"></span>

                </div>

            


               <div class="form-group col-md-4">
                 <label class="required-field">Assembly Constituency</label>
                <select name="asmb_cons" id="asmb_cons" class="form-control  select2 js-assembly" tabindex="3">
                    <option value="">--Select--</option>
                   
                  </select>
                 <span id="error_asmb_cons" class="text-danger"></span>

                </div>



              </div>
              <div class="row">

              <div class="form-group col-md-4" id="divUrbanCode">
                <label class="required-field">Rural/Urban</label>
                
                <select name="urban_code" id="urban_code" class="form-control js-urban" tabindex="4">
                  <option value="">--Select  --</option>
                  <option value="2"  @if(old('urban_code') == 2)  selected  @endif >Rural</option>
                  <option value="1"   @if(old('urban_code') == 1)  selected  @endif >Urban</option>
                  
                   
                </select>
                  <span id="error_urban_code" class="text-danger"></span>
              </div>

            

                 

                <div class="form-group col-md-4" id="divBodyCode">
                <label class="required-field">Block/Municipality/Corp.</label>
                
                <select name="block" id="block" class="form-control  select2 js-localbody" tabindex="5">
                  <option value="">--Select --</option>
                  
                   
                </select>
                  <span id="error_block" class="text-danger"></span>
              </div>

             
              <div class="form-group col-md-4" id="divBodyCode">
                <label  class="required-field">GP/Ward No</label>
                
                <select name="gp_ward" id="gp_ward" class="form-control  select2 js-gpward" tabindex="6">
                  <option value="">--Select --</option>
                  
                   
                </select>
                    <span id="error_gp_ward" class="text-danger"></span>
              </div>
               

               </div>
              <div class="row">
                 <div class="form-group col-md-4">
                 <label class="required-field">Village/Town/City</label>
                 <input type="text" id="village" name="village" class="form-control special-char" placeholder="Village/Town/City" maxlength="300" value="{{ old('village') }}" tabindex="7" >
                 <span id="error_village" class="text-danger"></span>
                </div>
                 <div class="form-group col-md-4">
                 <label class="">House/Premise No.</label>
                 <input type="text" id="house" name="house" class="form-control special-char" placeholder="House/Premise No." maxlength="300" value="{{ old('house') }}" tabindex="8" >
                 <span id="error_house" class="text-danger"></span>
                </div>

                <div class="form-group col-md-4">
                 <label class="required-field">Post Office</label>
                 <input type="text" id="post_office" name="post_office" class="form-control special-char" placeholder="Post Office" maxlength="300" value="{{ old('post_office') }}" tabindex="9">
                 <span id="error_post_office" class="text-danger"></span>
                </div>

                 </div>
              <div class="row">

                 <div class="form-group col-md-4">
                 <label class="required-field">Pin Code</label>
                 <input type="text" id="pin_code" name="pin_code" class="form-control NumOnly" placeholder="Pin Code" maxlength="6" value="{{ old('pin_code') }}"  tabindex="10" >
                 <span id="error_pin_code" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="required-field">Police Station</label>
                 <input type="text" id="police_station" name="police_station" class="form-control special-char" placeholder="Police Station" maxlength="200" value="{{ old('police_station') }}" tabindex="11" >
                 <span id="error_police_station" class="text-danger"></span>
                </div>

                 <div class="form-group col-md-4">
                 <label class="required-field">Number of years Dwelling in WB</label>
                 <input type="text" id="residency_period" name="residency_period" class="form-control NumOnly" maxlength="3" placeholder="Number of years Dwelling in WB"  value="{{ old('residency_period') }}" tabindex="12" >
                 <span id="error_residency_period" class="text-danger"></span>
                </div>

                 </div>
              <div class="row">

                <div class="form-group col-md-4">
                 <label class="required-field">Mobile Number</label>
                 <input type="text" id="mobile_no" name="mobile_no" class="form-control NumOnly" placeholder="Mobile No" maxlength="10" value="{{ old('mobile_no') }}"  tabindex="13" >
                 <span id="error_mobile_no" class="text-danger"></span>
                </div>



                <div class="form-group col-md-4">
                 <label class="">Email Id., if available</label>
                 <input type="text" id="email" name="email" class="form-control" placeholder="Email Id." maxlength="200" value="{{ old('email') }}" tabindex="14" >
                 <span id="error_email" class="text-danger"></span>
                </div>  

               </div>

             
                <br />
                 <br /> <br />
                <div class="col-md-12" align="center">
                 <button type="button" name="previous_btn_contact_details" id="previous_btn_contact_details" class="btn btn-info btn-lg">Previous</button>
                 <button type="button" name="btn_contact_details" id="btn_contact_details" class="btn btn-success btn-lg">Next</button>
                </div>
               
               </div>
              </div>
             </div>

               <div class="tab-pane fade" id="bank_details" style="min-height: 440px;">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Bank Account Details</b></h4></div>
               <div class="panel-body">

                 <div class="form-group col-md-6">
                 <label class="required-field">Bank Name</label>
                 <input type="text" name="name_of_bank" id="name_of_bank" class="form-control special-char" placeholder="Bank Name"  value="{{ old('name_of_bank') }}" maxlength="200" tabindex="1" />
                 <span id="error_name_of_bank" class="text-danger"></span>
                </div>
               
               
                
                <div class="form-group col-md-6">
                 <label class="required-field">Bank Branch Name</label>
                 <input type="text" name="bank_branch" id="bank_branch" class="form-control special-char" placeholder="Bank Branch Name"  value="{{ old('bank_branch') }}" maxlength="300" tabindex="2" />
                 <span id="error_bank_branch" class="text-danger"></span>
                </div>

                <div class="form-group col-md-6">
                 <label class="required-field">Bank Account No.</label>
                 <input type="text" name="bank_account_number" id="bank_account_number" class="form-control NumOnly" placeholder="Bank Account No"  value="{{ old('bank_account_number') }}" maxlength='16' tabindex="3" />
                 <span id="error_bank_account_number" class="text-danger"></span>
                </div>

                <div class="form-group col-md-6">
                 <label class="required-field">IFS Code</label>
                 <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control special-char" placeholder="IFSC Code" onkeyup="this.value = this.value.toUpperCase();"  value="{{ old('bank_ifsc_code') }}" maxlength='16' tabindex="4" />
                 <span id="error_bank_ifsc_code" class="text-danger"></span>
                </div>

                <br />

                <div class="col-md-12" align="center">
                 <button type="button" name="previous_btn_bank_details" id="previous_btn_bank_details" class="btn btn-info btn-lg">Previous</button>
                 <button type="button" name="btn_bank_details" id="btn_bank_details" class="btn btn-success btn-lg">Next</button>
                </div>
                <br />
               </div>
              </div>
             </div>

               <div class="tab-pane fade" id="type_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Type of Disability</b></h4></div>
               <div class="panel-body">

                  <div class="row">

                 <div class="form-group col-md-12" tabindex="1">

                  <label><h3>Type of Disability</h3></label> 
                  <br / >

                 <label>
                  <input type="checkbox" class="type-disability" name="type_disability[]" value="OH [Orthopedically Handicapped]" > OH [Orthopedically Handicapped],
                </label>

                <br / >

                <label>
                  <input type="checkbox" class="type-disability" name="type_disability[]" value="VH [Visually Handicapped]" > VH [Visually Handicapped],
                </label>

                 <br / >

                 <label>
                 <input type="checkbox" class="type-disability" name="type_disability[]" value="MI [Mentally Handicapped]" > HH [Hearing Handicapped],
                </label>
                <br / >
                <label>
               <input type="checkbox" class="type-disability" name="type_disability[]" value="MI [Mentally Handicapped]" > MI [Mentally Handicapped]
                </label>
                 <br / >    

                  <label>
               <input type="checkbox" class="type-disability" name="type_disability[]" value="MR [Mental Handicapped]" > MR [Mental Handicapped],
                </label>
                 <br / > 

                  <label>
               <input type="checkbox" class="type-disability" name="type_disability[]" value="MD [Multiple Handicapped]" > MD [Multiple Handicapped],
                </label>
                 <br / > 

                  <label>
               <input type="checkbox" class="type-disability" name="type_disability[]" value="LC [Leprosy Cured]" > LC [Leprosy Cured]
                </label>
                 <br / > 

                  <label>
               <input type="checkbox" class="type-disability" name="type_disability[]" value="NR [Nervous Disorder]" > NR [Nervous Disorder],
                </label>
                 <br / > 

                  <label>
               <input type="checkbox" class="type-disability" name="type_disability[]" value="OT [Others]" > OT [Others],
                </label>
                 <br / > 

                  <span id="error_type_disability" class="text-danger"></span>

                </div>

                 </div>

                 <div class="row">
                  <div class="form-group col-md-6">
                 <label class="required-field">Percentage of Disability.</label>
                 <input type="text" name="percentage_disability" id="percentage_disability" class="form-control NumOnly" placeholder="Percentage of Disability"  value="{{ old('Percentage of Disability') }}"   maxlength="3" tabindex="2" />
                 <span id="error_percentage_disability" class="text-danger"></span>
                </div>

                 <div class="form-group col-md-6">
                 <label class="required-field">Certifying Authority</label>
                 <input type="text" name="certifying_authority" id="certifying_authority" class="form-control special-char" placeholder="Certifying Authority"  value="{{ old('certifying_authority') }}"   maxlength="200" tabindex="3" />
                 <span id="error_certifying_authority" class="text-danger"></span>
                </div>

                 </div>
               
               
                              

                <br />

                <div class="col-md-12" align="center">
                 <button type="button" name="previous_btn_type_details" id="previous_btn_type_details" class="btn btn-info btn-lg">Previous</button>
                 <button type="button" name="btn_type_details" id="btn_type_details" class="btn btn-success btn-lg">Next</button>
                </div>
                <br />
               </div>
              </div>
             </div>
             

            <div class="tab-pane fade" id="experience_details">
              <div class="panel panel-default">
                 <div class="panel-heading"></h4></b>Enclosure List(Self Attested)</b></h4></div>
                  <div class="panel-body">

              
               


                            
                                   
                                      <div class="form-group col-md-12">
                                     <label class="required-field">Passport Photograph</label>
                                     <input type="file" name="passport_image" id="passport_image" class="form-control" tabindex="1" />
                                     <div class="imageSize">(Image type must be .jpg,.jpeg,.png,.gif and image size max 2MB)</div>
                                    <span id="error_passport_image" class="text-danger"></span>
                                    </div> 

                                    <div class="form-group col-md-12">
                                      &nbsp;

                                    <img id="passport_image_view" src="#" alt=""  width="200px" height="200px" />
                                   
                                    </div>

                                     <div class="form-group col-md-12">
                                     <label class="">Scan Copy of Form</label>
                                     <input type="file" name="signature_image" id="signature_image" class="form-control" tabindex="2" />
                                     <div class="imageSize">(File type must be .jpg,.jpeg,.png,.gif,.pdf and size max 2MB)</div>
                                    
                                     <span id="error_signature_image" class="text-danger"></span>
                                    </div>

                                     <div class="form-group col-md-12">
                                      &nbsp;
                                     <!-- <img id="signature_image_view" src="#" alt="" width="200px" height="200px" /> -->
                                   
                                    </div>

                                    <div class="form-group col-md-12">
                                     <label class="">Copy of Caste Certificate</label>
                                     <input type="file" name="cast_certificate_file" id="cast_certificate_file" class="form-control" tabindex="3" />
                                     <div class="imageSize">(File type must be .jpg,.jpeg,.png,.gif,.pdf and size max 2MB)</div>

                                     <!-- <img id="" src="#" alt="" width="200px" height="200px" /> -->
                                     <span id="error_cast_certificate_file" class="text-danger"></span>
                                    </div> 


                                     <div class="form-group col-md-12">
                                     <label class="required-field">Copy of Disability Certificate from Appropriate Authority</label>
                                     <input type="file" name="disability_certificate_file" id="disability_certificate_file" class="form-control" tabindex="4" />
                                     <div class="imageSize">(File type must be .jpg,.jpeg,.png,.gif,.pdf and size max 2MB)</div>

                                    <!--  <img id="" src="#" alt="" width="200px" height="200px" /> -->
                                     <span id="error_disability_certificate_file" class="text-danger"></span>
                                    </div>
                                     

                                     <div class="form-group col-md-12">
                                     <label class="">Copy of Digital Ration Card</label>
                                     <input type="file" name="digital_ration_card_file" id="digital_ration_card_file" class="form-control" tabindex="5" />
                                     <div class="imageSize">(File type must be .jpg,.jpeg,.png,.gif,.pdf and size max 2MB)</div>

                                    <!--  <img id="" src="#" alt="" width="200px" height="200px" /> -->
                                     <span id="error_digital_ration_card_file" class="text-danger"></span>
                                    </div>

                                    <div class="form-group col-md-12">
                                     <label class="">Copy of Aadhar Card, if available</label>
                                     <input type="file" name="aadhar_card_file" id="aadhar_card_file" class="form-control" tabindex="6" />
                                     <div class="imageSize">(File type must be .jpg,.jpeg,.png,.gif,.pdf and size max 2MB)</div>

                                   <!--   <img id="" src="#" alt="" width="200px" height="200px" /> -->
                                     <span id="error_aadhar_card_file" class="text-danger"></span>
                                    </div>

                                    <div class="form-group col-md-12">
                                     <label class="">Copy of Voter Id</label>
                                     <input type="file" name="voter_id_file" id="voter_id_file" class="form-control" tabindex="7" />
                                     <div class="imageSize">(File type must be .jpg,.jpeg,.png,.gif,.pdf and size max 2MB)</div>

                                    <!--  <img id="" src="#" alt="" width="200px" height="200px" /> -->
                                     <span id="error_voter_id_file" class="text-danger"></span>
                                    </div>

                                    <div class="form-group col-md-12">
                                     <label class="">Copy of Residential Certificate(Self Declaration)</label>
                                     <input type="file" name="residential_certificate_file" id="residential_certificate_file" class="form-control" tabindex="8" />
                                     <div class="imageSize">(File type must be .jpg,.jpeg,.png,.gif,.pdf and size max 2MB)</div>

                                    <!--  <img id="" src="#" alt="" width="200px" height="200px" /> -->
                                     <span id="error_residential_certificate_file" class="text-danger"></span>
                                    </div>

                                    <div class="form-group col-md-12">
                                     <label class="">Copy of Income Certificate(Self Declaration)</label>
                                     <input type="file" name="income_certificate_file" id="income_certificate_file" class="form-control" tabindex="9" />
                                     <div class="imageSize">(File type must be .jpg,.jpeg,.png,.gif,.pdf and size max 2MB)</div>

                                    <!--  <img id="" src="#" alt="" width="200px" height="200px" /> -->
                                     <span id="error_income_certificate_file" class="text-danger"></span>
                                    </div>

                                    <div class="form-group col-md-12">
                                     <label class="">Copy of bank Pass book</label>
                                     <input type="file" name="bank_passbook_file" id="bank_passbook_file" class="form-control" tabindex="10" />
                                     <div class="imageSize">(File type must be .jpg,.jpeg,.png,.gif,.pdf and size max 2MB)</div>

                                    <!--  <img id="" src="#" alt="" width="200px" height="200px" /> -->
                                     <span id="error_bank_passbook_file" class="text-danger"></span>
                                    </div>  

                                     <div class="form-group col-md-12">
                                     <label class="">Others, please specify</label>
                                     <input type="file" name="other_file" id="other_file" class="form-control" tabindex="11" />
                                     <div class="imageSize">(File type must be .jpg,.jpeg,.png,.gif,.pdf and size max 2MB)</div>

                                    <!--  <img id="" src="#" alt="" width="200px" height="200px" /> -->
                                     <span id="error_other_file" class="text-danger"></span>
                                    </div>       

                                     
                                  

                                    <div align="center" class="col-md-12">
                                     <button type="button" name="previous_btn_experience_details" id="previous_btn_experience_details" class="btn btn-info btn-lg">Previous</button>

                                    <button type="button" name="btn_experience_details" id="btn_experience_details" class="btn btn-success btn-lg">Next</button>

                                    

                                   <!--  <input type="button" class="btn btn-success btn-lg" name="btn_submit_preview"    
                                    id="btn_submit_preview" value="Preview and Submit" data-toggle="modal" data-target="#2confirm-submit"> -->
                                    
                                    </div>
                                    <br />
                                  </div>
                                </div>
                                </div>



                      <div class="tab-pane fade" id="decl_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Self Declaration</b></h4></div>


               <div class="panel-body">

                <div class="row">


                <div class="form-group col-md-12">
                 <label class="">In the event of my death, I hereby nominate
                 (Please mention Name, Address &
 Relationship) </label>
               
                </div>
                  
                  </div>


                 <div class="row">


                
                 <div class="form-group col-md-4">
                 <label class="">Name</label>
                 <input type="text" name="nominate_name" id="nominate_name" class="form-control txtOnly" placeholder="Name"  value="{{ old('nominate_name') }}" maxlength='200' tabindex="1" />
                 <span id="error_nominate_name" class="text-danger"></span>
                </div>

                 <div class="form-group col-md-4">
                 <label class="">Address</label>
                 <input type="text" name="nominate_address" id="nominate_address" class="form-control special-char" placeholder="Address"  value="{{ old('nominate_address') }}" maxlength='200' tabindex="2" />
                 <span id="error_nominate_address" class="text-danger"></span>
                </div>

                 <div class="form-group col-md-4">
                 <label class="">Relationship</label>
                 <input type="text" name="nominate_relationship" id="nominate_relationship" class="form-control txtOnly" placeholder="Relationship"  value="{{ old('nominate_relationship') }}" maxlength='200' tabindex="3" />
                 <span id="error_nominate_relationship" class="text-danger"></span>
                </div>


                  </div>
                 <div class="row">


                <div class="form-group col-md-12">
                 <label class="">to receive the rest amount payable to me till my death</label>
               </div>


                 </div>
                 <div class="row aadhar-text">




                <div class="form-group col-md-12 " >
                 <label class="">I  <select> <option value=""> give </option>
                 <option value="">do not give </option> </select> consent to the use of the Aadhaar No.for authenticating my identity for social security pension</label>
                 </div>


                   </div>


                <!--  <div class="row">

                  <div class="form-group col-md-4">
                 <label class="">Presently, I am reciving following pension(s) from</label>
                <select class="aided-organization form-control" name="org_val" id="org_val" onchange="showOrganizationTextbox_5()">

                  <option value="">select</option>
                   <option value="Central Govt.">Central Govt.</option>
                    <option value="State Govt.">State Govt.</option>
                      <option value="Local Administration">Local Administration</option>
                      <option value="Govt.
 Adied Organization">Govt.
 Adied Organization</option>
</select>
                 <span id="" class="text-danger"></span>
                </div>

                 <div class="form-group col-md-4">
                 <label class="">&nbsp;</label>

               </div>
                 <div class="form-group col-md-4">
                 <label class="">&nbsp;</label>

               </div>


                 </div>
                 <div class="row">


                 <div class="form-group col-md-4 ">
                 <label class="">1.</label>
                 <input type="text" name="org_name_1" id="text_1" class="form-control" placeholder=""  value="{{ old('org_name_1') }}" />
                 <span id="" class="text-danger"></span>
                </div>

                <div class="form-group col-md-4 ">
                 <label class="">2.</label>
                 <input type="text" name="org_name_2" id="text_2" class="form-control" placeholder=""  value="{{ old('org_name_2') }}" />
                 <span id="" class="text-danger"></span>
                </div>


                 <div class="form-group col-md-4">
                 <label class="">&nbsp;</label>

               </div>

                 </div> -->

                <div class="row">

                 <div class="form-group col-md-12" tabindex="4">

                   <label>Presently, I am reciving following pension(s) from</label>

                   <br / >

                 <label>
                  <input type="checkbox" class="receive-pension" name="receive_pension[]" value="Central Govt" > Central Govt.,
                </label>

                <br / >

                <label>
                  <input type="checkbox" class="receive-pension" name="receive_pension[]" value="State Govt" > State Govt.,
                </label>

                 <br / >

                 <label>
                 <input type="checkbox" class="receive-pension" name="receive_pension[]" value="Local Administration" > Local Administration,
                </label>
                <br / >
                <label>
               <input type="checkbox" class="receive-pension" name="receive_pension[]" value="Govt.
 Adied Organization" > Govt.
 Adied Organization,
                </label>
                 <br / >      
                </div>

                 </div>


                <div class="row">

                 <div class="form-group col-md-12" tabindex="5">

                   <label>Presently, I am receiving the following social Security Pension/s (Please tick)</label>

                   <br / >

                 <label>
                  <input type="checkbox" class="social-security-pension" name="social_security_pension[]" value="NSAP Old Age"> NSAP Old Age,
                </label>

                <br / >

                <label>
                  <input type="checkbox" class="social-security-pension" name="social_security_pension[]" value="NSAP Widow Pension" > NSAP Widow Pension,
                </label>

                 <br / >

                 <label>
                 <input type="checkbox" class="social-security-pension" name="social_security_pension[]" value="NSAP Disability Pension" > NSAP Disability Pension,
                </label>
                <br / >
                <label>
                  <input type="checkbox" class="social-security-pension" name="social_security_pension[]" value="Old Age Pension" > Old Age Pension,
                </label>

                 <br / >

                <label>
                 <input type="checkbox" class="social-security-pension" name="social_security_pension[]" value="Widow Pension" > Widow Pension,
                </label>

                 <br / >
                <label>
                  <input type="checkbox" class="social-security-pension" name="social_security_pension[]" value="Disability Pension" > Disability Pension,
                </label>

                 <br / >
                <label>
                 <input type="checkbox" class="social-security-pension" name="social_security_pension[]" value="Lok Prasar Prakalpa" > Lok Prasar Prakalpa,
                </label>

                 <br / >

                <label>
                  <input type="checkbox" class="social-security-pension" name="social_security_pension[]" value="Fisherman's Old Age Pension" > Fisherman's Old Age Pension,
                </label>

                 <br / >

                <label>
                 <input type="checkbox" class="social-security-pension" name="social_security_pension[]" value="Farmers Old Age Pension" > Farmers Old Age Pension,
                </label>

                 <br / >
                <label>
                  <input type="checkbox" class="social-security-pension" name="social_security_pension[]" value="Artisan/Weaver Old Age Pension" > Artisan/Weaver Old Age Pension,
                </label>                
                </div>

                 </div>

                
               
               
               
               
               
                
               

                <br />

                


                                    <div align="center" class="col-md-12">

                                     <button type="button" name="previous_btn_decl_details" id="previous_btn_decl_details" class="btn btn-info btn-lg">Previous</button>
                                    <!--  <button type="button" name="btn_experience_details" id="btn_experience_details" class="btn btn-success btn-lg">Next</button> -->

                                    <input type="button" class="btn btn-success btn-lg" name="btn_submit_preview"    
                                    id="btn_submit_preview" value="Preview and Submit" data-toggle="modal" data-target="#confirm-submit_">
                                    
                                    </div>



                <br />


               </div>
              </div>
             </div>





            </div>

  <div class="modal fade" id="confirm-submit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
               </button>
               <h2 class="modal-title" style="text-align: center;"> Confirm Submit </h2>
               
            </div>
            <div class="modal-body">
                <h4 style="text-align: center;">Are you sure you want to submit the following details?</h4>

                <!-- We display the details entered by the user here -->


                <div class="section1">

                  <!-- <div class="row color1">
                  <div class="col-md-12"><h2>Information Form for SC/ST Pension Scheme 2020</h2></div>
                </div> -->
                       
                      <!--  <div class="row">
                        <div class="col-md-6">
                        <div class="modal_field_name"></div>
                        <div class="modal_field_value" id="">
                          
                        
                        <img src="http://localhost/pension/public/bower_components/download.jpg" width="200px" height="200px">
                        </div>
                        </div>                        
                      
                      
                      
                     
                     </div> -->


                  <div class="row">


                    <div class="col-md-3">
                        <div class="modal_field_name"></div>
                        <div class="modal_field_value" id=""> <img src="{{ url('/')}}/bower_components/Emblem_of_West_Bengal.png" width="180px" height="200px"></div>
                    </div>


                    

                     <div class="col-md-6">
                      <div align="center">
                        <div class="modal_field_name"></div>
                        <div class="modal_field_value" id=""><p><h2>Government of West Beangal</h2></p></div>
                        <p><h2>Jai Bangla Pension Scheme</h2></p>
                       <!--  <p><h3> Information Form for SC/ST Pension Scheme 2020</h3></p></div> -->
                          </div>
                    </div>

                    <div class="col-md-3">
                        <div class="modal_field_name"></div>
                        <div class="modal_field_value" id=""> <img id="passport_image_view_modal" src="#" alt="" width="200px" height="200px" /></div>
                    </div>
                </div>



                 <div class="section1">
                <div class="row color1">
                  <div class="col-md-12"><h2>Personal Details</h2></div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="modal_field_name">Name:</div>
                        <div class="modal_field_value" id="name_modal"></div>
                    </div>
                </div>
                   
                   
                     
                     <div class="row">
                        
                        <div class="col-md-6">
                        <div class="modal_field_name" style="margin-right:6%;">Gender:</div>
                        <div class="modal_field_value" id="gender_modal"></div>
                        </div>

                        <div class="col-md-6">
                        <div class="modal_field_name" style="margin-right:6%;">Date of Birth:</div>
                        <div class="modal_field_value" id="dob_modal" ></div>
                        </div>

                      </div>

                      <div class="row">
                      <div class="col-md-12">
                        <div class="modal_field_name">Father's Name:</div>
                        <div class="modal_field_value" id="father_name_modal"></div>
                      </div>
                      </div>

                      <div class="row">
                      <div class="col-md-12">
                        <div class="modal_field_name">Mother's Name:</div>
                        <div class="modal_field_value" id="mother_name_modal"></div>
                      </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                        <div class="modal_field_name" style="margin-right:6%;">Caste:</div>
                        <div class="modal_field_value" id="caste_category_modal"></div>
                        </div>
                        <div class="col-md-6">
                        <div class="modal_field_name" style="margin-right:6%;">Marital Status:</div>
                        <div class="modal_field_value" id="marital_status_modal"></div>
                        </div>
                    </div>

                    <div class="row">
                      <div class="col-md-12">
                        <div class="modal_field_name">Spouse Name, if applicable:</div>
                        <div class="modal_field_value" id=spouse_name_modal></div>
                      </div>
                    </div>
                    

                     <div class="row">
                      <div class="col-md-12">
                        <div class="modal_field_name">Monthly Family Income(Rs.):</div>
                        <div class="modal_field_value" id=monthly_income_modal></div>
                      </div>
                    </div>
                     
                    
                   </div>

                 <div class="section1">
                       <div class="row color1">
                        <div class="col-md-12"><h2 style="">Personal Identification Number(S)</h2></div>
                       </div>
                       <div class="row">
                        <div class="col-md-6">
                        <div class="modal_field_name">Digital Ration Card No.:</div>
                        <div class="modal_field_value" id="ration_card_no_modal"></div>
                        </div>                        
                      
                        <div class="col-md-6">
                        <div class="modal_field_name">AHL TIN:</div>
                        <div class="modal_field_value" id="ahl_tin_modal"></div>
                        </div>
                       </div>
                       <div class="row">
                        <div class="col-md-6">
                         <div class="modal_field_name">Aadhaar No., if available:</div>
                         <div class="modal_field_value" id="aadhar_no_modal"></div>
                        </div>
                      
                        <div class="col-md-6">
                          <div class="modal_field_name">EPIC/Voter Id.No.:</div>
                          <div class="modal_field_value" id="epic_voter_id_modal"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                        <div class="modal_field_name">PAN, if available:</div>
                        <div class="modal_field_value" id="pan_no_modal"></div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-4">                      
                        <div class="modal_field_name" style="margin-right:6%;">BPL Seq No., if avaiable:</div>
                        <div class="modal_field_value" id="bpl_seq_no_modal"></div>
                        </div>
                        <div class="col-md-4">
                        <div class="modal_field_name" style="margin-right:6%;">BPL Id No., if avaiable:</div>
                        <div class="modal_field_value" id="bpl_id_no_modal"></div>
                        </div>

                        <div class="col-md-4">
                        <div class="modal_field_name" style="margin-right:6%;">BPL Total Score, if avaiable:</div>
                        <div class="modal_field_value" id="bpl_total_score_modal"></div>
                        </div>
                        

                    </div>
                      
                     
                     </div>


               

                  <div class="section1 ">   
                    <div class="row color1">
                      <div class="col-md-12"><h2 >Contact Details</h2></div>
                    </div>

                    <div class="row">
                      <div class="col-md-12">
                        <div class="modal_field_name">State:</div>
                        <div class="modal_field_value" id="state_modal"></div>
                      </div>
                      <div class="col-md-12">
                        <div class="modal_field_name">Assembly Constitution:</div>
                        <div class="modal_field_value" id="asmb_cons_modal"></div>
                      </div>
                        <div class="col-md-12">
                        <div class="modal_field_name">District:</div>
                        <div class="modal_field_value" id="district_modal"></div>
                      </div>

                      <div class="col-md-12">
                        <div class="modal_field_name">Block/Municipality/Corp:</div>
                        <div class="modal_field_value" id="block_modal"></div>
                      </div>

                      <div class="col-md-12">
                        <div class="modal_field_name">GP/Ward No.:</div>
                        <div class="modal_field_value" id="gp_ward_modal"></div>
                      </div>

                     
                      
                      <div class="col-md-12">
                        <div class="modal_field_name">Village/Town/City:</div>
                        <div class="modal_field_value" id="village_modal"></div>
                      </div>
                      <div class="col-md-12">
                        <div class="modal_field_name">House/Premise No.:</div>
                        <div class="modal_field_value" id="house_modal"></div>
                      </div>
                      <div class="col-md-12">
                        <div class="modal_field_name">Post Office:</div>
                        <div class="modal_field_value" id="post_office_modal"></div>
                      </div>
                      <div class="col-md-12">
                        <div class="modal_field_name">Pin Code:</div>
                        <div class="modal_field_value" id="pin_code_modal"></div>
                      </div>

                       <div class="col-md-12">
                        <div class="modal_field_name">Police Station:</div>
                        <div class="modal_field_value" id="police_station_modal"></div>
                      </div>

                     


                       <div class="col-md-12">
                        <div class="modal_field_name">Number of years Dwelling in WB:</div>
                        <div class="modal_field_value" id="residency_period_modal"></div>
                      </div>

                      <div class="col-md-12">
                        <div class="modal_field_name">Mobile Number:</div>
                        <div class="modal_field_value" id="mobile_no_modal"></div>
                      </div>
                      <div class="col-md-12">
                        <div class="modal_field_name">Email Id., if available:</div>
                        <div class="modal_field_value" id="email_modal"></div>
                      </div>

                    </div>
                    
                  </div>

                      <div class="section1">
                       <div class="row color1">
                        <div class="col-md-12"><h2 style="">Bank Account Details</h2></div>
                       </div>
                       <div class="row">
                                               
                      
                        <div class="col-md-12">
                         <div class="modal_field_name">Bank Name:</div>
                         <div class="modal_field_value" id="name_of_bank_modal"></div>
                        </div>

                         <div class="col-md-12">
                          <div class="modal_field_name">Bank Branch Name:</div>
                          <div class="modal_field_value" id="bank_branch_modal"></div>
                        </div>

                        
                      

                         <div class="col-md-12">
                        <div class="modal_field_name">Bank Account No.:</div>
                        <div class="modal_field_value" id="bank_account_number_modal"></div>
                        </div>

                        <div class="col-md-12">
                        <div class="modal_field_name">IFSC Code:</div>
                        <div class="modal_field_value" id="bank_ifsc_code_modal"></div>
                        </div> 
                        </div>
                     
                     
                     
                     </div>

                       <div class="section1">
                       <div class="row color1">
                        <div class="col-md-12"><h2 style="">Type of Disability</h2></div>
                       </div>
                       <div class="row">
                                               
                      
                        <div class="col-md-12">
                         <div class="modal_field_name">Type of Disability:</div>
                         <div class="modal_field_value" id="type-disability-modal"></div>
                        </div>

                         <div class="col-md-12">
                         <div class="modal_field_name">Percentage Disability:</div>
                         <div class="modal_field_value" id="percentage_disability_modal"></div>
                        </div>

                         <div class="col-md-12">
                         <div class="modal_field_name">Certifying Authority:</div>
                         <div class="modal_field_value" id="certifying_authority_modal"></div>
                        </div>
                     
                     
                     </div>

                      </div>


                      <div class="section1">
                       <div class="row color1">
                        <div class="col-md-12"><h2 style="">Self Declaration</h2></div>
                       </div>




                       <div class="row">

                         <div class="col-md-12">
                         <div class="modal_field_name">In the event of my death, I hereby nominate
                 (Please mention Name, Address &
 Relationship)</div>
                         
                        </div>
                                               
                      
                        <div class="col-md-12">
                         <div class="modal_field_name">Name:</div>
                         <div class="modal_field_value" id="nominate_name_modal"></div>
                        </div>

                         <div class="col-md-12">
                          <div class="modal_field_name">Address:</div>
                          <div class="modal_field_value" id="nominate_address_modal"></div>
                        </div>

                        
                      

                         <div class="col-md-12">
                        <div class="modal_field_name">Relationship:</div>
                        <div class="modal_field_value" id="nominate_relationship_modal"></div>
                        </div>

<!-- 
                         <div class="col-md-12">
                         <div class="modal_field_name">to receive the rest amount payable to me till my death</div>
                         
                        </div> -->

                         <div class="col-md-12 aadhar-text-modal">
                        <div class="modal_field_name">I give consent to the use of the Aadhaar No.for authenticating my identity for social security pension</div>
                        <div class="modal_field_value" id="org_val_modal"></div>
                        </div>


                         <div class="col-md-12">
                        <div class="modal_field_name">Presently, I am reciving following pension(s) from:</div>
                        <div class="modal_field_value" id="receive-pension-modal"></div>
                        </div>                        

                        <div class="col-md-12">
                        <div class="modal_field_name">Presently, I am receiving the following social Security Pension/s </div>
                        <div class="modal_field_value" id="checkbox-tick-modal">Nil</div>
                        </div>



                       
                     
                     
                     
                     </div>

                    <!--  <div class="section1">
                      <div class="row color1">
                        <div class="col-md-12"><h2 style="">Enclosure List(Self Attested)</h2></div>
                      </div>
                       <div class="row">
                      

                        <div class="col-md-12">
                        <div class="modal_field_name">Signature of the applicant</div>
                        <div class="modal_field_value" id="">
                          

                           <img id="blah2_modal" src="#" alt="" width="200px" height="200px" />
                        </div>
                        </div>
                       </div>
                     
                    
                      
                        
                        </div> -->
                        
                        </div>
                     

                      </div>
                       </div>
                 
                      


          

            <div class="modal-footer" style="text-align: center;">

             <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" id="submit_loader" width="150px" height="150px"></div>
             
            </div>
          

            <div class="modal-footer" style="text-align: center;">

              <button type="button" class="btn btn-default btn-lg" data-dismiss="modal" modal-cancel>Cancel</button>
             <!--  <input type="submit"  id="submit" value="Submit"class="btn btn-success success btn-lg modal-submit"> -->

              <button type="submit"  id="submit" value="Submit" class="btn btn-success success btn-lg modal-submit" >Submit </button>
              <button type="button"  id="submitting" value="Submit" class="btn btn-success success btn-lg" disabled >Submitting please wait</button>
             
            </div>
        </div>
  <!--   </div> -->
<!-- </div> -->



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
  <!------- footer starts ------->
    
    <!-- <div class="clearfix"></div> -->
    <!------- footer end ------->
  <!-- Footer -->
  </div>
  <div class="footer" style="margin-bottom: 0px; height: auto;">
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
</div>
  
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

 <!-- jQuery 2.1.3 -->
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<script src="{{ URL::asset('js/site.js') }}"></script>


<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script>
  $('.select2').select2();
</script>

<script>

function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#passport_image_view').attr('src', e.target.result);
       $('#passport_image_view_modal').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]);
  }
}

$("#passport_image").change(function() {
  $("#passport_image_view").show();
  readURL(this);
});


function readURL2(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#signature_image_view').attr('src', e.target.result);
      $('#signature_image_view_modal').attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]);
  }
}

$("#signature_image_").change(function() {
  readURL2(this);
});



$(document).ready(function(){

  $(".aadhar-text").hide();
  $(".aadhar-text-modal").hide();
  $("#submitting").hide();
  $("#submit_loader").hide();
  $("#passport_image_view").hide();
  $("#spouse_section").hide();

    if($("#marital_status").val() == "Married" )
    {
        $("#spouse_section").show(); 
    }


    $("#marital_status").on('change', function(){

      var marital_status =  $("#marital_status").val();
      if(marital_status == "Married")
      {
        $("#spouse_section").show(); 
      } 
      else
      {
        $("#spouse_section").hide();
      }
    });


 


  $(".receive-pension").click(function(){  
      

        var selectedRP = new Array();
        var n1 = jQuery(".receive-pension:checked").length;
        if (n1 > 0){
         
            jQuery(".receive-pension:checked").each(function(){
                selectedRP.push( $(this).val());
            });
        }  

        $("#receive-pension-modal").text(selectedRP)

        //alert(selectedCategory);
 });


  $(".social-security-pension").click(function(){
   
      

        var selectedCategory = new Array();
        var n2 = jQuery(".social-security-pension:checked").length;
        if (n2 > 0){
         
            jQuery(".social-security-pension:checked").each(function(){
                selectedCategory.push($(this).val());
            });
        }  

        $("#checkbox-tick-modal").text(selectedCategory)

        //alert(selectedCategory);
 });


   $(".type-disability").click(function(){  
      

        var selectedTD = new Array();
        var n3 = jQuery(".type-disability:checked").length;
        if (n3 > 0){
         
            jQuery(".type-disability:checked").each(function(){
                selectedTD.push( $(this).val());
            });
        }  

        $("#type-disability-modal").text(selectedTD)
        $("#type_disability_hidden").text(selectedTD)
        

        //alert(selectedCategory);
 });





  $("#dob").on('blur',function(){
   //alert($('#dob').val());

    //var today = new Date();

   

    var today = new Date('2020-01-01');




    //alert(today)
    var birthDate = new Date($('#dob').val());
    
    var age = today.getFullYear() - birthDate.getFullYear();
   
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }     
    $('#txt_age').val(age);
});
//prevent numeric entry in text 

 $('.txtOnly').keypress(function (e) {
            var regex = new RegExp(/^[a-zA-Z\s]+$/);
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            else {
                e.preventDefault();
                return false;
            }
        });

  $(".NumOnly").keyup(function(event) {
              
        $(this).val($(this).val().replace(/[^\d].+/, ""));
            if ((event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        }); 



// $('.txtOnly').keydown(function (e) {
  
//     if (e.altKey) {
    
//       e.preventDefault();
      
//     } else {
    
//       var key = e.keyCode;
      
//       if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90))) {
      
//         e.preventDefault();
        
//       }

//     }
    
//   });

// $('.NumOnly').keydown(function (e) {
  
//     if (e.altKey) {
    
//       e.preventDefault();
      
//     } else {
    
//       var key = e.keyCode;
      
//       if (key > 31 && (key < 48 || key > 57)) {
      
//         e.preventDefault();
        
//       }

//     }
    
//   });

$('.special-char').keyup(function()
  {
    var yourInput = $(this).val();
    re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
    var isSplChar = re.test(yourInput);
    if(isSplChar)
    {
      var no_spl_char = yourInput.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
      $(this).val(no_spl_char);
    }
  });

 $(".price-field").keyup(function() 
        {
          var val = $(this).val();
          if(isNaN(val)){
          val = val.replace(/[^0-9\.]/g,'');
          if(val.split('.').length>2) 
          val =val.replace(/\.+$/,"");
        }
        $(this).val(val);        
        });




 
 $('#btn_personal_details').click(function(){  

//var error_title ='';
  var error_first_name = '';
  var error_last_name = '';
  var error_gender = '';
  var error_dob ="";
  var error_txt_age = '';
  var error_father_first_name = '';
  var error_father_last_name = '';

  var error_mother_first_name = '';
  var error_mother_last_name = '';
  var error_caste_category = '';
  var error_marital_status = '';

  var error_monthly_income = '';

  




  if($.trim($('#first_name').val()).length == 0)
  {
   error_first_name = 'First Name is required';
   $('#error_first_name').text(error_first_name);
   $('#first_name').addClass('has-error');
  }
  else
  {
   error_first_name = '';
   $('#error_first_name').text(error_first_name);
   $('#first_name').removeClass('has-error');
  }

  if($.trim($('#last_name').val()).length == 0)
  {
   error_last_name = 'Last Name is required';
   $('#error_last_name').text(error_last_name);
   $('#last_name').addClass('has-error');
  }
  else
  {
   error_last_name = '';
   $('#error_last_name').text(error_last_name);
   $('#last_name').removeClass('has-error');
  }

   if($.trim($('#gender').val()).length == 0)
  {
   error_gender = 'Gender is required';
   $('#error_gender').text(error_gender);
   $('#gender').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_gender = '';
   $('#error_gender').text(error_gender);
   $('#gender').next().find('.select2-selection').removeClass('has-error');
  }
  

 if($.trim($('#dob').val()).length > 0)
 {

    

     var string = $.trim($('#dob').val());   
     var result = string.split('-');
     var year = result[result.length - 3];

     

    if(year < 1900  || year > 2000 )
    {
     error_dob = "Date of Birth range is not properly";
     $('#error_dob').text(error_dob);
     $('#dob').addClass('has-error');
    }
    else
    {      
     error_dob = '';
     $('#error_dob').text(error_dob);
     $('#dob').removeClass('has-error');    

    }

 }
 
  

  

  if($.trim($('#txt_age').val()).length == 0)
  {
   error_txt_age = 'Age is required';
   $('#error_txt_age').text(error_txt_age);
   $('#txt_age').addClass('has-error');
  }
  else
  {

      if($.trim($('#txt_age').val()) < 60 || $.trim($('#txt_age').val()) >120 )
      {
       error_txt_age = 'Age range is not properly';
       $('#error_txt_age').text(error_txt_age);
       $('#txt_age').addClass('has-error');
      }
      else
      {
       error_txt_age = '';
       $('#error_txt_age').text(error_txt_age);
       $('#txt_age').removeClass('has-error');
      }

  }




  if($.trim($('#father_first_name').val()).length == 0)
  {
   error_father_first_name = 'First Name is required';
   $('#error_father_first_name').text(error_father_first_name);
   $('#father_first_name').addClass('has-error');
  }
  else
  {
   error_father_first_name = '';
   $('#error_father_first_name').text(error_father_first_name);
   $('#father_first_name').removeClass('has-error');
  }

  if($.trim($('#father_last_name').val()).length == 0)
  {
   error_father_last_name = 'Last Name is required';
   $('#error_father_last_name').text(error_father_last_name);
   $('#father_last_name').addClass('has-error');
  }
  else
  {
   error_father_last_name = '';
   $('#error_father_last_name').text(error_father_last_name);
   $('#father_last_name').removeClass('has-error');
  }

   if($.trim($('#mother_first_name').val()).length == 0)
  {
   error_mother_first_name = 'First Name is required';
   $('#error_mother_first_name').text(error_mother_first_name);
   $('#mother_first_name').addClass('has-error');
  }
  else
  {
   error_mother_first_name = '';
   $('#error_mother_first_name').text(error_mother_first_name);
   $('#mother_first_name').removeClass('has-error');
  }

  if($.trim($('#mother_last_name').val()).length == 0)
  {
   error_mother_last_name = 'Last Name is required';
   $('#error_mother_last_name').text(error_mother_last_name);
   $('#mother_last_name').addClass('has-error');
  }
  else
  {
   error_mother_lst_name = '';
   $('#error_mother_last_name').text(error_mother_last_name);
   $('#mother_last_name').removeClass('has-error');
  }

  if($.trim($('#caste_category').val()).length == 0)
  {
   error_caste_category = 'Caste is required';
   $('#error_caste_category').text(error_caste_category);
   $('#caste_category').addClass('has-error');
  }
  else
  {
   error_caste_category = '';
   $('#error_caste_category').text(error_caste_category);
   $('#caste_category').removeClass('has-error');
  }

  if($.trim($('#marital_status').val()).length == 0)
  {
   error_marital_status = 'Marital Status is required';
   $('#error_marital_status').text(error_marital_status);
   $('#marital_status').addClass('has-error');
  }
  else
  {
   error_marital_status = '';
   $('#error_marital_status').text(error_marital_status);
   $('#marital_status').removeClass('has-error');
  }

  if($.trim($('#monthly_income').val()).length == 0)
  {
   error_monthly_income = 'Monthly Family Income is required';
   $('#error_monthly_income').text(error_monthly_income);
   $('#monthly_income').addClass('has-error');
  }
  else
  {
   error_monthly_income = '';
   $('#error_monthly_income').text(error_monthly_income);
   $('#monthly_income').removeClass('has-error');
  } 

  if( error_first_name != '' || error_last_name != '' || error_gender != '' || error_txt_age != '' || error_father_first_name != '' || error_father_last_name != '' || error_mother_first_name != '' || error_mother_last_name != '' || error_caste_category != '' || error_marital_status != '' || error_monthly_income != '' )

  

   // if( error_first_name != '')


  {
   return false;
  }
  else
  {   
   

   /*******SD**********/
   $('#list_personal_details').removeClass('active active_tab1');
   $('#list_personal_details').removeAttr('href data-toggle');
   $('#personal_details').removeClass('active');
   $('#list_personal_details').addClass('inactive_tab1');
   $('#list_id_details').removeClass('inactive_tab1');
   $('#list_id_details').addClass('active_tab1 active');
   $('#list_id_details').attr('href', '#id_details');
   $('#list_id_details').attr('data-toggle', 'tab');
   $('#id_details').addClass('active in');
   /*******************/
  }

});


 $('#previous_btn_id_details').click(function(){

  $('#list_id_details').removeClass('active active_tab1');
  $('#list_id_details').removeAttr('href data-toggle');
  $('#id_details').removeClass('active in');
  $('#list_id_details').addClass('inactive_tab1');
  $('#list_personal_details').removeClass('inactive_tab1');
  $('#list_personal_details').addClass('active_tab1 active');
  $('#list_personal_details').attr('href', '#personal_details');
  $('#list_personal_details').attr('data-toggle', 'tab');
  $('#personal_details').addClass('active in');
 });

$('#btn_id_details').click(function(){  

  var error_ration_card_cat = '';
  var error_ration_card_no = '';
  var error_epic_voter_id = '';
  var error_aadhar_no = '';
  

  if($.trim($('#ration_card_cat').val()).length == 0)
  {
   error_ration_card_cat = 'Digital Ration Card Category is required';
   $('#error_ration_card_cat').text(error_ration_card_cat);
   $('#ration_card_cat').addClass('has-error');
  }
  else
  {
   error_ration_card_cat = '';
   $('#error_ration_card_cat').text(error_ration_card_cat);
   $('#ration_card_cat').removeClass('has-error');
  }

   if($.trim($('#ration_card_no').val()).length == 0)
  {
   error_ration_card_no = 'Digital Ration Card No. is required';
   $('#error_ration_card_no').text(error_ration_card_no);
   $('#ration_card_no').addClass('has-error');
  }
  else
  {
    if($.trim($('#ration_card_no').val()).length >10)
    {
      error_ration_card_no = 'Digital Ration Card No should not be greater bthan 10 digit';
     $('#error_ration_card_no').text(error_ration_card_no);
     $('#ration_card_no').addClass('has-error');

    }
    else
    {
      error_ration_card_no = '';
      $('#error_ration_card_no').text(error_ration_card_no);
      $('#ration_card_no').removeClass('has-error');

    }
  }

  if($.trim($('#epic_voter_id').val()).length == 0)
  {
   error_epic_voter_id = 'EPIC/Voter Id.No is required';
   $('#error_epic_voter_id').text(error_epic_voter_id);
   $('#epic_voter_id').addClass('has-error');
  }
  else
  {
   error_epic_voter_id = '';
   $('#error_epic_voter_id').text(error_epic_voter_id);
   $('#epic_voter_id').removeClass('has-error');
  }

  if($.trim($('#aadhar_no').val()) != "")
  {
    
   $(".aadhar-text").show();
   $(".aadhar-text-modal").show();

     if($.trim($('#aadhar_no').val()).length != 12)
     {
     error_aadhar_no = 'Aadhar Card No should be 12 digit ';
     $('#error_aadhar_no').text(error_aadhar_no);
     $('#aadhar_no').addClass('has-error');
     }
     else
     {
     error_aadhar_no = '';
     $('#error_aadhar_no').text(error_aadhar_no);
     $('#aadhar_no').removeClass('has-error');
    }
  } 
  
  


  if(  error_ration_card_cat != '' || error_ration_card_no != '' || error_epic_voter_id != '' || error_aadhar_no !="" )
  {
   return false;
  }
  else
  {
    
   

   /*******SD**********/
   $('#list_id_details').removeClass('active active_tab1');
   $('#list_id_details').removeAttr('href data-toggle');
   $('#id_details').removeClass('active');
   $('#list_id_details').addClass('inactive_tab1');
   $('#list_contact_details').removeClass('inactive_tab1');
   $('#list_contact_details').addClass('active_tab1 active');
   $('#list_contact_details').attr('href', '#contact_details');
   $('#list_contact_details').attr('data-toggle', 'tab');
   $('#contact_details').addClass('active in');
   /*******************/
  }

});


 $('#previous_btn_contact_details').click(function(){

  $('#list_contact_details').removeClass('active active_tab1');
  $('#list_contact_details').removeAttr('href data-toggle');
  $('#contact_details').removeClass('active in');
  $('#list_contact_details').addClass('inactive_tab1');

  $('#list_id_details').removeClass('inactive_tab1');
  $('#list_id_details').addClass('active_tab1 active');
  $('#list_id_details').attr('href', '#id_details');
  $('#list_id_details').attr('data-toggle', 'tab');
  $('#id_details').addClass('active in');
 });


 $('#btn_contact_details').click(function(){ 
 
  var error_district =''; 
  var error_asmb_cons ='';

   var error_urban_code ='';
  var error_block ='';
  var error_gp_ward ='';

  var error_village ='';  
  var error_post_office ='';
  var error_pin_code ='';
  var error_police_station ='';
  var error_residency_period ='';
  var error_mobile_no ='';

  var error_email ='';

  if($.trim($('#district').val()).length == 0)
  {
   error_district = 'District is required';
   $('#error_district').text(error_district);
   $('#district').addClass('has-error');
  }
  else
  {
   error_district = '';
   $('#error_district').text(error_district);
   $('#district').removeClass('has-error');
  }  

  if($.trim($('#asmb_cons').val()).length == 0)
  {
   error_asmb_cons = 'Assembly Constitution is required';
   $('#error_asmb_cons').text(error_asmb_cons);
   $('#asmb_cons').addClass('has-error');
  }
  else
  {
   error_asmb_cons = '';
   $('#error_asmb_cons').text(error_asmb_cons);
   $('#asmb_cons').removeClass('has-error');
  }

  if($.trim($('#urban_code').val()).length == 0)
  {
   error_urban_code = 'Rural/Urban is required';
   $('#error_urban_code').text(error_urban_code);
   $('#urban_code').addClass('has-error');
  }
  else
  {
   error_urban_code = '';
   $('#error_urban_code').text(error_urban_code);
   $('#urban_code').removeClass('has-error');
  }


  if($.trim($('#block').val()).length == 0)
  {
   error_block = 'Block/Municipality is required';
   $('#error_block').text(error_block);
   $('#block').addClass('has-error');
  }
  else
  {
   error_block = '';
   $('#error_block').text(error_block);
   $('#block').removeClass('has-error');
  }


  if($.trim($('#gp_ward').val()).length == 0)
  {
   error_gp_ward = 'GP/Ward No. is required';
   $('#error_gp_ward').text(error_gp_ward);
   $('#gp_ward').addClass('has-error');
  }
  else
  {
   error_gp_ward = '';
   $('#error_gp_ward').text(error_gp_ward);
   $('#gp_ward').removeClass('has-error');
  }



   if($.trim($('#village').val()).length == 0)
  {
   error_village = 'Village/Town/City is required';
   $('#error_village').text(error_village);
   $('#village').addClass('has-error');
  }
  else
  {
   error_village = '';
   $('#error_village').text(error_village);
   $('#village').removeClass('has-error');
  }

  if($.trim($('#post_office').val()).length == 0)
  {
   error_post_office = 'Post Office is required';
   $('#error_post_office').text(error_post_office);
   $('#post_office').addClass('has-error');
  }
  else
  {
   error_post_office = '';
   $('#error_post_office').text(error_post_office);
   $('#post_office').removeClass('has-error');
  }

  if($.trim($('#pin_code').val()).length == 0)
  {
   error_pin_code = 'Pin Code is required';
   $('#error_pin_code').text(error_pin_code);
   $('#pin_code').addClass('has-error');
  }
  else
  {

     if($.trim($('#pin_code').val()).length !=6)
    {
      error_pin_code = 'Pin Code must be 6 digit';
     $('#error_pin_code').text(error_pin_code);
     $('#pin_code').addClass('has-error');
    }
    else
    {
     error_pin_code = '';
     $('#error_pin_code').text(error_pin_code);
     $('#pin_code').removeClass('has-error');

    }
   
  }


   if($.trim($('#police_station').val()).length == 0)
  {
   error_police_station = 'Police Station is required';
   $('#error_police_station').text(error_police_station);
   $('#police_station').addClass('has-error');
  }
  else
  {
   error_police_station = '';
   $('#error_police_station').text(error_police_station);
   $('#police_station').removeClass('has-error');
  }


   if($.trim($('#residency_period').val()).length == 0)
  {
   error_residency_period = 'Number of years Dwelling in WB is required';
   $('#error_residency_period').text(error_residency_period);
   $('#residency_period').addClass('has-error');
  }
  else
  {

      if($.trim($('#residency_period').val()) >120 )
      {
       error_residency_period = 'Number of years is not properly';
       $('#error_residency_period').text(error_residency_period);
       $('#residency_period').addClass('has-error');
      }
      else
      {
       error_residency_period = '';
       $('#error_residency_period').text(error_residency_period);
       $('#residency_period').removeClass('has-error');
      }

   
  }


   if($.trim($('#mobile_no').val()).length == 0)
  {
   error_mobile_no = 'Mobile Number is required';
   $('#error_mobile_no').text(error_mobile_no);
   $('#mobile_no').addClass('has-error');
  }
  else
  {


    if($.trim($('#mobile_no').val()).length !=10)
    {
     error_mobile_no = 'Mobile Number must be 10 digit';
    $('#error_mobile_no').text(error_mobile_no);
    $('#mobile_no').addClass('has-error');
    }
    else
    {
     error_mobile_no = '';
    $('#error_mobile_no').text(error_mobile_no);
    $('#mobile_no').removeClass('has-error');

    }


  if($.trim($('#email').val()).length == 0)
  {
   error_email = '';
   $('#error_email').text(error_email);
   $('#email').removeClass('has-error');
  }
  else
  {

     if((/^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z.]{2,5}$/).exec($.trim($('#email').val()))== null)
     {
     error_email = 'Email Id is invalid';
     $('#error_email').text(error_email);
     $('#email').addClass('has-error');
     }
     else
     {
      error_email = '';
     $('#error_email').text(error_email);
     $('#email').removeClass('has-error');
     }

  }

  
  }
  

  if(error_district != '' || error_asmb_cons != '' || error_urban_code != '' || error_block != '' || error_gp_ward != '' || error_village != '' || error_post_office != '' || error_pin_code != '' || error_police_station != '' || error_residency_period != '' || error_mobile_no != ''  || error_email != '' )

  // if(error_asmb_cons != ''  )
  {
   return false;
  }
  else
  {
   
   $('#list_contact_details').removeClass('active active_tab1');
   $('#list_contact_details').removeAttr('href data-toggle');
   $('#contact_details').removeClass('active');
   $('#list_contact_details').addClass('inactive_tab1');
   $('#list_bank_details').removeClass('inactive_tab1');
   $('#list_bank_details').addClass('active_tab1 active');
   $('#list_bank_details').attr('href', '#bank_details');
   $('#list_bank_details').attr('data-toggle', 'tab');
   $('#bank_details').addClass('active in');

   
  }

 });


 
 $('#previous_btn_bank_details').click(function(){
  $('#list_bank_details').removeClass('active active_tab1');
  $('#list_bank_details').removeAttr('href data-toggle');
  $('#bank_details').removeClass('active in');
  $('#list_bank_details').addClass('inactive_tab1');
  $('#list_contact_details').removeClass('inactive_tab1');
  $('#list_contact_details').addClass('active_tab1 active');
  $('#list_contact_details').attr('href', '#contact_details');
  $('#list_contact_details').attr('data-toggle', 'tab');
  $('#contact_details').addClass('active in');
 });


 //--------------------------

  $('#btn_bank_details').click(function(){ 


  
  
  
 var error_name_of_bank =''; 

  var error_bank_branch =''; 
  var error_bank_account_number =''; 
  var error_bank_ifsc_code =''; 

 

  if($.trim($('#name_of_bank').val()).length == 0)
  {
   error_name_of_bank = 'Name of Bank is required';
   $('#error_name_of_bank').text(error_name_of_bank);
   $('#name_of_bank').addClass('has-error');
  }
  else
  {
   error_name_of_bank = '';
   $('#error_name_of_bank').text(error_name_of_bank);
   $('#name_of_bank').removeClass('has-error');
  }

   if($.trim($('#bank_branch').val()).length == 0)
  {
   error_bank_branch = 'Bank Branch is required';
   $('#error_bank_branch').text(error_bank_branch);
   $('#bank_branch').addClass('has-error');
  }
  else
  {
   error_bank_branch = '';
   $('#error_bank_branch').text(error_bank_branch);
   $('#bank_branch').removeClass('has-error');
  }

   if($.trim($('#bank_account_number').val()).length == 0)
  {
   error_bank_account_number = 'Bank Account Number is required';
   $('#error_bank_account_number').text(error_bank_account_number);
   $('#bank_account_number').addClass('has-error');
  }
  else
  {
   error_bank_account_number = '';
   $('#error_bank_account_number').text(error_bank_account_number);
   $('#bank_account_number').removeClass('has-error');
  }

  if($.trim($('#bank_ifsc_code').val()).length == 0)
  {
   error_bank_ifsc_code = 'IFS Code is required';
   $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
   $('#bank_ifsc_code').addClass('has-error');
  }
  else
  {
   error_bank_ifsc_code = '';
   $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
   $('#bank_ifsc_code').removeClass('has-error');
  }


  
  if(error_name_of_bank !='' || error_bank_branch !=''||  error_bank_account_number !='' || error_bank_ifsc_code !='')
  {
   return false;
  }
  else
  {
    
    $('#list_bank_details').removeClass('active active_tab1');
    $('#list_bank_details').removeAttr('href data-toggle');
    $('#bank_details').removeClass('active');
    $('#list_bank_details').addClass('inactive_tab1');
    $('#list_type_details').removeClass('inactive_tab1');
    $('#list_type_details').addClass('active_tab1 active');
    $('#list_type_details').attr('href', '#type_details');
    $('#list_type_details').attr('data-toggle', 'tab');
    $('#type_details').addClass('active in');
  }

 });

 

  $('#previous_btn_type_details').click(function(){
  $('#list_type_details').removeClass('active active_tab1');
  $('#list_type_details').removeAttr('href data-toggle');
  $('#type_details').removeClass('active in');
  $('#list_type_details').addClass('inactive_tab1');
  $('#list_bank_details').removeClass('inactive_tab1');
  $('#list_bank_details').addClass('active_tab1 active');
  $('#list_bank_details').attr('href', '#bank_details');
  $('#list_bank_details').attr('data-toggle', 'tab');
  $('#bank_details').addClass('active in');
 });

  $('#btn_type_details').click(function(){ 

  var error_type_disability ='';
  var error_percentage_disability =''; 
  var error_certifying_authority ='';
  
  

  if($('input[type=checkbox][class="type-disability"]:checked').length == 0)
  {

    
   
    error_type_disability = 'Select Type of Disability';
   $('#error_type_disability').text(error_type_disability);
   $('#').addClass('has-error');
  }
  else
  {
    
    error_type_disability = '';
   $('#error_type_disability').text(error_type_disability);
   $('#').removeClass('has-error');

  }

 

  if($.trim($('#percentage_disability').val()).length == 0)
  {
   error_percentage_disability = 'Percentage of Disability is required';
   $('#error_percentage_disability').text(error_percentage_disability);
   $('#percentage_disability').addClass('has-error');
  }
  else
  {


      if($.trim($('#percentage_disability').val()).length >3 )
      {
       error_percentage_disability = 'Please Enter propeer value';
       $('#error_percentage_disability').text(error_percentage_disability);
      $('#percentage_disability').addClass('has-error');
      }
      else
      {
       percentage_disability = '';
       $('#error_percentage_disability').text(error_percentage_disability);
       $('#percentage_disability').removeClass('has-error');
      }


   
  }

  if($.trim($('#certifying_authority').val()).length == 0)
  {
   error_certifying_authority = 'Certifying Authority is required';
   $('#error_certifying_authority').text(error_certifying_authority);
   $('#certifying_authority').addClass('has-error');
  }
  else
  {
   error_certifying_authority = '';
   $('#error_certifying_authority').text(error_certifying_authority);
   $('#certifying_authority').removeClass('has-error');
  }

  

  
  if(error_type_disability !="" || error_percentage_disability !='' || error_certifying_authority !="")
  {
   return false;
  }
  else
  {
    
    $('#list_type_details').removeClass('active active_tab1');
    $('#list_type_details').removeAttr('href data-toggle');
    $('#type_details').removeClass('active');
    $('#list_type_details').addClass('inactive_tab1');
    $('#list_experience_details').removeClass('inactive_tab1');
    $('#list_experience_details').addClass('active_tab1 active');
    $('#list_experience_details').attr('href', '#experience_details');
    $('#list_experience_details').attr('data-toggle', 'tab');
    $('#experience_details').addClass('active in');
  }

 });

 

 //  $('#previous_btn_type_details').click(function(){
 //  $('#list_type_details').removeClass('active active_tab1');
 //  $('#list_type_details').removeAttr('href data-toggle');
 //  $('#type_details').removeClass('active in');
 //  $('#list_type_details').addClass('inactive_tab1');
 //  $('#list_bank_details').removeClass('inactive_tab1');
 //  $('#list_bank_details').addClass('active_tab1 active');
 //  $('#list_bank_details').attr('href', '#bank_details');
 //  $('#list_bank_details').attr('data-toggle', 'tab');
 //  $('#bank_details').addClass('active in');
 // });

 //--------------------------



  $('#previous_btn_experience_details').click(function(){
  $('#list_experience_details').removeClass('active active_tab1');
  $('#list_experience_details').removeAttr('href data-toggle');
  $('#experience_details').removeClass('active in');
  $('#list_experience_details').addClass('inactive_tab1');
  $('#list_type_details').removeClass('inactive_tab1');
  $('#list_type_details').addClass('active_tab1 active');
  $('#list_type_details').attr('href', '#type_details');
  $('#list_type_details').attr('data-toggle', 'tab');
  $('#type_details').addClass('active in');
 });


  $('#btn_experience_details').click(function(){   
  
  
  var error_passport_image="";
  var error_signature_image="";
  var error_cast_certificate_file="";
  var error_disability_certificate_file="";
  var error_digital_ration_card_file="";

  var error_aadhar_card_file="";
  var error_voter_id_file="";
  var error_residential_certificate_file="";
  var error_income_certificate_file="";
  var error_bank_passbook_file="";
  var error_other_file="";

  var file_size = 2097152;
  
  var image_mime = ["image/jpg" , "image/jpeg", "image/png", "image/gif"];
  var image_pdf_mime = ["image/jpg" , "image/jpeg", "image/png", "image/gif", "application/pdf"];

  if($('#passport_image')[0].files.length === 0){  

    error_passport_image = 'Passport Photograph is required';
    $('#error_passport_image').text(error_passport_image);
    $('#passport_image').addClass('has-error');
  }
  else
  {
    var passport_image_size = $('#passport_image')[0].files[0].size;
    var passport_image_type = $('#passport_image')[0].files[0].type; 

    if(passport_image_size > file_size)
    {     
    error_passport_image = 'File size should be within limit';
    $('#error_passport_image').text(error_passport_image);
    $('#passport_image').addClass('has-error');
    return false;
    }
    else
    {     
    error_passport_image = '';
    $('#error_passport_image').text(error_passport_image);
    $('#passport_image').removeClass('has-error');
    }
    if(jQuery.inArray(passport_image_type, image_mime) != -1) 
    {      
    error_passport_image = '';
    $('#error_passport_image').text(error_passport_image);
    $('#passport_image').removeClass('has-error');
    } 
    else 
    {  
    error_passport_image = 'File type not supported';
    $('#error_passport_image').text(error_passport_image);
    $('#passport_image').addClass('has-error');
    }     
  }  

  if($('#signature_image')[0].files.length > 0)
  {   
    var signature_image_size = $('#signature_image')[0].files[0].size;
    var signature_image_type = $('#signature_image')[0].files[0].type; 

    if(signature_image_size > file_size)
    {          
    error_signature_image = 'File size should be within limit';
    $('#error_signature_image').text(error_signature_image);
    $('#signature_image').addClass('has-error');
    return false;
    }
    else
    {     
    error_signature_image = '';
    $('#error_signature_image').text(error_signature_image);
    $('#signature_image').removeClass('has-error');
    }
    if(jQuery.inArray(signature_image_type, image_pdf_mime) != -1) 
    {      
    error_signature_image = '';
    $('#error_signature_image').text(error_signature_image);
    $('#signature_image').removeClass('has-error');
    } 
    else 
    {        
    error_signature_image = 'File type not supported';
    $('#error_signature_image').text(error_signature_image);
    $('#signature_image').addClass('has-error');
    }     
  }  
  //-------------------------------------------------------------------------
  if($('#cast_certificate_file')[0].files.length > 0)
  {   
    var cast_certificate_file_size = $('#cast_certificate_file')[0].files[0].size;
    var cast_certificate_file_type = $('#cast_certificate_file')[0].files[0].type; 

    if(cast_certificate_file_size > file_size)
    {          
    error_cast_certificate_file = 'File size should be within limit';
    $('#error_cast_certificate_file').text(error_cast_certificate_file);
    $('#cast_certificate_file').addClass('has-error');
    return false;
    }
    else
    {     
    error_cast_certificate_file = '';
    $('#error_cast_certificate_file').text(error_cast_certificate_file);
    $('#error_cast_certificate_file').removeClass('has-error');
    }
    if(jQuery.inArray(cast_certificate_file_type, image_pdf_mime) != -1) 
    {      
    error_cast_certificate_file = '';
    $('#error_cast_certificate_file').text(error_cast_certificate_file);
    $('#cast_certificate_file').removeClass('has-error');
    } 
    else 
    {        
    error_cast_certificate_file = 'File type not supported';
    $('#error_cast_certificate_file').text(error_cast_certificate_file);
    $('#cast_certificate_file').addClass('has-error');
    }     
  } 
//-----------------------------------------------------------------------------------
  if($('#disability_certificate_file')[0].files.length > 0)
  {   
    var disability_certificate_file_size = $('#disability_certificate_file')[0].files[0].size;
    var disability_certificate_file_type = $('#disability_certificate_file')[0].files[0].type; 

    if(disability_certificate_file_size > file_size)
    {          
    error_disability_certificate_file = 'File size should be within limit';
    $('#error_disability_certificate_file').text(error_disability_certificate_file);
    $('#disability_certificate_file').addClass('has-error');
    return false;
    }
    else
    {     
    error_disability_certificate_file = '';
    $('#error_disability_certificate_file').text(error_disability_certificate_file);
    $('#disability_certificate_file').removeClass('has-error');
    }
    if(jQuery.inArray(disability_certificate_file_type, image_pdf_mime) != -1) 
    {      
    error_disability_certificate_file = '';
    $('#error_disability_certificate_file').text(error_disability_certificate_file);
    $('#disability_certificate_file').removeClass('has-error');
    } 
    else 
    {        
    error_disability_certificate_file = 'File type not supported';
    $('#error_disability_certificate_file').text(error_disability_certificate_file);
    $('#disability_certificate_file').addClass('has-error');
    }     
  } 
  //--------------------------------------------------------------------
  if($('#digital_ration_card_file')[0].files.length > 0)
  {   
    var digital_ration_card_file_size = $('#digital_ration_card_file')[0].files[0].size;
    var digital_ration_card_file_type = $('#digital_ration_card_file')[0].files[0].type; 

    if(digital_ration_card_file_size > file_size)
    {          
    error_digital_ration_card_file = 'File size should be within limit';
    $('#error_digital_ration_card_file').text(error_digital_ration_card_file);
    $('#digital_ration_card_file').addClass('has-error');
    return false;
    }
    else
    {     
    error_digital_ration_card_file = '';
    $('#error_digital_ration_card_file').text(error_digital_ration_card_file);
    $('#digital_ration_card_file').removeClass('has-error');
    }
    if(jQuery.inArray(digital_ration_card_file_type, image_pdf_mime) != -1) 
    {      
    error_digital_ration_card_file = '';
    $('#error_digital_ration_card_file').text(error_digital_ration_card_file);
    $('#digital_ration_card_file').removeClass('has-error');
    } 
    else 
    {        
    error_digital_ration_card_file = 'File type not supported';
    $('#error_digital_ration_card_file').text(error_digital_ration_card_file);
    $('#digital_ration_card_file').addClass('has-error');
    }     
  } 
  //-------------------------------------------------------------------------
  if($('#aadhar_card_file')[0].files.length > 0)
  {   
    var aadhar_card_file_size = $('#aadhar_card_file')[0].files[0].size;
    var aadhar_card_file_type = $('#aadhar_card_file')[0].files[0].type; 

    if(aadhar_card_file_size > file_size)
    {          
    error_aadhar_card_file = 'File size should be within limit';
    $('#error_aadhar_card_file').text(error_aadhar_card_file);
    $('#aadhar_card_file').addClass('has-error');
    return false;
    }
    else
    {     
    error_aadhar_card_file = '';
    $('#error_aadhar_card_file').text(error_aadhar_card_file);
    $('#aadhar_card_file').removeClass('has-error');
    }
    if(jQuery.inArray(aadhar_card_file_type, image_pdf_mime) != -1) 
    {      
    error_aadhar_card_file = '';
    $('#error_aadhar_card_file').text(error_aadhar_card_file);
    $('#aadhar_card_file').removeClass('has-error');
    } 
    else 
    {        
    error_aadhar_card_file = 'File type not supported';
    $('#error_aadhar_card_file').text(error_aadhar_card_file);
    $('#aadhar_card_file').addClass('has-error');
    }     
  } 
  //-------------------------------------------------------------------
  if($('#voter_id_file')[0].files.length > 0)
  {   
    var voter_id_file_size = $('#voter_id_file')[0].files[0].size;
    var voter_id_file_type = $('#voter_id_file')[0].files[0].type; 

    if(voter_id_file_size > file_size)
    {          
    error_voter_id_file = 'File size should be within limit';
    $('#error_voter_id_file').text(error_voter_id_file);
    $('#voter_id_file').addClass('has-error');
    return false;
    }
    else
    {     
    error_voter_id_file = '';
    $('#error_voter_id_file').text(error_voter_id_file);
    $('#voter_id_file').removeClass('has-error');
    }
    if(jQuery.inArray(voter_id_file_type, image_pdf_mime) != -1) 
    {      
    error_voter_id_file = '';
    $('#error_voter_id_file').text(error_voter_id_file);
    $('#voter_id_file').removeClass('has-error');
    } 
    else 
    {        
    error_voter_id_file = 'File type not supported';
    $('#error_voter_id_file').text(error_voter_id_file);
    $('#voter_id_file').addClass('has-error');
    }     
  } 

  //-------------------------------------------------------------

  if($('#residential_certificate_file')[0].files.length > 0)
  {   
    var residential_certificate_file_size = $('#residential_certificate_file')[0].files[0].size;
    var residential_certificate_file_type = $('#residential_certificate_file')[0].files[0].type; 

    if(residential_certificate_file_size > file_size)
    {          
    error_residential_certificate_file = 'File size should be within limit';
    $('#error_residential_certificate_file').text(error_residential_certificate_file);
    $('#residential_certificate_file').addClass('has-error');
    return false;
    }
    else
    {     
    error_residential_certificate_file = '';
    $('#error_residential_certificate_file').text(error_residential_certificate_file);
    $('#residential_certificate_file').removeClass('has-error');
    }
    if(jQuery.inArray(residential_certificate_file_type, image_pdf_mime) != -1) 
    {      
    error_residential_certificate_file = '';
    $('#error_residential_certificate_file').text(error_residential_certificate_file);
    $('#residential_certificate_file').removeClass('has-error');
    } 
    else 
    {        
    error_residential_certificate_file = 'File type not supported';
    $('#error_residential_certificate_file').text(error_residential_certificate_file);
    $('#residential_certificate_file').addClass('has-error');
    }     
  } 
//--------------------------------------------------------------------------------------
  if($('#income_certificate_file')[0].files.length > 0)
  {   
    var income_certificate_file_size = $('#income_certificate_file')[0].files[0].size;
    var income_certificate_file_type = $('#income_certificate_file')[0].files[0].type; 

    if(income_certificate_file_size > file_size)
    {          
    error_income_certificate_file = 'File size should be within limit';
    $('#error_income_certificate_file').text(error_income_certificate_file);
    $('#income_certificate_file').addClass('has-error');
    return false;
    }
    else
    {     
    error_income_certificate_file = '';
    $('#error_income_certificate_file').text(error_income_certificate_file);
    $('#income_certificate_file').removeClass('has-error');
    }
    if(jQuery.inArray(income_certificate_file_type, image_pdf_mime) != -1) 
    {      
    error_income_certificate_file = '';
    $('#error_income_certificate_file').text(error_income_certificate_file);
    $('#income_certificate_file').removeClass('has-error');
    } 
    else 
    {        
    error_income_certificate_file = 'File type not supported';
    $('#error_income_certificate_file').text(error_income_certificate_file);
    $('#income_certificate_file').addClass('has-error');
    }     
  } 
 //---------------------------------------------------------------------
  if($('#bank_passbook_file')[0].files.length > 0)
  {   
    var bank_passbook_file_size = $('#bank_passbook_file')[0].files[0].size;
    var bank_passbook_file_type = $('#bank_passbook_file')[0].files[0].type; 

    if(bank_passbook_file_size > file_size)
    {          
    error_bank_passbook_file = 'File size should be within limit';
    $('#error_bank_passbook_file').text(error_bank_passbook_file);
    $('#bank_passbook_file').addClass('has-error');
    return false;
    }
    else
    {     
    error_bank_passbook_file = '';
    $('#error_bank_passbook_file').text(error_bank_passbook_file);
    $('#bank_passbook_file').removeClass('has-error');
    }
    if(jQuery.inArray(bank_passbook_file_type, image_pdf_mime) != -1) 
    {      
    error_bank_passbook_file = '';
    $('#error_bank_passbook_file').text(error_bank_passbook_file);
    $('#bank_passbook_file').removeClass('has-error');
    } 
    else 
    {        
    error_bank_passbook_file = 'File type not supported';
    $('#error_bank_passbook_file').text(error_bank_passbook_file);
    $('#bank_passbook_file').addClass('has-error');
    }     
  } 

  if($('#other_file')[0].files.length > 0)
  {   
    var other_file_size = $('#other_file')[0].files[0].size;
    var other_file_type = $('#other_file')[0].files[0].type; 

    if(other_file_size > file_size)
    {          
    error_other_file = 'File size should be within limit';
    $('#error_other_file').text(error_other_file);
    $('#other_file').addClass('has-error');
    return false;
    }
    else
    {     
    error_other_file = '';
    $('#error_other_file').text(error_other_file);
    $('#other_file').removeClass('has-error');
    }
    if(jQuery.inArray(other_file_type, image_pdf_mime) != -1) 
    {      
    error_other_file = '';
    $('#error_other_file').text(error_other_file);
    $('#other_file').removeClass('has-error');
    } 
    else 
    {        
    error_other_file = 'File type not supported';
    $('#error_other_file').text(error_other_file);
    $('#other_file').addClass('has-error');
    }     
  } 

  
 if(error_passport_image !='' || error_signature_image !='' || error_cast_certificate_file !='' || error_disability_certificate_file !='' || error_digital_ration_card_file !='' || error_aadhar_card_file !='' || error_voter_id_file !='' || error_residential_certificate_file !='' || error_income_certificate_file !='' || error_bank_passbook_file !='' || error_other_file !='' )
  {
   return false;
  }
  else
  {
    
    $('#list_experience_details').removeClass('active active_tab1');
    $('#list_experience_details').removeAttr('href data-toggle');
    $('#experience_details').removeClass('active');
    $('#list_experience_details').addClass('inactive_tab1');


    $('#list_decl_details').removeClass('inactive_tab1');
    $('#list_decl_details').addClass('active_tab1 active');
    $('#list_decl_details').attr('href', '#decl_details');
    $('#list_decl_details').attr('data-toggle', 'tab');
    $('#decl_details').addClass('active in');
  }

 });

 

  $('#previous_btn_decl_details').click(function(){

  $('#list_decl_details').removeClass('active active_tab1');
  $('#list_decl_details').removeAttr('href data-toggle');
  $('#decl_details').removeClass('active in');
  $('#list_decl_details').addClass('inactive_tab1');


  $('#list_experience_details').removeClass('inactive_tab1');
  $('#list_experience_details').addClass('active_tab1 active');
  $('#list_experience_details').attr('href', '#experience_details');
  $('#list_experience_details').attr('data-toggle', 'tab');
  $('#experience_details').addClass('active in');
 });

 

/***************************SD*********************************/
$('#btn_submit_preview').click(function(){

  $(".modal-submit").show();
  $("#submitting").hide();
  $("#submit_loader").hide();
  
 
 // var error_nominate_name= ''; 
 // var error_nominate_address= ''; 
 // var error_nominate_relationship= ''; 

 //  if($.trim($('#nominate_name').val()).length == 0)
 //  {
 //   error_nominate_name = 'Name is required';
 //   $('#error_nominate_name').text(error_nominate_name);
 //   $('#nominate_name').addClass('has-error');
 //  }
 //  else
 //  {
 //   error_nominate_name = '';
 //   $('#error_nominate_name').text(error_nominate_name);
 //   $('#nominate_name').removeClass('has-error');
 //  } 

 //   if($.trim($('#nominate_address').val()).length == 0)
 //  {
 //   error_nominate_address = 'Address is required';
 //   $('#error_nominate_address').text(error_nominate_address);
 //   $('#nominate_address').addClass('has-error');
 //  }
 //  else
 //  {
 //   error_nominate_address = '';
 //   $('#error_nominate_address').text(error_nominate_address);
 //   $('#nominate_address').removeClass('has-error');
 //  } 

 //   if($.trim($('#nominate_relationship').val()).length == 0)
 //  {
 //   error_nominate_relationship = 'Relationship is required';
 //   $('#error_nominate_relationship').text(error_nominate_relationship);
 //   $('#nominate_relationship').addClass('has-error');
 //  }
 //  else
 //  {
 //   error_nominate_relationship = '';
 //   $('#error_nominate_relationship').text(error_nominate_relationship);
 //   $('#nominate_relationship').removeClass('has-error');
 //  } 

 // if(error_nominate_name != ''  || error_nominate_address != ''  ||  error_nominate_relationship != '')
 //  {
 //   return false;
 //  }
 //  else
 //  {
   
 //  $("#confirm-submit").modal("show");

 //  }

  $("#confirm-submit").modal("show");



});

$('#btn_submit_preview').click(function() { 

    
    $('#name_modal').text($('#first_name').val()+' '+$('#middle_name').val()+' '+$('#last_name').val());
    $('#gender_modal').text($('#gender').val());
    $('#dob_modal').text($('#dob').val());
    $('#father_name_modal').text($('#father_first_name').val()+' '+$('#father_middle_name').val()+' '+$('#father_last_name').val());
    $('#mother_name_modal').text($('#mother_first_name').val()+' '+$('#mother_middle_name').val()+' '+$('#mother_last_name').val());


    $('#caste_category_modal').text($('#caste_category').val());
    $('#marital_status_modal').text($('#marital_status').val());
    $('#spouse_name_modal').text($('#spouse_first_name').val()+' '+$('#spouse_middle_name').val()+' '+$('#spouse_last_name').val());
    $('#bpl_seq_no_modal').text($('#bpl_seq_no').val());
    $('#bpl_id_no_modal').text($('#bpl_id_no').val());
    $('#bpl_total_score_modal').text($('#bpl_total_score').val());
    $('#monthly_income_modal').text($('#monthly_income').val());

    $('#ration_card_no_modal').text($('#ration_card_cat').val()+'-'+$('#ration_card_no').val());

    //$('#ration_card_cat_modal').text($('#ration_card_cat').val());
    //$('#ration_card_no_modal').text($('#ration_card_no').val());
    $('#ahl_tin_modal').text($('#ahl_tin').val());
    $('#aadhar_no_modal').text($('#aadhar_no').val());
    $('#epic_voter_id_modal').text($('#epic_voter_id').val());
    $('#pan_no_modal').text($('#pan_no').val());


    $('#state_modal').text($('#state').val());
    $('#asmb_cons_modal').text($('#asmb_cons :selected').text());
    $('#district_modal').text($("#district :selected").text());
    $('#police_station_modal').text($('#police_station').val());
    $('#block_modal').text($("#block :selected").text());
    $('#gp_ward_modal').text($("#gp_ward :selected").text());
    $('#village_modal').text($('#village').val());
    $('#house_modal').text($('#house').val());
    $('#post_office_modal').text($('#post_office').val());
    $('#pin_code_modal').text($('#pin_code').val());
    $('#mobile_no_modal').text($('#mobile_no').val());
    $('#email_modal').text($('#email').val());
    $('#bank_account_number_modal').text($('#bank_account_number').val());
    $('#name_of_bank_modal').text($('#name_of_bank').val());
    $('#bank_branch_modal').text($('#bank_branch').val());
    $('#bank_ifsc_code_modal').text($('#bank_ifsc_code').val());

    

    $('#percentage_disability_modal').text($('#percentage_disability').val());
    $('#certifying_authority_modal').text($('#certifying_authority').val());


    $('#nominate_name_modal').text($('#nominate_name').val());
    $('#nominate_address_modal').text($('#nominate_address').val());
    $('#nominate_relationship_modal').text($('#nominate_relationship').val());


    $('#org_val_modal').text($('#org_val').val());
    $('#text_1_modal').text($('#text_1').val());
    $('#text_2_modal').text($('#text_2').val());



    $('#residency_period_modal').text($('#residency_period').val());

    


$('.modal-submit').on('click',function(){
//$(".modal-submit").attr("disabled", true);
$(".modal-submit").hide();
$("#submitting").show();
$("#submit_loader").show();
//$("#register_form").submit();
});
 

    
});
/***************************************************************/
});
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





