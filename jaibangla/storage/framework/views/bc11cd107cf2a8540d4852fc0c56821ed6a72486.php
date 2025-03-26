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

<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <!-- <div class="preloader1"><img src="<?php echo e(asset('images/ZKZg.gif')); ?>" width="150px" id="loader_img"></div> -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Cross Scheme Duplicate Bank Account Beneficiary List
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
                                    <?php if($message = Session::get('success')): ?>
                                        <div class="alert alert-success alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong><?php echo e($message); ?> </strong>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($message = Session::get('message')): ?>
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong><?php echo e($message); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($message = Session::get('msg1')): ?>
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong><?php echo e($message); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                        <div class="col-md-4">
                                                <label class=" control-label">Scheme <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="scheme" id='scheme' required>
                                                    <option value="">-- Select Scheme --</option>
                                                    <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($scheme->id); ?>"><?php echo e($scheme->scheme_name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="20">Lakshmir Bhandar</option>
                                                </select>
                                                </select>
                                                <span class="text-danger" id="error_scheme"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <label class=" control-label">Cross Scheme For <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="cross_scheme" id='cross_scheme' required>
                                                <option value="">-- Select Cross Scheme --</option>
                                                <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($scheme->id); ?>"><?php echo e($scheme->scheme_name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="20">Lakshmir Bhandar</option>
                                                </select>
                                                <span class="text-danger" id="error_cross_scheme"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <label class=" control-label">Filter <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="aadhar_filter" id='aadhar_filter' required>
                                                    <option value="2">Bank and Aadhar both same</option>
                                                    <option value="1">Bank Same but Aadhar not same</option>
                                                </select>
                                                <span class="text-danger" id="error_aadhar_filter"></span>
                                            </div>
                                            <?php if($district_visible): ?>
                                                <div class="form-group col-md-4">
                                                    <label class="">District <span
                                                        class="text-danger">*</span></label>
                                                    <select name="district" id="district" class="form-control"
                                                        tabindex="6">
                                                        <option value="">--All --</option>
                                                        <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($district->district_code); ?>"
                                                                <?php echo e(old('district') == $district->district_code ? 'selected' : ''); ?>>
                                                                <?php echo e($district->district_name); ?>

                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                    <span id="error_district" class="text-danger"></span>
                                                </div>
                                            <?php else: ?>
                                                <input type="hidden" name="district" id="district"
                                                    value="<?php echo e($district_code_fk); ?>" />
                                            <?php endif; ?>

                                            <?php if($is_urban_visible): ?>
                                                <div class="form-group col-md-4" id="divUrbanCode">
                                                    <label class="">Rural/ Urban</label>
                                                    <select name="urban_code" id="urban_code" class="form-control"
                                                        tabindex="11">
                                                        <option value="">--All --</option>
                                                        <?php $__currentLoopData = Config::get('constants.rural_urban'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($key); ?>"
                                                                <?php if(old('urban_code') == $key): ?> selected <?php endif; ?>>
                                                                <?php echo e($val); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                    <span id="error_urban_code" class="text-danger"></span>
                                                </div>
                                            <?php else: ?>
                                                <input type="hidden" name="urban_code" id="urban_code"
                                                    value="<?php echo e($rural_urban_fk); ?>" />
                                            <?php endif; ?>

                                            <?php if($block_visible): ?>
                                                <div class="form-group col-md-4" id="divBodyCode">
                                                    <label class="" id="blk_sub_txt">Block/Sub Division</label>
                                                    <select name="block" id="block" class="form-control"
                                                        tabindex="16">
                                                        <option value="">--All --</option>
                                                    </select>
                                                    <span id="error_block" class="text-danger"></span>
                                                </div>
                                            <?php else: ?>
                                                <input type="hidden" name="block" id="block"
                                                    value="<?php echo e($block_munc_corp_code_fk); ?>" />
                                            <?php endif; ?>

                                            <div class="form-group col-md-4" id="municipality_div" 
                                                <?php if(!$municipality_visible): ?> style="display: none;" <?php endif; ?>>
                                                <label>Municipality</label>
                                                <select name="muncid" id="muncid" class="form-control" tabindex="16">
                                                    <option value="">-- All --</option>
                                                    <?php if(!empty($muncList) && count($muncList) > 0): ?>
                                                        <?php $__currentLoopData = $muncList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $munc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($munc->urban_body_code); ?>">
                                                                <?php echo e($munc->urban_body_name); ?>

                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php else: ?>
                                                        <option value="">No municipalities available</option>
                                                    <?php endif; ?>
                                                </select>
                                                <span id="error_muncid" class="text-danger"></span>
                                            </div>


                                                                                            <div class="form-group col-md-4" id="gp_ward_div" 
                                                    style="<?php echo e(isset($gp_ward_visible) && $gp_ward_visible ? '' : 'display: none;'); ?>">
                                                    <label id="gp_ward_txt">GP/Ward</label>
                                                    <select name="gp_ward" id="gp_ward" class="form-control" tabindex="17">
                                                        <option value="">-- All --</option>
                                                        <?php if(!empty($gpList) && count($gpList) > 0): ?>
                                                            <?php $__currentLoopData = $gpList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($gp->gram_panchyat_code); ?>" 
                                                                    <?php echo e(old('gp_ward') == $gp->gram_panchyat_code ? 'selected' : ''); ?>>
                                                                    <?php echo e($gp->gram_panchyat_name); ?>

                                                                </option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php else: ?>
                                                            <option value="">No GP/Ward available</option>
                                                        <?php endif; ?>
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
                                                <button class="btn btn-info" name="excel_btn" id="excel_btn" type="button"><i class="fa fa-file-excel-o"></i> Export To Excel</button>
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
                                            <th>Aadhaar</th>
                                            <th>Payment Status</th>
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
            <div class="modal fade  ben_view_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">Beneficiary</h4>
                        </div>
                        <div class="modal-body">
                            <h5 class="body-note w-100" style="text-align: center; align-content: center; color:black;" ><span id="source"></span></h5>
                            <h4 class="body-title w-100" style="text-align: center; align-content: center; color:red;" >Beneficiary ID: <span id="beneficiary_id">?</span></h4>	
                            <h5 class="body-note w-100" style="text-align: center; align-content: center; color:rgb(0, 60, 255);" ><span id="note"></span></h5>
                            <form method="POST" action="#" target="_blank" name="fullForm" id="fullForm" style="text-align: center; align-content: center;">
                                <div class="panel-group"> 
                                    <div class="panel panel-default">
                                        <div class="panel-body" style="padding: 5px;"> 
                                            <div class="form-group col-md-2">
                                                <label class="" for="heading">Enter Remarks:</label>
                                                <textarea style="margin: 0px; width: 300px; height: 60px;" name="accept_reject_comments" id="accept_reject_comments" class="form-control" maxlength="100"></textarea>
                                            </div>
                                            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                            <input type="hidden" name="ben_id" id="ben_id"  />
                                            <input type="hidden" id="scheme_id" name="scheme_id"/>
                                            <input type="hidden" id="type" name="type"/>
                                            
                                        </div>
                                    </div>
                                </div>    
                                <button type="button" class="btn btn-info" id="verifyReject"> </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button style="display:none;" type="button" id="submitting" value="Submit" class="btn btn-success success" disabled>Processing Please Wait</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
<?php $__env->stopSection(); ?>
<script src="<?php echo e(asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/master-data-v2.js')); ?>"></script>
<script>
    $(document).ready(function() {
        // Live Clock
        var interval = setInterval(function() {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);
        var error_cross_scheme = '';
        var error_scheme = '';
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

        $('#cross_scheme').attr('disabled', true);
        var originalOptions = $('#cross_scheme').html();

        $('#scheme').change(function () {
        if ($('#scheme').val().length != 0) {
            $('#cross_scheme').prop('disabled', false); 

            // Restore original options before removing specific ones
            $('#cross_scheme').html(originalOptions);

            var scheme_id = $(this).val();
            if (scheme_id == 1) {
                $('#cross_scheme option[value="1"]').remove();
                $('#cross_scheme option[value="2"]').remove();
                $('#cross_scheme option[value="11"]').remove();
                $('#cross_scheme option[value="20"]').remove();
            } else if (scheme_id == 3) {
                $('#cross_scheme option[value="3"]').remove();
                $('#cross_scheme option[value="2"]').remove();
                $('#cross_scheme option[value="11"]').remove();
                $('#cross_scheme option[value="20"]').remove();
            } else if (scheme_id == 10) {
                $('#cross_scheme option[value="2"]').remove();
                $('#cross_scheme option[value="11"]').remove();
                $('#cross_scheme option[value="10"]').remove();
            } else if (scheme_id == 11) {
                $('#cross_scheme option[value="11"]').remove();
            } else if (scheme_id == 20) { // Fixed typo
                $('#cross_scheme option[value="20"]').remove();
            }
        } else {
            $('#cross_scheme').prop('disabled', true);
             error_scheme = 'Scheme name is required';
            $('#error_scheme').text(error_scheme);
        }
    });



        $('#submit_btn').click(function() {
           // Validate Cross Scheme
            if ($.trim($('#cross_scheme').val()).length == 0) {
                error_cross_scheme = 'Cross Scheme  is required';
                $('#error_cross_scheme').text(error_cross_scheme);
            } else {
                $('#error_cross_scheme').text('');
            }

            // Validate Scheme
            if ($.trim($('#scheme').val()).length == 0) {
                error_scheme = 'Scheme  is required';
                $('#error_scheme').text(error_scheme); // Fixed typo
            } else {
                $('#error_scheme').text('');
            }

            // If there are errors, prevent form submission
            if (error_cross_scheme !== '' || error_scheme !== '') {
                return false;
            } else {
                $('#loadingDiv').show();
                $('#search_details').show();
                // $(':input[type="button"]').prop('disabled', false);

                var scheme_code = $('#cross_scheme').val();
                var gp_ward = $('#gp_ward').val();
                var muncid = $('#muncid').val();
                var aadhar_filter = $('#aadhar_filter').val();
                if ($.fn.DataTable.isDataTable('#example')) {
                    $('#example').DataTable().destroy();
                }
                var table = $('#example').DataTable({
                    dom: 'lfrtip',
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
                        url: "<?php echo e(route('crossSchemeBankDupVerifierList')); ?>",
                        type: "post",
                        data: function(d) {
                            d.gp_ward = gp_ward,
                            d.cross_scheme = $('#cross_scheme').val(),
                            d.scheme = $('#scheme').val(),
                            d.muncid = $('#muncid').val();
                            d.aadhar_filter = aadhar_filter,
                            d._token = "<?php echo e(csrf_token()); ?>"
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
                        },
                        {
                            "data": "payment_status"
                        },
                        {
                            "data": "action"
                        }
                    ]
                   
                });
            }
        });
            $(document).on('click', '.ben_view_details', function() {
                var benid=$(this).val();
                $('.ben_view_body').addClass('disabledcontent');
                $.ajax({
                type: 'post',
                url: "<?php echo e(route('cross-scheme-reject-pause-resume-view')); ?>",
                data: {_token:'<?php echo e(csrf_token()); ?>', 
                benid:benid,
                },
                    dataType: 'json',
                    success: function (response) {
                    //   console.log(JSON.stringify(response));
                    $('#beneficiary_id').text(response.beneficiary_id);
                    $('#ben_id').val(response.beneficiary_id);
                    $('#type').val(response.type);
                    $('#scheme_id').val(response.scheme_id);
                    var type = response.type;
                    var schemes_id = response.scheme_id;
                    var modalMessage = ''; 
                    var buttonText = '';
                    var note = '';
                    var source = '';
                    if (type == 1) {
                        modalMessage = 'Beneficiary Rejection';
                        buttonText = 'Reject';
                        if(schemes_id == 20){
                            source='(Applicant Source: Lakshmir Bhandar)';
                            // note='(NOTE: Please check/verify before Rejecting. If resume then the beneficiary payment will be re-opened.)';
                        }else{
                            source='(Applicant Source: Jai Bangla)';
                            // note='(NOTE: Please check/verify before resuming. If resume then the beneficiary payment will be re-opened.)';
                        }
                    } else if (type == 2) {
                        modalMessage = 'Beneficiary Resume';
                        buttonText = 'Resume';
                        if(schemes_id == 20){
                            source='(Applicant Source: Lakshmir Bhandar)';
                            note='(NOTE: Please check/verify before resuming. If resume then the beneficiary payment will be re-opened.)';
                        }else{
                            source='(Applicant Source: Jai Bangla)';
                            note='(NOTE: Please check/verify before resuming. If resume then the beneficiary payment will be re-opened.)';
                        }
                    } else if (type == 3) {
                        modalMessage = 'Beneficiary Pause';
                        buttonText = 'Pause';
                        if(schemes_id == 20){
                            source='(Applicant Source: Lakshmir Bhandar)';
                            note='(NOTE: Please check/verify before pausing. If pause then the beneficiary payment will be stopped.)';
                        }else{
                            source='(Applicant Source: Jai Bangla)';
                            note='(NOTE: Please check/verify before pausing. If pause then the beneficiary payment will be stopped.)';
                        }
                    }
                    $('.modal-title').text(modalMessage);
                    $('#verifyReject').text(buttonText);
                    $('#note').text(note);
                    $('#source').text(source);
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

           $(document).on('click', '#verifyReject', function() {   
        //    alert('pkfm');
            var accept_reject_comments = $('#accept_reject_comments').val();
            var beneficiary_id = $('#ben_id').val();
            var scheme_id = $('#scheme_id').val();
            var type = $('#type').val();
            $.confirm({
            title: 'Warning',
            type: 'orange',
            icon: 'fa fa-warning',
            content: '<strong>Are you sure to proceed?</strong>',
            buttons: {
            Ok: function(){
              $("#submitting").show();
              $("#verifyReject").hide();
              $.ajax({
                type: 'POST',
                url: "<?php echo e(url('cross-scheme-reject-pause-resume-post')); ?>",
                data: {
                    beneficiary_id: beneficiary_id,
                    scheme_id: scheme_id,
                    accept_reject_comments: accept_reject_comments,
                    type:type,
                  _token: '<?php echo e(csrf_token()); ?>',
                },
                success: function (data) {
                  // console.log(data);
                  //console.log(JSON.stringify(data));
                 // dataTable.ajax.reload();
                 var table_renew = $('#example').DataTable(); 
                 table_renew.ajax.reload( null, false );
                 $('#accept_reject_comments').val('');
                  //$('#example').DataTable().ajax.reload()
                  if(data.status==1){
                    $('.ben_view_modal').modal('hide');
                    $('#approve_rejdiv').hide();
                    $.confirm({
                      title: 'Success',
                      type: 'green',
                      icon: 'fa fa-check',
                      content: data.msg,
                      buttons: {
                        Ok: function(){
                          $("#submitting").hide();
                          $("#verifyReject").show();
                          $("html, body").animate({ scrollTop: 0 }, "slow");
                        }
                      }
                    });
                  }
                  else{
                    $("#submitting").hide();
                    $("#verifyReject").show();
                    $('.ben_view_modal').modal('hide');
                    $('#approve_rejdiv').hide();
                    $.alert({
                      title: 'Error',
                      type: 'red',
                      icon: 'fa fa-warning',
                      content: data.msg
                    });
                  }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                  $.confirm({
                    title: 'Error',
                    type: 'red',
                    icon: 'fa fa-warning',
                    content: 'Something went wrong in the approval!!',
                    buttons: {
                      Ok: function(){
                       // $("#verifyReject").show();
                      //  $("#submitting").hide();
                        location.reload();
                      }
                    }
                  });
                }           
              });
            },
            Cancel: function () {
              // $("#verifyReject").show();  // Re-enable reject button
              // $("#submitting").hide(); 
            },
          }
        });      
    });
      
        // Export Excel
        $('#excel_btn').click(function() {
            // alert('ok');
            var error_cross_scheme = '';
            var error_aadhar_filter = '';
            if ($.trim($('#cross_scheme').val()).length == 0) {
                error_cross_scheme = 'Scheme name is required';
                $('#error_cross_scheme').text(error_cross_scheme);
            } else {
                error_cross_scheme = '';
                $('#error_cross_scheme').text(error_cross_scheme);
            }
            if ($.trim($('#aadhar_filter').val()).length == 0) {
                error_aadhar_filter = 'Aadhar filter is required';
                $('#error_aadhar_filter').text(error_aadhar_filter);
            } else {
                error_aadhar_filter = '';
                $('#error_aadhar_filter').text(error_aadhar_filter);
            }
            if (error_cross_scheme != '' || error_aadhar_filter != '') {
                return false;
            } else {
                var cross_scheme = $('#cross_scheme').val();
                var aadhar_filter = $('#aadhar_filter').val();
                var gp_ward = $('#gp_ward').val();
                var token = "<?php echo e(csrf_token()); ?>";
                var data = {
                    '_token': token,
                    cross_scheme: cross_scheme,
                    aadhar_filter: aadhar_filter,
                    gp_ward : gp_ward
                };
                redirectPost('cross-scheme-dup-list-download', data);
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

<?php echo $__env->make('layouts.app-template-datatable_new', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>