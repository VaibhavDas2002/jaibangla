<style type="text/css">
  .has-error {
    border-color: #cc0000;
    background-color: #ffff99;
  }

  .preloader1 {
    position: fixed;
    top: 40%;
    left: 52%;
    z-index: 999;
  }

  .preloader1 {
    background: transparent !important;
  }

  #loadingDi {
    position: absolute;
    top: 0px;
    right: 0px;
    width: 100%;
    height: 100%;
    background-color: #fff;
    background-image: url('images/ajaxgif.gif');
    background-repeat: no-repeat;
    background-position: center;
    z-index: 10000000;
    opacity: 0.4;
    filter: alpha(opacity=40);
    /* For IE8 and earlier */
  }

  .loadingDivModal {
    position: absolute;
    top: 0px;
    right: 0px;
    width: 100%;
    height: 100%;
    background-color: #fff;
    background-image: url('images/ajaxgif.gif');
    background-repeat: no-repeat;
    background-position: center;
    z-index: 10000000;
    opacity: 0.4;
    filter: alpha(opacity=40);
    /* For IE8 and earlier */
  }

  #updateDiv {
    border: 1px solid #d9d9d9;
    padding: 8px;
    box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
  }
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
<div class="content-wrapper">
  <!-- <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" width="150px" id="loader_img"></div> -->
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Date Wise Lot Report <small>(Based on pushed date of the lots)</small>
    </h1>
    <ol class="breadcrumb">
      <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
    </ol>
  </section>
  <section class="content">
    <div class="box box-default">
      <div class="box-body">
        <div id="loadingDi"></div>
        <div class="panel panel-default">
          <div class="panel-heading" style="font-size: 14px; font-weight: bold; font-style: italic;"><span id="panel-icon">Enter Filter Criteria</div>
          <div class="panel-body" style="padding: 5px;">
            <div class="row">
              <div class="col-md-12">
                @if (($message = Session::get('success')) )
                <div class="alert alert-success alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }} </strong>
                </div>
                @endif
                @if (($message = Session::get('message')))
                <div class="alert alert-danger alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }}</strong>
                </div>
                @endif
                @if (($message = Session::get('msg1')))
                <div class="alert alert-danger alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }}</strong>
                </div>
                @endif
                <div class="row">
                  <div class="col-md-12" style="margin-bottom: 10px;">
                    <div class="form-group col-md-3">
                      <label class=" control-label">Scheme <span class="text-danger">*</span></label>
                      <select class="form-control" name="scheme_type" id='scheme_type' required>
                        <option value="">--Select Scheme--</option>
                        @foreach ($schemes as $scheme)
                        <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}
                        </option>
                        @endforeach
                      </select>
                      <span class="text-danger" id="error_scheme_type"></span>
                    </div>
                    <div class="form-group col-md-2">
                      <label for="select_type">Financial Year</label>
                      <select class="form-control select2" name="fin_year" id="fin_year">
                        <option value="">--- Select ---</option>
                        @foreach(Config::get('constants.fin_year') as $key=> $val)
                        <option value="{{$key}}">{{$val}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group col-md-2">
                      <label class="control-label">From Date<span class="text-danger"></span></label>
                      <input type="text" id="from_date" class="form-control" autocomplete="off" name="from_date" placeholder="DD/MM/YYYY">
                    </div>
                    <div class="form-group col-md-2">
                      <label class="control-label">To Date <span class="text-danger"></span></label>
                      <input type="text" id="to_date" class="form-control" autocomplete="off" name="to_date" placeholder="DD/MM/YYYY">
                    </div>
                    <div class="form-group col-md-3" style="margin-top: 24px;">
                      <button class="btn btn-primary" name="submit_btn" id="submit_btn" type="button" disabled><i class="fa fa-search"></i> Search</button>&nbsp;
                      {{-- <button class="btn btn-default" name="reset_btn" id="reset_btn" type="button" disabled><i class="fa fa-refresh"></i> Reset</button> --}}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="res_div" style="display: none;">
          <div class="panel panel-default">
            <div class="panel-heading" id="panel_head" style="font-size: 14px; font-weight: bold; font-style: italic;">List of Beneficiary</div>
            <div class="panel-body" style="padding: 5px; font-size: 14px;">
            <p style="text-align: right; font-weight: bold;">Report Generated On - @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo $date ;@endphp</p>
              <div class="table-responsive">
                <table id="example" class="table display" cellspacing="0" width="100%">
                  <thead style="font-size: 12px;">
                    <tr role="row">
                      <th>Sl No</th>
                      <th>Date</th>
                      <th>Lot Month</th>
                      <th>Lot Year</th>
                      <th>No. of Lot</th>
                      <th>No. of Beneficiary in the Lots</th>
                      <th>Success Count</th>
                      <th>Success Amount (Rs.)</th>
                      <th>Failed Count</th>
                      <th>Response Pending</th>
                    </tr>
                  </thead>
                  <tbody style="font-size: 14px;"></tbody>
                  <tfoot style="font-size: 14px;">
                    <tr>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade in" id="modalLotView">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Lot Wise Status</h4>
              </div>
              <div class="modal-body">
                <div class="loadingDivModal"></div>
                <div id="resultViewTable"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              </div>
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
    $('#from_date').datepicker({
      format: "dd/mm/yyyy",
      todayHighlight: true,
      autoclose: true,
      "setDate": "today",
      "endDate": "today+1",
      //   "maxDate":  new Date(),

    });
    $('#to_date').datepicker({
      format: "dd/mm/yyyy",
      todayHighlight: true,
      autoclose: true,
      "setDate": "today",
      "endDate": "today+1",
      //   "maxDate":  new Date(),

    });
    // Live Clock
    var interval = setInterval(function() {
      var momentNow = moment();
      $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
      $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);

    $('#loadingDi').hide();
    $('#submit_btn').removeAttr('disabled');
    $('#reset_btn').removeAttr('disabled');

    var error_scheme_type = '';
    $('#submit_btn').click(function() {
      if ($.trim($('#scheme_type').val()).length == 0) {
        error_scheme_type = 'Scheme name is required';
        $('#error_scheme_type').text(error_scheme_type);
      } else {
        error_scheme_type = '';
        $('#error_scheme_type').text(error_scheme_type);
      }

      if (error_scheme_type != '') {
        return false;
      } else {
        $('#loadingDi').show();
        $('#res_div').show();
        var msg = 'Scheme : ' + $("#scheme_type option:selected").text();
        $('#panel_head').text(msg);
        if ($.fn.DataTable.isDataTable('#example')) {
          $('#example').DataTable().destroy();
        }
        var table = $('#example').DataTable({
          dom: 'Blfrtip',
          "scrollX": true,
          "paging": true,
          "searchable": true,
          "ordering": false,
          "bFilter": true,
          "bInfo": true,
          "pageLength": 25,
          'lengthMenu': [
            [10, 20, 25, 50, 100, -1],
            [10, 20, 25, 50, 100, 'All']
          ],
          "serverSide": true,
          "processing": true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
          },
          "ajax": {
            url: "{{ url('calenderPaymentIndexGetData') }}",
            type: "post",
            data: function(d) {
              d.scheme_id = $("#scheme_type").val(),
              d.from_date = $("#from_date").val(),
              d.to_date = $("#to_date").val(),
              d.fin_year = $("#fin_year").val(),
              d._token = "{{csrf_token()}}"
            },
            error: function(jqXHR, textStatus, errorThrown) {
              $('#loadingDi').hide();
              $('.preloader1').hide();
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete": function() {
            $('#loadingDi').hide();
            //console.log('Data rendered successfully');
          },
          "columns": [{
              "data": "DT_RowIndex"
            },
            {
              "data": "pushed_at"
            },
            {
              "data": "lot_month"
            },
            {
              "data": "lot_year"
            },
            {
              "data": "no_of_lots"
            },
            {
              "data": "total_ben_in_lots",
              "defaultContent": "0"
            },
            {
              "data": "success_count",
              "defaultContent": "0"
            },
            {
              "data": "success_amount",
              "defaultContent": "0"
            },
            {
              "data": "failed_count",
              "defaultContent": "0"
            },
            {
              "data": "response_pending",
              "defaultContent": "0"
            },
          ],
          'columnDefs': [{
              "targets": [3, 4, 5, 6],
              // "className": "dt-body-right",
            },
            //hide the second & fourth column
            {
              'visible': false,
              'targets': []
            }
          ],

          "footerCallback": function(row, data, start, end, display) {

            var api = this.api(),
              data;

            // Remove the formatting to get integer data for summation
            var intVal = function(i) {
              return typeof i === 'string' ?
                i.replace(/[\$,]/g, '') * 1 :
                typeof i === 'number' ?
                i : 0;
            };

            // Total over this page



            footer4 = api
              .column(4, {
                page: 'current'
              })
              .data()
              .reduce(function(a, b) {
                return intVal(a) + intVal(b);
              }, 0);
            footer5 = api
              .column(5, {
                page: 'current'
              })
              .data()
              .reduce(function(a, b) {
                return intVal(a) + intVal(b);
              }, 0);
            footer6 = api
              .column(6, {
                page: 'current'
              })
              .data()
              .reduce(function(a, b) {
                return intVal(a) + intVal(b);
              }, 0);
            footer7 = api
              .column(7, {
                page: 'current'
              })
              .data()
              .reduce(function(a, b) {
                return intVal(a) + intVal(b);
              }, 0);
            footer8 = api
              .column(8, {
                page: 'current'
              })
              .data()
              .reduce(function(a, b) {
                return intVal(a) + intVal(b);
              }, 0);
            footer9 = api
              .column(9, {
                page: 'current'
              })
              .data()
              .reduce(function(a, b) {
                return intVal(a) + intVal(b);
              }, 0);

            // Update footer
            $(api.column(0).footer()).html(
              "Total-"
            );
            $(api.column(1).footer()).html(
              ""
            );
            $(api.column(2).footer()).html(
              ""
            );
            $(api.column(3).footer()).html(
              ""
            );
            $(api.column(4).footer()).html(
              footer4
            );
            $(api.column(5).footer()).html(
              footer5
            );
            $(api.column(6).footer()).html(
              footer6
            );
            $(api.column(7).footer()).html(
              footer7
            );
            $(api.column(8).footer()).html(
              footer8
            );
            $(api.column(9).footer()).html(
              footer9
            );

          },

          "buttons": [{
              extend: 'pdf',
              footer: true,
              pageSize: 'A4',
              title: "Datewise Payment Report @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp",
              messageTop:"Scheme : "+$("#scheme_type option:selected").text()+"Report Generated On - @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp",
              //orientation: 'landscape',
              pageMargins: [40, 60, 40, 60],
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7],

              }
            },
            {
              extend: 'excel',
              footer: true,
              pageSize: 'A4',
              title: "Datewise Payment Report @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp",
              messageTop:"Scheme : "+$("#scheme_type option:selected").text()+"Report Generated On - @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp",
              //orientation: 'landscape',
              pageMargins: [40, 60, 40, 60],
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7],
                stripHtml: false,
              }
            },
            //'pdf','excel','print'
          ],
        });
      }
    });
  });

  $(document).on('click', '.lot_view', function() {
    var view_value = $(this).val();
    // console.log(view_value);
    var valArr = view_value.split('_');
    var v_pushed_at = valArr[0];
    var v_lot_month = valArr[1];
    var v_lot_year = valArr[2];
    var v_payment_mode = valArr[3];
    var v_scheme_id = valArr[4];
    $('#loadingDi').show();
    $.ajax({
      type: 'post',
      url: "{{ route('calenderPaymentGetDataLotwise') }}",
      data: {
        pushed_at: v_pushed_at,
        lot_month: v_lot_month,
        lot_year: v_lot_year,
        payment_mode: v_payment_mode,
        scheme_id: v_scheme_id,
        _token: '{{ csrf_token() }}'
      },
      success: function(response) {
        $('#loadingDi').hide();
        $('.loadingDivModal').hide();
        // console.log(response);
        $('#modalLotView').modal('show');
        $('#resultViewTable').html("");
        $('#resultViewTable').html(response.htmlTable);
        $('#exampleLotTable').DataTable({
          dom: 'Blfrtip',
          "paging": true,
          "searchable": true,
          "ordering": false,
          "bFilter": true,
          "bInfo": true,
          "pageLength": 10,
          'lengthMenu': [
            [10, 20, 25, 50, 100, -1],
            [10, 20, 25, 50, 100, 'All']
          ],
          "buttons": [{
              extend: 'pdf',
              footer: true,
              pageSize: 'A4',
              title: "Datewise Payment Report @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp",
              messageTop:"Report Generated On - @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp",
            },
            {
              extend: 'excel',
              footer: true,
              pageSize: 'A4',
              title: "Datewise Payment Report @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp",
              messageTop:"Report Generated On - @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp",
            },
            //'pdf','excel','print'
          ],
        });
      },
      complete: function() {},
      error: function(jqXHR, textStatus, errorThrown) {
        $('#loadingDi').hide();
        $('.loadingDivModal').hide();
        ajax_error(jqXHR, textStatus, errorThrown);
      }
    });
  });

  function ajax_error(jqXHR, textStatus, errorThrown) {
    var msg = "<strong>Failed to Load data.</strong><br/>";
    if (jqXHR.status !== 422 && jqXHR.status !== 400) {
      msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
    } else {
      if (jqXHR.responseJSON.hasOwnProperty('exception')) {
        msg += "Exception: <strong>" + jqXHR.responseJSON.exception_message + "</strong>";
      } else {
        msg += "Error(s):<strong><ul>";
        $.each(jqXHR.responseJSON, function(key, value) {
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
    });
  }
</script>