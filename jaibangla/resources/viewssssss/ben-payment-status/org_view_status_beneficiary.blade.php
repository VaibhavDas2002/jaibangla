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
        Payment Status of the Beneficiary
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
              <td colspan="2" class="text-primary"><font size="3"><b>Beneficiary Personal Details (Pension Id: {{$result->id}})</b></font></td>
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

      <!-- Rejected Beneficiary Status(De-active Beneficiary) (next_level_role_id = -2)-->
      @if(isset($reject_id))
      <div align="center">
        <h4 class="text-danger"><b>This Beneficiary already rejected. All the payments done from <?php print date("d-m-Y", strtotime($approved->created_at)); ?> through this beneficiary id:
          <table>
            <tr>
            @foreach($reject_id as $val)
            <!-- <a href="{{ url('view-status/'.$val->original_approve_application_id) }}">{{$val->original_approve_application_id}}</a> -->
            <td>
              <form method="POST" action="{{ url('view-status/'.$val->original_approve_application_id) }}" onsubmit="return confirm('Do you want to see payment status?');" name="myForm">
                {{ csrf_field() }}
                <button class="btn btn-link" title="Check benficiary id: {{$val->original_approve_application_id}} payment status">
                  <h4><b>{{$val->original_approve_application_id}}</b></h4>
                </button>
              </form>
            </td>
            @endforeach
            </tr>
          </table>
        </b></h4>
      </div>
      @endif

      <!-- Active Veneficiary Payment Status -->
      @if(isset($ben_status))
      <div class="container">
        <!-- <div align="center"><b>Application Process Status</b></div> -->
        <table class="tbl1" style="font-size: 14px; margin-top: 10px; width: 90%; background-color: #fff;" align="center">
          <tr align="center">
            <td colspan="8" class="text-success"><font size="3"><b>Active Beneficiary (Pension Id: {{$result->id}})</b></font></td>
          </tr>
          <tr style="border: 1px solid #000;">
            <th width="5%">#No</th>
            <th width="10%">Pension Id</th>
            <th width="10%">Month</th>
            <th width="10%">Year</th>
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
          </table>
      </div>
      @endif

      <!-- De-active Beneficiary Payment Status list-->
      @if(isset($ben_status1))
      <div class="container">
        <!-- <div align="center"><b>Application Process Status</b></div> -->
        <table class="tbl1" style="font-size: 14px; margin-top: 10px; width: 90%; background-color: #fff;" align="center">
          <tr align="center">
            <td colspan="6" class="text-danger"><font size="3"><b>Rejected Beneficiary (Pension Id: <?php if(isset($duplicate_ids)) { print $duplicate_ids; } ?>)</b></font></td>
            <td colspan="2" class="text-warning"><font size="3"><b>Adjustment Date: <?php print date("d-m-Y", strtotime($adjust->created_at)); ?></b></font></td>
          </tr>
          <tr style="border: 1px solid #000;">
            <th width="5%">#No</th>
            <th width="10%">Pension Id</th>
            <th width="10%">Month</th>
            <th width="10%">Year</th>
            <th width="10%">IFSC Code</th>
            <th width="15%">Account No</th>
            <th width="20%">Process Status</th>
            <th width="20%">Payment Status</th>
          </tr>
          @php $i = 1; @endphp
          @foreach($ben_status1 as $status1)
          <tr>
            <td width="5%">@php print $i++; @endphp</td>
            <td width="10%">{{ $status1->pension_id }}</td>
            <td width="10%">{{ $status1->lot_month }}</td>
            <td width="10%">{{ $status1->lot_year }}</td>
            <td width="10%">{{ $status1->ifsc_code }}</td>
            <td width="15%">{{ $status1->account_no }}</td>
            <td width="20%">{{ $status1->process_status }}</td>
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
        </table>
      </div>
      @endif

      <!-- New Beneficiary Registered -->
      @if($result->payment_count == 0)
      <div align="center">
        <h4 class="text-danger"><b>This Beneficiary not get any payment</b></h4>
      </div>
      @endif
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