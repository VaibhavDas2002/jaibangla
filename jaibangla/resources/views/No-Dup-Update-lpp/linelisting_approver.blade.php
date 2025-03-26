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
                Update Incomplete Beneficiary Data
            </h1>

        </section>
        <section class="content">
            <div class="box box-default">
                <div class="box-body">
                    <div class="row">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">×</button>
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                        @if (count($errors) > 0)
                            <div class="alert alert-danger alert-block">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li><strong>{{ $error }}</strong></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (($message = Session::get('error')))
                            <div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                    </div>
                    <input type="hidden" name="dist_code" id="dist_code" value="{{ $dist_code }}" class="js-district_1">
                    <div class="panel panel-default">
                        <div class="panel-heading">Pending Beneficiary data yet to be Approved</div>
                        <div class="panel-body" style="padding: 5px;">


                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label class="required-field">Select Scheme</label>
                                    <select class="form-control" name="scheme_id" id="scheme_id">
                                        <option value="">--Select--</option>
                                        @foreach ($schemes as $scheme)
                                            <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="error_scheme_id" class="text-danger"></span>
                                </div>

                                <div class="form-group col-md-3">
                                    <label class="required-field">Operation Type</label>
                                    <select class="form-control" name="filter_type" id="filter_type">
                                        <option value="">--Select--</option>
                                        @foreach ($incomplete_types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="error_filter_type" class="text-danger"></span>
                                </div>
                                <div class="form-group col-md-3" id="failed_type_div" style="display: none;">
                                    <label class="required-field">Failed Type</label>
                                    <select class="form-control" name="failed_type" id="failed_type">
                                        <option value="">--Select--</option>
                                        <option value="3">SBI</option>
                                        <option value="4">RBI</option>
                                        <option value="5">IFMS</option>
                                    </select>
                                    <span id="error_failed_type" class="text-danger"></span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="control-label">Rural/Urban</label>
                                    <select name="filter_1" id="filter_1" class="form-control">
                                        <option value="">-----Select----</option>
                                        @foreach ($levels as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="control-label" id="blk_sub_txt">Block/Sub Division</label>
                                    <select name="filter_2" id="filter_2" class="form-control">
                                        <option value="">-----Select----</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row text-center">
                                <div class="col-md-12" style="margin-top: 25px;">
                                    <button type="button" name="filter" id="filter" class="btn btn-success">
                                        <i class="fa fa-search"></i> Search
                                    </button>&nbsp;&nbsp;
                                    <button type="button" name="reset" id="reset" class="btn btn-warning">
                                        <i class="fa fa-refresh"></i> Reset
                                    </button>
                                </div>
                            </div>

                            <hr />

                            <div class="row">

                                <div class="form-group col-md-offset-4 col-md-3" id="approve_rejdiv" style="display: none;">
                                    <button type="button" name="bulk_approve" class="btn btn-info btn-lg" id="bulk_approve"
                                        value="approve">
                                        Approve
                                    </button>
                                </div>
                                <form action="{{ route('TotalCountExcel') }}" method="post" id="downloadExcel">
                                    {{ csrf_field() }}
                                    <input id="excel_scheme_id" name="excel_scheme_id" type="hidden">
                                    <input id="excel_filter_type" name="excel_filter_type" type="hidden">
                                    <input id="excel_failed_type" name="excel_failed_type" type="hidden">
                                    <input type="hidden" name="submit" id="submit_button" value="">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-primary" name="submit" id="excel_another_total_btn"
                                            value="excel_another_total_btn" type="submit" disabled>
                                            <i class="fa fa-file-excel-o"></i>Download Excel Report
                                        </button>
                                    </div>
                                </form>



                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default" id="res_div" style="display: none;">
                        <div class="panel-heading" id="panel_head">List of New Edited Banking Information</div>
                        <div class="panel-body" style="padding: 5px; font-size: 14px;">
                            <div class="table-responsive">
                                <table id="example" class="display" cellspacing="0" width="100%">
                                    <thead style="font-size: 12px;">
                                        <th>Sl No</th>
                                        <th>Beneficiary ID</th>
                                        <th>Applicant Name</th>
                                        <th>Block/Muncipality</th>
                                        <th>GP/Ward</th>
                                        <th>Incomplete Status</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody style="font-size: 14px;"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
@section('script')
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script src="js/jquery.min.js" type="text/javascript"></script>
<script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    $(document).ready(function () {
        $('.sidebar-menu li').removeClass('active');
        $('.sidebar-menu #bankTrFailed').addClass("active");
        $('.sidebar-menu #accValTrFailedVerified').addClass("active");
        $('#opreation_type').val('A');
        $("#verifyReject").html("Approve");
        $('#div_rejection').hide();
        var dataTable = "";
        if ($.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable().destroy();
        }


        $('#scheme_id').change(function () {
            if ($(this).val() !== '') {
                $('#excel_scheme_based_btn').removeAttr('disabled');
                $('#excel_scheme_id').val($(this).val());
            } else {
                $('#excel_scheme_based_btn').attr('disabled',
                    'disabled'); // Re-disable the button if no value is selected
            }
        });

        $('#filter_type').change(function () {
            if ($(this).val() !== '') {
                $('#excel_another_total_btn').removeAttr('disabled');
                $('#excel_filter_type').val($(this).val())
            } else {
                $('#excel_another_total_btn').attr('disabled',
                    'disabled'); // Re-disable the button if no value is selected
            }
        });


        $('#failed_type').change(function () {
            if ($(this).val() !== '') {
                $('#excel_another_total_btn').removeAttr('disabled');
                $('#excel_failed_type').val($(this).val())
            } else {
                $('#excel_another_total_btn').attr('disabled',
                    'disabled'); // Re-disable the button if no value is selected
            }
        });








        $('#filter_type').change(function () {
            // alert($('#filter_type').val());
            if ($('#filter_type').val() === '10') { // Ensure comparison to string
                $('#failed_type_div').show();
            } else {
                $('#failed_type_div').hide();
            }
        });




        $('#example tbody').empty();
        var dataTable = $('#example').DataTable({
            dom: 'Blfrtip',
            "scrollX": true,
            "paging": true,
            "searchable": true,
            "ordering": false,
            "bFilter": true,
            "bInfo": true,
            "pageLength": 10,
            'lengthMenu': [
                [10],
                [10]
            ],
            "serverSide": true,
            "processing": true,
            "bRetrieve": true,
            "oLanguage": {
                "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
            },
            "ajax": {
                url: "{{ route('getNoDupLPPList') }}",
                type: "post",
                data: function (d) {
                    d.filter_1 = $('#filter_1').val(),
                        d.filter_2 = $('#filter_2').val(),
                        d.scheme_id = $('#scheme_id').val(),
                        d.filter_type = $('#filter_type').val(),
                        d.failed_type_id = $('#failed_type').val(),
                        d._token = "{{ csrf_token() }}"
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#loadingDiv').hide();
                    $('.preloader1').hide();
                    ajax_error(jqXHR, textStatus, errorThrown);
                }
            },
            "initComplete": function () {
                $('#loadingDiv').hide();
                //console.log('Data rendered successfully');
            },
            columns: [{
                "data": "DT_RowIndex"
            },
            {
                "data": "id"
            },
            {
                "data": "name"
            },
            {
                "data": "block_ulb_name"
            },
            {
                "data": "gp_ward_name"
            },
            {
                "data": "status"
            },
            {
                "data": "view"
            },
            ],

            "buttons": [{
                extend: 'pdf',
                footer: true,
                pageSize: 'A4',
                //orientation: 'landscape',
                pageMargins: [40, 60, 40, 60],
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6],

                }
            },
            {
                extend: 'excel',
                footer: true,
                pageSize: 'A4',
                //orientation: 'landscape',
                pageMargins: [40, 60, 40, 60],
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6],
                    stripHtml: false,
                }
            },
                //'pdf','excel','print'
            ],
        });

        $('#filter').click(function () {
            // alert('Hi');
            if ($.trim($('#scheme_id').val()).length == 0) {
                error_scheme_id = 'Scheme is required';
                $('#error_scheme_id').text(error_scheme_id);
            } else {
                error_scheme_id = '';
                $('#error_scheme_id').text(error_scheme_id);
            }

            if ($.trim($('#filter_type').val()).length == 0) {
                error_filter_type = 'Operation Type is required';
                $('#error_filter_type').text(error_filter_type);
            } else {
                error_filter_type = '';
                $('#error_filter_type').text(error_filter_type);
            }

            if ($.trim($('#failed_type').val()).length == 0) {
                error_failed_type = 'Failed Type is required';
                $('#error_failed_type').text(error_failed_type);
            } else {
                error_failed_type = '';
                $('#error_failed_type').text(error_failed_type);
            }

            if (error_scheme_id != '' || error_filter_type != '') {
                return false;
            } else if ($('#filter_type').val() == 6) {
                if (error_scheme_id != '' || error_filter_type != '' || error_failed_type != '') {
                    return false;
                } else {
                    $('#loadingDiv').show();
                    $('#res_div').show();
                    var msg = 'Beneficiary Details';
                    $('#panel_head').text(msg);
                    dataTable.ajax.reload();
                }
            } else {
                $('#loadingDiv').show();
                $('#res_div').show();
                var msg = 'Beneficiary Details';
                $('#panel_head').text(msg);
                dataTable.ajax.reload();
            }
        });

        $('#example').on('page.dt', function () {
            $('#approve_rejdiv').hide();
        });

        $('#example').on('length.dt', function (e, settings, len) {
            $("#check_all_btn").prop("checked", false);
        });


        $('#reset').click(function () {
            $('#filter_1').val('').trigger('change');
            $('#filter_2').val('').trigger('change');
            $('#block_ulb_code').val('').trigger('change');
            $('#gp_ward_code').val('').trigger('change');
            $('#failed_type').val('').trigger('change');
            $('#pay_mode').val('').trigger('change');
            dataTable.ajax.reload();
        });
        // --------------- Filter Section End-------------------- //

        // ------------ Master DropDown Section Start-------------------- //
        $('#filter_1').change(function () {
            var filter_1 = $(this).val();
            // alert(filter_1);
            $('#filter_2').html('<option value="">--All --</option>');
            $('#block_ulb_code').html('<option value="">--All --</option>');
            select_district_code = $('#dist_code').val();
            // alert(select_district_code);

            var htmlOption = '<option value="">--All--</option>';
            $('#gp_ward_code').html('<option value="">--All --</option>');
            if (filter_1 == 1) {
                $.each(subDistricts, function (key, value) {
                    if ((value.district_code == select_district_code)) {
                        htmlOption += '<option value="' + value.id + '">' + value.text +
                            '</option>';
                    }
                });
                $("#blk_sub_txt").text('Subdivision');
                $("#gp_ward_txt").text('Ward');
                $("#municipality_div").show();
                $("#gp_ward_div").show();
            } else if (filter_1 == 2) {
                // console.log(filter_1);
                $.each(blocks, function (key, value) {
                    if ((value.district_code == select_district_code)) {
                        htmlOption += '<option value="' + value.id + '">' + value.text +
                            '</option>';
                    }
                });
                $("#blk_sub_txt").text('Block');
                $("#gp_ward_txt").text('GP');
                $("#municipality_div").hide();
                $("#gp_ward_div").show();
            } else {
                $("#blk_sub_txt").text('Block/Subdivision');
                $("#gp_ward_txt").text('GP/Ward');
                $("#municipality_div").hide();
            }
            $('#filter_2').html(htmlOption);

        });
        $('#filter_2').change(function () {
            var rural_urbanid = $('#filter_1').val();
            $('#gp_ward_code').html('<option value="">--All --</option>');
            if (rural_urbanid == 1) {
                var sub_district_code = $(this).val();
                if (sub_district_code != '') {
                    $('#block_ulb_code').html('<option value="">--All --</option>');
                    select_district_code = $('#dist_code').val();
                    var htmlOption = '<option value="">--All--</option>';
                    $.each(ulbs, function (key, value) {
                        if ((value.district_code == select_district_code) && (value
                            .sub_district_code == sub_district_code)) {
                            htmlOption += '<option value="' + value.id + '">' + value.text +
                                '</option>';
                        }
                    });
                    $('#block_ulb_code').html(htmlOption);
                } else {
                    $('#block_ulb_code').html('<option value="">--All --</option>');
                }
            } else if (rural_urbanid == 2) {
                $('#muncid').html('<option value="">--All --</option>');
                $("#municipality_div").hide();
                var block_code = $(this).val();
                select_district_code = $('#dist_code').val();
                var htmlOption = '<option value="">--All--</option>';
                $.each(gps, function (key, value) {
                    if ((value.district_code == select_district_code) && (value.block_code ==
                        block_code)) {
                        htmlOption += '<option value="' + value.id + '">' + value.text +
                            '</option>';
                    }
                });
                $('#gp_ward_code').html(htmlOption);
                $("#gp_ward_div").show();
            } else {
                $('#block_ulb_code').html('<option value="">--All --</option>');
            }
        });
        $('#block_ulb_code').change(function () {
            var muncid = $(this).val();
            var district = $("#dist_code").val();
            var urban_code = $("#filter_1").val();
            if (district == '') {
                $('#filter_1').val('');
                $('#filter_2').html('<option value="">--All --</option>');
                $('#block_ulb_code').html('<option value="">--All --</option>');
            }
            if (urban_code == '') {
                // alert('Please Select Rural/Urban First');
                $('#filter_2').html('<option value="">--All --</option>');
                $('#block_ulb_code').html('<option value="">--All --</option>');
                $("#filter_1").focus();
            }
            if (muncid != '') {
                var rural_urbanid = $('#filter_1').val();
                if (rural_urbanid == 1) {
                    $('#gp_ward_code').html('<option value="">--All --</option>');
                    var htmlOption = '<option value="">--All--</option>';
                    $.each(ulb_wards, function (key, value) {
                        if (value.urban_body_code == muncid) {
                            htmlOption += '<option value="' + value.id + '">' + value.text +
                                '</option>';
                        }
                    });
                    $('#gp_ward_code').html(htmlOption);
                    //console.log(htmlOption);
                } else {
                    $('#gp_ward_code').html('<option value="">--All --</option>');
                    $("#gp_ward_div").hide();
                }
            } else {
                $('#gp_ward_code').html('<option value="">--All --</option>');
            }
        });
    });

</script>
@stop