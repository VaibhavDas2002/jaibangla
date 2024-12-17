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
  <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}">

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
              <h3 class="box-title">Employee Details</h3>
            </div>

            <div>
             @if ( ($message = Session::get('success')) && ($id =Session::get('id')))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }} with Application ID: {{$id}}</strong>
                <form method="POST" action="{{ route('nhmemployee.printSingleEmployee', ['id' => $id]) }}">
                       
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      
                        <button type="submit" class="btn btn-danger col-md-2 btn-lg" style="float: right; margin-top:-33px; margin-right:15px;">
                          Print
                        </button>
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
            <form method="post" id="register_form" action="{{url('nhmemployee')}}">
              {{ csrf_field() }}
            <ul class="nav nav-tabs">
             <li class="nav-item">
              <a class="nav-link active_tab1" style="border:1px solid #ccc" id="list_login_details">Personal Details</a>
             </li>
             <li class="nav-item">
              <a class="nav-link inactive_tab1" id="list_personal_details" style="border:1px solid #ccc">Qualifications</a>
             </li>
             <li class="nav-item">
              <a class="nav-link inactive_tab1" id="list_contact_details" style="border:1px solid #ccc">Salary A/C Details</a>
             </li>
             <li class="nav-item">
              <a class="nav-link inactive_tab1" id="list_experience_details" style="border:1px solid #ccc">Experience</a>
             </li>
            </ul>
            <div class="tab-content" style="margin-top:16px;">
             <div class="tab-pane active" id="login_details">
              <div class="panel panel-default">
               <div class="panel-heading">Personal Details</div>
               <div class="panel-body">
                <div class="form-group col-md-3">
                 <label class="">Title</label>
                 <select class="form-control select2" name="title" id="title" >
                    <option value="">--Select--</option>
                    <option value="Mr">Mr</option>
                    <option value="Mrs">Mrs</option>
                    <option value="Ms">Ms</option>                   
                    <option value="Dr.">Dr.</option>                   
                                 
                   <!--  <option value="Syed">Syed</option>  -->                  
                  </select>
                 <span id="error_title" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="required-field">First Name</label>
                 <input type="text" name="first_name" id="first_name" class="form-control txtOnly" placeholder="First Name" />
                 <span id="error_first_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Middle Name</label>
                 <input type="text" name="middle_name" id="middle_name" class="form-control txtOnly"  placeholder="Middle Name" />
                 <span id="error_middle_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="">Last Name</label>
                 <input type="text" name="last_name" id="last_name" class="form-control txtOnly" placeholder="Last Name" />
                 <span id="error_last_name" class="text-danger"></span>
                </div>

                <div class="form-group col-md-3">
                 <label class="required-field">Relation</label>
                 <select class="form-control select2" name="guardian_relation"  id="guardian_relation">
                    <option value="">--Select--</option>
                    <option value="Father">Father</option>
                    <option value="Mother">Mother</option>
                    <option value="Spouse">Spouse</option>                                       
                  </select>
                 <span id="error_guardian_relation" class="text-danger"></span>
                </div>
                <div class="form-group col-md-9">
                 <label class="required-field">Name of Father/Mother/Spouse</label>
                 <input type="text" name="guardian_name" id="guardian_name" class="form-control txtOnly" placeholder="Name of Father/Mother/Spouse" />
                 <span id="error_guardian_name" class="text-danger"></span>
                </div>

                <div class="form-group col-md-3">
                 <label class="required-field">Date of Birth</label>
                 <input type="date" name="dob" id="dob" class="form-control"/>
                 <!-- <input type="text" id="dob" name="dob"class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask placeholder="dd/mm/yyyy"> -->
                 <span id="error_dob" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="required-field">Gender</label>
                 <select class="form-control select2" name="gender" id="gender">
                    <option value="">--Select--</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Others">Others</option>                                       
                  </select>
                 <span id="error_gender" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="required-field">Caste Category</label>
                 <select class="form-control select2" name="caste_category" id="caste_category" >
                    <option value="">--Select--</option>
                    <option value="General">General</option>
                    <option value="SC">SC</option>
                    <option value="ST">ST</option>
                    <option value="OBC A">OBC A</option>
                    <option value="OBC B">OBC B</option>                                        
                  </select>
                 <span id="error_caste_category" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="required-field">Wheather engagged under PWD</label>
                 <select class="form-control select2" name="pwd" id="pwd">
                    <option value="">--Select--</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                  </select>
                 <span id="error_pwd" class="text-danger"></span>
                </div>

                <div class="form-group col-md-3">
                 <label class="required-field">Marital Status</label>
                 <select class="form-control select2" name="marital_status" id="marital_status" >
                    <option value="">--Select--</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                     <option value="Others">Others</option>
                  </select>
                 <span id="error_marital_status" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="required-field">Mobile Number 1</label>
                 <input type="number" name="mobile_number_1"id="mobile_number_1" class="form-control" placeholder="Mobile Number 1" value="">
                 <span id="error_mobile_number_1" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Mobile Number 2</label>
                 <input type="number" id="mobile_number_2" name="mobile_number_2"class="form-control" placeholder="Mobile Number 2">
                 <span id="error_mobile_number_2" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="required-field">Email ID</label>
                 <input type="email" id="email" name="email"class="form-control" placeholder="Email Address">
                 <span id="error_email" class="text-danger"></span>
                </div>

                <div class="form-group col-md-3">
                 <label>Identification Mark</label>
                 <input type="text" id="identification_mark" name="identification_mark" class="form-control txtOnly" placeholder="Identification Mark">
                 <span id="error_identification_mark" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="required-field">Blood Group</label>
                 <select class="form-control select2" name="blood_group" id="blood_group" >
                    <option value="">--Select--</option>
                    <option value="A +ve">A +ve</option>
                    <option value="A -ve">A -ve</option>
                    <option value="B +ve">B +ve</option>
                    <option value="B -ve">B -ve</option>
                    <option value="O +ve">O +ve</option>
                    <option value="O -ve">O -ve</option>
                    <option value="AB +ve">AB +ve</option>
                    <option value="AB -ve">AB -ve</option>
                    <option value="Bombay Group">Bombay Group</option>
                  </select>
                 <span id="error_blood_group" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="required-field">Emergency Contact Person</label>
                 <input type="text" id="person_name_emergency" name="person_name_emergency" class="form-control txtOnly" placeholder="Name">
                 <span id="error_person_name_emergency" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="required-field">Emergency Contact Mobile No</label>
                 <input type="number" id="person_emergency_mobile" name="person_emergency_mobile" class="form-control" placeholder="Mobile No">
                 <span id="error_person_emergency_mobile" class="text-danger"></span>
                </div>

                <div class="form-group col-md-3">
                 <label class="required-field">Present Address Line 1</label>
                 <input type="text" id="present_address_line1" name="present_address_line1" class="form-control" placeholder="Present Address Line 1">
                 <span id="error_present_address_line1" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="required-field">District</label>
                 <select class="form-control select2" name="present_address_district" id="present_address_district">
                    <option value="">---------Select Option---------</option>
                   <!--  <option value='Service Delivery'>Service Delivery</option>
                    <option value='Programme Management'>Programme Management</option> -->
                      @foreach ($districts as $district)
                                <option value="{{$district->district_code}}">{{$district->district_name}}</option>
                      @endforeach                                                      
                  </select>
                 <span id="error_present_address_district" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="required-field">Police Station</label>
                 <input type="text" id="present_address_police_station" name="present_address_police_station"class="form-control" placeholder="Police Station">
                 <span id="error_present_address_police_station" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="required-field">Pincode</label>
                 <input type="number" id="present_address_pincode" name="present_address_pincode" class="form-control" placeholder="Pincode">
                 <span id="error_present_address_pincode" class="text-danger"></span>
                </div>
                 <div class="form-group col-md-12" id="other_district_present_address_div">
                 <label class="required-field">If District is Outside WB,Specify District and State</label>
                 <input type="text" id="other_district_present_address" name="other_district_present_address" class="form-control" placeholder="">
                 <span id="error_other_district_present_address" class="text-danger"></span>
                </div>

                <div class="form-group col-md-12">
                 <label>Same as Present Address</label>
                 <!-- <input type="text" id="present_address_pincode" name="present_address_pincode" class="form-control" placeholder="Pincode"> -->
                 <input type="checkbox" name="same_as_present_address" id="same_as_present_address"><br>
                 <span id="error_same_as_present_address" class="text-danger"></span>
                </div>

                <div class="form-group col-md-3">
                 <label class="required-field">Permanent Address Line 1</label>
                 <input type="text" id="permanent_address_line1" name="permanent_address_line1" class="form-control" placeholder="Permanent Address Line 1">
                 <span id="error_permanent_address_line1" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="required-field">District</label>
                  <select class="form-control select2" name="permanent_address_district" id="permanent_address_district">
                    <option value="">---------Select Option---------</option>
                   <!--  <option value='Service Delivery'>Service Delivery</option>
                    <option value='Programme Management'>Programme Management</option> -->
                      @foreach ($districts as $district)
                                <option value="{{$district->district_code}}">{{$district->district_name}}</option>
                      @endforeach                                                      
                  </select>
                
                 <span id="error_permanent_address_district" class="text-danger"></span>
                </div>
              
                <div class="form-group col-md-3">
                 <label class="required-field">Police Station</label>
                 <input type="text" id="permanent_address_police_station" name="permanent_address_police_station"class="form-control" placeholder="Police Station">
                 <span id="error_permanent_address_police_station" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label class="required-field">Pincode</label>
                 <input type="number" id="permanent_address_pincode" name="permanent_address_pincode" class="form-control" placeholder="Pincode">
                 <span id="error_permanent_address_pincode" class="text-danger"></span>
                </div>
                 <div class="form-group col-md-12" id="other_district_permanent_address_div">
                 <label class="required-field">If District is Outside WB,Specify District and State</label>
                 <input type="text" id="other_district_permanent_address" name="other_district_permanent_address" class="form-control" placeholder="">
                 <span id="error_other_district_permanent_address" class="text-danger"></span>
                </div>
                <br />
                <div class="col-md-12" align="center">
                 <button type="button" name="btn_login_details" id="btn_login_details" class="btn btn-info btn-lg">Next</button>
                 <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                </div>
                <br />
               </div>
              </div>
             </div>
             <div class="tab-pane fade" id="personal_details">
              <div class="panel panel-default">
               <div class="panel-heading">Qualifications</div>
               <div class="panel-body">
                              
                <div class="form-group col-md-12">
                 <label class="required-field">Highest Educational Qualification</label>
                 <select class="form-control select2" name="highest_education" id="highest_education" style="width:100%!important;">
                    <option value="">--Select--</option>
                    <option value="Class VIII pass">Class VIII Pass</option>
                    <option value="Below Class X">Below Class X</option>
                    <option value="Class X pass">Class X Pass</option>
                    <option value="Class XII pass">Class XII Pass</option>
                    <option value="Graduation">Graduation</option>
                    <option value="Post Graduation">Post Graduation</option>
                     <option value="Above Post Graduation">Above Post Graduation</option>
                  </select>
                 <span id="error_highest_education" class="text-danger"></span>
                </div>

                <div class="form-group col-md-12">
                 <label>Technical Qualification</label>
                 <input type="text" name="technical_qualification" id="technical_qualification" class="form-control" placeholder="Technical Qualification" />
                 <span id="error_technical_qualification" class="text-danger"></span>
                </div>

                <div class="form-group col-md-12">
                 <label>Professional Qualification</label>
                <select class="form-control select2" name="professional_qualification" id="professional_qualification">
                    <option value="">--Select--</option>
                    <option value="MBBS">MBBS</option>
                    <option value="MD">MD</option>
                    <option value="MD In Homeopathy">MD In Homeopathy</option>
                    <option value="MD In Ayurveda">MD In Ayurveda</option>
                    <option value="BDS">BDS</option>
                    <option value="BHMS">BHMS</option>
                    <option value="BUMS">BUMS</option>
                    <option value="BAMS">BAMS</option>
                    <option value="GNM">GNM</option>
                    <option value="ANM(R)">ANM(R)</option>
                    <option value="B.Sc Nursing">B.Sc Nursing</option>
                    <option value="M.Sc Nursing">M.Sc Nursing</option>
                    <option value="D.Pharm">D.Pharm</option>
                    <option value="B.Pharm">B.Pharm</option>
                    <option value="M.Pharm">M.Pharm</option>
                    <option value="Others">Others</option>

                  </select>
                 <span id="error_professional_qualification" class="text-danger"></span>
                </div>

                <div class="form-group col-md-12" id="prof_other">
                 <label>If Other, Please Specify</label>
                 <input type="text" name="other_professional_qualification" id="other_professional_qualification" class="form-control" placeholder="Please Specify Other Qualification" />
                 <span id="error_other_professional_qualification" class="text-danger"></span>
                </div>

                <div class="form-group col-md-12">
                 <label id="registration_label" class="sample">If Professional qualification is MBBS/BDS/BHMS/BUMS/BAMS/Nursing Staff/Pharmacist, then Registration of respective council</label>
                 
                 <input type="text" name="registration" id="registration" class="form-control" placeholder="Registration Number" />
                 <span id="error_registration" class="text-danger"></span>
                </div>
                <!--
                <div class="form-group">
                 <label>Gender</label>
                 <label class="radio-inline">
                  <input type="radio" name="gender" value="male" checked> Male
                 </label>
                 <label class="radio-inline">
                  <input type="radio" name="gender" value="female"> Female
                 </label>
                </div>
                -->
             
                <br />  
                <div align="center">
                 <button type="button" name="previous_btn_personal_details" id="previous_btn_personal_details" class="btn btn-default btn-lg">Previous</button>
                 <button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>
                </div>
                <br />
               </div>
              </div>
             </div>
             <div class="tab-pane fade" id="contact_details">
              <div class="panel panel-default">
               <div class="panel-heading">Salary A/C Details</div>
               <div class="panel-body">
                <div class="form-group">
                 <label class="required-field">PAN</label>
                 <input type="text" name="pan" id="pan" class="form-control" />
                 <span id="error_pan" class="text-danger"></span>
                </div>
                <div class="form-group">
                 <label class="required-field">Bank Account Number (Salary Account)</label>
                 <input type="text" name="bank_account_number" id="bank_account_number" class="form-control NumOnly" />
                 <span id="error_bank_account_number" class="text-danger"></span>
                </div>
                <div class="form-group">
                 <label class="required-field">Name Of the Bank</label>
                 <select class="form-control select2" name="name_of_bank" id="name_of_bank">
                    <option value="">---------Select Bank---------</option>
                    <option value='Allahabad Bank'>Allahabad Bank</option>
                    <option value='Andhra Bank'>Andhra Bank</option>
                    <option value='Axis Bank Ltd'>Axis Bank Ltd</option>
                    <option value='Bank of Bahrain and Kuwait'>Bank of Bahrain and Kuwait</option>
                    <option value='Bandhan Bank'>Bandhan Bank</option>
                    <option value='Bank of Baroda - Corporate Banking'>Bank of Baroda - Corporate Banking</option>
                    <option value='Bank of Baroda - Retail Banking'>Bank of Baroda - Retail Banking</option>
                    <option value='Bank of India'>Bank of India</option>
                    <option value='Bank of Maharashtra'>Bank of Maharashtra</option>
                    <option value='Bangiya Gramin Vikash Bank'>Bangiya Gramin Vikash Bank</option>
                    <option value='Bhatpara Naihati Co-operative Bank'>Bhatpara Naihati Co-operative Bank</option>
                    <option value='Canara Bank'>Canara Bank</option>
                    <option value='Central Bank of India'>Central Bank of India</option>
                    <option value='City Union Bank'>City Union Bank</option>
                    <option value='Corporation Bank'>Corporation Bank</option>
                    <option value='Dena Bank'>Dena Bank</option>
                    <option value='Deutsche Bank'>Deutsche Bank</option>
                    <option value='Development Credit Bank'>Development Credit Bank</option>
                    <option value='Dhanlaxmi Bank'>Dhanlaxmi Bank</option>
                    <option value='Federal Bank'>Federal Bank</option>
                    <option value='HDFC Bank'>HDFC Bank</option>
                    <option value='ICICI Bank'>ICICI Bank</option>
                    <option value='IDBI Bank'>IDBI Bank</option> 
                    <option value='Indian Bank'>Indian Bank</option>
                    <option value='Indian Overseas Bank'>Indian Overseas Bank</option>
                    <option value='IndusInd Bank'>IndusInd Bank</option>
                    <option value='ING Vysya Bank'>ING Vysya Bank</option>
                    <option value='Jammu and Kashmir Bank'>Jammu and Kashmir Bank</option>
                    <option value='Karnataka Bank Ltd'>Karnataka Bank Ltd</option>
                    <option value='Karur Vysya Bank'>Karur Vysya Bank</option>
                    <option value='Kotak Bank'>Kotak Bank</option>
                    <option value='Laxmi Vilas Bank'>Laxmi Vilas Bank</option>
                    <option value='Malda District Central Co-operative Bank Ltd'>Malda District Central Co-operative Bank Ltd</option>
                    <option value='Murshidabad District Central Cooperative Bank Limited'>Murshidabad District Central Cooperative Bank Limited</option>
                    <option value='Oriental Bank of Commerce'>Oriental Bank of Commerce</option>
					<option value='Paschim Banga Gramin Bank'>Paschim Banga Gramin Bank</option>
                    <option value='Punjab National Bank - Corporate Banking'>Punjab National Bank - Corporate Banking</option>
                    <option value='Punjab National Bank - Retail Banking'>Punjab National Bank - Retail Banking</option>
                    <option value='Punjab & Sind Bank'>Punjab & Sind Bank</option>
					<option value='Raiganj Central Co-operative Bank'>Raiganj Central Co-operative Bank</option>
                    <option value='Shamrao Vitthal Co-operative Bank'>Shamrao Vitthal Co-operative Bank</option>
                    <option value='South Indian Bank'>South Indian Bank</option>
                    <option value='State Bank of Bikaner & Jaipur'>State Bank of Bikaner & Jaipur</option>
                    <option value='State Bank of Hyderabad'>State Bank of Hyderabad</option>
                    <option value='State Bank of India'>State Bank of India</option>
                    <option value='State Bank of Mysore'>State Bank of Mysore</option>
                    <option value='State Bank of Patiala'>State Bank of Patiala</option>
                    <option value='State Bank of Travancore'>State Bank of Travancore</option>
                    <option value='Syndicate Bank'>Syndicate Bank</option>
                    <option value='Tamilnad Mercantile Bank Ltd.'>Tamilnad Mercantile Bank Ltd.</option>
                    <option value='Tamluk Ghatal Central Cooperative Bank'>Tamluk Ghatal Central Cooperative Bank</option>
					 <option value='The Dakshin Dinajpur District Central Co-operative Bank Ltd.'>The Dakshin Dinajpur District Central Co-operative Bank Ltd.</option>
           <option value='The West Bengal State Co-operative Bank Ltd.'>The West Bengal State Co-operative Bank Ltd.</option>
                    <option value='UCO Bank'>UCO Bank</option>
                    <option value='Union Bank of India'>Union Bank of India</option>
                    <option value='United Bank of India'>United Bank of India</option>
                    <option value='Uttarbanga Kshetriya Gramin Bank'>Uttarbanga Kshetriya Gramin Bank</option>
                    <option value='Vijaya Bank'>Vijaya Bank</option>
                    <option value='Yes Bank Ltd'>Yes Bank Ltd</option>
                    <option value='Standard Chartered'>Standard Chartered</option>
                  </select>
                 <span id="error_name_of_bank" class="text-danger"></span>
                </div>
                <div class="form-group">
                 <label class="required-field">Name of Bank Branch</label>
                 <input type="text" name="bank_branch" id="bank_branch" class="form-control" />
                 <span id="error_bank_branch" class="text-danger"></span>
                </div>
                <div class="form-group">
                 <label class="required-field">IFSC Code of Salary Account</label>
                 <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control" />
                 <span id="error_bank_ifsc_code" class="text-danger"></span>
                </div>
                <div class="form-group">
                 <label class="required-field">Whether UAN is applicable?</label>
                 <select class="form-control select2" name="is_uan_present" id="is_uan_present">
                    <option value="">---------Select Option---------</option>
                    <option value='Yes'>Yes</option>
                    <option value='No'>No</option>
                  </select>
                 <span id="error_is_uan_present" class="text-danger"></span>
                </div>
                 <div class="form-group" id="uin_number_div">
                 <label class="">UAN Number</label>
                 <input type="text" name="uin_number" id="uin_number" class="form-control NumOnly" />
                 <span id="error_uin_number" class="text-danger"></span>
                </div>
                <br />

                <div align="center">
                 <button type="button" name="previous_btn_contact_details" id="previous_btn_contact_details" class="btn btn-default btn-lg">Previous</button>
                 <button type="button" name="btn_contact_details" id="btn_contact_details" class="btn btn-info btn-lg">Next</button>
                </div>
                <br />
               </div>
              </div>
             </div>

            <div class="tab-pane fade" id="experience_details">
              <div class="panel panel-default">
               <div class="panel-heading">Experience</div>
               <div class="panel-body">

                <div class="form-group">
                 <label class="required-field">If engaged previously under NHM / NUHM at any level  (If it is different from present post)</label>
                 <select class="form-control select2" name="engaged_or_not_nhm" id="engaged_or_not_nhm">
                    <option value="">---------Select Option---------</option>
                    <option value='1'>Yes</option>
                    <option value='0'>No</option>                                                        
                  </select>
                 <span id="error_engaged_or_not_nhm" class="text-danger"></span>
                </div>
                

                <div class="form-group col-md-4" id="designation_nhm">
                 <label class="required-field">Designation</label>
                 <input type="text" name="e_designation_nhm" id="e_designation_nhm" class="form-control" />
                 <span id="error_e_designation_nhm" class="text-danger"></span>                 
                </div>                
                
                 <div class="form-inline col-md-6" id="duration_nhm">
                 <!-- <label>Duration</label><br> -->
                    <div class="form-group col-md-6">
                        <label class="required-field">Duration From</label>
                        <input type="date" name="e_duration_from_nhm" id="e_duration_from_nhm" class="form-control" placeholder="From Date" />
                     </div>
                    <div class=" form-group col-md-6">
                        <label class="required-field">Duration To</label>
                        <input type="date" name="e_duration_to_Nhm" id="e_duration_to_nhm" class="form-control" placeholder="To Date" onchange="calculateExperienceNHM()" />
                    </div>
                 <span id="error_e_duration_nhm" class="text-danger"></span>                 
                </div>

                 <div class="form-group col-md-2 experience_year_month_nhm_outer">
                 <label>Experience(in Year & Month)</label>
                 <input type="text" name="experience_year_month_nhm" id="experience_year_month_nhm" class="form-control" />
                 <span id="error_experience_year_month_nhm" class="text-danger"></span>                 
                </div>


                 <div class="form-group col-md-6" id="remuneration_nhm">
                 <label class="required-field">Last Monthly Remuneration Drawn</label>
                 <input type="number" name="e_remuneration_nhm" id="e_remuneration_nhm" class="form-control" />
                 <span id="error_e_remuneration_nhm" class="text-danger"></span>                 
                </div>

                 <div class="form-group col-md-6" id="remarks_nhm">
                 <label class="required-field">Remarks</label>
                 <input type="text" name="e_remarks_nhm" id="e_remarks_nhm" class="form-control" />
                 <span id="error_e_remarks_nhm" class="text-danger"></span>                 
                </div>
                <br>

                <div class="form-group">
                 <label class="required-field">If engaged in any project/programme/scheme under H & FW or any other Department of Government of West Bengal previously</label>
                 <select class="form-control select2" name="engaged_or_not_hfw" id="engaged_or_not_hfw">
                    <option value="">---------Select Option---------</option>
                    <option value='1'>Yes</option>
                    <option value='0'>No</option>                                                        
                  </select>
                 <span id="error_engaged_or_not_hfw" class="text-danger"></span>
                </div>

                <div class="form-group col-md-4" id="designation_hfw">
                 <label class="required-field">Designation</label>
                 <input type="text" name="e_designation_hfw" id="e_designation_hfw" class="form-control" />
                 <span id="error_e_designation_hfw" class="text-danger"></span>                 
                </div>  

                <!-- <div class="form-inline col-md-5" id="duration_hfw">
                 <label>Duration</label><br>
                    <div class="form-group ">
                        <input type="date" name="e_duration_from_hfw" id="e_duration_from_hfw" class="form-control" placeholder="From Date" />
                     </div>
                    <div class=" form-group ">
                        <input type="date" name="e_duration_to_hfw" id="e_duration_to_hfw" class="form-control" placeholder="To Date" />
                    </div>
                 <span id="error_duration_hfw" class="text-danger"></span>                 
                </div> -->

                 <div class="form-inline col-md-6" id="duration_hfw">
                 <!-- <label>Duration</label><br> -->
                    <div class="form-group col-md-6">
                        <label class="required-field">Duration From</label>
                        <input type="date" name="e_duration_from_hfw" id="e_duration_from_hfw" class="form-control" placeholder="From Date" />
                     </div>
                    <div class=" form-group col-md-6">
                        <label class="required-field">Duration To</label>
                        <input type="date" name="e_duration_to_hfw" id="e_duration_to_hfw" class="form-control" placeholder="To Date" onchange="calculateExperienceHFW()"/>
                    </div>
                 <span id="error_e_duration_hfw" class="text-danger"></span>                 
                </div>

                 <div class="form-group col-md-2 experience_year_month_hfw_outer">
                 <label>Experience(in Year & Month)</label>
                 <input type="text" name="experience_year_month_hfw" id="experience_year_month_hfw" class="form-control"  />
                 <span id="error_experience_year_month_hfw" class="text-danger"></span>                 
                </div>

                <div class="form-group col-md-6" id="remuneration_hfw">
                 <label class="required-field">Last Monthly Remuneration Drawn</label>
                 <input type="number" name="e_remuneration_hfw" id="e_remuneration_hfw" class="form-control" />
                 <span id="error_e_remuneration_hfw" class="text-danger"></span>                 
                </div>
                <br>

                 <div class="form-group col-md-6" id="remarks_hfw">
                 <label class="required-field">Remarks</label>
                 <input type="text" name="e_remarks_hfw" id="e_remarks_hfw" class="form-control" />
                 <span id="error_e_remarks_hfw" class="text-danger"></span>                 
                </div>
                <br>

                <div class="form-group">
                 <label style="font-size: medium;">Details of Engagement in present post under NHM/NUHM</label>                            
                </div>

                 <div class="form-group">
                 <label>Advertisement Number</label>
                 <input type="text" name="advertisement_number" id="advertisement_number" class="form-control" />
                 <span id="error_advertisement_number" class="text-danger"></span>                 
                </div>

                 <div class="form-group col-md-4">
                 <label class="required-field">Appointing Authority</label>
                  <select class="form-control select2" name="appointing_authority" id="appointing_authority">
                    <option value="">---------Select Option---------</option>
                    <option value='WBSH&FWS'>WBSH&FWS</option>
                    <option value='DH & FWS'>DH & FWS</option>
                    <option value='BH & FWS'>BH & FWS</option>
                    <option value='KMC'>KMC</option>
                    <option value='ULBs'>ULBs</option>
                                                                           
                  </select>
                  <span id="error_appointing_authority" class="text-danger"></span>                 
                 </div>

                 <div class="form-group col-md-4" >
                 <label class="required-field">Contractual Employement Under</label>
                  <select class="form-control select2" name="contractual_employment_under" id="contractual_employment_under">
                    <option value="">---------Select Option---------</option>
                    <option value='NHM'>NHM</option>
                    <option value='NUHM'>NUHM</option>
                                                                         
                  </select>
                  <span id="error_contractual_employment_under" class="text-danger"></span>                 
                 </div>

                 <div class="form-group col-md-4" >
                 <label class="required-field">Service Category</label>
                  <select class="form-control select2 js-service_category" name="service_category" id="service_category">
                    <option value="">---------Select Option---------</option>
                   <!--  <option value='Service Delivery'>Service Delivery</option>
                    <option value='Programme Management'>Programme Management</option> -->
                      @foreach ($service_categorys as $service_category)
                                <option value="{{$service_category->id}}">{{$service_category->name}}</option>
                      @endforeach                                                      
                  </select>
                  <span id="error_service_category" class="text-danger"></span>                 
                 </div>

                 <div class="form-group col-md-4" >
                 <label class="required-field">Major Programme Head</label>
                  <select class="form-control select2 js-major_programme_head " name="contractual_under_nhm" id="contractual_under_nhm">
                    <option value="">---------Select Option---------</option>
                   <!--  <option value='2'>RMNCH+A</option>
                    <option value='1'>HSS</option>
                    <option value='3'>CD</option>
                    <option value='4'>NCD</option>
                    <option value='5'>NUHM</option> -->
                      <!-- @foreach ($major_programme_heads as $major_programme_head)
                                <option value="{{$major_programme_head->id}}">{{$major_programme_head->name}}</option>
                      @endforeach  --> 
                                                                                   
                  </select>

                  <span id="error_contractual_under_nhm" class="text-danger"></span>                 
                 </div>

                <div class="form-group col-md-4 " >
                 <label class="required-field">Programme Head </label>
                  <select class="form-control select2 js-programme_head" name="programme_head" id="programme_head">
                    <option value="">---------Select Option---------</option>
                   <!--  <option value='2'>Maternal Health</option>
                    <option value='3'>Child Health</option>
                    <option value='4'>Routine Immunization</option>
                    <option value='RBSK'>RBSK</option>
                    <option value='RKSK'>RKSK</option>
                    <option value='Family Planning'>Family Planning</option>
                    <option value='Skill Lab'>Skill Lab</option>
                    <option value='PCPNDT'>PCPNDT</option>
                    <option value='FRU'>FRU</option>
                    <option value='Quality Assurance'>Quality Assurance</option>
                    <option value='Blood Services'>Blood Services</option>
                    <option value='1'>ANM</option>
                    <option value='NVBDCP'>NVBDCP</option>
                    <option value='IDSP'>IDSP</option>
                    <option value='RNTCP'>RNTCP</option>
                    <option value='NLEP'>NLEP</option>
                    <option value='NPCDCS'>NPCDCS</option>
                    <option value='NTCP'>NTCP</option>
                    <option value='NMHP'>NMHP</option>
                    <option value='NPPCD'>NPPCD</option>
                    <option value='NPPCF'>NPPCF</option>
                    <option value='NPHCE'>NPHCE</option>
                    <option value='NOHP'>NOHP</option>
                    <option value='NPCB & YI'>NPCB & YI</option>
                    <option value='HCP'>HCP</option>
                    <option value='NPPC'>NPPC</option>
                    <option value='NIDDCP'>NIDDCP</option> -->
                                              
                  </select>


                  <span id="error_programme_head" class="text-danger"></span>                 
                 </div>

                <div class="form-group col-md-4" >
                 <label class="required-field">Designation List </label>
                  <select class="form-control select2 js-designation_list" name="designation_list" id="designation_list">
                    <option value="">---------Select Option---------</option>
                    <!-- <option value='Maternal Health'>Maternal Health</option>
                    <option value='Child Health'>Child Health</option>
                    <option value='Routine Immunization'>Routine Immunization</option>
                    <option value='RBSK'>RBSK</option>
                    <option value='RKSK'>RKSK</option>
                    <option value='Family Planning'>Family Planning</option>
                    <option value='Skill Lab'>Skill Lab</option>
                    <option value='PCPNDT'>PCPNDT</option>
                    <option value='FRU'>FRU</option>
                    <option value='Quality Assurance'>Quality Assurance</option>
                    <option value='Blood Services'>Blood Services</option>
                    <option value='ANM'>ANM</option>
                    <option value='NVBDCP'>NVBDCP</option>
                    <option value='IDSP'>IDSP</option>
                    <option value='RNTCP'>RNTCP</option>
                    <option value='NLEP'>NLEP</option>
                    <option value='NPCDCS'>NPCDCS</option>
                    <option value='NTCP'>NTCP</option>
                    <option value='NMHP'>NMHP</option>
                    <option value='NPPCD'>NPPCD</option>
                    <option value='NPPCF'>NPPCF</option>
                    <option value='NPHCE'>NPHCE</option>
                    <option value='NOHP'>NOHP</option>
                    <option value='NPCB & YI'>NPCB & YI</option>
                    <option value='HCP'>HCP</option>
                    <option value='NPPC'>NPPC</option>
                    <option value='NIDDCP'>NIDDCP</option> -->
                                                                         
                  </select>
                  <span id="error_designation_list" class="text-danger"></span>                 
                 </div>

                 <div class="form-group col-md-4">
                 <label class="required-field">Date of joining  in present designation</label>
                 <input type="date" name="date_of_joining" id="date_of_joining" class="form-control" />
                 <span id="error_date_of_joining" class="text-danger"></span>                 
                </div> 

                 <div class="form-group col-md-4">
                 <label class="required-field">Consolidated Monthly remuneration at the time of joining</label>
                 <input type="number" name="con_rem_time_joining" id="con_rem_time_joining" class="form-control" />
                 <span id="error_con_rem_time_joining" class="text-danger"></span>                 
                </div>

              <!--   <div class="form-group col-md-4">
                 <label class="required-field">Consolidated Monthly Salary at the time of joining</label>
                 <input type="number" name="con_monthly_salary_joining" id="con_monthly_salary_joining" class="form-control" />
                 <span id="error_con_monthly_salary_joining" class="text-danger"></span>                 
                </div> -->

                 <div class="form-group col-md-4" >
                 <label class="required-field">Date of joining  in present place of posting</label>
                 <input type="date" name="date_of_joining_in_posting" id="date_of_joining_in_posting" class="form-control" />
                 <span id="error_date_of_joining_in_posting" class="text-danger"></span>                 
                </div> 

                 <div class="form-group col-md-6">
                 <label class="required-field">Consolidated Monthly Remuneration as on 01.04.2019</label>
                 <input type="number" name="monthly_rem" id="monthly_rem" class="form-control" />
                 <span id="error_monthly_rem" class="text-danger"></span>                 
                </div>

                <div class="form-group col-md-6" >
                 <label class="required-field">Posting Level</label>
                  <select class="form-control select2 js-posting_level" name="posting_level" id="posting_level">
                    <option value="">---------Select Option---------</option>
                      @foreach ($nhm_posting_levels as $nhm_posting_level)
                                <option value="{{$nhm_posting_level->name}}">{{$nhm_posting_level->name}}</option>
                      @endforeach  
                                                                         
                  </select>
                  <span id="error_posting_level" class="text-danger"></span>                 
                 </div>

                  <div class="form-group col-md-12" id="posting_place_div" >
                 <label class="required-field">Place of Posting </label>
                  <select class="form-control select2 js-posting_place" name="posting_place" id="posting_place">
                    <option value="">---------Select Option---------</option>
                     
                                                                         
                  </select>
                  <span id="error_posting_place" class="text-danger"></span>                 
                 </div>

                 
                <br>

                 <!--  <div class="form-group col-md-12" >
                   <label style="font-size: medium;">Leave Status(Current Financial)</label>             
                  </div>
                  <div class="form-group col-md-4">
                   <label>Casual leave availed</label>
                   <input type="number" name="casual_leave_availed" id="casual_leave_availed"    class="form-control" />
                   <span id="error_casual_leave_availed" class="text-danger"></span>                 
                  </div>
                  <div class="form-group col-md-4">
                   <label>Earned leave availed</label>
                   <input type="number" name="earned_leave_availed" id="earned_leave_availed"    class="form-control" />
                   <span id="error_earned_leave_availed" class="text-danger"></span>                 
                  </div> -->

                <br>

                <div align="center" class="col-md-12">
                 <button type="button" name="previous_btn_experience_details" id="previous_btn_experience_details" class="btn btn-default btn-lg">Previous</button>
                <!--  <button type="button" name="btn_experience_details" id="btn_experience_details" class="btn btn-success btn-lg">Next</button> -->

                <input type="button" class="btn btn-success btn-lg" name="btn_submit_preview"    
                id="btn_submit_preview" value="Preview and Submit" data-toggle="modal" data-target="#confirm-submit">
                
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
                <div class="row color1">
                  <div class="col-md-12"><h2>Personal Details</h2></div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="modal_field_name">Name:</div>
                        <div class="modal_field_value" id=name_modal></div>
                    </div>
                </div>
                    <!-- <tr>
                        <th>Title</th>
                        <td id="title_modal"></td>
                        <th>First Name</th>
                        <td id="first_name_modal"></td>
                        <th>Middle Name</th>
                        <td id="middle_name_modal"></td>
                        <th>Last Name</th>
                        <td id="last_name_modal"></td>
                    </tr> -->
                    <div class="row">
                      <div class="col-md-6">
                        <div class="modal_field_name">Name of Father/Mother/Spouse:</div>
                        <div class="modal_field_value" id="guardian_name_modal"></div>
                      </div>
                        <div class="col-md-6">
                        <div class="modal_field_name">Relationship:</div>
                        <div  class="modal_field_value" id="guardian_relation_modal"></div>
                        </div>
                    </div>
                     
                     <div class="row">
                        <div class="col-md-6">
                        <div class="modal_field_name" style="margin-right:6%;">Date of Birth (YYYY-MM-DD):</div>
                        <div class="modal_field_value" id="dob_modal" ></div>
                        </div>
                        <div class="col-md-6">
                        <div class="modal_field_name" style="margin-right:6%;">Gender:</div>
                        <div class="modal_field_value" id="gender_modal"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                        <div class="modal_field_name" style="margin-right:6%;">Caste Category:</div>
                        <div class="modal_field_value" id="caste_category_modal"></div>
                        </div>
                        <div class="col-md-6">
                        <div class="modal_field_name" style="margin-right:6%;">Whether engaged under PWD:</div>
                        <div class="modal_field_value" id="pwd_modal"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                        <!-- <th>Marital Status</th>
                        <td id="marital_status_modal"></td> -->
                        <div class="modal_field_name" style="margin-right:6%;">Mobile Number 1:</div>
                        <div class="modal_field_value" id="mobile_number_1_modal"></div>
                        </div>
                        <div class="col-md-6">
                        <div class="modal_field_name" style="margin-right:6%;">Mobile Number 2:</div>
                        <div class="modal_field_value" id="mobile_number_2_modal"></div>
                        </div>
                        

                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="modal_field_name" style="margin-right:6%;">Email:</div>
                        <div class="modal_field_value" id="email_modal"></div>
                        </div>
                      <div class="col-md-6">
                        <div class="modal_field_name" style="margin-right:6%;">Marital Status:</div>
                        <div class="modal_field_value" id="marital_status_modal"></div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="modal_field_name" style="margin-right:6%;">Identification Mark:</div>
                        <div class="modal_field_value" id="identification_mark_modal"></div>
                      </div>
                      <div class="col-md-6">
                        <div class="modal_field_name" style="margin-right:6%;">Blood Group:</div>
                        <div class="modal_field_value" id="blood_group_modal"></div>
                      </div>                      
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="modal_field_name" style="margin-right:0%;">Name of the person in case of Emergency:</div>
                        <div class="modal_field_value" id="person_name_emergency_modal"></div>
                      </div>
                      <div class="col-md-6">
                         <div class="modal_field_name" style="margin-right:3%;">Mobile No of the person in case of Emergency:</div>
                        <div class="modal_field_value" id="person_emergency_mobile_modal"></div>
                      </div>
                    </div>
                     
                     <div class="row">
                      <div class="col-md-12">
                        <div class="modal_field_name">Present Address:</div>
                        <div class="modal_field_value" id="present_address_modal"></div>
                      </div>
                      <div class="col-md-12">
                        <div class="modal_field_name">Police Station:</div>
                        <div class="modal_field_value" id="present_address_police_station_modal"></div>
                      </div>
                        <div class="col-md-12">
                        <div class="modal_field_name">District:</div>
                        <div class="modal_field_value" id="present_address_district_modal"></div>
                      </div>
                      <div class="col-md-12">
                        <div class="modal_field_name">Pincode:</div>
                        <div class="modal_field_value" id="present_address_pincode_modal"></div>
                      </div>
                      <div class="col-md-12">
                        <div class="modal_field_name">If District is Outside WB,Specify District and State:</div>
                        <div class="modal_field_value" id="other_district_present_address_modal"></div>
                      </div>
                    </div>
                     
                     <div class="row">
                      <div class="col-md-12">
                        <div class="modal_field_name">Permanent Address:</div>
                        <div class="modal_field_value" id="permanent_address_modal"></div>
                      </div>
                      <div class="col-md-12">
                        <div class="modal_field_name">Police Station:</div>
                        <div class="modal_field_value" id="permanent_address_police_station_modal"></div>
                      </div>
                      <div class="col-md-12">
                        <div class="modal_field_name">District:</div>
                        <div class="modal_field_value" id="permanent_address_district_modal"></div>
                      </div>
                      <div class="col-md-12">
                        <div class="modal_field_name">Pincode:</div>
                        <div class="modal_field_value" id="permanent_address_pincode_modal"></div>
                      </div>
                       <div class="col-md-12">
                        <div class="modal_field_name">If District is Outside WB,Specify District and State:</div>
                        <div class="modal_field_value" id="other_district_permanent_address_modal"></div>
                      </div>
                    </div>
                   </div>

                   <div class="section1 ">   
                    <div class="row color1">
                      <div class="col-md-12"><h2 >Qualifications</h2></div>
                    </div>
                    <div clas="row">
                      <div class="col-md-12">
                        <div class="modal_field_name">Highest Educational Qualification:</div>
                        <div class="modal_field_value" id="highest_education_modal"></div>
                      </div>
                    </div>
                      <div class="row">
                        <div class="col-md-12">
                        <div class="modal_field_name">Technical Qualification:</div>
                        
                        <div class="modal_field_value" id="technical_qualification_modal"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                        <div class="modal_field_name">Professional Qualification:</div>
                        <div class="modal_field_value"  id="professional_qualification_modal"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                        <div class="modal_field_name">If Others,Please Specify:</div>
                        <div class="modal_field_value"  id="other_professional_qualification_modal"></div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-12">
                         <div class="modal_field_name">If Professional qualification is MBBS/BDS/BHMS/BUMS/BAMS/ Nursing Staff /Pharmacist, then Registration of respective council:</div>

                        <!-- </div> -->
                        <!-- <div class="col-md-2"> -->
                        <div class="modal_field_value" id="registration_modal"style="
                         "></div>
                        </div>
                      </div>
                      </div>

                      <div class="section1">
                       <div class="row color1">
                        <div class="col-md-12"><h2 style="">Salary Account Details</h2></div>
                       </div>
                       <div class="row">
                        <div class="col-md-6">
                        <div class="modal_field_name">PAN:</div>
                        <div class="modal_field_value" id="pan_modal"></div>
                        </div>                        
                      
                        <div class="col-md-6">
                        <div class="modal_field_name">Bank Account Number (Salary Account):</div>
                        <div class="modal_field_value" id="bank_account_number_modal"></div>
                        </div>
                       </div>
                       <div class="row">
                        <div class="col-md-6">
                         <div class="modal_field_name">Name Of the Bank:</div>
                         <div class="modal_field_value" id="name_of_bank_modal"></div>
                        </div>
                      
                        <div class="col-md-6">
                          <div class="modal_field_name">Name Of the Bank Branch:</div>
                          <div class="modal_field_value" id="bank_branch_modal"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                        <div class="modal_field_name">IFSC Code of Salary Account:</div>
                        <div class="modal_field_value" id="bank_ifsc_code_modal"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                        <div class="modal_field_name">Whether UAN is applicable?</div>
                        <div class="modal_field_value" id="is_uan_present_modal"></div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                        <div class="modal_field_name">UAN Number:</div>
                        <div class="modal_field_value" id="uin_number_modal"></div>
                        </div>
                      </div>
                     </div>

                     <div class="section1">
                      <div class="row color1">
                        <div class="col-md-12"><h2 style="">Experience</h2></div>
                      </div>
                       <div class="row">
                        <div class="col-md-12">
                        <div class="modal_field_name">If previously engaged under NHM / NUHM at any level (If it is different from present post):</div>
                        <div class="modal_field_value" id="engaged_or_not_nhm_modal"></div>
                        </div>
                       </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="modal_field_name">Designation:</div>
                            <div class="modal_field_value" id="designation_nhm_modal"></div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="modal_field_name">Duration(In YYYY-MM-DD):</div>
                            <div class="modal_field_value" id="e_duration_nhm_modal"></div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="modal_field_name">Last Monthly Remuneration Drawn:</div>
                            <div class="modal_field_value" id="e_remuneration_nhm_modal"></div>
                          </div>
                        </div>
                         <div class="row">
                          <div class="col-md-12">
                            <div class="modal_field_name">Remarks:</div>
                            <div class="modal_field_value" id="e_remarks_nhm_modal"></div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="modal_field_name">Experience(Years & Month):</div>
                            <div class="modal_field_value" id="experience_year_month_nhm_modal"></div>
                          </div>
                        </div>
                      <!-- </div> -->
                      

                      <div class="row">
                        <div class="col-md-12">
                        <div class="modal_field_name">If engaged in any project/programme/scheme under H & FW or any other Department of Government of West Bengal previously:</div>
                        <div class="modal_field_value" id="engaged_or_not_hfw_modal"></div>
                        </div>
                      </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="modal_field_name">Designation:</div>
                            <div class="modal_field_value" id="designation_hfw_modal"></div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="modal_field_name">Duration(In YYYY-MM-DD):</div>
                            <div class="modal_field_value" id="e_duration_hfw_modal"></div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="modal_field_name">Last Monthly Remuneration Drawn:</div>
                            <div class="modal_field_value" id="e_remuneration_hfw_modal"></div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="modal_field_name">Remarks:</div>
                            <div class="modal_field_value" id="e_remarks_hfw_modal"></div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="modal_field_name">Experience(Years & Month):</div>
                            <div class="modal_field_value" id="experience_year_month_hfw_modal"></div>
                          </div>
                        </div>
                      <!-- </div> -->
                      <div class="row">
                        <div class="col-md-12"><h4 style="text-decoration:underline;">Details of Engagement in present post under NHM/NUHM</h4></div>
                        <div class="row">
                          <div class="col-md-12">
                             <div class="modal_field_name">Advertisement Number:</div>
                            <div class="modal_field_value" id="advertisement_number_modal"></div>
                          </div>
                        </div>
                          <div class="row">
                             <div class="col-md-6">
                             <div class="modal_field_name">Appointing Authority:</div>
                             <div class="modal_field_value" id="appointing_authority_modal"></div>
                             </div>
                             <div class="col-md-6">
                             <div class="modal_field_name">Contractual Employement Under:</div>
                             <div class="modal_field_value" id="contractual_employment_under_modal"></div>
                             </div>
                             
                          </div>
                          <div class="row">
                             <div class="col-md-6">
                             <div class="modal_field_name">Service Category:</div>
                             <div class="modal_field_value" id="service_category_modal"></div>
                             </div>
                             <div class="col-md-6">
                             <div class="modal_field_name">Contractual under NHM - Major Programme Head:</div>
                             <div class="modal_field_value" id="contractual_under_nhm_modal"></div>
                             </div>
                             
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                             <div class="modal_field_name">Programme Head:</div>
                             <div class="modal_field_value" id="programme_head_modal"></div>
                             </div>
                             <div class="col-md-6">
                             <div class="modal_field_name">Designation List:</div>
                             <div class="modal_field_value" id="designation_list_modal"></div>
                             </div>
                          </div>
                            <div class="row">
                             <div class="col-md-12">
                             <div class="modal_field_name">Date of joining in present designation (YYYY-MM-DD):</div>
                             <div class="modal_field_value" id="date_of_joining_modal"></div>
                             </div>
                            </div>
                            <div class="row">
                             <div class="col-md-12">
                             <div class="modal_field_name">Consolidated Monthly remuneration at the time of joining:</div>
                             <div class="modal_field_value" id="con_rem_time_joining_modal"></div>
                             </div>
                            </div>
                           <!--  <div class="row">
                             <div class="col-md-12">
                             <div class="modal_field_name">Consolidated Monthly remuneration at the time of joining:</div>
                             <div class="modal_field_value" id="con_monthly_salary_joining_modal"></div>
                             </div>
                            </div> -->
                            <div class="row">
                             <div class="col-md-12">
                             <div class="modal_field_name">Date of joining in present place of posting (YYYY-MM-DD):</div>
                             <div class="modal_field_value" id="date_of_joining_in_posting_modal"></div>
                             </div>
                            </div>
                          <div class="row">
                            <div class="col-md-12">
                              <div class="modal_field_name"> Consolidated Monthly Remuneration as on 01.04.2019:</div>
                              <div class="modal_field_value" id="monthly_rem_modal"></div>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                              <div class="modal_field_name">Posting Level:</div>
                              <div class="modal_field_value" id="posting_level_modal"></div>
                            </div>
                          </div>
                        <div class="row">
                            <div class="col-md-12">
                              <div class="modal_field_name">Place of Posting :</div>
                              <div class="modal_field_value" id="posting_place_modal"></div>
                            </div>
                          </div>  
                        </div>
                        
                        </div>
                     
<!-- 
                      <div class="row">
                        <div class="col-md-12"><h4 style="text-decoration:underline;">Leave Status</h4></div>

                          <div class="row">
                             <div class="col-md-6">
                             <div class="modal_field_name">Casual leave availed:</div>
                             <div class="modal_field_value" id="casual_leave_availed_modal"></div>
                             </div>
                             <div class="col-md-6">
                             <div class="modal_field_name">Earned leave availed:</div>
                             <div class="modal_field_value" id="earned_leave_availed_modal"></div>
                             </div>
                             
                          </div>

                        </div> -->
                      </div>
                       </div>
                 
                      


            <!-- </div> -->
          <!-- </div> -->

            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Cancel</button>
                <input type="submit"  id="submit" value="Submit"class="btn btn-success success btn-lg">
                <!-- <input type="button" class="btn btn-success button-lg" name="btn_submit_preview"    
                id="btn_submit_preview" value="Preview and Submit" data-toggle="modal" data-target="#confirm-submit"> -->
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

  <!-- Footer -->
  @include('layouts.footer')
  
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

<script type="text/javascript">
function calculateExperienceNHM(){
  // JavaScript program to illustrate  
// calculation of no. of days between two date  

// To set two dates to two variables 
var dfrom_nhm=$('#e_duration_from_nhm').val();
var dto_nhm=$('#e_duration_to_nhm').val();
var date1=new Date(dfrom_nhm);
var date2=new Date(dto_nhm);
// var date1 = new Date("06/30/2019"); 
// var date2 = new Date("07/30/2019"); 
  
// To calculate the time difference of two dates 
var Difference_In_Time = date2.getTime() - date1.getTime(); 
  
// To calculate the no. of days between two dates 
var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24); 
var Difference_In_Years_final=Math.floor(Difference_In_Days/365);
var days_left=Difference_In_Days%365;
if (days_left>=30){
   Difference_In_Months_final=Math.floor(days_left/30);
}else{
    Difference_In_Months_final=0;
}


// var Difference_In_Months = Math.floor(Difference_In_Days/ 30);
// var Difference_In_Years = Math.floor(Difference_In_Months/ 12);
// var Difference_In_Months_final = Math.floor(Difference_In_Months % 12);
  
//To display the final no. of days (result) 
// document.write("Total number of days between dates  <br>"
//                + date1 + "<br> and <br>" 
//                + date2 + " is: <br> " 
//                + Difference_In_Days); 
// alert(Difference_In_Days);
// alert(Difference_In_Months);
$('#experience_year_month_nhm').val(Difference_In_Years_final+' Years '+Difference_In_Months_final+' Months');
// alert("years final"+Difference_In_Years_final);
// alert("months final"+Difference_In_Months_final);
//alert("mnths final"+Difference_In_Months_final);
//alert("months"+Difference_In_Months);


}


function calculateExperienceHFW(){
  // JavaScript program to illustrate  
// calculation of no. of days between two date  

// To set two dates to two variables 
var dfrom_hfw=$('#e_duration_from_hfw').val();
var dto_hfw=$('#e_duration_to_hfw').val();
var date1=new Date(dfrom_hfw);
var date2=new Date(dto_hfw);
// var date1 = new Date("06/30/2019"); 
// var date2 = new Date("07/30/2019"); 
  
// To calculate the time difference of two dates 
var Difference_In_Time = date2.getTime() - date1.getTime(); 
  
// To calculate the no. of days between two dates 
var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24); 
var Difference_In_Years_final=Math.floor(Difference_In_Days/365);
var days_left=Difference_In_Days%365;
if (days_left>=30){
   Difference_In_Months_final=Math.floor(days_left/30);
}else{
    Difference_In_Months_final=0;
}


// var Difference_In_Months = Math.floor(Difference_In_Days/ 30);
// var Difference_In_Years = Math.floor(Difference_In_Months/ 12);
// var Difference_In_Months_final = Math.floor(Difference_In_Months % 12);
  
//To display the final no. of days (result) 
// document.write("Total number of days between dates  <br>"
//                + date1 + "<br> and <br>" 
//                + date2 + " is: <br> " 
//                + Difference_In_Days); 
// alert(Difference_In_Days);
// alert(Difference_In_Months);
$('#experience_year_month_hfw').val(Difference_In_Years_final+' Years '+Difference_In_Months_final+' Months');
// alert("years final"+Difference_In_Years_final);
// alert("months final"+Difference_In_Months_final);
//alert("mnths final"+Difference_In_Months_final);
//alert("months"+Difference_In_Months);


}


</script>
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
<script>





$(document).ready(function(){

//$('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
 

//prevent numeric entry in text 
$('.txtOnly').keydown(function (e) {
  
    if (e.altKey) {
    
      e.preventDefault();
      
    } else {
    
      var key = e.keyCode;
      
      if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90))) {
      
        e.preventDefault();
        
      }

    }
    
  });

$('.NumOnly').keydown(function (e) {
  
    if (e.altKey) {
    
      e.preventDefault();
      
    } else {
    
      var key = e.keyCode;
      
      if (key > 31 && (key < 48 || key > 57)) {
      
        e.preventDefault();
        
      }

    }
    
  });


  



other_district_permanent_address=0;
other_district_present_address=0;
error_other_district_permanent_address_check=0;
error_other_district_present_address_check=0;
//same as present address code
$('#same_as_present_address').click(function() {   

   $('#permanent_address_line1').val($('#present_address_line1').val());
   //$('#permanent_address_district').val($('#present_address_district').val());
   $('#permanent_address_district').next().find('.select2-selection__rendered').text($('#present_address_district option:selected').text());
   $('#permanent_address_district').val($('#present_address_district option:selected').val());
   // test=$('#present_address_district option:selected').text();
   // alert(test);
   // test1= $('#select2-permanent_address_district-container').val();
   // alert("helo"+test1);
   $('#permanent_address_police_station').val($('#present_address_police_station').val());
   $('#permanent_address_pincode').val($('#present_address_pincode').val());

   if(other_district_present_address==1){
   $("#other_district_permanent_address_div").show();
   other_district_permanent_address=1;
    }

   $('#other_district_permanent_address').val($('#other_district_present_address').val());
});



// prof_other=0;
// error_registration_check=0;


$("#other_district_permanent_address_div").hide();
$('#permanent_address_district').change(function () {
    if ($('option:selected', this).val() == 1) {
        //$('form').hide();
        $('#other_district_permanent_address_div').show();
        other_district_permanent_address=1;
        error_other_district_permanent_address_check=0;
        // $("#other_professional_qualification").val();
        // $("#other_professional_qualification").text();
       
    }
    else {
       $('#other_district_permanent_address_div').hide();
       other_district_permanent_address=0;
       $("#other_district_permanent_address").val("");
       $("#other_district_permanent_address").text("");
      error_other_district_permanent_address_check=1;
       
    }
});


$("#other_district_present_address_div").hide();
$('#present_address_district').change(function () {
    if ($('option:selected', this).val() == 1) {
        //$('form').hide();
        $('#other_district_present_address_div').show();
        other_district_present_address=1;
        error_other_district_present_address_check=0;
        // $("#other_professional_qualification").val();
        // $("#other_professional_qualification").text();
       
    }
    else {
       $('#other_district_present_address_div').hide();
       other_district_present_address=0;
       $("#other_district_present_address").val("");
       $("#other_district_present_address").text("");
      error_other_district_present_address_check=1;
       
    }
});

 $('#btn_login_details').click(function(){

   // $('#list_login_details').removeClass('active active_tab1');
   // $('#list_login_details').removeAttr('href data-toggle');
   // $('#login_details').removeClass('active');
   // $('#list_login_details').addClass('inactive_tab1');
   // $('#list_personal_details').removeClass('inactive_tab1');
   // $('#list_personal_details').addClass('active_tab1 active');
   // $('#list_personal_details').attr('href', '#personal_details');
   // $('#list_personal_details').attr('data-toggle', 'tab');
   // $('#personal_details').addClass('active in');
  
/***************************************SD********************************************/
var mobile_validation = /^\d{10}$/;
var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  

//var error_title ='';
var error_first_name = '';
var error_middle_name = '';
//var error_last_name = '';
var error_guardian_relation ='';
var error_guardian_name ='';
var error_dob ='';
var error_gender ='';
var error_caste_category ='';
var error_marital_status ='';
var error_pwd ='';
var error_mobile_number_1 ='';
var error_mobile_number_2 ='';
var error_email ='';
var error_identification_mark ='';
var error_blood_group ='';
var error_person_name_emergency ='';
var error_person_emergency_mobile ='';
var error_present_address_line1 ='';
var error_present_address_district ='';
var error_present_address_police_station ='';
var error_present_address_pincode ='';
var error_permanent_address_line1 ='';
var error_permanent_address_district ='';
var error_permanent_address_police_station ='';
var error_permanent_address_pincode ='';

var error_other_district_present='';
var error_other_district_permanent='';

mobile_2_val=0;
identification_mark_val=0;


  
  // if($.trim($('#title').val()).length == 0)
  // {
  //  error_title = 'Title is required';
  //  $('#error_title').text(error_title);
  //  $('#title').next().find('.select2-selection').addClass('has-error');
  // }
  // else
  // {
  //  error_title = '';
  //  $('#error_title').text(error_title);
  //  $('#title').next().find('.select2-selection').removeClass('has-error');
  // }

  

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
  
  // if($.trim($('#last_name').val()).length == 0)
  // {
  //  error_last_name = 'Last Name is required';
  //  $('#error_last_name').text(error_last_name);
  //  $('#last_name').addClass('has-error');
  // }
  // else
  // {
  //  error_last_name = '';
  //  $('#error_last_name').text(error_last_name);
  //  $('#last_name').removeClass('has-error');
  // }

if($.trim($('#guardian_relation').val()).length == 0)
  {
   error_guardian_relation = 'Relation required';
   $('#error_guardian_relation').text(error_guardian_relation);
   $('#guardian_relation').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_guardian_relation = '';
   $('#error_guardian_relation').text(error_guardian_relation);
   $('#guardian_relation').next().find('.select2-selection').removeClass('has-error');
  }

  if($.trim($('#guardian_name').val()).length == 0)
  {
   error_guardian_name = 'Name of Father / Mother / Spouse is required';
   $('#error_guardian_name').text(error_guardian_name);
   $('#guardian_name').addClass('has-error');
  }
  else
  {
   error_guardian_name = '';
   $('#error_guardian_name').text(error_guardian_name);
   $('#guardian_name').removeClass('has-error');
  }

  if($.trim($('#dob').val()).length == 0)
  {
   error_dob = 'Date of Birth is required';
   $('#error_dob').text(error_dob);
   $('#dob').addClass('has-error');
  }
  else
  {
   error_dob = '';
   //alert($('#dob').val());
   $('#error_dob').text(error_dob);
   $('#dob').removeClass('has-error');
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

   if($.trim($('#caste_category').val()).length == 0)
  {
   error_caste_category = 'Catergory is required';
   $('#error_caste_category').text(error_caste_category);
   $('#caste_category').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_caste_category = '';
   $('#error_caste_category').text(error_caste_category);
   $('#caste_category').next().find('.select2-selection').removeClass('has-error');
  }

   if($.trim($('#pwd').val()).length == 0)
  {
   error_pwd = 'Field is required';
   $('#error_pwd').text(error_pwd);
   $('#pwd').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_pwd = '';
   $('#error_pwd').text(error_pwd);
   $('#pwd').next().find('.select2-selection').removeClass('has-error');
  }

   if($.trim($('#marital_status').val()).length == 0)
  {
   error_marital_status = 'Marital Status is required';
   $('#error_marital_status').text(error_marital_status);
   $('#marital_status').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_marital_status = '';
   $('#error_marital_status').text(error_marital_status);
   $('#marital_status').next().find('.select2-selection').removeClass('has-error');
  }  
  

  if($.trim($('#mobile_number_1').val()).length == 0){
   error_mobile_number_1 = 'Field is required';
   $('#error_mobile_number_1').text(error_mobile_number_1);
   $('#mobile_number_1').addClass('has-error');
 }
 else if($('#mobile_number_1').val().length>0){
   if (!mobile_validation.test($('#mobile_number_1').val()))
   {
    error_mobile_number_1 = 'Invalid Mobile Number';
    $('#error_mobile_number_1').text(error_mobile_number_1);
    $('#mobile_number_1').addClass('has-error');
   }else if($('#mobile_number_1').val().length!=10){
      error_mobile_number_1 = 'Invalid Mobile Number';
      $('#error_mobile_number_1').text(error_mobile_number_1);
      $('#mobile_number_1').addClass('has-error');
   }
   else
   {
    error_mobile_number_1 = '';
    $('#error_mobile_number_1').text(error_mobile_number_1);
    $('#mobile_number_1').removeClass('has-error');
   }
 }


//old validation for mobile1
  // if($('#mobile_number_1').val().length>0){

  //  if (!mobile_validation.test($('#mobile_number_1').val()))
  //  {
  //   error_mobile_number_1 = 'Invalid Mobile Number';
  //   $('#error_mobile_number_1').text(error_mobile_number_1);
  //   $('#mobile_number_1').addClass('has-error');
  //  }else if($('#mobile_number_1').val().length != 10){
  //   error_mobile_number_1 = 'Invalid Mobile Number';
  //   $('#error_mobile_number_1').text(error_mobile_number_1);
  //   $('#mobile_number_1').addClass('has-error');
  //  }
  // }else if($.trim($('#mobile_number_1').val()).length == 0){
  //   error_mobile_number_1= 'Mobile Number is required';
  //   $('#error_mobile_number_1').text(error_mobile_number_1);
  //   $('#mobile_number_1').addClass('has-error');
  // }   
  //  else
  //  {
  //   error_mobile_number_1 = '';
  //   $('#error_mobile_number_1').text(error_mobile_number_1);
  //   $('#mobile_number_1').removeClass('has-error');
  //  }
  
 //old mobile2 validation  
  //  if($('#mobile_number_2').val().length>0){  
  //    if (!mobile_validation.test($('#mobile_number_2').val()))
  //    {
  //     error_mobile_number_2 = 'Invalid Mobile Number';
  //     $('#error_mobile_number_2').text(error_mobile_number_2);
  //     $('#mobile_number_2').addClass('has-error');
      
  //    }else if($('#mobile_number_2').val().length!=10){
  //     error_mobile_number_2 = 'Invalid Mobile Number';
  //     $('#error_mobile_number_2').text(error_mobile_number_2);
  //     $('#mobile_number_2').addClass('has-error');
     
  //    }
  // }
  //  else
  //  {
  //    if($.trim($('#mobile_number_2').val()).length == 0)
  //     {
  //        mobile_2_val=0;
  //     }
  //     else{
  //       mobile_2_val=1;
  //     }
  //   error_mobile_number_2 = '';
  //   $('#error_mobile_number_2').text(error_mobile_number_2);
  //   $('#mobile_number_2').removeClass('has-error');
  //   // mobile_2_val=1;
  //  }
  

  if($.trim($('#mobile_number_2').val()).length == 0){
    mobile_2_val=0;
 }
 else if($('#mobile_number_2').val().length>0){
   if (!mobile_validation.test($('#mobile_number_2').val()))
   {
    error_mobile_number_2 = 'Invalid Mobile Number';
    $('#error_mobile_number_2').text(error_mobile_number_2);
    $('#mobile_number_2').addClass('has-error');
   }else if($('#mobile_number_2').val().length!=10){
      error_mobile_number_2 = 'Invalid Mobile Number';
      $('#error_mobile_number_2').text(error_mobile_number_2);
      $('#mobile_number_2').addClass('has-error');
   }
   else
   {
    error_mobile_number_2 = '';
    mobile_2_val=1;
    $('#error_mobile_number_2').text(error_mobile_number_2);
    $('#mobile_number_2').removeClass('has-error');
   }
 }



  
  
  if($.trim($('#email').val()).length == 0)
  {
   error_email = 'Email is required';
   $('#error_email').text(error_email);
   $('#email').addClass('has-error');
  }
  else
  {
   if (!filter.test($('#email').val()))
   {
    error_email = 'Invalid Email';
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

  // if($.trim($('#identification_mark').val()).length == 0)
  // {
  //  error_identification_mark = 'Identification Mark is required';
  //  $('#error_identification_mark').text(error_identification_mark);
  //  $('#identification_mark').addClass('has-error');
  // }
  // else
  // {
  //  error_identification_mark = '';
  //  $('#error_identification_mark').text(error_identification_mark);
  //  $('#identification_mark').removeClass('has-error');
  // }
  if($.trim($('#identification_mark').val()).length == 0)
  {
     identification_mark_val=0;
  }
  else{
    identification_mark_val=1;
  }



  if($.trim($('#blood_group').val()).length == 0)
  {
   error_blood_group = 'Blood Group is required';
   $('#error_blood_group').text(error_blood_group);
   $('#blood_group').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_blood_group = '';
   $('#error_blood_group').text(error_blood_group);
   $('#blood_group').next().find('.select2-selection').removeClass('has-error');
  }





if($.trim($('#person_name_emergency').val()).length == 0)
  {
   error_person_name_emergency = 'Field is required';
   $('#error_person_name_emergency').text(error_person_name_emergency);
   $('#person_name_emergency').addClass('has-error');
  }
  else
  {
   error_person_name_emergency = '';
   $('#error_person_name_emergency').text(error_person_name_emergency);
   $('#person_name_emergency').removeClass('has-error');
  }


if($.trim($('#person_emergency_mobile').val()).length == 0){
   error_person_emergency_mobile = 'Field is required';
   $('#error_person_emergency_mobile').text(error_person_emergency_mobile);
   $('#person_emergency_mobile').addClass('has-error');
 }
 else if($('#person_emergency_mobile').val().length>0){
   if (!mobile_validation.test($('#person_emergency_mobile').val()))
   {
    error_person_emergency_mobile = 'Invalid Mobile Number';
    $('#error_person_emergency_mobile').text(error_person_emergency_mobile);
    $('#person_emergency_mobile').addClass('has-error');
   }else if($('#person_emergency_mobile').val().length!=10){
      error_person_emergency_mobile = 'Invalid Mobile Number';
      $('#error_person_emergency_mobile').text(error_person_emergency_mobile);
      $('#person_emergency_mobile').addClass('has-error');
   }
   else
   {
    error_person_emergency_mobile = '';
    $('#error_person_emergency_mobile').text(error_person_emergency_mobile);
    $('#person_emergency_mobile').removeClass('has-error');
   }
 }



//old validation for emergency  mobile number
// if($('#person_emergency_mobile').val().length>0){
//    if (!mobile_validation.test($('#person_emergency_mobile').val()))
//    {
//     error_person_emergency_mobile = 'Invalid Mobile Number';
//     $('#error_person_emergency_mobile').text(error_person_emergency_mobile);
//     $('#person_emergency_mobile').addClass('has-error');
//    }else if($('#person_emergency_mobile').val().length!=10){
//       error_person_emergency_mobile = 'Invalid Mobile Number';
//       $('#error_person_emergency_mobile').text(error_person_emergency_mobile);
//       $('#person_emergency_mobile').addClass('has-error');
//    }
//  }else if($.trim($('#person_emergency_mobile').val()).length == 0){
//    error_person_emergency_mobile = 'Field is required';
//    $('#error_person_emergency_mobile').text(error_person_emergency_mobile);
//    $('#person_emergency_mobile').addClass('has-error');
//  }
//    else
//    {
//     error_person_emergency_mobile = '';
//     $('#error_person_emergency_mobile').text(error_person_emergency_mobile);
//     $('#person_emergency_mobile').removeClass('has-error');
//    }





if($.trim($('#present_address_line1').val()).length == 0)
  {
   error_present_address_line1 = 'Present Address Line 1 is required';
   $('#error_present_address_line1').text(error_present_address_line1);
   $('#present_address_line1').addClass('has-error');
  }
  else
  {
   error_present_address_line1 = '';
   $('#error_present_address_line1').text(error_present_address_line1);
   $('#present_address_line1').removeClass('has-error');
  }

if($.trim($('#present_address_district').val()).length == 0)
  {
   error_present_address_district = 'Present Address District is required';
   $('#error_present_address_district').text(error_present_address_district);
   $('#present_address_district').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_present_address_district = '';
   $('#error_present_address_district').text(error_present_address_district);
   $('#present_address_district').next().find('.select2-selection').removeClass('has-error');
  }

  if($.trim($('#present_address_police_station').val()).length == 0)
  {
   error_present_address_police_station = 'Police Station is required';
   $('#error_present_address_police_station').text(error_present_address_police_station);
   $('#present_address_police_station').addClass('has-error');
  }
  else
  {
   error_present_address_police_station = '';
   $('#error_present_address_police_station').text(error_present_address_police_station);
   $('#present_address_police_station').removeClass('has-error');
  }

  if($.trim($('#present_address_pincode').val()).length == 0)
  {
   error_present_address_pincode = 'Pincode is required';
   $('#error_present_address_pincode').text(error_present_address_pincode);
   $('#present_address_pincode').addClass('has-error');
  }
  else
  {
   error_present_address_pincode = '';
   $('#error_present_address_pincode').text(error_present_address_pincode);
   $('#present_address_pincode').removeClass('has-error');
  }

  if($.trim($('#permanent_address_line1').val()).length == 0)
  {
   error_permanent_address_line1 = 'Permanent Address Line 1 is required';
   $('#error_permanent_address_line1').text(error_permanent_address_line1);
   $('#permanent_address_line1').addClass('has-error');
  }
  else
  {
   error_permanent_address_line1 = '';
   $('#error_permanent_address_line1').text(error_permanent_address_line1);
   $('#permanent_address_line1').removeClass('has-error');
  }
//.next().find('.select2-selection__rendered').val
if($.trim($('#permanent_address_district').val()).length == 0)
  {
   error_permanent_address_district = 'Permanent Address District is required';
   $('#error_permanent_address_district').text(error_permanent_address_district);
   $('#permanent_address_district').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_permanent_address_district = '';
   $('#error_permanent_address_district').text(error_permanent_address_district);
   $('#permanent_address_district').next().find('.select2-selection').removeClass('has-error');
  }

  if($.trim($('#permanent_address_police_station').val()).length == 0)
  {
   error_permanent_address_police_station = 'Police Station is required';
   $('#error_permanent_address_police_station').text(error_permanent_address_police_station);
   $('#permanent_address_police_station').addClass('has-error');
  }
  else
  {
   error_permanent_address_police_station = '';
   $('#error_permanent_address_police_station').text(error_permanent_address_police_station);
   $('#permanent_address_police_station').removeClass('has-error');
  }

  if($.trim($('#permanent_address_pincode').val()).length == 0)
  {
   error_permanent_address_pincode = 'Pincode is required';
   $('#error_permanent_address_pincode').text(error_permanent_address_pincode);
   $('#permanent_address_pincode').addClass('has-error');
  }
  else
  {
   error_permanent_address_pincode = '';
   $('#error_permanent_address_pincode').text(error_permanent_address_pincode);
   $('#permanent_address_pincode').removeClass('has-error');
  }

//sssssssssssssssssssssss

if(other_district_present_address==1){
    if($.trim($('#other_district_present_address').val()).length == 0)
      {
       error_other_district_present = 'Field is required';
       // error_registration='';
       $('#error_other_district_present_address').text(error_other_district_present);
       $('#other_district_present_address').addClass('has-error');
       // $('#registration').removeClass('has-error');
       //  $('#error_registration').text(error_registration);
      }
    else
      {
       error_other_district_present = '';
       $('#error_other_district_present_address').text(error_other_district_present);
       $('#other_district_present_address').removeClass('has-error');
       // $('#registration').addClass('has-error');
      }
  }


  if(other_district_permanent_address==1){
    if($.trim($('#other_district_permanent_address').val()).length == 0)
      {
       error_other_district_permanent = 'Field is required';
       // error_registration='';
       $('#error_other_district_permanent_address').text(error_other_district_permanent);
       $('#other_district_permanent_address').addClass('has-error');
       // $('#registration').removeClass('has-error');
       //  $('#error_registration').text(error_registration);
      }
    else
      {
       error_other_district_permanent = '';
       $('#error_other_district_permanent_address').text(error_other_district_permanent);
       $('#other_district_permanent_address').removeClass('has-error');
       // $('#registration').addClass('has-error');
      }
  }




 // || error_middle_name !='' || error_guardian_relation != '' || error_guardian_name != '' || error_dob != '' || error_gender !='' || error_caste_category !='' || error_marital_status !='' || error_pwd !='' ||error_mobile_number_1 !='' || error_mobile_number_2 !='' || error_email !='' || error_identification_mark !='' || 
 //    error_blood_group !='' || error_person_name_emergency !='' || error_person_emergency_mobile !='' || error_present_address_line1 !='' || error_present_address_district !='' || error_present_address_police_station !='' || error_present_address_pincode !=''|| error_permanent_address_line1 !='' || error_permanent_address_district !='' || error_permanent_address_police_station !='' || error_permanent_address_pincode !='' ||error_other_district_present !='' || error_other_district_permanent !='' 

  if( error_first_name != '' )
  {
   return false;
  }
  else
  {
   // $('#list_personal_details').removeClass('active active_tab1');
   // $('#list_personal_details').removeAttr('href data-toggle');
   // $('#personal_details').removeClass('active');
   // $('#list_personal_details').addClass('inactive_tab1');
   // $('#list_contact_details').removeClass('inactive_tab1');
   // $('#list_contact_details').addClass('active_tab1 active');
   // $('#list_contact_details').attr('href', '#contact_details');
   // $('#list_contact_details').attr('data-toggle', 'tab');
   // $('#contact_details').addClass('active in');

   /*******SD**********/
   $('#list_login_details').removeClass('active active_tab1');
   $('#list_login_details').removeAttr('href data-toggle');
   $('#login_details').removeClass('active');
   $('#list_login_details').addClass('inactive_tab1');
   $('#list_personal_details').removeClass('inactive_tab1');
   $('#list_personal_details').addClass('active_tab1 active');
   $('#list_personal_details').attr('href', '#personal_details');
   $('#list_personal_details').attr('data-toggle', 'tab');
   $('#personal_details').addClass('active in');
   /*******************/
  }

});
 





/*************************************************************************************/
 
/*  var error_email = '';
  var error_password = '';
  var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  
  if($.trim($('#email').val()).length == 0)
  {
   error_email = 'Email is required';
   $('#error_email').text(error_email);
   $('#email').addClass('has-error');
  }
  else
  {
   if (!filter.test($('#email').val()))
   {
    error_email = 'Invalid Email';
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
  
  if($.trim($('#password').val()).length == 0)
  {
   error_password = 'Password is required';
   $('#error_password').text(error_password);
   $('#password').addClass('has-error');
  }
  else
  {
   error_password = '';
   $('#error_password').text(error_password);
   $('#password').removeClass('has-error');
  }

  if(error_email != '' || error_password != '')
  {
   return false;
  }
  else
  {
   $('#list_login_details').removeClass('active active_tab1');
   $('#list_login_details').removeAttr('href data-toggle');
   $('#login_details').removeClass('active');
   $('#list_login_details').addClass('inactive_tab1');
   $('#list_personal_details').removeClass('inactive_tab1');
   $('#list_personal_details').addClass('active_tab1 active');
   $('#list_personal_details').attr('href', '#personal_details');
   $('#list_personal_details').attr('data-toggle', 'tab');
   $('#personal_details').addClass('active in');
  }
  
 });
 */
 $('#previous_btn_personal_details').click(function(){

  $('#list_personal_details').removeClass('active active_tab1');
  $('#list_personal_details').removeAttr('href data-toggle');
  $('#personal_details').removeClass('active in');
  $('#list_personal_details').addClass('inactive_tab1');
  $('#list_login_details').removeClass('inactive_tab1');
  $('#list_login_details').addClass('active_tab1 active');
  $('#list_login_details').attr('href', '#login_details');
  $('#list_login_details').attr('data-toggle', 'tab');
  $('#login_details').addClass('active in');
 });
 
//check professional qualification for others
prof_other=0;
error_registration_check=0;

$("#prof_other").hide();
$('#professional_qualification').change(function () {
    if ($('option:selected', this).val() == "Others") {
        //$('form').hide();
        $('#prof_other').show();
        prof_other=1;
        error_registration_check=0;
        // $("#other_professional_qualification").val();
        // $("#other_professional_qualification").text();
       
    }else if  ($('option:selected', this).val() == ""){
       error_registration_check=0;
        $('#prof_other').hide();
        prof_other=0;
        $("#other_professional_qualification").val("");
        $("#other_professional_qualification").text("");
    }
    else {
       $('#prof_other').hide();
       prof_other=0;
       $("#other_professional_qualification").val("");
       $("#other_professional_qualification").text("");
      error_registration_check=1;
       
    }
});


 
 


 $('#btn_personal_details').click(function(){

  /***********************************************SD*********************************/
  // $('#list_personal_details').removeClass('active active_tab1');
  // $('#list_personal_details').removeAttr('href data-toggle');
  // $('#personal_details').removeClass('active');
  // $('#list_personal_details').addClass('inactive_tab1');
  // $('#list_contact_details').removeClass('inactive_tab1');
  // $('#list_contact_details').addClass('active_tab1 active');
  // $('#list_contact_details').attr('href', '#contact_details');
  // $('#list_contact_details').attr('data-toggle', 'tab');
  // $('#contact_details').addClass('active in');
 
  var error_highest_education ='';
  var error_other_professional_qualification='';
  var error_registration='';
  professional_qualification_val='';
  registration_val='';
  technical_qualification_vals='';

  
 
  if($.trim($('#highest_education').val()).length == 0)
  {
   error_highest_education = 'Highest Education is required';
   $('#error_highest_education').text(error_highest_education);
   $('#highest_education').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_highest_education = '';
   $('#error_highest_education').text(error_highest_education);
   $('#highest_education').next().find('.select2-selection').removeClass('has-error');
  }

   if($.trim($('#technical_qualification').val()).length == 0)
  {
     technical_qualification_vals=0;
  }else{
    technical_qualification_vals=1;
  }
 
 if($.trim($('#professional_qualification').val()).length == 0)
  {
     professional_qualification_val=0;
  }
  else{
    professional_qualification_val=1;
  }

  if($.trim($('#registration').val()).length == 0)
  {
     registration_val=0;
  }else{
    registration_val=1;
  }

  if(prof_other==1){
    if($.trim($('#other_professional_qualification').val()).length == 0)
      {
       error_other_professional_qualification = 'Field is required';
       error_registration='';
       $('#error_other_professional_qualification').text(error_other_professional_qualification);
       $('#other_professional_qualification').addClass('has-error');
       $('#registration').removeClass('has-error');
        $('#error_registration').text(error_registration);
      }
    else
      {
       error_other_professional_qualification = '';
       $('#error_other_professional_qualification').text(error_other_professional_qualification);
       $('#other_professional_qualification').removeClass('has-error');
       // $('#registration').addClass('has-error');
      }
  }else{

    if(error_registration_check==1){
     if($.trim($('#registration').val()).length == 0)
            {
             error_registration = 'Registration is required';
             $('#error_registration').text(error_registration);
             $('#registration').addClass('has-error');
            }
        else
            {
             error_registration = '';
             $('#error_registration').text(error_registration);
             $('#registration').removeClass('has-error');
            }
      $('#registration_label').addClass('required-field');
    }

  }

  if(error_highest_education != '' || error_registration !='' ||( error_other_professional_qualification !=''))
  {
   return false;
  }
  else
  {
    /****SD***/
  // $('#list_personal_details').removeClass('active active_tab1');
  // $('#list_personal_details').removeAttr('href data-toggle');
  // $('#personal_details').removeClass('active');
  // $('#list_personal_details').addClass('inactive_tab1');
  // $('#list_contact_details').removeClass('inactive_tab1');
  // $('#list_contact_details').addClass('active_tab1 active');
  // $('#list_contact_details').attr('href', '#contact_details');
  // $('#list_contact_details').attr('data-toggle', 'tab');
  // $('#contact_details').addClass('active in');
  /******/
   $('#list_personal_details').removeClass('active active_tab1');
   $('#list_personal_details').removeAttr('href data-toggle');
   $('#personal_details').removeClass('active');
   $('#list_personal_details').addClass('inactive_tab1');
   $('#list_contact_details').removeClass('inactive_tab1');
   $('#list_contact_details').addClass('active_tab1 active');
   $('#list_contact_details').attr('href', '#contact_details');
   $('#list_contact_details').attr('data-toggle', 'tab');
   $('#contact_details').addClass('active in');
  }


 /********************************************************************************/
  /*

  var error_first_name = '';
  var error_last_name = '';
  
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

  if(error_first_name != '' || error_last_name != '')
  {
   return false;
  }
  else
  {
   $('#list_personal_details').removeClass('active active_tab1');
   $('#list_personal_details').removeAttr('href data-toggle');
   $('#personal_details').removeClass('active');
   $('#list_personal_details').addClass('inactive_tab1');
   $('#list_contact_details').removeClass('inactive_tab1');
   $('#list_contact_details').addClass('active_tab1 active');
   $('#list_contact_details').attr('href', '#contact_details');
   $('#list_contact_details').attr('data-toggle', 'tab');
   $('#contact_details').addClass('active in');
  }*/
 });
 
 $('#previous_btn_contact_details').click(function(){
  $('#list_contact_details').removeClass('active active_tab1');
  $('#list_contact_details').removeAttr('href data-toggle');
  $('#contact_details').removeClass('active in');
  $('#list_contact_details').addClass('inactive_tab1');
  $('#list_personal_details').removeClass('inactive_tab1');
  $('#list_personal_details').addClass('active_tab1 active');
  $('#list_personal_details').attr('href', '#personal_details');
  $('#list_personal_details').attr('data-toggle', 'tab');
  $('#personal_details').addClass('active in');
 });
 

is_uan_present_val=0;
$("#uin_number_div").hide();
$('#is_uan_present').change(function () {
    if ($('option:selected', this).val() == 'Yes') {
        //$('form').hide();
        $('#uin_number_div').show();
        is_uan_present_val=1;
        //error_other_district_present_address_check=0;
        // $("#other_professional_qualification").val();
        // $("#other_professional_qualification").text();
       
    }
    else {
       $('#uin_number_div').hide();
       //other_district_present_address=0;
       is_uan_present_val=0;
       $("#uin_number").val("");
       $("#uin_number").text("");
      //error_other_district_present_address_check=1;
       
    }
});



 $('#btn_contact_details').click(function(){

  /*******************************************SD****************************/
  // $('#list_contact_details').removeClass('active active_tab1');
  // $('#list_contact_details').removeAttr('href data-toggle');
  // $('#contact_details').removeClass('active');
  // $('#list_contact_details').addClass('inactive_tab1');
  // $('#list_experience_details').removeClass('inactive_tab1');
  // $('#list_experience_details').addClass('active_tab1 active');
  // $('#list_experience_details').attr('href', '#experience_details');
  // $('#list_experience_details').attr('data-toggle', 'tab');
  // $('#experience_details').addClass('active in');
  var uin_validation = /^\d{12}$/;
  var error_pan ='';
  var error_bank_account_number ='';
  var error_name_of_bank ='';
  var error_bank_branch ='';
  var error_bank_ifsc_code ='';
  var error_uin_number='';
  var error_is_uan_present='';

  if($.trim($('#pan').val()).length == 0)
  {
   error_pan = 'PAN is required';
   $('#error_pan').text(error_pan);
   $('#pan').addClass('has-error');
  }
  else
  {
   error_pan = '';
   $('#error_pan').text(error_pan);
   $('#pan').removeClass('has-error');
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

  if($.trim($('#name_of_bank').val()).length == 0)
  {
   error_name_of_bank = 'Name of Bank is required';
   $('#error_name_of_bank').text(error_name_of_bank);
   $('#name_of_bank').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_name_of_bank = '';
   $('#error_name_of_bank').text(error_name_of_bank);
   $('#name_of_bank').next().find('.select2-selection').removeClass('has-error');
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

  if($.trim($('#bank_ifsc_code').val()).length == 0)
  {
   error_bank_ifsc_code = 'Bank Branch is required';
   $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
   $('#bank_ifsc_code').addClass('has-error');
  }
  else
  {
   error_bank_ifsc_code = '';
   $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
   $('#bank_ifsc_code').removeClass('has-error');
  }





  if($.trim($('#is_uan_present').val()).length == 0)
  {
   error_is_uan_present = 'Field is required';
   $('#error_is_uan_present').text(error_is_uan_present);
   $('#is_uan_present').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_is_uan_present = '';
   $('#error_is_uan_present').text(error_is_uan_present);
   $('#is_uan_present').next().find('.select2-selection').removeClass('has-error');
  }





if(is_uan_present_val==1){
//alert("hi"+is_uan_present_val);
 if($.trim($('#uin_number').val()).length == 0){
   error_uin_number = 'Field is required';
   $('#error_uin_number').text(error_uin_number);
   $('#uin_number').addClass('has-error');
 }
 else if($('#uin_number').val().length>0){
   if (!uin_validation.test($('#uin_number').val()))
   {
    error_uin_number = 'Invalid UIN Number';
    $('#error_uin_number').text(error_uin_number);
    $('#uin_number').addClass('has-error');
   }else if($('#uin_number').val().length!=12){
      error_uin_number = 'Invalid UIN Number';
      $('#error_uin_number').text(error_uin_number);
      $('#uin_number').addClass('has-error');
   }
   else
   {
    error_uin_number = '';
   
    $('#error_uin_number').text(error_uin_number);
    $('#uin_number').removeClass('has-error');
   }
 }
}
  
  if(error_pan != '' || error_bank_account_number !='' || error_name_of_bank !='' || error_bank_branch !='' || error_bank_ifsc_code !=''|| error_uin_number !='' || error_is_uan_present !='')
  {
   return false;
  }
  else
  {
    $('#list_contact_details').removeClass('active active_tab1');
    $('#list_contact_details').removeAttr('href data-toggle');
    $('#contact_details').removeClass('active');
    $('#list_contact_details').addClass('inactive_tab1');
    $('#list_experience_details').removeClass('inactive_tab1');
    $('#list_experience_details').addClass('active_tab1 active');
    $('#list_experience_details').attr('href', '#experience_details');
    $('#list_experience_details').attr('data-toggle', 'tab');
    $('#experience_details').addClass('active in');
  }
/*******************************************************************************/
  /*var error_address = '';
  var error_mobile_no = '';
  var mobile_validation = /^\d{10}$/;
  if($.trim($('#address').val()).length == 0)
  {
   error_address = 'Address is required';
   $('#error_address').text(error_address);
   $('#address').addClass('has-error');
  }
  else
  {
   error_address = '';
   $('#error_address').text(error_address);
   $('#address').removeClass('has-error');
  }
  
  if($.trim($('#mobile_no').val()).length == 0)
  {
   error_mobile_no = 'Mobile Number is required';
   $('#error_mobile_no').text(error_mobile_no);
   $('#mobile_no').addClass('has-error');
  }
  else
  {
   if (!mobile_validation.test($('#mobile_no').val()))
   {
    error_mobile_no = 'Invalid Mobile Number';
    $('#error_mobile_no').text(error_mobile_no);
    $('#mobile_no').addClass('has-error');
   }
   else
   {
    error_mobile_no = '';
    $('#error_mobile_no').text(error_mobile_no);
    $('#mobile_no').removeClass('has-error');
   }
  }
  if(error_address != '' || error_mobile_no != '')
  {
   return false;
  }
  else
  {
   $('#btn_contact_details').attr("disabled", "disabled");
   $(document).css('cursor', 'prgress');
   $("#register_form").submit();
  }
  */
 });

  $('#previous_btn_experience_details').click(function(){
  $('#list_experience_details').removeClass('active active_tab1');
  $('#list_experience_details').removeAttr('href data-toggle');
  $('#experience_details').removeClass('active in');
  $('#list_experience_details').addClass('inactive_tab1');
  $('#list_contact_details').removeClass('inactive_tab1');
  $('#list_contact_details').addClass('active_tab1 active');
  $('#list_contact_details').attr('href', '#contact_details');
  $('#list_contact_details').attr('data-toggle', 'tab');
  $('#contact_details').addClass('active in');
 });

 $("#designation_nhm").hide();
 $('#duration_nhm').hide();
 $('#remuneration_nhm').hide();
 $('#remarks_nhm').hide();
 $('.experience_year_month_nhm_outer').hide();
$('#engaged_or_not_nhm').change(function () {
    if ($('option:selected', this).val() == 1) {
        //$('form').hide();
        $('#designation_nhm').show();
        $('#duration_nhm').show();
        $('#remuneration_nhm').show();
        $('#remarks_nhm').show();
        $('.experience_year_month_nhm_outer').show();

    } else {
       $('#designation_nhm').hide();
       $('#duration_nhm').hide();
       $('#remuneration_nhm').hide();
       $('#remarks_nhm').hide();
       $('.experience_year_month_nhm_outer').hide();
    }
});

 $("#designation_hfw").hide();
 $('#duration_hfw').hide();
 $('#remuneration_hfw').hide();
 $('#remarks_hfw').hide();
 $('.experience_year_month_hfw_outer').hide();
$('#engaged_or_not_hfw').change(function () {
    if ($('option:selected', this).val() == 1) {
        //$('form').hide();
        $('#designation_hfw').show();
        $('#duration_hfw').show();
        $('#remuneration_hfw').show();
        $('#remarks_hfw').show();
        $('.experience_year_month_hfw_outer').show();
    } else {
       $('#designation_hfw').hide();
       $('#duration_hfw').hide();
       $('#remuneration_hfw').hide();
       $('#remarks_hfw').hide();
       $('.experience_year_month_hfw_outer').hide();
    }
});

 posting_places=0;
$('#posting_place_div').hide();
$('#posting_level').change(function () {
    if ($('option:selected', this).val() == "SPMU" || $('option:selected', this).val() =="DPMU" ||$('option:selected', this).val() == "CPMU" || $('option:selected', this).val() == "BPMU" || $('option:selected', this).val() == "State Drug Store" || $('option:selected', this).val() == "State Institute of Health and Family Welfare" ) {
        //$('form').hide();
        $('#posting_place_div').hide();
        posting_places=0;
    } else 
    {
       $('#posting_place_div').show();
       posting_places=1;
    }
});

// if($('#posting_level').val()=="SPMU" || $('#posting_level').val()=="DPMU" || $('#posting_level').val()=="CPMU" || $('#posting_level').val()=="BPMU")
// {
//   $('#posting_place_div').hide();
// }
// else
// {
//   $('#posting_place_div').show();
// }


/***************************SD*********************************/
$('#btn_submit_preview').click(function(){
 
 var error_engaged_or_not_nhm= '';
 var error_engaged_or_not_hfw= '';
 var error_appointing_authority ='';
 var error_contractual_employment_under ='';
 var error_service_category = '';
 var error_date_of_joining ='';
 var error_con_rem_time_joining='';
 var error_date_of_joining_in_posting='';
 var error_monthly_rem='';
 var error_casual_leave_availed='';
 var error_earned_leave_availed='';
 var error_contractual_under_nhm='';
 var error_programme_head='';
 var error_designation_list='';
 //var error_con_monthly_salary_joining='';
 var error_monthly_rem='';
 var error_posting_place='';

 var error_e_designation_nhm='';
 var error_e_duration_nhm='';
 var error_e_remuneration_nhm='';
 var error_e_remarks_nhm='';


 var error_e_designation_hfw='';
 var error_e_duration_hfw='';
 var error_e_remuneration_hfw='';
 var error_e_remarks_hfw='';
 // var error_e_duration_to_nhm='';
 //var error_e_remuneration_nhm='';
 var error_engaged_or_not_hfw='';
 //var error_designation_hfw='';
 //var error_e_duration_from_hfw='';
// var error_e_duration_to_hfw='';
 //var error_e_remuneration_hfw='';


 advertisement_number_val='';
check_val_nhm_digit='';
check_val_hfw_digit='';
e_designation_nhm_val='';
e_duration_from_nhm_val='';
e_duration_to_nhm_val='';
e_remuneration_nhm_val='';

 var check_val_engaged_or_not_nhm=$('#engaged_or_not_nhm').val();
 if(check_val_engaged_or_not_nhm==1){
  check_val_nhm="Yes";
  check_val_nhm_digit=1;
 }
 else{
  check_val_nhm="No";
  check_val_nhm_digit=0;
 }
 
 var check_val_engaged_or_not_hfw=$('#engaged_or_not_hfw').val();
 if(check_val_engaged_or_not_hfw==1){
  check_val_hfw="Yes";
  check_val_hfw_digit=1;
 }
 else{
  check_val_hfw="No";
  check_val_hfw_digit=0;
 }

var check_val_service_category_s=$('#service_category').val();
 if(check_val_service_category_s==1){
  check_val_service_category="Service Delivery";
 }
 else{
  check_val_service_category="Programme Management";
 }

 var check_val_mph_s=$('#contractual_under_nhm').val();
 if(check_val_mph_s==1){
  check_val_mph="HSS";
 }
 else if(check_val_mph_s==2){
  check_val_mph="RMNCH+A";
 }
 else if(check_val_mph_s==3){
  check_val_mph="CD";
 }
 else if(check_val_mph_s==4){
  check_val_mph="NCD";
 }
 else(check_val_mph_s==5)
  check_val_mph="NUHM";
 

 if($.trim($('#advertisement_number').val()).length == 0)
  {
    advertisement_number_val1=$('#advertisement_number').val();
    //console.log(advertisement_number_val1); 
     advertisement_number_val=0;
  }else{
    advertisement_number_val=1;
  }
 

if(check_val_engaged_or_not_nhm==1){
  
  if($.trim($('#e_designation_nhm').val()).length == 0)
  {
   error_e_designation_nhm = 'Field is required';
   $('#error_e_designation_nhm').text(error_e_designation_nhm);
   $('#e_designation_nhm').addClass('has-error');
  }
  else
  {
   error_e_designation_nhm = '';
   $('#error_e_designation_nhm').text(error_e_designation_nhm);
   $('#e_designation_nhm').removeClass('has-error');
  }

  if($.trim($('#e_duration_from_nhm').val()).length == 0)
  {
   error_e_duration_from_nhm = 'Field is required';
   $('#error_e_duration_from_nhm').text(error_e_duration_from_nhm);
   $('#e_duration_from_nhm').addClass('has-error');
  }
  else
  {
   error_e_duration_from_nhm = '';
   $('#error_e_duration_from_nhm').text(error_e_duration_from_nhm);
   $('#e_duration_from_nhm').removeClass('has-error');
  }

  if($.trim($('#e_duration_to_nhm').val()).length == 0)
  {
   error_e_duration_to_nhm = 'Field is required';
   $('#error_e_duration_to_nhm').text(error_e_duration_from_nhm);
   $('#e_duration_to_nhm').addClass('has-error');
  }
  else
  {
   error_e_duration_to_nhm = '';
   $('#error_e_duration_to_nhm').text(error_e_duration_to_nhm);
   $('#e_duration_to_nhm').removeClass('has-error');
  }

   if($.trim($('#e_remuneration_nhm').val()).length == 0)
  {
   error_e_remuneration_nhm = 'Field is required';
   $('#error_e_remuneration_nhm').text(error_e_remuneration_nhm);
   $('#e_remuneration_nhm').addClass('has-error');
  }
  else
  {
   error_e_remuneration_nhm = '';
   $('#error_e_remuneration_nhm').text(error_e_remuneration_nhm);
   $('#e_remuneration_nhm').removeClass('has-error');
  }

  if($.trim($('#e_remarks_nhm').val()).length == 0)
  {
   error_e_remarks_nhm = 'Field is required';
   $('#error_e_remarks_nhm').text(error_e_remarks_nhm);
   $('#e_remarks_nhm').addClass('has-error');
  }
  else
  {
   error_e_remarks_nhm = '';
   $('#error_e_remarks_nhm').text(error_e_remarks_nhm);
   $('#e_remarks_nhm').removeClass('has-error');
  }
}




if(check_val_engaged_or_not_hfw==1){
  
  if($.trim($('#e_designation_hfw').val()).length == 0)
  {
   error_e_designation_hfw = 'Field is required';
   $('#error_e_designation_hfw').text(error_e_designation_hfw);
   $('#e_designation_hfw').addClass('has-error');
  }
  else
  {
   error_e_designation_hfw = '';
   $('#error_e_designation_hfw').text(error_e_designation_hfw);
   $('#e_designation_hfw').removeClass('has-error');
  }

  if($.trim($('#e_duration_from_hfw').val()).length == 0)
  {
   error_e_duration_from_hfw = 'Field is required';
   $('#error_e_duration_from_hfw').text(error_e_duration_from_hfw);
   $('#e_duration_from_hfw').addClass('has-error');
  }
  else
  {
   error_e_duration_from_hfw = '';
   $('#error_e_duration_from_hfw').text(error_e_duration_from_hfw);
   $('#e_duration_from_hfw').removeClass('has-error');
  }

  if($.trim($('#e_duration_to_hfw').val()).length == 0)
  {
   error_e_duration_to_hfw = 'Field is required';
   $('#error_e_duration_to_hfw').text(error_e_duration_from_hfw);
   $('#e_duration_to_hfw').addClass('has-error');
  }
  else
  {
   error_e_duration_to_hfw = '';
   $('#error_e_duration_to_hfw').text(error_e_duration_to_hfw);
   $('#e_duration_to_hfw').removeClass('has-error');
  }

   if($.trim($('#e_remuneration_hfw').val()).length == 0)
  {
   error_e_remuneration_hfw = 'Field is required';
   $('#error_e_remuneration_hfw').text(error_e_remuneration_hfw);
   $('#e_remuneration_hfw').addClass('has-error');
  }
  else
  {
   error_e_remuneration_hfw = '';
   $('#error_e_remuneration_hfw').text(error_e_remuneration_hfw);
   $('#e_remuneration_hfw').removeClass('has-error');
  }

  if($.trim($('#e_remarks_hfw').val()).length == 0)
  {
   error_e_remarks_hfw = 'Field is required';
   $('#error_e_remarks_hfw').text(error_e_remarks_hfw);
   $('#e_remarks_hfw').addClass('has-error');
  }
  else
  {
   error_e_remarks_hfw = '';
   $('#error_e_remarks_hfw').text(error_e_remarks_hfw);
   $('#e_remarks_hfw').removeClass('has-error');
  }
}












 if($.trim($('#engaged_or_not_nhm').val()).length == 0)
  {
   error_engaged_or_not_nhm = 'Field is required';
   $('#error_engaged_or_not_nhm').text(error_engaged_or_not_nhm);
   $('#engaged_or_not_nhm').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_engaged_or_not_nhm = '';
   $('#error_engaged_or_not_nhm').text(error_engaged_or_not_nhm);
   $('#engaged_or_not_nhm').next().find('.select2-selection').removeClass('has-error');
  }

  if($.trim($('#engaged_or_not_hfw').val()).length == 0)
  {
   error_engaged_or_not_hfw = 'Field is required';
   $('#error_engaged_or_not_hfw').text(error_engaged_or_not_hfw);
   $('#engaged_or_not_hfw').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_engaged_or_not_hfw = '';
   $('#error_engaged_or_not_hfw').text(error_engaged_or_not_hfw);
   $('#engaged_or_not_hfw').next().find('.select2-selection').removeClass('has-error');
  }

//   if(check_val_nhm_digit==1){

//       if($.trim($('#designation_nhm').val()).length == 0)
//     {
//      error_appointing_authority = 'Field is required';
//      $('#error_designation_nhm').text(error_designation_nhm);
//      $('#designation_nhm').addClass('has-error');
//     }
//     else
//     {
//      error_appointing_authority = '';
//      $('#error_designation_nhm').text(error_designation_nhm);
//      $('#designation_nhm').removeClass('has-error');
//     }


//       if($.trim($('#duration_nhm').val()).length == 0)
//     {
//      error_duration_nhm = 'Field is required';
//      $('#error_duration_nhm').text(error_duration_nhm);
//      $('#duration_nhm').addClass('has-error');
//     }
//     else
//     {
//      error_duration_nhm = '';
//      $('#error_duration_nhm').text(error_duration_nhm);
//      $('#duration_nhm').removeClass('has-error');
//     }
// sssssssssssssssssssssss

//   }






  //error_appointing_authority
  if($.trim($('#appointing_authority').val()).length == 0)
  {
   error_appointing_authority = 'Field is required';
   $('#error_appointing_authority').text(error_appointing_authority);
   $('#appointing_authority').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_appointing_authority = '';
   $('#error_appointing_authority').text(error_appointing_authority);
   $('#appointing_authority').next().find('.select2-selection').removeClass('has-error');
  }

  if($.trim($('#contractual_employment_under').val()).length == 0)
  {
   error_contractual_employment_under = 'Field is required';
   $('#error_contractual_employment_under').text(error_contractual_employment_under);
   $('#contractual_employment_under').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_contractual_employment_under = '';
   $('#error_contractual_employment_under').text(error_appointing_authority);
   $('#contractual_employment_under').next().find('.select2-selection').removeClass('has-error');
  }

  if($.trim($('#service_category').val()).length == 0)
  {
   error_service_category = 'Field is required';
   $('#error_service_category').text(error_service_category);
   $('#service_category').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_service_category = '';
   $('#error_service_category').text(error_service_category);
   $('#service_category').next().find('.select2-selection').removeClass('has-error');
  }

  if($.trim($('#contractual_under_nhm').val()).length == 0)
  {
   error_contractual_under_nhm = 'Field is required';
   $('#error_contractual_under_nhm').text(error_contractual_under_nhm);
   $('#contractual_under_nhm').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_contractual_under_nhm = '';
   $('#error_contractual_under_nhm').text(error_contractual_under_nhm);
   $('#contractual_under_nhm').next().find('.select2-selection').removeClass('has-error');
  }

   if($.trim($('#programme_head').val()).length == 0)
  {
   error_programme_head = 'Field is required';
   $('#error_programme_head').text(error_programme_head);
   $('#programme_head').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_programme_head = '';
   $('#error_programme_head').text(error_programme_head);
   $('#programme_head').next().find('.select2-selection').removeClass('has-error');
  }

   if($.trim($('#designation_list').val()).length == 0)
  {
   error_designation_list = 'Field is required';
   $('#error_designation_list').text(error_designation_list);
   $('#designation_list').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_designation_list = '';
   $('#error_designation_list').text(error_designation_list);
   $('#designation_list').next().find('.select2-selection').removeClass('has-error');
  }

if($.trim($('#e_designation_nhm').val()).length == 0)
  {
 
  e_designation_nhm_val=0;
  }
  else
  {
   e_designation_nhm_val=1;
  }
if($.trim($('#e_duration_from_nhm').val()).length == 0)
  {
 
  e_duration_from_nhm_val=0;
  }
  else
  {
   e_duration_from_nhm_val=1;
  }
if($.trim($('#e_duration_to_nhm').val()).length == 0)
  {
 
  e_duration_to_nhm_val=0;
  }
  else
  {
   e_duration_to_nhm_val=1;
  }

if($.trim($('#e_remuneration_nhm').val()).length == 0)
  {
 
  e_remuneration_nhm_val=0;
  }
  else
  {
   e_remuneration_nhm_val=1;
  }



if($.trim($('#e_designation_hfw').val()).length == 0)
  {
 
  e_designation_hfw_val=0;
  }
  else
  {
   e_designation_hfw_val=1;
  }
if($.trim($('#e_duration_from_hfw').val()).length == 0)
  {
 
  e_duration_from_hfw_val=0;
  }
  else
  {
   e_duration_from_hfw_val=1;
  }
if($.trim($('#e_duration_to_hfw').val()).length == 0)
  {
 
  e_duration_to_hfw_val=0;
  }
  else
  {
   e_duration_to_hfw_val=1;
  }

if($.trim($('#e_remuneration_hfw').val()).length == 0)
  {
 
  e_remuneration_hfw_val=0;
  }
  else
  {
   e_remuneration_hfw_val=1;
  }







  if($.trim($('#date_of_joining').val()).length == 0)
  {
   error_date_of_joining = 'Field is required';
   $('#error_date_of_joining').text(error_date_of_joining);
   $('#date_of_joining').addClass('has-error');
  }
  else
  {
   error_date_of_joining = '';
   $('#error_date_of_joining').text(error_date_of_joining);
   $('#date_of_joining').removeClass('has-error');
  }

  if($.trim($('#con_rem_time_joining').val()).length == 0)
  {
   error_con_rem_time_joining = 'Field is required';
   $('#error_con_rem_time_joining').text(error_con_rem_time_joining);
   $('#con_rem_time_joining').addClass('has-error');
  }
  else
  {
   error_con_rem_time_joining= '';
   $('#error_con_rem_time_joining').text(error_con_rem_time_joining);
   $('#con_rem_time_joining').removeClass('has-error');
  }

  // if($.trim($('#con_monthly_salary_joining').val()).length == 0)
  // {
  //  error_con_monthly_salary_joining = 'Field is required';
  //  $('#error_con_monthly_salary_joining').text(error_con_monthly_salary_joining);
  //  $('#con_monthly_salary_joining').addClass('has-error');
  // }
  // else
  // {
  //  error_con_monthly_salary_joining= '';
  //  $('#error_con_monthly_salary_joining').text(error_con_monthly_salary_joining);
  //  $('#con_monthly_salary_joining').removeClass('has-error');
  // }


  if($.trim($('#date_of_joining_in_posting').val()).length == 0)
  {
   error_date_of_joining_in_posting = 'Field is required';
   $('#error_date_of_joining_in_posting').text(error_date_of_joining_in_posting);
   $('#date_of_joining_in_posting').addClass('has-error');
  }
  else
  {
   error_date_of_joining_in_posting = '';
   $('#error_date_of_joining_in_posting').text(error_date_of_joining_in_posting);
   $('#date_of_joining_in_posting').removeClass('has-error');
  }

  if($.trim($('#monthly_rem').val()).length == 0)
  {
   error_monthly_rem = 'Field is required';
   $('#error_monthly_rem').text(error_monthly_rem);
   $('#monthly_rem').addClass('has-error');
  }
  else
  {
   error_monthly_rem = '';
   $('#error_monthly_rem').text(error_monthly_rem);
   $('#monthly_rem').removeClass('has-error');
  }

   if($.trim($('#posting_level').val()).length == 0)
  {
   error_posting_level = 'Field is required';
   $('#error_posting_level').text(error_posting_level);
   $('#posting_level').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_posting_level = '';
   $('#error_posting_level').text(error_posting_level);
  $('#posting_level').next().find('.select2-selection').removeClass('has-error');
  }

  if(posting_places==1){
   
   if($.trim($('#posting_place').val()).length == 0)
  
  {
   error_posting_place = 'Field is required';
   $('#error_posting_place').text(error_posting_place);
  $('#posting_place').next().find('.select2-selection').addClass('has-error');
  }
  else
  {
   error_posting_place = '';
   $('#error_posting_place').text(error_posting_place);
  $('#posting_place').next().find('.select2-selection').removeClass('has-error');
  }
}

  // if($.trim($('#casual_leave_availed').val()).length == 0)
  // {
  //  error_casual_leave_availed = 'Field is required';
  //  $('#error_casual_leave_availed').text(error_casual_leave_availed);
  //  $('#casual_leave_availed').addClass('has-error');
  // }
  // else
  // {
  //  error_casual_leave_availed = '';
  //  $('#error_casual_leave_availed').text(error_casual_leave_availed);
  //  $('#casual_leave_availed').removeClass('has-error');
  // }

  // if($.trim($('#earned_leave_availed').val()).length == 0)
  // {
  //  error_earned_leave_availed = 'Field is required';
  //  $('#error_earned_leave_availed').text(error_earned_leave_availed);
  //  $('#earned_leave_availed').addClass('has-error');
  // }
  // else
  // {
  //  error_earned_leave_availed = '';
  //  $('#error_earned_leave_availed').text(error_service_category);
  //  $('#earned_leave_availed').removeClass('has-error');
  // }
 


 if(error_engaged_or_not_nhm != '' || error_engaged_or_not_hfw !='' || error_appointing_authority !='' || error_contractual_employment_under !='' || error_service_category !='' || error_contractual_under_nhm !='' || error_programme_head !='' || error_designation_list !='' || error_date_of_joining !='' || error_con_rem_time_joining !='' || error_date_of_joining_in_posting !='' || error_monthly_rem !=''  ||error_posting_level!=''|| error_posting_place !=''|| error_e_designation_nhm !='' ||error_e_duration_nhm !='' || error_e_remuneration_nhm!='' ||error_e_remarks_nhm !='' ||  error_e_designation_hfw!='' ||error_e_duration_hfw !='' || error_e_remuneration_hfw !='' || error_e_remarks_hfw!='' )
  {
   return false;
  }
  else
  {

  }

});

$('#btn_submit_preview').click(function() { 



  $('#lname').text($('#lastname').val());
  $('#name_modal').text($('#title').val()+' '+$('#first_name').val()+' '+$('#middle_name').val()+' '+$('#last_name').val());
     /* when the button in the form, display the entered values in the modal */
     // $('#title_modal').text($('#title').val());
     // $('#first_name_modal').text($('#first_name').val());
     // $('#middle_name_modal').text($('#middle_name').val());
     // $('#last_name_modal').text($('#last_name').val());
     $('#guardian_relation_modal').text($('#guardian_relation').val());
     $('#guardian_name_modal').text($('#guardian_name').val());
     $('#dob_modal').text($('#dob').val());
     $('#gender_modal').text($('#gender').val());
     $('#caste_category_modal').text($('#caste_category').val());
     $('#pwd_modal').text($('#pwd').val());
     $('#marital_status_modal').text($('#marital_status').val());
     $('#mobile_number_1_modal').text($('#mobile_number_1').val());
     //$('#mobile_number_2_modal').text($('#mobile_number_2').val());
     
    if(mobile_2_val !=1){
      $('#mobile_number_2_modal').text("Nil");
     }else{
       $('#mobile_number_2_modal').text($('#mobile_number_2').val());
     }

     $('#email_modal').text($('#email').val());
     //$('#identification_mark_modal').text($('#identification_mark').val());

     if(identification_mark_val !=1) {
      $('#identification_mark_modal').text("Nil");
     }else{
       $('#identification_mark_modal').text($('#identification_mark').val());
     }



     $('#blood_group_modal').text($('#blood_group').val());
     $('#person_name_emergency_modal').text($('#person_name_emergency').val());
     $('#person_emergency_mobile_modal').text($('#person_emergency_mobile').val());
     $('#present_address_modal').text($('#present_address_line1').val());
     $('#present_address_district_modal').text($('#present_address_district option:selected').text());
     //permanent_address_district_modal
     //$('#present_address_line2_modal').text($('#present_address_line2').val());
     $('#present_address_police_station_modal').text($('#present_address_police_station').val());
     $('#present_address_pincode_modal').text($('#present_address_pincode').val());

     if(other_district_present_address==1){
       $('#other_district_present_address_modal').text($('#other_district_present_address').val());
       }
    else
      {
         $('#other_district_present_address_modal').text("Nil");
       }
      

     $('#permanent_address_modal').text($('#permanent_address_line1').val());
     $('#permanent_address_district_modal').text($('#permanent_address_district option:selected').text());
    // $('#permanent_address_line2_modal').text($('#permanent_address_line2').val());
     $('#permanent_address_police_station_modal').text($('#permanent_address_police_station').val());
     $('#permanent_address_pincode_modal').text($('#permanent_address_pincode').val());
     

      if(other_district_permanent_address==1){
       $('#other_district_permanent_address_modal').text($('#other_district_permanent_address').val());
       }
    else
      {
         $('#other_district_permanent_address_modal').text("Nil");
       }

     $('#highest_education_modal').text($('#highest_education').val());
     
     if(technical_qualification_vals!=1){
     //$('#technical_qualification_modal').text($('#technical_qualification').val());
      $('#technical_qualification_modal').text("Nil");
     }
     else{
      $('#technical_qualification_modal').text($('#technical_qualification').val());
     }


     if(professional_qualification_val!=1){
     //$('#technical_qualification_modal').text($('#technical_qualification').val());
      $('#professional_qualification_modal').text("Nil");
     }
     else{
      $('#professional_qualification_modal').text($('#professional_qualification').val());
     }

     if(prof_other==0){
     //$('#technical_qualification_modal').text($('#technical_qualification').val());
      $('#other_professional_qualification_modal').text("Nil");
     }
     else{
      $('#other_professional_qualification_modal').text($('#other_professional_qualification').val());
     }


     if(registration_val!=1){
     //$('#technical_qualification_modal').text($('#technical_qualification').val());
      $('#registration_modal').text("Nil");
     }
     else{
      $('#registration_modal').text($('#registration').val());
     }


    
     //$('#professional_qualification_modal').text($('#professional_qualification').val());
     //$('#registration_modal').text($('#registration').val());
     $('#pan_modal').text($('#pan').val());
     $('#bank_account_number_modal').text($('#bank_account_number').val());
     $('#name_of_bank_modal').text($('#name_of_bank').val());
     $('#bank_branch_modal').text($('#bank_branch').val());
     $('#bank_ifsc_code_modal').text($('#bank_ifsc_code').val());
     $('#is_uan_present_modal').text($('#is_uan_present').val());

     if(is_uan_present_val==1){
     $('#uin_number_modal').text($('#uin_number').val());
     }else
     {
       $('#uin_number_modal').text('Nil');
     }
     //$('#engaged_or_not_nhm_modal').text($('#engaged_or_not_nhm').val());
     $('#engaged_or_not_nhm_modal').text('' +check_val_nhm);

      if(check_val_nhm_digit==1){           
        if(e_designation_nhm_val==1){
              $('#designation_nhm_modal').text($('#e_designation_nhm').val());
           }
        else{
           $('#designation_nhm_modal').text("Nil");
        }

        
      if(e_duration_from_nhm_val==1 && e_duration_to_nhm_val==1){
             $('#e_duration_nhm_modal').text($('#e_duration_from_nhm').val()+' To '+$('#e_duration_to_nhm').val());
              //$('#e_duration_to_nhm_modal').text($('#e_duration_to_nhm').val());
            }
            else if(e_duration_from_nhm_val==1){
               $('#e_duration_nhm_modal').text($('#e_duration_from_nhm').val()+' To Nil');
            }
            else if(e_duration_to_nhm_val==1){
               $('#e_duration_nhm_modal').text('Nil To '+ $('#e_duration_to_nhm').val());
            }else if(e_duration_from_nhm_val==0 && e_duration_to_nhm_val==0){
              $('#e_duration_nhm_modal').text("Nil");
            }


      if(e_remuneration_nhm_val==1){
               $('#e_remuneration_nhm_modal').text($('#e_remuneration_nhm').val());
         }
         else{
               $('#e_remuneration_nhm_modal').text("Nil");
          }

       $('#e_remarks_nhm_modal').text($('#e_remarks_nhm').val());
       $('#experience_year_month_nhm_modal').text($('#experience_year_month_nhm').val());

    }
    else{

     $('#designation_nhm_modal').text("Nil");
     $('#e_duration_nhm_modal').text("Nil");
     //$('#e_duration_to_nhm_modal').text($('#e_duration_to_nhm').val());
     $('#e_remuneration_nhm_modal').text("Nil");
     $('#e_remarks_nhm_modal').text("Nil");
      $('#experience_year_month_nhm_modal').text("Nil");
     }
     // $('#engaged_or_not_hfw_modal').text($('#engaged_or_not_hfw').val());
     $('#engaged_or_not_hfw_modal').text(''+check_val_hfw);

     if(check_val_hfw_digit==1){
         
         if(e_designation_hfw_val==1){
               $('#designation_hfw_modal').text($('#e_designation_hfw').val());
           }
        else{
           $('#designation_hfw_modal').text("Nil");
        }



      if(e_duration_from_hfw_val==1 && e_duration_to_hfw_val==1){
             $('#e_duration_hfw_modal').text($('#e_duration_from_hfw').val()+' To '+$('#e_duration_to_hfw').val());
              //$('#e_duration_to_nhm_modal').text($('#e_duration_to_nhm').val());
        }
      else if(e_duration_from_hfw_val==1){
               $('#e_duration_hfw_modal').text($('#e_duration_from_hfw').val()+' To Nil');
         }
      else if(e_duration_to_hfw_val==1){
            $('#e_duration_hfw_modal').text('Nil To '+ $('#e_duration_to_hfw').val());
         }
      else if(e_duration_from_hfw_val==0 && e_duration_to_hfw_val==0){
              $('#e_duration_hfw_modal').text("Nil");
            }



      if(e_remuneration_hfw_val==1){
                $('#e_remuneration_hfw_modal').text($('#e_remuneration_hfw').val());
      }
      else{
               $('#e_remuneration_hfw_modal').text("Nil");
       }

        $('#e_remarks_hfw_modal').text($('#e_remarks_hfw').val());
        $('#experience_year_month_hfw_modal').text($('#experience_year_month_hfw').val());
     //$('#designation_hfw_modal').text($('#e_designation_hfw').val());
     //$('#e_duration_hfw_modal').text($('#e_duration_from_hfw').val()+' To '+$('#e_duration_to_hfw').val());
     //$('#e_duration_to_hfw_modal').text($('#e_duration_to_hfw').val());
    // $('#e_remuneration_hfw_modal').text($('#e_remuneration_hfw').val());
    }
    else
    {
     $('#designation_hfw_modal').text("Nil");
     $('#e_duration_hfw_modal').text("Nil");
     //$('#e_duration_to_hfw_modal').text($('#e_duration_to_hfw').val());
     $('#e_remuneration_hfw_modal').text("Nil");
    $('#e_remarks_hfw_modal').text("Nil");
    $('#experience_year_month_hfw_modal').text("Nil");
    }

    if(advertisement_number_val!=1){
      $('#advertisement_number_modal').text("Nil");
     }
     else{
      $('#advertisement_number_modal').text($('#advertisement_number').val());
     }

    // $('#advertisement_number_modal').text($('#advertisement_number').val());
     $('#appointing_authority_modal').text($('#appointing_authority').val());
     $('#contractual_employment_under_modal').text($('#contractual_employment_under').val());
     //$('#service_category_modal').text($('#service_category').val());
     $('#service_category_modal').text(''+check_val_service_category);
     //$('#contractual_under_nhm_modal').text($('#contractual_under_nhm').val());
     //$('#contractual_under_nhm_modal').text(''+check_val_mph);

      // $selectedval=$('#contractual_under_nhm option:selected', this).val();
      // $('#contractual_under_nhm_modal').text(''+selectedval);
  $('#contractual_under_nhm_modal').text($('#contractual_under_nhm').find('option:selected').text());
     //$('#programme_head_modal').text($('#programme_head').val());
     //$('#programme_head_modal').text($('#programme_head:selected').text());
     //$("#yourdropdownid option:selected").text();
     $('#programme_head_modal').text($('#programme_head').find('option:selected').text());
     //$('#designation_list_modal').text($('#designation_list:selected').text());
      $('#designation_list_modal').text($('#designation_list').find('option:selected').text());
     $('#date_of_joining_modal').text($('#date_of_joining').val());
     $('#con_rem_time_joining_modal').text($('#con_rem_time_joining').val());
    
     // $('#con_monthly_salary_joining_modal').text($('#con_monthly_salary_joining').val());
     $('#date_of_joining_in_posting_modal').text($('#date_of_joining_in_posting').val());
     $('#monthly_rem_modal').text($('#monthly_rem').val());
     $('#posting_level_modal').text($('#posting_level').val());

     if(posting_places!=0){
     $('#posting_place_modal').text($('#posting_place').find('option:selected').text());
      }
      else{
        $('#posting_place_modal').text($('#posting_level').val());
      }


     $('#casual_leave_availed_modal').text($('#casual_leave_availed').val());
     $('#earned_leave_availed_modal').text($('#earned_leave_availed').val());

    
});
/***************************************************************/
});
</script>
</body>
</html>
