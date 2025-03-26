<?php $__env->startSection('action-content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <?php if(($message = Session::get('msg'))): ?>
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong><?php echo e($message); ?></strong>
                </div>
            <?php endif; ?>            
            <?php if(($message1 = Session::get('msg1'))): ?>
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong><?php echo e($message1); ?></strong>
                </div>
            <?php endif; ?>
            <div class="panel panel-default">
                <div class="panel-heading">Set Scheme Capacity</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="<?php echo e(route('linelisting-scheme-capacity')); ?>" onsubmit="return validate();">
                        <?php echo e(csrf_field()); ?>

                        
                        <div class="form-group<?php echo e($errors->has('scheme_type') ? ' has-error' : ''); ?>" id="scheme_div">
                            <label for="scheme_type" class="col-md-4 control-label">Scheme</label>

                            <div class="col-md-6">
                                <select name="scheme_type" id="scheme_type" class="form-control select2">
                                    <option value="0">--Select Scheme Type--</option>
                                    <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($scheme->id); ?>"><?php echo e($scheme->scheme_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php if($errors->has('scheme_type')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('scheme_type')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group<?php echo e($errors->has('cap_level') ? ' has-error' : ''); ?>">
                            <label for="cap_level" class="col-md-4 control-label">Capacity Level</label>

                            <div class="col-md-6">
                                <select name="cap_level" id="cap_level" class="form-control select2" onchange="capLevel()">
                                    <option value="0">--Select Capacity Level--</option>
                                    <option value="D">District</option>
                                    <option value="SD">Sub-District</option>
                                    <option value="BK">BLock</option>
                                </select>
                                <?php if($errors->has('cap_level')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('cap_level')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group<?php echo e($errors->has('district') ? ' has-error' : ''); ?>" id="district_div">
                            <label for="district" class="col-md-4 control-label">Distict</label>

                            <div class="col-md-6">
                                <select name="district" id="district" class="form-control select2">
                                    <option value="0">--Select District--</option>
                                    <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($district->district_code); ?>"><?php echo e($district->district_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php if($errors->has('district')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('district')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group<?php echo e($errors->has('is_rural_urban') ? ' has-error' : ''); ?>" id="rural_urban_div">
                            <label for="is_rural_urban" class="col-md-4 control-label">Rural/Urban (Block/Municipality)</label>

                            <div class="col-md-6">
                                <select name="is_rural_urban" id="is_rural_urban" class="form-control select2">
                                    <option value="0">--Select Rural/Urban--</option>
                                    <option value="2">Block (For Rural Area)</option>
                                    <option value="1">Municiplity (For Urban Area)</option>
                                </select>
                                <?php if($errors->has('is_rural_urban')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('is_rural_urban')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-4">
                                <button type="submit" class="btn btn-primary btn-block" name="submit_btn" id="submit_btn" disabled>
                                    Submit
                                </button>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo e(asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
<script>
    window.onload=function(){
        $('#district_div').hide();
        $('#rural_urban_div').hide();
        $('#submit_btn').removeAttr('disabled');
    }

    function capLevel(){
        var cap_l = $('#cap_level').val();
        if (cap_l == 'D') {
            $('#district_div').hide();
            $('#rural_urban_div').hide();
        }
        else if (cap_l == 'SD' || cap_l == 'BK') {
            $('#district_div').show();
            // $('#rural_urban_div').show();
        }
    }

    function validate() {
        if ($('#scheme_type').val() == 0) {
            alert('Please select scheme type');
            return false;
        }
        if ($('#cap_level').val() == 0) {
            alert('Please select capacity level');
            return false;
        }
        if ($('#cap_level').val() == 'SD') {
            if ($('#district').val() == 0) {
                alert('Please select district');
                return false;
            }
            // if ($('#is_rural_urban').val() == 0) {
            //     alert('Please select rural/urban');
            //     return false;
            // }
        }
        return true;
    }
</script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('scheme-capacity.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>