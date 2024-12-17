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
                Social Registry MIS Report
            </h1>
            <ol class="breadcrumb">
                <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span
                        class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
            </ol>
        </section>
        <section class="content">
            <div class="box box-default">
                <div class="box-body">
                    <div id="loadingDiv"></div>
                    <div class="panel panel-default">
                        <div class="panel-heading" style="font-size: 15px; font-weight: bold; font-style: italic;"><span
                                id="panel-icon">Select Date Range</div>
                        <div class="panel-body" style="padding: 5px;">
                            <div class="row">
                                <div class="col-md-12">
                                    @if ($message = Session::get('success'))
                                        <div class="alert alert-success alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }} </strong>
                                        </div>
                                    @endif
                                    @if ($message = Session::get('message'))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    @if ($message = Session::get('msg1'))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Date range:</label>
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" class="form-control pull-right"
                                                            id="reservation">
                                                        <span class="text-danger" id="error_reservation"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="margin-top: 25px;">
                                                <button class="btn btn-primary" name="submit_btn" id="submit_btn"
                                                    type="button" disabled><i class="fa fa-search"></i>
                                                    Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert print-error-msg" style="display:none;" id="errorDiv">
                        <button type="button" class="close" aria-label="Close" onclick="closeError('errorDiv')"><span
                                aria-hidden="true">&times;</span></button>
                        <ul></ul>
                    </div>

                    <div id="search_details" style="display: none;">
                        <div class="panel panel-default">
                            <div class="panel-heading" id="heading_msg"
                                style="font-size: 15px; font-weight: bold; font-style: italic;">MIS Report</div>
                            <div class="panel-body" style="padding: 5px; font-size: 12px;">
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped table-bordered" cellspacing="0"
                                        width="100%" style="font-size: 14px;">
                                        <thead>
                                            <th>Sl No.</th>
                                            <th>Scheme ID</th>
                                            <th>Scheme Name</th>
                                            <th>Approved Mapped Beneficiary</th>
                                            <th>Approved Unmapped Beneficiary</th>
                                            <th>Mapped Transaction</th>
                                            <th>Unapped Transaction</th>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th><th></th><th></th><th></th><th></th><th></th><th></th>
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
    </div>
@endsection
<script src="{{ asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script>
    $(document).ready(function() {
        $("#rejectingapprove").hide();
        // Live Clock
        var interval = setInterval(function() {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);
        $('#loadingDiv').hide();
        $('#submit_btn').removeAttr('disabled');
        $('#reset_btn').removeAttr('disabled');

        $('#reservation').daterangepicker({
            // Set the initial date range
            // startDate: moment().subtract(6, 'days'),
            // endDate: moment(),
            // Set the maximum date range to 7 days
            maxDate: moment(),
            dateLimit: {
                days: 7
            },
            // ... other options as needed
        });

        var error_reservation = '';
        var error_search_for = '';
        var error_district = '';
        $('#submit_btn').click(function() {
            // alert('H');
            // var scheme_code = $('#status').val();
            // alert(scheme_code);
            if ($.trim($('#reservation').val()).length == 0) {
                error_reservation = 'Date is required';
                $('#error_reservation').text(error_reservation);
            } else {
                error_reservation = '';
                $('#error_reservation').text(error_reservation);
            }
            if (error_reservation != '') {
                return false;
            } else {
                $('#loadingDiv').show();
                $('#search_details').show();
                // $(':input[type="button"]').prop('disabled', false);

                var date = $('#reservation').val();
                if ($.fn.DataTable.isDataTable('#example')) {
                    $('#example').DataTable().destroy();
                }
                var table = $('#example').DataTable({
                    dom: 'Blfrtip',
                    "scrollX": true,
                    "paging": false,
                    "searchable": true,
                    "ordering": false,
                    "bFilter": false,
                    "bInfo": false,
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
                        url: "{{ route('getSrMisReport') }}",
                        type: "post",
                        data: function(d) {
                            d.date = $('#reservation').val(),
                                d._token = "{{ csrf_token() }}"
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $('#submit_btn').attr('disabled', false);
                            $('#loadingDiv').hide();
                            $('.preloader1').hide();
                            ajax_error(jqXHR, textStatus, errorThrown);
                        }
                    },
                    "initComplete": function() {
                        $('#loadingDiv').hide();
                        //console.log('Data rendered successfully');
                    },
                    "columns": [{
                            "data": "DT_RowIndex",
                        },
                        {
                            "data": "scheme_id"
                        },
                        {
                            "data": "scheme_name"
                        },
                        {
                            "data": "approved_mapped_beneficiary"
                        },
                        {
                            "data": "approved_unmapped_beneficiary"
                        },
                        {
                            "data": "mapped_transaction"
                        },
                        {
                            "data": "unmapped_transaction"
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

                        // Update footer
                        $(api.column(1).footer()).html(
                            "Total: "
                        );
                        $(api.column(2).footer()).html(
                            ""
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
                    },
                    "buttons": [{
                            extend: 'pdf',
                            title: "Social Registry MIS Report For JaiBangla Generated On-@php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp ",   
                            messageTop: function(){
                                var dateRange = $("#reservation").val();
                                return "Report Generation on Date(MM/DD/YYYY): "+dateRange+"\nDate: @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp";
                            },
                            footer: true,
                            pageSize: 'A4',
                            orientation: 'landscape',
                            pageMargins: [40, 60, 40, 60],
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5],

                            }
                        },
                        {
                            extend: 'excel',
                            title: "Social Registry MIS Report For JaiBangla Generated On-@php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp ",   
                            messageTop: function(){
                                var dateRange = $("#reservation").val();
                                return "Report Generation on Date(MM/DD/YYYY): "+dateRange+"\nDate: @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp";
                            },
                            footer: true,
                            pageSize: 'A4',
                            orientation: 'landscape',
                            pageMargins: [40, 60, 40, 60],
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5],
                                stripHtml: false,
                            }
                        },
                        // 'pdf'
                    ],
                    
                });
            }
        });
        // Export Excel
    });

    function redirectPost(url, data, method = 'post') {
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

    function printMsg(msg, msgtype, divid) {
        $("#" + divid).find("ul").html('');
        $("#" + divid).css('display', 'block');
        if (msgtype == '0') {
            //alert('error');
            $("#" + divid).removeClass('alert-success');
            //$('.print-error-msg').removeClass('alert-warning');
            $("#" + divid).addClass('alert-warning');
        } else {
            $("#" + divid).removeClass('alert-warning');
            $("#" + divid).addClass('alert-success');
        }
        if (Array.isArray(msg)) {
            $.each(msg, function(key, value) {
                $("#" + divid).find("ul").append('<li>' + value + '</li>');
            });
        } else {
            $("#" + divid).find("ul").append('<li>' + msg + '</li>');
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
</script>
