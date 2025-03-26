<div class="tab-pane active" id="personal_details">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4><b>Personal Details</b></h4>
        </div>
        <div class="panel-body">
            <?php if($type == 4): ?>
                <div class="form-group col-md-12">
                    <div class="form-group col-md-4">
                        <label class="required-field">Aadhaar Number</label>
                        <input type="text" name="aadhar_no_dup_check_cmo" id="aadhar_no_dup_check_cmo"
                            class="form-control NumOnly" placeholder="Aadhar No." maxlength="12"
                            value="<?php echo e(old('aadhar_no_dup_check_cmo')); ?>" />
                        <input type="hidden" name="grievance_id" id="grievance_id"  value="<?php echo e($grievance_id); ?>"/>
                        <span id="error_aadhar_no_dup_check_cmo" class="text-danger"></span>
                    </div>
                    <div class="form-group col-md-2" style="margin: 23px;">
                        <input class="btn btn-danger" type="submit" name="btnDuplicateCMOSubmit" id="btnDuplicateCMOSubmit"
                            value="Check Duplicate">

                    </div>
                </div>
            <?php endif; ?>
            <?php if($type == 1): ?>

                <div class="form-group col-md-12">
                    <div class="form-group col-md-4">
                        <label class="required-field">Aadhaar Number</label>
                        <input type="text" name="aadhar_no_dup_check" id="aadhar_no_dup_check" class="form-control NumOnly"
                            placeholder="Aadhar No." maxlength="12" value="<?php echo e(old('aadhar_no_dup_check')); ?>" />
                        <span id="error_aadhar_no_dup_check" class="text-danger"></span>
                    </div>
                    <div class="form-group col-md-2" style="margin: 23px;">
                        <input class="btn btn-danger" type="submit" name="btnDuplicateSubmit" id="btnDuplicateSubmit"
                            value="Check Duplicate">

                    </div>
                </div>

                <div class="form-group col-md-12">
                    <div class="form-group col-md-4">
                        <label class="required-field">Application Date</label>
                        <input type="date" name="application_date" id="application_date" class="form-control"
                            max="<?php    echo date("Y-m-d"); ?>" />
                        <span id="error_application_date" class="text-danger"></span>

                    </div>
                </div>
            <?php endif; ?>
            <?php if($type == 1): ?>
                        <div class="form-group col-md-12">
                            <label class="required-field"><b>Application Type: </b></label>
                        </div>
                        <div class="form-group col-md-4 ">
                            <select class="form-control" name="entry_type" id="entry_type" <?php if(in_array('entry_type', $readonly)): ?>
                            readonly <?php endif; ?>>
                                <?php 
                                    $sel_val = '';
                                    if ($type == $op_type && isset($row->entry_type) && $row->entry_type == "Normal") {
                                        $sel_val = 'Normal';
                                    } else if ($type == $op_type && isset($row->entry_type) && $row->entry_type == "Form through Duare Sarkar camp") {
                                        $sel_val = 'Form through Duare Sarkar camp';
                                    } else {
                                        $sel_val = '';
                                    }
                                 ?>
                                <?php if($normal_entry): ?>
                                    <option value="Normal" <?php if($sel_val == "Normal"): ?> selected <?php endif; ?>>Normal Entry</option>
                                <?php endif; ?>
                                <?php if($ds_allow): ?>
                                    <option value="Form through Duare Sarkar camp" <?php if($sel_val == "Form through Duare Sarkar camp"): ?>
                                    selected <?php endif; ?>>Form through Duare Sarkar
                                        camp</option>
                                <?php endif; ?>

                            </select>
                        </div>

                        <div class="form-group">
                            <h3 class=""> For <b>Duare Sarkar</b> entry please select from dropdown <i><b>"Form
                                        through
                                        Duare Sarkar camp"</b></i></h3>
                        </div>
                        <div class="row duareSarkar" style="display:none;">
                            <div class="form-group col-md-4">
                                <label class="required-field">Duare Sarkar Registration No.</label>
                                <input type="text" name="ds_registration_no" id="ds_registration_no" class="form-control"
                                    placeholder="Duare Sarkar Registration No." maxlength="25"
                                    value="<?php echo e($type == $op_type ? $row->ds_registration_no : old('ds_registration_no')); ?>"
                                    <?php if(in_array('ds_registration_no', $readonly)): ?> readonly <?php endif; ?> />
                                <span id="error_ds_registration_no" class="text-danger"></span>

                            </div>
                            <div class="form-group col-md-4">
                                <label class="required-field">Duare Sarkar Date</label>
                                <input type="date" name="ds_date" id="ds_date" class="form-control"
                                    max="<?php    echo date("Y-m-d"); ?>"
                                    value="<?php echo e($type == $op_type ? $row->ds_date : old('ds_date')); ?>" <?php if(in_array('ds_date', $readonly)): ?> readonly <?php endif; ?> />
                                <span id="error_ds_date" class="text-danger"></span>

                            </div>
                        </div>

            <?php endif; ?>

            <div class="form-group col-md-12">
                <label class="">Beneficiary Name</label>
            </div>
            <input type="hidden" name="scheme_id" id="scheme_id" value="<?php echo e($scheme_id); ?>">
            <input type="hidden" name="type" id="type" value="<?php echo e($type); ?>">
            <div class="form-group col-md-4">
                <label class="required-field">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control txtOnly"
                    placeholder="First Name" maxlength="200"
                    value="<?php echo e($type == $op_type ? $row->ben_fname : old('first_name')); ?>" <?php if(in_array('first_name', $readonly)): ?> readonly <?php endif; ?> />
                <span id="error_first_name" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label>Middle Name</label>
                <input type="text" name="middle_name" id="middle_name" class="form-control txtOnly"
                    placeholder="Middle Name" maxlength="100"
                    value="<?php echo e($type == $op_type ? $row->ben_mname : old('middle_name')); ?>" <?php if(in_array('middle_name', $readonly)): ?> readonly <?php endif; ?> />
                <span id="error_middle_name" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control txtOnly" placeholder="Last Name"
                    maxlength="200" value="<?php echo e($type == $op_type ? $row->ben_lname : old('last_name')); ?>"
                    <?php if(in_array('last_name', $readonly)): ?> readonly <?php endif; ?> />
                <span id="error_last_name" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">Gender</label>
                <select class="form-control" name="gender" id="gender" <?php if(in_array('gender', $readonly)): ?> readonly
                <?php endif; ?>>
                    <?php if($type == $op_type): ?>
                        <?php $__currentLoopData = Config::get('constants.gender'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($scheme_id == 11 && in_array($key, array('Male', 'Other'))): ?>
                                <?php continue; ?>

                            <?php endif; ?>
                            <option value="<?php echo e($key); ?>" <?php if($row->gender == $key): ?> selected <?php endif; ?>>
                                <?php echo e($val); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <option value="">--Select--</option>
                        <?php $__currentLoopData = Config::get('constants.gender'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($scheme_id == 11 && in_array($key, array('Male', 'Other'))): ?>
                                <?php continue; ?>

                            <?php endif; ?>
                            <option value="<?php echo e($key); ?>" <?php if(old('gender') == $key): ?> selected <?php endif; ?>>
                                <?php echo e($val); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </select>
                <span id="error_gender" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="">Date of Birth</label>
                <input type="date" name="dob" id="dob" class="form-control"
                    value="<?php echo e($type == $op_type ? $row->dob : old('dob')); ?>" <?php if(in_array('dob', $readonly)): ?> readonly
                    <?php endif; ?> />
                <span id="error_dob" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">Age<span> (as on <?php echo e(date('d/m/Y')); ?>)</span></label>
                <label id="txt_age" class="form-control">
                    <?php echo e($type == $op_type ? $row->ben_age : old('txt_age')); ?>

                </label>

                <span id="error_txt_age" class="text-danger"></span>
            </div>

            <div class="form-group col-md-12">
                <label class="">Father's Name</label>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">First Name</label>
                <input type="text" name="father_first_name" id="father_first_name" class="form-control txtOnly"
                    placeholder="First Name" maxlength="200"
                    value="<?php echo e($type == $op_type ? $row->father_fname : old('father_first_name')); ?>"
                    <?php if(in_array('father_first_name', $readonly)): ?> readonly <?php endif; ?> />
                <span id="error_father_first_name" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label>Middle Name</label>
                <input type="text" name="father_middle_name" id="father_middle_name" class="form-control txtOnly"
                    placeholder="Middle Name" maxlength="100"
                    value="<?php echo e($type == $op_type ? $row->father_mname : old('father_middle_name')); ?>"
                    <?php if(in_array('father_middle_name', $readonly)): ?> readonly <?php endif; ?> />
                <span id="error_father_middle_name" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">Last Name</label>
                <input type="text" name="father_last_name" id="father_last_name" class="form-control txtOnly"
                    placeholder="Last Name" maxlength="200"
                    value="<?php echo e($type == $op_type ? $row->father_lname : old('father_last_name')); ?>"
                    <?php if(in_array('father_last_name', $readonly)): ?> readonly <?php endif; ?> />
                <span id="error_father_last_name" class="text-danger"></span>
            </div>

            <div class="form-group col-md-12">
                <label class="">Mother's Name</label>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">First Name</label>
                <input type="text" name="mother_first_name" id="mother_first_name" class="form-control txtOnly"
                    placeholder="First Name" maxlength="200"
                    value="<?php echo e($type == $op_type ? $row->mother_fname : old('mother_first_name')); ?>"
                    <?php if(in_array('mother_first_name', $readonly)): ?> readonly <?php endif; ?> />
                <span id="error_mother_first_name" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
                <label>Middle Name</label>
                <input type="text" name="mother_middle_name" id="mother_middle_name" class="form-control txtOnly"
                    placeholder="Middle Name" maxlength="100"
                    value="<?php echo e($type == $op_type ? $row->mother_mname : old('mother_middle_name')); ?>"
                    <?php if(in_array('mother_middle_name', $readonly)): ?> readonly <?php endif; ?> />
                <span id="error_mother_middle_name" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
                <label class="required-field">Last Name</label>
                <input type="text" name="mother_last_name" id="mother_last_name" class="form-control txtOnly"
                    placeholder="Last Name" maxlength="200"
                    value="<?php echo e($type == $op_type ? $row->mother_lname : old('mother_last_name')); ?>"
                    <?php if(in_array('mother_last_name', $readonly)): ?> readonly <?php endif; ?> />
                <span id="error_mother_last_name" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">Caste</label>
                <select class="form-control" name="caste_category" id="caste_category" <?php if(in_array('caste_category', $readonly)): ?> readonly <?php endif; ?>>
                    <?php if($type == $op_type): ?>
                        <?php if($scheme_id == 3): ?>
                            <option value="SC">SC</option>
                        <?php elseif($scheme_id == 1): ?>
                            <option value="ST">ST</option>
                        <?php else: ?>
                            <?php $__currentLoopData = Config::get('constants.caste'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php if($row->gender == $key): ?> selected <?php endif; ?>><?php echo e($val); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if($scheme_id == 3): ?>
                            <option value="SC">SC</option>
                        <?php elseif($scheme_id == 1): ?>
                            <option value="ST">ST</option>
                        <?php else: ?>
                            <?php $__currentLoopData = Config::get('constants.caste'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php if(old('caste_category') == $key): ?> selected <?php endif; ?>><?php echo e($val); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endif; ?>

                </select>
                <span id="error_caste_category" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4" id="caste_certificate_no_section">
                <label class="<?php echo e(in_array($scheme_id, [1, 3, 19]) ? 'required-field' : ''); ?>">Caste Certificate
                    No.</label>
                <input type="text" name="caste_certificate_no" id="caste_certificate_no" class="form-control"
                    placeholder="Caste Certificate No." maxlength="200"
                    value="<?php echo e($type == $op_type ? $row->caste_certificate_no : old('caste_certificate_no')); ?>"
                    <?php if(in_array('caste_certificate_no', $readonly)): ?> readonly <?php endif; ?> />
                <span id="error_caste_certificate_no" class="text-danger"></span>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">Marital Status</label>
                <select class="form-control" name="marital_status" id="marital_status" <?php if(in_array('marital_status', $readonly)): ?> readonly <?php endif; ?>>
                    <?php if($type == $op_type): ?>
                        <?php $__currentLoopData = Config::get('constants.marital_status'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php if($row->marital_status == $key): ?> selected <?php endif; ?>>
                                <?php echo e($val); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <option value="">--Select--</option>
                        <?php $__currentLoopData = Config::get('constants.marital_status'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php if(old('marital_status') == $key): ?> selected <?php endif; ?>>
                                <?php echo e($val); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </select>
                <span id="error_marital_status" class="text-danger"></span>
            </div>

            <div class="row" id="spouse_section">
                <div class="form-group col-md-4">
                    &nbsp;
                </div>
                <div class="form-group col-md-12">
                    <label class="">Spouse Name (if applicable)</label>
                </div>
                <div class="form-group col-md-4">
                    <label class="">First Name</label>
                    <input type="text" name="spouse_first_name" id="spouse_first_name" class="form-control txtOnly"
                        placeholder="First Name" maxlength="200"
                        value="<?php echo e($type == $op_type ? $row->spouse_fname : old('spouse_first_name')); ?>"
                        <?php if(in_array('spouse_first_name', $readonly)): ?> readonly <?php endif; ?> />
                    <span id="error_spouse_first_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                    <label>Middle Name</label>
                    <input type="text" name="spouse_middle_name" id="spouse_middle_name" class="form-control txtOnly"
                        placeholder="Middle Name" maxlength="100"
                        value="<?php echo e($type == $op_type ? $row->spouse_mname : old('spouse_middle_name')); ?>"
                        <?php if(in_array('spouse_middle_name', $readonly)): ?> readonly <?php endif; ?> />
                    <span id="error_spouse_middle_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                    <label class="">Last Name</label>
                    <input type="text" name="spouse_last_name" id="spouse_last_name" class="form-control txtOnly"
                        placeholder="Last Name" maxlength="200"
                        value="<?php echo e($type == $op_type ? $row->spouse_lname : old('spouse_last_name')); ?>"
                        <?php if(in_array('spouse_last_name', $readonly)): ?> readonly <?php endif; ?> />
                    <span id="error_spouse_last_name" class="text-danger"></span>
                </div>
            </div>

            <div class="form-group col-md-4">
                <label class="required-field">Monthly Family Income (In Rs)</label>
                <input type="text" name="monthly_income" id="monthly_income" class="form-control price-field"
                    placeholder="Monthly Family Income(Rs.)" maxlength="9"
                    value="<?php echo e($type == $op_type ? $row->mothly_income : old('monthly_income')); ?>"
                    <?php if(in_array('monthly_income', $readonly)): ?> readonly <?php endif; ?>>
                <span id="error_monthly_income" class="text-danger"></span>
            </div>
            <?php if($scheme_id == 2 || $scheme_id == 5 || $scheme_id == 17 || $scheme_id == 11): ?>
                <div class="additional_details">
                    <hr>
                    <?php echo $__env->make('JBformEntry.personal_additional', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </div>
            <?php endif; ?>
            <div class="col-md-12" align="center">
                <button type="button" name="btn_personal_details" id="btn_personal_details"
                    class="btn btn-success btn-lg">Next</button>
            </div>
            </br>
            </br>
        </div>
    </div>
</div>

<script src="<?php echo e(asset("js/FormEntry/personal_details.js")); ?>"></script>