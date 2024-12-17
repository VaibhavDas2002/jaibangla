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
  <section id="contact" class="wow">
   	 <div class="container">
   	 	
   	 	<div class="row">
	        <div class="col-md-6">
	          <h3 class="login-hd">Check PCC status</h3>
	          <div class="btn-detailsin">
               <div class="col-md-12">
			          <form action="{{url('certificateStatus')}}"  method="post" name="frm_pccstatus" id="frm_pccstatus">
                  {{ csrf_field() }}

                  @if(Session::has('message_status'))
                  <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message_status') }}</p>
                  @endif

                  

                

                  <div class="row">
                   
			              <div class="col-xs-12 col-sm-8 col-md-8">
                      <div class="form-group{{ $errors->has('pccstatus') ? ' has-error' : '' }}" >
			               
			                  <input type="text" placeholder="PCC Number OR Application Number" class="form-control contact-input user_id required" id="pccstatus" name="pccstatus" value="{{old('pccstatus')}}" required>

                        @if ($errors->has('pccstatus'))
                          <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                              <strong>{{ $errors->first('pccstatus') }}</strong>
                          </span>
                        @endif
			                </div>
			              </div>

                    
                    
			              <div class="col-md-4 col-xs-12">
			                <div class="form-group">
			                  <input type="submit" name="btn_pccstatus" class="btn btn-primary black" value="Search" id="btn_pccstatus">
			                </div>
			              </div>
                  </div>

			              <div class="col-xs-12">
			              	<div class="declare">
			                 <i>Enter your PCC Number OR Application Number, to check the current status.</i>
			                </div>
			                <div id="status_response" style="width: 100%; float: left;"></div>
			              </div>
                   

                    <div class="col-xs-12" >
                    
                      @if($applications != '')
                     
                      
                        <table class="table table-responsive" style="color: green; " cellpadding="0"  cellspacing="0" >
                            <tr style="background: #747474;color: #fff;">
                              <th >Applicant Name</th>
                              <th  >Purpose</th>
                              <th  >Status</th>
                              <th  >Valid Upto</th>
                            </tr>
                           
                            <tr style="background: #d2d2d2; font-weight: bold;">
                              <td>{{$applications->first_name }} {{$applications->middle_name}} {{$applications->last_name}}</td>
                              <td>{{$applications->pcc_purpose}}</td>
                                <td>
                                 Valid!
                              </td>
                              <td>{{$applications->valid_upto}}</td>
                            </tr>
                           
                          </table>
                        
                       @endif

                       
                        @if($applications == '' && $applications != null)
                        <div class="table-responsive">
                        <table class="table" style="color: red"  cellpadding="0"  cellspacing="0" >
                            <tr style="background: #d2d2d2; font-weight: bold;">
                              <td>Pcc Invalid !</td>
                            </tr>
                          </table>
                        </div>
                        @endif
                     
                    </div>
			          </form>

                
               
              
                

	        	  </div>
	          </div>
	        </div>
        <div class="col-md-6">
	        <h3 class="login-hd">Log In / Registration</h3>

	       
			   <div class="btn-detailsin">
        	<div class="col-md-12">
           <form action="{{url('/sendOtp')}}" method="post" name="frm_reg" id="frm_reg">
              {{ csrf_field() }}

               @if(Session::has('message'))
                <div class="alert {{ Session::get('alert-class', 'alert-info') }}"> {{ Session::get('message') }}</div>
               @endif

               @if(Session::has('message_resent'))
                <div class="alert {{ Session::get('alert-class', 'alert-info') }}"> {{ Session::get('message_resent') }}</div>
               @endif

              <div class="row">
                <div class="col-xs-12 col-sm-8 col-md-8">
                  <div class="form-group{{ $errors->has('mobileno') ? ' has-error' : '' }}" >
                    <input type="text" placeholder="Mobile No. (10 Digits)" class="form-control contact-input user_id required valid" maxlength="10" id="mobile_no" name="mobileno" required="" value='@if(Session::has('message')){{ Session::get('mobile') }}  @endif'  @if(Session::has('message')) readonly @endif>
                        @if ($errors->has('mobileno'))
                          <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                              <strong>{{ $errors->first('mobileno') }}</strong>
                          </span>
                        @endif
                  </div>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-4">
                  <div class="form-group">
                    <input id="btn_sendotp" type="submit" name="btn_sendotp" class="btn btn-primary black" @if(Session::has('message')) disabled @endif  value="Send OTP">
                  </div>
                </div>
                <br>
                  <div class="col-sm-12" >
                   <div style="color: #ff0000;"  class="form-group{{ $errors->has('captcha') ? ' has-error' : '' }}">
                     
                      <div class="row">
                          <div class="captcha col-sm-5"  >
                          <span>{!! captcha_img() !!}</span>
                          <button type="button" class="btn btn-primary btn-refresh"><i class="fa fa-refresh"></i></button>
                          </div>
                          <div class="col-sm-4"  style="margin-left: -45px;">
                             <input id="captcha" type="text" class="form-control" placeholder="Enter Captcha" name="captcha">
                           </div>
                         </div>

                          @if ($errors->has('captcha'))
                              <span class="help-block">
                                  <strong>{{ $errors->first('captcha') }}</strong>
                              </span>
                          @endif
                      </div>
                  </div>
              </div>
                <div class="col-xs-12">
                  <div class="declare">
                    <label class="custom-control custom-checkbox">
                      <input type="checkbox" name="reg[disclaimer]" id="reg_disclaimer" value="1" required="required" class="valid" @if(Session::has('message')) checked @endif>
                      <span class="custom-control-indicator"></span> <span class="custom-control-description"><i>I hereby declare that the mobile no. provided is registered in my name. Information and documents being furnished by me for the purpose of police clearance certificate are correct and authentic to the best of my knowledge.</i></span>
                       </label>
                  </div>
                </div>
              
                <div id="submit_div"></div>
                
            </form>
          </div>
          @if(Session::has('message'))
          <div class="col-md-12 veryOtp" >
              <form action="{{url('/verifyOtp')}}" method="post" name="frm_reg" id="frm_reg" >
                {{ csrf_field() }}

                <div id="verifyotp_div">
                 <div class="col-xs-12 col-sm-8 col-md-8">
                    <div class="form-group{{ $errors->has('otp') ? ' has-error' : '' }}">
                      <input type="text" placeholder="Enter OTP" class="form-control input-lg" maxlength="6" id="otp" name="otp" style="float: left;width: 62%;border-radius:5px 0 0 5px">
                       @if ($errors->has('otp'))
                          <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                              <strong>{{ $errors->first('otp') }}</strong>
                          </span>
                        @endif
                      <input type="submit" name="varify" value="Verify" class="btn btn-primary black"   style="border-radius:0 5px 5px 0">

                    </div>
                  </div>
                  
                </div>
                </form>
                <!--form action="{{url('/resendOtp')}}" method="post" name="frm_reg" id="frm_reg" >
                {{ csrf_field() }}
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <div class="form-group "> 
                      <input type="submit" value="Resend" class="btn btn-primary primary" >
                    </div>
                  </div>
                </form-->
              
          </div>
          @endif
          </div>
        </div>
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

  <!-- Template Main Javascript File -->
  <script src="{{ asset("frontend/js/main.js") }}"></script>

  <!-- Bootstrap file upload Javascript -->
<script type="text/javascript" src="{{ asset("frontend/js/bootstrap-filestyle.min.js") }}"></script>
<script type="text/javascript">
$(".btn-refresh").click(function(){
  $.ajax({
     type:'GET',
     url:'refresh_captcha',
     success:function(data){
        $(".captcha span").html(data.captcha);
     }
  });
});


</script>




</body>
</html>
