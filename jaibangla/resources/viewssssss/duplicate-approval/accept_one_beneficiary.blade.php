<style type="text/css">
  .has-error
  {
    border-color:#cc0000;
    background-color:#ffff99;
  }
  .preloader1{
    position: fixed;
    top:40%;
    left: 52%;
    z-index: 999;
  }
  .preloader1 {
    background: transparent !important;
  }
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
  <div class="content-wrapper">
    <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" width="150px" id="loader_img"></div>
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Reject Duplicate Approved Beneficiary
      </h1>
      <ol class="breadcrumb">
        <li class="active"><i class="fa fa-clock-o"></i> Date :> <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span></li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="box box-default">
        <div class="box-header with-border">
          <div class="box-title">
            <a href="{{URL('duplicate-approval')}}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i> Back</a> 
            Fiter Using Card No: {{ $card_no }}, 
            <font size="3">Scheme :
              @php
                $sObj = DB::table('public.m_scheme')->where('id',$scheme_id)->first();
                print $sObj->scheme_name;
              @endphp
            </font>
          </div>
        </div>
        <div class="box-body">
          @php if($ben_reports =='') { @endphp
          <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert" ><a href="{{URL('duplicate-approval')}}">Back</a></button> 
            <strong>One of the beneficiary ID is under transaction process. Rejection will be available once transaction is completed. </strong>
          </div>
          @php } else {  @endphp
          @php $pay_count = 0; @endphp
          @foreach($ben_reports as $report)
            @php 
              $ben_id_arr[] = $report->id; 
              $ben_id_str = implode(',',$ben_id_arr);
              $pay_count = $pay_count + $report->payment_count;
            @endphp
          @endforeach
          <div class="row">
            <div class="col-md-4">
              <form method="POST" action="{{ route('store-accept-one-approval') }}" id="approved_form">
                {{csrf_field()}}
                
                <input type="hidden" name="check_id" id="check_id" class="form-control">
                <input type="hidden" name="ben_id" id="ben_id" value="<?php print $ben_id_str; ?>" class="form-control">
                <input type="hidden" name="form_pay_count" id="form_pay_count" value="<?php print $pay_count; ?>" class="form-control">
                <input type="hidden" name="scheme_id" id="scheme_id" value="{{ $scheme_id }}">

                <input type="button" name="ben_sub" value="Approve" id="ben_sub" class="btn btn-success btn-lg" disabled style="width: 200px;" onclick="showDetails()">
              </form>
            </div>
            <div class="col-md-8">
              <div style="float: right; padding: 3px; font-weight: bold; background-color: #ffff99; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
                Note: (1) Check one beneficiary which one do you want to approve?<br/><span style="padding-left: 36px;">(2) Please look all the beneficiary carefully actualy duplicate or not before approve one beneficiary.</span>
              </div>
            </div>
          </div>
          <p style="border: 1px solid whitesmoke;"></p>
            <table id="example" class="display compact" cellspacing="0" width="100%">
              <thead style="font-size: 12px;">
                <tr role="row">
                  <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Pension Id</th>
                  <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Name</th>
                  <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Father's Name</th>
                  <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Voter ID Card</th>
                  <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Ration Card</th>
                  <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Block/ Municipality</th>
                  <th width="20%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank Details</th>
                  <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Pay Count</th>
                  <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Last Pay YY-MM</th>
                  <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th>

                </tr>
              </thead>
              <tbody style="font-size: 14px;">
                @php $i=1; @endphp    
                @foreach($ben_reports as $report)
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
          </table>
        @php } @endphp
      </div>
    </div>
  </section>
</div>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
  $(document).ready(function() {
    // Live Clock
    var interval = setInterval(function () {
    var momentNow = moment();
      $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
      $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);

    $('#loader_img').hide();
    $('#example').DataTable( {
      dom: 'Blfrtip',
      "paging": true,
      "pageLength":20,
      "lengthMenu": [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      "scrollX": true,
      buttons: [
       {
           extend: 'pdf',
           title: 'Report',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5,6,7,8],

            }
       },
       {
           extend: 'excel',
           title: 'Report',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                 columns: [0,1,2,3,4,5,6,7,8],
                stripHtml: false,
            }
       },
      //'pdf','excel','csv','print','copy'
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
    var fnl_str_alert = 'The beneficiary '+ben_name+'(ID- '+ben_id+') will get the next payment at '+date;
    //alert(fnl_str_alert);
    $.confirm({
      title: 'Alert!',
      type: 'success',
      icon: 'fa fa-check',
      content: '<strong>'+fnl_str_alert+'</strong>',
      buttons: {
        ok: function () {
          $.confirm({
            title: 'Confirm!',
            type: 'orange',
            icon: 'fa fa-warning',
            content: '<strong>Please check properly before you approve one beneficiary. If you approve one time then you can not changed?</strong>',
            buttons: {
              confirm: function () {
                $('#approved_form').submit();
              },
              cancel: function () {
              }
            }
          });
        },
        cancel: function () {
        }
      }
    });

	
  	//alert(ben_id+' '+total_count+' '+pay_yymm_check+' '+ben_name);
  	return true;
  }
</script>

