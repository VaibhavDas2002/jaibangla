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
  .highlight_row {
    background: #eee;
  }
  .textbox_css {
    background-color: lightblue;
    box-sizing: border-box;
    border: 1px solid #555;
    outline: none;
  }
  .textbox_pre_css {
    box-sizing: border-box; 
    border: 1px solid #f2c2c2; 
    outline: none;
  }
  .textbox_pre_css:hover {
    cursor: not-allowed;
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
         Set Scheme Capacity
        </h1>
        <ol class="breadcrumb">
           <li><a href="#"><i class="fa fa-dashboard"></i> Set Scheme Capacity</a></li>
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
                <div class="col-sm-6">
                  <h3 class="box-title">Set Scheme Capacity</h3>
                </div>
                <div class="col-md-6">
                  <span class="text-danger" style="font-size: 13px; float: right; font-weight: bold;">Note: Please click the checkbox for enter the capacity</span>
                </div>
                
            </div>
          </div>
          <div class="box-body">
            <p class="text-primary" style="font-size: 16px; font-weight: bold;">
              <span>Scheme : {{$scheme_name}}</span>
              <span style="float: right;">Date : @php print date('d/m/Y'); @endphp</span>
            </p>
            <form method="POST" action="{{ url('add-capacity') }}" name="capacityForm" id="capacityForm">
              {{ csrf_field() }}
              <input type="hidden" name="scheme_id" id="scheme_id" value="{{$scheme_id}}">

              <div class="table-responsive">  
               <table id="example" class="display table table-responsive table-hover" cellspacing="0" width="100%" style="border-top: 1px solid #000;">

                <thead>
                  <tr role="row">
                      <th width="30%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="state: activate to sort column ascending">District</th>
                      <!-- <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Total Approved Beneficiary</th> -->
                      <th width="30%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Capacity [Existing Quota]</th>
                      <th width="2%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending" style="background: linear-gradient(to right, #ffccff 0%, #ccffff 100%);"><span class="fa fa-check-square"></span></th>
                      <th width="38%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending" style="background: linear-gradient(to right, #ccffff 0%, #ffccff 100%);">Enter Capacity [New Quota]</th>
                  </tr>
                </thead>
                <tbody style="font-size: 16px;">
                  @foreach($cap as $c)
                      <tr role="row" class="odd">
                        <td>{{ $c->name }}</td>
                        <!-- <td></td> -->
                        <td>{{ $c->capacity }}</td>
                        <td>
                          <input type="checkbox" name="s_cap_{{$c->code}}" id="s_cap_{{$c->code}}" value="_{{$c->name}}_{{$c->code}}" class="ben_checkbox"> <font class="text-primary"><b></b></font>
                        </td>
                        <td>
                          <input type="hidden" name="dist_arr[]" id="dist_arr_{{$c->code}}" disabled>
                          <input type="text" name="capacity[]" id="cap_{{$c->code}}" disabled class="textbox_pre_css" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" autocomplete="off">
                        </td>
                    </tr>
                  @endforeach
                </tbody>
            </table>
          </div>
          <div align="center">
            <input type="submit" class="btn btn-primary btn-lg" name="ben_pre_save" id="ben_pre_save" value="Add" style="margin-top: 10px;" disabled>
          </div>
        </form>
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
          <h4 class="modal-title">{{$scheme_name}} Capacity</h4>
        </div>
        <div class="modal-body">
          <span style="font-size: 16px;" class="text-danger"><b><i>Please check everything</i></b></span>
          <!-- <div style="float: right;">
            <span style="background-color: #ba3f3f; color: #fff; border-radius: 2px;"><i class="glyphicon glyphicon-remove" style="padding: 2px 10px 1px 10px;"></i></span> Capacity less than approved beneficiary<br>
            <span style="background-color: #c2c23e; color: #fff; border-radius: 2px;"><i class="glyphicon glyphicon-ok" style="padding: 1px 10px 1px 10px;"></i></span> Capacity very closed to approved beneficiary<br>
            <span style="background-color: #2db350; color: #fff; border-radius: 2px;"><i class="glyphicon glyphicon-ok" style="padding-left: 3px;"></i><i class="glyphicon glyphicon-ok" style="padding-right: 3px;"></i></span> Everything Ok
          </div> -->
          <div id="details_div"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-lg pull-left" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success btn-lg" name="final_submit" id="final_submit">Submit</button>
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
      "paging": false,
      "pageLength": 20,
      'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      "ordering": false,
      // 'searching':false,        
     
      buttons: [
       {
           extend: 'pdf',
           title: 'Report {{$scheme_name}} Capacity',
           messageTop:'Date:<?php echo date('d/m/Y');  ?> \n Scheme Name: {{$scheme_name}}',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2],

            }
       },
       {   
           extend: 'print',
           title: 'Report {{$scheme_name}} Capacity',
           messageTop:'Date:<?php echo date('d/m/Y');  ?> \n Scheme Name: {{$scheme_name}}',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2],
                stripHtml: true,
            }
       },
       {
           extend: 'excel',
           title: 'Report {{$scheme_name}} Capacity',
           messageTop:'Date:<?php echo date('d/m/Y');  ?> \n Scheme Name: {{$scheme_name}}',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2],
                stripHtml: true,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    });
  });


  $("input[type='checkbox']").change(function (e) {
    if ($(this).is(":checked")) {
        $(this).closest('tr').addClass("highlight_row");
        $(this).parent().next().find('input[type="text"]').addClass('textbox_css');
        $(this).parent().next().find('input[type="text"]').removeAttr('disabled');
        $(this).parent().next().find('input[type="text"]').removeClass('textbox_pre_css');
        $(this).parent().next().find('input[type="text"]').attr('placeholder', 'Enter value');
        $(this).parent().next().find('input[type="hidden"]').removeAttr('disabled');
        $(this).parent().next().find('input[type="hidden"]').val($(this).val());
        $('#ben_pre_save').prop('disabled', $('.ben_checkbox').filter(':checked').length < 1);
    } 
    else {
        $(this).closest('tr').removeClass("highlight_row");
        $(this).parent().next().find('input[type="text"]').removeClass('textbox_css');
        $(this).parent().next().find('input[type="text"]').attr('disabled','disabled');
        $(this).parent().next().find('input[type="text"]').addClass('textbox_pre_css');
        $(this).parent().next().find('input[type="text"]').removeAttr('placeholder');
        $(this).parent().next().find('input[type="text"]').val('');
        $(this).parent().next().find('input[type="hidden"]').attr('disabled','disabled');
        $('#ben_pre_save').prop('disabled', $('.ben_checkbox').filter(':checked').length < 1);
    }

  });
  var full_arr = [];
  $('#ben_pre_save').on('click', function(e){
    e.preventDefault();
    var values = $("input[name='dist_arr[]']").map(function(){return $(this).val();}).get();
    var cap = $("input[name='capacity[]']").map(function(){return $(this).val();}).get();
  
    var html = '';
    html += '<table id="preview_tbl" class="table table-bordered table-condensed"><tr style="background-color: #e6e6ff;"><th>District</th><th>New Quota Added</th></tr>';
    //console.log(values);
    for (var i = 0; i < values.length; i++) {
      if (values[i] != '' && cap[i] != '') {
        html  += '<tr>';
        var full_arr = values[i].split("_");
        html += '<td>'+full_arr[1]+'</td><td>'+cap[i]+'</td>';
        // if (Number(cap[i]) - Number(full_arr[0]) < 0){
        //   html += '<td>'+full_arr[1]+'</td><td>'+full_arr[0]+'</td><td>'+cap[i]+'</td><td style="background-color: #ba3f3f; color: #fff;"><i class="glyphicon glyphicon-remove"></i></td>';
        //   /*class="glyphicon glyphicon-remove text-danger"*/
        // }
        // else if (Number(cap[i]) - Number(full_arr[0]) < 100 ){
        //   html += '<td>'+full_arr[1]+'</td><td>'+full_arr[0]+'</td><td>'+cap[i]+'</td><td style="background-color: #c2c23e; color: #fff;"><i class="glyphicon glyphicon-ok"></i></td>';
        //   /*class="glyphicon glyphicon-remove text-danger"*/
        // }
        // else {
        //   html += '<td>'+full_arr[1]+'</td><td>'+full_arr[0]+'</td><td>'+cap[i]+'</td><td style="background-color: #2db350; color: #fff;"><i class="glyphicon glyphicon-ok"></i><i class="glyphicon glyphicon-ok"></i></td>';
        //   /*class="glyphicon glyphicon-ok text-success"*/
        // }
        html += '</tr>';
      }
    }

    html += '</table>';
    $('#details_div').html(html);
    $('#modal-default').modal('show');
    $('#final_submit').on('click', function(){
      if ($('#preview_tbl tr').length == 1) {
        //alert('Please enter capacity');
      }
      else {
        $('#final_submit').html('Please wait...');
        $('#final_submit').attr('disabled','disabled');
        $('#capacityForm').submit();
      }
    });
  });

  

</script>
<!-- REQUIRED JS SCRIPTS -->

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>

</body>
</html>