
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, minimum-scale=0.1" />
<title>Jai Bangla | Government of West Bengal</title>
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset("/images/biswofab.png") }}" />
<link
    href="https://fonts.googleapis.com/css?family=Open+Sans:400,600&amp;display=swap"
    rel="stylesheet" />
<link href="{{ asset("/css/boostrap-new.min.css") }}" rel="stylesheet" />
<link href="{{ asset("/css/Boostrrap.css") }}" rel="stylesheet" />
<style>
body {
    background: url(../images/testimonial-bg1.jpg) no-repeat center center fixed;
    background-size: auto;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
    font-family: 'Open Sans', sans-serif;
}

.adminlogintable {
    margin-top: 30px;
}

.inner-container {
    margin-top: 50px;
    width: 100%;
    background: url(../images/Login_Page1.png) no-repeat center center;
    background-size: auto;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}

.form-control {
    padding: 6px 4px;
}

.adminlogintable tbody tr td input, .adminlogintable tbody tr td img {
    margin-bottom: 10px;
    font-size: 13px;
}

.list-row {
    position: relative;
    top: -30px;
    margin-top: 30px;
}

.list-text {
    top: -10px;
}

.adminlogintable tbody tr td input.btnotp {
    margin-bottom: 3px;
}

.admintextnumber {
    letter-spacing: 2px;
    margin-bottom: 30px;
    font-size: 18px;
    font-weight: 600;
}

.adminspannumber {
    padding: 3px 9px;
    border: 1px solid #333;
    border-radius: 5px;
    display: inline-block;
}
</style>

</head>
<body>
    <!-- <form name="form1" method="post" action="#" onsubmit="#" id="form1"> -->
        <div class="container">
            <div class="inner-container">
                <!-- @if (session('status'))
                    <div class="alert alert-danger">
                        {{ session('status') }}
                    </div>
                @endif -->
                <div class="row">
                    <div class="col-xs-3 col-sm-3 col-md-2" style="margin-top: 20px; margin-bottom: 10px;">
                        <img class="biswo" src="{{ asset("images/biswo.png") }}" alt="Alternate Text" />
                    </div>
                    <div class="col-xs-9  col-sm-9 col-md-10" style="margin-top: 20px; ">
                            <!-- <img class="e-sahayR"
                            style="float: right; margin-right: 20px; position: relative; top: 40px; width: 450px;"
                            src="jaibangla.png" alt="Jai Bangla" /> -->
                            <img class="e-sahayR"
                            style="float: right; margin-right: 20px; position: relative; width: 450px;"
                            src="{{ asset("images/jaibangla_dtl.png") }}" alt="Jai Bangla" />
                        <!-- <h4 class="first-heading">Government of West Bengal</h4> -->
                            <img src="{{ asset("images/bangla.png") }}" style="margin-top: 40px;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-7 col-md-offset-4 col-md-4">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('password.email') }}">
                        {{ csrf_field() }}
                        <table width="100%" class="adminlogintable">
                            <tr>
                                <td>
                                   @if (session('status'))
                                        <div class="alert alert-danger" style="text-align: center;">
                                            {{ session('status') }}
                                        </div>
                                    @endif 
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <!-- <input name="txt_Mobile_No" type="text"
                                    maxlength="10" id="txt_Mobile_No class="
                                    form-control" style="color: #3D5A5A; width: 180px;"
                                    placeholder="Enter Registered Mobile No." /> -->
                                    <input id="mobile_no" type="text" maxlength="10" class="form-control" name="mobile_no" value="{{ old('mobile_no') }}"style="color: #3D5A5A; width: 200px;"
                                    placeholder="Enter Registered Mobile No." required autofocus>
                                    @if ($errors->has('mobile_no'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('mobile_no') }}</strong>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr id="Tr_cap1">
                                <td align="center" class="style6">
                                    <table width="70%">
                                        <tr>
                                            <td colspan="2" align="center">
                                                <table width="200px">
                                                    <tr id="trCaptcha">
                                                        <td class="style6"
                                                            style="padding-left: 2px; padding-right: 0px;"
                                                            align="left"><!-- <img id="Image2" src="#"
                                                            style="height: 50px; width: 160px; border-width: 0px;" /> -->
                                                            <div class="captcha">
                                                                <span>{!! captcha_img('flat') !!}</span>
                                                                <a href="{{ route('password.request') }}"><img src="{{ asset("images/refresh1.png") }}" style="height: 20px; width: 20px; border-width: 0px;" ></a> 
                                                                <!-- <a href="{{ route('login') }}" class="btn btn-success btn-sm btn-refresh">Refresh</a> -->
                                                            </div>
                                                            <!-- <input id="captcha" type="text" class="form-control" name="captcha" value="{{ old('captcha') }}" placeholder="Enter captcha" required autofocus>
                                                            @if ($errors->has('captcha'))
                                                                <span class="help-block">
                                                                    <strong>{{ $errors->first('captcha') }}</strong>
                                                                </span>
                                                            @endif -->
                                                        </td>
                                                        
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table> <span id="lbl_captcha" style="color: Red; font-weight: bold;"></span>
                                </td>
                            </tr>

                            <tr id="Tr_cap3">
                                <td align="center" class="style2">
                                    <input id="captcha" type="text" class="mob form-control" name="captcha" value="{{ old('captcha') }}" placeholder="Enter captcha" autocomplete="off" style="width: 200px;" required autofocus>
                                    @if ($errors->has('captcha'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('captcha') }}</strong>
                                        </span>
                                    @endif
                                    <!-- <input name="txt_captcha"
                                    type="text" maxlength="5" id="txt_captcha"
                                    placeholder="Enter Above Text" class="mob form-control"
                                    style="width: 180px;" /> -->
                            </tr>
                            <!-- <tr id="Tr_cap3">
                                <td align="center" class="style2">
                                    <input id="login_otp" type="text" maxlength="6" class="mob form-control" name="login_otp" value="{{ old('login_otp') }}" placeholder="Enter otp"style="width: 180px;" required autofocus>
                                    @if ($errors->has('login_otp'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('login_otp') }}</strong>
                                        </span>
                                    @endif
                                    <input name="txt_otp"
                                    type="text" maxlength="5" id="txt_otp"
                                    placeholder="Enter OTP" class="mob form-control"
                                    style="width: 180px;" /> 
                            </tr> -->

                            <tr id="Tr_cap4">
                                <td align="center" class="style4" style="padding-top: 5px;">
                                    <button type="submit" class="btn btn-success btnlogin" style="width: 200px;">
                                        Send OTP
                                    </button>
                                    <!-- <input type="submit" name="btn_getotp" value="Login"
                                    id="btn_login" class="btn  btn-success btnlogin"
                                    style="width: 180px;" /> -->
                                </td>
                            </tr>

                            <tr id="Tr_cap41">
                                <td align="center" class="style4" style="padding-top: 5px;">
                                    <a class="btn btn-primary btnotp" style="width: 200px;" href="{{ route('login') }}">
                                        Already Have OTP
                                    </a>
                                    <!-- <input type="submit" name="gen_otp"
                                    value="Generate OTP" id="gen_otp"
                                    class="btn btn-primary btnotp" style="width: 180px;" /> -->
                                </td>
                            </tr>



                        </table>
                    </form>
                    </div>
                    <div class="col-xs-0 col-sm-5 col-md-4">
                        <img class="cm" src="{{ asset("images/CM_CMO_Image.png") }}" alt="" />
                    </div>
                </div>
                <div class="row list-row">
                    <div class="col-md-12">
                        <p class="text-center admintextnumber">
                            For Technical Issues Contact At: <span class="adminspannumber">###-####-####</span>
                        </p>
                    </div>
                    <!-- <div class="col-sm-6">
                        <input type="image" name="imgbtn_online" id="imgbtn_online" class="list-icon" src="{{ asset("images/home_user.png") }}" style="border-width:0px;">
                        <span id="lbl_online" class="list-text"
                            style="color: #3366CC; font-size: 12px; font-weight: bold;">Online
                            Users : </span>
                    </div>
                    <div class="col-sm-6">
                        <input type="image" name="ImageButton3" id="ImageButton3" class="list-icon" src="{{ asset("images/home.png") }}" style="border-width:0px;">
                        <a href="#"> <span id="Label2" class="list-text"
                            style="color: #3366CC; font-size: 12px; font-weight: bold;">Back
                                to Home</span></a>
                    </div> -->
                </div>
            </div>
            <div>
                <div class="footer" align="center" style="">
                    Site Designed, Hosted and Maintained by <a
                        href="http://www.nic.in/" target="_blank" style="color: #FFFFFF;">National
                        Informatics Centre</a> <br /> <span id="Label1"
                        style="font-size: 8.5pt;">Best Viewed in Google Chrome</span> <a href="#" id="ld"
                        style="color: yellow; font-size: 8.5pt;">Legal Disclaimer</a>
                </div>
            </div>
        </div>
    <!-- </form> -->
    <!-- jQuery 2.1.3 -->
    <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        jQuery('#mobile_no').keyup(function () { 
            this.value = this.value.replace(/[^0-9\.]/g,'');
        });
        window.onload = function()
        {
            document.getElementById('captcha').setAttribute('autocomplete', 'off');
        }
        // $("#mobile_no").keyup(function(event) {
              
        // $(this).val($(this).val().replace(/[^\d].+/, ""));
        //     if ((event.which < 48 || event.which > 57)) {
        //         event.preventDefault();
        //     }
        // });
    });
    </script>
</body>
</html>

<?php
    Session::forget('status');
?>


