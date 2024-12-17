<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>JB | Jai Bangla</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("images/favicon.ico") }}">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Theme style -->
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
      page. However, you can choose any other skin. Make sure you
      apply the skin class to the body tag so the changes take effect.
      -->
    <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css" />
</head>

<body class="hold-transition skin-blue sidebar-mini">
  <nav class="navbar navbar-default" style="background-color: #3c8dbc; margin-bottom: 0;">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="#" style="font-size: 20px; color: #fff;">Jai Bangla</a>
      </div>
      <!-- <ul class="nav navbar-nav navbar-right">
        <li><a href="#" style="color: #fff;"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
      </ul> -->
    </div>
  </nav>
  <div class="container" style="margin: 20px; font-size: 16px; font-weight: bold;">
    {{$msg}}
  </div>

  <!-- Main Footer -->
  <div>
    <footer style="background: #fff; padding: 15px; color: #444; border-top: 1px solid #d2d6de; background-color: ghostwhite;">
      <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="http://nicwb.nic.in">NIC</a>.</strong> All rights reserved.
    </footer>
  </div>

    <!-- AdminLTE App -->
    <script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
</body>
</html>
