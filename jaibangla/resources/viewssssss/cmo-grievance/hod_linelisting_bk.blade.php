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
        <section class="content-header">
            <h1>
                Sarasori Mukhyamantri (CMO Grievance) for Approved Beneficiary List 
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
                        <div class="panel-heading" style="font-size: 15px; font-weight: bold; font-style: italic;"><span id="panel-icon">Enter Filter Criteria</div>
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
                                                <label class=" control-label">Scheme <span class="text-danger">*</span></label>
                                                <select class="form-control" name="scheme_id" id='scheme_id' required>
                                                    <option value="">--Select Scheme--</option>
                                                    @foreach ($schemes as $scheme)
                                                        <option value="{{ $scheme->id }}">{{ $scheme->scheme_name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="error_scheme_id"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <label class=" control-label">Operation Type <span class="text-danger">*</span></label>
                                                <select class="form-control" name="operation_type" id='operation_type' required>
                                                    <option value="">--Select Operation Type--</option>
                                                    <option value="1">Pending</option>
                                                    <option value="2">Verified But Not Approved</option>
                                                    <option value="3">Verified & Approved</option>
                                                </select>
                                                <span class="text-danger" id="error_operation_type"></span>
                                            </div>
                                            @if ($district_visible)
                                                <div class="form-group col-md-4">
                                                    <label class="">District <span class="text-danger">*</span></label>
                                                    <select name="district" id="district" class="form-control" tabindex="6">
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
                                                    <select name="urban_code" id="urban_code" class="form-control" tabindex="11">
                                                        <option value="">--All --</option>
                                                            @foreach (Config::get('constants.rural_urban') as $key => $val)
                                                                <option value="{{ $key }}" @if (old('urban_code') == $key) selected @endif>
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

                                           {{-- <div class="form-group col-md-4" id="municipality_div"
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
                                            </div> --}}

                                        </div>
                                    </div>
                                    <div class="row">
                                        <center>
                                            <div>
                                                <button class="btn btn-primary" name="submit_btn" id="submit_btn"
                                                    type="button" disabled><i class="fa fa-search"></i>
                                                    Search</button>&nbsp;
                                                {{-- <button class="btn btn-info" name="excel_btn" id="excel_btn" type="button"><i class="fa fa-file-excel-o"></i> Export To Excel</button> --}}
                                            </div>
                                        </center>
                                    </div>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="form-group col-md-offset-4 col-md-3 " style="display: none;" id="approve_rejdiv">
                                  <button type="button" name="bulk_approve" class="btn btn-success btn-lg" id="bulk_approve" value="approve">
                                    Approve
                                  </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="search_details" style="display: none;">
                        <div class="panel panel-default">
                            <div class="panel-heading" id="heading_msg" style="font-size: 15px; font-weight: bold; font-style: italic;">List of Beneficiary</div>
                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 14px;">
                                        <thead>
                                            <th>Grevience ID</th>
                                            <th>JB Beneficiary ID</th>
                                            <th>Caller Name</th>
                                            <th>Beneficiary Name</th>
                                            <th>Caller Mobile No.</th>
                                            <th>JB Beneficiary Mobile No.</th>
                                            <th>JB Address</th>
                                            <th>CMO Address</th>
                                            <th>Action</th>
                                            <th>Check</th>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade bd-example-modal-lg ben_view_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">Sarasori Mukhyamantri (CMO Grievance) Approved Beneficiary Details</h4>
                        </div>
                        <div class="modal-body ben_view_body">
                            <p id="header_message" style="text-align: center; align-content: center; font-size: 15px; font-weight: bold;" class="text-success"></p>
                            <div class="panel-group singleInfo" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-default">
                                    <div class="panel-heading active" role="tab" id="personal">
                                        <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" class="loader_img" width="150px" id="loader_img_personal"></div>
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapsePersonal" aria-expanded="true" aria-controls="collapsePersonal">CMO Grivance Details (ID: <span class="grivance_id_modal"></span>)</a> 
                                        </h4> 
                                    </div> 
                                    <div id="collapsePersonal" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="personal">  
                                        <div class="panel-body" style="padding: 5px;">
                                            <div class="row">
                                                <div class="col-md-12" style="margin-bottom: 10px;">
                                                    <div class="col-md-4">
                                                        <strong>Caller Name : </strong>
                                                        <span style="font-size: 14px;" id='caller_name'></span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong>Caller Mobile No. : </strong>
                                                        <span style="font-size: 14px;" id='cmo_mobile_no'></span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong>Grivance Age: </strong>
                                                        <span style="font-size: 14px;" id='cmo_age'></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="margin-bottom: 10px;">
                                                    <div class="col-md-4">
                                                        <strong>Grivance District : </strong>
                                                        <span style="font-size: 14px;" id='cmo_dist_name'></span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong>Grivance Block/Municipality : </strong>
                                                        <span style="font-size: 14px;" id='cmo_block_ulb_name'></span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong>Complain Date: </strong>
                                                        <span style="font-size: 14px;" id='complain_date'></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="margin-bottom: 10px;">
                                                    <div class="col-md-12">
                                                        <strong>Complain Description: </strong>
                                                        <span style="font-size: 14px;" id='complain_description'></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="margin-bottom: 10px;">
                                                    <div class="col-md-12">
                                                        <strong>Verifier Process with ATR: </strong>
                                                        <span style="font-size: 14px;" id='atr'></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12" style="margin-bottom: 10px;">
                                                    <div class="col-md-12">
                                                        <strong>Remarks: </strong>
                                                        <span style="font-size: 14px;" id='remarks'></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                            <div class="panel-group singleInfo" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-default">
                                    <div class="panel-heading active" role="tab" id="banking">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseBank" aria-expanded="true" aria-controls="collapseBank" id="panel_bank_name_text">Beneficiary Details</a> 
                                        </h4> 
                                    </div> 
                                    <div id="collapseBank" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="banking">  
                                        <div class="panel-body" style="padding: 5px;">
                                            <table class="table table-bordered table-condensed" style="font-size: 14px;">  
                                                <tbody>
                                                    <tr>
                                                        <th scope="row" width="20%">Beneficiary ID</th>
                                                        <td id='ben_id' width="30%"></td>
                                                        <th scope="row" width="20%">Beneficiary Name</th>         
                                                        <td id="ben_name" width="30%"></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row" width="20%">Mobile No.</th>         
                                                        <td id="jb_mobile" width="30%"></td> 
                                                        <th scope="row" width="20%">Caste</th>         
                                                        <td id='jb_caste' width="30%"></td>         
                                                    </tr>
                                                    <tr>       
                                                        <th scope="row" width="20%">District Name</th>
                                                        <td id='jb_dist_name' width="30%"></td>
                                                        <th scope="row" width="20%">Block/Municipality Name</th>         
                                                        <td id='jb_block_ulb_name' width="30%"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                            <div class="panel-group">  
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingFour">   
                                        <h4 class="panel-title"> <a>Action</a> </h4> 
                                    </div> 
                                    <div id="collapse4" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingFour">  
                                        <div class="panel-body" style="padding: 5px;"> 
                                            <div class="form-group col-md-4">
                                                <label for="opreation_type">Select Operation<span class="text-danger"> *</span></label>
                                                <select name="opreation_type" id="opreation_type" class="form-control opreation_type">
                                                    <option value="A" selected>Approve</option>
                                                    <option value="T">Revert</option>  
                                                </select>
                                            </div> 
                                            <div class="form-group col-md-4" style="display:none;" id="div_rejection">
                                                <label for="reject_cause">Select Reverted Cause<span class="text-danger"> *</span></label>
                                                <select name="reject_cause" id="reject_cause" class="form-control">
                                                    <option value="">--Select--</option>
                                                    <option value="Banking informtion">Banking informtion</option>
                                                </select>
                                            </div> 
                                        <div class="form-group col-md-4">
                                            <label class="" for="heading">Enter Remarks</label>
                                            <textarea style="margin: 0px; width: 279px; height: 40px;" name="accept_reject_comments" id="accept_reject_comments" class="form-control" maxlength="100"></textarea>
                                        </div>
                                    </div> 
                                </div> 
                            </div>  
                        </div>
                        <form method="POST" action="#" target="_blank" name="fullForm" id="fullForm" style="text-align: center; align-content: center;">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="is_bulk" id="is_bulk" value="0" />
                            <input type="hidden" id="id" name="id"/>
                            <input type="hidden" id="application_id" name="application_id"/>
                            <input type="hidden" name="applicantId[]" id="applicantId" value="" />
                            <button type="button" class="btn btn-success btn-lg" id="verifyReject">Approve</button>
                            <button style="display:none;" type="button" id="submitting" value="Submit" class="btn btn-success success" disabled>Processing Please Wait</button>
                        </form> 
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection

  <!-- End Revert Model -->
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
        $('#bulk_revert').hide();
        $('#bulk_approve').hide();
        $('#submittingapprove').hide();
        $('#submit_btn').removeAttr('disabled');
        $('#reset_btn').removeAttr('disabled');
        $('#opreation_type').val('A');
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
        var error_operation_type = '';
        var error_district = '';
        $('#submit_btn').click(function() {

            if ($.trim($('#scheme_id').val()).length == 0) {
                error_scheme_id = 'Scheme name is required';
                $('#error_scheme_id').text(error_scheme_id);
            } else {
                error_scheme_id = '';
                $('#error_scheme_id').text(error_scheme_id);
            }
        
            if ($.trim($('#operation_type').val()).length == 0) {
                error_operation_type = 'Operation Type is required';
                $('#error_operation_type').text(error_operation_type);
            } else {
                error_operation_type = '';
                $('#error_operation_type').text(error_operation_type);
            }

            if ($.trim($('#district').val()).length == 0) {
                error_district = 'District is required';
                $('#error_district').text(error_district);
            } else {
                error_district = '';
                $('#error_district').text(error_district);
            }
            if (error_scheme_id != '' || error_district != '') {
                return false;
            } else {
                $('#loadingDiv').show();
                $('#search_details').show();
                // $(':input[type="button"]').prop('disabled', false);

                var operation_type = $('#operation_type').val();
                var scheme_code = $('#scheme_id').val();
                var district = $('#district').val();
                var urban_code = $('#urban_code').val();
                var block = $('#block').val();
                var gp_ward = $('#gp_ward').val();
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
                        [10, 20, 25, 50, 100],
                        [10, 20, 25, 50, 100]
                    ],
                    "serverSide": true,
                    "processing": true,
                    "bRetrieve": true,
                    "oLanguage": {
                        "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
                    },
                    "ajax": {
                        url: "{{ route('cmo-grivance-hod-listing') }}",
                        type: "post",
                        data: function(d) {
                            d.scheme_code = scheme_code,
                                d.district = district,
                                d.scheme_code = $('#scheme_id').val(),
                                d.operation_type = $('#operation_type').val(),
                                d.urban_code = $('#urban_code').val(),
                                d.block = $('#block').val(),
                                d.gp_ward = $('#gp_ward').val(),
                                d.muncid = $('#muncid').val(),
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
                        $('#confirm_yes').on('click',function(){
                            $("#confirm_yes").hide();
                            $("#submittingapprove").show();
                            $("#revert_form").submit();
                            
                        
                        });
                       
                    },
                    "columns": [
                        {
                            "data": "grivance_id"
                        },
                        {
                            "data": "id"
                        },
                        {
                            "data": "caller_name"
                        },
                        {
                            "data": "name"
                        },
                        {
                            "data": "grivance_mobile"
                        },
                        {
                            "data": "mobile_no"
                        },
                        {
                            "data": "address"
                        },
                        {
                            "data": "cmo_address"
                        },
                        {
                            "data": "action"
                        },  
                        {
                            "data": "check"
                        },   
                    ],
                    "buttons": [
                        {
                           extend: 'pdf',
                           footer: true,
                           pageSize:'A4',
                           //orientation: 'landscape',
                           pageMargins: [ 40, 60, 40, 60 ],
                           exportOptions: {
                                columns: [0,1,2,3,4,5,6,7,8,9],

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
                table.on('click','.ben_revert_button',function(){
                    // alert('Hi');
                $tr = $(this).closest('tr');
                if(($tr).hasClass('child')){
                    $tr = $tr.prev('parent');
                }
                var data = table.row($tr).data();
                $('#revert_beneficiary_id').val(data['id']);
                $('#revert_ben_id').html(data['id']);
                $('#revert_ben_name').html(data['ben_name']);
                $('#revert_app_mobile_no').html(data['mobile_no']);
                $('#revert_smo_mobile_no').html(data['sm_mobile_no']);
                $('#ben_revert_modal').modal('show');
                });
            }
        });

        $(document).on('click', '.ben_view_button', function() {
        $('#loader_img_personal').show();
        $('.ben_view_button').attr('disabled',true);
        var val=$(this).val();
        var array = val.split("_");
        var grivance_id = array[0];
        var scheme_id = array[1];
        $("#fullForm #is_bulk").val(0);
        $('#opreation_type').val('A').trigger('change');

        $("#verifyReject").html("Approve");
        $('#div_rejection').hide();
        $(".singleInfo").show();
        $('.applicant_id_modal').html('');
        $('#accept_reject_comments').val('');
        $("#collapseBank").collapse('hide');
        $('#collapsePersonal').collapse('hide');
        $('.ben_view_body').addClass('disabledcontent');
        var name_validation_type=$('#update_code').val();
        $.ajax({
          type: 'post',
          url: "{{route('cmo-grivance-hod-view')}}",
          data: {_token:'{{csrf_token()}}', 
          grivance_id:grivance_id,scheme_id:scheme_id
        },
          dataType: 'json',
          success: function (response) {
            //  console.log(JSON.stringify(response));
            $('.grivance_id_modal').text(response.grivance_id);
            $('#caller_name').text(response.caller_name);
            $('#cmo_mobile_no').text(response.grivance_mobile);
            $('#cmo_age').text(response.cmo_age);
            $('#complain_description').text(response.complain_description);
            $('#cmo_dist_name').text(response.cmo_dist_name);  
            $('#cmo_block_ulb_name').text(response.cmo_block_name);
            $('#complain_date').text(response.complain_date);
            $('#atr').text(response.atr);
            $('#remarks').text(response.remarks);
            $('#ben_id').text(response.jb_id);  
            $('#ben_name').text(response.jb_name);
            $('#jb_mobile').text(response.jb_mobile);
            $('#jb_caste').text(response.jb_caste);
            $('#jb_dist_name').text(response.jb_dist_name);
            $('#jb_block_ulb_name').text(response.jb_block_ulb_name);
            $('.ben_view_body').removeClass('disabledcontent');
            $("#collapseBank").collapse('show');
            $('#loader_img_personal').hide();
            $('.ben_view_button').removeAttr('disabled',true);
            $('.applicant_id_modal').html('(Beneficiary ID - '+response.id+' )');
            $('#fullForm #id').val(response.id);
            $('#header_message').text(response.header_msg);
            
            if(response.av_name_response == ''){
              $('#av_name_response').text('No name response from bank');
            }else{
              $('#av_name_response').text(response.av_name_response);
            }
          },
          complete: function(){
          },
          error: function (jqXHR, textStatus, errorThrown) {
            $('.ben_view_body').removeClass('disabledcontent');
            $('#loader_img_personal').hide();
            $('.ben_view_button').removeAttr('disabled',true);
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
        $('.ben_view_modal').modal('show');
      });

        $('#revert_Button').click(function(e){
        e.preventDefault();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '{{ url('cmo-grivance-hod-revert') }}',
            data: {
            ben_id: $('#revert_beneficiary_id').val(),
            scheme_id: $('#scheme_id').val(),
            _token: '{{ csrf_token() }}',
            },
            success: function (data) {
                if(data.return_status){
                    if(data.return_msg){
                    printMsg(data.return_msg,'0','errorDiv');
                    //console.log(data.session_lb_lifecertificate.is_error);
                    }
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    $('#example').DataTable().ajax.reload();
                }else{
          
                   printMsg(data.return_msg,'0','errorDiv');
                  $("html, body").animate({ scrollTop: 0 }, "slow");
                   return false;
        }
           
            },
            error: function (ex) {
             alert(sessiontimeoutmessage);
             window.location.href=base_url;
            }
        });
        });

        $('#revert_Button').click(function(e){
        e.preventDefault();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '{{ url('cmo-grivance-hod-revert') }}',
            data: {
            ben_id: $('#revert_beneficiary_id').val(),
            scheme_id: $('#scheme_id').val(),
            _token: '{{ csrf_token() }}',
            },
            success: function (data) {
                if(data.return_status){
                    if(data.return_msg){
                    printMsg(data.return_msg,'0','errorDiv');
                    //console.log(data.session_lb_lifecertificate.is_error);
                    }
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    $('#example').DataTable().ajax.reload();
                }else{
          
                   printMsg(data.return_msg,'0','errorDiv');
                  $("html, body").animate({ scrollTop: 0 }, "slow");
                   return false;
        }
           
            },
            error: function (ex) {
             alert(sessiontimeoutmessage);
             window.location.href=base_url;
            }
        });
        });

        // Export Excel
        $('#excel_btn').click(function() {
            var error_scheme_id = '';
            var error_district = '';
            if ($.trim($('#scheme_id').val()).length == 0) {
                error_scheme_id = 'Scheme name is required';
                $('#error_scheme_id').text(error_scheme_id);
            } else {
                error_scheme_id = '';
                $('#error_scheme_id').text(error_scheme_id);
            }
            if ($.trim($('#district').val()).length == 0) {
                error_district = 'District is required';
                $('#error_district').text(error_district);
            } else {
                error_district = '';
                $('#error_district').text(error_district);
            }
            if (error_scheme_id != ''  || error_district != '') {
                return false;
            } else {
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
                redirectPost('sm-cmoMisReportlistExcel', data);
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
    function controlCheckBox(){
    //console.log('ok');
    var anyBoxesChecked = false;
    $(' input[type="checkbox"]').each(function() {
      if ($(this).is(":checked")) {
        anyBoxesChecked = true;
      }
    });
    if (anyBoxesChecked == true) {
      $("#bulk_revert").show();
      document.getElementById('bulk_revert').disabled = false;
    } else{
      $("#bulk_revert").hide();
      document.getElementById('bulk_revert').disabled = true;
    }
  }
  function closeError(divId){
   $('#'+divId).hide();
  }
</script>
