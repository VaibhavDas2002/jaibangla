<section class="content">
  <div>

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-block">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>{{ $message }}</strong>
    </div>
    @elseif ($message = Session::get('danger'))
    <div class="alert alert-danger alert-block">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>{{ $message }}</strong>
    </div>
    @endif
  </div>
  <!-- <h3>Report Lot Master</h3> -->

  <input style="display: none" type="hidden" value="{{$status}}" id="record_status">
  <table id="example" class="display" cellspacing="0" width="100%">

    <thead>
      <tr role="row" class="sorting_asc" style="font-size: 12px;">
        <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Name: activate to sort column descending">Serial No</th>
        <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Name: activate to sort column descending">Lot No</th>
        <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Name: activate to sort column descending">Year Month</th>
        <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Name: activate to sort column descending">Status</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending">Total Beneficiary in the lot-List</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending">No. of Beneficiary in the lot</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending">Failed List</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending">Failed</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending">Success List</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending">Success</th>
        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending">Billed Amount</th>
        <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
          aria-label="Email: activate to sort column ascending" style="text-align: center">Action</th>
      </tr>
    </thead>
    <tbody>
      @php $i=1; @endphp
      @foreach($reports as $report)
      <tr>
        <td>@php print $i++; @endphp</td>
        <td>{{ $report->lot_no }}</td>
        <td>{{$report->scheme_name}}</br>{{ $report->lot_year }} {{ $report->lot_month }}</td>
        <td>
          @php
          if($report->lot_status==0)
          {print 'Ready for push to Bank.';}
          elseif($report->lot_status==1 )
          {print 'Lot signed.';}
          elseif($report->lot_status==2 )
          {print 'File in server. will be pushed in next cycle';}
          elseif($report->lot_status==3 )
          {print 'Pushed to SBI<br />Acknowledgement Received from SBI';}
          elseif($report->lot_status==4 )
          {print 'Pushed to SBI<br />Acknowledgement Received from SBI <br /> Payment Response received form SBI.';}
          elseif($report->lot_status==5 )
          {print 'Import SBI response complete';}
          elseif($report->lot_status==6 )
          {print 'All actions completed';}
          elseif($report->lot_status==10 )
          {print 'Lot Signing Failed. Please Re-sign the LOT';}
          elseif($report->lot_status==20 )
          {print 'Pushed to SBI Failed. Please Re-Push the LOT';}
          elseif($report->lot_status==30)
          {print 'Pushed to SBI</br> Acknowledgement receive failed.';}
          elseif($report->lot_status== 40 )
          {print 'Pushed to SBI<br />Acknowledgement Received from SBI<br /> Payment response receive failed.';}
          elseif($report->lot_status==50)
          {print 'Pushed to SBI<br />Acknowledgement Received from SBI <br /> Payment Response received form SBI.<br />
          Payment data not compiled. Please re-compile payment data.';}
          else
          {print 'Lot has been stopped.';}
          @endphp

        </td>

        <!-- <form class="excel_form" method="POST" action="{{ route('lot_payment_xls_generate') }}"> -->
        <td style="text-align: center">
          <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
            <input type="hidden" name="error_type" value="COUNT"> -->
          <button type="button" class="btn btn-xs btn-margin excel_btn"
            onmouseover="$(this).toggleClass('btn-primary');" onmouseout="$(this).toggleClass('btn-primary');"
            style="font-size: 16px;" title="Get Total Beneficiary List - {{ $report->credit_count }}"
            value="{{$report->lot_no}}_{{$report->scheme_id}}" data-toggle="tooltip"
            data-placement="bottom">Total</button>
        </td>
        <!-- </form> -->
        <td style="text-align: center">{{ $report->credit_count }}</td>

        @if($report->failed_count != '')
        <!-- <form class="excel_form" method="POST" action="{{ route('lot_payment_xls_generate') }}"> -->
        <td style="text-align: center">
          <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
            <input type="hidden" name="error_type" value="E2"> -->
          <button type="button" class="btn btn-xs btn-margin excel_btn_failed"
            onmouseover="$(this).toggleClass('btn-danger');" onmouseout="$(this).toggleClass('btn-danger');"
            style="font-size: 16px;"
            title="Get SBI Failed Beneficiary List - @php if($report->failed_count == '') {print '0';} else {print $report->failed_count;} @endphp"
            value="{{$report->lot_no}}_{{$report->scheme_id}}" data-toggle="tooltip" data-placement="bottom">
            @php if($report->failed_count == '') {print '0';} else {print "Failed";} @endphp
          </button>
        </td>
        <!-- </form> -->
        @else
        <td style="text-align: center">@php if($report->failed_count == '') {print '0';} else {print "Get Failed List";}
          @endphp</td>
        @endif
        <td style="text-align: center">@php if($report->failed_count == '') {print '0';} else {print
          $report->failed_count;} @endphp</td>

        @if($report->success_count != '')
        <!-- <form class="excel_form" method="POST" action="{{ route('lot_payment_xls_generate') }}"> -->
        <td style="text-align: center">
          <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
            <input type="hidden" name="error_type" value="S0"> -->
          <button type="button" class="btn btn-xs btn-margin excel_btn_success"
            onmouseover="$(this).toggleClass('btn-success');" onmouseout="$(this).toggleClass('btn-success');"
            style="font-size: 16px; "
            title="Get SBI Success Beneficiary List - @php if($report->success_count == '') {print '0';} else {print $report->success_count;} @endphp"
            value="{{$report->lot_no}}_{{$report->scheme_id}}" data-toggle="tooltip" data-placement="bottom">
            @php if($report->success_count == '') {print '0';} else {print "Success";} @endphp
          </button>
        </td>
        </form>
        @else
        <td style="text-align: center">@php if($report->success_count == '') {print '0';} else {print "Get Success
          List";} @endphp</td>
        @endif
        <td style="text-align: center">@php if($report->success_count == '') {print '0';} else {print
          $report->success_count;} @endphp</td>
        <td style="text-align: center">@php if($report->amount_debit == '') {print '0';} else {print
          $report->amount_debit/100;} @endphp</td>


        @if($report->lot_status==0 or $report->lot_status==10)
        <!-- <form method="POST" action="{{ route('push-to-sbi-single-lot') }}" class="submit-once"
          onSubmit="if(!confirm('Please click on OK if you are sure to export the Lot to SBI')){return false;}"> -->
        <td>
          <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
            <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
            <input type="hidden" name="debit_ref" value="{{$report->debit_reference}}"> -->
          <button type="button" id="pushtosbi_btn_{{$report->lot_no}}_{{$report->scheme_id}}"
            class="btn btn-info btn-margin pushtosbi_btn"
            value="{{$report->lot_no}}_{{$report->scheme_id}}_{{$report->debit_reference}}">
            Sign the Lot and Push to SBI
          </button>
        </td>
        <!-- </form> -->
        @elseif($report->lot_status==1 or $report->lot_status==20)
        <td>
          Waiting for to be pushed to SBI server in next cycle.
        </td>
        </form>
        @elseif($report->lot_status==2 or $report->lot_status==30)
        <td>
          Waiting for reciveing of Lot Acknowledgement from SBI.
        </td>
        </form>
        @elseif($report->lot_status==3 or $report->lot_status==40)
        <td>
          Waiting for reciveing of Payment response from SBI.
        </td>
        </form>
        @elseif($report->lot_status==4 )
        <td>
          Waiting for import of Payment response from SBI.
        </td>
        </form>
        @elseif($report->lot_status==5 or $report->lot_status==50)
        <!-- <form method="GET" action="{{ route('sbi_payment_status') }}" class="submit-once"
          onSubmit="if(!confirm('Please click on OK if you are sure to import the SBI Payment Response')){return false;}"> -->
        <td>
          <!-- <input type="hidden" name="lot_no" value="{{$report->lot_no}}">
            <input type="hidden" name="scheme_id" value="{{$report->scheme_id}}">
            <input type="hidden" name="debit_ref" value="{{$report->debit_reference}}">
            <input type="hidden" name="lot_month" value="{{$report->lot_month}}">
            <input type="hidden" name="lot_year" value="{{$report->lot_year}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
          <button type="submit" class="btn btn-warning btn-margin importsbi_btn"
            value="{{$report->lot_no}}_{{$report->scheme_id}}_{{$report->debit_reference}}_{{$report->lot_month}}_{{$report->lot_year}}">
            Import SBI Payment status
          </button>
        </td>
        <!-- </form> -->
        @elseif($report->lot_status ==6)
        <td style="text-align: center">
          <i class="glyphicon glyphicon-ok"></i>
        </td>
        @elseif($report->lot_status <0) <td style="text-align: center">
          <i class="glyphicon glyphicon-remove"></i>
          </td>

          @endif
      </tr>
      @endforeach
    </tbody>
    <!-- <tfoot> -->

    <!-- </tfoot> -->
  </table>

  </div>

  </div>

  </div>
</section>
<!-- /.content -->

<!-- Modal -->
<div class="modal fade" id="pushmodal_sbi" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h5 class="modal-title"><span id="modalTitle">Sign the Lot and Push To SBI</span></h5>
      </div>
      <div class="modal-body">
        <div id="load_div"><img src="../images/ZKZg.gif" width="60px"><span
            style="font-size: 18px; font-weight: bold; font-style: italic;"> Please wait....</span></div>
        <div class="row" style="display: none;" id="lot_det">
          <div class="col-md-12">
            <h4 class="text-primary">Scheme : <span id="s_name"></span><br> Lot No : <span id="l_no"></span><br> Lot
              Year : <span id="l_year"></span><br> Lot Month : <span id="l_month"></span></h4>
          </div>
        </div>
        <div class="row" id="modal_body_det" style="display: none;">
          <!-- <form method="POST" action="{{ route('push-to-sbi.export') }}" class="submit-once" 
            onSubmit="if(!confirm('Are you sure you want to push to SBI?')){return false;}"> -->
          <input type="hidden" name="_token" value="{{ csrf_token() }}" />
          <input type="hidden" name="lot_no" id="push_lot_no">
          <input type="hidden" name="scheme_id" id="push_scheme_id">
          <div class="col-md-8">
            <label for="bank_account">Select Bank Account</label>
            <select class="form-control" name="bank_account" id="bank_account" required>
              <option value="">Please Select Account No</option>

            </select>
            <span id="error_bank_acc" class="text-danger"></span>
          </div>
          <div class="col-md-4" style="margin-top: 23px;">
            <label class="control-label">&nbsp;</label>
            <button type="submit" name="bulk_approve" id="bulk_approve" value="approve" class="btn btn-info">Push To
              SBI</button>
          </div>
          <!-- </form> -->
        </div>
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


<script>
  $(document).ready(function() {
    $("#pushmodal_sbi").on('hide.bs.modal', function(){
  $('#lot_det').hide();
  $('#modal_body_det').hide();
  });
    $('[data-toggle="tooltip"]').tooltip();
    $('.excel_btn').click(function(){
      var val = $(this).val();
      var array = val.split("_");
      var lot_no = array[0];
      var scheme = array[1];
      var  data= {'_token': '{{csrf_token()}}', 'lot_no': lot_no, 'scheme_id': scheme, 'error_type': 'COUNT'};
      redirectPostExcel('{{route("lot_payment_xls_generate_new")}}', data, 'get');
    });

    $('.excel_btn_failed').click(function(){
      var val = $(this).val();
      var array = val.split("_");
      var lot_no = array[0];
      var scheme = array[1];
      var  data= {'_token': '{{csrf_token()}}', 'lot_no': lot_no, 'scheme_id': scheme, 'error_type': 'E2'};
      redirectPostExcel('{{route("lot_payment_xls_generate_new")}}', data, 'get');
    });

    $('.excel_btn_success').click(function(){
      var val = $(this).val();
      var array = val.split("_");
      var lot_no = array[0];
      var scheme = array[1];
      var  data= {'_token': '{{csrf_token()}}', 'lot_no': lot_no, 'scheme_id': scheme, 'error_type': 'S0'};
      redirectPostExcel('{{route("lot_payment_xls_generate_new")}}', data, 'get');
    });

    $('.pushtosbi_btn').click(function(){
      var select_scheme = $('#select_scheme').val();
      var lot_year = $('#lot_year').val();
      var lot_month = $('#lot_month').val();
      if (select_scheme == '' || lot_year == '' || lot_month == '') {
        //alert('Please select all the fields');
        $.alert({
          title: 'Error!!',
          type: 'red',
          icon: 'fa fa-warning',
          content: '<strong>Please select all the above fields from dropdown [Scheme, Financial Year, Month]</strong>',
        });
      }
      else {
        $('#load_div').show();
        $('#pushmodal_sbi').modal('show');
        $('#s_name').text('');
        $('#l_no').text('');
        $('#l_year').text('');
        $('#l_month').text('');
        $('#push_lot_no').val('');
        $('#push_scheme_id').val('');
        $('#bank_account').empty().append('<option value="">Please Select Account No</option>');

        var acc = '';
        var ifsc = '';
        var val = $(this).val();
        var array = val.split("_");
        var lot_no = array[0];
        var scheme = array[1];
        var debit_no = array[2];
        $.ajax({
          url: "{{ route('push-to-sbi-single-lot') }}",
          method: 'post',
          data: {
            scheme_id: scheme,
            lot_no: lot_no,
            debit_no:debit_no,
            _token:"{{csrf_token()}}"
          },
          success: function(result) {
            $('#load_div').hide();
            $('#lot_det').show();
            $('#modal_body_det').show();
            
            $('#s_name').text(result.datas.data[0].scheme_name);
            $('#l_no').text(result.datas.data[0].lot_no);
            $('#l_year').text(result.datas.data[0].lot_year);
            $('#l_month').text(result.datas.data[0].lot_month);
            $('#push_lot_no').val(result.datas.data[0].lot_no);
            $('#push_scheme_id').val(result.datas.data[0].scheme_id);

            var acc = result.bank_accounts[0].bank_account_no;
            var ifsc = result.bank_accounts[0].ifsc_code;
            $('#bank_account').empty().append('<option value="">Please Select Account No</option>');
            $('#bank_account').append($('<option>', {
              value: acc+':'+ifsc,
              text: acc+' ('+ifsc+')'
            }));
            // console.log(JSON.stringify(result));
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $('#load_div').hide();
            $('#pushmodal_sbi').modal('hide');
            ajax_error(jqXHR, textStatus, errorThrown);
          }
        });
      }
    });

    $('#bulk_approve').click(function(){
      var push_scheme_id = $('#push_scheme_id').val();
      var push_lot_no = $('#push_lot_no').val();
      var push_bank_account = $('#bank_account').val();  
      if (push_scheme_id == '' || push_lot_no == '') {
        //alert('Please wait will data is loading...');
        $.alert({
          title: 'Information !!',
          type: 'orange',
          icon: 'fa fa-info',
          content: '<strong>Please wait  data is loading...</strong>',
        });
      }
      else if ((push_scheme_id != '' && push_lot_no != '') && push_bank_account == '') {
        //alert('Please select bank account');
        style="border-color:#cc0000; background-color:#ffff99;"
        $('#bank_account').css({'border-color':'#cc0000','background-color':'#ffff99'});
        $('#error_bank_acc').text('Please select bank account');
        // $.alert({
        //   title: 'Error!!',
        //   type: 'red',
        //   icon: 'fa fa-warning',
        //   content: '<strong>Please select bank account</strong>',
        // });
      }
      else {
        $('#bank_account').removeAttr('style');
        $('#error_bank_acc').text('');
        $.confirm({
          title: 'Confirm!',
          type: 'orange',
          icon: 'fa fa-warning',
          content: '<strong>Are you want to sign the lot and push to SBI server ?</strong>',
          buttons: {
            confirm: function () {
              $('#load_div').show();
              $('#bulk_approve').html('Please wait...');
              $('#bulk_approve').attr('disabled','disabled');
              $.ajax({
                url: "{{ route('push-to-sbi.export') }}",
                method: 'post',
                data: {
                  scheme_id: push_scheme_id,
                  lot_no: push_lot_no,
                  bank_account: push_bank_account,
                  _token:"{{csrf_token()}}"
                },
                success: function(result) {
                  $('#load_div').hide();
                  $('#pushmodal_sbi').modal('hide');
                  $('#bulk_approve').html('Push To SBI');
                  $('#bulk_approve').removeAttr('disabled');
                  //alert(JSON.stringify(result));
                  $.confirm({
                    title: result.title,
                    type: result.type,
                    icon: result.icon,
                    content: result.msg,
                    buttons: {
                      ok: function () {
                        reload_table();
                      }
                    }
                  });
                  
                },
                error: function (jqXHR, textStatus, errorThrown) {
                  $('#load_div').hide();
                  $('#bulk_approve').html('Push To SBI');
                  $('#bulk_approve').removeAttr('disabled');
                  $('#pushmodal_sbi').modal('hide');
                  ajax_error(jqXHR, textStatus, errorThrown);
                }
              });
            },
            cancel: function () {
            }
          }
        });
      }
    });

    $('.importsbi_btn').click(function(){
      var select_scheme = $('#select_scheme').val();
      var lot_year = $('#lot_year').val();
      var lot_month = $('#lot_month').val();
      if (select_scheme == '' || lot_year == '' || lot_month == '') {
        //alert('Please select all the fields');
        $.alert({
          title: 'Error!!',
          type: 'red',
          icon: 'fa fa-warning',
          content: '<strong>Please select all the above required (*) fields from dropdown [Scheme, Financial Year, Month]</strong>',
        });
      }
      else {
        var val = $(this).val();
        var array = val.split("_");
        var lot_no = array[0];
        var scheme = array[1];
        var debit_no = array[2];
        var lot_month = array[3];
        var lot_year = array[4];
        $.confirm({
          title: 'Confirm!',
          type: 'orange',
          icon: 'fa fa-warning',
          content: '<strong>Are you are sure want to import the SBI Payment Response ?</strong>',
          buttons: {
            confirm: function () {
              $(".content").addClass("disabledcontent");
              $('#loader_img').show();
              $.ajax({
                url: "{{ route('sbi_payment_status') }}",
                method: 'post',
                data: {
                  scheme_id: scheme,
                  lot_no: lot_no,
                  debit_ref: debit_no,
                  lot_month: lot_month,
                  lot_year: lot_year,
                  _token:"{{csrf_token()}}"
                },
                success: function(result) {
                  $('#loader_img').hide();
                  $(".content").removeClass("disabledcontent");
                  // console.log(result.lot_no);
                  $.confirm({
                    title: result.title,
                    type: result.type,
                    icon: result.icon,
                    content: result.msg,
                    buttons: {
                      ok: function () {
                        reload_table();
                      }
                    }
                  });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                  $('#loader_img').hide();
                  $(".content").removeClass("disabledcontent");
                  ajax_error(jqXHR, textStatus, errorThrown);
                }
              });
            },
            cancel: function () {
            }
          }
        });
      }
    });

    $('#example').DataTable( {
      dom: 'Blfrtip',
      "paging": true,
      "pageLength":20,
      "lengthMenu": [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      "scrollX": true,
      buttons: [
       {
           extend: 'pdf',
           title: 'Lot Report- SBI Payment',
           footer: true,
           pageSize:'A4',
           orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2,3,5,7,9,10],

            }
       },
       {
           extend: 'excel',
           title: 'Lot Report- SBI Payment',
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                 columns: [0,1,2,3,5,7,9,10],
                stripHtml: false,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    });
  });

  function reload_table(){
    $('#loader_img').show();
    $('#res_div').show();
    $(".content").addClass("disabledcontent");
    var msg = 'Scheme : '+$( "#select_scheme option:selected" ).text()+' , Financial Year : '+$('#lot_year').val()+' , Month : '+$( "#lot_month option:selected" ).text();
    $.ajax({
      url: "{{ route('lot-master-sbi-list') }}",
      method: 'post',
      data: {
        select_scheme: $('#select_scheme').val(),
        lot_year: $('#lot_year').val(),
        lot_month: $('#lot_month').val(),
        _token:"{{csrf_token()}}"
      },
      success: function(result) {
        $('#loader_img').hide();
        $('#res_div').show();
        $('#sbilot_data').html('');
        $('#sbilot_data').html(result);
        $('#panel_head').text(msg);
        $(".content").removeClass("disabledcontent");
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#loader_img').hide();
        $('#res_div').show();
        // ajax_error(jqXHR, textStatus, errorThrown);
        var msg = "<strong>Failed to Load data.</strong><br/>";
        if (jqXHR.status !== 422 && jqXHR.status !== 400) {
          msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
        } 
        else {
          if (jqXHR.responseJSON.hasOwnProperty('exception')) {
            msg += "Exception: <strong>" + jqXHR.responseJSON.exception_message + "</strong>";
          } 
          else {
            msg += "Error(s):<strong><ul>";
            $.each(jqXHR.responseJSON, function (key, value) {
              msg += "<li>" + value + "</li>";
            });
            msg += "</ul></strong>";
          }
        }
        $.alert({
          title: 'Error!!',
          type: 'red',
          icon: 'fa fa-warning',
          content: msg,
          buttons: {
            ok: function () {
              window.location.href='./index';
            }
          }
        });
      }
    });
  }

  function redirectPostExcel(url, data , method = 'get'){
    var form = document.createElement('form');
    form.method = method;
    form.action = url;
    for (var name in data) {
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = name;
      input.value = data[name];
      form.appendChild(input);
    }
    $('body').append(form);
    form.submit();
  }
</script>