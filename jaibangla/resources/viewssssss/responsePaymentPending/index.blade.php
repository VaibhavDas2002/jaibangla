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
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
<div class="content-wrapper">
  <!-- <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" width="150px" id="loader_img"></div> -->
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Pending Response File from SBI
    </h1>
    <ol class="breadcrumb">
      <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
    </ol>
  </section>
  <section class="content">
    <div class="box box-default">
      <div class="box-body">
        <div id="loadingDiv"></div>
        <div class="panel panel-default">
          <div class="panel-heading"><span id="panel-icon">Filter Here</div>
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
                {{csrf_field()}}
                <div class="row">
                  <div class="col-md-12">

                    <div class="col-md-3">
                      <label class=" control-label">Scheme <span class="text-danger">*</span></label>
                      <select class="form-control select2" name="scheme_type" id='scheme_type' required>
                        <option value="">--Select Scheme--</option>
                        @foreach ($schemes as $scheme)
                        <option value="{{$scheme->Scheme->id}}">{{$scheme->Scheme->scheme_name}}</option>
                        @endforeach
                      </select>
                      <span class="text-danger" id="error_scheme_type"></span>
                    </div>
                    <div class="col-md-3">
                      <label class="control-label">From Date <span class="text-danger">*</span></label>
                    <input type="text" id="from_date" class="form-control" autocomplete="off"name="from_date" placeholder="DD/MM/YYYY">
                    <span class="text-danger" id="error_from_date"></span>
                    </div>
                    <div class="col-md-3">
                      <label class="control-label">To Date <span class="text-danger">*</span></label>
                      <input type="text" id="to_date" class="form-control"  autocomplete="off"name="to_date" placeholder="DD/MM/YYYY">
                      <span class="text-danger" id="error_to_date"></span>
                    </div>
                    <div class="col-md-3" style="margin-top: 23px;">
                      <button class="btn btn-primary" id="submit_btn" type="button" disabled><i class="fa fa-search"></i> Search</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="res_div" style="display: none;">
          <div class="panel panel-default">
            <div class="panel-heading" id="panel_head">List of Pending Lot According to the Lot Pushed Date to the SBI Server</div>
            <div class="panel-body" style="padding: 5px; font-size: 14px;">
              <div class="table-responsive">
                <table id="example" class="table display table-bordered" cellspacing="0" width="100%">
                  <thead style="font-size: 12px;">
                    {{-- <tr>
                      <th rowspan="2" style="vertical-align: middle;">Pushed Date</th>
                      <th colspan="2">Pending 7 Days</th>
                      <th colspan="2">Pending 10 Days</th>
                      <th colspan="2">Pending 15 Days</th>
                    </tr> --}}
                    <tr>
                      <th>SL No.</th>
                      <th>Lot No.</th>
                      <th>No. of Beneficiary</th>
                      <th>Pushed Data</th>
                      <th>Response Received Date</th>
                      <th>Response Received<br> <= 7 Days</th>
                      <th>Response Received<br>>7 & <=10 Days</th>
                      <th>Response Received <br>>10 Days</th>
                    </tr>
                  </thead>
                  <tbody style="font-size: 14px;"></tbody>
                  <tfoot style="font-size: 14px; font-weight: bold; text-align: right;">
                    <tr>
                      <td></td>
                      <td></td> 
                      <td></td> 
                      <td></td> 
                      <td></td>
                      <td></td> 
                      <td></td>
                      <td></td>                      
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->

  <!-- Modal -->
  <div class="modal fade" id="myModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Pending Response Lot Details</h4>
        </div>
        <div class="modal-body">
          <div id=lot_table_div></div>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->

</div>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
  $(document).ready(function() {
    // Live Clock
    var interval = setInterval(function() {
      var momentNow = moment();
      $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
      $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);

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

    $('#loadingDiv').hide();
    $('#submit_btn').removeAttr('disabled');
    $('#res_div').hide();

    var error_scheme_type = '';
    var error_from_date = '';
    var error_to_date = '';
    $('#submit_btn').click(function() {
      if ($.trim($('#scheme_type').val()).length == 0) {
        error_scheme_type = 'Scheme name is required';
        $('#error_scheme_type').text(error_scheme_type);
      } else {
        error_scheme_type = '';
        $('#error_scheme_type').text(error_scheme_type);
      }

      if ($.trim($('#from_date').val()).length == 0) {
        error_from_date = 'Date is required';
        $('#error_from_date').text(error_from_date);
      } else {
        error_from_date = '';
        $('#error_from_date').text(error_from_date);
      }

      if ($.trim($('#to_date').val()).length == 0) {
        error_to_date = 'Date is required';
        $('#error_to_date').text(error_to_date);
      } else {
        error_to_date = '';
        $('#error_to_date').text(error_to_date);
      }

      if (error_scheme_type != '' || error_from_date != '' || error_to_date != '') {
        return false;
      } else {
        $('#loadingDiv').show();
        $('#res_div').show();

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
          "pageLength": 20,
          'lengthMenu': [
            [10, 20, 25, 50, 100, -1],
            [10, 20, 25, 50, 100, 'All']
          ],
          "serverSide": true,
          "processing": true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center" style="font-size: 18px; font-weight: bold; color: green;">Processing...</div>'
          },
          "ajax": {
            url: "{{ url('payment-pending-data') }}",
            type: "post",
            data: function(d) {
              d.from_date = $('#from_date').val(),
              d.to_date = $('#to_date').val(),
              d.scheme_id = $('#scheme_type').val(),
              d._token = "{{csrf_token()}}"
            },
            error: function(jqXHR, textStatus, errorThrown) {
              $('#loadingDiv').hide();
              ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete": function() {
            $('#loadingDiv').hide();
            //console.log('Data rendered successfully');
          },
          "columns": [
            {
              "data": "DT_RowIndex"
            },
            {
              "data": "lot_no"
            },
            {
              "data": "credit_count"
            },
            {
              "data": "pushed_at"
            },
            {
              "data": "response_received_at"
            },
            // {
            //   "data": "pending_7_days_lot"
            // },
            // {
            //   "data": "pending_10_days_lot"
            // },
            // {
            //   "data": "pending_more_than_10_days_lot"
            // },
            {
                "mData": "pending_7_days_lot",
                "mRender": function (data, type, row) {
                    if (data == 1) {
                      return '<i class="fa fa-check text-success"></i>';
                    } else {
                      return '<i class="fa fa-close text-danger"></i>';
                    }
                }
            },
            {
                "mData": "pending_10_days_lot",
                "mRender": function (data, type, row) {
                    if (data == 1) {
                      return '<i class="fa fa-check text-success"></i>';
                    } else {
                      return '<i class="fa fa-close text-danger"></i>';
                    }
                }
            },
            {
                "mData": "pending_more_than_10_days_lot",
                "mRender": function (data, type, row) {
                    if (data == 1) {
                      return '<i class="fa fa-check text-success"></i>';
                    } else {
                      return '<i class="fa fa-close text-danger"></i>';
                    }
                }
            }
          ],
          columnDefs: [
            { className: 'text-right', targets: [2] },
            { className: 'text-center', targets: [3, 4, 5, 6, 7] },
          ],
          "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
              return typeof i === 'string' ?
              i.replace(/[\$,]/g, '')*1 :
              typeof i === 'number' ?
              i : 0;
            };
            total_lot = api
              .column( 1, { page: 'current'} )
              .data()
              .count();

            total_beneficiary = api
              .column( 2, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );
              
              pending_7_days_lot = api
              .column( 5, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );

              pending_10_days_lot = api
              .column( 6, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );

              pending_more_than_10_days_lot = api
              .column( 7, { page: 'current'} )
              .data()
              .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0 );

              // Update footer
              $( api.column( 0 ).footer() ).html(
                "Total-"
              );
              $( api.column( 1 ).footer() ).html(
                total_lot
              );
              $( api.column( 2 ).footer() ).html(
                total_beneficiary
              );
              $( api.column( 3 ).footer() ).html(
                ""
              );
              $( api.column( 4 ).footer() ).html(
                ""
              );
              $( api.column( 5 ).footer() ).html(
                pending_7_days_lot
              );
              $( api.column( 6 ).footer() ).html(
                pending_10_days_lot
              );
              $( api.column( 7 ).footer() ).html(
                pending_more_than_10_days_lot
              );   
          },
          "buttons": [
            'pdfHtml5', 'excel'
          ],
        });
      }
    });

  });

  function openPendingLotModal(scheme_id, type, pushed_at) {
    // console.log(value);
    $.ajax({
      type: 'post',
      url: "{{ route('payment-view-data') }}",
      data: {
        op_type: type,
        scheme_id: scheme_id,
        pushed_at: pushed_at,
        _token: '{{ csrf_token() }}'
      },
      success: function(response) {
        $('#loadingDiv').hide();
        // console.log(htmlLotTable);
        if (response.status == 1) {
          $.alert({
            title: response.title,
            type: response.type,
            icon: response.icon,
            content: response.msg
          });
        } else {
          $('.loadingDivModal').hide();
          $('#lot_table_div').html(response.htmlLotTable);

          var dataTable = "";
          if ($.fn.DataTable.isDataTable('#lotTable')) {
            $('#lotTable').DataTable().destroy();
          }
          dataTable = $('#lotTable').dataTable({
            "dom": 'Blfrtip',
            "pageLength": 10,
            'lengthMenu': [
              [10, 20, 25, 50, 100, -1],
              [10, 20, 25, 50, 100, 'All']
            ],
            "searchable": true,
            "paging": true,
            "scrollX": false,
            "ordering": false,
            "info": true,
            "bFilter": true,
            "bInfo": true,
            "buttons": [{
                extend: 'pdf',
                title: 'Scheme -' + response.schemeName + ' Pending Resposne from SBI',
                // orientation: 'landscape',
                messageTop: 'Scheme - ' + response.schemeName + '\n Pushed Date - ' + response.pushedAt + '\n Status : ' + response.opTypeName,
                exportOptions: {
                  columns: [0, 1, 2, 3],
                },
                text: '<i class="fa fa-file-pdf-o"></i> PDF'
              },
              {
                extend: 'excel',
                title: 'Scheme -' + response.schemeName + ' Pending Resposne from SBI',
                messageTop: 'Scheme - ' + response.schemeName + '\n Pushed Date - ' + response.pushedAt + '\n Status : ' + response.opTypeName,
                exportOptions: {
                  columns: [0, 1, 2, 3],
                },
                text: '<i class="fa fa-file-excel-o"></i> Excel'
              },
            ],
          });

          $('#myModal').modal('show');
        }
      },
      complete: function() {},
      error: function(jqXHR, textStatus, errorThrown) {
        $('#loadingDiv').hide();
        ajax_error(jqXHR, textStatus, errorThrown);
      }
    });
  }



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