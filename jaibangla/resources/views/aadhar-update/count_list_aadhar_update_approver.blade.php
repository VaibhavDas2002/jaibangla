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
                Faulty Aadhar MIS Report
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
                                            {{-- <div class="col-md-4">
                                              <label class=" control-label">Search For <span
                                                      class="text-danger">*</span></label>
                                              <select class="form-control" name="search_for" id='search_for' required>
                                                  <option value="">--Select Search For--</option>
                                                  <option value="dup_aadhar">Duplicate Aadhar</option>
                                                  <option value="no_aadhar">No Aadhar</option>
                                                  <option value="dup_mobile">Duplicate Mobile</option>
                                                  <option value="no_mobile">No Mobile</option>
                                              </select>
                                              <span class="text-danger" id="error_search_for"></span>
                                          </div> --}}
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
                                                    <label class="">District <span
                                                            class="text-danger">*</span></label>
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
                                                    <label class="" id="blk_sub_txt">Block/Sub Division</label>
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
                                                {{-- <button class="btn btn-info" name="excel_btn" id="excel_btn"
                                                    type="button"><i class="fa fa-file-excel-o"></i> Export All Data To
                                                    Excel</button> --}}
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
                                style="font-size: 15px; font-weight: bold; font-style: italic;">List of Beneficiary
                                <div class="pull-right" id="report_generation_text">Report Generated on:<b><?php date_default_timezone_set('Asia/Kolkata'); echo date("l jS \of F Y h:i:s A"); ?></b></div>
                            </div>
                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                <button class="btn btn-info exportToExcel" type="button" >Export to Excel</button>
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped table-bordered table2excel" cellspacing="0"
                                        width="100%" style="font-size: 14px;">
                                        <thead>
                                            <th>Sl No.</th>
                                            <th>Block/Sub-Division</th>
                                            <th>Total Approved Beneficiary</th>
                                            <th>Blank/Invalid Aadhar NO. of Total Approved Beneficiary</th>
                                            <th>Aadhar NO. Edited by Operator</th>
                                            <th>Aadhar NO. Verified by Verifier</th>
                                            <th>Aadhar NO. Approved by Approver</th>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr id="fotter_id"></tr>
                                            <tr>
                                                <td colspan="26" align="center" style="display:none;" id="fotter_excel">Heading</td>
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
<script src="{{ asset("js/jquery.table2excel.js") }}"></script>
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
        $(".exportToExcel").click(function(e){
            var c_date=$("#c_date").val();
            var scheme_name=$("#scheme_name").val();
            // alert(c_date);
            $(".table2excel").table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "Worksheet Name",
                filename: "Aadhar Update MIS Report for "+scheme_name+" as on "+c_date, //do not include extension
                fileext: ".xls" // file extension
            }); 
        });
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
                // $("#gp_ward_div").show();
                if (select_body_type == 2) {
                    $("#gp_ward_div").show();
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
        // $('#muncid').change(function() {
        //     var muncid = $(this).val();
        //     var district = $("#district").val();
        //     var urban_code = $("#urban_code").val();
        //     if (district == '') {
        //         $('#urban_code').val('');
        //         $('#block').html('<option value="">--All --</option>');
        //         $('#muncid').html('<option value="">--All --</option>');
        //         alert('Please Select District First');
        //         $("#district").focus();

        //     }
        //     if (urban_code == '') {
        //         alert('Please Select Rural/Urban First');
        //         $('#block').html('<option value="">--All --</option>');
        //         $('#muncid').html('<option value="">--All --</option>');
        //         $("#urban_code").focus();
        //     }
        //     if (muncid != '') {
        //         var rural_urbanid = $('#urban_code').val();
        //         if (rural_urbanid == 1) {
        //             var municipality_code = $(this).val();
        //             if (municipality_code != '') {
        //                 $('#gp_ward').html('<option value="">--All --</option>');
        //                 var htmlOption = '<option value="">--All--</option>';
        //                 $.each(ulb_wards, function(key, value) {
        //                     if (value.urban_body_code == municipality_code) {
        //                         htmlOption += '<option value="' + value.id + '">' + value.text +
        //                             '</option>';
        //                     }
        //                 });
        //                 $('#gp_ward').html(htmlOption);
        //             } else {
        //                 $('#gp_ward').html('<option value="">--All --</option>');
        //             }
        //         } else {
        //             $('#gp_ward').html('<option value="">--All --</option>');
        //             $("#gp_ward_div").hide();
        //         }
        //     } else {
        //         $('#gp_ward').html('<option value="">--All --</option>');
        //     }

        // });


        // End Master drop down
        var error_scheme_id = '';
        var error_search_for = '';
        var error_district = '';
        $('#submit_btn').click(function() {
            // alert("Hi!");
            if ($.trim($('#scheme_id').val()).length == 0) {
                error_scheme_id = 'Scheme name is required';
                $('#error_scheme_id').text(error_scheme_id);
            } else {
                error_scheme_id = '';
                $('#error_scheme_id').text(error_scheme_id);
            }
            $("#c_date").val('');
            $("#scheme_name").val('');
            loadDataTable();
          });
            // if (error_scheme_id != '') {
            //     return false;
            // } else {
                function loadDataTable() {
                    var scheme_id = $('#scheme_id').val();
                    var district = $('#district').val();
                    var urban_code = $('#urban_code').val();
                    var block = $('#block').val();
                    var gp_ward = $('#gp_ward').val();
                    var muncid = $('#muncid').val();

                    $("#loadingDiv").show();
                    $("#submit_loader1").show();
                    $("#submitting").hide();
                    $('#search_details').hide();
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ url('aadhar-update-count-list-approver') }}",
                        data: {
                            scheme_id: scheme_id,
                            district: district,
                            urban_code: urban_code,
                            block: block,
                            gp_ward: gp_ward,
                            muncid: muncid,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(data) {
                            $('#loadingDiv').hide();
                            //alert(data.title);
                            if (data.return_status) {
                                $('#search_details').show();
                                $("#heading_msg1").html("<h4><b>" + data.heading_msg1 +
                                    "</b></h4>");
                                $("#heading_excel1").html("<b>" + data.heading_msg1 +
                                    "</b>");
                                $("#heading_excel2").html("<b>" + data.heading_msg2 +
                                    "</b>");
                                $("#fotter_excel").html("<b>" + $('#report_generation_text').text() + "</b>");
                                $("#location_id").text(data.column);
                                $("#c_date").val(data.c_date);
                                $("#scheme_name").val(data.scheme_name);
                                $("#example > tbody").html("");
                                var table = $("#example tbody");
                                var table_footer = $("#example tfoot");
                                var slno = 1;
                                var fotter_1 = 0;
                                var fotter_2 = 0;
                                var fotter_3 = 0;
                                var fotter_4 = 0;
                                var fotter_5 = 0;
                                var fotter_6 = 0;
                                // var fotter_7 = 0;
                                $.each(data.row_data, function(i, item) {
                                    var total_approved = isNaN(parseInt(item.total_approved)) ? 0 : parseInt(item.total_approved);
                                    var blank_aadhar_count = isNaN(parseInt(item.blank_aadhar_count)) ? 0 : parseInt(item.blank_aadhar_count);
                                    // var aadhar_no_less_than_12 = isNaN(parseInt(item.aadhar_no_less_than_12)) ? 0 : parseInt(item.aadhar_no_less_than_12);
                                    var edited = isNaN(parseInt(item.edited)) ? 0 : parseInt(item.edited);
                                    var verified = isNaN(parseInt(item.verified)) ? 0 : parseInt(item.verified);
                                    var approved = isNaN(parseInt(item.approved)) ? 0 : parseInt(item.approved);
                                    var total_count = total_approved + blank_aadhar_count + edited + verified + approved;


                                    fotter_1 = fotter_1 + total_approved;
                                    fotter_2 = fotter_2 + blank_aadhar_count;
                                    // fotter_3 = fotter_3 + aadhar_no_less_than_12;
                                    fotter_3 = fotter_3 + edited;
                                    fotter_4 = fotter_4 + verified;
                                    fotter_5 = fotter_5 + approved;
                                    fotter_6 = fotter_6 + total_count;

                                    table.append("<tr><td>" + (i + 1) +
                                        "</td><td>" + item.location_name + "</td><td>" + total_approved + "</td><td>" + blank_aadhar_count + "</td><td>" + edited + "</td><td>" + verified + "</td><td>" + approved + "</td></tr>"); 
                                    //slno++;
                                });
                                // table_footer.append("<tr><th></th><th>Total</th><th>" + fotter_1 + "</th><th>" +
                                //     fotter_2 + "</th><th>" + fotter_3 + "</th><th>" + fotter_4 + "</th><th>" + fotter_5 + "</th><th>" + fotter_6 + "</th></tr>");
                                // table_footer.append('<tr><td colspan="10" style="display:none;" id="fotter_excel">Heading</td></tr>');

                                $("#example > tfoot #fotter_id").html(
                                    "<th></th><th>Total</th><th>" + fotter_1 +
                                    "</th><th>" + fotter_2 + "</th><th>" + fotter_3 +
                                    "</th><th>" + fotter_4 + "</th><th>" + fotter_5 +
                                    "</th>");
                                //$('#example tbody').empty();
                                $("#example").show();


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
            //}
        
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
    });

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
