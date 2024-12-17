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

 
<script type="text/javascript">
  window.history.forward();
</script>
</head>

<body id="body" >

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
    </div>
  </header><!-- #header -->

  <!--==========================
    Intro Section
  ============================-->

<main id="main" >

   <section id="contact" class="wow ">

    <div class="container box_inter" >
        <div class="form" id='printMe'>
            <div class="row" style="padding:20px;">
            	
                    <div class="col-md-12" > 
                      <div class="row">
                        <div class="col-md-8" >
                            <strong>Application ID :</strong> {{ $application->application_id }}<br/>
                            <strong>Applicant's Name :</strong> {{ $application->first_name}} {{ $application->middle_name}} {{ $application->last_name}}<br/>
                            <strong>Father's Name :</strong> {{ $application->father_name}}</div>
                        <div class="col-md-4" >
                           
                            <img  src="{{URL::to('/')}}/images/{{$application->application_id}}/{{$application->profile_img}}" class="imageSize " width="180" height="180"></img>
                        </div>
                      </div>
                    </div><br>

                    <div class="col-md-12 ">
                        <h4 style="background: #dcdfdf; padding:5px 10px; margin:10px 0px;">General Information</h4>
                        <div class="row">
                            <div class="col-md-4"><strong>Gendar:</strong> {{ $application->gender}}</div>
                            <div class="col-md-4"><strong>Dob :</strong> {{date("d-m-Y", strtotime($application->dob))}} </div>
                            <div class="col-md-4"><strong>Nationality :</strong> {{ $application->nationality}}</div>
                            
                            <div class="col-md-4"><strong>Spouse Name :</strong> {{ $application->spouse_name}}</div>
                            <div class="col-md-4"><strong>Email :</strong> {{ $application->email}}</div>
                            <div class="col-md-4" ><strong>Mobile No :</strong> {{ $application->mobile_no}}</div>
                            <div class="col-md-4"><strong>Stay From Date :</strong> {{ date("d-m-Y", strtotime($application->present_stay_frm_date))}}</div>
                            <div class="col-md-4"><strong>Stay To Date :</strong> {{date("d-m-Y", strtotime($application->present_stay_to_date))}} </div>
                        </div>
                    </div>
                    <div class="col-md-12 ">
                        <h4 style="background: #dcdfdf; padding:5px 10px; margin:10px 0px;">Present Address</h4>
                        <div class="row"> 
                            <div class="col-md-4"><strong>Present Address:</strong> {{ $application->present_address_line1}}, {{ $application->present_address_line2}}</div>
                            
                            <div class="col-md-4"><strong>Present Address Landmark:</strong>  {{ $application->present_address_landmark}}</div>
                            <div class="col-md-4"><strong>Present Pincode:</strong>{{$application->present_pincode}}</div>
                            <div class="col-md-4"><strong>Present City:</strong>{{$application->present_city}}</div>
                             <div class="col-md-4"><strong>Present State:</strong> {{ $present_state->name}}</div>
                        </div>
                    </div>
                   
                    
                  
                    
                    <div class="col-md-12 ">
                        <h4 style="background: #dcdfdf; padding:5px 10px; margin:10px 0px;">Permanent Address</h4>
                        <div class="row">
                        
                        <div class="col-md-4"><strong>Permanent Address :</strong> {{ $application->permanent_address_line1}},{{ $application->permanent_address_line2}}</div>
                        <div class="col-md-4"><strong>Permanent Address Landmark :</strong> {{ $application->permanent_address_landmark}}</div>

                        <div class="col-md-4"><strong>Permanent Pincode:</strong>{{$application->permanent_pincode}}</div>
                        <div class="col-md-4"><strong>Permanent City:</strong>{{$application->permanent_city}}</div>
                        <div class="col-md-4"><strong>Permanent State:</strong> {{ $permanent_state->name}}</div>
                        <div class="col-md-4"><strong>Permanent Country:</strong> {{ $permanent_country->name}}</div>
                        
                       </div>
                    </div>
                    <div class="col-md-12 ">
                        <h4 style="background: #dcdfdf; padding:5px 10px; margin:10px 0px;">Others Information</h4>
                        <div class="row">
                        
                        <div class="col-md-4"><strong>Purpose :</strong> {{ $application->pcc_purpose}}</div>
                        
                    </div>
                   </div>
                   
                   <?php 
                    $stored_file_name = json_decode($application_images->stored_file_name);
                    $extension_type = json_decode($application_images->extension_type);
                    

                    if($stored_file_name !='' || $extension_type !=''){
                      $image_type = array_combine($stored_file_name, $extension_type);



                    }

                    $document_type_data = json_decode($application_images->document_type);
                    $document_number = json_decode($application_images->document_no);

                    if($document_type_data !='' || $document_number !=''){
                        $document_data = array_combine($document_type_data, $document_number);
                    }

                     ?>
                    @foreach($document_data as $k=>$v)
                    <div class="col-md-12 backgroundColorEven">
                        <div class="row">
                         <div class="col-md-4 applicant_Details " ><strong>{{$k}} :</strong> {{ $v}}</div>
                         <div class="col-md-4 applicant_Details " ><br></div>
                         <div class="col-md-4 applicant_Details " ><br></div>
                       </div>
                    </div>
                    @endforeach

                    <div class="clear"></div><br/>
                    <br/>
                
            </div>
            <div class="col-md-12" style="padding-bottom: 20px; ">
                <div class="row" >
                    @foreach($image_type as $key=>$type)
                      @if($type=='jpg')
                        <div class="col-md-3 imgPosition gallery clearfix" >
                            <img  src="{{URL::to('/')}}/images/{{$application->application_id}}/{{$key}}" class="imageSize " width="180"></img>
                           
                        </div>

                        @elseif($type=='jpeg')
                        <div class="col-md-4 imgPosition gallery">
                           
                                <img src="{{URL::to('/')}}/images/{{$application->application_id}}/{{$key}}" class="imageSize" width="180" ></img>
                            
                        </div>

                        @elseif($type=='png')
                        <div class="col-md-4 imgPosition gallery">
                            
                                <img  src="{{URL::to('/')}}/images/{{$application->application_id}}/{{$key}}" class="imageSize" width="180" ></img>
                           
                        </div>
                        
                        
                        @elseif($type=='pdf')
                        <div class="col-md-4 imgPosition pdfContener">
                            <a id="link" rel="gallery" class="fancybox fancybox.iframe" href="{{URL::to('/')}}/images/{{$application->application_id}}/{{$key}}" target="_blank" width="">PDF Document</a>
                        </div>
                        @else 
                            <div class="col-md-4"><span>There Is no Image for show </span></div>
                       @endif
                    @endforeach
                </div>
            </div>
        </div>

            <div class="row">
             <div class="col-sm-2" style="padding-bottom:20px ;">
                <a href="{{url('editApplication/'.$application->application_id)}}" class="btn btn-primary">Edit Application</a>
             </div>
             <div class="col-sm-2" style="padding-bottom:20px ;">
                <a onclick="printDiv('printMe')" class="btn btn-primary" style="color: #fff;">Print Application </a>
             </div>
             <div class="col-sm-5"></div>
                <div class="col-sm-3 float-right" style="padding-bottom:20px;">
                  <form action="https://pgi.billdesk.com/pgidsk/PGIMerchantPayment" method="POST" role="form" class="contactForm">
                    {{ csrf_field() }}
                      <div class="row">
                         <div class="col-md-12" >                                   
                         <input type="hidden" name="msg" value="{{$txn_msg}}">
                          <input type="submit" class="btn btn-success" value="Proceed to Payment Gateway"/>
                          </div>

                      </div>
                  </form>
                </div>
            </div>
    </div>

      
             
       
  </section><!-- #contact -->

 
        
         
    

</main>

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
  function printDiv(divCon){
    var printContent = document.getElementById(divCon).innerHTML;
  var oldcont = document.body.innerHTML;
  document.body.innerHTML = printContent;
  window.print();
  document.body.innerHTML = oldcont;
  }
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

<script>
  

  $('#policestation').change(function() {
    var id=$(this).val();
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    $.ajax({
    type: "GET",
    url:"{{url('/application/checkPolicestation')}}/"+id,

    success: function(data) {
        //console.log(data);
        $('#police_id').html(data);
    }
    });
  });

   
</script>







</body>
</html>
