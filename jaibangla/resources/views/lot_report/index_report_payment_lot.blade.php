<style type="text/css">
    .preloader1 {
        position: fixed;
        top: 40%;
        left: 52%;
        z-index: 999;
    }

    .preloader1 {
        background: transparent !important;
    }

    .disabledcontent {
        pointer-events: none;
        opacity: 0.4;
    }

    .has-error {
        border-color: #cc0000;
        background-color: #ffff99;
    }

    .modal {
        text-align: center;
        padding: 0 !important;
    }

    .modal:before {
        content: '';
        display: inline-block;
        height: 100%;
        vertical-align: middle;
        margin-right: -4px;
    }

    .modal-dialog {
        display: inline-block;
        text-align: left;
        vertical-align: middle;
    }

    label.required:after {
        color: red;
        content: '*';
        font-weight: bold;
        margin-left: 5px;
        float: right;
        margin-top: 5px;
    }

    .filterDiv {
        border: 1px solid #d9d9d9;
        border-left: 3px solid deepskyblue;
        margin-bottom: 10px;
        padding: 8px;
        box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
    }

    .resultDiv {
        border: 1px solid #d9d9d9;
        border-left: 3px solid seagreen;
        /*margin-bottom: 10px; */
        padding: 8px;
        box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
    }
</style>

@extends('layouts.app-template-datatable_new')
@section('content')

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Districtwise Beneficiary Payment Report
            </h1>
            {{-- <ol class="breadcrumb">
      <span style="font-size: 12px; font-weight: bold;"><i class="fa fa-clock-o"> Date : </i><span
          class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
    </ol> --}}
        </section>
        <section class="content">
            <div class="box box-default">
                <div class="box-body">
                    <div id="loadingDiv">
                    </div>

                    <!-- <div class="panel panel-default">
              <div class="panel-heading">Search By District, Year and Month</div>
              <div class="panel-body" style="padding: 5px;"> -->
                    <div class="filterDiv">
                        <div class="row">
                            @if ($message = Session::get('success'))
                                <div class="alert alert-success alert-block">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>{{ $message }}</strong>

                                </div>
                            @endif
                            @if (count($errors) > 0)
                                <div class="alert alert-danger alert-block">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li><strong> {{ $error }}</strong></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <label class="control-label" for="selectScheme">Scheme <span
                                            class="text-danger">*</span>
                                    </label>
                                    <select name="selectScheme" id="selectScheme" class="form-control" tabindex="1" onchange="getMonthNames()">
                                        <option value="" selected>---Select Scheme---</option>
                                        @foreach ($schemes as $scheme)
                                            <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="error_selectScheme" class="text-danger"></span>
                                </div>

                                <div class="col-md-2">
                                    <label class="control-label">Financial Year <span class="text-danger">*</span></label>
                                    <select class="form-control" name="lot_year" id='lot_year' onchange="getMonthNames()">
                                        <option value="">--Select Financial Year--</option>
                                        @foreach (Config::get('constants.fin_year') as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="control-label">Month <span class="text-danger">*</span></label>
                                    <select class="form-control" name="lot_month" id='lot_month'>
                                        <option value="">--Select Month--</option>
                                        {{-- @foreach (Config::get('constants.monthval') as $key => $month)
                                            <option value="{{ $key }}">{{ $month }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>

                                <div class="col-md-2" style="margin-top: 23px;">
                                    <label class=" control-label">&nbsp; </label>
                                    <button type="button" name="filter" id="filter" class="btn btn-success"><i
                                            class="fa fa-search"></i> Search</button>&nbsp;&nbsp;
                                    {{-- <button type="button" name="reset" id="reset" class="btn btn-warning">Reset</button>  --}}
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- </div> -->

                    <!-- <div class="panel panel-default" id="res_div" style="display: block;">
              <div class="panel-heading" id="panel_head"
                style="font-size: 16px; background: linear-gradient(to right, #c9d6ff, #e2e2e2); font-weight: bold; font-style: italic;">Payment Lot Report
              </div>
              <div class="panel-body" style="padding: 5px; font-size: 14px;"> -->


                    <div class="table-responsive resultDiv" id="payment_div" style="display: none;">
                        <table id="tableForPayment" class="table table-bordered display" cellspacing="0" width="100%"
                            style="border: 2px solid ghostwhite;">
                            <thead style="font-size: 12px;">
                                {{-- <tr role="row">
                                    <th colspan="1">District[A]</th>
                                    <th colspan="4">Beneficiary Record[B]</th>
                                    <th colspan="10">Payment Status[C]</th>
                                    <th colspan="4">Rejected[D]</th>
                                    <th colspan="4">Payment Awaited[E]</th>
                                    <th colspan="4">Amount[F]</th>
                                </tr> --}}
                                <tr role="row">
                                    <th>District <br><span style="font-weight: normal;">[1]</span></th>
                                    {{-- <th>Total Beneficiary <br><span style="font-weight: normal;">[2]</span></th> --}}
                                    <th>Lot Generated <br><span style="font-weight: normal;">[2]</span></th>
                                    <th>Lot Not Generated <br><span style="font-weight: normal;">[3]</span></th>
                                    <th>Send To Bank <br><span style="font-weight: normal;">[4]</span></th>
                                    <th>Send To Bank Amount <br><span style="font-weight: normal;">[5]</span></th>
                                    <th>Response Received <br><span style="font-weight: normal;">[6]</span></th>
                                    <th>IFMS Returned <br><span style="font-weight: normal;">[7]</span></th>
                                    <th>Payment Success <br><span style="font-size: 10px; font-weight: normal;">(SBI/RBI)</span> <br><span style="font-weight: normal;">[8]</span></th>
                                    <th>Payment Failure <br><span style="font-size: 10px; font-weight: normal;">(SBI/RBI)</span> <br><span style="font-weight: normal;">[9]</span></th>
                                    <th>Amount Disbursed <br><span style="font-weight: normal;">[10]</span></th>
                                    {{-- <th>Approved Bank Edited <br><span style="font-weight: normal;">[12]</span></th> --}}
                                    {{-- <th>Deactivate <br><span style="font-weight: normal;">[13]</span></th> --}}
                                </tr>
                            </thead>
                            <tbody style="font-size: 14px;">
                            </tbody>
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
                                    <td></td>
                                    <td></td>
                                    {{-- <td></td>
                                    <td></td>
                                    <td></td> --}}
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- </div>
            </div> -->
                </div>
            </div>
        </section>
    </div>
@endsection
<script src="{{ asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Live Clock
        var interval = setInterval(function() {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);
        $('#loadingDiv').hide();
        $('#filter_div').removeClass('disabledcontent');
        // $('#loader_img').hide();
        //    $('#res_div').hide();
        $('#submit_btn').removeAttr('disabled');
        // $('#change_msg').hide();
        // $('#lot_year_div').hide();
        // $('#lot_month_div').hide();
        // $('#lot_status_div').hide();
        $('.sidebar-menu li').removeClass('active');
        $('.sidebar-menu #paymentReportMain').addClass("active");
        $('.sidebar-menu #lotPaymentReport').addClass("active");

    });


    $(document).ready(function() {
        //$('#loadingDiv').hide(); 
        $('#filter').click(function() {
            //$('#loadingDiv').show();   
            // var district_code = $('#district_code').val();  
            var phase_code = $('#phase_code').val();
            var lot_year = $('#lot_year').val();
            var lot_month = $('#lot_month').val();

            if (lot_month == '' || lot_year == '') {
                // alert('Please select financial year');
                $.alert({
                    title: 'Alert!',
                    content: 'Please select financial year and month',
                    type: 'red',
                    icon: 'fa fa-warning',

                });
            } else {
                $('#payment_div').show();
                list_table();
                // $('#tableForPayment').DataTable().ajax.reload();        
                // if(district_code != '' || lot_year != '')
                // {  
                //   $('#tableForPayment').DataTable().ajax.reload();            
                // }
                // else{
                //   // alert('Please select district or financial year');
                //   $.alert({
                //     title: 'Alert!',
                //     content: 'Please select district or financial year',
                //   });
                // }

            }
        });

        $('#reset').click(function() {
            //$('#loadingDiv').show();    
            // $('#district_code').val(""); 
            $('#phase_code').val("");
            $('#lot_month').val("");
            $('#lot_year').val("");
            $('#tableForPayment').DataTable().ajax.reload();

        });

    });

    // Get All enabled months whose lot generation enabled
    function getMonthNames() {
        var scheme_id =  $("#selectScheme").val();
        var fin_year = $('#lot_year').val();
        if (scheme_id != '' && fin_year != '') {
            $('#loadingDiv').show();
            $.ajax({
                url: "{{ route('getLotCreateEnabledMonthList') }}",
                method: 'POST',
                data: {
                    select_scheme: scheme_id,
                    lot_year: fin_year,
                    _token: "{{ csrf_token() }}"
                },
                success: function(result) {
                    $('#loadingDiv').hide();
                    $('#lot_month').html('');
                    $('#lot_month').html(result.monthData);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loadingDiv').hide();
                    ajax_error(jqXHR, textStatus, errorThrown);
                }
            });
        }
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

    function list_table() {
        var table = "";
        $("#tableForPayment").dataTable().fnDestroy();
        table = $('#tableForPayment').DataTable({

            dom: 'Blfrtip',
            // "scrollX": true,
            "paging": false, // Disable Pagination
            "searchable": true,
            "ordering": false, // Disable Ordering of all column
            "bFilter": true,
            "bInfo": false, // Disable Showing 1 to 20 of 2000 entries
            "pageLength": 25,
            'lengthMenu': [
                [10, 20, 30, 50, 100],
                [10, 20, 30, 50, 100]
            ],
            "serverSide": true,
            "processing": true,
            "bRetrieve": true,
            "oLanguage": {
                "sProcessing": '<div class="preloader1" align="center"><img src="images/ZKZg.gif" width="100px"></div>'
            },
            ajax: {
                url: "{{ url('reportPaymentLotpost') }}",
                type: "POST",
                data: function(d) {
                    d.selectScheme = $("#selectScheme").val(),
                    d.lot_year = $("#lot_year").val(),
                    d.lot_month = $("#lot_month").val(),
                    d._token = "{{ csrf_token() }}"
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('.preloader1').hide();
                    $.alert({
                        title: 'Error!!',
                        type: 'red',
                        icon: 'fa fa-warning',
                        content: 'Somthing went wrong may be session timeout. Please logout and login again.',
                    });
                    //ajax_error(jqXHR, textStatus, errorThrown);
                }
            },

            columns: [
                // { "data": "DT_RowIndex" },
                {
                    "data": "district_name"
                },
                // {
                //     "data": "total_beneficiary",
                //     "defaultContent": "0"
                // },
                {
                    "data": "lot_generated",
                    "defaultContent": "0"
                },
                {
                    "data": "lot_not_generated",
                    "defaultContent": "0"
                },
                {
                    "data": "push_to_bank",
                    "defaultContent": "0"
                },
                {
                    "data": "push_to_bank_amount",
                    "defaultContent": "0"
                },
                {
                    "data": "response_received",
                    "defaultContent": "0"
                },
                {
                    "data": "ifms_returned",
                    "defaultContent": "0"
                },
                {
                    "data": "payment_success",
                    "defaultContent": "0"
                },
                {
                    "data": "payment_failure",
                    "defaultContent": "0"
                },
                {
                    "data": "amount_disbursed",
                    "defaultContent": "0"
                },
                // {
                //     "data": "bank_edited",
                //     "defaultContent": "0"
                // },
                // {
                //     "data": "deactivate_ben",
                //     "defaultContent": "0"
                // }
            ],
            'columnDefs': [{
                "targets": [1, 2, 3, 4, 5, 6, 7, 8, 9],
                "className": "dt-body-right",
            }],

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
                footer1 = api
                    .column(1, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                footer2 = api
                    .column(2, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                footer3 = api
                    .column(3, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
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
                    /*
                footer10 = api
                    .column(10, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                footer11 = api
                    .column(11, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                footer12 = api
                    .column(12, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);*/
                // Update footer
                $(api.column(0).footer()).html(
                    "Total-"
                );
                $(api.column(1).footer()).html(
                    footer1
                );
                $(api.column(2).footer()).html(
                    footer2
                );
                $(api.column(3).footer()).html(
                    footer3
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
                /*$(api.column(10).footer()).html(
                    footer10
                );
                $(api.column(11).footer()).html(
                    footer11
                );
                $(api.column(12).footer()).html(
                    footer12
                );*/
            },

            buttons: [{
                    extend: 'pdfHtml5',
                    title: "Payment  Report- District Wise @php
                        date_default_timezone_set('Asia/Kolkata');
                        $date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));
                        $date = $date->format('F j, Y g:i:a');
                        echo $date;
                    @endphp",
                    messageTop: "Date:@php
                        date_default_timezone_set('Asia/Kolkata');
                        $date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));
                        $date = $date->format('F j, Y g:i:a');
                        echo $date;
                    @endphp, Lot Year - " + $("#lot_year").val() +
                        " , Lot Month - " + $("#lot_month option:selected").text(),
                    footer: true,
                    orientation: 'landscape',
                    pageSize: 'A1',
                    pageMargins: [5, 5, 5, 5],
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],

                    }
                },

                {
                    extend: 'excel',
                    title: "Payment  Report- District Wise @php
                        date_default_timezone_set('Asia/Kolkata');
                        $date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));
                        $date = $date->format('F j, Y g:i:a');
                        echo $date;
                    @endphp",
                    messageTop: "Date:@php
                        date_default_timezone_set('Asia/Kolkata');
                        $date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));
                        $date = $date->format('F j, Y g:i:a');
                        echo $date;
                    @endphp, Lot Year - " + $("#lot_year").val() +
                        " , Lot Month - " + $("#lot_month option:selected").text(),
                    footer: true,
                    pageSize: 'A4',
                    orientation: 'landscape',
                    pageMargins: [40, 60, 40, 60],
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                        stripHtml: true,
                    }
                },


            ]


        });
    }
</script>
