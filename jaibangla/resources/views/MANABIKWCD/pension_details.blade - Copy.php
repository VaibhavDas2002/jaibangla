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
             <h3 class="box-title"><b>Government of West Bengal Jai Bangla Pension Scheme({{$scheme_name}})</b></h3>
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
            <form method="post" id="register_form" action="{{url('manabik-wcd')}}" enctype="multipart/form-data" class="submit-once" >
              {{ csrf_field() }}
                <input type="hidden" name="scheme_id" value="{{ $scheme_id }}">
                <input type="hidden" name="is_state_login" value="{{ $is_state_login }}">

              @if($is_state_login)
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Entry For</b></h4></div>
               <div class="panel-body">

                <div class="row">
                          
              
             

               <div class="form-group col-md-4">
                 <label class="required-field">District</label>
                 <select name="district_state" id="district_state" class="form-control" >
                  <option value="">--Select  --</option>
                   @foreach ($districts as $district)
                  <option value="{{$district->district_code}}"  @if(old('district')== $district->district_code)  selected  @endif> {{$district->district_name}}</option>
                  @endforeach
                </select>
                 <span id="error_district_state" class="text-danger"></span>

                </div>

            


            

              <div class="form-group col-md-4" id="divUrbanCode">
                <label class="required-field">Rural/ Urban</label>
                
                <select name="urban_code_state" id="urban_code_state" class="form-control" >
                  <option value="">--Select  --</option>
                  @foreach(Config::get('constants.rural_urban') as $key=>$val)
                  <option value="{{$key}}" @if( old('urban_code') == $key)  selected  @endif >{{$val}}</option>
                  @endforeach     
                   
                </select>
                  <span id="error_urban_code_state" class="text-danger"></span>
              </div>

            

                 

                <div class="form-group col-md-4" id="divBodyCode">
                <label class="required-field" id="blk_sub_txt">Block/SubDivision.</label>
                
                <select name="block_subdiv_state" id="block_subdiv_state" class="form-control" >
                  <option value="">--Select --</option>
                  
                   
                </select>
                  <span id="error_block_subdiv_state" class="text-danger"></span>
              </div>

        
             
               

               </div>
              
            

             
             
                 
               
               
               </div>
              </div>
              @endif
            <ul class="nav nav-tabs">

             
             <li class="nav-item">
              <a class="nav-link active_tab1" style="border:1px solid #ccc" id="list_personal_details"><b>Personal Details</b></a>
             </li>

              <li class="nav-item">
              <a class="nav-link inactive_tab1"  id="list_id_details" style="border:1px solid #ccc"><b>Personal Identification Number(S)</b></a>
             </li>

             <li class="nav-item">
              <a class="nav-link inactive_tab1" id="list_contact_details" style="border:1px solid #ccc"><b>Contact Details</b></a>
             </li>
             <li class="nav-item">
              <a class="nav-link inactive_tab1" id="list_bank_details" style="border:1px solid #ccc"><b>Bank Account Details</b></a>
             </li>
             <li class="nav-item">
              <a class="nav-link inactive_tab1" id="list_experience_details" style="border:1px solid #ccc"><b>Enclosure List (Self Attested)</b></a>
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
                <div class="form-group col-md-12">
                  <label class="required-field"><b>Application Type: </b></label>
                </div>
                @php
                  $tabindex=1;
                @endphp
                <div class="form-group col-md-4 ">
                  <select class="form-control " name="entry_type" id="entry_type"  tabindex="{{$tabindex}}">
                    <option value="Normal Form">Normal Form</option>
                    <option value="Form through Duare Sarkar camp">Form through Duare Sarkar camp</option>
                  </select>
                <span id="error_entry_type" class="text-danger"></span>

               @php
                  $tabindex=$tabindex+1;
                @endphp

                </div>
                <div class="form-group">
                <h3 class=""> For <b>Duare Sarkar</b> entry please select from dropdown <i><b>"Form through Duare Sarkar camp"</b></i></h3>
                </div>  
                <div class="row duareSarkar" style="display:none;">
               <div class="form-group col-md-4" >
                 <label class="required-field">Duare Sarkar Registration No.</label>
                 <input type="text" name="ds_registration_no" id="ds_registration_no" class="form-control" placeholder="Duare Sarkar Registration No." maxlength="100" value="{{ old('ds_registration_no') }}"  tabindex="{{$tabindex}}"  />
                 <span id="error_ds_registration_no" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>
                <div class="form-group col-md-4" >
                 <label class="required-field">Duare Sarkar Date</label>
                 <input type="date" name="ds_date" id="ds_date" class="form-control"  max="<?php echo date("Y-m-d"); ?>"   value="{{old('ds_date')}}"  tabindex="{{$tabindex}}"/>
                 <span id="error_ds_date" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>
                </div>

                <div class="form-group col-md-12">
                 <label class="">Beneficiary Name</label>
               
                </div>



                                 <!--    <div class="form-group col-md-12">
                                     <label class="required-field">Passport Photograph</label>
                                     <input type="file" name="signature_image" id="signature_image" class="form-control"  />

                                    <span id="error_signature_image" class="text-danger"></span>
                                    </div>  -->



                <div class="form-group col-md-4" >
                 <label class="required-field">First Name</label>
                 <input type="text" name="first_name" id="first_name" class="form-control txtOnly" placeholder="First Name" maxlength="200" value="{{ old('first_name') }}"  tabindex="{{$tabindex}}" />
                 <span id="error_first_name" class="text-danger"></span>
                @php
                  $tabindex=$tabindex+1;
                @endphp

                </div>
                <div class="form-group col-md-4">
                 <label>Middle Name</label>
                 <input type="text" name="middle_name" id="middle_name" class="form-control txtOnly"  placeholder="Middle Name" maxlength="100" value="{{ old('middle_name') }}"  tabindex="{{$tabindex}}" />
                 <span id="error_middle_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="required-field">Last Name</label>
                 <input type="text" name="last_name" id="last_name" class="form-control txtOnly" placeholder="Last Name" maxlength="200" value="{{ old('last_name') }}" tabindex="{{$tabindex}}" />
                 <span id="error_last_name" class="text-danger"></span>
                @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>

                <div class="form-group col-md-4">
                 <label class="required-field">Gender</label>
                 <select class="form-control " name="gender" id="gender"  tabindex="{{$tabindex}}">
                    <option value="">--Select--</option>
                    @foreach(Config::get('constants.gender') as $key=>$val)
                    <option value="{{$key}}" @if(old('gender')==$key)  selected  @endif>{{$val}}</option>
                    @endforeach                                     
                  </select>
                 <span id="error_gender" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>

                <div class="form-group col-md-4">
                 <label class="">Date of Birth</label>
                 <input type="date" name="dob" id="dob" class="form-control" tabindex="{{$tabindex}}" value="{{old('dob')}}" />
                 <!-- <input type="text" id="dob" name="dob"class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask placeholder="dd/mm/yyyy"> -->
                 <span id="error_dob" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>
                <div class="form-group col-md-4">
                 <label class="required-field">Age<span style=""> (as on 01/01/2020)</span></label>
                  <input type="hidden" name="hidden_age" id="hidden_age" val="">
                 <input type="text" name="txt_age" id="txt_age" class="form-control NumOnly" placeholder="Age"  value="{{ old('txt_age') }}"  maxlength="3" tabindex="{{$tabindex}}"  />
                 <span id="error_txt_age" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                 
                </div>



                
                <div class="form-group col-md-12">
                 <label class="">Father's Name</label>
               
                </div>
              
                <div class="form-group col-md-4">
                 <label class="required-field">First Name</label>
                 <input type="text" name="father_first_name" id="father_first_name" class="form-control txtOnly" placeholder="First Name" maxlength="200" value="{{ old('father_first_name') }}" tabindex="{{$tabindex}}" />
                 <span id="error_father_first_name" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>
                <div class="form-group col-md-4">
                 <label>Middle Name</label>
                 <input type="text" name="father_middle_name" id="father_middle_name" class="form-control txtOnly"  placeholder="Middle Name" maxlength="100" value="{{ old('father_middle_name') }}" tabindex="{{$tabindex}}" />
                 <span id="error_father_middle_name" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>
                <div class="form-group col-md-4">
                 <label class="required-field">Last Name</label>
                 <input type="text" name="father_last_name" id="father_last_name" class="form-control txtOnly" placeholder="Last Name" maxlength="200" value="{{ old('father_last_name') }}" tabindex="{{$tabindex}}" />
                 <span id="error_father_last_name" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>


                <div class="form-group col-md-12">
                 <label class="">Mother's Name</label>
               
                </div>
              
                <div class="form-group col-md-4">
                 <label class="required-field">First Name</label>
                 <input type="text" name="mother_first_name" id="mother_first_name" class="form-control txtOnly" placeholder="First Name" maxlength="200" value="{{ old('mother_first_name') }}" tabindex="{{$tabindex}}" />
                 <span id="error_mother_first_name" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>
                <div class="form-group col-md-4">
                 <label>Middle Name</label>
                 <input type="text" name="mother_middle_name" id="mother_middle_name" class="form-control txtOnly"  placeholder="Middle Name" maxlength="100" value="{{ old('mother_middle_name') }}" tabindex="{{$tabindex}}" />
                 <span id="error_mother_middle_name" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>
                <div class="form-group col-md-4">
                 <label class="required-field">Last Name</label>
                 <input type="text" name="mother_last_name" id="mother_last_name" class="form-control txtOnly" placeholder="Last Name" maxlength="200" value="{{ old('mother_last_name') }}" tabindex="{{$tabindex}}" />
                 <span id="error_mother_last_name" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>
                
                <div class="row">
                <div class="form-group col-md-4">
                 <label class="required-field">Caste</label>
                 <select class="form-control" name="caste_category" id="caste_category" tabindex="{{$tabindex}}">
                               
		@foreach(Config::get('constants.caste') as $key=>$val)
			<option value="{{$key}}" @if(old('caste_category')==$key)  selected  @endif>{{$val}}</option>
		@endforeach   
                                                                        
                  </select>
                 <span id="error_caste_category" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>
               </div>
      
                <div class="form-group col-md-4">
                 <label class="required-field">Type of Disability</label>
                 <select class="form-control" name="disablity_type" id="disablity_type" tabindex="{{$tabindex}}">
               

                 <option value="">--Select--</option>
                    @foreach(Config::get('constants.disablity_type') as $key=>$val)
                    
                    <option value="{{$key}}" @if(old('disablity_type') == $key)  selected  @endif >{{$val}}</option>
                    @endforeach

                                                                        
                  </select>
                 <span id="error_disablity_type" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>
                <div class="form-group col-md-4">
                 <label class="required-field">Percentage of Disablity</label>
                 <input type="text" name="disablity_type_percentage" id="disablity_type_percentage" class="form-control " placeholder="Percentage" maxlength="5" value="{{ old('disablity_type_percentage') }}" tabindex="{{$tabindex}}" />
                 <span id="error_disablity_type_percentage" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>

                <div class="form-group col-md-4">
                  <label class="required-field">Authority Name</label>
                  <input type="text" name="disablity_type_authority" id="disablity_type_authority" class="form-control txtOnly" placeholder="Certifying Authority" maxlength="200" value="{{ old('disablity_type_authority') }}" tabindex="{{$tabindex}}" />
                  <span id="error_disablity_type_authority" class="text-danger"></span>
                  @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>

		<div class="form-group col-md-4">
                 <label class="required-field">Authority Designation</label>
                 <input type="text" name="disability_designation" id="disability_designation" class="form-control txtOnly" placeholder="Designation Name" maxlength="200" value="{{ old('disability_designation') }}" tabindex="{{$tabindex}}" />
                 <span id="error_disability_designation" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>

                <div class="form-group col-md-4">
                 <label class="required-field">Marital Status</label>
                 <select class="form-control" name="marital_status" id="marital_status" tabindex="{{$tabindex}}" >
                    <option value="">--Select--</option>
                    @foreach(Config::get('constants.marital_status') as $key=>$val)
                    @if($key=='Seperated') 
                    @continue;
                    @endif;
                    <option value="{{$key}}" @if(old('marital_status') == $key)  selected  @endif >{{$val}}</option>
                    @endforeach
                  </select>
                 <span id="error_marital_status" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>
                

                <div class="row" id="spouse_section" >


                <div class="form-group col-md-4">
                  &nbsp;
                </div>
  
                <div class="form-group col-md-12">
                 <label class="">Spouse Name (if applicable)</label>
               
                </div>
              
                <div class="form-group col-md-4">
                 <label class="">First Name</label>
                 <input type="text" name="spouse_first_name" id="spouse_first_name" class="form-control txtOnly" placeholder="First Name" maxlength="200" value="{{ old('spouse_first_name') }}" tabindex="{{$tabindex}}" />
                 <span id="error_spouse_first_name" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>
                <div class="form-group col-md-4">
                 <label>Middle Name</label>
                 <input type="text" name="spouse_middle_name" id="spouse_middle_name" class="form-control txtOnly"  placeholder="Middle Name" maxlength="100" value="{{ old('spouse_middle_name') }}" tabindex="{{$tabindex}}" />
                 <span id="error_spouse_middle_name" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>
                <div class="form-group col-md-4">
                 <label class="">Last Name</label>
                 <input type="text" name="spouse_last_name" id="spouse_last_name" class="form-control txtOnly" placeholder="Last Name" maxlength="200" value="{{ old('spouse_last_name') }}" tabindex="{{$tabindex}}" />
                 <span id="error_spouse_last_name" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
                </div>  

                </div>




                 <div class="form-group col-md-4">
                 <label class="required-field">Monthly Family Income (In Rs)</label>
                 <input type="text" name="monthly_income" id="monthly_income" class="form-control price-field" placeholder="Monthly Family Income(Rs.)" maxlength="9" value="{{ old('monthly_income') }}" tabindex="{{$tabindex}}" >
                 <span id="error_monthly_income" class="text-danger"></span>
                 @php
                  $tabindex=$tabindex+1;
                @endphp
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
                  <div class="form-group col-md-12">
                  <label class="">{!! $document_msg !!}</label>
                  <!-- Any one of Digital Ration Card Number, EPIC/Voter Id, Aadhaar Number, PAN is mandatory</label> -->
                  </div>
                </div>
                 <div class="row">
              
                <div class="form-group col-md-4">
                 <label class="">Digital Ration Card Number</label>
                  <div class="row" >
                  <div class="col-md-5" >
                    
                    
                   <!--  <input style="margin-left:-15px; margin-right:-15px;" type="text" name="ration_card_cat" id="ration_card_cat" class="form-control special-char" placeholder="Category" maxlength="5" value="{{ old('ration_card_cat') }}"  tabindex="1" /> -->

                    <select class="form-control " name="ration_card_cat" id="ration_card_cat"  tabindex="1" style="margin-left:-15px;">
                    <option value="">Category</option>
                    @foreach(Config::get('constants.ration_cat') as $key=>$val)
                    <option value="{{$key}}" @if(old('ration_card_cat') == $key)  selected  @endif >{{$val}}</option>
                    @endforeach                                          
                    </select>
                   
                  </div>
                  
                  <div class="col-md-7">
                   
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
                 <label  class="">Aadhaar Number</label>
                 <input type="text" name="aadhar_no" id="aadhar_no" class="form-control NumOnly" placeholder="Aadhar No." maxlength="12" value="{{ old('aadhar_no') }}"  tabindex="4" />
                 <span id="error_aadhar_no" class="text-danger"></span>
                </div>
 
</div>
<div class="row">
                <div class="form-group col-md-4">
                 <label class="">EPIC/Voter Id number</label>
                 <input type="text" name="epic_voter_id" id="epic_voter_id" class="form-control"  placeholder="EPIC/Voter Id.No."  maxlength="20" value="{{ old('epic_voter_id') }}" tabindex="5" />
                 <span id="error_epic_voter_id" class="text-danger"></span>
                </div>

                <div class="form-group col-md-4">
                 <label  class="">PAN</label>
                 <input type="text" name="pan_no" id="pan_no" class="form-control special-char" placeholder="PAN" maxlength="10" value="{{ old('pan_no') }}"  onkeyup="this.value = this.value.toUpperCase();" tabindex="6" />
                 <span id="error_pan_no" class="text-danger"></span>
                </div>


                <div class="form-group col-md-4">
                 <label class="">BPL Seq Number (if avaiable)</label>
                 <input type="text" name="bpl_seq_no" id="bpl_seq_no" class="form-control special-char" placeholder="BPL Seq No." maxlength="12" value="{{ old('bpl_seq_no') }}" tabindex="7" >
                 <span id="error_bpl_seq_no" class="text-danger"></span>
                </div>

</div>
<div class="row">
                <div class="form-group col-md-4">
                 <label class="">BPL Id Number (if avaiable)</label>
                 <input type="text" name="bpl_id_no" id="bpl_id_no" class="form-control special-char" placeholder="BPL Id No." maxlength="12" value="{{ old('bpl_id_no') }}" tabindex="8" >
                 <span id="error_bpl_id_no" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="">BPL Total Score (if avaiable)</label>
                 <input type="text" name="bpl_total_score" id="bpl_total_score" class="form-control NumOnly" placeholder="BPL Total Score" maxlength="6" value="{{ old('bpl_total_score') }}"  tabindex="9" >
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
                <div class="form-group col-md-12 ajax_loader" style="display:none;">
                  <img src="{{asset('images/ZKZg.gif')}}"/>
                </div>                
               <div class="form-group col-md-4">
                 <label class="required-field">State</label>
                 <input type="text" id="state" name="state" class="form-control" placeholder="" value="WEST BENGAL" readonly="true" tabindex="1">
                 <span id="error_state" class="text-danger"></span>
                </div>              
             

               <div class="form-group col-md-4">
                 <label class="required-field">District</label>
                 <select name="district" id="district" class="form-control  client-js-district" tabindex="2">
                  <option value="">--Select  --</option>
                   @foreach ($districts as $district)
                  <option value="{{$district->district_code}}"  @if(old('district')== $district->district_code)  selected  @endif> {{$district->district_name}}</option>
                  @endforeach
                </select>
                 <span id="error_district" class="text-danger"></span>

                </div>

            


               <div class="form-group col-md-4">
                 <label class="required-field">Assembly Constituency</label>
                <select name="asmb_cons" id="asmb_cons" class="form-control  select2 client-js-assembly" tabindex="3">
                    <option value="">--Select--</option>
                   
                  </select>
                 <span id="error_asmb_cons" class="text-danger"></span>

                </div>



              </div>
              <div class="row">

              <div class="form-group col-md-4" id="divUrbanCode">
                <label class="required-field">Rural/ Urban</label>
                
                <select name="urban_code" id="urban_code" class="form-control client-js-urban" tabindex="4">
                  <option value="">--Select  --</option>
                  @foreach(Config::get('constants.rural_urban') as $key=>$val)
                  <option value="{{$key}}" @if( old('urban_code') == $key)  selected  @endif >{{$val}}</option>
                  @endforeach     
                   
                </select>
                  <span id="error_urban_code" class="text-danger"></span>
              </div>

            

                 

                <div class="form-group col-md-4" id="divBodyCode">
                <label class="required-field">Block/Municipality/Corp.</label>
                
                <select name="block" id="block" class="form-control  select2 client-js-localbody" tabindex="5">
                  <option value="">--Select --</option>
                  
                   
                </select>
                  <span id="error_block" class="text-danger"></span>
              </div>

             
              <div class="form-group col-md-4" id="divBodyCode">
                <label  class="required-field">GP/Ward No</label>
                
                <select name="gp_ward" id="gp_ward" class="form-control  select2 client-js-gpward" tabindex="6">
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
                 <label class="">House/Premise Number</label>
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
                 <label class="">Email Id </label>
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

              <div class="tab-pane fade" id="bank_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Bank Account Details</b></h4></div>
               <div class="panel-body">

                <div class="form-group col-md-6">
                  <label class="required-field">IFS Code</label>
                  <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control special-char" placeholder="IFSC Code" onkeyup="this.value = this.value.toUpperCase();"  value="{{ old('bank_ifsc_code') }}" maxlength='11' tabindex="1" />
                  <span id="error_bank_ifsc_code" class="text-danger"></span>
                </div>

                <div class="form-group col-md-6">
                 <label class="required-field">Bank Name</label>
                 <input type="text" name="name_of_bank" id="name_of_bank" class="form-control special-char" placeholder="Bank Name"  value="{{ old('name_of_bank') }}" maxlength="200" tabindex="2" readonly />
                 <span id="error_name_of_bank" class="text-danger"></span>
                </div>
               
               
                
                <div class="form-group col-md-6">
                 <label class="required-field">Bank Branch Name</label>
                 <input type="text" name="bank_branch" id="bank_branch" class="form-control special-char" placeholder="Bank Branch Name"  value="{{ old('bank_branch') }}" maxlength="300" tabindex="3" readonly />
                 <span id="error_bank_branch" class="text-danger"></span>
                </div>

                <div class="form-group col-md-6">
                 <label class="required-field">Bank Account Number</label>
                 <input type="text" name="bank_account_number" id="bank_account_number" class="form-control NumOnly" placeholder="Bank Account No"  value="{{ old('bank_account_number') }}" maxlength='16' tabindex="4" />
                 <span id="error_bank_account_number" class="text-danger"></span>

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
             

            <div class="tab-pane fade" id="experience_details">
              <div class="panel panel-default">
                 <div class="panel-heading"></h4></b>Enclosure List (Self Attested)</b></h4></div>
                  <div class="panel-body">

              
               <!-- Document Dynamic-->   
               {!! $document_msg !!}
                                   @foreach ($doc_list_man as $doc_man)
                                    <div class="form-group col-md-12">
                                    <label class="required-field">{{ $doc_man['doc_name'] }}</label>
                                    <input type="file" name="doc_{{ $doc_man['id'] }}" id="doc_{{ $doc_man['id'] }}" class="form-control" tabindex="1" />
                                    <div class="imageSize">(Image type must be {{ $doc_man['doc_type'] }} and image size max {{ $doc_man['doc_size_kb'] }}KB)</div>
                                    <span id="error_doc_{{ $doc_man['id']}}" class="text-danger"></span>
                                    </div>

                                    @endforeach                             
                                     
                                    @foreach ($doc_list_opt as $doc_opt)
                                    <div class="form-group col-md-12">
                                    <label class="">{{ $doc_opt['doc_name'] }}</label>
                                    <input type="file" name="doc_{{ $doc_opt['id'] }}" id="doc_{{ $doc_opt['id'] }}" class="form-control" tabindex="1" />
                                    <div class="imageSize">(Image type must be {{ $doc_opt['doc_type'] }} and image size max {{ $doc_opt['doc_size_kb'] }}KB)</div>
                                    <span id="error_doc_{{ $doc_opt['id'] }}" class="text-danger"></span>
                                    </div>

                                    @endforeach 
                                     
                               <!-- Document Dynamic End-->   
                                     
                                  

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




                <div class="form-group col-md-12 aadhar-text" >
                 <label class="">I  <select name="av_status" id="av_status"> <option value="1"> give </option>
                 <option value="0">do not give </option> </select> consent to the use of the Aadhaar No.for authenticating my identity for social security pension (In case Aadhaar no. provided by the applicant)</label>
                 </div>


              </div>




                 <div class="row">
                 <?php
                  $old_receive_pension = array();
                  if(old('receive_pension')!=null)
                    $old_receive_pension = old('receive_pension');
                    //explode(',',);
                  $old_social_security_pension = array();
                  if(old('social_security_pension')!=null)
                    $old_social_security_pension = old('social_security_pension');
                    //explode(',',);
                ?>
                 <div class="form-group col-md-12" tabindex="4">

                   <label>Presently, I am reciving following pension(s) from</label>

                   <br / >
                @foreach(Config::get('constants.pension_body') as $key=>$desc)
                <label>
               <input type="checkbox" class="receive-pension" name="receive_pension[]"  value="{{$key}}" @if(in_array($key,$old_receive_pension,true)) checked @endif> {{$desc}} 
                </label>
                <br / >
                @endforeach
              
                </div>
                <label>In case the applicant is receiving pension from other sources</label>
                <br/>
                <label>1.</label>
                <input type="text" name="receiving_pension_other_source_1" id="receiving_pension_other_source_1" class="form-control" placeholder=""  value="{{ old('receiving_pension_other_source_1') }}" maxlength='300' tabindex="3" />
                <label>2.</label>
                <input type="text" name="receiving_pension_other_source_2" id="receiving_pension_other_source_2" class="form-control" placeholder=""  value="{{ old('receiving_pension_other_source_2') }}" maxlength='300' tabindex="3" />
                 </div>


                 <div class="row">

                 <div class="form-group col-md-12" tabindex="5">

                   <label>Presently, I am receiving the following social Security Pension/s (Please tick)</label>

                   <br / >
                @foreach(Config::get('constants.social_pension_cat') as $key=>$desc)
                <label>
                <input type="checkbox" class="social-security-pension" name="social_security_pension[]" value="{{$key}}" @if(in_array($key,$old_social_security_pension,true)) checked @endif> {{$desc}}
                </label>


                <br / >
                @endforeach
            
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
                        <div class="modal_field_value" id=""><p><h2>Government of West Bengal</h2></p></div>
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
                                <div class="modal_field_name">Application Type:</div>
                                <div class="modal_field_value" id="entry_type_modal"></div>
                              </div>
                  </div>
                            <div class="row modalDuareSarkar">

                              <div class="col-md-6">
                                <div class="modal_field_name" style="margin-right:6%;">Duare Sarkar Registration no.</div>
                                <div class="modal_field_value" id="ds_registration_no_modal"></div>
                              </div>

                              <div class="col-md-6">
                                <div class="modal_field_name" style="margin-right:6%;">Duare Sarkar Date:</div>
                                <div class="modal_field_value" id="ds_date_modal"></div>
                              </div>
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
                        <div class="col-md-3">
                        <div class="modal_field_name" style="margin-right:6%;">Type of Disablity:</div>
                        <div class="modal_field_value" id="disablity_type_modal"></div>
                        </div>
                        <div class="col-md-3">
                        <div class="modal_field_name" style="margin-right:6%;">Percentage of Disablity:</div>
                        <div class="modal_field_value" id="disablity_type_percentage_modal"></div>
                        </div>
                        <div class="col-md-3">
                          <div class="modal_field_name" style="margin-right:6%;">Designation:</div>
                          <div class="modal_field_value" id="disability_designation_authority_modal"></div>
                          </div>
                        <div class="col-md-3">
                        <div class="modal_field_name" style="margin-right:6%;">Authority Name:</div>
                        <div class="modal_field_value" id="disablity_type_authority_modal"></div>
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
                        <div class="col-md-12"><h2 style="">Self Declaration</h2></div>
                       </div>




                       <div class="row">

                    

<!-- 
                         <div class="col-md-12">
                         <div class="modal_field_name">to receive the rest amount payable to me till my death</div>
                         
                        </div> -->

                         <div class="col-md-12 aadhar-text-modal">
                        <div class="modal_field_name">I <span id="av_status_modal">give</span> consent to the use of the Aadhaar No.for authenticating my identity for social security pension (In case Aadhaar no. provided by the applicant)</div>
                       
                        </div>


                        <div class="col-md-12">
                        <div class="modal_field_name">Presently, I am reciving following pension(s) from:</div>
                        <div class="modal_field_value" id="receive-pension-modal"></div>
                        </div>
                        <div class="col-md-12"
                        style="
                        float: left;
                        font-weight: 700;
                        margin-right: 1%;
                        padding-top: 1%;
                        margin-top: 1%;">
                        <div class="">In case the applicant is receiving pension from other sources:</div>
                        <ul>
                          <li>1.<span id="receiving_pension_other_source_1_txt"></span></li>
                          <li>2.<span id="receiving_pension_other_source_2_txt"></span></li>
                        </ul>
                        {{-- <div class="modal_field_value">1.<span id="receiving_pension_other_source_1_txt"></span></div>
                        <div class="modal_field_value">2.<span id="receiving_pension_other_source_2_txt"></span></div> --}}
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

  <!-- Footer -->
  @include('layouts.footer')
  
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

 <!-- jQuery 2.1.3 -->
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<script src="{{ URL::asset('js/site.js') }}"></script>

<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script src="{{ URL::asset('js/site-client.js') }}"></script>


<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script>
  $('.select2').select2();
</script>











<script>
  var specialKeys = new Array();
        specialKeys.push(8); //Backspace
        function IsNumeric(e) {
          alert()
            var keyCode = e.which ? e.which : e.keyCode
            var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
            document.getElementById("error").style.display = ret ? "none" : "inline";
            return ret;
        }

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

// Document Dynamic
$("#doc_{{$profile_img}}").change(function() {
$("#passport_image_view").show();
  readURL(this);
});


// function readURL2(input) {
//   if (input.files && input.files[0]) {
//     var reader = new FileReader();
    
//     reader.onload = function(e) {
//       $('#signature_image_view').attr('src', e.target.result);
//       $('#signature_image_view_modal').attr('src', e.target.result);
//     }
    
//     reader.readAsDataURL(input.files[0]);
//   }
// }

// $("#signature_image_").change(function() {
//   readURL2(this);
// });



$(document).ready(function(){
  var old_districtValue="";
  var old_assemblyValue="";
  var old_blockValue="";
  var old_gpValue="";
  var old_urbanValue="";
  @if (old('district'))
  old_districtValue={{old('district')}};
  @endif
  @if (old('asmb_cons'))
  old_assemblyValue={{old('asmb_cons')}};
  @endif
  @if (old('urban_code'))
  old_urbanValue={{old('urban_code')}};
  @endif
  @if (old('block'))
  old_blockValue={{old('block')}};
  @endif
  @if (old('gp_ward'))
  old_gpValue={{old('gp_ward')}};
  @endif
  @if (old('district'))
  var event = new Event('change');
  $("#district").val(old_districtValue);
  var element = document.getElementById('district');
  element.dispatchEvent(event);

  $("#asmb_cons").val(old_assemblyValue);

  $("#urban_code").val(old_urbanValue);
  var element1 = document.getElementById('urban_code');
  element1.dispatchEvent(event);

  $("#block").val(old_blockValue);
  var element2 = document.getElementById('block');
  element2.dispatchEvent(event);

  $("#gp_ward").val(old_gpValue);
  @endif
    $(".aadhar-text").hide();
    $(".aadhar-text-modal").hide();
    $("#submitting").hide();
    $("#submit_loader").hide();
    $("#passport_image_view").hide(); 
    $("#spouse_section").hide(); 


    $('form.submit-once').submit(function(e){
    if( $(this).hasClass('form-submitted') ){
        e.preventDefault();
        return;
    }
    $(this).addClass('form-submitted');
   });
if($("#entry_type").val() == "Form through Duare Sarkar camp" )
    {
        $(".duareSarkar").show(); 
    }
    else{
       $(".duareSarkar").hide(); 
    }



$("#entry_type").on('change', function(){
      var entry_type =  $("#entry_type").val();
      //alert(entry_type);
      if(entry_type == "Form through Duare Sarkar camp")
      {
        
        $(".duareSarkar").show(); 
      } 
      else
      {
        $(".duareSarkar").hide();
      }
    });

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
  
  
    //$(".submitting").attr("disabled", true);


    $(".receive-pension").click(function(){        

        var selectedRP = new Array();
        var n1 = jQuery(".receive-pension:checked").length;
        if (n1 > 0){
         
            jQuery(".receive-pension:checked").each(function(){
                selectedRP.push( $(this).val());
            });
        }  

        $("#receive-pension-modal").text(selectedRP)
        
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

       
    });


    $("#dob").on('blur',function(){ 
      var today = new Date();
      var birthDate = new Date($('#dob').val());

      var diff_ms = today.getTime() - birthDate.getTime();
      var age_dt = new Date(diff_ms); 
      var age = Math.ceil(age_dt.getUTCFullYear() - 1970);

      if(isNaN(age)){
        age = 0;
      }
      $('#hidden_age').val(age); 
      $('#txt_age').val(age);
    });
    
    // $("#dob").on('blur',function(){ 
    // var today = new Date('2020-01-01');
    
    // var birthDate = new Date($('#dob').val());
    
    // var age = today.getFullYear() - birthDate.getFullYear();
   
    // var m = today.getMonth() - birthDate.getMonth();
    // if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
    //     age--;
    // }    
    //  $('#hidden_age').val(age); 
    // $('#txt_age').val(age);
    // });

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


/*$('.txtOnly').keydown(function (e) {
  
    if (e.altKey) {
    
      e.preventDefault();
      
    } else {
    
      var key = e.keyCode;
      
      if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90))) {
      
        e.preventDefault();
        
      }

    }
    
  });*/

  

 

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
$('#district_state').change(function() {
      var district=$(this).val();
      //alert(district);
        $('#urban_code_state').val('');
        $('#block_subdiv_state').html('<option value="">--Select --</option>');
});    
$('#urban_code_state').change(function() {
       var urban_code=$(this).val();
        if(urban_code==''){
          $('#block_subdiv_state').html('<option value="">--Select --</option>'); 
        }
        $('#block_subdiv_state').html('<option value="">--Select --</option>');
        select_district_code= $('#district_state').val();
        if(select_district_code==''){
               alert('Please Select District First');
               $("#district_state").focus();
               $("#urban_code_state").val('');
        }
        else{
        select_body_type= urban_code;
        var htmlOption='<option value="">--Select--</option>';
        if(select_body_type==2){
            $("#blk_sub_txt").text('Block');
            $.each(blocks, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        }else if(select_body_type==1){
            $("#blk_sub_txt").text('Subdivision');
            $.each(subDistricts, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        } 
        else{
          $("#blk_sub_txt").text('Block/Subdivision');
        }   
        $('#block_subdiv_state').html(htmlOption);
        }

    });
        

 
 $('#btn_personal_details').click(function(){  

  var error_district_state = '';
  var error_urban_code_state = '';
  var error_block_subdiv_state = '';
  var error_entry_type = '';
  var error_ds_registration_no = '';
  var error_ds_date = ''; 
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

  var error_disablity_type = '';
  var error_disablity_type_percentage = '';
  var error_disablity_type_authority = '';
  var error_disability_designation='';
  var error_monthly_income = '';
  var is_state_login='{{$is_state_login}}';
  if(is_state_login==1){
  if($.trim($('#district_state').val()).length == 0)
  {
   error_district_state = 'Please Select District';
   $('#error_district_state').text(error_district_state);
   $('#district_state').addClass('has-error');
  }
  else
  {
   error_district_state = '';
   $('#error_district_state').text(error_district_state);
   $('#district_state').removeClass('has-error');
  }
  if($.trim($('#urban_code_state').val()).length == 0)
  {
   error_urban_code_state = 'Please Select Rural/ Urban';
   $('#error_urban_code_state').text(error_urban_code_state);
   $('#urban_code_state').addClass('has-error');
  }
  else
  {
   error_urban_code_state = '';
   $('#error_urban_code_state').text(error_urban_code_state);
   $('#urban_code_state').removeClass('has-error');
  }
  if($.trim($('#block_subdiv_state').val()).length == 0)
  {
   error_block_subdiv_state = 'Please Select Block/SubDivision.';
   $('#error_block_subdiv_state').text(error_block_subdiv_state);
   $('#block_subdiv_state').addClass('has-error');
  }
  else
  {
   error_block_subdiv_state = '';
   $('#error_block_subdiv_state').text(error_block_subdiv_state);
   $('#block_subdiv_state').removeClass('has-error');
  }
  }
 if($.trim($('#entry_type').val()).length == 0)
  {
   error_entry_type = 'Please Select Application Type';
   $('#error_entry_type').text(error_entry_type);
   $('#entry_type').addClass('has-error');
  }
  else
  {
   error_entry_type = '';
   $('#error_entry_type').text(error_entry_type);
   $('#entry_type').removeClass('has-error');
  }
  if($('#entry_type').val()=='Form through Duare Sarkar camp')
  {
      if($.trim($('#ds_registration_no').val()).length == 0)
      {
      error_ds_registration_no = 'Duare Sarkar Registration no. is required';
      $('#error_ds_registration_no').text(error_ds_registration_no);
      $('#ds_registration_no').addClass('has-error');
      }
      else
      {
      error_ds_registration_no = '';
      $('#error_ds_registration_no').text(error_ds_registration_no);
      $('#ds_registration_no').removeClass('has-error');
      }
      if($.trim($('#ds_date').val()).length == 0)
      {
      error_ds_date = 'Duare Sarkar Date is required';
      $('#error_ds_date').text(error_ds_date);
      $('#ds_date').addClass('has-error');
      }
      else
      {
      error_ds_date = '';
      $('#error_ds_date').text(error_ds_date);
      $('#ds_date').removeClass('has-error');
      }
  }


 
  
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
   $('#gender').addClass('has-error');
  }
  else
  {
   error_gender = '';
   $('#error_gender').text(error_gender);
   $('#gender').removeClass('has-error');
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
        if($.trim($('#dob').val()).length > 0)
        { 
	        if( $('#hidden_age').val() != $('#txt_age').val() )
	        {      
	        error_txt_age = 'Age should be equal according to date of birth';
	        $('#error_txt_age').text(error_txt_age);
	        $('#txt_age').addClass('has-error');
	        }
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
  if($.trim($('#disablity_type').val()).length == 0)
  {
    error_disablity_type = 'Disablity Type is required';
   $('#error_disablity_type').text(error_disablity_type);
   $('#disablity_type').addClass('has-error');
  }
  else
  {
   error_disablity_type = '';
   $('#error_disablity_type').text(error_disablity_type);
   $('#disablity_type').removeClass('has-error');
  }
  if($.trim($('#disablity_type_percentage').val()).length == 0)
  {
   var error_disablity_type_percentage = 'Disablity Type Percentage is required';
   $('#error_disablity_type_percentage').text(error_disablity_type_percentage);
   $('#disablity_type_percentage').addClass('has-error');
  }
  else
  {
    var val=$('#disablity_type_percentage').val();
   error_disablity_type_percentage = '';
   var regex=  /^((0|[1-9]\d?)(\.\d{1,2})?|100(\.00?)?)$/;
        if (!val.match(regex)) {


  var error_disablity_type_percentage = 'Disablity Type Percentage is not correct';
   $('#error_disablity_type_percentage').text(error_disablity_type_percentage);
   $('#disablity_type_percentage').addClass('has-error');
  
}
else{
  if($('#disablity_type_percentage').val() < 40){
   var error_disablity_type_percentage = 'Disablity Percentage should be >= 40';
   $('#error_disablity_type_percentage').text(error_disablity_type_percentage);
   $('#disablity_type_percentage').addClass('has-error');
  }else{	
  $('#error_disablity_type_percentage').text('');
   $('#disablity_type_percentage').removeClass('has-error');
}
}





  } 
  if($.trim($('#disablity_type_authority').val()).length == 0)
  {
   error_disablity_type_authority = 'Certifying Authority is required';
   $('#error_disablity_type_authority').text(error_disablity_type_authority);
   $('#disablity_type_authority').addClass('has-error');
  }
  else
  {
    error_disablity_type_authority = '';
   $('#error_disablity_type_authority').text(error_disablity_type_authority);
   $('#disablity_type_authority').removeClass('has-error');
  } 

  
  if($.trim($('#disability_designation').val()).length == 0)
  {
    error_disability_designation = 'Designation name is required';
   $('#error_disability_designation').text(error_disability_designation);
   $('#disability_designation').addClass('has-error');
  }
  else
  {
    error_disability_designation = '';
   $('#error_disability_designation').text(error_disability_designation);
   $('#disability_designation').removeClass('has-error');
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

   if( error_district_state!='' || error_urban_code_state!='' || error_block_subdiv_state!='' || error_entry_type!='' || error_ds_registration_no !='' || error_ds_date !='' || error_first_name != '' || error_last_name != '' || error_gender != '' || error_txt_age != '' || error_father_first_name != '' || error_father_last_name != '' || error_mother_first_name != '' || error_mother_last_name != '' || error_caste_category != '' || error_marital_status != '' || error_monthly_income != '' 
   || error_disablity_type!="" || error_disablity_type_percentage!="" || error_disablity_type_authority!="" || error_disability_designation!=""
   
   )

  

   //if( error_first_name != '')


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
  var error_pan_no = '';
  

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
        error_ration_card_no = 'Digital Ration Card No should not be greater than 10 digit';
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

  if($.trim($('#aadhar_no').val()).length == 0)
  {
    error_aadhar_no = 'Aadhar No is required';
   $('#error_aadhar_no').text(error_aadhar_no);
   $('#aadhar_no').addClass('has-error');
  }
  else
  {
    
   $(".aadhar-text").show();
   $(".aadhar-text-modal").show();

     if($.trim($('#aadhar_no').val()).length != 12)
     {

     error_aadhar_no = 'Aadhar No should be 12 digit ';
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
  

  if($.trim($('#pan_no').val()).length == 0)
  {
    error_pan_no = 'PAN No is required';
   $('#error_pan_no').text(error_pan_no);
   $('#pan_no').addClass('has-error');
  }
  else
  {
    error_pan_no = '';
   $('#error_pan_no').text(error_pan_no);
   $('#pan_no').removeClass('has-error');
  }
  
  
  if((error_ration_card_cat == '' && error_ration_card_no == '') || error_epic_voter_id == '' || error_aadhar_no == '' || error_pan_no == '') 
  {
      $('#error_ration_card_no').text('');
      $('#ration_card_no').removeClass('has-error');
      $('#error_ration_card_cat').text('');
      $('#ration_card_cat').removeClass('has-error');
      $('#error_epic_voter_id').text('');
      $('#epic_voter_id').removeClass('has-error');
      $('#error_aadhar_no').text('');
      $('#aadhar_no').removeClass('has-error');
      $('#error_pan_no').text('');
      $('#pan_no').removeClass('has-error');
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
  }else{
    return false;
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

 function ltrim(str){
    return str.replace(/^0+/, "");
 }
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
   $('.js-assembly').addClass('has-error');
  }
  else
  {
   error_asmb_cons = '';
   $('#error_asmb_cons').text(error_asmb_cons);
   $('.js-assembly').removeClass('has-error');
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


    if(ltrim($.trim($('#mobile_no').val())).length !=10)
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
  

   if(error_district != '' || error_urban_code != '' || error_block != '' || error_gp_ward != '' || error_village != '' || error_post_office != '' || error_pin_code != '' || error_police_station != '' || error_residency_period != '' || error_mobile_no != ''  || error_email != '' || error_asmb_cons != ''  )
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

 $('#bank_ifsc_code').blur(function(){
    $ifsc_data = $.trim($('#bank_ifsc_code').val());
    $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
    if($ifscRGEX.test($ifsc_data))
    {
      $('#bank_ifsc_code').removeClass('has-error');
      $('#error_bank_ifsc_code').text('');

      $.ajax({
        type: 'POST',
        url: '{{ url('legacy/getBankDetails') }}',
        data: {
          ifsc: $ifsc_data,
          _token: '{{ csrf_token() }}',
        },
        success: function (data) {
          if (!data || data.length === 0) {
            $('#error_bank_ifsc_code').text('No data found with the IFSC');
            $('#bank_ifsc_code').addClass('has-error');
            return;
          }
          data = JSON.parse(data);
         // console.log(data);
          $('#name_of_bank').val(data.bank);
          $('#bank_branch').val(data.branch);
        },
        error: function (ex) {
          $('#error_bank_ifsc_code').text('Data fetch error');
          $('#bank_ifsc_code').addClass('has-error');
        }
      });

    }else{
      $('#error_bank_ifsc_code').text('IFSC format invalid please check the code');
      $('#bank_ifsc_code').addClass('has-error');
    }
 });

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

  $ifsc_data = $.trim($('#bank_ifsc_code').val());
  $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
  if($ifscRGEX.test($ifsc_data))
  {
    error_bank_ifsc_code = '';
    $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
    $('#bank_ifsc_code').removeClass('has-error');
  }
  else{
    error_bank_ifsc_code = 'Please check IFS Code format';
    $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
    $('#bank_ifsc_code').addClass('has-error');    
  }

  
   if(error_name_of_bank !='' || error_bank_branch !=''||  error_bank_account_number !='' || error_bank_ifsc_code !='')
    // if(error_name_of_bank !='' )
  {
   return false;
  }
  else
  {
    
    $('#list_bank_details').removeClass('active active_tab1');
    $('#list_bank_details').removeAttr('href data-toggle');
    $('#bank_details').removeClass('active');
    $('#list_bank_details').addClass('inactive_tab1');
    $('#list_experience_details').removeClass('inactive_tab1');
    $('#list_experience_details').addClass('active_tab1 active');
    $('#list_experience_details').attr('href', '#experience_details');
    $('#list_experience_details').attr('data-toggle', 'tab');
    $('#experience_details').addClass('active in');
  }

 });

 

  $('#previous_btn_experience_details').click(function(){
  $('#list_experience_details').removeClass('active active_tab1');
  $('#list_experience_details').removeAttr('href data-toggle');
  $('#experience_details').removeClass('active in');
  $('#list_experience_details').addClass('inactive_tab1');
  $('#list_bank_details').removeClass('inactive_tab1');
  $('#list_bank_details').addClass('active_tab1 active');
  $('#list_bank_details').attr('href', '#bank_details');
  $('#list_bank_details').attr('data-toggle', 'tab');
  $('#bank_details').addClass('active in');
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

  
    
    $('#list_experience_details').removeClass('active active_tab1');
    $('#list_experience_details').removeAttr('href data-toggle');
    $('#experience_details').removeClass('active');
    $('#list_experience_details').addClass('inactive_tab1');


    $('#list_decl_details').removeClass('inactive_tab1');
    $('#list_decl_details').addClass('active_tab1 active');
    $('#list_decl_details').attr('href', '#decl_details');
    $('#list_decl_details').attr('data-toggle', 'tab');
    $('#decl_details').addClass('active in');
  //}

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
    $('#entry_type_modal').text($('#entry_type').val());
    if($('#entry_type').val()=='Form through Duare Sarkar camp'){
    $('#ds_registration_no_modal').text($('#ds_registration_no').val());
    var duare_sarkar_date = $('#ds_date').val();
    var dArr = duare_sarkar_date.split("-");
    var today1 = dArr[2] + '/' + dArr[1] + '/' + dArr[0];
    $('#ds_date_modal').text(today1);
     $(".modalDuareSarkar").show();
    }
    else{
      $(".modalDuareSarkar").hide();
    }

    $('#name_modal').text($('#first_name').val()+' '+$('#middle_name').val()+' '+$('#last_name').val());
    $('#gender_modal').text($('#gender').val());
    $('#dob_modal').text($('#dob').val());
    $('#father_name_modal').text($('#father_first_name').val()+' '+$('#father_middle_name').val()+' '+$('#father_last_name').val());
    $('#mother_name_modal').text($('#mother_first_name').val()+' '+$('#mother_middle_name').val()+' '+$('#mother_last_name').val());


    $('#caste_category_modal').text($('#caste_category').val());
    $('#disablity_type_modal').text($( "#disablity_type option:selected" ).text());
    $('#disablity_type_percentage_modal').text($('#disablity_type_percentage').val());
    $('#disablity_type_authority_modal').text($('#disablity_type_authority').val());
    $('#disability_designation_authority_modal').text($('#disability_designation').val());
    
    $('#marital_status_modal').text($('#marital_status').val());
   // $('#fisherman_comm_modal').text($('#fisherman_comm').val());
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
    $('#av_status_modal').text($("#av_status option:selected" ).text());
    $('#text_1_modal').text($('#text_1').val());
    $('#text_2_modal').text($('#text_2').val());
    $('#receiving_pension_other_source_1_txt').text($('#receiving_pension_other_source_1').val());
    $('#receiving_pension_other_source_2_txt').text($('#receiving_pension_other_source_2').val());

    $('#residency_period_modal').text($('#residency_period').val());

    






// $('.modal-submit').click(function(){
// $(".modal-submit").attr("disabled", true);


// });

    
});

$('.modal-submit').on('click',function(){
//$(".modal-submit").attr("disabled", true);
$(".modal-submit").hide();
$("#submitting").show();
$("#submit_loader").show();
//$("#register_form").submit();
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


