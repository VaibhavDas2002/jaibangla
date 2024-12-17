<!DOCTYPE html>

<html>


<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base />
    <!-- Favicon icon -->
    <link rel="shortcut icon" href="images/fav_icon.png">

    <!-----  Css  ----->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/fonts.css">
    <!-- <link rel="stylesheet" type="text/css" href="fontawesome.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive-style.css">

    <!-----  Css End ----->
    <style>
        .quick_links {
            min-height: 130px;
        }
        .full_width{display:none;}
        
    </style>
    <script>
      window.history.forward();
    </script>
</head>

<body>

    <!------- header starts ------->
    <div class="header">
        <div class="container">
            <div class="row">
                    <div class="header-right col-lg-12 col-md-12 col-sm-12 col-xs-12">

                        <div class="row ">
                            <div class="header-right-top">
                                <div class="header-right-top-left">
                                    <ul>
                                        <li title="Toll Free Number"> <i class="fa fa-phone"></i> ######</li>
                                        <li title="Send Email"><i class="fa fa-envelope"></i><a href="#"> ######@####.com</a></li>
                                        <li title="Send SMS"><i class="fa fa-comments"></i> #########</li>
                                        <li class="loginoffice"><a href="{{ url('/login') }}" title="log In"><i class="fa fa-user"></i>Officials Sign In</a></li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                        <div class="clearfix"></div>

                    </div>
            </div>
            <div class="row">
                <div class="col-lg-8 col-md-8 col-xs-12 logo">
                    <div class="row">
                        <a href="#">
                            <h1>
                                <img src="images/jaibangla_logo.png">
                            </h1>
                        </a>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <!------- header end ------->



    <!------- about starts ------->
    <div class="about" >
        <div class="container">
            <div class="row" >
                <img class="images/about_img_mobile " style="display:none;" src="images/CM_CMO_Default.png">
            </div>
            <div class="row">
                    <div class="col-5 about_text">
                        
                        <h2 style="color: #4f6128;"><span style="color: #0375c5;display:none;">Welcome to 
                            <span class="e-smadhan" style=""> <img src="images/CMO_LOGO.png" alt="Alternate Text" /></span> </span>Single Platform for applying and processing pensions schemes under Goverment of West Bengal</h2>
                        <p>
                            To ensure that the citizens of West Bengal get the responsive, accountable and transparent process of applying for pension schemes under Government of West Bengal is one of the most important initiatives of the State Government. The Jai Bangla Portal is made available online with an objective of speedy approval and effective monitoring by the Government besides providing a fast access to the public. 
                    
                            
                    
                        </p>
                        
                        <!--<a href="#" class="read">read more...</a>-->
                    </div>
                    <div class="col-4 about_img">
                        <img src="images/CM_CMO_Default.png" alt="" />
                    </div>

                    <div class="col-3" >
                      <div style="background-color:#fff; border:1px solid #dcdfdf; border-radius: 10px;">
                        
                       <div class="login-hd"><h4 style="padding: 10px;">LOGIN</h4>
              </div>

        <div class=" row btn-detailsin">
          <div class="col-md-12">
             <form action="{{url('/sendOtp')}}" method="post" name="frm_reg" id="frm_reg">
                {{ csrf_field() }}

                 @if(Session::has('message'))
                  <div class="alert {{ Session::get('alert-class', 'alert-info') }}"> {{ Session::get('message') }}</div>
                 @endif

                 @if(Session::has('message1'))
                  <div class="alert alert-danger"> {{ Session::get('message1') }}</div>
                 @endif

                 @if(Session::has('message_resent'))
                  <div class="alert {{ Session::get('alert-class', 'alert-info') }}"> {{ Session::get('message_resent') }}</div>
                 @endif
                
                  <div class="col-md-12 ">
                    <div class="form-group{{ $errors->has('mobileno') ? ' has-error' : '' }}" >
                      <input type="text" placeholder="Mobile No. (10 Digits)" class="form-control contact-input user_id required valid lineColor" maxlength="10" id="mobile_no" name="mobileno" required="" value='@if(Session::has('message')){{ Session::get('mobile') }}  @endif'  @if(Session::has('message')) readonly @endif>

                          @if ($errors->has('mobileno'))
                            <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                                <strong>{{ $errors->first('mobileno') }}</strong>
                            </span>
                          @endif
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-12" >
                     <div style="color: #ff0000;"  class="form-group{{ $errors->has('captcha') ? ' has-error' : '' }}">
                        
                           <div class="refereshrecapcha col-sm-12">
                              <span>{!! captcha_img('flat') !!}</span>
                              <!-- <a href="{{ url('publiclogin') }}"><img src="{{ asset('images/refresh1.png') }}" style="height: 20px; width: 20px; border-width: 0px;" ></i></a> -->
                              <a href="javascript:void(0)" onclick="return refereshcapcha();"><img src="{{ asset('images/refresh1.png') }}" style="height: 20px; width: 20px; border-width: 0px;" ></a>
                            </div>
                             <div class="col-sm-12" style="margin-bottom:10px;">
                               <input id="captcha" type="text" class="form-control lineColor" placeholder="Enter Captcha" name="captcha" autocomplete="off">
                             </div>
                        
                            @if ($errors->has('captcha'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('captcha') }}</strong>
                                </span>
                            @endif

                          <div class="col-md-12">
                            <div class="form-group">
                              <input style="width: 100%;" id="btn_sendotp" type="submit" name="btn_sendotp" class="btn btn-success black lineColor" @if(Session::has('message')) disabled @endif  value="Send OTP">
                            </div>
                          </div>  
                        </div>
                    </div>
                  </div>
                
           
                
                  <div id="submit_div"></div>
                  
              </form>
          </div>
        </div>

            <div class="col-md-12 veryOtp"  >
                 <form action="{{url('/verifyOtp')}}" method="post" name="frm_reg" id="frm_reg" >
                  {{ csrf_field() }}
                  <div class="row">
                     <div id="verifyotp_div">
                        <div class="row">
                            <div class="col-md-6 form-group{{ $errors->has('otp') ? ' has-error' : '' }}"  >
                        <input style="margin-left:15px;" type="text" placeholder="Enter OTP" class="form-control lineColor" maxlength="6" id="otp" name="otp" >
                        
                            <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                                <strong></strong>
                            </span>
                          </div>
                         <div class="col-md-6">
                          <input style="width: 90%" type="submit" name="varify" value="Verify" class="btn btn-primary black"   ></div>
                        </div>
                        
                      
                      
                        
                      
                      
                     
                    </div>
                  </div>
                 
                    
                  
                  </form>
                
            </div>
                      </div>
                    </div>
                       
           </div>
                    
                    <!-- <div class="col-2 about_img" style="width: 180px;">
                    <form name="form1" method="post" action="#" onsubmit="#" id="form1">
                        <div class="row" style="background: url(images/pb_lgin_hdr.png); background-repeat: no-repeat; height: 50px; width: 180px;">
                          <span style="text-align: center; width: 100%; margin-top: 15px; color:  #FFC300;"><b>Public Login</b></span>
                        </div>
                        <div class="row" style="height: 40px;">
                          <input type="text" class="style2" name="mobile_no" style="border-color: #00F; background-color: #cdcaca;width: 180px;" placeholder="Enter Mobile Number">
                        </div>
                        <div class="row" style="height: 40px;">
                          <input type="text" class="style2" name="OTP" style="border-color: #00F; background-color: #cdcaca; width: 180px;" placeholder="Enter OTP">
                        </div>
                        <div class="row" style="height: 50px; width: 200px;">
                          <div class="btn-group">
                           <button type="button" class="btn btn-primary active">Login</button>

               <button type="button" class="btn btn-primary disabled">Generate OTP</button> 
              </div>
                        </div>
                    </form>
                    </div> -->
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <!------- about end ------->
    <!------- quick links starts ------->
    <div class="quick_links">
        <div class="container">
            <div class="row">
                 <div class="col-md-12 mainnumber">
                     <h2 class="textnumber">For Technical Issues Contact At: <span class="spannumber"> 033-####-####</span></h2>
                 </div>
            </div>
        </div>

    </div>
    </div>
    <div class="clearfix"></div>
    <!------- quick links end ------->

    



    <!------- footer starts ------->
    <div class="footer">
        <div class="col-lg-12">
            <div class="footer_copy col-md-5">
            <div style="text-align:left;">
                        Copyright &copy; 2019-2020 Govt of West Bengal - All Rights Reserved.</div>
            </div>
            <div class="col-lg-12 designed_by">
                <div class="footer_text"></div>

                <div class="footer_bottom_wrap">
                    <div class="site_visitor">
                        Site Visitor :
                        <span id="Label2" style="color:#2F5B93;">######</span></div>
                    <div class="nic_logo">
                        Designed and Developed by: <a href="http://www.nic.in/" target="_blank">
                            <img src="images/nic.png" alt="NIC" /></a>
                    </div>
                </div>

            </div>
        </div>
        </div>
        <div class="clearfix"></div>
        <!------- footer end ------->

    <!-- jQuery 2.1.3 -->
    <script src="{{ asset ('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <!-- <script src="{{ asset('js/site.js') }}"></script> -->
    <script type="text/javascript">
    $(document).ready(function() {
        jQuery('#mobile_no').keyup(function () { 
            this.value = this.value.replace(/[^0-9\.]/g,'');
        });
        jQuery('#otp').keyup(function () { 
            this.value = this.value.replace(/[^0-9\.]/g,'');
        });
        window.onload = function()
        {
            document.getElementById('captcha').setAttribute('autocomplete', 'off');
            document.getElementById('otp').setAttribute('autocomplete', 'off');
        }
        // $("#mobile_no").keyup(function(event) {
              
        // $(this).val($(this).val().replace(/[^\d].+/, ""));
        //     if ((event.which < 48 || event.which > 57)) {
        //         event.preventDefault();
        //     }
        // });
        // $("#otp").keyup(function(event) {
              
        // $(this).val($(this).val().replace(/[^\d].+/, ""));
        //     if ((event.which < 48 || event.which > 57)) {
        //         event.preventDefault();
        //     }
        // }); 
    });

    function refereshcapcha(){
          //alert('hi');
          $.ajax({
          url: "{{ url('/refereshcapcha') }}",
          type: 'get',
            dataType: 'html',        
            success: function(json) {
              //alert(json);
              $('.refereshrecapcha span').html(json);
              //$('.refereshrecapcha span').html(json.refereshrecapcha);
            },
            error: function(data) {
              alert('Try Again.');
            }
          });
        }
    </script>
</body>

</html>