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

      <nav id="nav-menu-container">
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
      </nav><!-- #nav-menu-container -->
    </div>
  </header><!-- #header -->

  <!--==========================
    Intro Section
  ============================-->
 

  <main id="main">

    <section id="contact" class="wow fadeInUp">

    <div class="container">
       <div class="form">
        <div id="sendmessage">Your message has been sent. Thank you!</div>
        
        <form action="{{url('/application/save')}}" method="post" role="form" class="contactForm" enctype="multipart/form-data" >
          {{ csrf_field() }}
          <div class="row">
             <div class="col-md-12">
              <h3>APPLICATION FORM</h3>
                <h4>BASIC INFO</h4>
                <div class="form-row">

                  <div class="form-group col-md-4">
                    <label>First Name</label>
                    <input type="text" name="in_first_name" class="form-control" id="in_first_name" placeholder="First Name" data-rule="text" data-msg="Please enter Your First Name" required />
                    <div class="validation"></div>
                  </div>
                  <div class="form-group col-md-4">
                    <label>Middle Name</label>
                    <input type="text" class="form-control" name="in_middle_name" id="in_middle_name" placeholder="Middle Name" data-rule="text" data-msg="Please enter Your Middle Name" />
                    <div class="validation"></div>
                  </div>

                  <div class="form-group col-md-4">
                    <label>Last Name</label>
                    <input type="text" class="form-control" name="in_last_name" id="in_last_name" placeholder="Last Name" data-rule="text" data-msg="Please enter Your Last Name" required/>
                    <div class="validation"></div>
                  </div>
                </div>
                <div class="row">
                  <!--
                  <div class="form-group col-md-6">
                      <label>Passport Size Photo</label>
                      <input type="file" class="form-control" name="photo" id="photo" placeholder="Photo"  />
                      <p>(Photo must be of 3.5cm X 4.5cm color & less 40kb)</p>
                      <div class="validation"></div>
                  </div>-->
                   <div class="form-group col-md-6 col-md-push-3">
                    <label>Passport Size Photo</label>
                      <input type="file" name="user_img" id="user_img" class="filestyle " >
                      <p>(Photo must be of 3.5cm X 4.5cm color & less 40kb)</p>
                    </div>
                    



                   <div class="form-group col-md-6">
                      <label>Gender</label>
                      <select name="in_gender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="m">Male</option>
                        <option value="f">Female</option>
                      </select>
                  </div>

                  <div class="form-group col-md-6">
                        <label>Date Of Birth</label>
                          <input type="date" class="form-control label-floating is-empty" name="in_dob" id="date" placeholder="Date" data-rule="required" data-msg="This field is required" required/>
                          <div class="validation"></div>
                        
                  </div>
                  <div class="form-group col-md-6">
                    <label>Nationality</label>
                    <input type="text" name="in_nationality" class="form-control" id="in_nationality" placeholder="Nationality" data-rule="text" data-msg="Please enter Your Nationality Name" required/>
                    <div class="validation"></div>
                  </div>

                   <div class="form-group col-md-6">
                    <label>Father's Name</label>
                    <input type="text" name="in_father_name" class="form-control" id="in_father_name" placeholder="Father's Name" data-rule="text" data-msg="Please enter Your Father's Name" required/>
                    <div class="validation"></div>
                  </div>
                  
                  <div class="form-group col-md-6">
                    <label>Spouse Name</label>
                    <input type="text" name="in_spouse_name" class="form-control" id="in_spouse_name" placeholder="Spouse Name" data-rule="text" data-msg="Please enter Your Spouse Name" required/>
                    <div class="validation"></div>
                  </div>

                  <div class="form-group col-md-6">
                    <label>Email Address</label>
                    <input type="email" name="in_email" class="form-control" id="in_email" placeholder="Email Address" data-rule="email" data-msg="Please enter Your email Id" required/>
                    <div class="validation"></div>
                  </div>

                  <div class="form-group col-md-6">
                    <label>Mobile Number</label>
                    <input type="tel" pattern="^\d{10}$" name="in_mobile_no" class="form-control" id="in_mobile_no" placeholder="Mobile Number" min="1" max="9" data-rule="text" data-msg="Please enter Your email Address" required/>
                    <div class="validation"></div>  

                  </div>
                      
                
                </div>  
                <h3>CONTACT INFO</h3>
                <h4>PRESENT ADDRESS</h4>
                <div class="row">
                <div class="form-group col-md-6">
                    <label>Address1</label>
                    <input type="text" name="in_present_address_line1" class="form-control" id="in_present_address_line1" placeholder="Address1" data-rule="text" data-msg="Please enter Your Address1" required/>
                    <div class="validation"></div>

                </div>
                <div class="form-group col-md-6">
                    <label>Address2</label>
                    <input type="text" name="in_present_address_line2" class="form-control" id="in_present_address_line2" placeholder="Address2" data-rule="text" data-msg="Please enter Your Address1" required/>
                    <div class="validation"></div>
                </div>
                <div class="form-group col-md-6">
                    <label>Nearest Landmark</label>
                    <input type="text" name="in_present_address_landmark" class="form-control" id="in_present_address_landmark" placeholder="Nearest Landmark" data-rule="text" data-msg="Please enter Your Nearest Landmark" required/>
                    <div class="validation"></div>
                    <p>(Applicant should reside in present address minimum for 6 months)</p>

                </div>

                  <div class="form-group col-md-6">
                    <label>Pincode</label>
                    <input type="text" name="in_present_pincode" class="form-control" id="in_present_pincode" placeholder="Pincode" data-rule="text" data-msg="Please enter Your Pincode" required/>
                    <div class="validation"></div>
                 </div>

                  <div class="form-group col-md-6">
                    <label>City</label>
                    <input type="text" name="in_present_city" class="form-control" id="in_present_city" placeholder="City" data-rule="text" data-msg="Please enter Your City" required/>
                    <div class="validation"></div>
                 </div>

                  <div class="form-group col-md-6">
                    <label>State</label>
                    <select name="in_present_state" id="in_present_state" class="form-control" required>
                        <option value="">--Select State--</option>
                        @foreach ($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                 </div>

                 <div class="form-group col-md-6 col-md-pull-6">
                    <label>Country</label>
                    <select name="in_present_country" id="in_present_country" class="form-control" required>
                        <option value="">--Select Country--</option>
                        @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                 </div>
              </div>

                 <h6>Period of Staying in this Address</h6>

                
                <div class="row">  
                  <div class="form-group col-md-6" >
                    <div class="row">
                       <div class="col-md-6">
                         
                          <label>From Date</label>
                          <input type="date" class="form-control label-floating is-empty" name="in_present_stay_frm_date" id="date" placeholder="Date" data-rule="required" data-msg="This field is required" required/>
                       </div>

                        <div class="col-md-6">
                          <label>To  Date</label>
                         <input type="date" class="form-control label-floating is-empty" name="in_present_stay_to_date" id="date" placeholder="Date" data-rule="required" data-msg="This field is required" required/>
                       </div>

                       
                     </div>  
                  </div>

                  <div class="form-group col-md-6" >
                    <div class="row">
                      
                      <label>Select Police Station </label>

                        <select name="in_police_station_name" class="form-control" required>
                          <option value="">--Select PS--</option>
                          @foreach ($policestations as $policestation)
                            <option value="{{ $policestation->id }}">{{ $policestation->name }}</option>
                          @endforeach
                        </select>
                       
                     </div>  
                  </div>



                <div class="form-group col-md-12">
                  
               </div>
               <div class="form-group col-md-12">PERMANENT ADDRESS ( <input type="checkbox" name="addressSame">Same as Present Address)</div>

                <div class="form-group col-md-6">
                    <label>Address1</label>
                    <input type="text" name="in_permanent_address_line1" class="form-control" id="address1" placeholder="Address1" data-rule="text" data-msg="Please enter Your Address1" required/>
                    <div class="validation"></div>

                </div>
                <div class="form-group col-md-6">
                    <label>Address2</label>
                    <input type="text" name="in_permanent_address_line2" class="form-control" id="address2" placeholder="Address2" data-rule="text" data-msg="Please enter Your Address1" required/>
                    <div class="validation"></div>
                </div>
                <div class="form-group col-md-6">
                    <label>Nearest Landmark</label>
                    <input type="text" name="in_permanent_address_landmark" class="form-control" id="nearestLandmark" placeholder="Nearest Landmark" data-rule="text" data-msg="Please enter Your Nearest Landmark" required/>
                    <div class="validation"></div>
                    

                </div>

                  <div class="form-group col-md-6">
                    <label>Pincode</label>
                    <input type="text" name="ppincode" class="form-control" id="pincode" placeholder="Pincode" data-rule="text" data-msg="Please enter Your Pincode" required/>
                    <div class="validation"></div>
                 </div>

                  <div class="form-group col-md-6">
                    <label>City</label>
                    <input type="text" name="pcity" class="form-control" id="city" placeholder="City" data-rule="text" data-msg="Please enter Your City" required/>
                    <div class="validation"></div>
                 </div>

                  <div class="form-group col-md-6">
                    <label>State</label>
                    <select name="ddl_state" id="ddl_state" class="form-control" required>
                        <option value="">--Select State--</option>
                        @foreach ($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                 </div>

                 <div class="form-group col-md-6 col-md-pull-6">
                    <label>Country</label>
                    <select name="ddl_country" id="ddl_country" class="form-control" required>
                        <option value="">--Select Country--</option>
                        @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                 </div>

                <div class="form-group col-md-12"><P><b>ADDRESS PROOF WITH  PHOTO ID</b></P>
                  <p>Note: Attach only scanned jpg images or PDF files of your original documents. Scanned Images must be clear and readable otherwise applications will be rejected</p>

                  <label>Please Select the document for PCC</label>
                  <div class="row" id="crud_table">
                    
                     <div class="form-group col-md-4" >
                        <select class="form-control doc_name" name="doc_name" required>
                              <option value="aadharCard">Aadhar Card (both side)</option>
                              <option value="drivingLience">Driving Licence(both side)</option>
                              <option value="passport">Passport</option>
                              <option value="voterId">Voter ID (both side)</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4 ">
                      <input type="file" class="filestyle input-group-cus doc_type" required>
                    </div>
                     <div class="form-group col-md-3">
                      <input type="text" class="form-control doc_number" name="doc_no" id="doc_no" placeholder="Input Number required">
                    </div>

                  </div>
                   
                      <div align="right">
                        <button type="button" name="add" id="add" class="btn btn-success btn-xs">+</button>
                      </div>
                 </div>

               </div>

                <h4>PURPOSE</h4>
                 <label>Please select the purpose for PCC</label>
                  <div class="form-group col-md-3">

                  <input type="radio" name="in_pcc_purpose" value="Visa/Immigration"> Visa/Immigration<br>
                  
                  </div>
                  <div class="form-group col-md-3">
                    <input type="radio" name="in_pcc_purpose" value="employment" checked> Employment<br>
                  </div>
                  <div class="form-group col-md-3">
                    <input type="radio" name="in_pcc_purpose" value="others" checked> Others<br>
                    
                  </div>

                  <h3>PURPOSE SPECIFIC DETAILS</h3>
                  <H4>RECORD VERIFICATION</H4>
                  <label>Please select the purpose for PCC</label>
                  <div class="form-group col-md-3">
                    <input type="checkbox" name="in_pcc_virification_for" value="crime_record" checked> Crime Record<br>
                   
                  </div>
                  <div class="form-group col-md-3">
                    <input type="checkbox" name="in_pcc_virification_for" value="traffic" checked> Traffic<br>
                    
                  </div>

                   <div class="row">
               <div class="form-group col-md-10"><button type="button" name="add" id="add" class="btn btn-success btn-xs">Save as Draft</button></div>
                  <div class="form-group col-md-2"><small>PCC process fee Rs.300/-</small><button type="submit" name="add" id="add" class="btn btn-success btn-xs">Save and Process</button></div>
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
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD8HeI8o-c1NppZA-92oYlXakhDPYR7XMY"></script>
  

  <!-- Template Main Javascript File -->
  <script src="{{ asset("frontend/js/main.js") }}"></script>

  <!-- Bootstrap file upload Javascript -->
<script type="text/javascript" src="{{ asset("frontend/js/bootstrap-filestyle.min.js") }}"></script>

<script>
  

  $(document).ready(function(){
    var count = 1;
    $("#add").click(function(){
      //alert("check me");
      count = count + 1;

      var html_code ='<div class="col-md-12" id="row'+count+'">'; 
      html_code +=`<div class="row">`;
      html_code +=`<div class="form-group col-md-4" >
                        <select class="form-control doc_name" name="doc_name" >
                              <option value="aadharCard">Aadhar Card (both side)</option>
                              <option value="drivingLience">Driving Licence(both side)</option>
                              <option value="passport">Passport</option>
                              <option value="voterId">Voter ID (both side)</option>
                        </select>
                    </div>`; 
      html_code +=`<div class="form-group col-md-4 input-group-cus">
      <input type="file" id="demo`+count+`" class="filestyle doc_type" placeholder="No file"  ></div>`; 
      html_code +='<div class="form-group col-md-3 doc_number"><input type="text" class="form-control" name="doc_no" id="doc_no" placeholder="Input Number"></div>'; 
     
      html_code +=`<div  ><button type='button' name='remove' style='float:right; width:30px' data-row='row`+count+`' class='btn btn-danger btn-xs remove'>-</button></div>`; 
      html_code +="</div></div>";
      $('#crud_table').append(html_code); 
      $("#demo" + count).filestyle();
  });

      $(document).on('click','.remove',function(){
        var delete_row = $(this).data("row");
        $('#' + delete_row).remove();
      });

      $('#add').on('click',function(){
      var doc_name=[];
      var doc_type=[];
      var doc_number=[];

      $('.doc_name').each(function(){
        doc_name.push($(this).text());
      });
      $('.doc_type').each(function(){
        doc_type.push($(this).text());
      });
      $('.doc_number').each(function(){
        doc_number.push($(this).text());
      });
      $.ajax({
        url:"",
        method:"POST",
        data:{doc_name:doc_name,doc_type:doc_type,doc_number:doc_number},
        success:function(data){
          console.log(data);
        }
      });

      });


});
</script>


</body>
</html>
