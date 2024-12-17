@extends('layouts.app-template-datatable_new')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Repeat Lot
      </h1>
      <ol class="breadcrumb">
        <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="box box-default">
        <div class="box-header with-border">
          <div class="box-title">
            <button type="button" class="btn btn-success btn-sm" onclick="window.location.href='{{route('generic-lot')}}'"><i class="fa fa-arrow-left"></i> Back</button>
            <font size="4">Making Lot For : <b>{{$lot_month}} [{{$lot_year}}], {{$scheme_name}}</b></font>
          </div>
        </div>
        <div class="box-body">
          <div class="row" style="margin-bottom: 10px;">
            <div class="col-md-12">
              <font size="3">Your Selected Total Beneficiary is: <b><span id="add_ben_count"></span></b></font>
              <div style="float: right; padding: 3px; font-weight: bold; background-color: #ffff99; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
                Note: <br>(1) You can select maximum 10,000 Beneficiary.<br>(2) You can check muliple checkbox at a time.
              </div>
              <br>
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
                
                <input type="submit" name="btn_append_lot" value="Create new Lot" id="btn_append_lot" class="btn btn-success btn-lg col-md-3">
            
              </form>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading">
              <font size="3">Showing Lots of <b>{{$month}} [{{$get_parent_financial_year}}]</b> paid through {{$pmt_mode}} payment mode</font>
            </div>
            <div class="panel-body" style="padding: 5px;">
              <div class="table-responsive">
                <table id="example" class="display" cellspacing="0" width="100%"> 
                  <thead>
                    <tr role="row" class="sorting_asc" style="font-size: 12px;">
                      <th width="8%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Sl No</th>
                      <th  width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Lot No</th>
                      <th width="15%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of Beneficiary</th>
                      <th width="19%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of IFMS Wrong Data</th>
                      <th width="16%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of RBI Failed</th>
                      <th width="17%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No. of RBI Success</th>
                      <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th>      
                    </tr>
                  </thead>
                  <tbody>
                    @php $i=1; @endphp
                      @foreach($reports as $report)
                      <tr> 
                        <td>@php print $i++; @endphp</td>
                        <td>{{ $report->lot_no }}</td>
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
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
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

    $('#example').DataTable( {
      dom: 'Blfrtip',
      "paging": true,
      "pageLength":20,
      "lengthMenu": [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      buttons: [
       {
           extend: 'pdf',
           title: 'Report <?php echo date('d-m-Y'); ?>',
           text: '<i class="fa fa-file-pdf-o"></i> PDF',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5],

            }
       },
       {
           extend: 'excel',
           title: 'Report <?php echo date('d-m-Y'); ?>',
           text: '<i class="fa fa-file-excel-o"></i> Excel',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,4,5],

            }
       },
      //'pdf','excel','csv','print','copy'
      ]
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
            // alert('Please Select Beneficiary less than 10000');
            error_alert('Please Select Beneficiary less than 10000');
            return false;
        }
        if (total < 1 && total > 0) {
            // alert('Please Select Beneficiary greater than 5000');
            error_alert('Please Select Beneficiary greater than 5000');
            return false;
        }
        if (total < 0) {
            // alert('Please go to the "Append Lot" link in the sidebar, and do it again!!');
            error_alert('Please go to the "Append Lot" link in the sidebar, and do it again!!');
            return false;
        }
        if (total == '' || total == 0) {
            // alert('Please select the checkbox!!');
            error_alert('Please select the checkbox!!');
            return false;
        }
        return true;
    }

    function error_alert(msg){
      $.alert({
        title: 'Error!!',
        type: 'red',
        icon: 'fa fa-warning',
        content: '<b>'+msg+'</b>',
      });
    }
</script>