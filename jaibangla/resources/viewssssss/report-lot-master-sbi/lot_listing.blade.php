<section class="content">
    <div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @elseif ($message = Session::get('danger'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
    </div>
    <!-- <h4 style="font-weight: bold;" class="text-danger">Signing the lot is now temporarily suspended due to expired the signing certificate. After validating the new certificate from SBI end, it will again open.</h4> -->

    <input style="display: none" type="hidden" value="{{ $status }}" id="record_status">
    <table id="example" class="display" cellspacing="0" width="100%">

        <thead>
            <tr role="row" class="sorting_asc" style="font-size: 12px;">
                <th width="5%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                    colspan="1" aria-label="Name: activate to sort column descending">Serial No</th>
                <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                    colspan="1" aria-label="Name: activate to sort column descending">Lot No</th>
                <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                    colspan="1" aria-label="Name: activate to sort column descending">Year Month</th>
                <th width="15%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                    colspan="1" aria-label="Name: activate to sort column descending">Status</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                    aria-label="Email: activate to sort column ascending">Total Beneficiary in the lot-List</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                    aria-label="Email: activate to sort column ascending">No. of Beneficiary in the lot</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                    aria-label="Email: activate to sort column ascending">Failed List</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                    aria-label="Email: activate to sort column ascending">Failed</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                    aria-label="Email: activate to sort column ascending">Success List</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                    aria-label="Email: activate to sort column ascending">Success</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"
                    aria-label="Email: activate to sort column ascending">Billed Amount</th>
                <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                    colspan="1" aria-label="Email: activate to sort column ascending">Debit Remarks</th>
                <th width="5%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                    colspan="1" aria-label="Email: activate to sort column ascending" style="text-align: center">
                    Action</th>
            </tr>
        </thead>
        <tbody>
            @php $i=1; @endphp
            @foreach ($reports as $report)
                <tr>
                    <td>@php print $i++; @endphp</td>
                    <td>{{ $report->lot_no }}</td>
                    <td>{{ $report->scheme_name }}</br>{{ $report->lot_year }} {{ $report->lot_month }}</td>
                    <td>
                        @php
                            if ($report->lot_status == 0) {
                                print 'Ready for push to Bank.';
                            } elseif ($report->lot_status == 1) {
                                print 'Lot signed.';
                            } elseif ($report->lot_status == 2) {
                                print 'File in server. will be pushed in next cycle';
                            } elseif ($report->lot_status == 3) {
                                print 'Pushed to SBI<br />Acknowledgement Received from SBI';
                            } elseif ($report->lot_status == 4) {
                                print 'Pushed to SBI<br />Acknowledgement Received from SBI <br /> Payment Response received form SBI.';
                            } elseif ($report->lot_status == 5) {
                                print 'Import SBI response complete';
                            } elseif ($report->lot_status == 6) {
                                print 'All actions completed';
                            } elseif ($report->lot_status == 7) {
                                print 'Lot has been stopped temporarily';
                            } elseif ($report->lot_status == 10) {
                                print 'Lot Signing Failed. Please Re-sign the LOT';
                            } elseif ($report->lot_status == 20) {
                                print 'Pushed to SBI Failed. Please Re-Push the LOT';
                            } elseif ($report->lot_status == 30) {
                                print 'Pushed to SBI</br> Acknowledgement receive failed.';
                            } elseif ($report->lot_status == 40) {
                                print 'Pushed to SBI<br />Acknowledgement Received from SBI<br /> Payment response receive failed.';
                            } elseif ($report->lot_status == 50) {
                                print 'Pushed to SBI<br />Acknowledgement Received from SBI <br /> Payment Response received form SBI.<br />
          Payment data not compiled. Please re-compile payment data.';
                            } else {
                                print 'Lot has been stopped.';
                            }
                        @endphp

                    </td>
                    @if ($report->lot_status < 0)
                        <td style="text-align: center">0</td>
                    @else
                        <td style="text-align: center">
                            <button type="button" class="btn btn-xs btn-margin excel_btn"
                                onmouseover="$(this).toggleClass('btn-primary');"
                                onmouseout="$(this).toggleClass('btn-primary');" style="font-size: 16px;"
                                title="Get Total Beneficiary List - {{ $report->credit_count }}"
                                value="{{ $report->lot_no }}_{{ $report->scheme_id }}" data-toggle="tooltip"
                                data-placement="bottom">Total</button>
                        </td>
                    @endif
                    @if ($report->lot_status < 0)
                        <td style="text-align: center">0</td>
                    @else
                        <td style="text-align: center">{{ $report->credit_count }}</td>
                    @endif

                    @if ($report->failed_count != '')
                        <td style="text-align: center">
                            <button type="button" class="btn btn-xs btn-margin excel_btn_failed"
                                onmouseover="$(this).toggleClass('btn-danger');"
                                onmouseout="$(this).toggleClass('btn-danger');" style="font-size: 16px;"
                                title="Get SBI Failed Beneficiary List - @php if($report->failed_count == '') {print '0';} else {print $report->failed_count;} @endphp"
                                value="{{ $report->lot_no }}_{{ $report->scheme_id }}" data-toggle="tooltip"
                                data-placement="bottom">
                                @php
                                    if ($report->failed_count == '') {
                                        print '0';
                                    } else {
                                        print 'Failed';
                                    }
                                @endphp
                            </button>
                        </td>
                    @else
                        <td style="text-align: center">
                        @php 
                        if ($report->failed_count == '') {
                                print '0';
                            } else {
                                print 'Get Failed List';
                            }
                        @endphp</td>
                    @endif
                    <td style="text-align: center">
                    @php 
                    if ($report->failed_count == '') {
                            print '0';
                        } else {
                            print $report->failed_count;
                    } @endphp</td>

                    @if ($report->success_count != '')
                        <td style="text-align: center">
                            <button type="button" class="btn btn-xs btn-margin excel_btn_success"
                                onmouseover="$(this).toggleClass('btn-success');"
                                onmouseout="$(this).toggleClass('btn-success');" style="font-size: 16px; "
                                title="Get SBI Success Beneficiary List - @php if($report->success_count == '') {print '0';} else {print $report->success_count;} @endphp"
                                value="{{ $report->lot_no }}_{{ $report->scheme_id }}" data-toggle="tooltip"
                                data-placement="bottom">
                                @php
                                    if ($report->success_count == '') {
                                        print '0';
                                    } else {
                                        print 'Success';
                                    }
                                @endphp
                            </button>
                        </td>
                        </form>
                    @else
                        <td style="text-align: center">
                        @php
                            if ($report->success_count == '') {
                                print '0';
                            } else {
                                print "Get Success
          List";
                        } @endphp</td>
                    @endif
                    <td style="text-align: center">
                    @php
                        if ($report->success_count == '') {
                            print '0';
                        } else {
                            print $report->success_count;
                    } @endphp</td>
                    <td style="text-align: center">@php 
                    if ($report->amount_debit == '') {
                            print '0';
                        } else {
                            print $report->amount_debit / 100;
                    } @endphp</td>
                    <td>{{ $report->description }}</td>

                    @if ($report->lot_status == 0 or $report->lot_status == 10)
                        <td>
                            <button type="button" id="pushtosbi_btn_{{ $report->lot_no }}_{{ $report->scheme_id }}"
                                class="btn btn-info btn-margin pushtosbi_btn"
                                value="{{ $report->lot_no }}_{{ $report->scheme_id }}_{{ $report->debit_reference }}">
                                Sign the Lot and Push to SBI
                            </button>
                        </td>
                    @elseif($report->lot_status == 1 or $report->lot_status == 20)
                        <td>
                            {{-- Waiting for to be pushed to SBI server in next cycle. --}}
                            <button type="button" id="sendtosbi_btn_{{ $report->lot_no }}_{{ $report->scheme_id }}"
                                class="btn btn-primary btn-margin sendtosbi_btn"
                                value="{{ $report->lot_no }}_{{ $report->scheme_id }}_{{ $report->debit_reference }}">
                                Send to SBI
                            </button>
                        </td>
                        </form>
                    @elseif($report->lot_status == 2 or $report->lot_status == 30)
                        <td>
                            {{-- Waiting for reciveing of Lot Acknowledgement from SBI. --}}
                            <button type="button"
                                id="receive_ack_sbi_btn_{{ $report->lot_no }}_{{ $report->scheme_id }}"
                                class="btn btn-warning btn-margin receive_ack_sbi_btn"
                                value="{{ $report->lot_no }}_{{ $report->scheme_id }}_{{ $report->debit_reference }}">
                                Receive ACK
                            </button>
                        </td>
                        </form>
                    @elseif($report->lot_status == 3 or $report->lot_status == 40)
                        <td>
                            {{-- Waiting for reciveing of Payment response from SBI. --}}
                            <button type="button"
                                id="receive_resposne_sbi_btn_{{ $report->lot_no }}_{{ $report->scheme_id }}"
                                class="btn btn-danger btn-margin receive_response_sbi_btn"
                                value="{{ $report->lot_no }}_{{ $report->scheme_id }}_{{ $report->debit_reference }}">
                                Receive Response
                            </button>
                        </td>
                        </form>
                    @elseif($report->lot_status == 4)
                        <td>
                            {{-- Waiting for import of Payment response from SBI. --}}
                            <button type="button"
                                id="import_response_sbi_btn_{{ $report->lot_no }}_{{ $report->scheme_id }}"
                                class="btn btn-success btn-margin import_response_sbi_btn"
                                value="{{ $report->lot_no }}_{{ $report->scheme_id }}_{{ $report->debit_reference }}">
                                Import Payment Response
                            </button>
                        </td>
                        </form>
                    @elseif($report->lot_status == 5 or $report->lot_status == 50)
                        <td>
                            <button type="submit" class="btn btn-warning btn-margin importsbi_btn"
                                value="{{ $report->lot_no }}_{{ $report->scheme_id }}_{{ $report->debit_reference }}_{{ $report->lot_month }}_{{ $report->lot_year }}">
                                Import SBI Payment status
                            </button>
                        </td>
                        <!-- </form> -->
                    @elseif($report->lot_status == 6)
                        <td style="text-align: center">
                            <i class="glyphicon glyphicon-ok"></i>
                        </td>
                    @elseif($report->lot_status < 0)
                        <td style="text-align: center">
                            <i class="glyphicon glyphicon-remove"></i>
                        </td>
                    @elseif($report->lot_status == 7)
                        <td style="text-align: center">
                            <i class="fa fa-warning"></i>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        <!-- <tfoot> -->

        <!-- </tfoot> -->
    </table>

    </div>

    </div>

    </div>
</section>
<!-- /.content -->



<script>
    $(document).ready(function() {
        $("#pushmodal_sbi").on('hide.bs.modal', function() {
            $('#lot_det').hide();
            $('#modal_body_det').hide();
        });
        $('[data-toggle="tooltip"]').tooltip();
        $('.excel_btn').click(function() {
            var val = $(this).val();
            var array = val.split("_");
            var lot_no = array[0];
            var scheme = array[1];
            var data = {
                '_token': '{{ csrf_token() }}',
                'lot_no': lot_no,
                'scheme_id': scheme,
                'error_type': 'COUNT'
            };
            redirectPostExcel('{{ route('lot_payment_xls_generate_new') }}', data, 'get');
        });

        $('.excel_btn_failed').click(function() {
            var val = $(this).val();
            var array = val.split("_");
            var lot_no = array[0];
            var scheme = array[1];
            var data = {
                '_token': '{{ csrf_token() }}',
                'lot_no': lot_no,
                'scheme_id': scheme,
                'error_type': 'E2'
            };
            redirectPostExcel('{{ route('lot_payment_xls_generate_new') }}', data, 'get');
        });

        $('.excel_btn_success').click(function() {
            var val = $(this).val();
            var array = val.split("_");
            var lot_no = array[0];
            var scheme = array[1];
            var data = {
                '_token': '{{ csrf_token() }}',
                'lot_no': lot_no,
                'scheme_id': scheme,
                'error_type': 'S0'
            };
            redirectPostExcel('{{ route('lot_payment_xls_generate_new') }}', data, 'get');
        });

        $('.pushtosbi_btn').click(function() {
            var select_scheme = $('#select_scheme').val();
            var lot_year = $('#lot_year').val();
            var lot_month = $('#lot_month').val();
            if (select_scheme == '' || lot_year == '' || lot_month == '') {
                //alert('Please select all the fields');
                $.alert({
                    title: 'Error!!',
                    type: 'red',
                    icon: 'fa fa-warning',
                    content: '<strong>Please select all the above fields from dropdown [Scheme, Financial Year, Month]</strong>',
                });
            } else {
                $(".content").addClass("disabledcontent");
                $('#loader_img').show();
                var acc = '';
                var ifsc = '';
                var val = $(this).val();
                var array = val.split("_");
                var lot_no = array[0];
                var scheme = array[1];
                var debit_no = array[2];
                $.ajax({
                    url: "{{ route('push-to-sbi-single-lot') }}",
                    method: 'post',
                    data: {
                        scheme_id: scheme,
                        lot_no: lot_no,
                        debit_no: debit_no,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(result) {
                        $(".content").removeClass("disabledcontent");
                        $('#loader_img').hide();
                        var acc = result.bank_accounts[0].bank_account_no;
                        var ifsc = result.bank_accounts[0].ifsc_code;
                        // console.log(JSON.stringify(result));

                        // New 27-11-2021
                        var contents = '';
                        contents =
                            '<div><font style="font-size: 15px; font-weight: bold;" class="text-success">Scheme : ' +
                            result.datas.data[0].scheme_name + '<br> Lot No : ' + result
                            .datas.data[0].lot_no + '<br> Lot Year : ' + result.datas.data[
                                0].lot_year + '<br> Lot Month : ' + result.datas.data[0]
                            .lot_month + '</font></div>';
                        contents +=
                            '<div><input type="hidden" name="lot_no" id="push_lot_no" value="' +
                            result.datas.data[0].lot_no +
                            '"><input type="hidden" name="scheme_id" id="push_scheme_id" value="' +
                            result.datas.data[0].scheme_id +
                            '"><label for="bank_account">Select Bank Account</label><select class="form-control" name="bank_account" id="bank_account" required><option value="' +
                            acc + ':' + ifsc + '">' + acc + ' (' + ifsc + ')' +
                            '</option></select><span id="error_bank_acc" class="text-danger"></span></div>';
                        $.confirm({
                            keyboardEnabled: true,
                            title: 'Sign the Lot and Push To SBI',
                            content: contents,
                            buttons: {
                                Confirm: {
                                    text: 'Push To SBI',
                                    btnClass: 'btn-info',
                                    keys: ['enter', 'shift'],
                                    action: function() {
                                        push_to_sbi();
                                    }
                                },
                                cancel: function() {}
                            }
                        });

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $(".content").removeClass("disabledcontent");
                        $('#loader_img').hide();
                        ajax_error(jqXHR, textStatus, errorThrown);
                    }
                });
            }
        });

        // Send To SBI
        $('.sendtosbi_btn').click(function() {
            var val = $(this).val();
            var confirm_msg = '<strong>Are you are sure want to send payment file to SBI server ?</strong>';
            var url = "{{ route('pushToSBIPaymentLot') }}";
            common_payment_transaction(val, confirm_msg, url);
        });

        // Receive Acknowledgement
        $('.receive_ack_sbi_btn').click(function() {
            var val = $(this).val();
            var confirm_msg = '<strong>Are you are sure want to receive acknowledgement file from SBI server ?</strong>';
            var url = "{{ route('reciveAcknowledgementSBIPaymentLot') }}";
            common_payment_transaction(val, confirm_msg, url);
        });

        // Receive Resposne
        $('.receive_response_sbi_btn').click(function() {
            var val = $(this).val();
            var confirm_msg = '<strong>Are you are sure want to receive payment resposne file from SBI server ?</strong>';
            var url = "{{ route('reciveResponseSBIPaymentLot') }}";
            common_payment_transaction(val, confirm_msg, url);
        });

        // Import Resposne
        $(document).on('click', '.import_response_sbi_btn', function() {
            var val = $(this).val();
            var confirm_msg = '<strong>Are you are sure want to import payment resposne ?</strong>';
            var url = "{{ route('importResponseSBIPaymentLot') }}";
            common_payment_transaction(val, confirm_msg, url);
        });


        $('.importsbi_btn').click(function() {
            var select_scheme = $('#select_scheme').val();
            var lot_year = $('#lot_year').val();
            var lot_month = $('#lot_month').val();
            if (select_scheme == '' || lot_year == '' || lot_month == '') {
                //alert('Please select all the fields');
                $.alert({
                    title: 'Error!!',
                    type: 'red',
                    icon: 'fa fa-warning',
                    content: '<strong>Please select all the above required (*) fields from dropdown [Scheme, Financial Year, Month]</strong>',
                });
            } else {
                var val = $(this).val();
                var array = val.split("_");
                var lot_no = array[0];
                var scheme = array[1];
                var debit_no = array[2];
                var lot_month = array[3];
                var lot_year = array[4];
                $.confirm({
                    title: 'Confirm!',
                    type: 'orange',
                    icon: 'fa fa-warning',
                    content: '<strong>Are you are sure want to import the SBI Payment Response ?</strong>',
                    buttons: {
                        Confirm: {
                            text: 'Confirm',
                            btnClass: 'btn-green',
                            keys: ['enter', 'shift'],
                            action: function() {
                                $(".content").addClass("disabledcontent");
                                $('#loader_img').show();
                                $.ajax({
                                    url: "{{ route('sbi_payment_status') }}",
                                    method: 'post',
                                    data: {
                                        scheme_id: scheme,
                                        lot_no: lot_no,
                                        debit_ref: debit_no,
                                        lot_month: lot_month,
                                        lot_year: lot_year,
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function(result) {
                                        $('#loader_img').hide();
                                        $(".content").removeClass(
                                            "disabledcontent");
                                        // console.log(result.lot_no);
                                        $.confirm({
                                            title: result.title,
                                            type: result.type,
                                            icon: result.icon,
                                            content: result.msg,
                                            buttons: {
                                                Ok: {
                                                    text: 'Ok',
                                                    btnClass: 'btn-green',
                                                    keys: ['enter',
                                                        'shift'
                                                    ],
                                                    action: function() {
                                                        reload_table
                                                            ();
                                                    }

                                                }

                                            }
                                        });
                                    },
                                    error: function(jqXHR, textStatus,
                                    errorThrown) {
                                        $('#loader_img').hide();
                                        $(".content").removeClass(
                                            "disabledcontent");
                                        ajax_error(jqXHR, textStatus,
                                            errorThrown);
                                    }
                                });
                            }
                        },
                        cancel: function() {}
                    }
                });
            }
        });

        $('#example').DataTable({
            dom: 'Blfrtip',
            "paging": true,
            "pageLength": 20,
            "lengthMenu": [
                [10, 20, 25, 50, 100, -1],
                [10, 20, 25, 50, 100, 'All']
            ],
            "scrollX": true,
            buttons: [{
                    extend: 'pdf',
                    title: 'Lot Report- SBI Payment',
                    footer: true,
                    pageSize: 'A4',
                    orientation: 'landscape',
                    pageMargins: [40, 60, 40, 60],
                    exportOptions: {
                        columns: [0, 1, 2, 3, 5, 7, 9, 10],

                    }
                },
                {
                    extend: 'excel',
                    title: 'Lot Report- SBI Payment',
                    footer: true,
                    pageSize: 'A4',
                    //orientation: 'landscape',
                    pageMargins: [40, 60, 40, 60],
                    exportOptions: {
                        columns: [0, 1, 2, 3, 5, 7, 9, 10],
                        stripHtml: false,
                    }
                },
                //'pdf','excel','csv','print','copy'
            ],


        });
    });

    function push_to_sbi() {
        var push_scheme_id = $('#push_scheme_id').val();
        var push_lot_no = $('#push_lot_no').val();
        var push_bank_account = $('#bank_account').val();
        if (push_scheme_id == '' || push_lot_no == '') {
            $.alert({
                title: 'Information !!',
                type: 'orange',
                icon: 'fa fa-info',
                content: '<strong>Something went wrong!!</strong>',
            });
        } else if ((push_scheme_id != '' && push_lot_no != '') && push_bank_account == '') {
            $.alert({
                title: 'Error!!',
                type: 'red',
                icon: 'fa fa-warning',
                content: '<strong>Please select bank account</strong>',
            });
        } else {
            $('#bank_account').removeAttr('style');
            $('#error_bank_acc').text('');
            $.confirm({
                keyboardEnabled: true,
                title: 'Confirm!',
                type: 'orange',
                icon: 'fa fa-warning',
                content: '<strong>Are you want to sign the lot and push to SBI server ?</strong>',
                buttons: {
                    Confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-green',
                        keys: ['enter', 'shift'],
                        action: function() {
                            $(".content").addClass("disabledcontent");
                            $('#loader_img').show();
                            $.ajax({
                                url: "{{ route('push-to-sbi.export') }}",
                                method: 'post',
                                data: {
                                    scheme_id: push_scheme_id,
                                    lot_no: push_lot_no,
                                    bank_account: push_bank_account,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(result) {
                                    $(".content").removeClass("disabledcontent");
                                    $('#loader_img').hide();
                                    //alert(JSON.stringify(result));
                                    $.confirm({
                                        keyboardEnabled: true,
                                        title: result.title,
                                        type: result.type,
                                        icon: result.icon,
                                        content: result.msg,
                                        buttons: {
                                            Ok: {
                                                text: 'Ok',
                                                btnClass: 'btn-green',
                                                keys: ['enter', 'shift'],
                                                action: function() {
                                                    reload_table();
                                                }
                                            }
                                        }
                                    });

                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    $(".content").removeClass("disabledcontent");
                                    $('#loader_img').hide();
                                    ajax_error(jqXHR, textStatus, errorThrown);
                                }
                            });
                        }
                    },
                    cancel: function() {}
                }

            });
        }
    }

    function common_payment_transaction(val, confirm_msg, url) {
        var array = val.split("_");
        var lot_no = array[0];
        var scheme = array[1];
        var debit_no = array[2];
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
                        $(".content").addClass("disabledcontent");
                        $('#loader_img').show();
                        $.ajax({
                            url: url,
                            method: 'post',
                            data: {
                                scheme_id: scheme,
                                lot_no: lot_no,
                                debit_ref: debit_no,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(result) {
                                $('#loader_img').hide();
                                $(".content").removeClass("disabledcontent");
                                // console.log(result.lot_no);
                                $.confirm({
                                    title: result.title,
                                    type: result.type,
                                    icon: result.icon,
                                    content: result.msg,
                                    buttons: {
                                        Ok: {
                                            text: 'Ok',
                                            btnClass: 'btn-green',
                                            keys: ['enter', 'shift'],
                                            action: function() {
                                                reload_table();
                                            }

                                        }

                                    }
                                });
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                $('#loader_img').hide();
                                $(".content").removeClass("disabledcontent");
                                ajax_error(jqXHR, textStatus, errorThrown);
                            }
                        });
                    }
                },
                cancel: function() {}
            }
        });
    }

    function reload_table() {
        $('#loader_img').show();
        $('#res_div').show();
        $(".content").addClass("disabledcontent");
        var msg = 'Scheme : ' + $("#select_scheme option:selected").text() + ' , Financial Year : ' + $('#lot_year')
            .val() + ' , Month : ' + $("#lot_month option:selected").text();
        $.ajax({
            url: "{{ route('lot-master-sbi-list') }}",
            method: 'post',
            data: {
                select_scheme: $('#select_scheme').val(),
                lot_year: $('#lot_year').val(),
                lot_month: $('#lot_month').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(result) {
                $('#loader_img').hide();
                $('#res_div').show();
                $('#sbilot_data').html('');
                $('#sbilot_data').html(result);
                $('#panel_head').text(msg);
                $(".content").removeClass("disabledcontent");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#loader_img').hide();
                $('#res_div').show();
                // ajax_error(jqXHR, textStatus, errorThrown);
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
                    buttons: {
                        ok: function() {
                            window.location.href = './index';
                        }
                    }
                });
            }
        });
    }

    function redirectPostExcel(url, data, method = 'get') {
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
</script>
