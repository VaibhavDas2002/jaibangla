<style>
  .tbl {
    border: 1px solid #000;
  }
  .tbl tr td {
    width: 50%;
    padding: 2px 5px 2px 5px;
  }
  #details_div {
    /*border: 1px solid #000;*/
    margin: 10px;
    border-radius: 10px;
    padding: 0px 10px 10px 10px;
  }
</style>
@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Application Status of the Beneficiary
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Application Status of the Beneficiary</a></li>
        <!-- <li class="active">Duplicate Approve</li> -->
      </ol>
    </section>
    <div id="details_div">
      <div class="container">
        <div align="center"><h4 class="text-warning"><b>{{$msg}}</b></h4></div>
        <table class="tbl" style="font-size: 14px; margin-top: 10px; width: 80%; background-color: #fff;" align="center">
          <tr align="center" style="border: 1px solid #000;">
              <td colspan="2"><font size="3"><b>Beneficiary Personal Details (Pension Id: {{$result->id}})</b></font></td>
            </tr>
          <tr>
            <td><b>Name </b></td>
            <td>{{ $result->ben_fname }} {{ $result->ben_mname }} {{ $result->ben_lname }}</td>
          </tr>
          <tr>
            <td><b>Father's Name </b></td>
            <td>{{ $result->father_fname }} {{ $result->father_mname }} {{ $result->father_lname }}</td>
          </tr>
          <tr>
            <td><b>Ration Card </b></td>
            <td>{{ $result->ration_card_cat}} - {{ $result->ration_card_no }}</td>
          </tr>
          <tr>
            <td><b>Voter Card </b></td>
            <td>{{ $result->epic_voter_id }}</td>
          </tr>
          <tr>
            <td><b>Block/Municipality </b></td>
            <td>{{ $result->block_ulb_name }}</td>
          </tr>
          <tr>
            <td><b>Village/City </b></td>
            <td>{{ $result->village_town_city }}</td>
          </tr>
          <tr>
            <td><b>GP/Ward </b></td>
            <td>{{ $result->gp_ward_name }}</td>
          </tr>
          <tr>
            <td><b>P.S </b></td>
            <td>{{ $result->police_station }}</td>
          </tr>
          <tr>
            <td><b>P.O </b></td>
            <td>{{ $result->post_office }}</td>
          </tr>
          <tr>
            <td><b>PIN </b></td>
            <td>{{ $result->pincode }}</td>
          </tr>
          <tr>
            <td><b>Bank Name </b></td>
            <td>{{ $result->bank_name }}</td>
          </tr>
           <tr>
            <td><b>Branch Name </b></td>
            <td>{{ $result->branch_name }}</td>
          </tr>
          <tr>
            <td><b>IFSC </b></td>
            <td>{{ $result->bank_ifsc }}</td>
          </tr>
          <tr>
            <td><b>Account No </b></td>
            <td>{{ $result->bank_code }}</td>
          </tr>
          @if($result->last_paid_yymm != null)
          <tr>
            <td><b>Last Payment (Month-Year) </b></td>
            <td><!-- {{ $result->last_paid_yymm }} -->
              <?php 
                $month_arr=[];
                $month_arr = ['01'=> 'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'];
                $date = $result->last_paid_yymm;
                $arr = str_split($date, 2);
                $year = $arr[0];
                $month = $arr[1];
                foreach($month_arr as $key=>$value){
                  if($month == $key){
                    $month_final = $value;
                  }
                }
                print $month_final.'-'.$year;
              ?>
            </td>
          </tr>
          @endif
          <tr>
            <td><b>No. of Payment </b></td>
            <td>{{ $result->payment_count }}</td>
          </tr>
          <tr>
            <td><b>Application Date </b></td>
            <td><?php print date("d-m-Y", strtotime($result->created_at)); ?></td>
          </tr>
          
        </table>
      </div>
      <!-- /.content -->
    </div>
    <div align="center">
      <button class="btn btn-primary btn-lg" title="Print Status" onclick="printDiv('details_div')">Print</button> 
    </div>
  </div>
  
  <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script>
    function printDiv(divName) {
      var printContents = document.getElementById(divName).innerHTML;
      var originalContents = document.body.innerHTML;
      document.body.innerHTML = printContents;
      window.print();
      document.body.innerHTML = originalContents;
    }
  </script>
@endsection