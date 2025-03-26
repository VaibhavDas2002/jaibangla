<input id="designation_id" type="hidden" value="<?php echo e($designation_id); ?>">

<?php if($is_verifier && $view_type == 1): ?>
    <?php $__env->startSection('form_section'); ?>
    <?php if($row->next_level_role_id == null): ?>
        <form method="post" action="<?php echo e(route('jb-forward')); ?>">
            <?php echo e(csrf_field()); ?>

            <input type="hidden" name="benId" value="<?php echo e($row->id); ?>">
            <input type="hidden" name="scheme_id" value="<?php echo e($row->scheme_id); ?>">
            <div class="section1  example-screen">
                <div class="row">
                    <div class="col-md-12">
                        <input style="width:100%; padding: 2%; margin:1%;" type="text" name="comments" id="comments"
                            class="form-control" placeholder="Comments" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4" style="text-align: center;"><input type="submit" name="submit" value="Reject"
                            id="Rejectsubmit" class="btn btn-danger btn-lg"></div>
                    <div class="col-md-4" style="text-align: center;"><input type="submit" name="submit" value="Revert"
                            id="Revertsubmit" class="btn btn-info btn-lg"></div>
                    <?php if($verifyBtnvisible == 1): ?>
                        <div class="col-md-4" style="text-align: center;"><input type="submit" name="submit" value="Verify"
                                id="Verifysubmit" class="btn btn-success btn-lg"></div>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    <?php endif; ?>
    <?php $__env->stopSection(); ?>
<?php elseif($is_approver && $view_type == 1): ?>
    <?php $__env->startSection('form_section'); ?>
    <form method="post" action="<?php echo e(route('jb-forward-approve')); ?>">
        <?php echo e(csrf_field()); ?>

        <input type="hidden" name="scheme_id" value="<?php echo e($row->scheme_id); ?>">
        <input type="hidden" name="benId" value="<?php echo e($row->id); ?>">
        <div class="row">
            <div class="col-md-3 text-center">
                <input type="submit" name="submit" value="Reject" id="Rejectsubmit"
                    class="btn btn-danger btn-lg btn-action">
            </div>
            <div class="col-md-3 text-center">
                <input type="submit" name="submit" value="Revert" id="Revertsubmit"
                    class="btn btn-primary btn-lg btn-action">
            </div>
            <?php if($approveBtnvisible == 1): ?>
                <div class="col-md-3 text-center">
                    <input type="submit" name="submit" value="Approve" id="Approvesubmit"
                        class="btn btn-success btn-lg btn-action">
                </div>
            <?php endif; ?>
        </div>
    </form>

    <?php $__env->stopSection(); ?>

<?php elseif($is_hod && $view_type == 2): ?>
<?php $__env->startSection('form_section'); ?>
    <form method="post" action="<?php echo e(route('jb-forward-recomend')); ?>">
        <?php echo e(csrf_field()); ?>

        <input type="hidden" name="scheme_id" value="<?php echo e($row->scheme_id); ?>">
        <input type="hidden" name="benId" value="<?php echo e($row->id); ?>">
        <div class="row">
            <div class="col-md-3 text-center">
                <input type="submit" name="submit" value="Reject" id="Rejectsubmit"
                    class="btn btn-danger btn-lg btn-action">
            </div>
            <div class="col-md-3 text-center">
                <input type="submit" name="submit" value="Revert" id="Revertsubmit"
                    class="btn btn-primary btn-lg btn-action">
            </div>
            <?php if($recomendBtnvisible == 1): ?>
                <div class="col-md-3 text-center">
                    <input type="submit" name="submit" value="Recomend" id="Recomendsubmit"
                        class="btn btn-success btn-lg btn-action">
                </div>
            <?php endif; ?>
        </div>
    </form>

    <?php $__env->stopSection(); ?>
<?php endif; ?>
<?php echo $__env->make('pension-details-view.pension_view_details', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>