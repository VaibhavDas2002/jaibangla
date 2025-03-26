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
           Beneficiary Status MIS Report
        </h1>
        <ol class="breadcrumb">
            <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span
                    class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
        </ol>
    </section>
    <section class="content">
        <div class="box box-default">
            <div class="box-body">
                <div id="loadingDi"></div>
                <div class="panel panel-default">
                    <div class="panel-heading" style="font-size: 14px; font-weight: bold; font-style: italic;"><span
                            id="panel-icon">Enter Filter Criteria</div>
                    <div class="panel-body" style="padding: 5px;">
                        <div class="row">
                            <div class="col-md-12">
                                @if (($message = Session::get('success')))
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
                                    <div class="col-md-12">
                                        <!-- Scheme Selection -->
                                        <div class="col-md-4">
                                            <label for="scheme_type" class="control-label">Scheme <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="scheme_type" id="scheme_type" required>
                                                <option value="">--Select Scheme--</option>
                                                @foreach ($schemes as $scheme)
                                                    <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger" id="error_scheme_type"></span>
                                        </div>

                                        @include('common-selection.index')
                                        <!-- Operation Type Selection -->
                                        <!-- <div class="form-group col-md-4">
                                            <label for="filter_type" class="control-label">Operation Type <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="filter_type" id="filter_type" required>
                                                <option value="">--All--</option>
                                                <option value="0">Incomplete Data</option>
                                                <option value="1">Duplicate Aadhar</option>
                                                <option value="2">No Aadhar</option>
                                                <option value="3">Duplicate Bank</option>
                                                <option value="4">Duplicate Mobile</option>
                                                <option value="5">No Mobile</option>
                                                <option value="6">Payment Failure</option>
                                                <option value="7">Name Validation Failed</option>
                                                <option value="8">Account Validation Failed</option>
                                            </select>
                                            <span id="error_filter_type" class="text-danger"></span>
                                        </div> -->
                                    </div>

                                    <!-- Include Common Selection -->

                                    <!-- Search Button -->
                                    <div class="text-center">
                                        <button class="btn btn-primary" name="submit_btn" id="submit_btn" type="button"
                                            disabled>
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>

                                    <div class="col-md-12" style="text-align: left; margin-top: 20px; display: none;" >
                                        <form action="{{route('blkUlb_mis_report_excel')}}" method="post">
                                            {{csrf_field()}}
                                            <input type="hidden" name="excel_scheme_id" id="excel_scheme_id" />
                                            <input type="hidden" name="excel_dist_code" id="excel_dist_code"
                                                value="{{$dist_code}}" />
                                            <input type="hidden" name="excel_filter_1" id="excel_filter_1" />
                                            <input type="hidden" name="excel_filter_2" id="excel_filter_2" />
                                            <button class="btn btn-success" name="excel_btn" id="excel_btn"
                                                type="submit" disabled>
                                                <i class="fa fa-file-excel-o"></i> Download List
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="res_div" style="display: none;">
                        <div class="panel panel-default">
                            <div class="panel-heading" id="panel_head"
                                style="font-size: 14px; font-weight: bold; font-style: italic;">Count of Beneficiaries
                            </div>
                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                <div class="table-responsive">
                                    <table id="example" class="table display" cellspacing="0" width="100%">
                                        <thead style="font-size: 12px;">
                                            <!-- Primary header row -->
                                            <tr>
                                                <th rowspan="2">Block/Sub-Division</th>
                                                <!-- This spans both header rows -->
                                                <th colspan="3" style="background-color: #cccccc;">Incomplete Details</th>
                                                <th colspan="3">No Aadhar Number</th>
                                                <th colspan="3" style="background-color: #cccccc;">Duplicate Aadhar Number</th>
                                                <th colspan="3">No Mobile Number</th>
                                                <th colspan="3" style="background-color: #cccccc;">Duplicate Mobile Number</th>
                                                <th colspan="3">Duplicate Bank Account Number</th>
                                                <th colspan="3" style="background-color: #cccccc;">Transaction Failure</th>
                                                <th colspan="3">Name Validation Failure</th>
                                                <th colspan="3" style="background-color: #cccccc;">Account Validation Failure</th>
                                            </tr>
                                            <!-- Secondary header row -->
                                            <tr>
                                                <th>Yet to Take Action</th>
                                                <th>Updated form Verifier</th>
                                                <th>Approved from Approver</th>
                                                <th style="background-color: #cccccc;">Yet to Take Action</th>
                                                <th style="background-color: #cccccc;">Updated form Verifier</th>
                                                <th style="background-color: #cccccc;">Approved from Approver</th>
                                                <th>Yet to Take Action</th>
                                                <th>Updated form Verifier</th>
                                                <th>Approved from Approver</th>
                                                <th style="background-color: #cccccc;">Yet to Take Action</th>
                                                <th style="background-color: #cccccc;">Updated form Verifier</th>
                                                <th style="background-color: #cccccc;">Approved from Approver</th>
                                                <th>Yet to Take Action</th>
                                                <th>Updated form Verifier</th>
                                                <th>Approved from Approver</th>
                                                <th style="background-color: #cccccc;" >Yet to Take Action</th>
                                                <th style="background-color: #cccccc;">Updated form Verifier</th>
                                                <th style="background-color: #cccccc;">Approved from Approver</th>
                                                <th>Yet to Take Action</th>
                                                <th>Updated form Verifier</th>
                                                <th>Approved from Approver</th>
                                                <th style="background-color: #cccccc;">Yet to Take Action</th>
                                                <th style="background-color: #cccccc;">Updated form Verifier</th>
                                                <th style="background-color: #cccccc;">Approved from Approver</th>
                                                <th>Yet to Take Action</th>
                                                <th>Updated form Verifier</th>
                                                <th>Approved from Approver</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 14px;">
                                            <tr>


                                            </tr>
                                        </tbody>
                                    </table>


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
<script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
    $(document).ready(function () {
        var interval = setInterval(function () {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);

        $('#loadingDi').hide();
        $('#submit_btn').removeAttr('disabled');

        $('#submit_btn').click(function () {
            if ($.trim($('#scheme_type').val()).length == 0) {
                error_scheme_type = 'Scheme name is required';
                $('#error_scheme_type').text(error_scheme_type);
            } else {
                error_scheme_type = '';
                $('#error_scheme_type').text(error_scheme_type);
            }

            // if ($.trim($('#filter_type').val()).length == 0) {
            //     error_filter_type = 'Filter is required';
            //     $('#error_filter_type').text(error_filter_type);
            // } else {
            //     error_filter_type = '';
            //     $('#error_filter_type').text(error_filter_type);
            // }



            if (error_scheme_type != '') {
                return false;
            } else {
                loadDatatable();
                $('.dt-buttons').hide();
            }
        });

        $('#scheme_type').change(function () {
            if ($(this).val() != '') {
                $('#excel_scheme_id').val($(this).val());
                $('#excel_btn').removeAttr('disabled');

            }
            else {
                $('#excel_scheme_id').val('');
            }
        });


        $('#filter_1').change(function () {
            if ($(this).val() != '') {
                $('#excel_filter_1').val($(this).val());
            }
            else {
                $('#excel_filter_1').val('');
            }
        });

        $('#filter_2').change(function () {
            if ($(this).val() != '') {
                $('#excel_filter_2').val($(this).val());
            }
            else {
                $('#excel_filter_2').val('');
            }
        });







        function loadDatatable() {
            $('#loadingDi').show();
            $('#res_div').show();

            let schemeText = $("#scheme_type option:selected").text();
            $('#panel_head').text('Count of Beneficiaries of Scheme: ' + schemeText);

            if ($.fn.DataTable.isDataTable('#example')) {
                $('#example').DataTable().destroy();
            }

            $('#example').DataTable({
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
                "ajax": {
                    url: "{{ route('ben_mis_report_post') }}",
                    type: "post",
                    data: function (d) {
                        d.scheme_id = $('#scheme_type').val();
                        // d.filter_type = $('#filter_type').val();
                        d.dist_code = $('#dist_code').val();
                        d.filter_1 = $('#rural_urban_code').val();
                        d.filter_2 = $('#blk_ulb_code').val();
                        d._token = "{{csrf_token()}}";
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('#loadingDi').hide();
                        $.alert({
                            title: 'Error!',
                            type: 'red',
                            icon: 'fa fa-warning',
                            content: 'Loading Error! Session timeout, please logout and login again.'
                        });
                    }
                },
                "initComplete": function () {
                    $('#loadingDi').hide();
                },
                "columns": [
                    { "data": "blkUlb_name" },
                    { "data": "incomplete_data_p" },
                    { "data": "incomplete_data_v" },
                    { "data": "incomplete_data_a" },
                    { "data": "no_aadhar_p" },
                    { "data": "no_aadhar_v" },
                    { "data": "no_aadhar_a" },
                    { "data": "dup_aadhar_p" },
                    { "data": "dup_aadhar_v" },
                    { "data": "dup_aadhar_a" },
                    { "data": "no_mobile_p" },
                    { "data": "no_mobile_v" },
                    { "data": "no_mobile_a" },
                    { "data": "dup_mobile_p" },
                    { "data": "dup_mobile_v" },
                    { "data": "dup_mobile_a" },
                    { "data": "dup_bank_p" },
                    { "data": "dup_bank_v" },
                    { "data": "dup_bank_a" },
                    { "data": "bank_failure_p" },
                    { "data": "bank_failure_v" },
                    { "data": "bank_failure_a" },
                    { "data": "name_failure_p" },
                    { "data": "name_failure_v" },
                    { "data": "name_failure_a" },
                    { "data": "ac_failure_p" },
                    { "data": "ac_failure_v" },
                    { "data": "ac_failure_a" }
                ]

            });
        }
    });
</script>