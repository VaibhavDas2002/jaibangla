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
          Duplicate Approval
          <!-- <small>Preview</small> -->
        </h1>
        <h4>Fiter Using Card No: <b>{{ $card_no }}</b></h4>
      </section>

      <!-- Main content -->
      <section class="content">
      @php if($ben_reports =='')
          { @endphp
            <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert" ><a href="{{URL('duplicate-approval')}}">Back</a></button> 
                    <strong>One of the beneficiary ID is under transaction process. Rejection will be available once transaction is completed. </strong>
                </div>
      @php }
      else {  @endphp      
     
       <table id="example" class="display compact" cellspacing="0" width="100%">
        <thead>
              <tr role="row">
                <!-- <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Serial No</th> -->
                <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Id</th>
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Name</th>
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Father's Name</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Voter ID Card</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Ration Card</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Block/ Municipality</th>
                <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank Details</th>
                <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Pay Count</th>
                <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Last Pay YY-MM</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th>
                
              </tr>
            </thead>
            <tbody>
            @php $i=1; $pay_count = 0; @endphp    
            @foreach($ben_reports as $report)
            @php 
              $ben_id_arr[] = $report->id; 
              $ben_id_str = implode(',',$ben_id_arr);
              $pay_count = $pay_count + $report->payment_count;
            @endphp
                <tr role="row" class="odd">
                  <!-- <td class="sorting_1">@php print $i++; @endphp</td> -->
                  <td>{{$report->id}}</td>
                  <td>{{ $report->ben_fname }} {{ $report->ben_mname }} {{ $report->ben_lname }}</td>
                  <td>{{ $report->father_fname }} {{ $report->father_mname }} {{ $report->father_lname }}</td>
                  <td>{{ $report->epic_voter_id }}</td>
                  <td>{{ $report->ration_card_cat }}-{{ $report->ration_card_no }}</td>
                  <td>{{ $report->block_ulb_name }}</td>
                  <td>
                  	<div align="center" class="text-success"><b>IFSC: {{ $report->bank_ifsc }} </b></div>
                        <div align="center" style="border: 1px solid #000;padding: 5px;border-radius: 5px; background-color: #fffaeb;"><b>Acc No: {{ $report->bank_code }}</b></div>
                  </td><!-- 
                  <td>{{ $report->bank_ifsc }}</td> -->
                  <td>{{ $report->payment_count }}</td>
                  <td><!-- {{$report->last_paid_yymm}} -->
                    <?php
                    if ($report->last_paid_yymm == 0) {
                      print $report->last_paid_yymm;
                    }
                    else{
                      $month_arr=[];
                      $month_arr = ['01'=> 'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'];
                      $date = $report->last_paid_yymm;
                      $arr = str_split($date, 2);
                      $year = $arr[0];
                      $month = $arr[1];
                      foreach($month_arr as $key=>$value){
                        if($month == $key){
                          $month_final = $value;
                        }
                      }
                      print $month_final.'-'.$year;
                      //print implode('-', array_reverse(str_split($date, 2)));
                    } 
                    ?>
                      
                  </td>
                  <td>
				  @if($report->payment_count>0)
                    <input type="checkbox" name="ben_{{$report->id}}" id="ben_{{$report->id}}" 
                    value="{{$report->id}}-{{$report->payment_count}}-{{$report->last_paid_yymm}}-{{$report->ben_fname}} {{$report->ben_mname}} {{$report->ben_lname}}" 
                    class="ben_checkbox" onchange="myFunction(this.checked,this.value);"><span class="text-success"><b>Check For Approve</b></span>
                  @else
					<input type="checkbox" disabled title="NOTE: No payment details to reconcile.&#10;&#13;Follow UPDATE BENEFICIARY DETAILS->STOP PAYMENT to Reject"><span><font color="#f23322"><b>Check For Reject</b></font></span>
				  @endif
                  </td>
              </tr>
              @endforeach
            
            </tbody>
            <tfoot>
              <tr>
                <!-- <th width="5%" rowspan="1" colspan="1">Serial No</th> -->
                <th width="5%" rowspan="1" colspan="1">Id</th>
                <th width="10%" rowspan="1" colspan="1">Name</th>
                <th width="10%" rowspan="1" colspan="1">Father's Name</th>
                <th width="10%" rowspan="1" colspan="1">Voter Id Card</th>
                <th width="10%" rowspan="1" colspan="1">Ration Card</th>
                <th width="10%" rowspan="1" colspan="1">Block/ Municipality</th>
                <th width="30%" rowspan="1" colspan="1">Bank Details</th>
                <th width="5%" rowspan="1" colspan="1">Pay Count</th>
                <th width="5%" rowspan="1" colspan="1">Last Pay YY-MM</th>
                <th width="10%" rowspan="1" colspan="1">Action</th>
              </tr>
            </tfoot>     
          
          
    </table>
    <div align="center">
      <form method="POST" action="{{ route('store-accept-one-approval') }}" onsubmit="return showDetails() && confirm('Please check properly before you approve one beneficiary. If you approve one time then you can not changed?')">
        {{csrf_field()}}
        
        <input type="hidden" name="check_id" id="check_id" class="form-control">
        <input type="hidden" name="ben_id" id="ben_id" value="<?php print $ben_id_str; ?>" class="form-control">
        <input type="hidden" name="form_pay_count" id="form_pay_count" value="<?php print $pay_count; ?>" class="form-control">
        <input type="hidden" name="scheme_id" id="scheme_id" value="{{ $scheme_id }}">

        <input type="submit" name="ben_sub" value="Approve" id="ben_sub" class="btn btn-success btn-lg" disabled style="width: 200px;">
      </form>
    </div>
    @php } @endphp
  <!-- </div> -->

<!-- /.row -->

  </section>
</div>
  @include('layouts.footer')

</div>
<!-- /.content -->



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
      dom: 'Bfrtip',
      buttons: [
      'pdf','excel','csv','print','copy'
      ]
    });
  });
  
  //submit button disabled and enabled
  function myFunction(check, value){
    //alert(value);
    if (check == true){
      document.getElementById('check_id').value = value;
      document.getElementById("ben_sub").disabled=false;
    }
    else{
      document.getElementById('check_id').value = '';
      document.getElementById("ben_sub").disabled=true;
    }  
  }

  //select only one checkbox
  $(document).ready(function() {
  $('.ben_checkbox').each(function() {
    $(this).addClass('unselected');
  });
  $('.ben_checkbox').on('click', function() {
    $(this).toggleClass('unselected');
    $(this).toggleClass('selected');
    $('.ben_checkbox').not(this).prop('checked', false);
    $('.ben_checkbox').not(this).removeClass('selected');
    $('.ben_checkbox').not(this).addClass('unselected');
  });
});


  function showDetails(){
  	var i = 1;
  	var total_str = document.getElementById('check_id').value;
  	//Splitting it with : as the separator
	var myarr = total_str.split("-");
	var ben_id = myarr[0];
	var pay_count_check = myarr[1];
	var pay_yymm_check = myarr[2];
	var ben_name = myarr[3];
	var str_year = pay_yymm_check.substring(0, 2);
	var str_month = pay_yymm_check.substring(2, 4);
	var final_dt = '20'+str_year+'-'+str_month; 
	var total_count = Number(document.getElementById('form_pay_count').value) - Number(pay_count_check) + Number(i);
	//alert(total_count);
	//Add months to the date
	var dt = new Date(final_dt);
    dt.setMonth( dt.getMonth() + total_count );
    const ye = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(dt);
	const mo = new Intl.DateTimeFormat('en', { month: 'long' }).format(dt);
    var date = `${mo}-${ye}`;
    var fnl_str_alert = 'The beneficiary '+ben_name+'('+ben_id+') will get the next payment at '+date;
    alert(fnl_str_alert);

	
  	//alert(ben_id+' '+total_count+' '+pay_yymm_check+' '+ben_name);
  	return true;
  }
</script>

</body>
</html>