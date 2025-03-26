<div class="row">
    <div class="form-group col-md-12">
        <hr>
    </div>
    <?php if($scheme_id == 2): ?>
        <div class="form-group col-md-4">
            <label class="required-field">Type of Disability</label>
            <select class="form-control" name="disablity_type" id="disablity_type">
                <?php if($type == $op_type): ?>
                    <?php $__currentLoopData = Config::get('constants.disablity_type'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if($row->type_disability == $key): ?> selected <?php endif; ?>>
                            <?php echo e($val); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>

                    <option value="">--Select--</option>
                    <?php $__currentLoopData = Config::get('constants.disablity_type'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(old('disablity_type') == $key): ?> selected <?php endif; ?>><?php echo e($val); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </select>
            <span id="error_disablity_type" class="text-danger"></span>
        </div>
        <div class="form-group col-md-4">
            <label class="required-field">Percentage of Disablity</label>
            <input type="text" name="disablity_type_percentage" id="disablity_type_percentage" class="form-control "
                placeholder="Percentage" maxlength="5"
                value="<?php echo e($type == $op_type ? $row->percentage_disability : old('disablity_type_percentage')); ?>" />
            <span id="error_disablity_type_percentage" class="text-danger"></span>

        </div>
        <div class="form-group col-md-4">
            <label class="required-field">Authority Name</label>
            <input type="text" name="disablity_type_authority" id="disablity_type_authority" class="form-control txtOnly"
                placeholder="Certifying Authority" maxlength="200"
                value="<?php echo e($type == $op_type ? $row->certifying_auth : old('disablity_type_authority')); ?>" />
            <span id="error_disablity_type_authority" class="text-danger"></span>

        </div>
        <div class="form-group col-md-4">
            <label class="required-field">Authority Designation</label>
            <input type="text" name="disability_designation" id="disability_designation" class="form-control txtOnly"
                placeholder="Designation Name" maxlength="200"
                value="<?php echo e($type == $op_type ? $row->disability_designation : old('disability_designation')); ?>" />
            <span id="error_disability_designation" class="text-danger"></span>
        </div>
    <?php endif; ?>

    <?php if($scheme_id == 5): ?>
        <div class="form-group col-md-4">
            <label>Belongs to Fisherman Community</label>
            <select class="form-control" name="fisherman_comm" id="fisherman_comm" tabindex="14">
                <?php if($type == $op_type): ?>
                    <option value="YES" <?php if($row->fisherman_comm == "YES"): ?> selected <?php endif; ?>>Yes</option>
                    <option value="NO" <?php if($row->fisherman_comm == "NO"): ?> selected <?php endif; ?>>No</option>
                <?php else: ?>

                    <option value="">--Select--</option>
                    <option value="YES" <?php if(old('fisherman_comm') == $key): ?> <?php endif; ?>>Yes</option>
                    <option value="NO" <?php if(old('fisherman_comm') == $key): ?> <?php endif; ?>>No</option>
                <?php endif; ?>

            </select>
            <span id="error_fisherman_comm" class="text-danger"></span>
        </div>

        <div class="form-group col-md-4">
            <label class="required-field">Physically Handicapped </label>
            <select class="form-control" name="phy_hadi_status" id="phy_hadi_status" tabindex="15">
                <?php if($type == $op_type): ?>
                    <option value="No" <?php if($row->phy_hadi_status == "No"): ?> selected <?php endif; ?>>No</option>
                    <option value="Yes" <?php if($row->phy_hadi_status == "Yes"): ?> selected <?php endif; ?>>Yes</option>

                <?php else: ?>
                    <option value="No" <?php if(old('phy_hadi_status') == 'No'): ?> selected <?php endif; ?>>No</option>
                    <option value="Yes" <?php if(old('phy_hadi_status') == 'Yes'): ?> <?php endif; ?>>Yes</option>
                <?php endif; ?>
            </select>
            <span id="error_phy_hadi_status" class="text-danger"></span>
        </div>
    <?php endif; ?>

    <?php if($scheme_id == 17): ?>

        <div class="form-group col-md-4">
            <label class="required-field">Select Application Phase</label>
            <select class="form-control" name="app_phase" id="app_phase">
                <?php if($type == $op_type): ?>
                    <?php $__currentLoopData = Config::get('constants.purohit_phase'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if($row->app_phase == $key): ?> selected <?php endif; ?>>
                            <?php echo e($val); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <option value="">--Select--</option>
                    <?php $__currentLoopData = Config::get('constants.purohit_phase'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(old('app_phase') == $key): ?> selected <?php endif; ?>><?php echo e($val); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </select>
            <span id="error_app_phase" class="text-danger"></span>
        </div>

        <div class="form-group col-md-4">
            <label class="required-field">Temple Type</label>
            <select class="form-control" name="temple_type" id="temple_type">
                <?php if($type == $op_type): ?>

                    <option value='Temple Purohit' <?php if($row->temple_type == 'Temple Purohit'): ?> selected <?php endif; ?>>Temple Purohit
                    </option>
                    <option value='Tribal Religious Place Purohit' <?php if($row->temple_type == 'Tribal Religious Place Purohit'): ?>
                    selected <?php endif; ?>>Tribal Religious Place Purohit</option>
                    <option value='Community Purohit' <?php if($row->temple_type == 'Community Purohit'): ?> selected <?php endif; ?>>Community
                        Purohit</option>
                <?php else: ?>

                    <option value="">--Select--</option>
                    <option value='Temple Purohit' <?php if(old('temple_type') == 'Temple Purohit'): ?> selected <?php endif; ?>>Temple Purohit
                    </option>
                    <option value='Tribal Religious Place Purohit' <?php if(old('temple_type') == 'Tribal Religious Place Purohit'): ?>
                    selected <?php endif; ?>>Tribal Religious Place Purohit</option>
                    <option value='Community Purohit' <?php if(old('temple_type') == 'Community Purohit'): ?> selected <?php endif; ?>>Community
                        Purohit</option>
                <?php endif; ?>
            </select>
            <span id="error_temple_type" class="text-danger"></span>
        </div>
    <?php endif; ?>
    <?php if($scheme_id == 11): ?>
        <div class="form-group col-md-12">
            <label class="">Husband's Name</label>
        </div>

        <div class="form-group col-md-4">
            <label class="required-field">First Name</label>
            <input type="text" name="husband_first_name" id="husband_first_name" class="form-control txtOnly"
                placeholder="First Name" maxlength="200" value="<?php echo e($type == $op_type ? $row->husband_fname : old('husband_first_name')); ?>" tabindex="4" />
            <span id="error_husband_first_name" class="text-danger"></span>
        </div>
        <div class="form-group col-md-4">
            <label>Middle Name</label>
            <input type="text" name="husband_middle_name" id="husband_middle_name" class="form-control txtOnly"
                placeholder="Middle Name" maxlength="100" value="<?php echo e($type == $op_type ? $row->husband_mname : old('husband_middle_name')); ?>" tabindex="5" />
            <span id="error_husband_middle_name" class="text-danger"></span>
        </div>
        <div class="form-group col-md-4">
            <label class="required-field">Last Name</label>
            <input type="text" name="husband_last_name" id="husband_last_name" class="form-control txtOnly"
                placeholder="Last Name" maxlength="200" value="<?php echo e($type == $op_type ? $row->husband_lname : old('husband_last_name')); ?>" tabindex="6" />
            <span id="error_husband_last_name" class="text-danger"></span>
        </div>
    <?php endif; ?>
</div>