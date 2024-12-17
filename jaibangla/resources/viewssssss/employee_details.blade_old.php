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
              <h3 class="box-title">Case Details</h3>
            </div>

            <div>
             @if ($message = Session::get('success'))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
              </div>
              @endif
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
                 <label>Title</label>
                 <select class="form-control select2" name="ps_id" >
                    <option value="">--Select--</option>
                    <option value="Mr">Mr</option>
                    <option value="Mrs">Mrs</option>
                    <option value="Ms">Ms</option>                   
                    <option value="Md">Md</option>                   
                    <option value="Sk">Sk</option>                   
                    <option value="Syed">Syed</option>                   
                  </select>
                 <span id="error_email" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>First Name</label>
                 <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name" />
                 <span id="error_first_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Middle Name</label>
                 <input type="text" name="middle_name" id="middle_name" class="form-control"  placeholder="Middle Name" />
                 <span id="error_middle_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Last Name</label>
                 <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name" />
                 <span id="error_last_name" class="text-danger"></span>
                </div>

                <div class="form-group col-md-3">
                 <label>Gurdian</label>
                 <select class="form-control select2" name="guardian_relation" >
                    <option value="">--Select--</option>
                    <option value="Father">Father</option>
                    <option value="Mother">Mother</option>
                    <option value="Spouse">Spouse</option>                                       
                  </select>
                 <span id="error_guardian_relation" class="text-danger"></span>
                </div>
                <div class="form-group col-md-9">
                 <label>Name</label>
                 <input type="text" name="guardian_name" id="guardian_name" class="form-control" placeholder="Guadian Name" />
                 <span id="error_guardian_name" class="text-danger"></span>
                </div>

                <div class="form-group col-md-3">
                 <label>Date of Birth</label>
                 <input type="text" id="dob" name="dob"class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                 <span id="error_dob" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Gender</label>
                 <select class="form-control select2" name="gender" >
                    <option value="">--Select--</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>                                       
                  </select>
                 <span id="error_gender" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Caste Category</label>
                 <select class="form-control select2" name="caste_category" >
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
                 <label>Weather engagged under PWD</label>
                 <select class="form-control select2" name="pwd" >
                    <option value="">--Select--</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                  </select>
                 <span id="error_pwd" class="text-danger"></span>
                </div>

                <div class="form-group col-md-3">
                 <label>Marital Status</label>
                 <select class="form-control select2" name="marital_status" >
                    <option value="">--Select--</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                  </select>
                 <span id="error_marital_status" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Mobile Number 1</label>
                 <input type="text" name="mobile_number_1"id="mobile_number_1" class="form-control" placeholder="Mobile Number 1">
                 <span id="error_mobile_number_1" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Mobile Number 2</label>
                 <input type="text" id="mobile_number_2" name="mobile_number_2"class="form-control" placeholder="Mobile Number 2">
                 <span id="error_mobile_number_2" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Email Address</label>
                 <input type="text" id="email" name="email"class="form-control" placeholder="Email Address">
                 <span id="error_email" class="text-danger"></span>
                </div>

                <div class="form-group col-md-3">
                 <label>Identification Mark</label>
                 <input type="text" id="identification_mark" name="identification_mark" class="form-control" placeholder="Identification Mark">
                 <span id="error_identification_mark" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Blood Group</label>
                 <select class="form-control select2" name="blood_group" >
                    <option value="">--Select--</option>
                    <option value="A +ve">A +ve</option>
                    <option value="A -ve">A -ve</option>
                    <option value="B +ve">B +ve</option>
                    <option value="B -ve">B -ve</option>
                    <option value="O +ve">O +ve</option>
                    <option value="O -ve">O -ve</option>
                    <option value="AB +ve">AB +ve</option>
                    <option value="AB -ve">AB -ve</option>
                  </select>
                 <span id="error_blood_group" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Name of the person in case of Emergency</label>
                 <input type="text" id="person_name_emergency" name="person_name_emergency" class="form-control" placeholder="Name">
                 <span id="error_person_name_emergency" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Mobile No of the person in case of Emergency</label>
                 <input type="text" id="person_emergency_mobile" name="person_emergency_mobile" class="form-control" placeholder="Mobile No">
                 <span id="error_person_emergency_mobile" class="text-danger"></span>
                </div>

                <div class="form-group col-md-3">
                 <label>Present Address Line 1</label>
                 <input type="text" id="present_address_line1" name="present_address_line1" class="form-control" placeholder="Present Address Line 1">
                 <span id="error_present_address_line1" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Present Address Line 2</label>
                 <input type="text" id="present_address_line2" name="present_address_line2" class="form-control" placeholder="Present Address Line 2">
                 <span id="error_present_address_line2" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Police Station</label>
                 <input type="text" id="present_address_police_station" name="present_address_police_station"class="form-control" placeholder="Police Station">
                 <span id="error_present_address_police_station" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Pincode</label>
                 <input type="text" id="present_address_pincode" name="present_address_pincode" class="form-control" placeholder="Pincode">
                 <span id="error_present_address_pincode" class="text-danger"></span>
                </div>

                <div class="form-group col-md-3">
                 <label>Permanent Address Line 1</label>
                 <input type="text" id="permanent_address_line1" name="permanent_address_line1" class="form-control" placeholder="Permanent Address Line 1">
                 <span id="error_permanent_address_line1" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Permanent Address Line 2</label>
                 <input type="text" id="permanet_address_line2" name="permanet_address_line2" class="form-control" placeholder="Permanent Address Line 2">
                 <span id="error_permanet_address_line2" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Police Station</label>
                 <input type="text" id="permanent_address_poilce_station" name="permanent_address_poilce_station"class="form-control" placeholder="Police Station">
                 <span id="error_permanent_address_poilce_station" class="text-danger"></span>
                </div>
                <div class="form-group col-md-3">
                 <label>Pincode</label>
                 <input type="text" id="permanent_address_pincode" name="permanent_address_pincode" class="form-control" placeholder="Pincode">
                 <span id="error_permanent_address_pincode" class="text-danger"></span>
                </div>

                <br />
                <div align="center">
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
                <div class="form-group">
                 <label>Highest Educational Qualification</label>
                 <select class="form-control" name="highest_education" >
                    <option value="">--Select--</option>
                    <option value="Class VIII passed">Class VIII passed</option>
                    <option value="Below Class X">Below Class X</option>
                    <option value="Class X passed">Class X passed</option>
                    <option value="Class XII passed">Class XII passed</option>
                    <option value="Graduation">Graduation</option>
                    <option value="Post Graduation">Post Graduation</option>
                  </select>
                 <span id="error_highest_education" class="text-danger"></span>
                </div>
                <div class="form-group">
                 <label>Technical Qualification</label>
                 <input type="text" name="technical_qualification" id="technical_qualification" class="form-control" placeholder="Technical Qualification" />
                 <span id="error_technical_qualification" class="text-danger"></span>
                </div>
                <div class="form-group">
                 <label>If Professional qualification is MBBS/BDS/BHMS/BUMS/BAMS/Staff Nurse/Pharmacist, then Registration of respective council</label>
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
                 <label>PAN</label>
                 <input type="text" name="pan" id="pan" class="form-control" />
                 <span id="error_pan" class="text-danger"></span>
                </div>
                <div class="form-group">
                 <label>Bank Account Number (Salary Account)</label>
                 <input type="text" name="bank_account_number" id="bank_account_number" class="form-control" />
                 <span id="error_bank_account_number" class="text-danger"></span>
                </div>
                <div class="form-group">
                 <label>Name Of the Bank</label>
                 <select class="form-control" name="name_of_bank" >
                    <option value="">---------Select Bank---------</option>
                    <option value='Allahabad Bank'>Allahabad Bank</option>
                    <option value='Andhra Bank'>Andhra Bank</option>
                    <option value='Axis Bank'>Axis Bank</option>
                    <option value='Bank of Bahrain and Kuwait'>Bank of Bahrain and Kuwait</option>
                    <option value='Bank of Baroda - Corporate Banking'>Bank of Baroda - Corporate Banking</option>
                    <option value='Bank of Baroda - Retail Banking'>Bank of Baroda - Retail Banking</option>
                    <option value='Bank of India'>Bank of India</option>
                    <option value='Bank of Maharashtra'>Bank of Maharashtra</option>
                    <option value='Canara Bank'>Canara Bank</option>
                    <option value='Central Bank of India'>Central Bank of India</option>
                    <option value='City Union Bank'>City Union Bank</option>
                    <option value='Corporation Bank'>Corporation Bank</option>
                    <option value='Deutsche Bank'>Deutsche Bank</option>
                    <option value='Development Credit Bank'>Development Credit Bank</option>
                    <option value='Dhanlaxmi Bank'>Dhanlaxmi Bank</option>
                    <option value='Federal Bank'>Federal Bank</option>
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
                    <option value='Oriental Bank of Commerce'>Oriental Bank of Commerce</option>
                    <option value='Punjab National Bank - Corporate Banking'>Punjab National Bank - Corporate Banking</option>
                    <option value='Punjab National Bank - Retail Banking'>Punjab National Bank - Retail Banking</option>
                    <option value='Punjab & Sind Bank'>Punjab & Sind Bank</option>
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
                    <option value='UCO Bank'>UCO Bank</option>
                    <option value='Union Bank of India'>Union Bank of India</option>
                    <option value='United Bank of India'>United Bank of India</option>
                    <option value='Vijaya Bank'>Vijaya Bank</option>
                    <option value='Yes Bank Ltd'>Yes Bank Ltd</option>
                  </select>
                 <span id="error_name_of_bank" class="text-danger"></span>
                </div>
                <div class="form-group">
                 <label>Name of Bank Branch</label>
                 <input type="text" name="bank_branch" id="bank_branch" class="form-control" />
                 <span id="error_bank_branch" class="text-danger"></span>
                </div>
                <div class="form-group">
                 <label>IFSC Code of Salary Account</label>
                 <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control" />
                 <span id="error_bank_ifsc_code" class="text-danger"></span>
                </div>
                <br />

                <div align="center">
                 <button type="button" name="previous_btn_contact_details" id="previous_btn_contact_details" class="btn btn-default btn-lg">Previous</button>
                 <button type="button" name="btn_contact_details" id="btn_contact_details" class="btn btn-success btn-lg">Next</button>
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
                 <label>If engaged under NHM / NUHM at any level  (If it is different from present post)</label>
                 <select class="form-control" name="engaged_or_not_nhm" id="engaged_or_not_nhm">
                    <option value="">---------Select Option---------</option>
                    <option value='1'>Yes</option>
                    <option value='0'>No</option>                                                        
                  </select>
                 <span id="error_engaged_or_not_nhm" class="text-danger"></span>
                </div>
                

                <div class="form-group col-md-4" id="designation_nhm">
                 <label>Designation</label>
                 <input type="text" name="e_designation_nhm" id="e_designation_nhm" class="form-control" />
                 <span id="error_designation_nhm" class="text-danger"></span>                 
                </div>                
                
                 <div class="form-inline col-md-5" id="duration_nhm">
                 <label>Duration</label><br>
                    <div class="form-group ">
                        <input type="text" name="e_duration_from_nhm" id="e_duration_from_nhm" class="form-control" placeholder="From Date" />
                     </div>
                    <div class=" form-group ">
                        <input type="text" name="e_duration_to_Nhm" id="e_duration_to_nhm" class="form-control" placeholder="To Date" />
                    </div>
                 <span id="error_duration_nhm" class="text-danger"></span>                 
                </div>

                 <div class="form-group col-md-3" id="remuneration_nhm">
                 <label>Last Monthly Remuneration Drawn</label>
                 <input type="text" name="e_remuneration_nhm" id="e_remuneration_nhm" class="form-control" />
                 <span id="error_remuneration_nhm" class="text-danger"></span>                 
                </div>
                <br>

                <div class="form-group">
                 <label>If engaged in any project/programme/scheme under H & FW or any other Department of Government of West Bengal</label>
                 <select class="form-control" name="engaged_or_not_hfw" id="engaged_or_not_hfw">
                    <option value="">---------Select Option---------</option>
                    <option value='1'>Yes</option>
                    <option value='0'>No</option>                                                        
                  </select>
                 <span id="error_engaged_or_not_hfw" class="text-danger"></span>
                </div>

                <div class="form-group col-md-4" id="designation_hfw">
                 <label>Designation</label>
                 <input type="text" name="e_designation_hfw" id="e_designation_hfw" class="form-control" />
                 <span id="error_designation_hfw" class="text-danger"></span>                 
                </div>  

                <div class="form-inline col-md-5" id="duration_hfw">
                 <label>Duration</label><br>
                    <div class="form-group ">
                        <input type="text" name="e_duration_from_hfw" id="e_duration_from_hfw" class="form-control" placeholder="From Date" />
                     </div>
                    <div class=" form-group ">
                        <input type="text" name="e_duration_to_hfw" id="e_duration_to_hfw" class="form-control" placeholder="To Date" />
                    </div>
                 <span id="error_duration_hfw" class="text-danger"></span>                 
                </div>

                <div class="form-group col-md-3" id="remuneration_hfw">
                 <label>Last Monthly Remuneration Drawn</label>
                 <input type="text" name="e_remuneration_hfw" id="e_remuneration_hfw" class="form-control" />
                 <span id="error_remuneration_hfw" class="text-danger"></span>                 
                </div>
                <br>

                <div class="form-group">
                 <label style="font-size: medium;">Details of Engagement in present post under NHM/NUHM</label>                            
                </div>

                 <div class="form-group">
                 <label>Advertisement Number (Document Upload)</label>
                 <input type="text" name="advertisement_number" id="advertisement_number" class="form-control" />
                 <span id="error_advertisement_number" class="text-danger"></span>                 
                </div>

                 <div class="form-group col-md-4">
                 <label>Appointing Authority</label>
                  <select class="form-control" name="appointing_authority" id="appointing_authority">
                    <option value="">---------Select Option---------</option>
                    <option value='WBSH&FWS'>WBSH&FWS</option>
                    <option value='DH & FWS'>DH & FWS</option>
                    <option value='BH & FWS'>BH & FWS</option>
                    <option value='KMC'>KMC</option>
                    <option value='Others ULBS'>Others ULBS</option>                                                        
                  </select>
                  <span id="error_appointing_authority" class="text-danger"></span>                 
                 </div>

                 <div class="form-group col-md-4" >
                 <label>Contractual Employement Under</label>
                  <select class="form-control " name="contractual_employment_under" id="contractual_employment_under">
                    <option value="">---------Select Option---------</option>
                    <option value='NHM'>NHM</option>
                    <option value='NUHM'>NUHM</option>
                                                                         
                  </select>
                  <span id="error_contractual_employment_under" class="text-danger"></span>                 
                 </div>

                 <div class="form-group col-md-4" >
                 <label>Service Category</label>
                  <select class="form-control js-service_category" name="service_category" id="service_category">
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
                 <label>Contractual under NHM - Major Programme Head</label>
                  <select class="form-control js-major_programme_head " name="contractual_under_nhm" id="contractual_under_nhm">
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
                 <label>Programme Head </label>
                  <select class="form-control js-programme_head" name="programme_head" id="programme_head">
                    <option value="-1">---------Select Option---------</option>
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
                 <label>Designation List </label>
                  <select class="form-control js-designation_list" name="designation_list" id="designation_list">
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
                 <label>Date of joining  in present designation</label>
                 <input type="text" name="date_of_joining" id="date_of_joining" class="form-control" />
                 <span id="error_date_of_joining" class="text-danger"></span>                 
                </div> 

                 <div class="form-group col-md-4">
                 <label>Consolidated remuneration at the time of joining</label>
                 <input type="text" name="con_rem_time_joining" id="con_rem_time_joining" class="form-control" />
                 <span id="error_con_rem_time_joining" class="text-danger"></span>                 
                </div>

                 <div class="form-group col-md-4" >
                 <label>Date of joining  in present place of posting</label>
                 <input type="text" name="date_of_joining_in_posting" id="date_of_joining_in_posting" class="form-control" />
                 <span id="error_date_of_joining_in_posting" class="text-danger"></span>                 
                </div> 

                 <div class="form-group col-md-4">
                 <label>Monthly Remuneration as on 01.04.2019</label>
                 <input type="text" name="monthly_rem" id="monthly_rem" class="form-control" />
                 <span id="error_monthly_rem" class="text-danger"></span>                 
                </div>

                <br>

                  <div class="form-group col-md-12" >
                   <label style="font-size: medium;">Leave Status(Current Financial)</label>             
                  </div>
                  <div class="form-group col-md-4">
                   <label>Casual leave availed</label>
                   <input type="text" name="casual_leave_availed" id="casual_leave_availed"    class="form-control" />
                   <span id="error_casual_leave_availed" class="text-danger"></span>                 
                  </div>
                  <div class="form-group col-md-4">
                   <label>Earned leave availed</label>
                   <input type="text" name="earned_leave_availed" id="earned_leave_availed"    class="form-control" />
                   <span id="error_earned_leave_availed" class="text-danger"></span>                 
                  </div>

                <br>

                <div align="center" class="col-md-12">
                 <button type="button" name="previous_btn_experience_details" id="previous_btn_experience_details" class="btn btn-default btn-lg">Previous</button>
                <!--  <button type="button" name="btn_experience_details" id="btn_experience_details" class="btn btn-success btn-lg">Next</button> -->

                <input type="submit" class="btn btn-info" value="Submit Button">
                </div>
                


                <br />
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
      <!-- /.row -->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->

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


<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
<script>
$(document).ready(function(){

//$('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
 
 $('#btn_login_details').click(function(){

   $('#list_login_details').removeClass('active active_tab1');
   $('#list_login_details').removeAttr('href data-toggle');
   $('#login_details').removeClass('active');
   $('#list_login_details').addClass('inactive_tab1');
   $('#list_personal_details').removeClass('inactive_tab1');
   $('#list_personal_details').addClass('active_tab1 active');
   $('#list_personal_details').attr('href', '#personal_details');
   $('#list_personal_details').attr('data-toggle', 'tab');
   $('#personal_details').addClass('active in');
  
  /*
  var error_email = '';
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
  */
 });
 
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
 
 $('#btn_personal_details').click(function(){


  $('#list_personal_details').removeClass('active active_tab1');
  $('#list_personal_details').removeAttr('href data-toggle');
  $('#personal_details').removeClass('active');
  $('#list_personal_details').addClass('inactive_tab1');
  $('#list_contact_details').removeClass('inactive_tab1');
  $('#list_contact_details').addClass('active_tab1 active');
  $('#list_contact_details').attr('href', '#contact_details');
  $('#list_contact_details').attr('data-toggle', 'tab');
  $('#contact_details').addClass('active in');
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
 
 $('#btn_contact_details').click(function(){
  $('#list_contact_details').removeClass('active active_tab1');
  $('#list_contact_details').removeAttr('href data-toggle');
  $('#contact_details').removeClass('active');
  $('#list_contact_details').addClass('inactive_tab1');
  $('#list_experience_details').removeClass('inactive_tab1');
  $('#list_experience_details').addClass('active_tab1 active');
  $('#list_experience_details').attr('href', '#experience_details');
  $('#list_experience_details').attr('data-toggle', 'tab');
  $('#experience_details').addClass('active in');
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
$('#engaged_or_not_nhm').change(function () {
    if ($('option:selected', this).val() == 1) {
        //$('form').hide();
        $('#designation_nhm').show();
        $('#duration_nhm').show();
        $('#remuneration_nhm').show();
    } else {
       $('#designation_nhm').hide();
       $('#duration_nhm').hide();
       $('#remuneration_nhm').hide();
    }
});

 $("#designation_hfw").hide();
 $('#duration_hfw').hide();
 $('#remuneration_hfw').hide();
$('#engaged_or_not_hfw').change(function () {
    if ($('option:selected', this).val() == 1) {
        //$('form').hide();
        $('#designation_hfw').show();
        $('#duration_hfw').show();
        $('#remuneration_hfw').show();
    } else {
       $('#designation_hfw').hide();
       $('#duration_hfw').hide();
       $('#remuneration_hfw').hide();
    }
});



});
</script>
</body>
</html>
