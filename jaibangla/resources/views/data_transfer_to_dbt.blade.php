<style type="text/css">
    .required-field::after {
        content: "*";
        color: red;
    }

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

    .panel-heading {
        padding: 0;
        border: 0;
    }

    .panel-title>a,
    .panel-title>a:active {
        display: block;
        padding: 5px;
        color: #555;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        word-spacing: 3px;
        text-decoration: none;
    }

    .panel-heading a:before {
        font-family: 'Glyphicons Halflings';
        content: "\e114";
        float: right;
        transition: all 0.5s;
    }

    .panel-heading.active a:before {
        -webkit-transform: rotate(180deg);
        -moz-transform: rotate(180deg);
        transform: rotate(180deg);
    }

    #loadingDivModal {
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

    #enCloserTable tbody tr td {
        padding: 10px 10px 10px 10px;
    }

    .modal-open {
        overflow: visible !important;
    }

    .disabledcontent {
        opacity: 0.4;
        pointer-events: none;
    }
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Data Pushed to DBT
            </h1>

        </section>
        <section class="content">
            <div class="box box-default">
                <div class="box-body">
                    <input type="hidden" name="dist_code" id="dist_code" value="" class="js-district_1">
                    <div class="panel panel-default">
                        <div class="panel-heading">Data Pushed Criteria</div>
                        <div class="panel-body" style="padding: 5px;">
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
                            {{-- <form method="GET" action="{{ route('jbDataPushedToDbt') }}"> --}}
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="required-field">Select Scheme</label>
                                        <select class="form-control" name="scheme_id" id="scheme_id">
                                            <option value="">--Select Scheme--</option>
                                            @foreach ($schemes as $scheme)
                                                <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}</option>
                                            @endforeach
                                        </select>
                                        <span id="error_scheme_id" class="text-danger"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class=" control-label required-field">Select Financial Year</label>
                                        <select class="form-control" name="fin_year" id='fin_year' required>
                                            <option value="">--Select Financial Year--</option>
                                            @foreach($finYears as $key => $finYear)
                                            <option value="{{ $key }}">{{ $finYear }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger" id="error_fin_year"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class=" control-label required-field">Select Month</label>
                                        <select class="form-control" name="month" id='month' required>
                                            <option value="">--Select Month--</option>
                                            @foreach($monthVals as $key => $monthVal)
                                            <option value="{{ $key }}">{{ $monthVal }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger" id="error_month"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <center>
                                        <div class="form-group col-md-12" style="margin-top: 24px;">
                                            <button type="submit" name="filter" id="filter" class="btn btn-success"> Show Data</button>&nbsp;&nbsp;
                                            <button type="button" name="reset" id="reset" class="btn btn-warning"><i
                                                    class="fa fa-refresh"></i> Reset</button>
                                        </div>
                                        <div class=""><img src="{{ asset('images/ZKZg.gif') }}"
                                            id="submit_loader1" width="50px" height="50px"
                                            style="display:none;"></div>
                                    </center>
                                </div>
                            {{-- </form> --}}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="modal fade bd-example-modal-lg" id="modalUpdateAadhar" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Data Pushed to DBT for <b><span id="month_name"></span><b></h4>
                </div>
                <div class="modal-body">
                    <div id="loadingDivModal"></div>
                    <div class="" id="updateDiv">                                
                        <table class="table table-bordered table-responsive table-condensed table-striped" style="font-size: 14px;">
                            <tr>
                                <td>
                                    <strong>Scheme Code: </strong>
                                    <span id="scheme_code"></span>
                                </td>
                                <td>
                                    <strong>Financial Year Code: </strong>
                                    <span id="fin_year_code"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Financial Year: </strong>
                                    <span id="financial_year"></span>
                                </td>
                                <td>
                                    <strong>Reporting Month: </strong>
                                    <span id="reporting_month"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Total Beneficiary: </strong>
                                    <span id="total_ben"></span>
                                </td>
                                <td>
                                    <strong>Total Beneficiary Digitized :</strong>
                                    <span id="total_ben_digitized"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Beneficiary Aadhar Seeded: </strong>
                                    <span id="ben_aadhar_seeded"></span>
                                </td>
                                <td>
                                    <strong>Mobile Captured:</strong>
                                    <span id="mob_captured"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Benefit Type: </strong>
                                    <span id="benefit_type"></span>
                                </td>
                                <td>
                                    <strong>Fund Transfer Cash:</strong>
                                    <span id="fund_transfer_cash"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Number of Transaction Cash Electronic: </strong>
                                    <span id="no_trans_cash_electronics"></span>
                                </td>
                                <td>
                                    <strong>Amount Transaction Cash Electronic:</strong>
                                    <span id="amt_trans_cash_electronics"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Transaction Aadhar Seeded: </strong>
                                    <span id="trans_aadhar_seeded"></span>
                                </td>
                                <td>
                                    <strong>Number of De-duplicated: </strong>
                                    <span id="no_de_dup"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Saving Amount: </strong>
                                    <span id="saving_amount"></span>
                                </td>
                                <td>
                                    <strong>Remarks: </strong>
                                    <span id="remarks"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Total Beneficiary Incremental: </strong>
                                    <span id="total_ben_incremental"></span>
                                </td>
                                <td>
                                    <strong>Beneficiary Digitized Incremental: </strong>
                                    <span id="ben_digitized_incremental"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Beneficiary Aadhar Seeded Incremental: </strong>
                                    <span id="ben_aadhar_seeded_incremental"></span>
                                </td>
                                <td>
                                    <strong>Mobile Captured Incremental: </strong>
                                    <span id="mob_captured_incremental"></span>
                                </td>
                            </tr>
                        </table>
                        <div class="row">
                            <div class="col-md-12" style="text-align: center;"><input type="button" name="submit"
                                    value="Push" id="pushedToDbt" class="btn btn-info btn-lg"></div>
                        </div>
                        <!-- </div> -->
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script>
        // Define an array of month names
        var monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        function getMonthName(monthNumber) {
            if (monthNumber < 1 || monthNumber > 12) {
                return "Invalid month number"; // Handle out-of-range numbers
            }
            return monthNames[monthNumber - 1]; // Array is 0-based
        }
        $(document).ready(function() {
            $('.sidebar-menu li').removeClass('active');
            $('.sidebar-menu #bankTrFailed').addClass("active");
            $('.sidebar-menu #accValTrFailedVerified').addClass("active");
            $('#opreation_type').val('A');
            $("#verifyReject").html("Approve");
            $('#div_rejection').hide();
            

            $('#filter').click(function() {
                var month = $('#month').val();
                var monthName = getMonthName(month);

                $('#month_name').text(monthName);
                if ($.trim($('#scheme_id').val()).length == 0) {
                    error_scheme_id = 'Scheme is required';
                    $('#error_scheme_id').text(error_scheme_id);
                } else {
                    error_scheme_id = '';
                    $('#error_scheme_id').text(error_scheme_id);
                }

                if ($.trim($('#month').val()).length == 0) {
                    error_month = 'Month is required';
                    $('#error_month').text(error_month);
                } else {
                    error_month = '';
                    $('#error_month').text(error_month);
                }

                if ($.trim($('#fin_year').val()).length == 0) {
                    error_fin_year = 'Financial Year is required';
                    $('#error_fin_year').text(error_fin_year);
                } else {
                    error_fin_year = '';
                    $('#error_fin_year').text(error_fin_year);
                }

                if (error_scheme_id != '' || error_month != '' || error_fin_year != '') {
                    return false;
                } else {
                    $('#submit_loader1').show();
                    var scheme_id = $('#scheme_id').val();
                    var month = $('#month').val();
                    var fin_year = $('#fin_year').val();
                    var monthName = getMonthName(month);
                    $('#month_name').text(monthName);
                    $.ajax({
                        type: 'post',
                        url: "{{ route('viewModalData') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            scheme_id: scheme_id,
                            month: month,
                            fin_year: fin_year
                        },
                        dataType: 'json',
                        success: function(data) {
                            $('#submit_loader1').hide();
                            $('#loadingDivModal').hide();
                            if (data.status == 1) {
                                $.alert({
                                    title: response.title,
                                    type: response.type,
                                    icon: response.icon,
                                    content: response.msg
                                });
                                $("html, body").animate({
                                    scrollTop: 0
                                }, "slow");
                            } else {
                                $("#scheme_code").text(data.dbt_scheme_code);
                                $("#fin_year_code").text(data.finYearCode);
                                $("#financial_year").text(data.fin_year);
                                $("#reporting_month").text(data.reporting_month);
                                $("#total_ben").text(data.total_ben);
                                $("#total_ben_digitized").text(data.total_ben_digitalized);
                                $("#ben_aadhar_seeded").text(data.ben_aadhar_seeded);
                                $("#mob_captured").text(data.mobile_captured);
                                $("#benefit_type").text(data.benefit_type);
                                $("#fund_transfer_cash").text(data.fund_transfer_cash);
                                $("#no_trans_cash_electronics").text(data.no_trans_cash_electronic);
                                $("#amt_trans_cash_electronics").text(data.amnt_trans_cash_electronic);
                                $("#trans_aadhar_seeded").text(data.trans_aadhar_seeded);
                                $("#no_de_dup").text(data.no_deduplicated);
                                $("#saving_amount").text(data.saving_amnt);
                                $("#remarks").text(data.remarks);
                                $("#total_ben_incremental").text(data.total_ben);
                                $("#ben_digitized_incremental").text(data.total_ben_digitalized);
                                $("#ben_aadhar_seeded_incremental").text(data.ben_aadhar_seeded);
                                $("#mob_captured_incremental").text(data.mobile_captured);
                                $('#modalUpdateAadhar').modal('show');
                            }
                        }
                    });
                }
            });

            $('#pushedToDbt').click(function() {
                $('#loadingDivModal').show();

                var month = $('#month').val();
                var scheme_id = $('#scheme_id').val();
                var fin_year = $('#fin_year').val();
                $.ajax({
                    type: 'post',
                    url: "{{ route('jbDataPushedToDbt') }}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        scheme_id: scheme_id,
                        month: month,
                        fin_year: fin_year
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#loadingDivModal').hide();
                        $('#modalUpdateAadhar').hide();
                        if (data.status == 2) {
                            $.alert({
                                title: data.title,
                                type: data.type,
                                icon: data.icon,
                                content: data.msg
                            });
                            $("html, body").animate({
                                scrollTop: 0
                            }, "slow");
                        } else {
                            $("#submitting").hide();
                            $("#verifyReject").show();
                            $('.ben_view_modal').modal('hide');
                            $('#approve_rejdiv').hide();
                            $.alert({
                                title: data.title,
                                type: data.type,
                                icon: data.icon,
                                content: data.msg
                            });
                            $("html, body").animate({
                                scrollTop: 0
                            }, "slow");
                        }
                    },
                    complete: function() {

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('.ben_view_body').removeClass('disabledcontent');
                        $('#loader_img_personal').hide();
                        $('#loadingDiv').hide();
                        $('.ben_view_button').removeAttr('disabled', true);
                        $('.ben_view_modal').modal('hide');
                        // ajax_error(jqXHR, textStatus, errorThrown);
                        /*$.alert({
                            title: 'Error!!',
                            type: 'red',
                            icon: 'fa fa-warning',
                            content: 'Something wrong while fetching the beneficiary data!!',
                        });*/
                    }
                });
            });

            $('#example').on('page.dt', function() {
                $('#approve_rejdiv').hide();
            });

            $('#example').on('length.dt', function(e, settings, len) {
                $("#check_all_btn").prop("checked", false);
            });
            

            $('#reset').click(function() {
                $('#scheme_id').val('').trigger('change');
                $('#month').val('').trigger('change');
                $('#fin_year').val('').trigger('change');                
            });
        });
    </script>
@stop
