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

    #loadingDi {
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
            Block Sub Division Wise MIS Report
        </h1>
        <ol class="breadcrumb">
            <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span
                    class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
        </ol>
    </section>
    <section class="content">
        <div class="box box-default">
            <div class="box-body">
                <div id="loadingDi"></div>
                <div class="panel panel-default">
                    <div class="panel-heading" style="font-size: 14px; font-weight: bold; font-style: italic;"><span
                            id="panel-icon">Enter Filter Criteria</div>
                    <div class="panel-body" style="padding: 5px;">
                        <div class="row">
                            <div class="col-md-12">
                                <?php if(($message = Session::get('success'))): ?>
                                    <div class="alert alert-success alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong><?php echo e($message); ?> </strong>
                                    </div>
                                <?php endif; ?>
                                <?php if(($message = Session::get('message'))): ?>
                                    <div class="alert alert-danger alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong><?php echo e($message); ?></strong>
                                    </div>
                                <?php endif; ?>
                                <?php if(($message = Session::get('msg1'))): ?>
                                    <div class="alert alert-danger alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong><?php echo e($message); ?></strong>
                                    </div>
                                <?php endif; ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Scheme Selection -->
                                        <div class="col-md-4">
                                            <label for="scheme_type" class="control-label">Scheme <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="scheme_type" id="scheme_type" required>
                                                <option value="">--Select Scheme--</option>
                                                <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($scheme->id); ?>"><?php echo e($scheme->scheme_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <span class="text-danger" id="error_scheme_type"></span>
                                        </div>

                                        <?php echo $__env->make('common-selection.index', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                        <!-- Operation Type Selection -->
                                        <!-- <div class="form-group col-md-4">
                                            <label for="filter_type" class="control-label">Operation Type <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="filter_type" id="filter_type" required>
                                                <option value="">--All--</option>
                                                <option value="0">Incomplete Data</option>
                                                <option value="1">Duplicate Aadhar</option>
                                                <option value="2">No Aadhar</option>
                                                <option value="3">Duplicate Bank</option>
                                                <option value="4">Duplicate Mobile</option>
                                                <option value="5">No Mobile</option>
                                                <option value="6">Payment Failure</option>
                                                <option value="7">Name Validation Failed</option>
                                                <option value="8">Account Validation Failed</option>
                                            </select>
                                            <span id="error_filter_type" class="text-danger"></span>
                                        </div> -->
                                    </div>

                                    <!-- Include Common Selection -->

                                    <!-- Search Button -->
                                    <div class="text-center">
                                        <button class="btn btn-primary" name="submit_btn" id="submit_btn" type="button"
                                            disabled>
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>

                                    <div class="col-md-12" style="text-align: left; margin-top: 20px;">
                                        <form action="<?php echo e(route('blkUlb_mis_report_excel')); ?>" method="post">
                                            <?php echo e(csrf_field()); ?>

                                            <input type="hidden" name="excel_scheme_id" id="excel_scheme_id" />
                                            <input type="hidden" name="excel_dist_code" id="excel_dist_code"
                                                value="<?php echo e($dist_code); ?>" />
                                            <input type="hidden" name="excel_filter_1" id="excel_filter_1" />
                                            <input type="hidden" name="excel_filter_2" id="excel_filter_2" />
                                            <button class="btn btn-success" name="excel_btn" id="excel_btn"
                                                type="submit" disabled>
                                                <i class="fa fa-file-excel-o"></i> Download List
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="res_div" style="display: none;">
                        <div class="panel panel-default">
                            <div class="panel-heading" id="panel_head"
                                style="font-size: 14px; font-weight: bold; font-style: italic;">List of Beneficiary
                            </div>
                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                <div class="table-responsive">
                                    <table id="example" class="table display" cellspacing="0" width="100%">
                                        <thead style="font-size: 12px;">
                                            <th>Block/Sub-Division</th>
                                            <th>Incomplete Details</th>
                                            <th>No Aadhar Number</th>
                                            <th>Duplicate Aadhar Number</th>
                                            <th>Bank Failure</th>
                                            <th>Duplicate Bank Account Number</th>
                                            <th>No Mobile Number</th>
                                            <th>Duplicate Mobile Number</th>
                                        </thead>
                                        <tbody style="font-size: 14px;"></tbody>
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
<?php $__env->stopSection(); ?>
<script src="<?php echo e(asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
<script>
    $(document).ready(function () {
        var interval = setInterval(function () {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);

        $('#loadingDi').hide();
        $('#submit_btn').removeAttr('disabled');

        $('#submit_btn').click(function () {
            if ($.trim($('#scheme_type').val()).length == 0) {
                error_scheme_type = 'Scheme name is required';
                $('#error_scheme_type').text(error_scheme_type);
            } else {
                error_scheme_type = '';
                $('#error_scheme_type').text(error_scheme_type);
            }

            // if ($.trim($('#filter_type').val()).length == 0) {
            //     error_filter_type = 'Filter is required';
            //     $('#error_filter_type').text(error_filter_type);
            // } else {
            //     error_filter_type = '';
            //     $('#error_filter_type').text(error_filter_type);
            // }



            if (error_scheme_type != '') {
                return false;
            } else {
                loadDatatable();
                $('.dt-buttons').hide();
            }
        });

        $('#scheme_type').change(function () {
            if ($(this).val() != '') {
                $('#excel_scheme_id').val($(this).val());
                $('#excel_btn').removeAttr('disabled');

            }
            else {
                $('#excel_scheme_id').val('');
            }
        });


        $('#filter_1').change(function () {
            if ($(this).val() != '') {
                $('#excel_filter_1').val($(this).val());
            }
            else {
                $('#excel_filter_1').val('');
            }
        });

        $('#filter_2').change(function () {
            if ($(this).val() != '') {
                $('#excel_filter_2').val($(this).val());
            }
            else {
                $('#excel_filter_2').val('');
            }
        });







        function loadDatatable() {
            $('#loadingDi').show();
            $('#res_div').show();

            let schemeText = $("#scheme_type option:selected").text();
            $('#panel_head').text('List of Beneficiaries of Scheme: ' + schemeText);

            if ($.fn.DataTable.isDataTable('#example')) {
                $('#example').DataTable().destroy();
            }

            $('#example').DataTable({
                dom: 'Blfrtip',
                "scrollX": true,
                "paging": true,
                "searchable": true,
                "ordering": false,
                "bFilter": true,
                "bInfo": true,
                "pageLength": 20,
                'lengthMenu': [
                    [10, 20, 25, 50, 100, -1],
                    [10, 20, 25, 50, 100, 'All']
                ],
                "serverSide": true,
                "processing": true,
                "ajax": {
                    url: "<?php echo e(route('blkUlb_mis_report_post')); ?>",
                    type: "post",
                    data: function (d) {
                        d.scheme_id = $('#scheme_type').val();
                        d.filter_type = $('#filter_type').val();
                        d.dist_code = $('#dist_code').val();
                        d.filter_1 = $('#filter_1').val();
                        d.filter_2 = $('#filter_2').val();
                        d._token = "<?php echo e(csrf_token()); ?>";
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('#loadingDi').hide();
                        $.alert({
                            title: 'Error!',
                            type: 'red',
                            icon: 'fa fa-warning',
                            content: 'Loading Error! Session timeout, please logout and login again.'
                        });
                    }
                },
                "initComplete": function () {
                    $('#loadingDi').hide();
                },
                "columns": [
                    { "data": "blkUlb_name" },
                    { "data": "incomplete_data" },
                    { "data": "no_aadhar" },
                    { "data": "dup_aadhar" },
                    { "data": "bank_failure" },
                    { "data": "dup_bank" },
                    { "data": "no_mobile" },
                    { "data": "dup_mobile" },
                ]
            });
        }
    });
</script>
<?php echo $__env->make('layouts.app-template-datatable_new', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>