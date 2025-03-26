<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Jb | Jai Bangla</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="<?php echo e(asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css")); ?>" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link href="<?php echo e(asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset("css/select2.min.css")); ?>" rel="stylesheet">
    <link href="<?php echo e(asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")); ?>" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css"> -->
    <style>
        .errorField {
            border-color: #990000;
        }

        .searchPosition {
            margin: 70px;
        }

        .submitPosition {
            margin: 25px 0px 0px 0px;
        }


        .typeahead {
            border: 2px solid #FFF;
            border-radius: 4px;
            padding: 8px 12px;
            max-width: 300px;
            min-width: 290px;
            background: rgba(66, 52, 52, 0.5);
            color: #FFF;
        }

        .tt-menu {
            width: 300px;
        }

        ul.typeahead {
            margin: 0px;
            padding: 10px 0px;
        }

        ul.typeahead.dropdown-menu li a {
            padding: 10px !important;
            border-bottom: #CCC 1px solid;
            color: #FFF;
        }

        ul.typeahead.dropdown-menu li:last-child a {
            border-bottom: 0px !important;
        }

        .bgcolor {
            max-width: 550px;
            min-width: 290px;
            max-height: 340px;
            background: url("world-contries.jpg") no-repeat center center;
            padding: 100px 10px 130px;
            border-radius: 4px;
            text-align: center;
            margin: 10px;
        }

        .demo-label {
            font-size: 1.5em;
            color: #686868;
            font-weight: 500;
            color: #FFF;
        }

        .dropdown-menu>.active>a,
        .dropdown-menu>.active>a:focus,
        .dropdown-menu>.active>a:hover {
            text-decoration: none;
            background-color: #1f3f41;
            outline: 0;
        }

        table.dataTable thead th,
        table.dataTable thead td {
            padding: 10px 13px;
        }

        table.dataTable tfoot th,
        table.dataTable tfoot td {
            padding: 10px 5px;
        }

        .criteria1 {
            text-transform: uppercase;
            font-weight: bold;
        }

        #example_length {
            margin-left: 40%;
            margin-top: 2px;
        }

        @keyframes  spinner {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner:before {
            content: '';
            box-sizing: border-box;
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin-top: -10px;
            margin-left: -10px;
            border-radius: 50%;
            border: 2px solid #ccc;
            border-top-color: #333;
            animation: spinner .6s linear infinite;
        }

        .select2 {
            width: 100% !important;
        }

        .select2 .has-error {
            border-color: #cc0000;
            background-color: #ffff99;
        }
    </style>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <?php echo $__env->make('layouts.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php echo $__env->make('layouts.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <div class="content-wrapper">
            <section class="content-header">
                <?php if($report_name): ?>
                    <b><?php echo e($report_name); ?></b>
                    <br>
                <?php endif; ?>
                <div class='row'>
                    <div>
                        <?php if(($message = Session::get('message'))): ?>
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e($message); ?></strong>

                            </div>
                        <?php endif; ?>
                        <?php if(($error = Session::get('error'))): ?>
                            <div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e($error); ?></strong>

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
                </div>
            </section>
            <!-- Main content -->
            <section class="content">
                <input type="hidden" name="dist_code" value="<?php echo e($dist_code); ?>" class="client-js-district" hidden>
                <input type="hidden" name="is_urban" value="<?php echo e($is_urban); ?>" class="client-js-urban">
                <input type="hidden" name="scheme_id" value="<?php echo e($scheme_id); ?>" id="scheme_id">
                <inpyt type="hidden" name="type" name="type" value="<?php echo e($type); ?>" />
                <div class="row">
                    <?php if($type == 2): ?>

                        <div class="form-group col-md-4">
                            <label class=" control-label">Application Type</label>
                            <select name="application_type" id="application_type" class="form-control full-width">
                                <option value="1" selected>Pending</option>
                                <?php if($designation_id == 'Verifier'): ?>
                                    <option value="2">Verified but Approval Pending</option>
                                <?php endif; ?>
                                <option value="3">Verified and Approved</option>
                                <option value="4">Rejected</option>
                                <option value="5">Probable duplicate list</option>
                                <?php if($scheme_id == 1): ?>
                                    <option value="6">Received from Bandhu</option>
                                    <option value="7">Transfer to Bandhu</option>
                                    <option value="11">Transfer to OAP</option>
                                <?php endif; ?>
                                <?php if($scheme_id == 3): ?>
                                    <option value="9">Received from Johar</option>
                                    <option value="10">Transfer to Johar</option>
                                    <option value="11">Transfer to OAP</option>
                                <?php endif; ?>
                                <?php if($scheme_id == 10): ?>
                                    <option value="12">Received from Bandhu</option>
                                    <option value="13">Received from Johar</option>
                                <?php endif; ?>
                                <option value="14">Back to LB</option>
                            </select>
                        </div>

                    <?php endif; ?>

                    <?php if($is_urban == 1): ?>
                        <div class="form-group col-md-3">
                            <label class=" control-label">Select Filter Criteria :Municipality</label>
                            <select name="munc" id="munc"
                                class="form-control select2 full-width js-municipality client-js-localbody">
                                <option value="">-----Select----</option>
                                <?php $__currentLoopData = $urban_bodys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $urban_body): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($urban_body->urban_body_code); ?>"> <?php echo e($urban_body->urban_body_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class=" control-label">Select Filter Criteria :Wards</label>
                            <select name="gp_ward" id="gp_ward" class="form-control  client-js-gpward">
                                <option value="">--Select --</option>
                            </select>
                        </div>


                    <?php endif; ?>
                    <?php if($is_urban == 2): ?>
                        <input type="hidden" name="munc" id="munc" value="" />
                        <div class="form-group col-md-3">
                            <label class=" control-label">Select Filter Criteria : Gram Panchayat</label>
                            <select name="gp_ward" id="gp_ward" class="form-control select2 ">
                                <option value="">-----Select----</option>
                                <?php $__currentLoopData = $gps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($gp->gram_panchyat_code); ?>"> <?php echo e($gp->gram_panchyat_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    <?php else: ?>
                    <?php endif; ?>



                    <?php if($aadhar_filer_visible == 1): ?>
                        <div class="form-group col-md-4">
                            <label class="">Select Filter Criteria :Aadhaar</label>
                            <select name="aadhar_exists" id="aadhar_exists" class="form-control full-width">
                                <option value="1" selected>Applications with Aadhaar Number</option>
                                <option value="0">Applications without Aadhaar Number</option>
                            </select>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="aadhar_exists" id="aadhar_exists" value="1" />
                    <?php endif; ?>
                    <div class="form-group col-md-3" style="margin-top:25px;">
                        <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
                        <button type="button" name="reset" id="reset" class="btn btn-default">Reset</button>
                    </div>
                </div>

                <form class="row" method="POST" action="<?php echo e(route('nhmemployee.MassEmployeeApproval')); ?>"
                    class="submit-once">
                    <table id="example" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr role="row" style="font-size: 12px;">
                                <th width="7%">Beneficiary ID</th>
                                <th width="12%">Applicant Name</th>
                                <th width="12%">DOB</th>
                                <th width="12%">Gender</th>
                                <?php if($is_urban == 1): ?>
                                    <th width="12%">Municipality Name</th>
                                <?php endif; ?>
                                <th width="12%">GP/Ward Name</th>
                                <th width="17%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </form>
                <div class="row">
                    <div class="col-sm-7">
                        <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <script src="<?php echo e(asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
    <script src="<?php echo e(asset("js/select2.full.min.js")); ?>"></script>
    <script src="<?php echo e(URL::asset('js/master-data-v2.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('js/site-client-v2.js')); ?>"></script>
    <script>
        $('.select2').select2();
    </script>
    <script src="<?php echo e(asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js")); ?>"
        type="text/javascript"></script>
    <script src="<?php echo e(asset("/bower_components/AdminLTE/dist/js/app.min.js")); ?>" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <!-- <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script> -->
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function () {
            var base_url = '<?php echo e(url('/')); ?>';
            $("#submittingrevert").hide();
            $("#submittingreject").hide();
            var munc = $("#munc").val();
            var gp_ward = $("#gp_ward").val();
            var aadhar_exists = $("#aadhar_exists").val();
            fill_datatable(munc, gp_ward, aadhar_exists);

            function fill_datatable(munc = '', gp_ward = '', aadhar_exists = 1) {
                var scheme_id = $("#scheme_id").val();
                if ($.fn.dataTable.isDataTable('#example')) {
                    $('#example').DataTable().destroy();
                }
                var dataTable = $('#example').DataTable({
                    dom: 'Blfrtip',
                    paging: true,
                    pageLength: 100,
                    lengthMenu: [[20, 50, 100, 500, 1000, -1], [20, 50, 100, 500, 1000, 'All']],
                    processing: true,
                    serverSide: true,
                    ordering: false,
                    ajax: {
                        url: "<?php echo e(url('VerifierDataAjax')); ?>",
                        type: "GET",
                        data: function (d) {
                            d.scheme_id = scheme_id;
                            d.munc = munc;
                            d.gp_ward = gp_ward;
                            d.aadhar_exists = aadhar_exists;
                            d._token = "<?php echo e(csrf_token()); ?>";
                        },
                        error: function (ex) {
                            alert('Session time out..Please login again');
                            //window.location.href = base_url;
                        }
                    },
                    columns: [
                        { "data": "id" },
                        { "data": "name" },
                        { "data": "dob" },
                        { "data": "gender" },
                        <?php if($is_urban == 1): ?>
                            { "data": "block_ulb_name" },
                        <?php endif; ?>
                        { "data": "gp_ward_name" },
                        { "data": "view" },
                    ],
                });
            }
            $('#filter').click(function () {
                var munc = $("#munc").val();
                var gp_ward = $("#gp_ward").val();
                var aadhar_exists = $("#aadhar_exists").val();
                fill_datatable(munc, gp_ward, aadhar_exists);
            });

            $('#reset').click(function () {
                $('#munc').val('');
                $('#gp_ward').val('');
                $('#example').DataTable().destroy();
                fill_datatable();
            });
            $(document).on('click', '.revert', function () {
                $('#formRevert #beneficiary_id').val('');
                $('#application_text_approve_revert').text('');
                $('.revert').attr('disabled', false);
                var benid = $(this).val();
                //alert(benid);
                $('#revert_' + benid).attr('disabled', true);
                $('#formRevert #beneficiary_id').val(benid);
                $('#application_text_approve_revert').text(benid);
                $('#modalConfirmRevert').modal();
            });
            $(document).on('click', '.reject', function () {
                $('#formReject #beneficiary_id').val('');
                $('#application_text_approve_reject').text('');
                $('.reject').attr('disabled', false);
                var benid = $(this).val();
                $('#reject_' + benid).attr('disabled', true);
                $('#formReject #beneficiary_id').val(benid);
                $('#application_text_approve_reject').text(benid);
                $('#modalConfirmReject').modal();
            });
            $('#confirm_yes_revert').on('click', function () {
                $("#confirm_yes_revert").hide();
                $("#submittingrevert").show();
                $("#formRevert").submit();
            });
            $('#confirm_yes_reject').on('click', function () {
                $("#confirm_yes_reject").hide();
                $("#submittingreject").show();
                $("#formReject").submit();
            });
        });
    </script>
</body>

</html>