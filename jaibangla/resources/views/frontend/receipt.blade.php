<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PCC</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="keywords">
  <meta content="" name="description">

  <!-- Favicons -->
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("frontend/img/favicon.ico") }}">
 

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i|Raleway:300,400,500,700,800|Montserrat:300,400,700" rel="stylesheet">

  <!-- Bootstrap CSS File -->
  <link href="{{ asset("frontend/lib/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet">

  <!-- Libraries CSS Files -->
  <link href="{{ asset("frontend/lib/font-awesome/css/font-awesome.min.css") }}" rel="stylesheet">
  <link href="{{ asset("frontend/lib/animate/animate.min.css") }}" rel="stylesheet">
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
        <img src="{{ asset("frontend/img/Bidhannagar_Police_logo.png") }}" alt="Bidhan Nagar City Police" width="80" />
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
 

  <main id="main">

  <section id="contact" class="wow fadeInUp">
     

   

    <div class="container">
       <div class="form">
        
        <div id="errormessage"></div>        
          <div class="row">
             <div class="col-md-12">
             
                <h4>BASIC INFO</h4>
                <div class="form-row">
                  
                  <div class="form-group col-md-6">
                    <label><b>Application No :</b></label>
                    {{$application->application_id}} 
                    
                  </div>

                  <div class="form-group col-md-6">
                    <label><b>Name :</b></label>
                    {{ucfirst(trans($application->first_name))}} {{ucfirst(trans($application->middle_name))}} {{ucfirst(trans($application->last_name))}}
                    
                  </div>
                  
                 
                </div>
                <div class="row">

                <div class="form-group col-md-3">
                    <label><b>Gender :</b></label>
                    {{ucfirst(trans($application->gender))}}
                </div>

                  <div class="form-group col-md-3">
                    <label><b>Date Of Birth :</b></label>
                        {{$application->dob}}
                  </div>
                  <div class="form-group col-md-3">
                    <label><b>Nationality :</b></label>
                    {{$application->nationality}}
                  </div>
                   <div class="form-group col-md-3">
                    <label><b>Father's Name:</b></label>
                    {{ucfirst(trans($application->father_name))}}
                  </div>
                  
                  

                  <div class="form-group col-md-6">
                    <label><b>Email Address:</b></label>
                    {{$application->email}}
                  </div>

                  <div class="form-group col-md-6">
                    <label><b>Mobile Number :</b> </label>
                    {{$application->mobile_no}}
                  </div>
                      
                
                </div>  
                
                <h4>PRESENT ADDRESS</h4>
                <div class="row">
                <div class="form-group col-md-12">
                    <label><b>Address:</b>{{$application->present_address_line1}} {{$application->present_address_line2}}</label>

                    
                </div>
                
                 <div class="form-group col-md-4">
                    <label><b>City :</b></label>
                    {{$application->present_city}} 
                 </div>

                  <div class="form-group col-md-4">
                    <label><b>State :</b></label>
                    West Bangal
                 </div>

                 <div class="form-group col-md-4">
                    <label><b>Country :</b></label>
                    India
                 </div>
                </div>

                

                
                <div class="row">  

                <div class="form-group col-md-4">
                  <label><b>Police Station :</b></label>
                  {{$application->police_station_name}}  
                 
               </div>
               <div class="form-group col-md-12"><b>PERMANENT ADDRESS :</b></div>
               
               <div class="form-group col-md-12">
                    
                 </div>
                 <label><b>Address:</b>{{$application->permanent_address_line1}} {{$application->permanent_address_line2}}</label>

               </div>
               
                <h4>PURPOSE</h4>
                 <div class="row">
                  <div class="form-group col-md-4">
                  <label><b>Purpose for Varification :</b></label>
                  {{$application->pcc_purpose}}                  
                  </div>                  
                </div>

                 
                 
                  

                  <h4>Payment Info</h4>
                  <div class="row">                 
                  
                   <div class="form-group col-md-4">
                      <label><b>Transaction Ref No :</b></label>
                      {{$txnRefNo}}
                   </div>

                    <div class="form-group col-md-4">
                      <label><b>Status :</b></label>
                      {{$status}}
                   </div>

                   <div class="form-group col-md-4">
                      <label><b>Amount Paid :</b></label>
                      {{$amount}}
                   </div>
                  </div>
                  

                   <div class="row">
               <div class="form-group col-md-10"></div>
                  <div class="form-group col-md-2"><button type="button" name="add" id="add" class="btn btn-success btn-xs" onclick="window.print();">Print</button></div>
             </div>
 
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
  <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js"></script>
  <script>
		$(document).ready(function($){
			
			$('#brnDateTime').mask("99/99/9999:99:99:99",{placeholder:"xx/xx/xxxx:xx:xx:xx"});
		});
	</script>





</body>
</html>
