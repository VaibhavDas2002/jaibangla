<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PCC | Bidhan Nagar Police</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="keywords">
  <meta content="" name="description">


  <!-- Favicons -->
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("frontend/img/favicon.ico") }}">
 

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i|Raleway:300,400,500,700,800|Montserrat:300,400,700" rel="stylesheet">

  <!-- Bootstrap CSS File -->
  <link href="{{ asset("frontend/lib/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet">
  <link href="{{ asset("css/select2.min.css") }}" rel="stylesheet">
  <!-- Libraries CSS Files -->
  <link href="{{ asset("frontend/lib/font-awesome/css/font-awesome.min.css") }}" rel="stylesheet">
  <!--link href="{{ asset("frontend/lib/animate/animate.min.css") }}" rel="stylesheet"-->
  <link href="{{ asset("frontend/lib/ionicons/css/ionicons.min.css") }}" rel="stylesheet">
  <link href="{{ asset("frontend/lib/owlcarousel/assets/owl.carousel.min.css") }}" rel="stylesheet">
  <link href="{{ asset("frontend/lib/magnific-popup/magnific-popup.css") }}" rel="stylesheet">
  <link href="{{ asset("frontend/lib/ionicons/css/ionicons.min.css") }}" rel="stylesheet">

  <!-- Main Stylesheet File -->
  <link href="{{ asset("frontend/css/style.css") }}" rel="stylesheet">

  <!-- =======================================================
    Theme Name: Reveal
    Theme URL: https://bootstrapmade.com/reveal-bootstrap-corporate-template/
    Author: BootstrapMade.com
    License: https://bootstrapmade.com/license/
  ======================================================= -->



<script type="text/javascript">
  window.history.forward();
</script>
</head>

<body id="body">

  <!--==========================
    Top Bar
  ============================-->


  <!--==========================
    Header
  ============================-->
  <header id="header">
    <div class="container">

      <div id="logo" class="pull-left">
        <a href="{{ url('/') }}"><img src="{{ asset("frontend/img/Bidhannagar_Police_logo.png") }}" alt="Bidhan Nagar City Police" width="80" /></a>
        <!-- Uncomment below if you prefer to use an image logo -->
        <!-- <a href="#body"><img src="img/logo.png" alt="" title="" /></a>-->
      </div>
      <nav id="nav-menu-container">
        <ul class="nav-menu">
          <li class="menu-active"><a style="color: #000;" href="{{url('pagelogout')}}">Logout</a></li>
        </ul>
      </nav>

      <!--nav id="nav-menu-container">
        <ul class="nav-menu">
          <li class="menu-active"><a href="#body">Home</a></li>
          <li><a href="{{url('echalan/{ref_no}')}}">Upload Document</a></li>
          <li><a href="#services">Search application Id</a></li>
          <li><a href="#portfolio">Portfolio</a></li>
          <li><a href="#team">Team</a></li>
          <li class="menu-has-children"><a href="">Drop Down</a>
            <ul>
              <li><a href="#">Drop Down 1</a></li>
              <li><a href="#">Drop Down 3</a></li>
              <li><a href="#">Drop Down 4</a></li>
              <li><a href="#">Drop Down 5</a></li>
            </ul>
          </li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </nav--><!-- #nav-menu-container -->
    </div>
  </header><!-- #header -->

  <!--==========================
    Intro Section
  ============================-->
 

  <main id="main" >

    <section id="contact" class="wow "  >

    <div class="container box_inter" >
       <div class="form">

        <form name="registration" id="registration" action="{{url('application/update/'.$application->application_id)}}" method="POST" role="form" class="contactForm" enctype="multipart/form-data"> 
          {{ csrf_field() }}
                 
          <div class="row">
             <div class="col-md-12">
              <h3>UPDATE APPLICATION FORM</h3>
                <h4>BASIC INFO</h4>
                <div class="form-row">

                  <div class="form-group{{ $errors->has('in_first_name') ? 'has-error' : '' }} col-md-4">
                    <label for="in_first_name">First Name<span class="requiredStar">*</span></label>
                    <input type="text" name="in_first_name" class="form-control in_first_name" id="in_first_name" placeholder="First Name" data-rule="text" data-msg="Please enter Your First Name" value="{{$application->first_name}}" required/>
                     @if ($errors->has('in_first_name'))
                        <span class="requiredfield">
                            <strong>{{ $errors->first('in_first_name') }}</strong>
                        </span>
                      @endif
                  </div>

                  <div class="form-group{{ $errors->has('in_middle_name') ? ' has-error' : '' }} col-md-4">
                    <label for="in_middle_name">Middle Name</label>
                    <input type="text" name="in_middle_name" value="{{$application->middle_name}}" class="form-control in_middle_name" id="in_middle_name" placeholder="Middle Name" data-rule="text" data-msg="Please enter Your First Name" />
                     @if ($errors->has('in_middle_name'))
                        <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                            <strong>{{ $errors->first('in_middle_name') }}</strong>
                        </span>
                      @endif
                  </div>

                  <div class="form-group{{ $errors->has('in_last_name') ? 'has-error' : '' }} col-md-4">
                    <label>Last Name<span class="requiredStar">*</span></label>
                    <input type="text" class="form-control" name="in_last_name" value="{{$application->last_name}}" id="in_last_name" placeholder="Last Name" data-rule="text" data-msg="Please enter Your Last Name" required/>
                    @if ($errors->has('in_last_name'))
                        <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                            <strong>{{ $errors->first('in_last_name') }}</strong>
                        </span>
                      @endif
                  </div>
                </div>
                <div class="row">
                  <div class="form-group{{ $errors->has('user_img') ? 'has-error' : '' }} col-md-4 ">
                    <label>Passport Size Photo<span class="requiredStar">*</span></label>
                      <input type="file" name="user_img" value="{{ old('user_img') }}"  id="user_img" class="filestyle " required>
                      @if ($errors->has('user_img'))
                        <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                            <strong>{{ $errors->first('user_img') }}</strong>
                        </span>
                      @endif
                      <p>(Photo must be of 3.5cm X 4.5cm color & .jpg/.jpeg/.png format only & less 40kb otherwise applications will be rejected)</p>
                    </div>
                    <div class="col-md-2" >
                      <img id="imgbanner" class="img-responsive" width="100" style=" padding-left:0px;  padding-bottom:7px; ">
                      <div id="imgsize"></div>
                    </div>
                    



                   <div class="form-group{{ $errors->has('in_gender') ? 'has-error' : '' }} col-md-6">
                      <label>Gender<span class="requiredStar">*</span></label>
                      <select name="in_gender" value="{{ old('in_gender') }}"  class="form-control select2" required>
                        <option value="">Select Gender</option>
                        <option value="m" @if(($application->gender)=='m'){{'selected'}}@endif>Male</option>
                        <option value="f"@if(($application->gender)=='f'){{'selected'}}@endif>Female</option>
                      </select>
                      @if ($errors->has('in_gender'))
                        <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                            <strong>{{ $errors->first('in_gender') }}</strong>
                        </span>
                      @endif

                  </div>

                  <div class="form-group{{ $errors->has('in_dob') ? 'has-error' : '' }} col-md-6">
                        <label>Date Of Birth<span class="requiredStar">*</span></label>
                          <input type="date" class="form-control label-floating is-empty" name="in_dob" id="in_dob" value="{{$application->dob}}" placeholder="Date" data-rule="required" data-msg="This field is required" required/>
                          @if ($errors->has('in_dob'))   
                            <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                                <strong>{{ $errors->first('in_dob') }}</strong>
                            </span>
                          @endif
                          <div class="validation"></div>
                        
                  </div>
                  <div class="form-group{{ $errors->has('in_nationality') ? ' has-error' : '' }} col-md-6">
                    <label>Nationality<span class="requiredStar">*</span></label>

                      <select class="form-control select2" name="in_nationality" value="{{ old('in_nationality') }}" id="in_nationality" style="width: 100%;" placeholder="Nationality">
                        
                        <option value="india" @if(($application->nationality)=='india'){{'selected'}}@endif>India</option>
                        <option value="afghan" @if(($application->nationality)=='afghan'){{'selected'}}@endif>Afghan</option>
                        <option value="american" @if(($application->nationality)=='american'){{'selected'}}@endif >American</option>
                        <option value="andorran" @if(($application->nationality)=='andorran'){{'selected'}}@endif>Andorran</option>
                        <option value="angolan" @if(($application->nationality)=='angolan'){{'selected'}}@endif>Angolan</option>
                        <option value="antiguans" @if(($application->nationality)=='antiguans'){{'selected'}}@endif>Antiguans</option>
                        <option value="argentinean" @if(($application->nationality)=='argentinean'){{'selected'}}@endif>Argentinean</option>
                        <option value="armenian" @if(($application->nationality)=='armenian'){{'selected'}}@endif>Armenian</option>
                        <option value="australian" @if(($application->nationality)=='australian'){{'selected'}}@endif>Australian</option>
                        <option value="austrian" @if(($application->nationality)=='austrian'){{'selected'}}@endif>Austrian</option>
                        <option value="azerbaijani" @if(($application->nationality)=='azerbaijani'){{'selected'}}@endif >Azerbaijani</option>
                        <option value="bahamian" @if(($application->nationality)=='bahamian'){{'selected'}}@endif>Bahamian</option>
                        <option value="bahraini" @if(($application->nationality)=='bahraini'){{'selected'}}@endif>Bahraini</option>
                        <option value="bangladeshi" @if(($application->nationality)=='bangladeshi'){{'selected'}}@endif>Bangladeshi</option>
                        <option value="barbadian" @if(($application->nationality)=='barbadian'){{'selected'}}@endif>Barbadian</option>
                        <option value="barbudans" @if(($application->nationality)=='barbudans'){{'selected'}}@endif>Barbudans</option>
                        <option value="batswana" @if(($application->nationality)=='batswana'){{'selected'}}@endif>Batswana</option>
                        <option value="belarusian" @if(($application->nationality)=='belarusian'){{'selected'}}@endif>Belarusian</option>
                        <option value="belgian" @if(($application->nationality)=='belgian'){{'selected'}}@endif>Belgian</option>

                      </select>

                        @if ($errors->has('in_nationality'))
                            <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                                <strong>{{ $errors->first('in_nationality') }}</strong>
                            </span>
                        @endif

                   
                  </div>

                   <div class="form-group{{ $errors->has('in_father_name') ? 'has-error' : '' }} col-md-6">
                    <label>Father's Name<span class="requiredStar">*</span></label>
                    <input type="text" name="in_father_name" value="{{$application->father_name}}" class="form-control" id="in_father_name" placeholder="Father's Name" data-rule="text" data-msg="Please enter Your Father's Name" required/>
                    @if ($errors->has('in_father_name'))
                      <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                          <strong>{{ $errors->first('in_father_name') }}</strong>
                      </span>
                    @endif
                    <div class="validation"></div>
                  </div>
                  
                  <div class="form-group col-md-6">
                    <label>Spouse Name</label>
                    <input type="text" name="in_spouse_name" value="{{$application->spouse_name}}" class="form-control" id="in_spouse_name" placeholder="Spouse Name" data-rule="text" data-msg="Please enter Your Spouse Name" />
                    <div class="validation"></div>
                  </div>

                  <div class="form-group{{ $errors->has('in_email') ? 'has-error' : '' }} col-md-6">
                    <label>Email Address<span class="requiredStar">*</span></label>
                    <input type="email" name="in_email" value="{{$application->email}}" class="form-control" id="in_email" placeholder="Email Address" data-rule="email" data-msg="Please enter Your email Id" required/>
                    @if ($errors->has('in_email'))
                      <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                          <strong>{{ $errors->first('in_email') }}</strong>
                      </span>
                    @endif
                    <div class="validation"></div>
                  </div>

                  <div class="form-group{{ $errors->has('in_mobile_no') ? 'has-error' : '' }} col-md-6">
                    <label>Mobile Number<span class="requiredStar">*</span></label>
                    <input type="tel" pattern="^\d{10}$" name="in_mobile_no" class="form-control" id="in_mobile_no"  placeholder="Mobile Number" min="1" max="9" data-rule="text" data-msg="Please enter Your email Address" required value="{{$application->mobile_no}}" readonly/>
                    @if ($errors->has('in_mobile_no'))
                      <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                          <strong>{{ $errors->first('in_mobile_no') }}</strong>
                      </span>
                    @endif
                    <div class="validation"></div>  

                  </div>
                      
                
                </div>  
                <h3>CONTACT INFO</h3>
                <h4>PRESENT ADDRESS</h4>
                <div class="row">
                <div class="form-group{{ $errors->has('in_present_address_line1') ? 'has-error' : '' }} col-md-6">
                    <label>Address1<span class="requiredStar">*</span></label>
                    <input type="text" name="in_present_address_line1" value="{{$application->present_address_line1}}" class="form-control" id="in_present_address_line1" placeholder="Address1" data-rule="text" data-msg="Please enter Your Address1" required/>
                    @if ($errors->has('in_present_address_line1'))
                      <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                          <strong>{{ $errors->first('in_present_address_line1') }}</strong>
                      </span>
                    @endif
                    <div class="validation"></div>

                </div>
                <div class="form-group{{ $errors->has('in_present_address_line2') ? 'has-error' : '' }} col-md-6">
                    <label>Address2<span class="requiredStar">*</span></label>
                    <input type="text" name="in_present_address_line2" value="{{$application->present_address_line2}}" class="form-control" id="in_present_address_line2" placeholder="Address2" data-rule="text" data-msg="Please enter Your Address1" required/>
                    @if ($errors->has('in_present_address_line2'))
                      <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                          <strong>{{ $errors->first('in_present_address_line2') }}</strong>
                      </span>
                    @endif
                    <div class="validation"></div>
                </div>
                <div class="form-group{{ $errors->has('in_present_address_landmark') ? 'has-error' : '' }} col-md-6">
                    <label>Nearest Landmark<span class="requiredStar">*</span></label>
                    <input type="text" name="in_present_address_landmark" value="{{$application->present_address_landmark}}" class="form-control" id="in_present_address_landmark" placeholder="Nearest Landmark" data-rule="text" data-msg="Please enter Your Nearest Landmark" required/>
                    @if ($errors->has('in_present_address_landmark'))
                      <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                          <strong>{{ $errors->first('in_present_address_landmark') }}</strong>
                      </span>
                    @endif
                    <p>(Applicant should reside in present address minimum for 6 months)</p>

                </div>

                  <div class="form-group{{ $errors->has('in_present_pincode') ? 'has-error' : '' }} col-md-6">
                    <label>Pincode<span class="requiredStar">*</span></label>
                    <input type="number" name="in_present_pincode" value="{{$application->present_pincode}}" class="form-control" id="in_present_pincode" placeholder="Pincode" pattern="[0-9]{6}" maxlength="6" data-rule="text" data-msg="Please enter Your Pincode" required/>
                    @if ($errors->has('in_present_pincode'))
                      <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                          <strong>{{ $errors->first('in_present_pincode') }}</strong>
                      </span>
                    @endif
                 </div>

                  <div class="form-group{{ $errors->has('in_present_city') ? 'has-error' : '' }} col-md-6">
                    <label>City<span class="requiredStar">*</span></label>
                    <input type="text" name="in_present_city"  class="form-control" id="in_present_city" placeholder="City" value="{{$application->present_city}}" data-rule="text" data-msg="Please enter Your City" required/>
                    @if ($errors->has('in_present_city'))
                      <span class="requiredfield">
                          <strong>{{ $errors->first('in_present_city') }}</strong>
                      </span>
                    @endif
                 </div>

                  <div class="form-group col-md-6">
                    <label>State<span class="requiredStar">*</span></label>
                    <select name="in_present_state" value="{{ old('in_present_state') }}" id="in_present_state" class="form-control  select2" required>
                        <option value="">--Select State--</option>
                        @foreach ($states as $state)
                        <option value="{{ $state->id }}" 
                          @if($state->id ==$application->present_state) selected @endif >{{ $state->name }}
                        </option>
                        @endforeach
                    </select>
                   

                 </div>

                 <div class="form-group col-md-6 col-md-pull-6">
                    <label>Country<span class="requiredStar">*</span></label>
                    <select name="in_present_country" value="{{ old('in_present_country') }}" id="in_present_country" class="form-control select2" required>
                        <option value="">--Select Country--</option>
                        @foreach ($countries as $country)
                        <option value="{{ $country->id }}" 
                         @if($country->id ==1) selected @endif>{{ $country->name }}
                       </option>
                        @endforeach
                    </select>

                    
                 </div>

                 <div class="form-group{{ $errors->has('in_police_station_name') ? 'has-error' : '' }} col-md-6" >
                    
                      
                      <label>Select Police Station <span class="requiredStar">*</span></label>

                        <select name="in_police_station_name" value="{{ old('in_police_station_name') }}" class="form-control select2" id="policestation" required>
                          <option value="">--Select PS--</option>
                          @foreach ($policestations as $policestation)
                            <option value="{{ $policestation->id }}" @if($policestation->id == $application->police_station_code) selected @endif >{{$policestation->name}}</option>
                          @endforeach
                        </select>
                        <div class="text-danger" style="padding: 10px; font-weight: bold;" id="police_id"></div>
                       
                       
                     
                  </div>
              </div>

                 <h6>Period of Staying in this Address</h6>

                   

                  <div class="row">  
                  <div class="form-group{{ $errors->has('fromMonth') ? 'has-error' : '' }}   col-md-6" >
                    <div class="row">
                       <div class="col-md-6 " >
                        <label>From Date <span class="requiredStar">*</span></label>

                        <?php 
                        $from_date = explode('-',$application->present_stay_frm_date);
                        //echo $from_date[0];
                        ?>
                       
                      

          <select id="fromMonth" name="fromMonth" value="{{ old('fromMonth') }}" class="form-control select2" required>
            
            <option value="01" <?php if ($from_date[1]== '01'){echo 'selected';}?>>January</option>
            <option value="02" <?php if ($from_date[1]== '02'){echo 'selected';}?>>February</option>
            <option value="03" <?php if ($from_date[1]== '03'){echo 'selected';}?>>March</option>
            <option value="04" <?php if ($from_date[1]== '04'){echo 'selected';}?>>April</option>
            <option value="05" <?php if ($from_date[1]== '05'){echo 'selected';}?>>May</option>
            <option value="06" <?php if ($from_date[1]== '06'){echo 'selected';}?>>June</option>
            <option value="07" <?php if ($from_date[1]== '07'){echo 'selected';}?>>July</option>
            <option value="08" <?php if ($from_date[1]== '08'){echo 'selected';}?>>August</option>
            <option value="09" <?php if ($from_date[1]== '09'){echo 'selected';}?>>September</option>
            <option value="10" <?php if ($from_date[1]== '10'){echo 'selected';}?>>October</option>
            <option value="11" <?php if ($from_date[1]== '11'){echo 'selected';}?>>November</option>
            <option value="12" <?php if ($from_date[1]== '12'){echo 'selected';}?>>December</option>
            
            
          </select>
                        

                           @if ($errors->has('fromMonth'))
                              <span class="requiredfield">
                                  <strong>{{ $errors->first('fromMonth') }}</strong>
                              </span>
                            @endif
                       </div>

                       <div class="  col-md-6">
                         <label>&nbsp;</label>
                          <select name="fromYear" value="{{ old('fromYear') }}" id="fromYear" class="form-control select2" required>
                            <option value="">Select Year</option>

                            
                            <?php
                            for($i=1947;$i<=2018;$i++){
                              ?>
                              <option value="<?php echo $i; ?>" <?php if ($from_date[0]== $i){echo 'selected';}?> ><?php echo $i; ?></option>
                              <?php
                                }
                             ?>
                          
                      
                          </select>

                           
                       </div>
                     </div>  
                  </div>

                  <div class="form-group col-md-6" >
                    <div class="row">
                       <div class="col-md-6">
                         
                          <label>To  Date<span class="requiredStar">*</span></label>
                           <?php 
                        $to_date = explode('-',$application->present_stay_to_date);
                        //echo $from_date[0];
                        ?>

                          <select name="ToMonth" value="{{ old('ToMonth') }}" id="ToMonth" class="form-control select2" required>

            <option value="01" <?php if ($to_date[1]== '01'){echo 'selected';}?>>January</option>
            <option value="02" <?php if ($to_date[1]== '02'){echo 'selected';}?>>February</option>
            <option value="03" <?php if ($to_date[1]== '03'){echo 'selected';}?>>March</option>
            <option value="04" <?php if ($to_date[1]== '04'){echo 'selected';}?>>April</option>
            <option value="05" <?php if ($to_date[1]== '05'){echo 'selected';}?>>May</option>
            <option value="06" <?php if ($to_date[1]== '06'){echo 'selected';}?>>June</option>
            <option value="07" <?php if ($to_date[1]== '07'){echo 'selected';}?>>July</option>
            <option value="08" <?php if ($to_date[1]== '08'){echo 'selected';}?>>August</option>
            <option value="09" <?php if ($to_date[1]== '09'){echo 'selected';}?>>September</option>
            <option value="10" <?php if ($to_date[1]== '10'){echo 'selected';}?>>October</option>
            <option value="11" <?php if ($to_date[1]== '11'){echo 'selected';}?>>November</option>
            <option value="12" <?php if ($to_date[1]== '12'){echo 'selected';}?>>December</option> 

                            
                          </select>

                            
                       </div>

                       <div class="col-md-6">
                         <label>&nbsp;</label>
                          <select name="ToYear" value="{{ old('ToYear') }}" id="ToYear" class="form-control select2" required>
                            <option value="">Select Year</option>
                            <?php
                            for($i=1947;$i<=2018;$i++){
                              ?>
                              <option value="<?php echo $i; ?>" <?php if ($to_date[0]== $i){echo 'selected';}?>><?php echo $i; ?></option>
                              <?php
                                }
                             ?>
                          </select>

                       </div>
                     </div>  
                  </div>

                <div class="form-group col-md-12">
                  
               </div>
               <div class="form-group col-md-12">PERMANENT ADDRESS ( <input type="checkbox" id="addressCheckbox" name="addressSame">Same as Present Address)</div>

                <div class="form-group{{ $errors->has('in_permanent_address_line1') ? 'has-error' : '' }} col-md-6">
                    <label>Address1<span class="requiredStar">*</span></label>
                    <input type="text" name="in_permanent_address_line1" value="{{$application->permanent_address_line1}}" class="form-control" id="in_permanent_address_line1" placeholder="Address1" data-rule="text" data-msg="Please enter Your Address1" required/>
                    @if ($errors->has('in_permanent_address_line1'))
                      <span class="requiredfield">
                          <strong>{{ $errors->first('in_permanent_address_line1') }}</strong>
                      </span>
                    @endif

                </div>
                <div class="form-group{{ $errors->has('in_permanent_address_line2') ? 'has-error' : '' }} col-md-6">
                    <label>Address2<span class="requiredStar">*</span></label>
                    <input type="text" name="in_permanent_address_line2" value="{{$application->permanent_address_line2}}" class="form-control" id="in_permanent_address_line2" placeholder="Address2" data-rule="text" data-msg="Please enter Your Address1" required/>
                    @if ($errors->has('in_permanent_address_line2'))
                      <span class="requiredfield">
                          <strong>{{ $errors->first('in_permanent_address_line2') }}</strong>
                      </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('in_permanent_address_landmark') ? 'has-error' : '' }} col-md-6">
                    <label>Nearest Landmark<span class="requiredStar">*</span></label>
                    <input type="text" name="in_permanent_address_landmark" value="{{$application->permanent_address_landmark}}" class="form-control" id="in_permanent_address_landmark" placeholder="Nearest Landmark" data-rule="text" data-msg="Please enter Your Nearest Landmark" required/>
                    @if ($errors->has('in_permanent_address_landmark'))
                      <span class="requiredfield">
                          <strong>{{ $errors->first('in_permanent_address_landmark') }}</strong>
                      </span>
                    @endif
                    

                </div>

                  <div class="form-group{{ $errors->has('in_permanent_pincode') ? 'has-error' : '' }} col-md-6">
                    <label>Pincode<span class="requiredStar">*</span></label>
                    <input type="number" name="in_permanent_pincode" value="{{$application->permanent_pincode}}" class="form-control" id="in_permanent_pincode" placeholder="Pincode" data-rule="text" data-msg="Please enter Your Pincode" pattern="[0-9]{6}" maxlength="6" required/>
                    @if ($errors->has('in_permanent_pincode'))
                      <span class="requiredfield">
                          <strong>{{ $errors->first('in_permanent_pincode') }}</strong>
                      </span>
                    @endif
                 </div>

                  <div class="form-group{{ $errors->has('in_permanent_city') ? 'has-error' : '' }} col-md-6">
                    <label>City<span class="requiredStar">*</span></label>
                    <input type="text" name="in_permanent_city" value="{{$application->permanent_city}}" class="form-control" id="in_permanent_city" placeholder="City" data-rule="text" data-msg="Please enter Your City" required/>
                    @if ($errors->has('in_permanent_city'))
                      <span class="requiredfield">
                          <strong>{{ $errors->first('in_permanent_city') }}</strong>
                      </span>
                    @endif
                 </div>

                  <div class="form-group{{ $errors->has('in_permanent_state') ? 'has-error' : '' }} col-md-6">
                    <label>State<span class="requiredStar">*</span></label>
                    <select name="in_permanent_state"  id="in_permanent_state" class="form-control " required>
                        <option value="">--Select State--</option>
                        @foreach ($states as $state)
                        <option value="{{ $state->id }}" @if($state->id == $application->permanent_state) selected @endif >{{ $state->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('in_permanent_state'))
                      <span class="requiredfield">
                          <strong>{{ $errors->first('in_permanent_state') }}</strong>
                      </span>
                    @endif
                 </div>

                 <div class="form-group{{ $errors->has('in_permanent_country') ? 'has-error' : '' }} col-md-6 col-md-pull-6">
                    <label>Country<span class="requiredStar">*</span></label>
                    <select name="in_permanent_country" value="{{ old('in_permanent_country') }}" id="in_permanent_country" class="form-control" required>
                        <option value="">--Select Country--</option>
                        @foreach ($countries as $country)
                        <option value="{{ $country->id }}"  @if($country->id == $application->permanent_country) selected @endif>{{ $country->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('in_permanent_country'))
                      <span class="requiredfield">
                          <strong>{{ $errors->first('in_permanent_country') }}</strong>
                      </span>
                    @endif
                 </div>

                <div class="form-group col-md-12"><P><b>ADDRESS PROOF WITH  PHOTO ID</b></P>
                  <p>Note: Attach only scanned jpg images or PDF files of your original documents. Scanned Images must be clear and document size must be less then 100kb and readable otherwise applications will be rejected</p>

                  <label>Please Select the document for PCC</label>
                  <div class="row" id="crud_table">
                    
                     <div class="form-group{{ $errors->has('doc_name') ? 'has-error' : '' }} col-md-4" >
                        <select class="form-control doc_name"  id="doc_name_type" name="doc_name[]" required>
                              <option >--Select Document Name--</option>
                              <option value="aadharCard">Aadhar Card (both side)</option>
                              <option value="drivingLience">Driving Licence(both side)</option>
                              <option value="passport">Passport</option>
                              <option value="voterId">Voter ID (both side)</option>
                        </select>
                        @if ($errors->has('doc_name'))
                          <span class="requiredfield">
                              <strong>{{ $errors->first('doc_name') }}</strong>
                          </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('doc_type') ? 'has-error' : '' }} col-md-4 ">
                      <input type="file" class="filestyle input-group-cus doc_type" name="doc_type[]" required>
                      @if ($errors->has('doc_type'))
                          <span class="requiredfield">
                              <strong>{{ $errors->first('doc_type') }}</strong>
                          </span>
                      @endif
                    </div>
                     <div class="form-group{{ $errors->has('doc_number') ? 'has-error' : '' }} col-md-3">
                      <input type="text" class="form-control doc_number doc_no" name="doc_number[]" id="doc_no" placeholder="Input Number required" required>
                      @if ($errors->has('doc_number'))
                          <span class="requiredfield">
                              <strong>{{ $errors->first('doc_number') }}</strong>
                          </span>
                      @endif
                    </div>

                  </div>
                   
                      <div align="right">
                        <button type="button" name="add" id="add" class="btn btn-success btn-xs">+</button>
                      </div>
                 </div>

               </div>

                <h4>PURPOSE</h4>
                 <label>Please select the purpose for PCC<span class="requiredStar">*</span></label>

                 

                <div class="col-md-12">
                  <div class="row">
                    
                    <div class="form-group{{ $errors->has('in_pcc_purpose') ? 'has-error' : '' }} col-md-4">
                      <select class="form-control " name="in_pcc_purpose" id="purposeType" required>
                              <option >--Select Purpose--</option>
                              <option value="Visa/Immigration" @if($application->pcc_purpose =='Visa/Immigration') selected @endif>Visa/Immigration</option>
                              <option value="employment" @if($application->pcc_purpose =='employment') selected @endif>Employment</option>
                              <option value="others" @if($application->pcc_purpose == 'others') selected @endif>Others</option>
                      </select>
                      @if ($errors->has('in_pcc_purpose'))
                          <span class="requiredfield">
                              <strong>{{ $errors->first('in_pcc_purpose') }}</strong>
                          </span>
                      @endif
                    </div>
                    <div class="form-group col-md-3" id="otherType" style="display:none;">
                      <input type="text" class="form-control" name="new_purpose" placeholder="Specify Other Type"/>
                    </div>
                  </div>
                </div>
                  


                  <!--h3>PURPOSE SPECIFIC DETAILS</h3>
                  <H4>RECORD VERIFICATION</H4>
                  <label>Please select the purpose for PCC<span class="requiredStar">*</span></label>
                  <div class="form-group{{ $errors->has('in_pcc_virification_for') ? 'has-error' : '' }} col-md-3">
                    <input type="checkbox" name="in_pcc_virification_for" value="crime_record" > Crime Record<br>
                    <input type="checkbox" name="in_pcc_virification_for" value="antecedents" > Antecedents<br>
                    @if ($errors->has('in_pcc_virification_for'))
                          <span class="requiredfield">
                              <strong>{{ $errors->first('in_pcc_virification_for') }}</strong>
                          </span>
                    @endif
                   
                  </div-->
                 

                   <div class="row">
               <div class="form-group col-md-9 pos-left">
                
                <!--button type="submit" name="save" id="save" class="btn btn-danger btn-xs">Save as Draft</button--></div>
                  <div class="form-group col-md-2 pull-right"><small>PCC process fee Rs.300/-</small><button type="submit" name="save" id="save" value="Save and Process" class="btn btn-success btn-xs" >Save and Proceed</button></div>
             </div>
 
             </div>
            
            </div>
        </form>
      </div>

    </div>
    </section><!-- #contact -->

  </main>

  <!--==========================
    Footer
  ============================-->
  <footer id="footer">
    <div class="container">
      <div class="copyright">
        &copy; Copyright <strong>NIC</strong>. All Rights Reserved
      </div>
      <div class="credits">
        <!--
          All the links in the footer should remain intact.
          You can delete the links only if you purchased the pro version.
          Licensing information: https://bootstrapmade.com/license/
          Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/buy/?theme=Reveal
        -->
       
      </div>
    </div>
  </footer><!-- #footer -->

  <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

  <!-- JavaScript Libraries -->
  <script src="{{ asset("frontend/lib/jquery/jquery.min.js") }}"></script>
  <script src="{{ asset("js/select2.full.min.js") }}"></script>
  <script src="{{ asset("js/validation.js") }}"></script>
  <script src="{{ asset("frontend/lib/jquery/jquery-migrate.min.js") }}"></script>
  <script src="{{ asset("frontend/lib/bootstrap/js/bootstrap.bundle.min.js") }}"></script>
  <script src="{{ asset("frontend/lib/easing/easing.min.js") }}"></script>
  <script src="{{ asset("frontend/lib/superfish/hoverIntent.js") }}"></script>
  <script src="{{ asset("frontend/lib/superfish/superfish.min.js") }}"></script>
  <script src="{{ asset("frontend/lib/wow/wow.min.js") }}"></script>
  <script src="{{ asset("frontend/lib/owlcarousel/owl.carousel.min.js") }}"></script>
  <script src="{{ asset("frontend/lib/magnific-popup/magnific-popup.min.js") }}"></script>
  <script src="{{ asset("frontend/lib/sticky/sticky.js") }}"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD8HeI8o-c1NppZA-92oYlXakhDPYR7XMY"></script>
  


  <!-- Template Main Javascript File -->
  <script src="{{ asset("frontend/js/main.js") }}"></script>

  <!-- Bootstrap file upload Javascript -->
<script type="text/javascript" src="{{ asset("frontend/js/bootstrap-filestyle.min.js") }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>

<script>
  $(document).ready(function(){
     $('.doc_name').change(function() {
       var value = $(this).val();
        if(value =='aadharCard'){
        $(this).find(function(){
         
           $(this).find('.doc_no').attr("placeholder", "XXXX XXXX XXXX");
            $(this).find('.doc_no').attr('maxlength','14');
          //$(this).find('.doc_no').mask("99/99/9999:99:99:99",{placeholder:"xx/xx/xxxx:xx:xx:xx"});
           //$('.doc_no').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
          //$(this).find('.doc_no').mask("9999 9999 9999",{placeholder:"XXXX XXXX XXXX"});
        });
      }
      if(value =='drivingLience'){
        $(this).find(function(){
         $(this).find('.doc_no').attr("placeholder", "Driving Lience Number Required");
        });
      }
      if(value =='passport'){
        $(this).find(function(){
         $(this).find('.doc_no').attr("placeholder", "PassPort Number Required ");
        });
      }
      if(value =='voterId'){
        $(this).find(function(){
         $(this).find('.doc_no').attr("placeholder", "Voter Id Number Required");
        });
      }
         
        
      });
       
  });
 
</script>


<script>
    $(document).ready(function(){
      function realURL(input){
        if(input.files && input.files[0]){
          var reader = new FileReader();

         
          reader.onload = function(e){
            var FileSize = input.files[0].size / 1024 ; 
            if (FileSize > 40) {
              alert('File size exceeds 40 KB');
            } else {
              $('#imgbanner').attr('src', e.target.result);
              $('#imgsize').text("Image Size : " + Math.ceil(FileSize) + ' KB');
             }
          }
           reader.readAsDataURL(input.files[0]);
          }   
      }

      $('#user_img').change(function(){
        realURL(this);
      })
    });
</script>

<script>
  
      $('#ToYear').change(function(){
        //alert("data");
        var fromMonth = $('#fromMonth').val();
        var fromYear = $('#fromYear').val();
        var formDateMonth_date  = fromYear +"-"+fromMonth+"-"+"01";
        var formDateMonth = new Date(formDateMonth_date);


        


        var ToMonth = $('#ToMonth').val();
        var ToYear = $('#ToYear').val();
        var ToDateMonth_date  = ToYear +"-"+ToMonth+"-"+"01";
        var ToDateMonth = new Date(ToDateMonth_date);
       
        var diff_date =  ToDateMonth - formDateMonth;
        if(diff_date > 15724800000){
          
        }else{

          alert(" Today value must be greater than from date value ");
         
          //document.registration.ToYear.focus();
         //$('$ToYear').val("");
         
          
        }

       
      });




</script>









</body>
</html>
