<?php 

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>JB | Jai Bangla
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
      <link href="{{ asset("css/select2.min.css") }}" rel="stylesheet">
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
      <!-- <section class="content-header">

        
      </section> -->

      <!-- Main content -->
      <section class="content">
      <div class="col-md-12">
<button type="button" class="btn btn-success btn-sm" onclick="window.location.href='{{route('generic-lot')}}'">Back</button>
      </div>
       <font size="5">Making lot for <b>{{$lot_month}}</b></font><br>
       <font size="3"> For Scheme <b>{{$scheme_name}}</font><br>
       <font size="3">Your Selected Total Beneficiary is: <b><span id="add_ben_count"></span></b></font><br>
       <span class="text-danger"><b>( Please select the beneficiary between 5000 to 10000 )</b></span>
       <font size="3">Showing Lots of <b>{{$month}}</b> paid through {{$pmt_mode}} payment mode</font>
       <form method="POST" action="{{ route('generic-lot-number') }}" onsubmit="return validate() && confirm('Are you sure want to add/proceed with it?');">
        {{csrf_field()}}
        <input type="hidden" name="total_ben_checked" id="total_ben_checked" class="form-control">
        <input type="hidden" name="lot_no_arr" id="lot_no_arr" class="form-control">
        <input type="hidden" name="lot_year" id="lot_year" value="{{$lot_year}}" class="form-control">
        <input type="hidden" name="lot_month" id="lot_month" value="{{$lot_month}}" class="form-control">
        <input type="hidden" name="scheme_id" id="scheme_id" value="{{$scheme_id}}" class="form-control">
        <input type="hidden" name="chk_paid_yymm" id="chk_paid_yymm" value="{{$chk_paid_yymm}}" class="form-control">
        <input type="hidden" name="pmt_mode" id="pmt_mode" value="{{$pmt_mode}}" class="form-control">
        <input type="hidden" name="target_mode" id="target_mode" value="{{$target_mode}}" class="form-control">
        <input type="hidden" name="lot_type" id="lot_type" value="{{$lot_type}}" class="form-control">
        <input type="hidden" name="select_category" id="select_category" value="{{$select_category}}" class="form-control">
        
        <input type="submit" name="btn_append_lot" value="Create new Lot" id="btn_append_lot" class="btn btn-success col-md-3">
    
      </form>
       

       <table id="example" class="display" cellspacing="0" width="100%"> 

        <thead>
          <tr role="row" class="sorting_asc" style="font-size: 12px;">
            <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Sl No</th>
            <th  width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Lot No</th>
            <!-- <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Scheme Id</th> -->
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of Beneficiary</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of IFMS Wrong Data</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of RBI Failed</th>
            <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of RBI Success</th>
            <th>Action</th>      
          </tr>
        </thead>
        <tbody>
          @php $i=1; @endphp
            @foreach($reports as $report)
            <tr> 
              <td>@php print $i++; @endphp</td>
              <td>{{ $report->lot_no }}</td>
              <!-- <td>@php if($report->scheme_id == 1) {print '1-Jai Johar';} elseif($report->scheme_id == 3) {print '3-Taposili Bandhu';} @endphp</td> -->
              <td>{{ $report->ben_count }}</td>
              <td>@php if($report->ifms_wrongdata_count == '') {print '0';} else {print $report->ifms_wrongdata_count;} @endphp</td>
              <td>@php if($report->rbi_failed_count == '') {print '0';} else {print $report->rbi_failed_count;} @endphp</td>
              <td>@php if($report->rbi_success_count == '') {print '0';} else {print $report->rbi_success_count;} @endphp</td>
              
              <td>
                <input type="checkbox" name="checkbox_append_lot_<?php print $i; ?>" value="{{$report->rbi_success_count}}-{{$report->lot_no}}" onchange="appendlot(this.value,this.checked);">  
              </td>
            </tr>   
            @endforeach   
        </tbody>
        <!-- <tfoot> -->
       
        <!-- </tfoot> -->       
    </table>
 
  </div>
 
</div>

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
      //dom: 'Bfrtip',
      "paging": true,
      "pageLength":20,
      "lengthMenu": [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      // buttons: [
      // 'pdf','excel','print'
      // ]
    });
  });
    var lot_arr = [];
    document.getElementById('add_ben_count').innerHTML = 0;
    function appendlot(value, check){
        if (check == true){
            var myarr = value.split("-");
            var total_ben = myarr[0];
            var lot_no = myarr[1];

            if (document.getElementById('total_ben_checked').value == '') {
                document.getElementById('total_ben_checked').value = Number(total_ben);
                document.getElementById('add_ben_count').innerHTML = document.getElementById('total_ben_checked').value;
                lot_arr.push(lot_no);
                document.getElementById('lot_no_arr').value = lot_arr;
                //document.getElementById('btn_append_lot').disabled = false;
            }
            else { 
                var count = document.getElementById('total_ben_checked').value;
                var final_count = Number(count) + Number(total_ben);
                document.getElementById('total_ben_checked').value = final_count;
                document.getElementById('add_ben_count').innerHTML = final_count;
                lot_arr.push(lot_no);
                document.getElementById('lot_no_arr').value = lot_arr;
            }  
        }
        else{
            var myarr = value.split("-");
            var total_ben = myarr[0];
            var lot_no = myarr[1];
            document.getElementById('total_ben_checked').value -= total_ben;
            document.getElementById('add_ben_count').innerHTML = document.getElementById('total_ben_checked').value;
            var index = lot_arr.indexOf(lot_no);
            if (index > -1) {
              lot_arr.splice(index, 1);
            }
            document.getElementById('lot_no_arr').value = lot_arr;
        }  
      }

    function validate() {
        var total = document.getElementById('total_ben_checked').value;
        if (total > 10000) {
            alert('Please Select Beneficiary less than 10000');
            return false;
        }
        if (total < 1 && total > 0) {
            alert('Please Select Beneficiary greater than 5000');
            return false;
        }
        if (total < 0) {
            alert('Please go to the "Append Lot" link in the sidebar, and do it again!!');
            return false;
        }
        if (total == '' || total == 0) {
            alert('Please select the checkbox!!');
            return false;
        }
        return true;
    }
</script>

</body>
</html>