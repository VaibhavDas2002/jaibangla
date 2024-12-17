<?php 

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Jai Bangla
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
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Dupliacte Approval
          <!-- <small>Preview</small> -->
        </h1>
        <span style="font-size: 18px; ">
          <!-- {{ $filters }} -->
          @php
          if($filters == 'voter'){
            print 'Fitered By Epic Voter Id';
          }
          elseif($filters == 'ration'){
            print 'Filtered By Ration Card No';
          }
          else{
            print 'Filtered By Both Epic Voter Id and Ration Card No';
          }
          @endphp
        </span>
      </section>

      <!-- Main content -->
      <section class="content">

       <table id="example" class="display nowrap" cellspacing="0" width="100%">
        <thead>
              <tr role="row">
                <th width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Serial No</th>
                <th width="20%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Ration Card No</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Voter ID Card</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No of Benificiary</th>
                <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th>
                
              </tr>
            </thead>
            <tbody>
            @php $i=1; @endphp    
            @foreach($reports as $report)
                <tr role="row" class="odd">
                  <td class="sorting_1">@php print $i++; @endphp</td>
                  <td>
                    @php if($filters == 'ration'){ print $report->ration_card_no; } 
                    elseif($filters == 'voter') { print ''; }
                    else { print $report->ration_card_no; }@endphp
                  </td>
                  <td>
                    @php if($filters == 'voter'){ print $report->epic_voter_id; } 
                    elseif($filters == 'ration'){ print ''; }
                    else { print $report->epic_voter_id; }@endphp
                  </td>
                  <td>{{$report->ben_no}}</td>
                  <td>
                    <form method="POST" action="{{ route('accept-one-approval') }}">
                        <input type="hidden" name="filter" id="filter" value="{{$filters}}">
                        <input type="hidden" name="scheme_id" id="scheme_id" value="{{$scheme_id}}">
                        <input type="hidden" name="dist_code" id="dist_code" value="{{$dist_code}}">
                    @php if($filters == 'ration') { @endphp
                      <!-- <form method="POST" action="{{ route('accept-one-approval',['filter' => $filters, 'ration_card' => $report->ration_card_no, 'scheme_id' => $scheme_id,  'dist_code' => $dist_code]) }}"> -->
                        <input type="hidden" name="ration_card" id="ration_card" value="{{$report->ration_card_no}}">  
                    @php } elseif($filters == 'voter') { @endphp
                      <!-- <form method="POST" action="{{ route('accept-one-approval',['filter' => $filters, 'voter_id' => $report->epic_voter_id, 'scheme_id' => $scheme_id, 'dist_code' => $dist_code]) }}"> -->
                        <input type="hidden" name="ration_card" id="ration_card" value="{{$report->epic_voter_id}}">
                    @php } else { @endphp
                      <!-- <form method="POST" action="{{ route('accept-one-approval',['filter' => $filters, 'ration_card' => $report->ration_card_no, 'scheme_id' => $scheme_id]) }}"> -->
                        <input type="hidden" name="ration_card" id="ration_card" value="{{$report->ration_card_no}}">
                    @php } @endphp       
                        {{ csrf_field() }}
                          <button class="btn btn-primary btn-xs">View <i class="glyphicon glyphicon-edit"></i></button>
                      </form>
                  </td>
              </tr>
              @endforeach
            
            </tbody>
            <tfoot>
              <tr>
                <th width="10%" rowspan="1" colspan="1">Serial No</th>
                <th width="10%" rowspan="1" colspan="1">Ration Card No</th>
                <th width="10%" rowspan="1" colspan="1">Voter Id Card</th>
                <th width="20%" rowspan="1" colspan="1">No of Benificiary</th>
                <th width="20%" rowspan="1" colspan="1">Action</th>
              </tr>
            </tfoot>     
          
          
    </table>

  
<!-- /.row -->

</section>
</div>
<!-- /.content -->
@include('layouts.footer')
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
<script>
  $(document).ready(function() {
    $('#example').DataTable( {
      "pageLength":20,
      'lengthMenu': [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      dom: 'Bfrtip',
      buttons: [
      'pdf','excel','print'
      ]
    } );
  } );
</script>

</body>
</html>