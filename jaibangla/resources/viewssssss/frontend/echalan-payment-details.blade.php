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

  <section id="contact" class="wow ">

    <div class="container box_inter">
	  <div class="form">
	      <div id="sendmessage">Your message has been sent. Thank you!</div>
	      <div id="errormessage"></div>
	      <form action="{{url('/application/echalanSubmit/{ref_no}')}} " method="POST" role="form" class="contactForm" enctype="multipart/form-data"  >
	      	{{ csrf_field() }}
	          <div class="row">
	             <div class="col-md-12">
	              <h3>E-chalan Details for Application no {{$application_no}}</h3>
	                <h4>BASIC INFO</h4>
	                <div class="form-row">

	                  <div class="form-group col-md-6">
	                    <label>GRN</label>
	                    <input type="text" name="grn" class="form-control" id="grn" placeholder="Government Reference Number" data-rule="text" data-msg="Please enter Government Reference Number" required/>
	                    <div class="validation"></div>
	                  </div>

	                  <div class="form-group col-md-6">
	                    <label>Payment Mode</label>
                      <select name="paymentMode" class="form-control" id="paymentMode">
                        <option>--Select Payment Type--</option>
                        <option value="Online Payment">Online Payment</option>
                      </select>
	                    <!--input type="text" name="paymentMode" class="form-control" id="paymentMode" placeholder="Payment Mode" data-rule="text" data-msg="Please enter Payment Mode" required />
	                    <div class="validation"></div-->
	                  </div>

	                  <div class="form-group col-md-6">
	                    <label>GRN Date</label>
	                     <input type="date" class="form-control label-floating is-empty" name="GRNDate" id="GRNDate" placeholder="GRN Date" data-rule="required" data-msg="This field is required" required/>
	                  </div>

	                  <div class="form-group col-md-6">
	                    <label>Bank Code</label>
	                    <input type="text" name="bankCode" class="form-control" id="bankCode" placeholder="Bank Code Number" data-rule="text" data-msg="Please enter Bank Code Number" required/>
	                    <div class="validation"></div>
	                  </div>

	                  <div class="form-group col-md-6">
	                    <label>BRN</label>
	                    <input type="text" name="brn" class="form-control" id="brn" placeholder="Bank Reference Number" data-rule="text" data-msg="Please enter Bank Reference Number" required/>
	                    <div class="validation"></div>
	                  </div>

	                  <div class="form-group col-md-6">
	                    <label>BRN date</label>
	                    
	                    <input type="text" name="brnDateTime" class="form-control" id="brnDateTime" data-rule="text" data-msg="Please enter Bank Reference Number Date and Time" required/>
	                   
	                  </div>
	                  <div class="form-group col-md-12 col-md-push-3">
                    	<label>Please Upload e-Challan Generated from GRIPS portal</label>
                      	<input type="file" name="echallan" id="echallan" class="filestyle " >
                      	<p>(Document must be of Pdf or JPG/JPEG/PNG format & less 200kb)</p>
                       </div>
	                  <input type="hidden" name="application_no" id="application_no" value="{{$application_no}}">
	                  <div class="col-md-4 offset-md-5" style="margin-bottom: 20px;"><button type="submit" name="submit" id="submit" class="btn btn-success">Submit</button></div>
	                  
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
  <script src="{{ asset("frontend/lib/jquery/jquery-migrate.min.js") }}"></script>
  <script src="{{ asset("frontend/lib/bootstrap/js/bootstrap.bundle.min.js") }}"></script>
  <script src="{{ asset("frontend/lib/easing/easing.min.js") }}"></script>
  <script src="{{ asset("frontend/lib/superfish/hoverIntent.js") }}"></script>
  <script src="{{ asset("frontend/lib/superfish/superfish.min.js") }}"></script>
  <script src="{{ asset("frontend/lib/wow/wow.min.js") }}"></script>
  <script src="{{ asset("frontend/lib/owlcarousel/owl.carousel.min.js") }}"></script>
  <script src="{{ asset("frontend/lib/magnific-popup/magnific-popup.min.js") }}"></script>
  <script src="{{ asset("frontend/lib/sticky/sticky.js") }}"></script>
  <script src="{{ asset("frontend/lib/jquery/map_api.js") }}"></script>
  
  
  

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


<script type="text/javascript">
  window.history.forward();
</script>






</body>
</html>
