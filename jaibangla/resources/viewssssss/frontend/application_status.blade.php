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
          <li><a href="#about">About Us</a></li>
          <li><a href="#services">Services</a></li>
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

    <section id="contact" class="wow">

    <div class="container box_inter">
       <!-- change start here -->
       <div class="row">
       
          {{ csrf_field() }}
        <div class="col-md-12" style="margin-top: 15px;">

          <div class="alert alert-success">
            <h4>{{$message}}. Your application reference no : <strong>{{$ref_no}}</strong> .{{$grips}} </h4>

            

           
          </div>

          <div class="step1">
            <h5>1. Open Government Payment Website <a href="https://wbifms.gov.in/GRIPS" target="_blank">https://wbifms.gov.in/GRIPS</a> And Click on Revenue Payment Button </h5>
            <img src="{{ asset('grips_img/1.png') }}" width="70%" height="" alt="Government Payment Website">
          </div>
          <p/>
          <div class="step2">
            <h5>2. Select Department/Directorate: Home(Police) and Select Service : Other items Other fees and Press Process Button</h5>
            <img src="{{ asset('grips_img/2.png') }}" width="70%" height="" alt="Government Payment Website">
          </div>
          <p/>
           <div class="step3">
            <h5>3. Fillup Name, address, Mobile number and Other details and Select User Type: Depositor and Payment Mode: Counter Payment and REF. No. lastly click on Process Button. 
            </h5>
            <img src="{{ asset('grips_img/3.png') }}" width="70%" height="" alt="Government Payment Website">
          </div>
          <p/>
          <div class="step4">
            <h5>4.  fill up  with <strong>SERVICE TYPE :</strong> Other Receipts <strong>HEAD OF ACCOUNT DESCRIPTION : </strong>Other Items-Others Receipts and input  with amount lastly click on submit button</h5>
            <img src="{{ asset('grips_img/4.png') }}" width="70%" height="" alt="Government Payment Website">
          </div>
          <p/>
          <div class="step5">
            <h5>5. Check Details And click On Confirm Button</h5>
            <img src="{{ asset('grips_img/5.png') }}" width="70%" height="" alt="Government Payment Website">
          </div>
          <p/>
          <div class="step6">
            <h5>6. Click On your Payment Bank</h5>
            <img src="{{ asset('grips_img/6.png') }}" width="70%" height="" alt="Government Payment Website">
          </div>
          <p/>
          <div class="step7">
            <h5>7. Click On <u>GO TO  State Bank of India  (For Counter Payment)</u></h5>
            <img src="{{ asset('grips_img/7.png') }}" width="70%" height="" alt="Government Payment Website">
          </div>

          <p/>
          <div class="step8">
            <h5>8. Check  Payment details. Put image varification code  and Click on Confirm Button</u></h5>
            <img src="{{ asset('grips_img/8.png') }}" width="70%" height="" alt="Government Payment Website">
          </div>

          <p/>
          <div class="step9">
            <h5>9. 
          Print the Challan and produce it to any branch of the selected bank for payment.Please Carry 2 copies of this e-Challan to Bank.</u></h5>
            <img src="{{ asset('grips_img/9.png') }}" width="70%" height="" alt="Government Payment Website">
          </div>
          <p/>
          <div class="step10">
            Take a download PDF Document copy . 
          </div>
          <p/>
          <div class="col-md-6 offset-md-3" style="margin-bottom: 20px;"><button type="submit" name="submit" id="submit" class="btn btn-success">Accept</button></div>



        </div>
      

       </div>
       <!-- change ends here -->
    </div>
    </section><!-- #contact -->

  </main>

  <!--==========================
    Footer
  ============================-->
  <footer id="footer">
    <div class="container">
      <div class="copyright">
        &copy; Copyright <strong><a href="#">NIC</a></strong>. All Rights Reserved
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

<script type="text/javascript">
  window.history.forward();
</script>





</body>
</html>
