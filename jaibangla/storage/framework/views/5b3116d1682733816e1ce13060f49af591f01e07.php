<?php $__env->startSection('action-content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="">
                        <?php echo e(csrf_field()); ?> 

                        <div class="form-group<?php echo e($errors->has('scheme') ? ' has-error' : ''); ?>">
                            <label for="scheme" class="col-md-4 control-label">Select Scheme</label>

                            <div class="col-md-6">
                                <select onchange="la(this.value)" class="form-control" name="scheme"  id="scheme">
                                    <option value="">--Select--</option>
                                    <?php $__currentLoopData = $scheme_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($scheme->id == '2'): ?>
                                    <option value="<?php echo e(url('application-list-read-only-edit-bsk')); ?>?pr1=<?php echo e($scheme->short_code); ?>"><?php echo e($scheme->display_name); ?></option>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                          
                                </select>
                                <span id="error_construction" class="text-danger"></span>
                            </div>
                        </div>

                        <script>
                            function la(src)
                            {
                                window.location=src;
                            }
                            
                        </script>

                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('commonView.update_base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>