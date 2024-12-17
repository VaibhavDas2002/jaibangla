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
                            <form method="GET" action="{{ route('jbDataPushedToDbt') }}">
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
                                            <button type="submit" name="filter" id="filter" class="btn btn-success"> Pushed</button>&nbsp;&nbsp;
                                            <button type="button" name="reset" id="reset" class="btn btn-warning"><i
                                                    class="fa fa-refresh"></i> Reset</button>
                                        </div>
                                    </center>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="form-group col-md-offset-4 col-md-3 " style="display: none;"
                                        id="approve_rejdiv">
                                        <button type="button" name="bulk_approve" class="btn btn-info btn-lg"
                                            id="bulk_approve" value="approve">
                                            Approve</button>
                                    </div>
                                </div>
                            </form>
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
    <script>
        $(document).ready(function() {
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

            $('#filter').click(function() {
                // alert('Hi');
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
                    // alert('Hi');
                    $('#loadingDiv').show();
                    $('#res_div').show();
                    var msg = 'Beneficiary Details';
                    $('#panel_head').text(msg);
                    // dataTable.ajax.reload();
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
                        // console.log(JSON.stringify(response));
                        if (data.status == 1) {
                            $.confirm({
                            title: data.title,
                            type: data.type,
                            icon: data.icon,
                            content: data.msg,
                            buttons: {
                                Ok: function() {
                                    $("#submitting").hide();
                                    $("#verifyReject").show();
                                    $("html, body").animate({scrollTop: 0},"slow");
                                }
                            }
                            });
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
                        }
                    },
                    complete: function() {

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('.ben_view_body').removeClass('disabledcontent');
                        $('#loader_img_personal').hide();
                        $('.ben_view_button').removeAttr('disabled', true);
                        $('.ben_view_modal').modal('hide');
                        // ajax_error(jqXHR, textStatus, errorThrown);
                        $.alert({
                            title: 'Error!!',
                            type: 'red',
                            icon: 'fa fa-warning',
                            content: 'Something wrong while fetching the beneficiary data!!',
                        });
                    }
                });
                }
            });

            $('#example').on('page.dt', function() {
                $('#approve_rejdiv').hide();
            });

            $('#example').on('length.dt', function(e, settings, len) {
                $("#check_all_btn").prop("checked", false);
            });
            // $('#check_all_btn').on('change', function() {
            //     var checked = $(this).prop('checked');

            //     dataTable.cells(null, 9).every(function() {
            //         var cell = this.node();
            //         $(cell).find('input[type="checkbox"][name="chkbx"]').prop('checked', checked);
            //     });
            //     var data = dataTable
            //         .rows(function(idx, data, node) {
            //             return $(node).find('input[type="checkbox"][name="chkbx"]').prop('checked');
            //         })
            //         .data()
            //         .toArray();
            //     //console.log(data);
            //     if (data.length === 0) {
            //         $("input.all_checkbox").removeAttr("disabled", true);
            //     } else {
            //         $("input.all_checkbox").attr("disabled", true);
            //     }
            //     var anyBoxesChecked = false;
            //     var applicantId = Array();
            //     $('input[type="checkbox"][name="chkbx"]').each(function(index, value) {
            //         if ($(this).is(":checked")) {
            //             anyBoxesChecked = true;
            //             applicantId.push(value.value);
            //         }
            //     });

            //     $("#fullForm #applicantId").val($.unique(applicantId));
            //     if (anyBoxesChecked == true) {
            //         $('#approve_rejdiv').show();
            //         $('.ben_view_button').attr('disabled', true);
            //         document.getElementById('bulk_approve').disabled = false;
            //         // document.getElementById('bulk_blkchange').disabled = false;
            //     } else {
            //         $('#approve_rejdiv').hide();
            //         $('.ben_view_button').removeAttr('disabled', true);
            //         document.getElementById('bulk_approve').disabled = true;
            //         // document.getElementById('bulk_blkchange').disabled = true;
            //     }
            //     // console.log(applicantId);
            // });
            // ------------------- End Checkbox Operation -----------------------//

            // ------------------- View Button Click Section -----------------------//
            // $(document).on('click', '.ben_view_button', function() {
            //     $('#loader_img_personal').show();
            //     $('.ben_view_button').attr('disabled', true);
            //     var benid = $(this).val();
            //     $('#fullForm #application_id').val(benid);
            //     $("#fullForm #is_bulk").val(0);
            //     $('#opreation_type').val('A').trigger('change');
            //     $("#verifyReject").html("Approve");
            //     $('#div_rejection').hide();
            //     $(".singleInfo").show();
            //     $('.applicant_id_modal').html('');
            //     $('#accept_reject_comments').val('');
            //     $("#collapseBank").collapse('hide');
            //     $('#collapsePersonal').collapse('hide');
            //     $('.ben_view_body').addClass('disabledcontent');
            //     $.ajax({
            //         type: 'post',
            //         url: "{{ route('getModalView') }}",
            //         data: {
            //             _token: '{{ csrf_token() }}',
            //             benid: benid,

            //         },
            //         dataType: 'json',
            //         success: function(response) {
            //             // console.log(JSON.stringify(response));
            //             $('#fullname').text(response.ben_name);
            //             $('#old_acc_no').text(response.bank_code);
            //             $('#old_bank_name').text(response.bank_name);
            //             $('#old_branch_name').text(response.branch_name);
            //             $('#old_ifsc').text(response.bank_ifsc);
            //             $('#new_acc_no').text(response.new_bank_code);
            //             $('#new_ifsc').text(response.new_bank_ifsc);

            //             $('.ben_view_body').removeClass('disabledcontent');
            //             $("#collapseBank").collapse('show');
            //             $('#loader_img_personal').hide();
            //             $('.ben_view_button').removeAttr('disabled', true);

            //             $('.ben_doc_button').attr('id', 'btnDoc_' + response.id +'');
            //             $('.ben_doc_button').val(response.id);
            //             $('.applicant_id_modal').html('(Beneficiary ID - ' + response.id +
            //                 ' )');
            //             $('#fullForm #id').val(response.id);
            //         },
            //         complete: function() {

            //         },
            //         error: function(jqXHR, textStatus, errorThrown) {
            //             $('.ben_view_body').removeClass('disabledcontent');
            //             $('#loader_img_personal').hide();
            //             $('.ben_view_button').removeAttr('disabled', true);
            //             $('.ben_view_modal').modal('hide');
            //             // ajax_error(jqXHR, textStatus, errorThrown);
            //             $.alert({
            //                 title: 'Error!!',
            //                 type: 'red',
            //                 icon: 'fa fa-warning',
            //                 content: 'Something wrong while fetching the beneficiary data!!',
            //             });
            //         }
            //     });
            //     $('.ben_view_modal').modal('show');

            // });
            // $('#bulk_approve').click(function() {
            //     $(".singleInfo").hide();
            //     $("#fullForm #is_bulk").val(1);
            //     $('#opreation_type').val('A').trigger('change');
            //     $("#verifyReject").html("Approve");
            //     $('#div_rejection').hide();
            //     $('#fullForm #id').val('');
            //     $('#fullForm #application_id').val('');
            //     $('#accept_reject_comments').val('');
            //     benid = "";
            //     $('.ben_view_modal').modal('show');
            // });

            // $(document).on('click', '.opreation_type', function() {
            //     if ($(this).val() == 'T' || $(this).val() == 'R') {
            //         $('#div_rejection').show();
            //         if ($(this).val() == 'T')
            //             $("#verifyReject").html("Revert");
            //         else if ($(this).val() == 'R')
            //             $("#verifyReject").html("Reject");
            //     } else {
            //         $("#verifyReject").html("Approve");
            //         $('#div_rejection').hide();
            //         $("#reject_cause").val('');
            //     }
            // });
            // -------------------- View Button Click Section End -----------------------//

            // -------------------- Final Approve Section-------------------------- //
            // $(document).on('click', '#verifyReject', function() {
            //     var reject_cause = $('#reject_cause').val();
            //     var opreation_type = $('#opreation_type').val();
            //     var accept_reject_comments = $('#accept_reject_comments').val();
            //     var is_bulk = $('#is_bulk').val();
            //     var single_app_id = $('#application_id').val();
            //     var applicantId = $('#applicantId').val();
            //     var scheme_id = $('#scheme_id').val();
            //     var valid = 1;
            //     if (opreation_type == 'R' || opreation_type == 'T') {
            //         var valid = 0;
            //         if (reject_cause != '') {
            //             var valid = 1;
            //         } else {
            //             $.alert({
            //                 title: 'Error!!',
            //                 type: 'red',
            //                 icon: 'fa fa-warning',
            //                 content: '<strong>Please Select Cause</strong>',
            //             });
            //             return false;
            //         }
            //     }
            //     if (valid == 1) {
            //         $.confirm({
            //             title: 'Warning',
            //             type: 'orange',
            //             icon: 'fa fa-warning',
            //             content: '<strong>Are you sure to proceed?</strong>',
            //             buttons: {
            //                 Ok: function() {
            //                     $("#submitting").show();
            //                     $("#verifyReject").hide();
            //                     var id = $('#id').val();

            //                     $.ajax({
            //                         type: 'POST',
            //                         url: "{{ url('updateDeduplicateBankApprove') }}",
            //                         data: {
            //                             reject_cause: reject_cause,
            //                             opreation_type: opreation_type,
            //                             accept_reject_comments: accept_reject_comments,
            //                             application_id: id,
            //                             is_bulk: is_bulk,
            //                             scheme_id: scheme_id,
            //                             applicantId: applicantId,
            //                             single_app_id: single_app_id,
            //                             _token: '{{ csrf_token() }}',
            //                         },
            //                         success: function(data) {
            //                             console.log(data);
            //                             console.log(JSON.stringify(data));
            //                             // dataTable.ajax.reload();
            //                             var table_renew = $('#example').DataTable();
            //                             table_renew.ajax.reload(null, false);
            //                             //$('#example').DataTable().ajax.reload()
            //                             if (data.status == 1) {
            //                                 $('.ben_view_modal').modal('hide');
            //                                 $('#approve_rejdiv').hide();
            //                                 $.confirm({
            //                                     title: data.title,
            //                                     type: data.type,
            //                                     icon: data.icon,
            //                                     content: data.msg,
            //                                     buttons: {
            //                                         Ok: function() {
            //                                             $("#submitting").hide();
            //                                             $("#verifyReject").show();
            //                                             $("html, body").animate({scrollTop: 0},"slow");
            //                                         }
            //                                     }
            //                                 });
            //                             } else {
            //                                 $("#submitting").hide();
            //                                 $("#verifyReject").show();
            //                                 $('.ben_view_modal').modal('hide');
            //                                 $('#approve_rejdiv').hide();
            //                                 $.alert({
            //                                     title: data.title,
            //                                     type: data.type,
            //                                     icon: data.icon,
            //                                     content: data.msg
            //                                 });
            //                             }
            //                         },
            //                         error: function(jqXHR, textStatus, errorThrown) {
            //                             $.confirm({
            //                                 title: 'Error',
            //                                 type: 'red',
            //                                 icon: 'fa fa-warning',
            //                                 content: 'Something went wrong in the approval!!',
            //                                 buttons: {
            //                                     Ok: function() {
            //                                         // $("#verifyReject").show();
            //                                         //  $("#submitting").hide();
            //                                         location.reload();
            //                                     }
            //                                 }
            //                             });
            //                         }
            //                     });
            //                 },
            //                 Cancel: function() {

            //                 },
            //             }
            //         });
            //     }
            // });
            // -------------------- Final Approve Section --------------------------// 

            // --------------- Filter Section -------------------- //
            //   $('#filter').click(function(){
            //     dataTable.ajax.reload();
            //   });

            $('#reset').click(function() {
                $('#scheme_id').val('').trigger('change');
                $('#month').val('').trigger('change');
                $('#fin_year').val('').trigger('change');                
            });
        });
    </script>
@stop
