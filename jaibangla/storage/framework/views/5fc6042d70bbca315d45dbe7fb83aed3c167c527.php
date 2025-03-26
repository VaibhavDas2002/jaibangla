<!DOCTYPE html>


<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>JB | Jai Bangla</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link href="<?php echo e(asset('/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo e(asset('css/select2.min.css')); ?>" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link href="<?php echo e(asset('/bower_components/AdminLTE/dist/css/AdminLTE.min.css')); ?>" rel="stylesheet" type="text/css" />

    <link href="<?php echo e(asset('/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css')); ?>" rel="stylesheet"
        type="text/css" />


    <link href="<?php echo e(asset('/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css')); ?>" rel="stylesheet"
        type="text/css" />

    <style>
        .box {
            width: 800px;
            margin: 0 auto;
        }

        .active_tab1 {
            background-color: #fff;
            color: #333;
            font-weight: 600;
        }

        .inactive_tab1 {
            background-color: #f5f5f5;
            color: #333;
            cursor: not-allowed;
        }

        .has-error {
            border-color: #cc0000;
            background-color: #ffff99;
        }

        .select2 {
            width: 100% !important;
        }

        .select2 .has-error {
            border-color: #cc0000;
            background-color: #ffff99;
        }

        .modal_field_name {
            float: left;
            font-weight: 700;
            margin-right: 1%;
            padding-top: 1%;
            margin-top: 1%;
        }

        .modal_field_value {
            margin-right: 1%;
            padding-top: 1%;
            margin-top: 1%;
        }

        .row {
            margin-right: 0px !important;
            margin-left: 0px !important;
            margin-top: 1% !important;
        }

        .section1 {
            border: 1.5px solid #9187878c;
            margin: 2%;
            padding: 2%;
        }

        .color1 {
            margin: 0% !important;
            background-color: #5f9ea061;
        }

        .modal-header {
            background-color: #7fffd4;
        }

        .required-field::after {
            content: "*";
            color: red;
        }

        .imageSize {
            font-size: 9px;
            color: #333;
        }

        #divScrool {
            overflow-x: scroll;
        }
    </style>


</head>


<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <!-- Main Header -->
        <?php echo $__env->make('layouts.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <!-- Sidebar -->
        <?php echo $__env->make('layouts.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div> <!-- class="box box-primary" -->
                            <div class="box-header with-border">
                                <h3 class="box-title"><b>

                                   Incomplete Details MIS Report

                                    </b></h3>
                                <!-- <p><h3 class="box-title"><b>Bandhu Prakalpa (for SC)</b></h3></p> -->
                            </div>

                            <div>
                                <?php if(($message = Session::get('success')) && ($id = Session::get('id'))): ?>
                                    <div class="alert alert-success alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong><?php echo e($message); ?> with Application ID: <?php echo e($id); ?></strong>


                                    </div>
                                <?php endif; ?>
                                <?php if($message = Session::get('error')): ?>
                                    <div class="alert alert-danger alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong><?php echo e($message); ?></strong>


                                    </div>
                                <?php endif; ?>
                                <?php if(count($errors) > 0): ?>
                                    <div class="alert alert-danger alert-block">
                                        <ul>
                                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li><strong> <?php echo e($error); ?></strong></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                           

                                <div class="tab-content" style="margin-top:16px;">

                                    <div class="tab-pane active" id="personal_details">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4><b>Search Criteria</b></h4>
                                            </div>
                                            <div class="panel-body">



                                                <div class="row">
                                                <div class="form-group col-md-3">
                                                        <label for="cars" class="required-field">Select Scheme</label>
                                                         <select name="scheme_id" id="scheme_id" class="form-control">
                                                            <option value="">-- Select Scheme -- </option>
                                                           <?php $__currentLoopData = $scheme; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schemes): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                           <option value="<?php echo e($schemes->id); ?>"><?php echo e($schemes->scheme_name); ?></option>
                                                               
                                                           <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                         </select>
                                                        <span id="error_scheme" class="text-danger"></span>
                                                    </div>
                                                    <div class="form-group col-md-3">
                                                        <label for="cars" class="required-field">Choose Incomplete Type:</label>
                                                         <select name="incomplete_type" id="incomplete_type" class="form-control">
                                                            <option value="">-- Select Incomplete Type --</option>
                                                           <?php $__currentLoopData = $incomplete_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                           <option value="<?php echo e($list->id); ?>"><?php echo e($list->name); ?></option>
                                                           <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                         </select>
                                                        <span id="error_incomplete" class="text-danger"></span>
                                                    </div>

                                                    <div class="form-group col-md-3" id="failed_type_div" style="display: none;">
                                                        <label class="required-field">Failed Type</label>
                                                        <select class="form-control" name="failed_type" id="failed_type">
                                                            <option value="">--Select--</option>
                                                            <option value="3">SBI</option>
                                                            <option value="4">RBI</option>
                                                            <option value="5">IFMS</option>
                                                        </select>
                                                        <span id="error_failed_type" class="text-danger"></span>
                                                    </div>
                                                    <?php if($district_visible): ?>
                                                        <div class="form-group col-md-4">
                                                            <label class="">District</label>
                                                            <select name="district" id="district" class="form-control"
                                                                tabindex="6">
                                                                <option value="">--All --</option>
                                                                <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <option value="<?php echo e($district->district_code); ?>"
                                                                        <?php if(old('district') == $district->district_code): ?> selected <?php endif; ?>>
                                                                        <?php echo e($district->district_name); ?></option>
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
                                                            <label>Rural/ Urban</label>

                                                            <select name="urban_code" id="urban_code"
                                                                class="form-control" tabindex="11">
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
                                                            <label class="" id="blk_sub_txt">Block/Sub  Division.</label>

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

                                                    <div class="form-group col-md-4" id="municipality_div" style="<?php echo e($municipality_visible?'':'display:none'); ?>">
                <label class="">Municipality</label>
                
                <select name="muncid" id="muncid" class="form-control" tabindex="16" >
                  <option value="">--All --</option>
                    <?php $__currentLoopData = $muncList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $munc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($munc->urban_body_code); ?>"> <?php echo e($munc->urban_body_name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                   
                </select>
                  <span id="error_muncid" class="text-danger"></span>
              </div> 


                                                     <div class="form-group col-md-4" id="gp_ward_div" style="<?php echo e($gp_ward_visible?'':'display:none'); ?>">
                <label class="" id="gp_ward_txt">GP/Ward</label>
                
                <select name="gp_ward" id="gp_ward" class="form-control" tabindex="17" >
                  <option value="">--All --</option>
                   <?php $__currentLoopData = $gpList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($gp->gram_panchyat_code); ?>"> <?php echo e($gp->gram_panchyat_name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                   
                </select>
                  <span id="error_gp_ward" class="text-danger"></span>
              </div> 


                                                </div>
                                                <div class="row">
                                                    
                                                    <div class="col-md-12" align="center">

                                                        <button type="button" id="submitting" value="Submit"
                                                            class="btn btn-success success btn-lg modal-search form-submitted">Search
                                                        </button>

                                                        <div class=""><img src="<?php echo e(asset('images/ZKZg.gif')); ?>"
                                                                id="submit_loader1" width="50px" height="50px"
                                                                style="display:none;"></div>
                                                    </div>
                                                    <br />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content" style="margin-top:16px;">


                                            <div class="alert print-error-msg" style="display:none;" id="errorDiv">
                                                <button type="button" class="close" aria-label="Close"
                                                    onclick="closeError('errorDiv')"><span
                                                        aria-hidden="true">&times;</span></button>
                                                <ul></ul>
                                            </div>

                                            <div class="tab-pane active" id="search_details" style="display:none;">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading" id="heading_msg">
                                                        <h4><b>Search Result</b></h4>
                                                    </div>
                                                    <div class="panel-body">
                                                        <button class="btn btn-info exportToExcel"
                                                            type="button">Export to Excel</button><br /><br /><br />
                                                        <div id="divScrool">
                                                            <table id="example"
                                                                class="table table-striped table-bordered table2excel"
                                                                style="width:100%">
                                                                <thead>
                                                                    <tr>
                                                                        <td colspan="21" align="center"
                                                                            style="display:none;" id="heading_excel">
                                                                            Heading</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th id="" rowspan="1">Sl No.(A)
                                                                        </th>
                                                                        <th id="location_id" rowspan="1">Location Name</th>
                                                                        <th>Total Action Pending</th>
                                                                        <th>total Update by Verifier </th>
                                                                        <th>Total Approved by Approver</th>
                                                                      
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                                <tfoot>
                                                                    <tr id="fotter_id"></tr>
                                                                    <tr>
                                                                        <td colspan="21" align="center"
                                                                            style="display:none;" id="fotter_excel">
                                                                            Heading</td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <!-- /.box -->
                </div>
                <!--/.col (left) -->

        </div>
   


        </section>

    </div>
    <!-- /.content-wrapper -->

    <!-- Footer -->
    <?php echo $__env->make('layouts.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <!-- ./wrapper -->

    <!-- REQUIRED JS SCRIPTS -->

    <!-- jQuery 2.1.3 -->
    <script src="<?php echo e(asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js')); ?>"></script>
    <script src="<?php echo e(asset('/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js')); ?>"
        type="text/javascript"></script>
    <script src="<?php echo e(asset('/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js')); ?>"
        type="text/javascript"></script>
    <script src="<?php echo e(asset('js/select2.full.min.js')); ?>"></script>

    <!-- Bootstrap 3.3.2 JS -->
    <script src="<?php echo e(asset('/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(URL::asset('js/site.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('js/master-data-v2.js')); ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo e(asset('/bower_components/AdminLTE/dist/js/app.min.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery.table2excel.js')); ?>"></script>

    <script>
        
        //alert(base_date);

        $(document).ready(function() {
            $('.sidebar-menu li').removeClass('active');
            // $('.sidebar-menu #lk-main').addClass("active"); 
            $('.sidebar-menu #misReportWithNormal').addClass("active");
            //loadDataTable();
            $('#incomplete_type').change(function() {
                // alert($('#filter_type').val());
                    if ($('#incomplete_type').val() === '10') { // Ensure comparison to string
                    $('#failed_type_div').show();
                    } else {
                        $('#failed_type_div').hide();
                    }
            });

            $('#incomplete_type option[value="13"]').remove();
            $(".exportToExcel").click(function(e) {
                // alert('ok');
                $(".table2excel").table2excel({
                    // exclude CSS class
                    exclude: ".noExl",
                    name: "Worksheet Name",
                    filename: "Jai Bangla  MIS Report", //do not include extension
                    fileext: ".xls" // file extension
                });
            });


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
            $('.modal-search').on('click', function() {
                var error_incomplete = '';
                var error_scheme = '';
                var error_failed_type = '';
                var incomplete_type = $("#incomplete_type").val();
                var scheme_id = $("#scheme_id").val();
                var failed_type = $("#failed_type").val();

                if ($.trim($('#failed_type').val()).length == 0) {
                    error_failed_type = 'Failed Type is required';
                    $('#error_failed_type').text(error_failed_type);
                    $('#error_failed_type').removeClass('has-error');
                } else {
                    error_failed_type = '';
                    $('#error_failed_type').text(error_failed_type);
                    $('#error_failed_type').addClass('has-error');
                }

                if (incomplete_type != '') {
                    error_incomplete = '';
                    $('#error_incomplete').text(error_incomplete);
                    $('#incomplete_type').removeClass('has-error');
                } else {
                    error_incomplete = 'Incomplete Type is required.';
                    $('#error_incomplete').text(error_incomplete);
                    $('#incomplete_type').addClass('has-error');
                }

                if (scheme_id != '') {
                    error_scheme = '';
                    $('#error_scheme').text(error_scheme);
                    $('#scheme_id').removeClass('has-error');
                } else {
                    error_scheme = 'Scheme is required.';
                    $('#error_scheme').text(error_scheme);
                    $('#scheme_id').addClass('has-error');
                }


                if (error_scheme != '' || error_incomplete != '') {
                    return false;
                } else if ($('#incomplete_type').val() == '10') {
                    if (error_scheme != '' || error_incomplete != '' || error_failed_type != '') {
                        return false;
                    } else {
                        loadDataTable();
                    }
                } else {
                    loadDataTable();
                }
            });
        });

        function loadDataTable() {
            var incomplete_type = $("#incomplete_type").val();
            var district = $('#district').val();
            var urban_code = $('#urban_code').val();
            var block = $('#block').val();
            var gp_ward = $('#gp_ward').val();
            var muncid = $('#muncid').val();
            var scheme_id = $('#scheme_id').val();
            var falied_type = $('#failed_type').val();

            $("#submit_loader1").show();
            $("#submitting").hide();
            $('#search_details').hide();
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: "<?php echo e(route('noDupMisPost')); ?>",
                data: {
                    incomplete_type: incomplete_type,
                    district: district,
                    urban_code: urban_code,
                    block: block,
                    gp_ward: gp_ward,
                    scheme_id: scheme_id,
                    muncid: muncid,
                    falied_type: falied_type,
                    _token: '<?php echo e(csrf_token()); ?>',
                },
                success: function(data) {
                    // console.log(data);
                    var incomplete_text = $('#incomplete_type option:selected').text();
                    if($('#incomplete_type').val() == '10'){
                        failed_type = $('#failed_type option:selected').text();
                    }else{
                        failed_type = '';
                    }

                    if (data.return_status) {
                        $('#search_details').show();
                        $("#heading_msg").html("<h4><b>" + data.heading_msg + "</b> --"+incomplete_text+ "--" + failed_type+ "</h4>"  );
                        $("#heading_excel").html("<b>" + data.heading_msg + "</b> " + "<I>" + incomplete_text + "--" + failed_type+"</I>");

                        $("#fotter_excel").html("<b>" + $('#report_generation_text').text() + "</b>");
                        $("#location_id").text(data.column + '(B)');
                        $("#report_generation_text").text(data.report_geneartion_time);
                        $("#example > tbody").html("");
                        var table = $("#example tbody");
                        var slno = 1;
                        var fotter_1 = 0;
                        var fotter_2 = 0;
                        var fotter_3 = 0;
                        $.each(data.row_data, function(i, item) {
                            var yet_to_action = isNaN(parseInt(item.yet_to_action)) ? 0 : parseInt(item.yet_to_action);
                            var approval_pending = isNaN(parseInt(item.approval_pending)) ? 0 : parseInt(item.approval_pending);
                            var approved = isNaN(parseInt(item.approved)) ? 0 : parseInt(item.approved);

                            fotter_1 = fotter_1 + yet_to_action;
                            fotter_2 = fotter_2 + approval_pending;
                            fotter_3 = fotter_3 + approved;
                            
                            table.append("<tr><td>" + (i + 1) + "</td><td>" + item.location_name +
                                "</td><td>" + yet_to_action +"</td><td>" + approval_pending + "</td><td>" + approved +
                                "</td></tr>");
                        });

                        $("#example > tfoot #fotter_id").html("<th></th><th>Total</th><th>" + fotter_1 +
                            "</th><th>" + fotter_2 + "</th><th>" + fotter_3 +   "</th>");
                        $("#example").show();
                    } else {
                        $('#search_details').hide();
                        $("#example").hide();
                        printMsg(data.return_msg, '0', 'errorDiv');
                    }
                    $("#submit_loader1").hide();
                    $("#submitting").show();
                },
                error: function(ex) {
                   
                    $("#submit_loader1").hide();
                   
                    $("#submitting").show();
                     alert('Something wrong..may be session timeout. please logout and then login again');
                    //  location.reload();

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

        function closeError(divId) {
            $('#' + divId).hide();
        }
    </script>
</body>

</html>
