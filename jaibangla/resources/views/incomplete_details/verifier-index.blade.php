<style>
        #loadingDi{
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
                Verify/Update Beneficiary Incomplete Data
            </h1>
            <ol class="breadcrumb">
                <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span
                        class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
            </ol>
        </section>
        <section class="content">
            <div class="box box-primary">
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
                                    @if (($message = Session::get('error')))
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-4">
                                                <label class=" control-label">Scheme <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="scheme_type" id='scheme_type' required>
                                                    <option value="">--Select Scheme--</option>
                                                    @foreach ($schemes as $scheme)
                                                        <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="error_scheme_type"></span>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label class="required-field">Operation Type <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="filter_type" id="filter_type">
                                                    <option value="">--Select--</option>
                                                    @foreach ($incomplete_types as $type)
                                                        <option value="{{$type->id}}">{{$type->name}}</option>
                                                    @endforeach
                                                </select>
                                                <span id="error_filter_type" class="text-danger"></span>
                                            </div>

                                            <div class="form-group col-md-3" id="failed_type_div" style="display:none;">
                                                <label class="required-field">Failed Type <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="failed_type" id="failed_type">
                                                    <option value="">--Select--</option>
                                                    <option value="3">SBI</option>
                                                    <option value="4">RBI</option>
                                                    <option value="5">IFMS</option>
                                                </select>
                                                <span id="error_failed_type" class="text-danger"></span>
                                            </div>

                                            <input type="hidden" name="dist_code" value="{{ $dist_code }}"
                                                class="client-js-district" hidden>
                                            <input type="hidden" name="is_urban" value="{{ $is_urban }}"
                                                class="client-js-urban">


                                            @if($is_urban == 1)
                                                <div class="form-group col-md-3">
                                                    <label class=" control-label">Select Filter Criteria :Municipality</label>
                                                    <select name="munc" id="munc"
                                                        class="form-control select2 full-width js-municipality client-js-localbody">
                                                        <option value="">-----Select----</option>
                                                        @foreach ($urban_bodys as $urban_body)
                                                            <option value="{{$urban_body->urban_body_code}}">
                                                                {{$urban_body->urban_body_name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label class=" control-label">Select Filter Criteria :Wards</label>
                                                    <select name="gp_ward" id="gp_ward" class="form-control  client-js-gpward">
                                                        <option value="">--Select --</option>
                                                    </select>
                                                </div>


                                            @endif
                                            @if($is_urban == 2)
                                                <input type="hidden" name="munc" id="munc" value="" />
                                                <div class="form-group col-md-3">
                                                    <label class=" control-label">Select Filter Criteria : Gram
                                                        Panchayat</label>
                                                    <select name="gp_ward" id="gp_ward" class="form-control select2 ">
                                                        <option value="">-----Select----</option>
                                                        @foreach ($gps as $gp)
                                                            <option value="{{$gp->gram_panchyat_code}}"> {{$gp->gram_panchyat_name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                            @endif
                                        </div>




                                    </div>
                                    <div style="text-align: center;">
                                        <button class="btn btn-primary" name="submit_btn" id="submit_btn" type="button"
                                            disabled><i class="fa fa-search"></i> Search</button>&nbsp;
                                        <button class="btn btn-default" name="reset_btn" id="reset_btn" type="button"
                                            disabled><i class="fa fa-refresh"></i> Reset</button>

                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="res_div" style="display: none;">
                        <div class="panel panel-default">
                            <div class="panel-heading" id="panel_head"
                                style="font-size: 14px; font-weight: bold; font-style: italic;">
                                List of Beneficiary</div>
                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                <div class="table-responsive">
                                    <table id="example" class="table display" cellspacing="0" width="100%">
                                        <thead style="font-size: 12px;">
                                            <th>Application ID</th>
                                            <th>Applicant Name</th>
                                            <th>Block/Municipality</th>
                                            <th>GP/Ward</th>
                                            <th>Aadhar No</th>
                                            <th>Bank A/C</th>
                                            <th>Bank IFSC</th>
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
            </div>
        </section>
    </div>
@endsection

<script src="{{ asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script src="{{ URL::asset('js/site-client-v2.js') }}"></script>
<script>
    $('.select2').select2();
</script>
<script src="{{ asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script>
    $(document).ready(function () {
        // Update date and time every second
        setInterval(function () {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 1000);

        // Hide elements initially
        $('#loadingDi').hide();
        $('#failed_type_div').hide();
        $('#submit_btn').prop('disabled', false);
        $('#reset_btn').prop('disabled', false);
        $("#filter_type option[value='13']").remove();
        // Show/hide 'failed_type_div' based on 'filter_type' selection
        $('#filter_type').on('change', function () {
            if ($(this).val() === '10') {
                $('#failed_type_div').show();
            } else {
                $('#failed_type_div').hide();
            }
        });

        // Ensure the correct state on page load
        if ($('#filter_type').val() === '10') {
            $('#failed_type_div').show();
        } else {
            $('#failed_type_div').hide();
        }

        var error_scheme_type = '';
        var error_filter_type = '';
        var error_failed_type = '';
        $('#submit_btn').click(function () {
            var filter_type = $.trim($('#filter_type').val());
            if (filter_type == 10) {
                if ($.trim($('#failed_type').val()).length == 0) {
                    error_failed_type = 'Failed type is required';
                    $('#error_failed_type').text(error_failed_type);
                    $('#failed_type').addClass('has-error');
                } else {
                    error_failed_type = '';
                    $('#error_failed_type').text(error_failed_type);
                    $('#failed_type').removeClass('has-error');
                }
            }

            if ($.trim($('#scheme_type').val()).length == 0) {
                error_scheme_type = 'Scheme name is required';
                $('#error_scheme_type').text(error_scheme_type);
            } else {
                error_scheme_type = '';
                $('#error_scheme_type').text(error_scheme_type);
            }

            if ($.trim($('#filter_type').val()).length == 0) {
                error_filter_type = 'Filter is required';
                $('#error_filter_type').text(error_filter_type);
            } else {
                error_filter_type = '';
                $('#error_filter_type').text(error_filter_type);
            }
            if (filter_type == 10) {
                if (error_scheme_type != '' || error_filter_type != '' || error_failed_type != '') {
                    return false;
                } else {
                    loadDatatable();
                }
            } else {
                if (error_scheme_type != '' || error_filter_type != '') {
                    return false;
                } else {
                    loadDatatable();
                }
            }

        });

        $('#reset_btn').click(function (){
            window.location.reload();
        });
    });

    function loadDatatable() {
        $('#loadingDi').show();
        $('#res_div').show();
        var msg = 'List of Beneficiaries of Scheme : ' + $("#scheme_type option:selected").text();
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
            "pageLength": 20,
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
                url: "{{ route('getNoDupListAjax') }}",
                type: "POST",
                data: function (d) {
                    d.scheme_id = $('#scheme_type').val(),
                        d.filter_type = $('#filter_type').val(),
                        d.is_urban = $('#rural_urban_code').val(),
                        d.blk_ulb_code = $('#blk_ulb_code').val(),
                        d.failed_type = $('#failed_type').val(),
                        d.pay_validated = $('#failed_type').val(),
                        d._token = "{{csrf_token()}}"
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#loadingDi').hide();
                    $('.preloader1').hide();
                    ajax_error(jqXHR, textStatus, errorThrown);
                    $.alert({
                      title: 'Error!!',
                      type: 'red',
                      icon: 'fa fa-warning',
                      content: 'Loading Error! Session timeout, please logout and login again.'
                    });
                }
            },
            "initComplete": function () {
                $('#loadingDi').hide();
                //console.log('Data rendered successfully');
            },
            "columns": [
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
                    "data": "aadhar_no"
                },
                {
                    "data": "bank_code"
                },
                {
                    "data": "bank_ifsc"
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
                orientation: 'landscape',
                pageMargins: [40, 60, 40, 60],
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5,6,7],

                }
            },
            {
                extend: 'excel',
                footer: true,
                pageSize: 'A4',
                orientation: 'landscape',
                pageMargins: [40, 60, 40, 60],
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5,6,7],
                    stripHtml: false,
                }
            },
                'print'
            ],
        });
    }
</script>