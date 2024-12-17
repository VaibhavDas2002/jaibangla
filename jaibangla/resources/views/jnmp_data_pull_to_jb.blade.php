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
                            
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="required-field">Limit</label>
                                        <input type="text" name="limit" id="limit" class="form-control">
                                        <span id="error_limit" class="text-danger"></span>
                                    </div>
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
                                </div>
                                <div class="row">
                                    <center>
                                        <div class="form-group col-md-12" style="margin-top: 24px;">
                                            <button type="submit" name="filter" id="filter" class="btn btn-primary"> Import Data From LB</button>&nbsp;&nbsp;
                                            <button type="button" name="reset" id="reset" class="btn btn-warning"><i class="fa fa-refresh"></i> Reset</button>&nbsp;&nbsp;
                                            <button type="button" name="deathMarking" id="deathMarking" class="btn btn-warning"> Marking for Death</button>
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
            $('#example tbody').empty();
            $('#filter').click(function() {
                // alert('Hi');
                if ($.trim($('#limit').val()).length == 0) {
                    error_limit = 'Scheme is required';
                    $('#error_limit').text(error_limit);
                } else {
                    error_limit = '';
                    $('#error_limit').text(error_limit);
                }

                if (error_limit != '') {
                    return false;
                } else {
                    // alert('Hi');
                    $('#loadingDiv').show();
                    $('#res_div').show();
                    var msg = 'Beneficiary Details';
                    $('#panel_head').text(msg);
                    // dataTable.ajax.reload();
                    var limit = $('#limit').val();
                    $.ajax({
                    type: 'post',
                    url: "{{ route('pullJnmpData') }}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        limit: limit
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

            $('#deathMarking').click(function() {
                // alert('Marking Death');
                if ($.trim($('#scheme_id').val()).length == 0) {
                    error_scheme_id = 'Scheme is required';
                    $('#error_scheme_id').text(error_scheme_id);
                } else {
                    error_scheme_id = '';
                    $('#error_scheme_id').text(error_scheme_id);
                }

                if (error_scheme_id != '') {
                    return false;
                } else {
                    $('#loadingDiv').show();
                    $('#res_div').show();
                    var msg = 'Beneficiary Details';
                    $('#panel_head').text(msg);
                    // dataTable.ajax.reload();
                    var scheme_id = $('#scheme_id').val();
                    $.ajax({
                        type: 'post',
                        url: "{{ route('deathMarkInJb') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            scheme_id: scheme_id
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
            
            $('#reset').click(function() {
                $('#scheme_id').val('').trigger('change');
                $('#month').val('').trigger('change');
                $('#fin_year').val('').trigger('change');                
            });
        });
    </script>
@stop
