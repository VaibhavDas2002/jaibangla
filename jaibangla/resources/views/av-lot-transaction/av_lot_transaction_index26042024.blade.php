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
                Account Validation Lot Transaction
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
                                    <div class="col-md-3">
                                        <label class=" control-label">Scheme<span class="text-danger">*</span></label>
                                        <select class="form-control" name="scheme_id" id="scheme_id" required>
                                            <option value="">-- Select Scheme --</option>
                                            @foreach ($schemes as $scheme)
                                                <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger" id="error_search_for"></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class=" control-label">Status</label>
                                        <select class="form-control" name="status" id="status" required>
                                            <option value="">---- Status ----</option>
                                            <option value="0">Lot Created</option>
                                            <option value="1">File Generation Pending</option>
                                            <option value="2">Lot Acknowledgement Pending</option>
                                            <option value="3">Lot Response Pending</option>
                                        </select>
                                        <span class="text-danger" id="error_search_for"></span>
                                    </div>
                                    <div class="col-md-3" style="margin-top: 25px;">
                                        <button class="btn btn-primary" name="submit_btn" id="submit_btn" type="button"
                                            disabled><i class="fa fa-search"></i>
                                            Search</button>
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
                                style="font-size: 15px; font-weight: bold; font-style: italic;">List of Validation Lot</div>
                            <div class="panel-body" style="padding: 5px;">
                                <div class="table-responsive">
                                    <table id="example" class="table display table-condensed" cellspacing="0"
                                        width="100%" style="font-size: 14px;">
                                        <thead style="font-size: 12px;">
                                            <th>Sl No.</th>
                                            <th>File Name</th>
                                            <th>Status</th>
                                            <th>No. of Beneficiary in this File</th>
                                            <th>Sucess</th>
                                            <th>Failed</th>
                                            <th>Acknowledgement Status</th>
                                            <th>Action</th>
                                        </thead>
                                        <tbody></tbody>
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

        var error_scheme_id = '';
        var error_search_for = '';
        var error_district = '';
        $('#submit_btn').click(function() {
            // alert('H');
            // var scheme_code = $('#status').val();
            // alert(scheme_code);
            if ($.trim($('#scheme_id').val()).length == 0) {
                error_scheme_id = 'Scheme name is required';
                $('#error_scheme_id').text(error_scheme_id);
            } else {
                error_scheme_id = '';
                $('#error_scheme_id').text(error_scheme_id);
            }
            if (error_scheme_id != '') {
                return false;
            } else {
                loadDatatable();
            }
        });
        // Export Excel
        $('#excel_btn').click(function() {
            var error_scheme_id = '';
            var error_search_for = '';
            var error_district = '';
            if ($.trim($('#scheme_id').val()).length == 0) {
                error_scheme_id = 'Scheme name is required';
                $('#error_scheme_id').text(error_scheme_id);
            } else {
                error_scheme_id = '';
                $('#error_scheme_id').text(error_scheme_id);
            }
            if (error_scheme_id != '') {
                return false;
            } else {
                // var search_option = $('#search_for').val();
                var scheme_code = $('#scheme_id').val();
                var district = $('#district').val();
                var urban_code = $('#urban_code').val();
                var block = $('#block').val();
                var gp_ward = $('#gp_ward').val();
                var muncid = $('#muncid').val();
                var token = "{{ csrf_token() }}";
                var data = {
                    '_token': token,
                    scheme_id: scheme_code,
                    district: district,
                    urban_code: urban_code,
                    block: block,
                    gp_ward: gp_ward,
                    $muncid: muncid
                };
                redirectPost('generateExcel', data);
            }
        });

        // Generate File
        $(document).on('click', '.av_file_generate', function() {
            var val = $(this).val();
            var array = val.split("_");
            var file_name = array[0];
            var scheme_id = array[1];
            var dataSrc = {
                scheme_id: scheme_id,
                file_name: file_name,
                _token: '{{ csrf_token() }}'
            };
            var confirm_msg = '<strong>Are you are sure want to generate validation file ?</strong>';
            var url = "{{ route('avfileGeneration') }}";
            common_av_transaction(confirm_msg, url, dataSrc);
        });

        // File Pushed
        $(document).on('click', '.av_push_file_sbi', function() {
            var val = $(this).val();
            var array = val.split("_");
            var file_name = array[0];
            var scheme_id = array[1];
            var dataSrc = {
                scheme_id: scheme_id,
                file_name: file_name,
                _token: '{{ csrf_token() }}'
            };
            var confirm_msg = '<strong>Are you are sure want to push to SBI ?</strong>';
            var url = "{{ route('pushedSBIAccountValidationFile') }}";
            common_av_transaction(confirm_msg, url, dataSrc);
        });

        // Receive Acknowledgement
        $(document).on('click', '.av_ack_file_sbi', function() {
            var val = $(this).val();
            var array = val.split("_");
            var file_name = array[0];
            var scheme_id = array[1];
            var dataSrc = {
                scheme_id: scheme_id,
                file_name: file_name,
                _token: '{{ csrf_token() }}'
            };
            var confirm_msg = '<strong>Are you are sure want to receive acknowledgement from SBI ?</strong>';
            var url = "{{ route('receiveAckSBIAccountValidationFile') }}";
            common_av_transaction(confirm_msg, url, dataSrc);
        });
    });

    function common_av_transaction(confirm_msg, url, dataSrc) {
        $.confirm({
            title: 'Confirm!',
            type: 'orange',
            icon: 'fa fa-warning',
            content: confirm_msg,
            buttons: {
                Confirm: {
                    text: 'Confirm',
                    btnClass: 'btn-green',
                    keys: ['enter', 'shift'],
                    action: function() {
                        $('#loadingDiv').show();
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: dataSrc,
                            success: function(response) {
                                $('#loadingDiv').hide();
                                $.alert({
                                    title: response.title,
                                    type: response.type,
                                    icon: response.icon,
                                    content: response.msg,
                                    buttons: {
                                        Ok: {
                                            text: 'Ok',
                                            btnClass: 'btn-green',
                                            keys: ['enter',
                                                'shift'
                                            ],
                                            action: function() {
                                                loadDatatable();
                                            }

                                        }

                                    }
                                });
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                $('#loadingDiv').hide();
                                ajax_error(jqXHR, textStatus, errorThrown);
                            }
                        });
                    }
                },
                cancel: function() {}
            }
        });
    }

    function loadDatatable() {
        $('#loadingDiv').show();
        $('#search_details').show();
        var msg = 'List of Validation Lot of Scheme : ' + $("#scheme_id option:selected")
            .text();
        $('#heading_msg').text(msg);
        var scheme_code = $('#scheme_id').val();
        var status = $('#status').val();
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
                url: "{{ route('reportLotMasterValidation') }}",
                type: "post",
                data: function(d) {
                    d.status = $('#status').val(),
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
                    "data": 'DT_RowIndex'
                },
                {
                    "data": "input_file_name"
                },
                {
                    "data": "status"
                },
                {
                    "data": "total_record"
                },
                {
                    "data": "success_count"
                },
                {
                    "data": "failed_count"
                },
                {
                    "data": "ack_status"
                },
                {
                    "data": "action"
                }
            ],
            "buttons": [{
                    extend: 'pdf',
                    footer: true,
                    pageSize: 'A4',
                    //orientation: 'landscape',
                    pageMargins: [40, 60, 40, 60],
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5],

                    }
                },
                {
                    extend: 'excel',
                    footer: true,
                    pageSize: 'A4',
                    //orientation: 'landscape',
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
