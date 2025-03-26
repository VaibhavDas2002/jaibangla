<?php $__env->startSection('action-content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Select Pension Scheme</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="">
                        <?php echo e(csrf_field()); ?>


                        <div class="form-group<?php echo e($errors->has('scheme') ? ' has-error' : ''); ?>">
                            <label for="scheme" class="col-md-4 control-label">Scheme Type</label>

                            <div class="col-md-6">
                                <select onchange="la(this.value)" class="form-control " name="scheme" id="scheme">
                                    <option value="">--Select--</option>
                                    <?php $__currentLoopData = $return_arr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $arr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($arr['active'] == 1): ?>
                                            <option value="jb-pension?scheme_id=<?php echo e(encrypt($arr['id'])); ?>&type=<?php echo e(1); ?>">
                                                <?php echo e($arr['display_name']); ?></option>
                                        <?php else: ?>
                                            <option value="#" disabled><?php echo e($arr['display_name']); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                                </select>
                                <span id="error_construction" class="text-danger"></span>
                            </div>
                        </div>

                        <script>
                            function la(src) {
                                window.location = src;
                            }

                        </script>


                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('portal.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>