
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, minimum-scale=0.1" />
<title>Jai Bangla | Government of West Bengal</title>
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset("/images/biswofab.png") }}" />
<link
    href="https://fonts.googleapis.com/css?family=Open+Sans:400,600&amp;display=swap"
    rel="stylesheet" />
<!-- <link href="{{ asset("/css/boostrap-new.min.css") }}" rel="stylesheet" /> -->
<link rel="stylesheet" href="{{ asset ("/css/boostrap-new.min.css") }}"  type="text/css" >
<link rel="stylesheet" href="{{ asset ("/css/Boostrrap.css") }}"  type="text/css" >
<!-- <link href="{{ asset("/css/Boostrrap.css") }}" rel="stylesheet" /> -->
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
    background: url(images/Login_Page1.png) no-repeat center center;
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
                
                <!-- <div class="row">
                    <div class="col-xs-12 col-sm-5 col-md-offset-3 col-md-5">
                        <div class="alert alert-danger" role="alert">
                            The Site will be down for maintainance from 11:00PM - 12:00AM
                        </div>
                    </div>
                </div> -->
                <div class="row">

                    <div class="col-xs-12 col-sm-7 col-md-offset-4 col-md-4">
                        <form class="form-horizontal" method="POST" action="{{ route('ds_status_check_sendotp') }}">
                        {{ csrf_field() }}

                        <!-- @if (session('otp'))
                            <div class="alert alert-success">
                                {{ session('otp') }}
                            </div>
                        @endif -->
                        <table width="100%" class="adminlogintable">
                            <tr>
                                <td>
                                    @if (session('msg'))
                                    <div class="alert alert-danger" style="text-align: center;">
                                        {{ session('msg') }}
                                    </div>
                                    <!-- <span style="color: red;">{{ session('msg') }} </span> -->
                                    @endif
                                       @if(count($errors) > 0)
                                            <div class="alert alert-danger alert-block">
                                            <ul>
                                            @foreach($errors as $error)
                                            <li><strong> {{ $error }}</strong></li>
                                            @endforeach
                                            </ul>
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
                                    placeholder="Enter Registered Mobile No." required autofocus autocomplete="off">
                                   
                                </td>
                            </tr>
                           

                           
                           

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

                            



                        </table>
                        <input type="hidden" name="scheme_id" id="scheme_id" value="{{$scheme_id}}"/>
                    </form>
                    </div>
                    <div class="col-xs-0 col-sm-5 col-md-4">
                    <img class="cm" src="{{ asset("images/CM_CMO_Image.png") }}" alt="" />
                    </div>
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
    });
    </script>
</body>

</html>

<?php
    Session::forget('msg');
?>
