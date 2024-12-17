<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>JB | JaiBangla
  </title>
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset("frontend/img/favicon.ico") }}">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"> -->
  <!-- Theme style -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
      <link href="{{ asset("css/select2.min.css") }}" rel="stylesheet">
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet" type="text/css" />  
  
   
   
   <link rel="stylesheet" href="{{ asset("/css/jquery.dataTables.min.css")}}">
    <link rel="stylesheet" href="{{ asset("/css/buttons.dataTables.min.css")}}">

   

   
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
    table.dataTable thead th, table.dataTable thead td{
      padding:10px 13px;
    }
    table.dataTable tfoot th, table.dataTable tfoot td{
      padding:10px 5px;
    }

    .criteria1{
      text-transform: uppercase;
      font-weight: bold;
    }
    
    #example_length{
      margin-left: 40%;
      margin-top: 2px;
    }
    @keyframes spinner {
    to {transform: rotate(360deg);}
  }
   
  .spinner:before {
    content: '';
    box-sizing: border-box;
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin-top: -10px;
    margin-left: -10px;
    border-radius: 50%;
    border: 2px solid #ccc;
    border-top-color: #333;
    animation: spinner .6s linear infinite;
  }
  .select2{
      width:100%!important;
    }
    .select2 .has-error {
      border-color:#cc0000;
     background-color:#ffff99;
  }

  button {
    font-size: 16px;
  }

  /*Modal*/
  .example-modal .modal {
    position: relative;
    top: auto;
    bottom: auto;
    right: auto;
    left: auto;
    display: block;
    z-index: 1;
  }

  .example-modal .modal {
    background: transparent !important;
  }

  #preloader1{
    position: fixed;
    top:40%;
    left: 52%;
    z-index: 999;
  }
  #preloader1 {
    background: transparent !important;
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
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
         Report Repeat Lot Master
        </h1>
        <ol class="breadcrumb">
           <li><a href="#"><i class="fa fa-dashboard"></i> Report Repeat Lot Master</a></li>
          <!-- <li class="active"></li> -->
        </ol>
      </section>

      <div id="preloader1" align="center" style="display: none;">
        <img src="images/ZKZg.gif" width="100px">
      </div>
      <!-- Main content -->
      <section class="content">

        <div class="box box-default">
          <div class="box-header with-border">
            <div class="row">
                <div class="col-sm-8">
                  <h3 class="box-title">Report Repeat Lot Master</h3>
                </div>
                
            </div>
          </div>
          <div class="box-body">
          
            <div style="font-size: 16px; font-weight: bold;" class="text-primary">
              <span style="float: right;">Date : @php print date('d/m/Y'); @endphp</span>
              <span>Scheme : {{$scheme_name}}</span><br>
              <span>Month : {{$month}} - @php print date("F", strtotime("$month +1 month")); @endphp</span><br>
              <span>Year : {{$year}}</span>
            </div>
            <div class="table-responsive">
        
               <table id="example" class="display table-responsive" cellspacing="0" width="100%">

                <thead>

                      <tr role="row" style="font-size: 14px;">
                        <th class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">SL No.</th>

                        <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Parent Lot No</th>
                        <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Parent Lot Total Ben</th>
                        <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Parent Lot Payment Mode</th>
                        <th width="8%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Parent No.of Beneficiary Success</th>
                        <th width="8%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Parent No.of Beneficiary IFMS Failed</th>
                        <th width="8%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Parent No.of Beneficiary Error</th>
                        <th width="8%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of Duplicate Ben</th>
                        <th width="8%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of Adjusted Ben</th>
                        <th width="8%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of De- activated Ben</th>
                        <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Child Lot No</th>
                        <th  width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Child Lot Total Ben</th>
                        <th  width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Child Lot Month</th>
                        <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Child Lot Payment Mode</th>
                        <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Remarks</th>
                      </tr>
                    </thead>
                    <!-- 
                      Number of fields :=>
                      1. Parent Lot No                     => parent_lot_no
                      2. Parent Payment Mode               => parent_payment_mode
                      3. Parent Total Beneficiary          => parent_total
                      4. Parent Total Success Beneficiary  => a
                      new. Parent Total Beneficiary IFMS Failed => ifms_failed
                      5. Parent Total Error Beneficiary    => err
                      6. Success Adjuasted Beneficiary     => b
                      7. Success Duplicate Beneficiary     => c
                      8. Success Deactivated Beneficiary   => d
                      9. Child Lot No                      => repeat_drn_part
                      10.Child Total Beneficiary           => e
                      11.Child Payment Mode                => child_payment
                      12.Child Lot Month                   => child_lot_month
                      13.Remarks                           => rem
                      14.Parent Lot : Remarks(Modal)       => remark
                     -->
                    <tbody style="font-size: 15px;">
                      @php $i = 1; @endphp
                      @foreach($results as $res)
                      <tr>
                        <td>@php print $i++; @endphp</td>
                        <td width="10%">{{ $res->parent_lot_no }}</td>
                        <td width="5%">{{ $res->parent_total }}</td>
                        <td width="5%">{{ $res->parent_payment_mode }}</td>
                        <td width="8%">{{ $res->a }}</td>
                        <td width="5%">{{ $res->ifms_failed }}</td>
                        <td width="8%">{{ $res->err }}</td>
                        <td width="8%">{{ $res->c }}</td>
                        <td width="8%">{{ $res->b }}</td>
                        <td width="8%">{{ $res->d }}</td>
                        <td width="5%">{{ $res->repeat_drn_part }}</td>
                        <td width="10%">{{ $res->e }}</td>
                        <td width="5%">{{ $res->child_lot_month }}</td>
                        <td width="10%">{{ $res->child_payment }}</td>
                        <td width="5%">
                          @if($res->rem == 'OK')
                          <span class="label label-success">{{$res->rem}}</span>
                          <!-- <button class="btn btn-success btn-xs btn-remarks" value="{{$res->remark}}">{{$res->rem}}</button> -->
                          <!--<button class="btn btn-success btn-xs btn-remarks" value="{{$res->repeat_drn_part}}" onclick="getData(this.value);">{{$res->rem}}</button>-->
                          @elseif($res->rem == 'Parallel Lot')
                          <span class="label label-warning">{{$res->rem}}</span>
                          @else
                          <!-- <button class="btn btn-danger btn-xs btn-remarks" value="{{$res->remark}}">{{$res->rem}}</button> -->
                          <button class="btn btn-danger btn-xs btn-remarks" value="{{$res->repeat_drn_part}}" onclick="getData(this.value);">{{$res->rem}}</button>
                          @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                    <!-- <tfoot> -->
                  
                    <!-- </tfoot> -->

                    
                  
                  
            </table>
	   </div>
          </div>
        </div>
        
  </div>
  <!-- Footer -->
  @include('layouts.footer')
  </div>


  <!-- Modal -->
  <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Lot Remarks</h4>
        </div>
        <div class="modal-body">
          <h5 align="center"><b>Parent Lot No and Remarks</b></h5>
          <div id="lot_remarks"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <!-- <button type="button" class="btn btn-primary"><i class="fa fa-print"></i> Print</button> -->
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->


<!-- /.row -->
</section>
<!-- /.content -->

</div>

<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>
<script src="{{ URL::asset('js/site.js') }}"></script>

<script>
  $('.select2').select2();
</script>
<script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset("js/dataTables.buttons.min.js") }}"></script>
<script src="{{ asset("js/buttons.flash.min.js") }}"></script>
<script src="{{ asset("js/jszip.min.js") }}"></script>
<script src="{{ asset("js/pdfmake.min.js") }}"></script>
<script src="{{ asset("js/vfs_fonts.js") }}"></script>
<script src="{{ asset("js/buttons.html5.min.js") }}"></script>
<script src="{{ asset("js/buttons.print.min.js") }}"></script>


<script>

  $(document).ready(function() {
    
    $('#example').DataTable( {
      //dom: 'Bfrtip',
      dom: 'Blfrtip',
      "paging": true,
      "pageLength": 20,
      'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
                 
     
      buttons: [
       {
           extend: 'pdf',
           //title: 'Line Listing Report of  ',
           title: 'Report Repeat Lot Master {{$month}} - <?php print date("F", strtotime("$month +1 month")); ?> {{$year}} {{$scheme_name}} <?php echo date('d-m-Y');  ?>',
           messageTop:'Year: {{$year}}\n Month: {{$month}} - <?php print date("F", strtotime("$month +1 month")); ?>\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: {{$scheme_name}}',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13],

            }
       },
       {   
           extend: 'print',
           title: 'Report Repeat Lot Master {{$month}} - <?php print date("F", strtotime("$month +1 month")); ?> {{$year}} {{$scheme_name}} <?php echo date('d-m-Y');  ?>',
           messageTop:'Year: {{$year}}\n Month: {{$month}} - <?php print date("F", strtotime("$month +1 month")); ?>\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: {{$scheme_name}}',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13],
                stripHtml: true,
            }
       },
       {
           extend: 'excel',
           title: 'Report Repeat Lot Master {{$month}} - <?php print date("F", strtotime("$month +1 month")); ?> {{$year}} {{$scheme_name}} <?php echo date('d-m-Y');  ?>',
           messageTop:'Year: {{$year}}\n Month: {{$month}} - <?php print date("F", strtotime("$month +1 month")); ?>\n Date:<?php echo date('d/m/Y');  ?>\n Scheme Name: {{$scheme_name}}',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13],
                stripHtml: true,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    } );

    //$('[data-toggle="tooltip"]').tooltip();
  });

  function getData(value){
    document.getElementById('preloader1').style.display = '';
    getParentLotData(value);
  }
  
  function getParentLotData(child_lot_no){
    loadReportRepeatLotDetailsItems(child_lot_no, '../api/getReportRepeatLotRemarks/');
  }
  function loadReportRepeatLotDetailsItems(child_lot_no, path){
    $.ajax({
      type: 'GET',
      url: path +child_lot_no,
      
      success: function (datas) {
        if (!datas || datas.data.length === 0) {
          //alert("sucess with 0 data");
          return;
        }
        $('#lot_remarks').text('');
        var rem = datas.data[0].remark;
        var arr = rem.split('.');
        for (var i = 0; i < arr.length ; i++) {
          $('#lot_remarks').append('<p>'+arr[i]+'</p>');
        }
        document.getElementById('preloader1').style.display = 'none';
        $('#modal-default').modal('show');
        //$('#lot_remarks').text(datas.data[0].remark);
      },
      error: function (ex) {
         //alert('error url');
      }
    });
  }

</script>
<!-- REQUIRED JS SCRIPTS -->

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>

</body>
</html>