<?php 

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>
  </title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet" type="text/css" />  
  

   
   <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
   <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">

   

   
   <style>
   .errorField{
    border-color: #990000;
  }
  .searchPosition{
    margin:70px;
  }
  .submitPosition{
    margin: 25px 0px 0px 0px;
  }

  
  .typeahead { border: 2px solid #FFF;border-radius: 4px;padding: 8px 12px;max-width: 300px;min-width: 290px;background: rgba(66, 52, 52, 0.5);color: #FFF;}
  .tt-menu { width:300px; }
  ul.typeahead{margin:0px;padding:10px 0px;}
  ul.typeahead.dropdown-menu li a {padding: 10px !important;  border-bottom:#CCC 1px solid;color:#FFF;}
  ul.typeahead.dropdown-menu li:last-child a { border-bottom:0px !important; }
  .bgcolor {max-width: 550px;min-width: 290px;max-height:340px;background:url("world-contries.jpg") no-repeat center center;padding: 100px 10px 130px;border-radius:4px;text-align:center;margin:10px;}
  .demo-label {font-size:1.5em;color: #686868;font-weight: 500;color:#FFF;}
  .dropdown-menu>.active>a, .dropdown-menu>.active>a:focus, .dropdown-menu>.active>a:hover {
    text-decoration: none;
    background-color: #1f3f41;
    outline: 0;
  }
  
</style>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- Google Font -->
<link rel="stylesheet"
href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

</head>
<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    
    <!-- Main Header -->
    @include('layouts.header')
    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <section class="content-header">
      <h1>
        Daily Sitrep
        <small>Date: <?php echo date('d/m/Y'); ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><a href="#">DailySitrep</a></li>        
      </ol>
      </section>
      <section class="invoice">
      <div id="page-content">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            Daily Sitrep
            <small class="pull-right">Date: <?php echo date('d/m/Y'); ?></small>
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      <div class="row invoice-info">
        <div class="col-sm-1 invoice-col">
          To:          
        </div>
        <div class="col-sm-11 invoice-col">        
        {{$config->report_to}}
        </div>
        <div class="col-sm-12 invoice-col">&nbsp;</div>
        <div class="col-sm-1 invoice-col">
          From:
        </div>
        <div class="col-sm-5 invoice-col">
        {{$config->report_from}} <br>
        {{$config->org_no}} 
        </div>
        <div class="col-sm-6 invoice-col">
        <br/>
          Dated: <?php echo date('d/m/Y'); ?>
        </div>
        <div class="col-sm-1 invoice-col">
          &nbsp;        
        </div>
        <br/>
        <div class="col-sm-11 invoice-col">
          Ref: {{$config->ref}}           
        </div>
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table">
            <tr>
              <td>1</td>
              <td>Activities of Extrimist Groups :</td>             
            </tr>
            <tr>
              <td>2</td>
              <td>Activities of Various Communal/Fundamentalist groups :</td>             
            </tr>
            <tr>
              <td>3</td>
              <td>Activities of Rowdy's/ Extorsion :</td>              
            </tr>            
            <tr>
              <td>4</td>
              <td>Industrial Disputes(Lock out/Suspension of Work/Closure) :</td>              
            </tr>
            <tr>
              <td>5</td>
              <td>Political disturbances (Clash/Proccession/Meeting etc) :</td>              
            </tr>
            <tr>
              <td>6</td>
              <td>Other intelligence related information :</td>              
            </tr>
            <tr>
              <td>7</td>
              <td>Outstanding good work done by Police :</td>              
            </tr>
            <tr>
              <td>8</td>
              <td>
                Total No of Arrests :<strong>48</strong><br/>
                <p>i) Arrest in specefic cases:- 10, PS Name case No dt u/s <strong>Arrest-01</strong></p>
                <p>ii) Arrest in Preventive Cases:- <strong>30</strong></p>
                <p>
                <div class="table-responsive">
                  <table class="table table-bordered">
                  <tr>
                  <td align="center">34 Police Act</td><td align="center">42/290 IPC</td><td align="center">151/107 Cr. PC</td><td align="center">41/109 Cr. PC</td><td align="center">Others</td><td align="center">Total</td>
                  </tr>
                  <tr>
                  <td align="center">29</td><td align="center">01</td><td align="center">00</td><td align="center">00</td><td align="center">00</td><td align="center">00</td>
                  </tr>
                  </table>
                </div>
                </p>
                <p>iii) Gambling and PC Act Arrested:-00, Board Money:-Nil</p>                
                <p>iv) Warrentees Arrested:-<strong>08</strong>Recalled:-<strong>05</strong>Otherwise:-<strong>08</strong></p>
                <p>v) Compound Slip:-Nil</p>
                
              </td>              
            </tr>
            <tr>
              <td>9</td>
              <td>Sizure of Arms Ammunation and Explosive :</td>              
            </tr>
            <tr>
              <td>10</td>
              <td>Gist of FIR:
                  <p>i.&nbsp; Ref Naihati PS Case No Lorem ipsum dolor sit amet, volutpat sit viverra, porttitor ac proin pellentesque hendrerit purus eu. Per eu ipsum congue, urna hymenaeos eros rutrum dolor, ante interdum lacinia ac ac facilisis. Aliquam nam cras, eleifend amet ligula, sed pretium ante parturient semper. Tincidunt amet tincidunt vestibulum, nunc molestie ornare, labore pellentesque enim. Sed id suspendisse, odio sollicitudin ipsum vestibulum, maecenas quisquam tortor a ridiculus justo, nibh aliquam urna. Vestibulum wisi, quam suscipit et posuere, ante ullamcorper ullamcorper, id at sapien aliquam gravida fringilla et, dictumst euismod odio.</p>
                  <p>ii.&nbsp; Ref Naihati PS Case No Lorem ipsum dolor sit amet, volutpat sit viverra, porttitor ac proin pellentesque hendrerit purus eu. Per eu ipsum congue, urna hymenaeos eros rutrum dolor, ante interdum lacinia ac ac facilisis. Aliquam nam cras, eleifend amet ligula, sed pretium ante parturient semper. Tincidunt amet tincidunt vestibulum, nunc molestie ornare, labore pellentesque enim. Sed id suspendisse, odio sollicitudin ipsum vestibulum, maecenas quisquam tortor a ridiculus justo, nibh aliquam urna. Vestibulum wisi, quam suscipit et posuere, ante ullamcorper ullamcorper, id at sapien aliquam gravida fringilla et, dictumst euismod odio.</p>
                  <p>iii.&nbsp; Ref Naihati PS Case No Lorem ipsum dolor sit amet, volutpat sit viverra, porttitor ac proin pellentesque hendrerit purus eu. Per eu ipsum congue, urna hymenaeos eros rutrum dolor, ante interdum lacinia ac ac facilisis. Aliquam nam cras, eleifend amet ligula, sed pretium ante parturient semper. Tincidunt amet tincidunt vestibulum, nunc molestie ornare, labore pellentesque enim. Sed id suspendisse, odio sollicitudin ipsum vestibulum, maecenas quisquam tortor a ridiculus justo, nibh aliquam urna. Vestibulum wisi, quam suscipit et posuere, ante ullamcorper ullamcorper, id at sapien aliquam gravida fringilla et, dictumst euismod odio.</p>
                  <p>iv.&nbsp; Ref Naihati PS Case No Lorem ipsum dolor sit amet, volutpat sit viverra, porttitor ac proin pellentesque hendrerit purus eu. Per eu ipsum congue, urna hymenaeos eros rutrum dolor, ante interdum lacinia ac ac facilisis. Aliquam nam cras, eleifend amet ligula, sed pretium ante parturient semper. Tincidunt amet tincidunt vestibulum, nunc molestie ornare, labore pellentesque enim. Sed id suspendisse, odio sollicitudin ipsum vestibulum, maecenas quisquam tortor a ridiculus justo, nibh aliquam urna. Vestibulum wisi, quam suscipit et posuere, ante ullamcorper ullamcorper, id at sapien aliquam gravida fringilla et, dictumst euismod odio.</p>
                  <p>v.&nbsp; Ref Naihati PS Case No Lorem ipsum dolor sit amet, volutpat sit viverra, porttitor ac proin pellentesque hendrerit purus eu. Per eu ipsum congue, urna hymenaeos eros rutrum dolor, ante interdum lacinia ac ac facilisis. Aliquam nam cras, eleifend amet ligula, sed pretium ante parturient semper. Tincidunt amet tincidunt vestibulum, nunc molestie ornare, labore pellentesque enim. Sed id suspendisse, odio sollicitudin ipsum vestibulum, maecenas quisquam tortor a ridiculus justo, nibh aliquam urna. Vestibulum wisi, quam suscipit et posuere, ante ullamcorper ullamcorper, id at sapien aliquam gravida fringilla et, dictumst euismod odio.</p>
                  <p>vi.&nbsp; Ref Naihati PS Case No Lorem ipsum dolor sit amet, volutpat sit viverra, porttitor ac proin pellentesque hendrerit purus eu. Per eu ipsum congue, urna hymenaeos eros rutrum dolor, ante interdum lacinia ac ac facilisis. Aliquam nam cras, eleifend amet ligula, sed pretium ante parturient semper. Tincidunt amet tincidunt vestibulum, nunc molestie ornare, labore pellentesque enim. Sed id suspendisse, odio sollicitudin ipsum vestibulum, maecenas quisquam tortor a ridiculus justo, nibh aliquam urna. Vestibulum wisi, quam suscipit et posuere, ante ullamcorper ullamcorper, id at sapien aliquam gravida fringilla et, dictumst euismod odio.</p>
                  <p>vii.&nbsp; Ref Naihati PS Case No Lorem ipsum dolor sit amet, volutpat sit viverra, porttitor ac proin pellentesque hendrerit purus eu. Per eu ipsum congue, urna hymenaeos eros rutrum dolor, ante interdum lacinia ac ac facilisis. Aliquam nam cras, eleifend amet ligula, sed pretium ante parturient semper. Tincidunt amet tincidunt vestibulum, nunc molestie ornare, labore pellentesque enim. Sed id suspendisse, odio sollicitudin ipsum vestibulum, maecenas quisquam tortor a ridiculus justo, nibh aliquam urna. Vestibulum wisi, quam suscipit et posuere, ante ullamcorper ullamcorper, id at sapien aliquam gravida fringilla et, dictumst euismod odio.</p>
                  <p>viii.&nbsp; Ref Naihati PS Case No Lorem ipsum dolor sit amet, volutpat sit viverra, porttitor ac proin pellentesque hendrerit purus eu. Per eu ipsum congue, urna hymenaeos eros rutrum dolor, ante interdum lacinia ac ac facilisis. Aliquam nam cras, eleifend amet ligula, sed pretium ante parturient semper. Tincidunt amet tincidunt vestibulum, nunc molestie ornare, labore pellentesque enim. Sed id suspendisse, odio sollicitudin ipsum vestibulum, maecenas quisquam tortor a ridiculus justo, nibh aliquam urna. Vestibulum wisi, quam suscipit et posuere, ante ullamcorper ullamcorper, id at sapien aliquam gravida fringilla et, dictumst euismod odio.</p>
              </td>
            </tr>
            <tr>
              <td>11</td>
              <td>Any other important incident likely to recieve public attension :</td>              
            </tr>
            <tr>
              <td>12</td>
              <td>Report regading raid agaisnt ID, Liquor,etc :</td>              
            </tr>
            <tr>
              <td>13</td>
              <td>Report regarding Chit Funds :</td>              
            </tr>
            <tr>
                <td>13</td>
                <td>Ref. Org No 1062/CR Dt of Police, West Bengal, <br/>
                <p>
                <div class="table-responsive">
                  <table class="table">
                  <tr>
                    <td>Sl</td>
                    <td>District</td>
                  </tr>
                  <tr>
                  
                  </tr>
                  </table>
                </div>
                </p>
                </td>
            </tr>
            </tbody>
          </table>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      </div>
      

      <!-- this row will not appear when printing -->
      <div class="row no-print">
        <div class="col-xs-12">
          
          <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
          <button type="button" class="btn btn-success pull-right jquery-word-export"><i class="fa fa-credit-card"></i> Export As .doc
          </button>
          <button type="button" class="btn btn-primary pull-right" style="margin-right: 5px;">
            <i class="fa fa-download"></i> Generate PDF
          </button>

        </div>
      </div>
    </section>

    </div>
  
</div>
<!-- /.row -->

</section>
<!-- /.content -->
</div>


<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<!-- FileSaver.js (necessary for saving the word document!) -->
<script src="{{ asset("js/FileSaver.js") }}"></script>
<!-- jQuery-Word-Export plugin -->
<script src="{{ asset("js/jquery.wordexport.js") }}"></script>
<script>
  $(document).ready(function() {
    $('#example').DataTable( {
      dom: 'Bfrtip',
      buttons: [
      'pdf','excel','csv','print','copy'
      ]
    } );   
    
    $(".jquery-word-export").click(function(event) {
      var today='<?php echo date('d/m/Y'); ?>';      
      $("#page-content").wordExport('Sitrep_'+today);
    });
  } );
</script>

</body>
</html>