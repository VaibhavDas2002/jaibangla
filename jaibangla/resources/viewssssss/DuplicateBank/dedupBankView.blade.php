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
        padding: 10px;
        color: #555;
        font-size: 14px;
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

    .modal-confirm {
        color: #636363;
        width: 400px;
    }

    .modal-confirm .modal-content {
        padding: 20px;
        border-radius: 5px;
        border: none;
        text-align: center;
        font-size: 14px;
    }

    .modal-confirm .modal-header {
        border-bottom: none;
        position: relative;
    }

    .modal-confirm h4 {
        text-align: center;
        font-size: 26px;
        margin: 30px 0 -10px;
    }

    .modal-confirm .close {
        position: absolute;
        top: -5px;
        right: -2px;
    }

    .modal-confirm .modal-body {
        color: #999;
    }

    .modal-confirm .modal-footer {
        border: none;
        text-align: center;
        border-radius: 5px;
        font-size: 13px;
        padding: 10px 15px 25px;
    }

    .modal-confirm .modal-footer a {
        color: #999;
    }

    .modal-confirm .icon-box {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        border-radius: 50%;
        z-index: 9;
        text-align: center;
        border: 3px solid #f15e5e;
    }

    .modal-confirm .icon-box i {
        color: #f15e5e;
        font-size: 46px;
        display: inline-block;
        margin-top: 13px;
    }

    .modal-confirm .btn,
    .modal-confirm .btn:active {
        color: #fff;
        border-radius: 4px;
        background: #60c7c1;
        text-decoration: none;
        transition: all 0.4s;
        line-height: normal;
        min-width: 120px;
        border: none;
        min-height: 40px;
        border-radius: 3px;
        margin: 0 5px;
    }

    .modal-confirm .btn-secondary {
        background: #c1c1c1;
    }

    .modal-confirm .btn-secondary:hover,
    .modal-confirm .btn-secondary:focus {
        background: #a8a8a8;
    }

    .modal-confirm .btn-danger {
        background: #f15e5e;
    }

    .modal-confirm .btn-danger:hover,
    .modal-confirm .btn-danger:focus {
        background: #ee3535;
    }

    .trigger-btn {
        display: inline-block;
        margin: 100px auto;
    }

    .all_checkbox {
        padding-left: 20px;

    }
</style>

@extends('layouts.app-template-datatable_new')
@section('content')

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <section class="content">
            <div class="box box-default">
                <div class="box-body">




                    <div class="panel panel-default">
                        <div class="panel-body" style="padding: 5px; font-size: 14px;">

                            @if ($message = Session::get('success'))
                                <div class="row">
                                    <div class="alert alert-success alert-block" style="margin:10px 30px 10px 30px;">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>{{ $message }}</strong>

                                    </div>
                                </div>
                            @endif
                            @if ($error = Session::get('error'))
                                <div class="row">
                                    <div class="alert alert-danger alert-block" style="margin:10px 30px 10px 30px;">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>{{ $error }}</strong>

                                    </div>
                                </div>
                            @endif
                            <div class="alert print-error-msg" style="display:none;" id="errorDiv">
                                <button type="button" class="close" aria-label="Close"
                                    onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
                                <ul></ul>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <a href="{{ url('dedupBankListView') }}?scheme_id={{ $scheme_id }}"><img
                                            width="50px;" style="pull-left" src="{{ asset('images/back.png') }}"
                                            alt="Back" /></a>
                                </div>
                                <div class="col-md-8">
                                    <h3 style="text-align: center;">Scheme:<span
                                            style="color:red;">{{ $scheme_name }}</span></h3>
                                    <h3 style="text-align: center;">Bank Account No.:<span
                                            style="color:red;">{{ $bank_code }}</span></h3>

                                </div>


                            </div>
                            <div class="row">

                                <div class="form-group col-md-offset-3 col-md-3" style="display: none;" id="approve_rejdiv">
                                    <button type="button" name="bulk_reject" class="btn btn-danger btn-lg" id="bulk_reject"
                                        value="approve">
                                        Bulk Reject</button>

                                </div>

                            </div>

                            <div class="table-responsive">
                                <table id="example" class="display" cellspacing="0" width="100%">
                                    <thead style="font-size: 12px;">
                                        <th witdth="2%">Beneficiary Id</th>
                                        <th witdth="10%">Beneficiary name</th>
                                        <th witdth="2%">Mobile No.</th>
                                        <th witdth="10%">Block/Municipality</th>
                                        <th witdth="10%">GP/Ward </th>
                                        <th witdth="50%">Action</th>
                                        <th witdth="5%">Check &nbsp;<input type="checkbox" id="select_all" /></th>

                                    </thead>
                                    <tbody style="font-size: 14px;">
                                        @if (count($data) > 0)
                                            @foreach ($data as $row)
                                                <tr>
                                                    <td>{{ $row['application_id'] }}</td>
                                                    <td>{{ $row['ben_name'] }}</td>
                                                    <td>{{ $row['mobile_no'] }}</td>
                                                    <td>{{ $row['local_body_name'] }}</td>
                                                    <td>{{ $row['gp_ward_name'] }}</td>
                                                    <td><button class="btn btn-info ben_doc_button btn-sm"
                                                            id="btnDoc_{{ $row['application_id'] }}"
                                                            value="{{ $row['application_id'] }}">View Bank
                                                            Passbok</button>&nbsp;
                                                        @if ($row['allowed'] == 1)
                                                            <button class="btn btn-warning ben_reject_button btn-sm"
                                                                id="btnUpdate_{{ $row['application_id'] }}"
                                                                value="{{ $row['application_id'] }}">Reject</button>&nbsp;
                                                            <a class="btn btn-danger btn-sm"
                                                                href="{{ url('/dedupBankUpdate') }}?bank_code={{ $bank_code }}&application_id={{ $row['application_id'] }}&scheme_id={{ $scheme_id }}">Edit</a>&nbsp;
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($row['allowed'] == 1)
                                                            <input type="checkbox" name="chkbx" class="all_checkbox"
                                                                onclick="controlCheckBox();"
                                                                value="{{ $row['application_id'] }}" />
                                                        @else
                                                            <span class="label label-success">Related to other</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5">No Duplicate Record Found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>
    <div id="modalApproval" class="modal fade">
        <form method="POST" action="{{ route('dupBankReject') }}" name="dupReject" id="dupReject">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="application_id" name="application_id" />
            <input type="hidden" name="applicantId" id="applicantId" value="" />
            <input type="hidden" name="is_bulk" id="is_bulk" value="0" />
            <input type="hidden" name="bank_code" id="bank_code" value="{{ $bank_code }}">
            <input type="hidden" name="bank_ifsc" id="bank_ifsc" value="{{ $bank_ifsc }}">
            <input type="hidden" name="scheme_id" id="scheme_id" value="{{ $scheme_id }}" />

            <div class="modal-dialog modal-confirm">
                <div class="modal-content">
                    <div class="modal-header flex-column">

                        <h4 class="modal-title w-100">Are you sure?</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Do you really want to Reject <span id="singleInfo">of the application(<span
                                    id="application_text_approve"></span>)</span>?</p>
                        <div class="form-group col-md-12" id="div_rejection">
                            <label class="required" for="reject_cause">Select Reject Cause</label>
                            <select name="reject_cause" id="reject_cause" class="form-control">
                                <option value="">--Select--</option>
                                @foreach ($reject_revert_reason as $r_arr)
                                    <option value="{{ $r_arr->id }}">{{ $r_arr->reason }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger modal-submitapprove">OK</button>
                        <button type="button" id="submittingapprove" value="Submit"
                            class="btn btn-success success btn-lg" disabled>Submitting please wait</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal" id="encolser_modal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="encolser_name">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="encolser_content"> </div>


            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('js/jquery.table2excel.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.sidebar-menu li').removeClass('active');
            $('.sidebar-menu #lk-main').addClass("active");
            $('.sidebar-menu #dup_bank_code_approved').addClass("active");
            var sessiontimeoutmessage = '{{ $sessiontimeoutmessage }}';
            var base_url = '{{ url('/') }}';
            $("#submittingapprove").hide();
            $(".NumOnly").keyup(function(event) {
                $(this).val($(this).val().replace(/[^\d].+/, ""));
                if ((event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });
            $('#example').DataTable({
                "paging": true,
                "searchable": true,
                "paging": false,
                "ordering": false,
                "bFilter": true,
                "bInfo": true,
                "pageLength": 20
            });
            $('#select_all').on('click', function() {
                if (this.checked) {
                    $('.all_checkbox').each(function() {
                        this.checked = true;
                    });
                } else {
                    $('.all_checkbox').each(function() {
                        this.checked = false;
                    });
                }
                controlCheckBox();
            });
            $('.all_checkbox').on('click', function() {
                if ($('.all_checkbox:checked').length == $('.all_checkbox').length) {
                    $('#select_all').prop('checked', true);

                } else {
                    $('#select_all').prop('checked', false);
                }
            });


            $(document).on('click', '.ben_doc_button', function() {
                $('.ben_doc_button').attr('disabled', true);
                //$('.ben_reject_button').attr('disabled',true); 
                $('.ben_reject_button').attr('disabled', true);
                var benidArr = $(this).val();
                View_encolser_modal('Copy of Bank Pass book', 10, benidArr);
            });

            $(document).on('click', '.ben_reject_button', function() {
                $('#dupReject #application_id').val('');
                $('.ben_reject_button').attr('disabled', false);
                var benidArr = $(this).val();
                //console.log(benidArr);
                var benidArr_explode = benidArr.split('_');
                //console.log(benidArr_explode);
                $("#reject_cause").val('');
                //var benid=benidArr_explode[0];
                $('#dupReject #application_id').val(benidArr);
                $('.ben_reject_button').attr('disabled', true);
                $('.ben_doc_button').attr('disabled', true);
                $('#bulk_reject').attr('disabled', true);
                $("#dupReject #is_bulk").val(0);
                $('#application_text_approve').text(benidArr_explode[0]);
                $("#modalApproval").modal();

            });
            $('#bulk_reject').click(function() {
                $("#reject_cause").val('');
                if ($('.all_checkbox:checked').length == $('.all_checkbox').length) {
                    alert(
                        'You Cannot reject all the beneficiaries. Please retain at least one beneficiary and uncheck this beneficiary from the list');
                    return false;
                } else {
                    $("#singleInfo").hide();
                    $("#dupReject #is_bulk").val(1);
                    $('#dupReject #application_id').val('');
                    $('.ben_reject_button').attr('disabled', true);
                    $('.ben_doc_button').attr('disabled', true);
                    $("#modalApproval").modal();
                }
            });
            $('#encolser_modal').on('hidden.bs.modal', function() {
                $('.ben_reject_button').attr('disabled', false);
                $('#bulk_approve').attr('disabled', false);
            });

            $('#modalApproval').on('hidden.bs.modal', function() {
                $('.ben_reject_button').attr('disabled', false);
                $('#bulk_reject').attr('disabled', false);
                $('.ben_doc_button').attr('disabled', false);
                $("#dupReject #is_bulk").val(0);
                $('#dupReject #application_id').val('');
                $('#dupReject #applicantId').val('');
                $("#singleInfo").show();
            });
            $('.modal-submitapprove').on('click', function() {
                var reject_reason = $("#reject_cause").val();
                if (reject_reason != '') {
                    $(".modal-submitapprove").hide();
                    $("#submittingapprove").show();
                    $("#dupReject").submit();
                } else {
                    alert('Please Select Reject Cause');
                    $("#reject_cause").focus();
                    return false;
                }
            });
        });

        function controlCheckBox() {
            var anyBoxesChecked = false;
            var applicantId = Array();
            $('.all_checkbox').each(function() {
                if ($(this).is(":checked")) {
                    anyBoxesChecked = true;
                    applicantId.push($(this).val());
                }

            });
            $("#dupReject #applicantId").val($.unique(applicantId));
            // alert(anyBoxesChecked);
            if (anyBoxesChecked == true) {
                $('#approve_rejdiv').show();
                // $("#select_all").attr("disabled", true);
                //$('.ben_view_button').attr('disabled',true);
                document.getElementById('bulk_approve').disabled = false;
                // document.getElementById('bulk_blkchange').disabled = false;
            } else {
                $('#approve_rejdiv').hide();
                // $('.ben_view_button').removeAttr('disabled',true);
                //$("#select_all").removeAttr("disabled", true);
                document.getElementById('bulk_approve').disabled = true;
                // document.getElementById('bulk_blkchange').disabled = true;
            }
            //console.log(applicantId);
        }

        function View_encolser_modal(doc_name, doc_type, application_id) {
            // alert(is_faulty);
            $('#encolser_name').html('');
            $('#encolser_content').html('');
            $('#encolser_name').html(doc_name + '(' + application_id + ')');
            $('#encolser_content').html('<img   width="50px" height="50px" src="images/ZKZg.gif"/>');

            var url = '{{ url('ajaxGetEncloser') }}';

            //alert(url);
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    doc_type: doc_type,
                    scheme_id: {{ $scheme_id }},
                    application_id: application_id,
                    _token: '{{ csrf_token() }}',
                },
            }).done(function(data, textStatus, jqXHR) {
                if (data.return_status) {
                    $('#encolser_content').html('');
                    $('#encolser_content').html(data.htmlText);
                    $('.ben_doc_button').attr('disabled', false);
                    // $('.ben_reject_button').attr('disabled',false); 
                    $('.ben_reject_button').attr('disabled', false);
                    $("#encolser_modal").modal();
                } else {
                    alert(data.return_msg);
                }

            }).fail(function(jqXHR, textStatus, errorThrown) {
                $('.ben_doc_button').attr('disabled', false);
                //$('.ben_reject_button').attr('disabled',false); 
                $('.ben_reject_button').attr('disabled', false);
                $('#encolser_content').html('');
                alert(sessiontimeoutmessage);
                window.location.href = base_url;
            });
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

        function closeError(divId) {
            $('#' + divId).hide();
        }
    </script>
@stop
