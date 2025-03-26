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
                Cross Scheme Bank Duplicate List
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
                                                <label class=" control-label">Cross Scheme <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="cross_scheme" id='cross_scheme' required>
                                                    <option value="">--Select Scheme--</option>
                                                    {{-- <option value="1">LB & Taposili Bandhu</option> --}}
                                                    {{-- <option value="2">LB & Jai Johar</option> --}}
                                                    <option value="3">LB & Old Age Pension</option>
                                                    {{-- <option value="4">Jai Johar & Taposili Bandhu</option> --}}
                                                </select>
                                                <span class="text-danger" id="error_cross_scheme"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <label class=" control-label">Filter <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="aadhar_filter" id='aadhar_filter' required>
                                                    <option value="1">Aadhaar Not Same</option>
                                                    <option value="2">Same Aadhaar</option>
                                                </select>
                                                <span class="text-danger" id="error_aadhar_filter"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <label class=" control-label">District</label>
                                                <select class="form-control" name="district" id='district' required>
                                                    <option value="">--All--</option>
                                                    @foreach ($districts as $district)
                                                        <option value="{{ $district->district_code }}">
                                                            {{ $district->district_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="error_district"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <center>
                                            <div>
                                                <button class="btn btn-primary" name="submit_btn" id="submit_btn"
                                                    type="button" disabled><i class="fa fa-search"></i>
                                                    Search</button>&nbsp;
                                                <button class="btn btn-info" name="excel_btn" id="excel_btn"
                                                    type="button"><i class="fa fa-file-excel-o"></i> Export All Data To
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
                                        width="100%" style="font-size: 14px;">
                                        <thead>
                                            <th>District</th>
                                            <th>Scheme</th>
                                            <th>Application ID</th>
                                            <th>Beneficiary ID</th>
                                            <th>Name</th>
                                            <th>Block/Municipality</th>
                                            <th>GP/Ward</th>
                                            <th>Mobile No.</th>
                                            <th>Account No.</th>
                                            <th>Aadhar No.</th>
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

        <div class="modal fade" id="modalUpdateAadhar" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Beneficiary Details</h4>
                    </div>
                    <div class="modal-body">
                        <div class="loadingDivModal"></div>
                        <div class="" id="updateDiv">
                            <!-- <div class="panel-heading">Enter Bank Details</div>
                            <div class="panel-body"> -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 style="text-align: center;" class="text-primary">Application ID: <span
                                            id="application_id"></span></h4>
                                </div>
                            </div>
                            <table class="table table-bordered table-responsive table-condensed table-striped"
                                style="font-size: 14px;">
                                <tr>
                                    <td>
                                        <strong>Name as JNMP Portal: </strong>
                                        <span id="name_jnmp_div"></span>
                                    </td>
                                    <td>
                                        <strong>Date of Death: </strong>
                                        <span id="death_div"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Name : </strong>
                                        <span id="name_div"></span>
                                    </td>
                                    <td>
                                        <strong>Gender: </strong>
                                        <span id="gender_div"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Mobile NO.: </strong>
                                        <span id="mobile_div"></span>
                                    </td>
                                    <td>
                                        <strong>Father's Name :</strong>
                                        <span id="father_div"></span>
                                    </td>
                                </tr>
                            </table>
                            <input type="hidden" name="pension_id" id="pension_id" value="">
                            <input type="hidden" name="update_scheme_id" id="update_scheme_id" value="">
                            {{-- <input type="hidden" name="old_aadhar_no" id="old_aadhar_no" value=""> --}}
                            <div class="table-responsive">
                                <table class="table table-bordered table-responsive table-condensed"
                                    style="width:100%; font-size: 14px;">
                                    <tr>
                                        <th>Upload File: <span class="text-danger">*</span></th>
                                        <td>
                                            <input type="file" name="doc_117" id="doc_117">
                                            <small style="font-weight: normal;" id='file_msg'></small> <br />
                                            <span class="text-danger" id="error_doc_117"></span>
                                        </td>
                                    </tr>
                                    <tr>

                                    </tr>
                                    <tr>
                                        <th>Remarks: </th>
                                        <td>
                                            <input type="text" name="remarks" id="remarks" class="form-control"
                                                value="" maxlength="100">
                                            <small style="font-weight: normal;">Max 100 character allowed</small>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="text-align: center;"><input type="button" name="submit"
                                        value="Save as alive" id="verifySubmit" class="btn btn-success btn-lg"></div>
                            </div>
                            <!-- </div> -->
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
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

        // Master drop down 
        // $('#district').change(function() {
        //     var district = $(this).val();
        //     //alert(district);
        //     $('#urban_code').val('');
        //     $('#block').html('<option value="">--All --</option>');
        //     $('#muncid').html('<option value="">--All --</option>');
        // });

        // $('#urban_code').change(function() {
        //     var urban_code = $(this).val();
        //     if (urban_code == '') {
        //         $('#muncid').html('<option value="">--All --</option>');
        //     }
        //     $('#muncid').html('<option value="">--All --</option>');
        //     $('#block').html('<option value="">--All --</option>');
        //     $('#gp_ward').html('<option value="">--All --</option>');
        //     select_district_code = $('#district').val();
        //     if (select_district_code == '') {
        //         alert('Please Select District First');
        //         $("#district").focus();
        //         $("#urban_code").val('');
        //     } else {
        //         select_body_type = urban_code;
        //         var htmlOption = '<option value="">--All--</option>';
        //         $("#gp_ward_div").show();
        //         if (select_body_type == 2) {
        //             $("#blk_sub_txt").text('Block');
        //             $("#gp_ward_txt").text('GP');
        //             $("#municipality_div").hide();
        //             $.each(blocks, function(key, value) {
        //                 if (value.district_code == select_district_code) {
        //                     htmlOption += '<option value="' + value.id + '">' + value.text +
        //                         '</option>';
        //                 }
        //             });
        //         } else if (select_body_type == 1) {
        //             $("#blk_sub_txt").text('Subdivision');
        //             $("#gp_ward_txt").text('Ward');
        //             $("#municipality_div").show();
        //             $.each(subDistricts, function(key, value) {
        //                 if (value.district_code == select_district_code) {
        //                     htmlOption += '<option value="' + value.id + '">' + value.text +
        //                         '</option>';
        //                 }
        //             });
        //         } else {
        //             $("#blk_sub_txt").text('Block/Subdivision');
        //         }
        //         $('#block').html(htmlOption);
        //     }

        // });
        // $('#block').change(function() {
        //     var block = $(this).val();
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
        //     if (block != '') {
        //         var rural_urbanid = $('#urban_code').val();
        //         if (rural_urbanid == 1) {
        //             var sub_district_code = $(this).val();
        //             if (sub_district_code != '') {
        //                 $('#muncid').html('<option value="">--All --</option>');
        //                 select_district_code = $('#district').val();
        //                 var htmlOption = '<option value="">--All--</option>';
        //                 $.each(ulbs, function(key, value) {
        //                     if ((value.district_code == select_district_code) && (value
        //                             .sub_district_code == sub_district_code)) {
        //                         htmlOption += '<option value="' + value.id + '">' + value.text +
        //                             '</option>';
        //                     }
        //                 });
        //                 $('#muncid').html(htmlOption);
        //             } else {
        //                 $('#muncid').html('<option value="">--All --</option>');
        //             }
        //         } else if (rural_urbanid == 2) {
        //             $('#muncid').html('<option value="">--All --</option>');
        //             $("#municipality_div").hide();
        //             var block_code = $(this).val();
        //             select_district_code = $('#district').val();

        //             var htmlOption = '<option value="">--All--</option>';
        //             $.each(gps, function(key, value) {
        //                 if ((value.district_code == select_district_code) && (value
        //                         .block_code == block_code)) {
        //                     htmlOption += '<option value="' + value.id + '">' + value.text +
        //                         '</option>';
        //                 }
        //             });
        //             $('#gp_ward').html(htmlOption);
        //             $("#gp_ward_div").show();


        //         } else {
        //             $('#muncid').html('<option value="">--All --</option>');
        //             $("#municipality_div").hide();
        //         }
        //     } else {
        //         $('#muncid').html('<option value="">--All --</option>');
        //         $('#gp_ward').html('<option value="">--All --</option>');
        //     }

        // });
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
        var error_cross_scheme = '';
        var error_search_for = '';
        var error_district = '';
        $('#submit_btn').click(function() {
            if ($.trim($('#cross_scheme').val()).length == 0) {
                error_cross_scheme = 'Cross scheme selection is required';
                $('#error_cross_scheme').text(error_cross_scheme);
            } else {
                error_cross_scheme = '';
                $('#error_cross_scheme').text(error_cross_scheme);
            }
            // if ($.trim($('#district').val()).length == 0) {
            //     error_district = 'District is required';
            //     $('#error_district').text(error_district);
            // } else {
            //     error_district = '';
            //     $('#error_district').text(error_district);
            // }
            if (error_cross_scheme != '') {
                return false;
            } else {
                $('#loadingDiv').show();
                $('#search_details').show();
                // $(':input[type="button"]').prop('disabled', false);

                // var search_option = $('#search_for').val();
                var scheme_code = $('#scheme_id').val();
                var district = $('#district').val();
                var urban_code = $('#urban_code').val();
                var block = $('#block').val();
                var gp_ward = $('#gp_ward').val();
                var aadhar_filter = $('#aadhar_filter').val();
                var muncid = $('#muncid').val();
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
                        url: "{{ route('crossSchemeBankDupList') }}",
                        type: "post",
                        data: function(d) {
                            d.district = district,
                                d.scheme_code = $('#cross_scheme').val(),
                                d.aadhar_filter = aadhar_filter,
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
                    "columns": [
                        {
                            "data": "district_name"
                        },
                        {
                            "data": "scheme_name"
                        },
                        {
                            "data": "application_id"
                        },
                        {
                            "data": "beneficiary_id"
                        },
                        {
                            "data": "ben_name"
                        },
                        {
                            "data": "block_ulb_name"
                        },
                        {
                            "data": "gp_ward_name"
                        },
                        {
                            "data": "mobile_no"
                        },
                        {
                            "data": "bank_code"
                        },
                        {
                            "data": "aadhar_no"
                        }
                    ],
                    "buttons": [
                        // {
                        //    extend: 'pdf',
                        //    footer: true,
                        //    pageSize:'A4',
                        //    //orientation: 'landscape',
                        //    pageMargins: [ 40, 60, 40, 60 ],
                        //    exportOptions: {
                        //         columns: [0,1,2,3,4,5],

                        //     }
                        //    },
                        //    {
                        //        extend: 'excel',
                        //        footer: true,
                        //        pageSize:'A4',
                        //        //orientation: 'landscape',
                        //        pageMargins: [ 40, 60, 40, 60 ],
                        //        exportOptions: {
                        //             columns: [0,1,2,3,4,5],
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
            var error_cross_scheme = '';
            var error_district = '';
            if ($.trim($('#cross_scheme').val()).length == 0) {
                error_cross_scheme = 'Scheme name is required';
                $('#error_cross_scheme').text(error_cross_scheme);
            } else {
                error_scheme_id = '';
                $('#error_cross_scheme').text(error_cross_scheme);
            }
            if (error_cross_scheme != '') {
                return false;
            } else {
                // var search_option = $('#search_for').val();
                var scheme_code = $('#cross_scheme').val();
                var district = $('#district').val();
                // var urban_code = $('#urban_code').val();
                // var block = $('#block').val();
                // var gp_ward = $('#gp_ward').val();
                // var muncid = $('#muncid').val();
                var token = "{{ csrf_token() }}";
                var data = {
                    '_token': token,
                    scheme_code: scheme_code,
                    district: district,
                    // urban_code: urban_code,
                    // block: block,
                    // gp_ward: gp_ward,
                    // $muncid: muncid
                };
                redirectPost('crossSchemeBankDupExcel', data);
            }
        });
    });

    function viewModalFunction(value, scheme_id) {
        $('.loadingDivModal').show();
        $.ajax({
            type: 'POST',
            url: "{{ route('modalViewData') }}",
            data: {
                scheme_id: scheme_id,
                id: value,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('.loadingDivModal').hide();
                if (response.status == 1) {
                    $.alert({
                        title: response.title,
                        type: response.type,
                        icon: response.icon,
                        content: response.msg
                    });
                    $('#submit_btn').trigger('click');
                    $("html, body").animate({
                        scrollTop: 0
                    }, "slow");
                } else {
                    $('#update_scheme_id').val('');
                    $('#pension_id').val('');
                    $('#old_aadhar_no').val('');
                    $('#doc_117').val('');
                    $('#remarks').val('');
                    $('#doc_117').removeClass('has-error');
                    $('#error_doc_117').text('');
                    $('#name_div').text(response.ben_name);
                    $('#father_div').text(response.father_name);
                    $('#mobile_div').text(response.mobile_no);
                    $('#gender_div').text(response.gender);
                    $('#update_scheme_id').val(response.scheme_id);
                    $('#pension_id').val(response.id);
                    $('#application_id').text(response.id);
                    $('#name_jnmp_div').text(response.jnmp_fullname);
                    $('#death_div').text(response.jnmp_date_of_death);
                    var file_msg = '(Image type must be ' + response.doc_type + ' and image size max ' +
                        response.doc_size_kb + ' KB)';
                    $('#file_msg').text(file_msg);
                    $('.loadingDivModal').hide();
                    $('#modalUpdateAadhar').modal('show');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('.loadingDivModal').hide();
                ajax_error(jqXHR, textStatus, errorThrown);
            }
        });
    }

    $(document).on('click', '#verifySubmit', function() {
        var error_doc_117 = '';
        var error_remarks = '';
        var error_reactive_reason = '';
        var remarks = $('#remarks').val();
        var reactive_reason = $('#reactive_reason').val();
        var file_sp = document.getElementById("doc_117");
        var file_attachment = file_sp.files[0];

        if (file_sp.value != '') {
            error_doc_117 = '';
            $('#error_doc_117').text(error_doc_117);
            $('#doc_117').removeClass('has-error');
        } else {
            error_doc_117 = 'Supproting Document is required';
            $('#error_doc_117').text(error_doc_117);
            $('#doc_117').addClass('has-error');
        }

        if (reactive_reason != '') {
            error_reactive_reason = '';
            $('#error_reactive_reason').text(error_reactive_reason);
            $('#reactive_reason').removeClass('has-error');
        } else {
            error_reactive_reason = 'Re-active reason is required.';
            $('#error_reactive_reason').text(error_reactive_reason);
            $('#reactive_reason').addClass('has-error');
        }

        if (remarks != '') {
            error_remarks = '';
            $('#error_remarks').text(error_remarks);
            $('#remarks').removeClass('has-error');
        } else {
            error_remarks = 'Remarks is required.';
            $('#error_remarks').text(error_remarks);
            $('#remarks').addClass('has-error');
        }

        if (error_doc_117 != '' || error_reactive_reason != '' || error_remarks != '') {
            return false;
        } else {
            // alert('OK');
            $.confirm({
                type: 'orange',
                title: 'Confirmation!',
                content: 'Are you want to active this beneficiary ? <br> <span style="color: black;"><b>Note: After activation this beneficiary will started to get payment.</b></span>',
                icon: 'fa fa-warning',
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-blue',
                        keys: ['enter', 'shift'],
                        action: function() {
                            // alert('OK');
                            var beneficiary_Id = $('#pension_id').val();
                            var updateSchemeId = $('#update_scheme_id').val();
                            var reactive_reason = $('#reactive_reason').val();
                            //   alert(updateSchemeId);
                            var remarks = $('#remarks').val();
                            var formData = new FormData();
                            var files = $('#doc_117')[0].files;
                            formData.append('doc_117', files[0]);
                            formData.append('id', beneficiary_Id);
                            formData.append('scheme_id', updateSchemeId);
                            formData.append('reactive_reason', reactive_reason);
                            formData.append('remarks', remarks);
                            formData.append('_token', '{{ csrf_token() }}');
                            $('.loadingDivModal').show();
                            $.ajax({
                                type: 'POST',
                                url: "{{ route('activeBeneficiary') }}",
                                data: formData,
                                dataType: 'json',
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    $('.loadingDivModal').hide();
                                    if (response.status == 1) {
                                        $.alert({
                                            title: response.title,
                                            type: response.type,
                                            icon: response.icon,
                                            content: response.msg
                                        });
                                        $('#modalUpdateAadhar').modal('hide');
                                        $('#res_div').hide();
                                        // $('#scheme_type').val('').trigger('change');
                                        $('#submit_btn').trigger('click');
                                        $("html, body").animate({
                                            scrollTop: 0
                                        }, "slow");
                                    } else {
                                        var html = '';
                                        html += '<ul>';
                                        if (Array.isArray(response.msg)) {
                                            $.each(response.msg, function(key, value) {
                                                html += '<li>' + value +
                                                '</li>';
                                            });
                                        } else {
                                            html = '<li>' + response.msg + '</li>';
                                        }
                                        html += '<ul>';
                                        $.alert({
                                            title: response.title,
                                            type: response.type,
                                            icon: response.icon,
                                            content: html
                                        });
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    $('.loadingDivModal').hide();
                                    ajax_error(jqXHR, textStatus, errorThrown);
                                }
                            });
                        }
                    },
                    cancel: function() {},
                }
            });
        }
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
