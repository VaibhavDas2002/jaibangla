<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Personal Details</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" fdprocessedid="pcxrxb"><i
                    class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div><strong>Name :</strong> <?php echo e($row->ben_fname); ?> <?php echo e($row->ben_mname); ?>

                    <?php echo e($row->ben_lname); ?>

                </div>
            </div>
            <div class="col-md-6">
                <div><strong>Gender:</strong> <?php echo e($row->gender); ?></div>
            </div>
            <?php if(!is_null($row->dob)): ?>
                <div class="col-md-6">
                    <div><strong>Date of Birth (DD-MM-YYYY):</strong>
                        <?php echo e(date('d/m/Y', strtotime($row->dob))); ?></div>

                </div>
            <?php endif; ?>
            <div class="col-md-6">
                <div><strong>Father's Name :</strong> <?php echo e($row->father_fname); ?>

                    <?php echo e($row->father_mname); ?>

                    <?php echo e($row->father_lname); ?>

                </div>
            </div>

            <div class="col-md-6">
                <div><strong>Mother's Name :</strong> <?php echo e($row->mother_fname); ?>

                    <?php echo e($row->mother_mname); ?>

                    <?php echo e($row->mother_lname); ?>

                </div>
            </div>
            <div class="col-md-6">
                <div><strong>Caste:</strong> <?php echo e($row->caste); ?></div>
            </div>
            <div class="col-md-6">
                <div><strong>Marital Status:</strong> <?php echo e($row->marital_status); ?></div>
            </div>
            <?php if($scheme_id == 11): ?>
                <div class="col-md-6">
                    <div><strong>Husband's Name :</strong> <?php echo e($row->husband_fname); ?>

                        <?php echo e($row->husband_mname); ?>

                        <?php echo e($row->husband_lname); ?>

                    </div>
                </div>
            <?php endif; ?>

            <div class="col-md-6">
                <div><strong>Spouse Name :</strong> <?php echo e($row->spouse_fname); ?>

                    <?php echo e($row->spouse_mname); ?>

                    <?php echo e($row->spouse_lname); ?>

                </div>
            </div>

            <div class="col-md-6">
                <div><strong>Monthly Family Income(Rs.):</strong>
                    <?php echo e($row->mothly_income); ?>

                </div>
            </div>
        </div>

        <?php echo $__env->yieldContent('personal-add'); ?>
        
    </div>
</div>