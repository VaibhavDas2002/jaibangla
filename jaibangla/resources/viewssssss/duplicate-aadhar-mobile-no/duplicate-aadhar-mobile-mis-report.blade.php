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
                Approved & Verified De-Duplicate Aadhar & Mobile MIS Report
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
                                        <div class="col-md-4">
                                                <label class=" control-label">Search For <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="search_for" id='search_for' required>
                                                    <option value="">--Select Search For--</option>
                                                    <option value="1">Duplicate Aadhar</option>
                                                    {{-- <option value="2">No Aadhar</option> --}}
                                                    <option value="3">Duplicate Mobile</option>
                                                    <option value="4">No Mobile</option>
                                                    {{-- <option value="dup_ration_card">Duplicate Ration Card</option>
                                                    <option value="no_ration_card">No Ration Card</option> --}}
                                                </select>
                                                <span class="text-danger" id="error_search_for"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <label class=" control-label">Scheme <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="scheme_id" id='scheme_id' required>
                                                    <option value="">--Select Scheme--</option>
                                                    @foreach ($schemes as $scheme)
                                                        <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="error_scheme_id"></span>
                                            </div>
                                            @if ($district_visible)
                                                <div class="form-group col-md-4">
                                                    <label class="">District</label>
                                                    <select name="district" id="district" class="form-control"
                                                        tabindex="6">
                                                        <option value="">--All --</option>
                                                        @foreach ($districts as $district)
                                                            <option value="{{ $district->district_code }}"
                                                                @if (old('district') == $district->district_code) selected @endif>
                                                                {{ $district->district_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span id="error_district" class="text-danger"></span>
                                                </div>
                                            @else
                                                <input type="hidden" name="district" id="district"
                                                    value="{{ $district_code_fk }}" />
                                            @endif

                                            @if ($is_urban_visible)
                                                <div class="form-group col-md-4" id="divUrbanCode">
                                                    <label class="">Rural/ Urban</label>
                                                    <select name="urban_code" id="urban_code" class="form-control"
                                                        tabindex="11">
                                                        <option value="">--All --</option>
                                                        @foreach (Config::get('constants.rural_urban') as $key => $val)
                                                            <option value="{{ $key }}"
                                                                @if (old('urban_code') == $key) selected @endif>
                                                                {{ $val }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span id="error_urban_code" class="text-danger"></span>
                                                </div>
                                            @else
                                                <input type="hidden" name="urban_code" id="urban_code"
                                                    value="{{ $rural_urban_fk }}" />
                                            @endif

                                            @if ($block_visible)
                                                <div class="form-group col-md-4" id="divBodyCode">
                                                    <label class="" id="blk_sub_txt">Block/Sub Division.</label>
                                                    <select name="block" id="block" class="form-control"
                                                        tabindex="16">
                                                        <option value="">--All --</option>
                                                    </select>
                                                    <span id="error_block" class="text-danger"></span>
                                                </div>
                                            @else
                                                <input type="hidden" name="block" id="block"
                                                    value="{{ $block_munc_corp_code_fk }}" />
                                            @endif

                                            <div class="form-group col-md-4" id="municipality_div"
                                                style="{{ $municipality_visible ? '' : 'display:none' }}">
                                                <label class="">Municipality</label>
                                                <select name="muncid" id="muncid" class="form-control"
                                                    tabindex="16">
                                                    <option value="">--All --</option>
                                                    @foreach ($muncList as $munc)
                                                        <option value="{{ $munc->urban_body_code }}">
                                                            {{ $munc->urban_body_name }}</option>
                                                    @endforeach
                                                </select>
                                                <span id="error_muncid" class="text-danger"></span>
                                            </div>

                                            <div class="form-group col-md-4" id="gp_ward_div"
                                                style="{{ $gp_ward_visible ? '' : 'display:none' }}">
                                                <label class="" id="gp_ward_txt">GP/Ward</label>
                                                <select name="gp_ward" id="gp_ward" class="form-control"
                                                    tabindex="17">
                                                    <option value="">--All --</option>
                                                    @foreach ($gpList as $gp)
                                                        <option value="{{ $gp->gram_panchyat_code }}">
                                                            {{ $gp->gram_panchyat_name }}</option>
                                                    @endforeach
                                                </select>
                                                <span id="error_gp_ward" class="text-danger"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <center>
                                            <div>
                                                <button class="btn btn-primary" name="submit_btn" id="submit_btn"
                                                    type="button" disabled><i class="fa fa-search"></i>
                                                    Search</button>&nbsp;
                                                {{-- <button class="btn btn-default" name="reset_btn" id="reset_btn" type="button" disabled><i class="fa fa-refresh"></i> Reset</button> --}}
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

                    <div id="search_details" style="display:none;">
                        <div class="panel panel-default">
                            <div class="panel-heading" id="heading_msg"
                                style="font-size: 15px; font-weight: bold; font-style: italic;">List of Beneficiary</div>
                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                <div class="pull-right" id="report_generation_text">Report Generated on:<b><?php date_default_timezone_set('Asia/Kolkata'); echo date("l jS \of F Y h:i:s A"); ?></b></div>
                                <div><button id="excel_btn" class="btn btn-primary exportToExcel"><i class="fa fa-file-excel-o"></i> Export To Excel</button></div>
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped table-bordered" cellspacing="0"
                                        width="100%" style="font-size: 14px; overflow-x: auto; white-space: nowrap;">
                                        <thead>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="10" style="display:none;" id="fotter_excel">Heading</td>
                                            </tr> 
                                        </tfoot>
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
@section('script')
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

        // Master drop down 
        $('#district').change(function() {
            var district = $(this).val();
            //alert(district);
            $('#urban_code').val('');
            $('#block').html('<option value="">--All --</option>');
            $('#muncid').html('<option value="">--All --</option>');
        });

        $('#urban_code').change(function() {
            var urban_code = $(this).val();
            if (urban_code == '') {
                $('#muncid').html('<option value="">--All --</option>');
            }
            $('#muncid').html('<option value="">--All --</option>');
            $('#block').html('<option value="">--All --</option>');
            $('#gp_ward').html('<option value="">--All --</option>');
            select_district_code = $('#district').val();
            if (select_district_code == '') {
                alert('Please Select District First');
                $("#district").focus();
                $("#urban_code").val('');
            } else {
                select_body_type = urban_code;
                var htmlOption = '<option value="">--All--</option>';
                $("#gp_ward_div").show();
                if (select_body_type == 2) {
                    $("#blk_sub_txt").text('Block');
                    $("#gp_ward_txt").text('GP');
                    $("#municipality_div").hide();
                    $.each(blocks, function(key, value) {
                        if (value.district_code == select_district_code) {
                            htmlOption += '<option value="' + value.id + '">' + value.text +
                                '</option>';
                        }
                    });
                } else if (select_body_type == 1) {
                    $("#blk_sub_txt").text('Subdivision');
                    $("#gp_ward_txt").text('Ward');
                    $("#municipality_div").show();
                    $.each(subDistricts, function(key, value) {
                        if (value.district_code == select_district_code) {
                            htmlOption += '<option value="' + value.id + '">' + value.text +
                                '</option>';
                        }
                    });
                } else {
                    $("#blk_sub_txt").text('Block/Subdivision');
                }
                $('#block').html(htmlOption);
            }

        });
        $('#block').change(function() {
            var block = $(this).val();
            var district = $("#district").val();
            var urban_code = $("#urban_code").val();
            if (district == '') {
                $('#urban_code').val('');
                $('#block').html('<option value="">--All --</option>');
                $('#muncid').html('<option value="">--All --</option>');
                alert('Please Select District First');
                $("#district").focus();

            }
            if (urban_code == '') {
                alert('Please Select Rural/Urban First');
                $('#block').html('<option value="">--All --</option>');
                $('#muncid').html('<option value="">--All --</option>');
                $("#urban_code").focus();
            }
            if (block != '') {
                var rural_urbanid = $('#urban_code').val();
                if (rural_urbanid == 1) {
                    var sub_district_code = $(this).val();
                    if (sub_district_code != '') {
                        $('#muncid').html('<option value="">--All --</option>');
                        select_district_code = $('#district').val();
                        var htmlOption = '<option value="">--All--</option>';
                        $.each(ulbs, function(key, value) {
                            if ((value.district_code == select_district_code) && (value
                                    .sub_district_code == sub_district_code)) {
                                htmlOption += '<option value="' + value.id + '">' + value.text +
                                    '</option>';
                            }
                        });
                        $('#muncid').html(htmlOption);
                    } else {
                        $('#muncid').html('<option value="">--All --</option>');
                    }
                } else if (rural_urbanid == 2) {
                    $('#muncid').html('<option value="">--All --</option>');
                    $("#municipality_div").hide();
                    var block_code = $(this).val();
                    select_district_code = $('#district').val();

                    var htmlOption = '<option value="">--All--</option>';
                    $.each(gps, function(key, value) {
                        if ((value.district_code == select_district_code) && (value
                                .block_code == block_code)) {
                            htmlOption += '<option value="' + value.id + '">' + value.text +
                                '</option>';
                        }
                    });
                    $('#gp_ward').html(htmlOption);
                    $("#gp_ward_div").show();


                } else {
                    $('#muncid').html('<option value="">--All --</option>');
                    $("#municipality_div").hide();
                }
            } else {
                $('#muncid').html('<option value="">--All --</option>');
                $('#gp_ward').html('<option value="">--All --</option>');
            }

        });
        $('#muncid').change(function() {
            var muncid = $(this).val();
            var district = $("#district").val();
            var urban_code = $("#urban_code").val();
            if (district == '') {
                $('#urban_code').val('');
                $('#block').html('<option value="">--All --</option>');
                $('#muncid').html('<option value="">--All --</option>');
                alert('Please Select District First');
                $("#district").focus();

            }
            if (urban_code == '') {
                alert('Please Select Rural/Urban First');
                $('#block').html('<option value="">--All --</option>');
                $('#muncid').html('<option value="">--All --</option>');
                $("#urban_code").focus();
            }
            if (muncid != '') {
                var rural_urbanid = $('#urban_code').val();
                if (rural_urbanid == 1) {
                    var municipality_code = $(this).val();
                    if (municipality_code != '') {
                        $('#gp_ward').html('<option value="">--All --</option>');
                        var htmlOption = '<option value="">--All--</option>';
                        $.each(ulb_wards, function(key, value) {
                            if (value.urban_body_code == municipality_code) {
                                htmlOption += '<option value="' + value.id + '">' + value.text +
                                    '</option>';
                            }
                        });
                        $('#gp_ward').html(htmlOption);
                    } else {
                        $('#gp_ward').html('<option value="">--All --</option>');
                    }
                } else {
                    $('#gp_ward').html('<option value="">--All --</option>');
                    $("#gp_ward_div").hide();
                }
            } else {
                $('#gp_ward').html('<option value="">--All --</option>');
            }

        });


        // End Master drop down
        var error_scheme_id = '';
        var error_search_for = '';
        $('#submit_btn').click(function() {
            if ($.trim($('#scheme_id').val()).length == 0) {
                error_scheme_id = 'Scheme name is required';
                $('#error_scheme_id').text(error_scheme_id);
            } else {
                error_scheme_id = '';
                $('#error_scheme_id').text(error_scheme_id);
            }

            if ($.trim($('#search_for').val()).length == 0) {
                error_search_for = 'Filter type is required';
                $('#error_search_for').text(error_search_for);
            } else {
                error_search_for = '';
                $('#error_search_for').text(error_search_for);
            }

            if (error_scheme_id != '' || error_search_for != '') {
                return false;
            } else {
                loadDataTable();
            }
        });
        // Export Excel
        $(".exportToExcel").click(function(e) {
            var c_date = $("#report_generation_text").val();
            var scheme_name = $("#scheme_id").val();
            var filter_value = $('#search_for :selected').text();
            // alert(filter_value);
            var currentDate = new Date();
            var formattedDate = 
            (currentDate.getMonth() + 1).toString().padStart(2, '0') + "_" + 
            currentDate.getDate().toString().padStart(2, '0') + "_" + 
            currentDate.getFullYear() + "_" + 
            currentDate.getHours().toString().padStart(2, '0') + "_" + 
            currentDate.getMinutes().toString().padStart(2, '0') + "_" + 
            currentDate.getSeconds().toString().padStart(2, '0');
            $("#example").table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "Worksheet Name",
                filename: "MIS Report of "+filter_value +" "+formattedDate, //do not include extension
                fileext: ".xls" // file extension
            });
        });
    });

    function loadDataTable() {
        var scheme_code = $('#scheme_id').val();
        var district = $('#district').val();
        var urban_code = $('#urban_code').val();
        var block = $('#block').val();
        var gp_ward = $('#gp_ward').val();
        var muncid = $('#muncid').val();
        var search_for = $('#search_for').val();

        $("#loadingDiv").show();
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: "{{ url('duplicateAadharMobileReport') }}",
            data: {
                scheme_id: scheme_code,
                district: district,
                urban_code: urban_code,
                block: block,
                gp_ward: gp_ward,
                muncid: muncid,
                search_for: search_for,
                _token: '{{ csrf_token() }}',
            },
            success: function(data) {
                // console.log(JSON.stringify(data));
                $('#loadingDiv').hide();
                if (data.return_status) {
                    $('#search_details').show();
                    var filter_value = $('#search_for :selected').text();
                    var scheme_name = $('#search_for :selected').text();
                    $("#heading_msg").html(data.heading_msg +' - '+ filter_value);
                    
                    
                    // alert(date);
                    if ($.fn.DataTable.isDataTable('#example')) {
                        $('#example').DataTable().destroy();
                    }
                    var table_head = $("#example > thead").html("");
                    // table_head.append('<tr><th></th><th></th><th colspan="5" style="text-align:center; background-color: LightGray;">Duplicate Aadhar Card</th><th colspan="5" style="text-align:center;">No Aadhar Card</th><th colspan="5" style="text-align:center;  background-color: LightGray;">Duplicate Mobile Number</th><th colspan="5" style="text-align:center;">No Mobile Number</th></tr>');
                    table_head.append('<tr><td colspan="7" style="display:none; text-align: center;">'+data.heading_msg+'- '+ filter_value +'</td></tr>');
                    table_head.append(
                        "<tr style='font-size: 12px;'><th>Sl No</th><th id='location_id'>District Name</th><th>Correction Pending [A]</th><th>Verification Pending [B]</th><th>Approval Pending [C]</th><th>Approved [D]</th><th>Total [E = A+B+C+D]</th></tr>"
                    );
                    
                    $("#location_id").text(data.column);
                    $("#example > tbody").html("");
                    $("#example > tfoot").html("");
                    var table = $("#example tbody");
                    var table_footer = $("#example tfoot");
                    var slno = 1;
                    var footer_1 = 0;
                    var footer_2 = 0;
                    var footer_3 = 0;
                    var footer_4 = 0;
                    var footer_5 = 0;
                    $.each(data.row_data, function(i, item) {
                        var correction_pending = isNaN(parseInt(item.correction_pending)) ? 0 : parseInt(item.correction_pending);
                        var verification_pending = isNaN(parseInt(item.verification_pending)) ? 0 : parseInt(item.verification_pending);
                        var approval_pending = isNaN(parseInt(item.approval_pending)) ? 0 : parseInt(item.approval_pending);
                        var approved = isNaN(parseInt(item.approved)) ? 0 : parseInt(item.approved);
                        // var total = isNaN(parseInt(item.total)) ? 0 : parseInt(item.total);
                        var total = correction_pending + verification_pending + approval_pending + approved;

                        // Column value add in footer.
                        
                        footer_1 = footer_1 + correction_pending;
                        footer_2 = footer_2 + verification_pending;
                        footer_3 = footer_3 + approval_pending;
                        footer_4 = footer_4 + approved;
                        footer_5 = footer_5 + total;

                        table.append("<tr><td>" + (i + 1) + "</td><td>" + item.location_name + "</td><td>" +
                            correction_pending + "</td><td>" + verification_pending + "</td><td>" +
                            approval_pending + "</td><td>" + approved + "</td><td>" + total + "</td></tr>"
                        );
                    });
                    table_footer.append("<tr><th></th><th>Total</th><th>" + footer_1 + "</th><th>" +
                        footer_2 + "</th><th>" + footer_3 + "</th><th>" + footer_4 + "</th><th>" + footer_5 + "</th></tr>");
                    table_footer.append('<tr><td colspan="10" style="display:none;" id="fotter_excel">Heading</td></tr>'); 
                    $("#fotter_excel").html("<b>"+$('#report_generation_text').text()+"</b>"); 
                    // var origin   = window.location.origin;
                    // table_footer.append('<tr><td colspan="12" style="display:none;" id="fotter_excel"><b> Report Generated From '+origin+'</b></td></tr>');
                } else {
                    $('#search_details').hide();
                    $("#example").hide();
                    printMsg(data.return_msg, '0', 'errorDiv');
                }
                $("#submit_loader1").hide();
                $("#submitting").show();

            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#submit_loader1").hide();
                $('#loadingDiv').hide();
                $("#submitting").show();

                $.alert({
                    title: 'Error!!',
                    type: 'red',
                    icon: 'fa fa-warning',
                    content: 'Something wrong..may be session timeout. please logout and then login again',
                });
                //  location.reload();
                // ajax_error(jqXHR, textStatus, errorThrown)
            }
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
</script>
@endsection