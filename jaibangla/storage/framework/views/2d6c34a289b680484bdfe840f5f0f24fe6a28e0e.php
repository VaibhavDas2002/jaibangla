<!DOCTYPE html>

<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
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
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
    <link href="<?php echo e(asset('/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css')); ?>" rel="stylesheet"
        type="text/css" />

    <!-- bootstrap wysihtml5 - text editor -->
    <!-- <link rel="stylesheet" href="<?php echo e(asset('/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')); ?>"> -->

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
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
    </style>


</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->

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
                        <!--   <?php if($message = Session::get('failure')): ?>
<div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                      <strong><?php echo e($message); ?></strong>
              </div>
<?php endif; ?> -->
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form method="post" id="register_form" action="<?php echo e(url('wcd20210202ReportPost')); ?>"
                        class="submit-once">
                        <?php echo e(csrf_field()); ?>



                        <input type="hidden" name="district_code_fk" id="district_code_fk"
                            value="<?php echo e($district_code_fk); ?>">
                        <input type="hidden" name="rural_urban_fk" id="rural_urban_fk" value="<?php echo e($rural_urban_fk); ?>">
                        <input type="hidden" name="block_munc_corp_code_fk" id="block_munc_corp_code_fk"
                            value="<?php echo e($block_munc_corp_code_fk); ?>">


                        <div class="tab-content" style="margin-top:16px;">






                            <div class="tab-pane active" id="personal_details">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4><b> Applications Statistics</b></h4>
                                    </div>
                                    <div class="panel-body">

                                      

                                        <div class="row">
                                            <div class="col-md-3" id="phase_div">
                                                <label class="control-label">Select Phase</label>
                                                <select name="phase_code" id="phase_code" class="form-control" tabindex="6" >
                                                  <option value="">-----All----</option>
                                                  <?php $__currentLoopData = $phase_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                 <option value="<?php echo e($phase->phase_code); ?>"> <?php echo e($phase->phase_des); ?></option>
                                                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                 <option value="-1">Normal Entry</option>
                                               </select>
                                                <span id="error_phase_code" class="text-danger"></span>
                                               </div>
                                            <div class="form-group col-md-4">
                                                <label class="required-field">Select Scheme</label>
                                                <select name="scheme_code" id="scheme_code" class="form-control"
                                                    tabindex="1">
                                                    <option value="">--All --</option>
                                                    <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($scheme->id); ?>"
                                                            <?php if(old('scheme_code') == $scheme->id): ?> selected <?php endif; ?>>
                                                            <?php echo e($scheme->scheme_name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                </select>
                                                <span id="error_scheme_code" class="text-danger"></span>

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
                                            <?php endif; ?>
                                            <?php if($block_visible): ?>
                                                <div class="form-group col-md-4" id="divBodyCode">
                                                    <label class="">Block/Municipality/Corp.</label>

                                                    <select name="block" id="block" class="form-control"
                                                        tabindex="16">
                                                        <option value="">--All --</option>


                                                    </select>
                                                    <span id="error_block" class="text-danger"></span>
                                                </div>
                                            <?php endif; ?>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label class="">From Date(Application Submission Date)</label>
                                                    <input type="date" class="form-control" id="from_date">
                                                    <span id="error_from_date" class="text-danger"></span>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="">To Date (Application Submission Date)</label>
                                                    <input type="date" class="form-control" id="to_date">
                                                    <span id="error_to_date" class="text-danger"></span>
                                                </div>
                                            </div>
                                            <br />
                                            <br />
                                            <div class="col-md-12" align="center">

                                                <button type="button" id="submitting" value="Submit"
                                                    class="btn btn-success success btn-lg modal-search form-submitted">Search
                                                </button>

                                                <div class=""><img src="<?php echo e(asset('images/ZKZg.gif')); ?>"
                                                        id="submit_loader1" width="50px" height="50px"
                                                        style="display:none;"></div>

                                                <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
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

                                                <p class="pull-right">Report generated on: <span
                                                        id="generation_time"></span></p>
                                                <br />

                                                <table id="example" class="table table-striped table-bordered"
                                                    style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th id="location_id" width="25%">District</th>
                                                            <th>Applications Submitted</th>
                                                            <th>Yet to be Verified and Approved</th>
                                                            <th>Verified but Approval Pending</th>
                                                            <th>Verified and Approved</th>
                                                            <th>Rejected</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th></th>
                                                            <th>Applications Submitted</th>
                                                            <th>Yet to be Verified and Approved</th>
                                                            <th>Verified but Approval Pending</th>
                                                            <th>Verified and Approved</th>
                                                            <th>Rejected</th>

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





                </form>
        </div>
        <!-- /.box -->
    </div>
    <!--/.col (left) -->

    </div>
    <!--  <?php if(session()->has('success')): ?>
<div class="alert alert-success">
            <?php echo e(session()->get('success')); ?>

        </div>
<?php endif; ?> -->
    <!-- /.row -->


    </section>

    <!-- Main content -->
    <!--  <section class="content">

      Your Page Content Here



    </section> -->
    <!-- /.content -->
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
    <script src="<?php echo e(asset('js/dataTables.buttons.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/buttons.flash.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jszip.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/pdfmake.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/vfs_fonts.js')); ?>"></script>
    <script src="<?php echo e(asset('js/buttons.html5.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/buttons.print.min.js')); ?>"></script>
    <script>
        //alert(base_date);
        $(document).ready(function() {
            var c_time = '<?php echo e($c_time); ?>';
            // alert(c_time);
            $('#district').change(function() {
                var district = $(this).val();
                //alert(district);
                $('#urban_code').val('');
                $('#block').html('<option value="">--All --</option>');
                if (district == '') {
                    $("#district_code_fk").val('');
                    $("#rural_urban_fk").val('');
                    $("#block_munc_corp_code_fk").val('');
                    //alert($("#rural_urban_fk").val());
                } else {
                    $("#district_code_fk").val(district);
                }
            });

            $('#urban_code').change(function() {
                var urban_code = $(this).val();
                if (urban_code == '') {
                    $("#rural_urban_fk").val('');
                    $("#block_munc_corp_code_fk").val('');
                }
                if (urban_code != '') {
                    $("#rural_urban_fk").val(urban_code);
                }
                $('#block').html('<option value="">--All --</option>');
                select_district_code = $('#district_code_fk').val();
                if (select_district_code == '') {
                    alert('Please Select District First');
                    $("#district").focus();
                    $("#urban_code").val('');
                    $("#rural_urban_fk").val('');
                } else {
                    select_body_type = urban_code;
                    var htmlOption = '<option value="">--All--</option>';
                    if (select_body_type == 2) {
                        $.each(blocks, function(key, value) {
                            if (value.district_code == select_district_code) {
                                htmlOption += '<option value="' + value.id + '">' + value.text +
                                    '</option>';
                            }
                        });
                    } else if (select_body_type == 1) {
                        $.each(ulbs, function(key, value) {
                            if (value.district_code == select_district_code) {
                                htmlOption += '<option value="' + value.id + '">' + value.text +
                                    '</option>';
                            }
                        });
                    }

                    $('#block').html(htmlOption);
                }
            });
            $('#block').change(function() {
                var block = $(this).val();
                var district = $("#district_code_fk").val();
                var urban_code = $("#rural_urban_fk").val();
                if (district == '') {
                    alert('Please Select District First');
                    $("#block").val('');
                    //$("#block_munc_corp_code_fk").val('');
                    $("#district").focus();
                }
                if (urban_code == '') {
                    alert('Please Select Rural/Urban First');
                    $("#block").val('');
                    $("#block_munc_corp_code_fk").val('');
                    $("#urban_code").focus();
                }
                if (block != '') {
                    $("#block_munc_corp_code_fk").val(block);
                } else {
                    $("#block_munc_corp_code_fk").val('');
                }
            });
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();
            var error_from_date = '';
            var error_to_date = '';

            // if ((from_date != '' && to_date == '') || (from_date == '' && to_date != '')) {
            //   error_from_date = 'Scheme name is required';
            //     $('#error_scheme_id').text(error_scheme_id);
            // } else {
              
            // }

            $('.modal-search').on('click', function() {
                var phase_code = $('#phase_code').val();
                var scheme_code = $('#scheme_code').val();
                var district = $('#district_code_fk').val();
                var urban_code = $('#rural_urban_fk').val();
                var block = $('#block_munc_corp_code_fk').val();
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                var report_type = $('#report_type').val();

                var is_date_valid = 1;
                if ((from_date != '' && to_date == '') || (from_date == '' && to_date != '')) {
                    is_date_valid = 0;
                } else {
                    is_date_valid = 1;
                }

                if (scheme_code != '' && is_date_valid == 1) {
                    $("#generation_time").html('');
                    $("#submit_loader1").show();
                    $("#submitting").hide();
                    $('#search_details').hide();
                    $.ajax({
                        type: 'get',
                        dataType: 'json',
                        url: '<?php echo e(url('applicationstatreportpost')); ?>',
                        data: {
                            scheme_code: scheme_code,
                            phase_code: phase_code,
                            district: district,
                            urban_code: urban_code,
                            block: block,
                            from_date: from_date,
                            to_date: to_date,
                            report_type: report_type,
                            _token: '<?php echo e(csrf_token()); ?>',
                        },
                        success: function(data) {

                            //alert(data.title);
                            if (data.return_status) {
                                $('#search_details').show();
                                $("#heading_msg").html("<h4><b>" + data.heading_msg +
                                    "</b></h4>");
                                // alert(data.c_time);
                                $("#generation_time").html(data.c_time);
                                $("#location_id").text(data.column);
                                if ($.fn.DataTable.isDataTable('#example')) {
                                    $('#example').DataTable().destroy();
                                }
                                $("#example > tbody").html("");
                                var table = $("#example tbody");
                                $.each(data.row_data, function(i, item) {
                                    var applied = isNaN(parseInt(item.applied)) ? 0 :
                                    parseInt(item.applied);
                                    var fresh = isNaN(parseInt(item.fresh)) ? 0 :
                                        parseInt(item.fresh);
                                    var approved = isNaN(parseInt(item.approved)) ? 0 :
                                        parseInt(item.approved);
                                    var verified = isNaN(parseInt(item.verified)) ? 0 :
                                        parseInt(item.verified);
                                    var rejected = isNaN(parseInt(item.rejected)) ? 0 :
                                        parseInt(item.rejected);
                                    // var pending = isNaN(parseInt(item.pending)) ? 0 : parseInt(item.pending);
                                    //var pending=parseInt(item.applied-((item.approved)+(item.rejected)));
                                    var total = fresh+approved+verified+rejected;
                                    table.append("<tr><td>" + item.location_name +
                                        "</td><td>" + total + "</td><td>" +
                                            fresh + "</td> <td>" + verified +
                                        "</td> <td>" + approved +
                                        "</td> <td>" + rejected + "</td></tr>");
                                });


                                //$('#example tbody').empty();
                                $("#example").show();
                                $('#example').dataTable({
                                    "paging": false,
                                    "ordering": false,
                                    "info": false,
                                    "dom": 'Bfrtip',
                                    "buttons": [
                                        'copy',
                                        {
                                            extend: 'excel',
                                            footer: true,
                                            title: data.title,
                                            messageTop: data.heading_msg +
                                                ' ,Report generated on:' + c_time
                                        },
                                        {
                                            extend: 'pdf',
                                            title: data.title,
                                            footer: true,
                                            messageTop: data.heading_msg +
                                                ' ,Report generated on:' + c_time
                                        }

                                    ],
                                    "footerCallback": function(row, data, start, end,
                                        display) {
                                        var api = this.api(),
                                            data;

                                        // converting to interger to find total
                                        var intVal = function(i) {
                                            return typeof i === 'string' ?
                                                i.replace(/[\$,]/g, '') * 1 :
                                                typeof i === 'number' ?
                                                i : 0;
                                        };

                                        // computing column Total of the complete result 
                                        var Total = api
                                            .column(1)
                                            .data()
                                            .reduce(function(a, b) {
                                                return intVal(a) + intVal(b);
                                            }, 0);

                                        var freshTotal = api
                                            .column(2)
                                            .data()
                                            .reduce(function(a, b) {
                                                return intVal(a) + intVal(b);
                                            }, 0);
                                        var verifiedTotal = api
                                            .column(3)
                                            .data()
                                            .reduce(function(a, b) {
                                                return intVal(a) + intVal(b);
                                            }, 0);
                                        var approveTotal = api
                                            .column(4)
                                            .data()
                                            .reduce(function(a, b) {
                                                return intVal(a) + intVal(b);
                                            }, 0);

                                            var rejectTotal = api
                                            .column(5)
                                            .data()
                                            .reduce(function(a, b) {
                                                return intVal(a) + intVal(b);
                                            }, 0);


                                        // Update footer by showing the total with the reference of the column index 
                                        $(api.column(0).footer()).html('Total');
                                        $(api.column(1).footer()).html(Total);
                                        $(api.column(2).footer()).html(
                                            freshTotal);
                                        $(api.column(3).footer()).html(
                                            verifiedTotal);
                                       $(api.column(4).footer()).html(approveTotal);
                                        $(api.column(5).footer()).html(rejectTotal);
                                        //$( api.column( 5 ).footer() ).html(pendingTotal);
                                    }
                                });
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
                            //$("#submitting").hide();
                            $("#submitting").show();
                            // alert('Something wrong..may be session timeout. please logout and then login again');
                            //location.reload();

                        }
                    });
                } else {
                    if (is_date_valid == 0) {
                        alert('Please Select both application From Date and To Date');
                    }
                    if(scheme_code == '') {
                        alert('Please Select Scheme');
                        $('#scheme_code').focus();
                    }
                }
            });
        });

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
