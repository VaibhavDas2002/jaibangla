<style>
  .tbl {
    border: 1px solid #000;
  }
  .tbl1 {
    border: 1px solid #000;
  }
  .tbl tr td {
    width: 50%;
    padding: 1px;
  }
  .tbl1 tr td {
    /*width: 20%;*/
    padding: 5px;
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
        Status of the Beneficiary
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Beneficiary Payment Status</a></li>
        <!-- <li class="active">Duplicate Approve</li> -->
      </ol>
    </section>
    <div align="center"><span class="js_show_error" style="font-weight: bold; font-size: 16px; padding: 5px; color: red;" class="text-danger"></span></div>
    <div id="details_div">
      <div class="container">
        <!-- <div align="center"><b>Beneficiary Personal Details</b></div> -->
        <table class="tbl" style="font-size: 14px; margin-top: 10px; width: 80%; background-color: #fff;" align="center">
          <tr align="center" style="border: 1px solid #000;">
              <td colspan="2"><b>Beneficiary Personal Details (Pension Id: {{$result->id}})</b></td>
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
          <tr>
            <td><b>Payment Count </b></td>
            <td>{{ $result->payment_count }}</td>
          </tr>
        </table>
      </div>
      <div class="container">
        <!-- <div align="center"><b>Application Process Status</b></div> -->
        <table class="tbl1" style="font-size: 14px; margin-top: 10px; width: 90%; background-color: #fff;" align="center">
          <tr align="center">
            <td colspan="8"><b>Application Payment Status (Pension Id: {{$result->id}}<?php if(isset($duplicate_ids)) { print ','.$duplicate_ids; } ?>)</b></td>
          </tr>
          <tr style="border: 1px solid #000;">
            <th width="5%">#No</th>
            <th width="10%">Pension Id</th>
            <th width="10%">Lot Month</th>
            <th width="10%">Lot Year</th>
            <th width="10%">IFSC Code</th>
            <th width="15%">Account No</th>
            <th width="20%">Process Status</th>
            <th width="20%">Payment Status</th>
          </tr>
          @php $i = 1; @endphp
          @foreach($ben_status as $status)
            <tr>
              <td width="5%">@php print $i++; @endphp</td>
              <td width="10%">{{ $status->pension_id }}</td>
              <td width="10%">{{ $status->lot_month }}</td>
              <td width="10%">{{ $status->lot_year }}</td>
              <td width="10%">{{ $status->ifsc_code }}</td>
              <td width="15%">{{ $status->account_no }}</td>
              <td width="20%">{{ $status->process_status }}</td>
              <td width="25%">
                <?php if ($status->payment_status == 'Payment error') { ?>
                  <span class="text-danger"><b>{{ $status->payment_status }}</b></span>
                  <button class="btn btn-xs btn-danger" class="js-status-error" value="{{$status->lot_no}}-{{$status->pension_id}}" onclick="getStatus(this.value);">View Error</button>
                <?php } else { ?>
                  <span class="text-success"><b>{{ $status->payment_status }}</b></span>
                <?php } ?>
              </td>
            </tr>
            @endforeach
            @php if(isset($ben_status1)) { @endphp
            @foreach($ben_status1 as $status1)
            <tr>
              <td width="5%">@php print $i++; @endphp</td>
              <td width="15%">{{ $status1->pension_id }}</td>
              <td width="15%">{{ $status1->lot_month }}</td>
              <td width="15%">{{ $status1->lot_year }}</td>
              <td width="25%">{{ $status1->process_status }}</td>
              <td width="25%">
                <?php if ($status1->payment_status == 'Payment error') { ?>
                  <span class="text-danger"><b>{{ $status1->payment_status }}</b></span>
                  <button class="btn btn-xs btn-danger" class="js-status-error" value="{{$status1->lot_no}}-{{$status1->pension_id}}" onclick="getStatus(this.value);">View Error</button>
                <?php } else { ?>
                  <span class="text-success"><b>{{ $status1->payment_status }}</b></span>
                <?php } ?>
              </td>
            </tr>
          @endforeach
          @php } @endphp
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