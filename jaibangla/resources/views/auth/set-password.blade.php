<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, minimum-scale=0.1" />
    <title>Jai Bangla | Government of West Bengal</title>
    <link rel="icon" type="image/png" sizes="32x32" href="images/biswofab.png" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600&amp;display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('/css/boostrap-new.min.css') }}" type="text/css">
    <link href="{{ asset('/css/Boostrrap.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <style>
        body {
            background: url(images/testimonial-bg1.jpg) no-repeat center center fixed;
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
            background: url(images/Login_Page_new.png) no-repeat center center;
            background-size: auto;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        .inner-container .paschimbanga_sarkar h2 {
            color: #115e28;
            font-size: 35px;
            font-weight: bold;
        }

        .inner-container .paschimbanga_sarkar h3 {
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 600;
            color: #341c90;
            margin-top: -6px;
            font-size: 20px;
        }

        .inner-container img.biswo {
            margin-left: 20px;
        }

        .inner-container .bg_blue {
            background-color: #003399;
            padding: 20px -2px;
            width: 183px;
            height: 47px;
            border-radius: 12px;
            margin-left: 80px;
        }

        .inner-container .bg_blue h2 {
            color: #fff;
            font-weight: 600;
            margin-left: 21px;
            padding-top: 8px;
        }

        .inner-container .pb_wb h4 {
            font-size: 15px;
        }

        .inner-container .pb_wb h3 {
            margin-top: -8px;
            font-size: 17px;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-left: 35px;
            font-weight: 600;
            color: #341c90;
        }

        .float-right {
            float: right;
            margin-top: -25px;
        }


        .bar li {
            padding: 5px 7px;
            border: 1px solid #fff
        }

        .bar li div {
            cursor: pointer;
            color: #fff;
        }

        .bar li.last_one {
            border: none;
        }

        .bar li.last_one a {
            color: #fff;
            padding: 0;
        }

        .form-control {
            padding: 6px 4px;
        }

        .adminlogintable tbody tr td input,
        .adminlogintable tbody tr td img {
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

        .nic {
            color: #fff
        }

        .footer {
            background-color: blue;
            margin: 0;
        }

        .footer-text {
            color: #fff
        }

        #Label1 {
            color: #fff
        }

        @media screen and (max-width: 991px) {
            .adminlogintable {
                margin-top: 30px;
                position: relative;
                left: 100px;
            }

            .admintextnumber {
                letter-spacing: 2px;
                margin-bottom: 3px;
                font-size: 14px;
                font-weight: 600;
            }
        }

        @media screen and (max-width: 767px) {
            .adminlogintable {
                left: 0px;
            }

            .inner-container {
                height: 600px;
            }

            .adminlogintable tbody tr td input,
            .adminlogintable tbody tr td img {
                margin-bottom: 5px;
                font-size: 13px;
            }

            .list-row {
                position: relative;
                top: 0;
                margin-top: 20px;
            }

            .list-row div {
                text-align: center;
            }

            .paschimbanga_sarkar {
                margin-left: 20px;
            }

            .inner-container .paschimbanga_sarkar h2 {
                font-size: 22px;
            }

            .inner-container .paschimbanga_sarkar h3 {
                font-size: 15px;
            }

            .inner-container .bg_blue {
                margin-left: 10px;
                width: 175px;
                height: 37px;
            }

            .inner-container .bg_blue h2 {
                font-size: 20px;
                margin-left: 37px;
            }

            .inner-container .pb_wb h3 {
                font-size: 12px;
                margin-left: 7px;
            }

            .e-sahayR {
                float: right !important;
                margin-right: 1px !important;
                position: relative !important;
                top: 12px !important;
                width: 200px !important;
            }

            .wbIcon {
                margin-right: 1px !important;
                margin-top: 32px !important;
                width: 200px !important;
            }

            .admintextnumber {
                letter-spacing: 1px;
                margin-bottom: 3px;
                margin-top: -8px;
            }
        }

        /* toggle */
        .checkbox {
            opacity: 0;
            position: absolute;
        }

        .label {
            width: 43px;
            height: 18px;
            background-color: #20425f;
            display: flex;
            border-radius: 50px;
            align-items: center;
            justify-content: space-between;
            padding: 5px;
            position: relative;
            transform: scale(1.5);
        }

        .ball {
            width: 15px;
            height: 15px;
            background-color: white;
            position: absolute;
            top: 2px;
            left: 2px;
            border-radius: 50%;
            transition: transform 0.2s linear;
        }

        .checkbox:checked+.label .ball {
            transform: translateX(24px);
        }

        .fa-moon {
            color: pink;
        }

        .fa-sun {
            color: yellow;
        }

        /* dark */
        body.dark {
            background: url(images/testimonial-bg_dr.png) no-repeat center center fixed;
            background-size: auto;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        body.dark .inner-container {
            margin-top: 50px;
            width: 100%;
            background: url(images/Login_Page_dr.png) no-repeat center center;
            background-size: auto;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        body.dark .float-right {
            float: right;
            margin-top: -25px;
        }


        body.dark .inner-container .bg_blue h2 {
            color: #ffff00;
        }

        body.dark .inner-container .paschimbanga_sarkar h2 {
            color: #ffff00;
        }

        body.dark .inner-container .paschimbanga_sarkar h3 {
            color: #ffff00;
        }

        body.dark .alert-danger {
            color: #ffff00;
            background-color: #000;
            border-color: #ffff00;
        }

        body.dark ::placeholder {
            color: #ffff00;
        }

        body.dark .btn-success {
            background-color: #000;
            color: #ffff00;
            border-color: #000;
        }

        body.dark .btn-primary {
            background-color: #000;
            color: #ffff00;
            border-color: #000;
        }

        body.dark .adminlogintable tbody tr td input {
            background-color: #555;
        }

        body.dark .inner-container .bg_blue {
            background-color: #141313;
        }

        body.dark .inner-container .pb_wb h4 {
            color: #ffff00;
        }

        body.dark .inner-container .pb_wb h3 {
            color: #ffff00;
        }

        body.dark .inner-container .float-right {
            margin-top: -25px;
        }

        body.dark .font_increase {
            margin-top: -40px;
        }

        body.dark .footer-text {
            color: #fff
        }

        body.dark .nic {
            color: #ffff00
        }

        body.dark #Label1 {
            color: #fff
        }

        body.dark .bar li {
            border: 1px solid #ffff00 !important;
        }

        body.dark .bar li div {
            color: #ffff00 !important;

        }

        body.dark .bar li.last_one a {
            color: #ffff00 !important;
        }

        body.dark .footer {
            background-color: #000;
            color: #ffff00;
        }

        body.dark .form-control {
            color: #ffff00;
        }

        /* font increase decrease */
        .font_increase {
            margin-top: -35px;
        }

        #left-control-items {
            padding-top: 4px;
            margin-bottom: 5px;
        }

        .list-inline {
            padding-left: 0;
            margin-left: -5px;
            list-style: none;
        }

        .help-block {
            font-size: 12px;
            color: #d9534f;
            font-weight: bold;
        }

        /* Custom tooltip styling */
        .tooltip-inner {
            max-width: 200px;
            /* Maximum width of the tooltip */
            width: 200px;
            /* Fixed width of the tooltip */
            height: auto;
            /* Auto height depending on content */
            padding: 10px;
            /* Adjust padding */
            font-size: 14px;
            /* Font size */
            background-color: #333;
            /* Background color */
            color: #fff;
            /* Text color */
            text-align: left;
            /* Align text */
        }
    </style>

</head>

<body>
    <div class="container">
        <div class="inner-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="font_increase">
                        <ul class="bar nav nav-pills">
                            <li>
                                <div id="largerTextLink">A+</div>
                            </li>
                            <li>
                                <div id="smallerTextLink">A-</div>
                            </li>
                            <li>
                                <div id="resetFont">A</div>
                            </li>
                            <li class="last_one"><a href="#">Screen Reader</a></li>
                        </ul>
                    </div>
                    <div class="float-right">
                        <div>
                            <input type="checkbox" class="checkbox" id="checkbox">
                            <label for="checkbox" class="label">
                                <i class="fas fa-moon"></i>
                                <i class='fas fa-sun'></i>
                                <div class='ball'>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-3 col-sm-3 col-md-2" style="margin-top: 20px; margin-bottom: 10px;">
                    <img class="biswo" src="images/biswo.png" alt="Alternate Text" />
                </div>
                <div class="col-xs-9  col-sm-9 col-md-10" style="margin-top: 20px; ">
                    <div class="col-md-6">
                        <div class="paschimbanga_sarkar">
                            <h2>পশ্চিমবঙ্গ সরকার</h2>
                            <h3>Government Of West Bengal</h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg_blue">
                            <h2>জয় বাংলা</h2>
                        </div>
                        <div class="pb_wb">
                            <h4>পশ্চিমবঙ্গ সরকারের সমস্ত সামাজিক পেনশন প্রকল্পের</h4>
                            <h3>One Umbrella Scheme</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="text-default" style="font-weight: bold; text-align: center; align-content: center;">
                    @if ($passwardSetMsg == 1)
                        Please set the password first before login.
                    @else
                        Your password is expired.
                    @endif<br>
                    <small>NOTE: This password not the login OTP.</small>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-7 col-md-offset-4 col-md-4">
                    <form class="form-horizontal" method="POST" action="{{ route('reset-password-post') }}"
                        id="setPasswordForm">
                        {{ csrf_field() }}
                        <input type="hidden" name="user" value="{{ $user_in }}">
                        <input type="hidden" name="token" value="{{ $token_in }}">
                        <table width="100%" class="adminlogintable">
                            <tr>
                                <td>
                                    <!-- Display All Errors -->
                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger" style="font-size: 12px;">
                                            <ul>
                                                @foreach ($errors as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @if (session('msg'))
                                        <div class="alert alert-danger" style="text-align: center;">
                                            {{ session('msg') }}
                                        </div>
                                    @endif
                                    @if (session('otp'))
                                        <div class="alert alert-success" style="text-align: center;">
                                            {{ session('otp') }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <div style="position: relative; width: 200px;">
                                        <input id="user_password" type="password" minlength="8" class="form-control"
                                            name="user_password" placeholder="Enter Password" autocomplete="off"
                                            required autofocus style="width: 100%; padding-right: 60px;">

                                        <!-- Show/Hide Password Button -->
                                        <button type="button" id="togglePassword" class="toggle-password"
                                            style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                                            <i class="fa fa-eye-slash"></i>
                                        </button>

                                        <!-- Info Button -->
                                        <button type="button" id="infoPassword" class="info-password"
                                            style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;"
                                            data-toggle="tooltip" data-placement="right"
                                            title="Your password needs to be at least 8 characters long, at least one uppercase letter, at least one lowercase letter, at least one digit, at least one special character.">
                                            <i class="fa fa-info"></i>
                                        </button>
                                    </div>
                                    {{-- <div id="passwordInfo"
                                        style="display:none; margin-top: 10px; color: #337ab7; font-weight: bold;">
                                        Your password needs to be at least 8 characters long, at least one uppercase
                                        letter, at least one lowercase letter, at least one digit, at least one special
                                        character.
                                    </div> --}}
                                    <span id="passwordStrength" class="help-block"></span>
                                    {{-- @if ($errors->has('user_password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('user_password') }}</strong>
                                        </span>
                                    @endif --}}
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <input id="confirm_user_password" type="password" minlength="8"
                                        class="form-control" name="confirm_user_password" style="width: 200px;"
                                        placeholder="Enter Confirm Password" autocomplete="off" required autofocus>
                                    <span id="passwordStrengthConfirm" class="help-block"></span>
                                    {{-- @if ($errors->has('confirm_user_password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('confirm_user_password') }}</strong>
                                        </span>
                                    @endif --}}
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
                                                            align="left">
                                                            <div class="captcha">
                                                                <span>{!! captcha_img('flat') !!}</span>
                                                                <a href="javascript:window.location.reload(true)"><img
                                                                        src="{{ asset('images/refresh1.png') }}"
                                                                        style="height: 20px; width: 20px; border-width: 0px;"></a>
                                                                {{-- <button type="button" class="btn btn-success btn-refresh"><i class="fa fa-refresh"></i></button> --}}
                                                            </div>
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
                                    <input id="captcha" type="text" class="mob form-control" name="captcha"
                                        value="{{ old('captcha') }}" placeholder="Enter captcha" autocomplete="off"
                                        style="width: 200px;" required autofocus>
                                    {{-- @if ($errors->has('captcha'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('captcha') }}</strong>
                                        </span>
                                    @endif --}}
                            </tr>
                            <tr id="Tr_cap4">
                                <td align="center" class="style4" style="padding-top: 5px;">
                                    <button type="submit" class="btn btn-success setPasswordBtn"
                                        style="width: 200px;">
                                        Set Password
                                    </button>
                                </td>
                            </tr>

                            <tr id="Tr_cap41">
                                <td align="center" class="style4" style="padding-top: 5px;">
                                    <a class="btn btn-primary btnotp" style="width: 200px;"
                                        href="{{ route('login') }}">
                                        <i class="fa fa-arrow-left"></i> Back To Login
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
            <div class="row list-row">

            </div>
        </div>


    </div>
    <div class="footer" align="center">
        <span class="footer-text">Site Designed & Developed by</span> <a href="http://www.nic.in/" target="_blank"
            class="nic">National
            Informatics Centre</a> <br /> <span id="Label1" style="font-size: 8.5pt;">Best Viewed in Google
            Chrome</span> <a href="#exampleModal" id="ld" data-toggle="modal"
            style="color: yellow; font-size: 8.5pt;">Legal
            Disclaimer|</a>&nbsp;<a href="{{ route('copyright-policy') }}" target="_blank"
            style="color: pink; font-size: 8.5pt;">Copyright Policy</a>|
        <a target="_blank" href="{{ route('privacy-policy') }}" style="color: #3af207; font-size: 8.5pt;">Privacy
            Policy</a>|
        <a target="_blank" href="{{ route('hyperlink-policy') }}"
            style="color: #f25574; font-size: 8.5pt;">Hyperlink Policy</a>|
        <a target="_blank" href="{{ route('terms-policy') }}" style="color: #bbf207; font-size: 8.5pt;">Terms &
            Condition</a>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Legal Disclaimer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        All efforts have been made to make the information as accurate as possible. Respective
                        Departments, Govt of West Bengal or Department of Finance as Nodal or National Informatics
                        Centre (NIC), will not be responsible for any loss to any person caused by inaccuracy in the
                        information available on this Website. Any discrepancy found may be brought to the notice of
                        respective departments, Govt of West Bengal or Department of Finance as Nodal or National
                        Informatics Centre (NIC). The content / information / data owned & maintained by respective
                        department along with Department of Finance as Nodal Department.</p>
                </div>

            </div>
        </div>
    </div>
    <!-- </form> -->
    <!-- jQuery 2.1.3 -->
    <script src="{{ asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <script src="{{ asset('/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // console.log('document rweady');
            // Prevent form from submitting when fields are interacted with
            var isSubmitting = false;
            window.onload = function() {
                document.getElementById('captcha').setAttribute('autocomplete', 'off');
                document.getElementById('user_password').setAttribute('autocomplete', 'off');
                document.getElementById('confirm_user_password').setAttribute('autocomplete', 'off');
            }
            $('[data-toggle="tooltip"]').tooltip();
            document.getElementById('togglePassword').addEventListener('click', function() {
                var passwordField = document.getElementById('user_password');
                var type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);

                // Toggle eye icon
                if (type === 'password') {
                    $('#togglePassword').html('<i class="fa fa-eye-slash"></i>');
                } else {
                    $('#togglePassword').html('<i class="fa fa-eye"></i>');
                }
            });

            // document.getElementById('infoPassword').addEventListener('click', function() {
            //     var info = document.getElementById('passwordInfo');
            //     info.style.display = info.style.display === 'none' ? 'block' : 'none';
            // });

            // $(".btn-refresh").click(function() {
            //     $.ajax({
            //         type: 'GET',
            //         url: "{{ url('/refresh_captcha') }}",
            //         success: function(data) {
            //             console.log(data);

            //             $(".captcha span").html(data.captcha);
            //         }
            //     });
            // });

            // Disable copy, cut, paste, and context menu in the password fields
            $('#user_password, #confirm_user_password').on('copy paste cut contextmenu', function(e) {
                e.preventDefault();
            });

            $('#setPasswordForm').on('submit', function(e) {
                e.preventDefault();

                if (isSubmitting) return; // Prevent multiple submissions
                isSubmitting = true;

                var password = $('#user_password').val();
                var password_confirm = $('#confirm_user_password').val();
                var message = checkPasswordStrength(password);

                if (message !== "Strong password") {
                    $('#passwordStrength').text('Password is weak').css('color', '#d9534f');
                    isSubmitting = false;
                    return;
                } else if (password !== password_confirm) {
                    $('#passwordStrengthConfirm').text('Password Confirmation Not Matched').css('color',
                        '#d9534f');
                    isSubmitting = false;
                    return;
                }

                $('#setPasswordForm').off('submit'); // Unbind submit event to prevent infinite loop
                $('#setPasswordForm').submit(); // Proceed with form submission
            });

            $('#user_password').on('keyup', function() {
                var password = $(this).val();
                var message = checkPasswordStrength(password);

                $('#passwordStrength').text(message).css('color', message === "Strong password" ?
                    '#28a745' : '#d9534f');
            });

            $('#confirm_user_password').on('keyup', function() {
                var password_confirm = $(this).val();
                var message = ($('#user_password').val() == password_confirm) ?
                    "Password Confirmation Matched" : "Password Confirmation Not Matched";

                $('#passwordStrengthConfirm').text(message).css('color', message ===
                    "Password Confirmation Matched" ? '#28a745' : '#d9534f');
            });

            function checkPasswordStrength(password) {
                var minLength = /.{8,}/;
                var upperCase = /[A-Z]/;
                var lowerCase = /[a-z]/;
                var digit = /\d/;
                var specialChar = /[!@#$%^&*(),.?":{}|<>]/;

                var messages = [];

                if (!minLength.test(password)) {
                    messages.push("at least 8 characters long");
                }
                if (!upperCase.test(password)) {
                    messages.push("at least one uppercase letter");
                }
                if (!lowerCase.test(password)) {
                    messages.push("at least one lowercase letter");
                }
                if (!digit.test(password)) {
                    messages.push("at least one digit");
                }
                if (!specialChar.test(password)) {
                    messages.push("at least one special character");
                }

                if (messages.length > 0) {
                    return "Weak password. Your password needs to be " + messages.join(", ") + ".";
                } else {
                    return "Strong password";
                }
            }
        });
    </script>
    <script>
        const checkbox = document.getElementById('checkbox');

        checkbox.addEventListener('change', () => {
            document.body.classList.toggle('dark');
        })

        if (_getCookie("fontSize") != null) {
            var fontSize = _getCookie("fontSize");
            jQuery("body").css("font-size", fontSize + "px");
            $('a,p,input[type=text]').css("font-size", fontSize + "px");

        } else {
            var fs = jQuery("body").css('font-size');
            var fontSize = fs;
            jQuery("body").css("font-size", fs);
            $('a,p,input[type=text]').css("font-size", fs + "px");

        }
        // Cookies
        function _getCookie(name) {
            return localStorage.getItem('fontSize');

        }

        function _set_font_size(fontType) {

            if (fontType == 'increase') {
                if (parseInt(fontSize) < 20) {
                    fontSize = parseInt(fontSize) + 2;
                }
            } else if (fontType == "decrease") {
                if (parseInt(fontSize) > 10) {
                    fontSize = parseInt(fontSize) - 2;
                }
            } else {

                fontSize = 14;
                localStorage.clear();
            }
            // _setCookie("fontSize", fontSize);
            localStorage.setItem('fontSize', fontSize);
            jQuery("body").css("font-size", fontSize + "px");
            $('a,p,input[type=text]').css("font-size", fontSize + "px");
        }
        $('#largerTextLink').click(function() {
            _set_font_size('increase');
        });
        $('#resetFont').click(function() {
            _set_font_size();
            localStorage.clear();
        });
        $('#smallerTextLink').click(function() {
            _set_font_size('decrease');
        });
    </script>
</body>

</html>
