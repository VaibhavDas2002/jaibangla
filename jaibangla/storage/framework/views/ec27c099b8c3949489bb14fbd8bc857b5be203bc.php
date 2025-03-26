<?php $__env->startSection('action-content'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php if(($message = Session::get('success'))): ?>
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e($message); ?></strong>
                            </div>
                        <?php endif; ?>

                        <form class="form-horizontal" role="form" method="POST" action="">
                            <?php echo e(csrf_field()); ?>


                            <div class="form-group<?php echo e($errors->has('scheme') ? ' has-error' : ''); ?>">
                                <label for="scheme" class="col-md-4 control-label">Select Scheme</label>

                                <div class="col-md-6">
                                    <select onchange="navigateToScheme(this.value)" class="form-control" name="scheme"
                                        id="scheme">
                                        <option value="">--Select--</option>
                                        <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="cmo-grievance-workflow?scheme_id=<?php echo e($scheme->id); ?>&type=1">
                                                <?php echo e($scheme->scheme_name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>

                                    <?php if($errors->has('scheme')): ?>
                                        <span class="help-block text-danger">
                                            <?php echo e($errors->first('scheme')); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <script>
                                // Function to redirect to the selected scheme's URL
                                function navigateToScheme(url) {
                                    if (url) {
                                        window.location.href = url;
                                    }
                                }
                            </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('JBProcessApplication.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>