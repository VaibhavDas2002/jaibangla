<style>
  .tbl {
    border: 1px solid #000;
  }
  .tbl1 {
    border: 1px solid #000;
  }
  .tbl tr td {
    width: 25%;
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
      <div id="preloader1" align="center" style="display: none;">
        <img src="../images/ZKZg.gif" width="100px">
      </div>
    <section class="content">
      <div class="box box-default">
        <div class="box-header">
          <div class="row">
            <div class="col-sm-8">
              <h3 class="box-title">Beneficiary Payment Status</h3>
            </div> 
          </div>
        </div>
        <div class="box-body">
          <div id="details_div">
            <div class="row">
              <div class="col-md-10 col-md-offset-1">
                <!-- <div align="center"><b>Beneficiary Personal Details</b></div> -->
                <table class="tbl" style="font-size: 14px; margin-top: 10px; width: 100%; background-color: #fff;">
                  <tr align="center" style="border: 1px solid #000;">
                      <td colspan="4" class="text-primary"><font size="3"><b>Beneficiary Personal Details (Pension Id: {{$result->id}})</b></font></td>
                  </tr>
                  <tr>
                    <td><b>Name </b></td>
                    <td>{{ $result->ben_fname }} {{ $result->ben_mname }} {{ $result->ben_lname }}</td>
                    <th>Village/City </th>
                    <td>{{ $result->village_town_city }}</td>
                  </tr>
                  <tr>
                    <td><b>Father's Name </b></td>
                    <td>{{ $result->father_fname }} {{ $result->father_mname }} {{ $result->father_lname }}</td>
                    <th>GP/Ward</th>
                    <td>{{ $result->gp_ward_name }}</td>
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
                    <td><b>P.S </b></td>
                    <td>{{ $result->police_station }}</td>
                  </tr>
                  @endif
                  <tr>
                    <td><b>No. of Payment </b></td>
                    <td>{{ $result->payment_count }}</td>
                    <td><b>P.O </b></td>
                    <td>{{ $result->post_office }}</td>
                  </tr>
                  <tr>
                    <td><b>Application Date </b></td>
                    <td><?php print date("d-m-Y", strtotime($result->created_at)); ?></td>
                    <td><b>PIN </b></td>
                    <td>{{ $result->pincode }}</td>
                  </tr>
		  <tr>
                    <td><b>Scheme </b></td>
                    <td>
                      @php $sc_id = $result->scheme_id;
                        $sobj = DB::table('m_scheme')->where('id',$sc_id)->first();
                        print $sobj->scheme_name;
                      @endphp
                    </td>
                    <td></td>
                    <td></td>
                  </tr>
                </table>
              </div>
              <!-- Show Error Div -->
              <div id="error_initial_div">
                  <span class="js_show_error" style="float: right; font-size: 12px; color: red; font-weight: bold;" class="text-danger"></span>
              </div>
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

            <!-- <div align="right"><span class="js_show_error" style="font-size: 12px; color: red; border: 1px solid red;" class="text-danger"></span></div> -->

            <!-- Active Veneficiary Payment Status -->
            @if(isset($ben_status))
            <div class="">
              <!-- <div align="center"><b>Application Process Status</b></div> -->
              <table class="tbl1" style="font-size: 14px; margin-top: 10px; width: 100%; background-color: #fff;" align="">
                <tr align="center">
                  <td colspan="10" class="text-success"><font size="3"><b>Active Beneficiary (Pension Id: {{$result->id}})(Current Payments)</b></font></td>
                </tr>
                <tr style="border: 1px solid #000;">
                  <th width="5%">#No</th>
                  <th width="10%">Pension Id</th>
                  <th width="10%">Lot No</th>
                  <th width="10%">Month</th>
                  <th width="10%">Year</th>
                  <th width="10%">IFSC Code</th>
                  <th width="10%">Account No</th>
                   <th width="10%">Payment Mode</th>
                  <th width="15%">Process Status</th>
                  <th width="20%">Payment Status</th>
                </tr>
                @php $i = 1; @endphp
                @if(count($ben_status)>0)
                @foreach($ben_status as $status)
                  <tr>
                    <td width="5%">@php print $i++; @endphp</td>
                    <td width="10%">{{ $status->pension_id }}</td>
                    <td width="10%">{{ $status->lot_no }}</td>
                    <td width="10%">{{ $status->lot_month }}</td>
                    <td width="10%">{{ $status->lot_year }}</td>
                    <td width="10%">{{ $status->ifsc_code }}</td>
                    <td width="10%">{{ $status->account_no }}</td>
                     <td width="10%">{{ $status->payment_mode }}</td>
                    <td width="15%">{{ $status->process_status }}</td>
                    <td width="20%">
                      <?php if ($status->payment_status == 'Payment error') { ?>
                        <span class="text-danger"><b>{{ $status->payment_status }}</b></span>
                        <button class="btn btn-xs btn-danger" class="js-status-error" value="{{$status->lot_no}}-{{$status->scheme_id}}-{{$status->pension_id}}" onclick="getStatus(this.value,1);">View Error</button>
                      <?php } elseif ($status->payment_status == 'Payment under process') { ?>
                        <span class="text-warning"><b>{{ $status->payment_status }}</b></span>
                      <?php } else { ?>
                        <span class="text-success"><b>{{ $status->payment_status }}</b></span>
                        <button class="btn btn-xs btn-success" class="js-status-error" value="{{$status->lot_no}}-{{$status->scheme_id}}-{{$status->pension_id}}" onclick="getStatus(this.value,1);">View UTR</button>
                      <?php } ?>
                    </td>
                  </tr>
                  @endforeach
                  @else
                   <tr><td colspan="10" align="center">No Payment Found</td></tr>
                  @endif
                </table>
            </div>
            @endif
          @if(isset($ben_status_old))
            <div class="">
              <!-- <div align="center"><b>Application Process Status</b></div> -->
              <table class="tbl1" style="font-size: 14px; margin-top: 10px; width: 100%; background-color: #fff;" align="">
                <tr align="center">
                  <td colspan="10" class="text-success"><font size="3"><b>Active Beneficiary (Pension Id: {{$result->id}})(Old Payments)</b></font></td>
                </tr>
                <tr style="border: 1px solid #000;">
                  <th width="5%">#No</th>
                  <th width="10%">Pension Id</th>
                  <th width="10%">Lot No</th>
                  <th width="10%">Month</th>
                  <th width="10%">Year</th>
                  <th width="10%">IFSC Code</th>
                  <th width="10%">Account No</th>
                  <th width="10%">Payment Mode</th>
                  <th width="15%">Process Status</th>
                  <th width="20%">Payment Status</th>
                </tr>
                @php $i = 1; @endphp
                 @if(count($ben_status_old)>0)
                @foreach($ben_status_old as $status_old)
                  <tr>
                    <td width="5%">@php print $i++; @endphp</td>
                    <td width="10%">{{ $status_old->pension_id }}</td>
                    <td width="10%">{{ $status_old->lot_no }}</td>
                    <td width="10%">{{ $status_old->lot_month }}</td>
                    <td width="10%">{{ $status_old->lot_year }}</td>
                    <td width="10%">{{ $status_old->ifsc_code }}</td>
                    <td width="10%">{{ $status_old->account_no }}</td>
                    <td width="10%">{{ $status_old->payment_mode }}</td>
                    <td width="15%">{{ $status_old->process_status }}</td>
                    <td width="20%">
                      <?php if ($status_old->payment_status == 'Payment error') { ?>
                        <span class="text-danger"><b>{{ $status_old->payment_status }}</b></span>
                        <button class="btn btn-xs btn-danger" class="js-status-error" value="{{$status_old->lot_no}}-{{$status_old->scheme_id}}-{{$status_old->pension_id}}" onclick="getStatus(this.value,2);">View Error</button>
                      <?php } elseif ($status_old->payment_status == 'Payment under process') { ?>
                        <span class="text-warning"><b>{{ $status_old->payment_status }}</b></span>
                      <?php } else { ?>
                        <span class="text-success"><b>{{ $status_old->payment_status }}</b></span>
                        <button class="btn btn-xs btn-success" class="js-status-error" value="{{$status_old->lot_no}}-{{$status_old->scheme_id}}-{{$status_old->pension_id}}" onclick="getStatus(this.value,2);">View UTR</button>
                      <?php } ?>
                    </td>
                  </tr>
                  @endforeach
                  @else
                  <tr><td colspan="10" align="center">No Payment Found</td></tr>
                  @endif
                </table>
            </div>
            @endif
            <!-- De-active Beneficiary Payment Status list-->
            @if(isset($ben_status1))
            <div class="">
              <!-- <div align="center"><b>Application Process Status</b></div> -->
              <table class="tbl1" style="font-size: 14px; margin-top: 10px; width: 100%; background-color: #fff;" align="">
                <tr align="center">
                  <td colspan="7" class="text-danger"><font size="3"><b>Rejected Beneficiary (Pension Id: <?php if(isset($duplicate_ids)) { print $duplicate_ids; } ?>)</b></font></td>
                  <td colspan="2" class="text-warning"><font size="3"><b>Adjustment Date: <?php print date("d-m-Y", strtotime($adjust->created_at)); ?></b></font></td>
                </tr>
                <tr style="border: 1px solid #000;">
                  <th width="5%">#No</th>
                  <th width="10%">Pension Id</th>
                  <th width="10%">Lot No</th>
                  <th width="10%">Month</th>
                  <th width="10%">Year</th>
                  <th width="10%">IFSC Code</th>
                  <th width="10%">Account No</th>
                  <th width="15%">Process Status</th>
                  <th width="20%">Payment Status</th>
                </tr>
                @php $i = 1; @endphp
                @foreach($ben_status1 as $status1)
                <tr>
                  <td width="5%">@php print $i++; @endphp</td>
                  <td width="10%">{{ $status1->pension_id }}</td>
                  <td width="10%">{{ $status->lot_no }}</td>
                  <td width="10%">{{ $status1->lot_month }}</td>
                  <td width="10%">{{ $status1->lot_year }}</td>
                  <td width="10%">{{ $status1->ifsc_code }}</td>
                  <td width="10%">{{ $status1->account_no }}</td>
                  <td width="15%">{{ $status1->process_status }}</td>
                  <td width="20%">
                    <?php if ($status1->payment_status == 'Payment error') { ?>
                      <span class="text-danger"><b>{{ $status1->payment_status }}</b></span>
                      <button class="btn btn-xs btn-danger" class="js-status-error" value="{{$status1->lot_no}}-{{$status1->scheme_id}}-{{$status1->pension_id}}" onclick="getStatus(this.value,1);">View Error</button>
                    <?php } elseif ($status->payment_status == 'Payment under process') { ?>
                      <span class="text-warning"><b>{{ $status->payment_status }}</b></span>
                    <?php } else { ?>
                      <span class="text-success"><b>{{ $status1->payment_status }}</b></span>
                      <button class="btn btn-xs btn-success" class="js-status-error" value="{{$status1->lot_no}}-{{$status1->scheme_id}}-{{$status1->pension_id}}" onclick="getStatus(this.value,1);">View UTR</button>
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
              <h4 class="text-danger"><b>This Beneficiary did not get any payment</b></h4>
            </div>
            @endif
            <!-- /.content -->
          </div>
          <div align="center">
            <button class="btn btn-primary btn-lg" title="Print Status" onclick="printDiv('details_div')"><i class="fa fa-print"></i> Print</button>
          </div>
        </div>
      </div>
	  
  <!-- Modal -->
  <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Payment Status</h4>
        </div>
        <div class="modal-body">
          <!--<h5 align="center"><b>Parent Lot No and Remarks</b></h5>-->
          <div id="pmt_status"></div>
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

	  
    </section>
    <!-- <div align="center"><span class="js_show_error" style="font-weight: bold; font-size: 10px; color: red;" class="text-danger"></span></div> -->
    
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

  function getStatus(value,payment_type){
    document.getElementById('preloader1').style.display = '';
    getStatus1(value,payment_type);
  }
    function getStatus1(value,payment_type){
      var myarr = value.split("-");
      var lot_no = myarr[0];
      var scheme_id = myarr[1];
      var pension_id = myarr[2];
      paymentStatusErrorFun(lot_no, scheme_id, pension_id,payment_type);
    }

    function paymentStatusErrorFun(lot_no, scheme_id, pension_id,payment_type) {
      loadItemsPaymentStatusError(lot_no, scheme_id, pension_id,payment_type, '../api/paymentStatusError/', '.js_show_error');
    }

    function loadItemsPaymentStatusError(lot_no, scheme_id, pension_id, payment_type,path, selectInputClass) {
      $.ajax({
      type: 'GET',
      url: path +lot_no+'/'+scheme_id+'/'+pension_id+'/'+payment_type,

      success: function (datas) {
        if (!datas || datas.length === 0) {
          //alert("sucess with 0 data");
           return;
        }
       //alert(datas);
        $('#pmt_status').text('');
       for (var  i = 0; i < datas.length; i++) {
          $('#pmt_status').append('<p>'+datas[i].status_code+'</p>');
        // console.log(datas[i].status_code);
         //var error = datas[i].status_code;
          //$(selectInputClass).text(datas[i].status_code);
        }
        document.getElementById('preloader1').style.display = 'none';
        $('#modal-default').modal('show');
      },
      error: function (ex) {
         alert('Actual error data not found!!');
      }
      });
    }
/*
	
  function loadItemsPaymentStatusError(lot_no, scheme_id, pension_id, path){
    $.ajax({
      type: 'GET',
      url: path +lot_no+'/'+scheme_id+'/'+pension_id,
      
      success: function (datas) {
        if (!datas || datas.data.length === 0) {
          //alert("sucess with 0 data");
          return;
        }
        $('#pmt_status').text('');
        var rem = datas.data[0].status_code;
        var arr = rem.split('.');
        for (var i = 0; i < arr.length ; i++) {
          $('#pmt_status').append('<p>'+arr[i]+'</p>');
        }
        document.getElementById('preloader1').style.display = 'none';
        $('#modal-default').modal('show');
        //$('#pmt_status').text(datas.data[0].remark);
      },
      error: function (ex) {
         alert('Actual error data not found!!');
      }
    });
  }*/
  </script>
@endsection