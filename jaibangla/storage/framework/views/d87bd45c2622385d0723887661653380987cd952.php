<!--Additional Details  -->
<?php if($scheme_id == 17 || $scheme_id == 2 || $scheme_id == 10): ?>
    <div class="box box-primary collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title">Additional Details</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" fdprocessedid="pcxrxb"><i
                        class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <?php if($scheme_id == 2): ?>
                    <div class="col-md-6">
                        <div><strong>Type of Disability:</strong> <?php echo e($row->type_disability); ?>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div><strong>Percentage of Disablity:</strong>
                            <?php echo e($row->percentage_disability); ?></div>
                    </div>
                    <div class="col-md-6">
                        <div><strong>Certifying Authority:</strong> <?php echo e($row->certifying_auth); ?>

                        </div>
                    </div>
                <?php endif; ?>
                <?php if($scheme_id == 17): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div><strong>Phase:</strong> <?php echo e($row->app_phase); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div><strong>Temple Type:</strong> <?php echo e($row->temple_type); ?></div>
                        </div>

                    </div>
                <?php endif; ?>

                <?php if($scheme_id == 10): ?>
                    <?php if($row->sm_flag == 1): ?>
                        <div class="row color1">
                            <div class="col-md-12">
                                <h3>Sarasori Mukhyamantri</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="">Mobile Number</label>
                                <span id="" class="text-danger"><?php echo e($row->sm_mobile_no); ?></span>
                            </div>
                        </div>
                        <br />
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php echo $__env->yieldContent('additional-add'); ?>
        </div>
    </div>

<?php endif; ?>