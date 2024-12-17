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
                Pending Lot Beneficiary List
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
                                id="panel-icon">Enter Filter Criteria</div>
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
                                            <div class="col-md-6">
                                                <label class=" control-label">Scheme <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="scheme_id" id='scheme_id' required
                                                    onchange="lotType(this.value)">
                                                    <option value="">--Select Scheme--</option>
                                                    @foreach ($schemes as $scheme)
                                                        <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="error_scheme_id"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <label class=" control-label">Select Lot Type<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="lot_type" id='lot_type' required>
                                                    {{-- <option value="">--Select Lot Type--</option> --}}
                                                    
                                                </select>
                                                <span class="text-danger" id="error_lot_type"></span>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <center>
                                            <div>
                                                <button class="btn btn-primary" name="submit_btn" id="submit_btn"
                                                    type="button" disabled><i class="fa fa-search"></i>
                                                    Search</button>&nbsp;
                                                <button class="btn btn-success" name="excel_btn" id="excel_btn"
                                                    type="button"><i class="fa fa-file-excel-o"></i> Export To
                                                    Excel</button>
                                            </div>
                                        </center>
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
                                style="font-size: 15px; font-weight: bold; font-style: italic;">List of Beneficiary</div>
                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped table-bordered" cellspacing="0"
                                        width="100%" style="font-size: 12px;">
                                        <thead>
                                            <th>Application ID</th>
                                            <th>Name</th>
                                            <th>District</th>
                                            <th>Block/Municipality</th>
                                            <th>Created Date</th>
                                            <th>Account No.</th>
                                            <th>Bank IFSC</th>
                                            <th>Payment Count</th>
                                            <th>Last Payment Date</th>
                                            <th>Lot Month-Year</th>
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
        <!-- /.content -->
    </div>
@endsection
<script src="{{ asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script>
    $(document).ready(function() {
        // Live Clock
        var interval = setInterval(function() {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);
        $('#loadingDiv').hide();
        $('#submit_btn').removeAttr('disabled');
        $('#reset_btn').removeAttr('disabled');
        $('#search_for').change(function() {
            var search_for = $(this).val();
            if (search_for == 'dup_ration_card' || search_for == 'no_ration_card') {
                $("#scheme_id option").each(function(i) {
                    if ($(this).val() == 1 || $(this).val() == 3 || $(this).val() == 19) {
                        $("#scheme_id option[value='" + $(this).val() + "']").attr('disabled',
                            false);
                    } else {
                        $("#scheme_id option[value='" + $(this).val() + "']").attr('disabled',
                            true);
                    }

                });
            } else {
                $("#scheme_id option").each(function(i) {

                    $("#scheme_id option[value='" + $(this).val() + "']").attr('disabled',
                        false);


                });
            }
        })
        // Master drop down 
        // End Master drop down
        var error_scheme_id = '';
        var error_lot_type = '';
        // var error_district = '';
        $('#submit_btn').click(function() {
            if ($.trim($('#scheme_id').val()).length == 0) {
                error_scheme_id = 'Scheme name is required';
                $('#error_scheme_id').text(error_scheme_id);
            } else {
                error_scheme_id = '';
                $('#error_scheme_id').text(error_scheme_id);
            }
            if ($.trim($('#lot_type').val()).length == 0) {
                error_lot_type = 'Lot Type is required';
                $('#error_lot_type').text(error_lot_type);
            } else {
                error_lot_type = '';
                $('#error_lot_type').text(error_lot_type);
            }
            if (error_scheme_id != '' || error_lot_type != '') {
                return false;
            } else {
                $('#loadingDiv').show();
                $('#search_details').show();
                // $(':input[type="button"]').prop('disabled', false);

                var lot_type = $('#lot_type').val();
                var scheme_code = $('#scheme_id').val();
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
                        url: "{{ url('lotTypeWiseBeneficiary') }}",
                        type: "post",
                        data: function(d) {
                            // d.scheme_code = scheme_code,
                            d.lot_type = $('#lot_type').val(),
                                d.scheme_code = $('#scheme_id').val(),
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
                            "data": "id"
                        },
                        {
                            "data": "fullname"
                        },
                        {
                            "data": "district_name"
                        },
                        {
                            "data": "block_ulb_name"
                        },
                        {
                            "data": "created_at"
                        },
                        {
                            "data": "bank_code"
                        },
                        {
                            "data": "bank_ifsc"
                        },
                        {
                            "data": "payment_count"
                        },
                        {
                            "data": "last_paid_yymm"
                        },
                        {
                            "data": "lot_month_year"
                        },
                    ],
                    "buttons": [{
                            extend: 'pdf',
                            footer: true,
                            pageSize: 'A4',
                            //orientation: 'landscape',
                            pageMargins: [40, 60, 40, 60],
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7],

                            }
                        },
                        //    {
                        //        extend: 'excel',
                        //        footer: true,
                        //        pageSize:'A4',
                        //        //orientation: 'landscape',
                        //        pageMargins: [ 40, 60, 40, 60 ],
                        //        exportOptions: {
                        //             columns: [0,1,2,3,4,5,6],
                        //             stripHtml: false,
                        //         }
                        //     },
                        // 'pdf'
                    ],
                });
            }
        });
        // Export Excel
        $('#excel_btn').click(function() {
            var error_scheme_id = '';
            var error_lot_type = '';
            // var error_district = '';
            if ($.trim($('#scheme_id').val()).length == 0) {
                error_scheme_id = 'Scheme name is required';
                $('#error_scheme_id').text(error_scheme_id);
            } else {
                error_scheme_id = '';
                $('#error_scheme_id').text(error_scheme_id);
            }
            if ($.trim($('#lot_type').val()).length == 0) {
                error_lot_type = 'Lot Type is required';
                $('#error_lot_type').text(error_lot_type);
            } else {
                error_lot_type = '';
                $('#error_lot_type').text(error_lot_type);
            }
            if (error_scheme_id != '' || error_lot_type != '') {
                return false;
            } else {
                var lot_type = $('#lot_type').val();
                var scheme_code = $('#scheme_id').val();
                var token = "{{ csrf_token() }}";
                var data = {
                    '_token': token,
                    scheme_id: scheme_code,
                    lot_type: lot_type
                };
                redirectPost('lotWiseBenExcel', data);
            }
        });
    });

    function lotType(value) {
        // alert(value);
        $('.loadingDivModal').show();
        $.ajax({
            type: 'POST',
            url: "{{ route('schemeWiseLotType') }}",
            data: {
                scheme_id: value,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log(response);
                $('.loadingDivModal').hide();
                if (response.status == 1) {
                    // alert("If");
                    $.alert({
                        title: response.title,
                        type: response.type,
                        icon: response.icon,
                        content: response.msg
                    });
                } else {
                    // alert("Else");
                    $('#lot_type').html('<option value="">-- Select Lot Type --</option>');
                    $.each(response, function (key, value) {
                        $("#lot_type").append('<option value="' + value
                            .id + '">' + value.lot_type + '</option>');
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('.loadingDivModal').hide();
                ajax_error(jqXHR, textStatus, errorThrown);
            }
        });
    }

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
