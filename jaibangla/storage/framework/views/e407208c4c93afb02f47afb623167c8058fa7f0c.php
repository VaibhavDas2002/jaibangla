<style>
    .row {
        margin-right: 0px !important;
        margin-left: 0px !important;
        margin-top: 1% !important;
    }

    .applnlbl {
        color: #006600;
        font-size: 20px;

    }

    .select2 {
        width: 400px !important;
    }
</style>


<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Cross - Scheme Configuration</h1>
        <ol class="breadcrumb">
            <i class="fa fa-clock-o"></i> Date:
            <span style="font-size: 12px; font-weight: bold;">
                <span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span>
            </span>
        </ol>
    </section>

    <section class="content">
        <div id="loadingDiv"></div>
        <div class="col-md-6 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"
                    style="font-size: 15px; font-weight: bold; font-style: italic; padding: 5px 15px;">
                    <span id="panel-icon"></span> Configure Duplication based on Scheme
                </div>
                <div class="panel-body" style="padding: 5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <?php if(Session::has('success')): ?>
                                <div class="alert alert-success alert-block">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong><?php echo e(Session::get('success')); ?></strong>
                                </div>
                            <?php endif; ?>

                            <?php if(Session::has('message')): ?>
                                <div class="alert alert-danger alert-block">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong><?php echo e(Session::get('message')); ?></strong>
                                </div>
                            <?php endif; ?>


                            <div class="row">
                                <form method="POST" role="form" action="<?php echo e(route('scheme_config_store')); ?>">
                                    <?php echo e(csrf_field()); ?>

                                    <div class="form-group col-md-12 applnDiv">
                                        <label class="applnlbl"><b>Select Type:</b><span
                                                class="text-danger">*</span></label>
                                        <label>
                                            <input type="radio" class="config-type" id="config-type-0"
                                                name="config_type" value="0" checked>
                                            Same Scheme
                                        </label>
                                        <label>
                                            <input type="radio" class="config-type" id="config-type-1"
                                                name="config_type" value="1">
                                            Cross-Scheme
                                        </label>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="scheme_id" class="control-label">Scheme <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control select" name="scheme_id" id="scheme_id"
                                                    required>
                                                    <option value="">--Select Scheme--</option>
                                                    <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($scheme->id); ?>"><?php echo e($scheme->scheme_name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <span class="text-danger" id="error_scheme_id"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="cross_scheme" style="display: none;">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="schemelist" class="control-label">Cross Scheme <span
                                                        class="text-danger">*</span></label>
                                                <select id="scheme" class="form-control select2" name="schemelist[]"
                                                    multiple="multiple">
                                                    <option value="">--Select Scheme--</option>
                                                    <?php $__currentLoopData = $scheme_all; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($scheme->id); ?>"><?php echo e($scheme->scheme_name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="field_type">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="field_type" class="control-label">Field Type <span
                                                        class="text-danger">*</span></label>
                                                <?php $__currentLoopData = Config::get('constants.dup_config'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="checkbox">
                                                        <label class="">
                                                            <input type="checkbox" id="<?php echo e($key); ?>" name="field_type[]"
                                                                value="<?php echo e($key); ?>">
                                                            <?php echo e($desc); ?>

                                                        </label>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-4">
                                            <button type="submit" class="btn btn-primary">Create</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<script src="<?php echo e(asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
<script>
    $(document).ready(function () {
        // Clock update with moment.js
        setInterval(function () {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);

        $('#loadingDiv').hide();
        $('#cross_scheme').hide();

        $('input[name="config_type"]').change(function () {
            if ($('#config-type-1').is(':checked')) {
                $('#cross_scheme').css('display', 'inline'); // Use block here
            } else {
                $('#cross_scheme').hide();
            }
        });
    });
</script>
<?php echo $__env->make('layouts.app-template-datatable_new', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>