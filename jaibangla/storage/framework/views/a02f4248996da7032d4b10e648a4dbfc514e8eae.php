<?php if(in_array($scheme_id, [2, 10, 11, 13, 17])): ?>
    <div class="row">
        <label>In case the applicant is receiving pension from other sources</label>
        <br />
        <label>1.</label>
        <input type="text" name="receiving_pension_other_source_1" id="receiving_pension_other_source_1"
            class="form-control" placeholder=""
            value="<?php echo e($type == $op_type ? $row->receiving_pension_other_source_1 : old('receiving_pension_other_source_1')); ?>"
            maxlength='300' tabindex="3" />
        <label>2.</label>
        <input type="text" name="receiving_pension_other_source_2" id="receiving_pension_other_source_2"
            class="form-control" placeholder=""
            value="<?php echo e($type == $op_type ? $row->receiving_pension_other_source_2 : old('receiving_pension_other_source_2')); ?>"
            maxlength='300' tabindex="3" />
    </div>
<?php endif; ?>
<?php if($scheme_id != 2 || $scheme_id != 11): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="modal_field_name">In the event of my death, I hereby nominate (Please mention Name, Address &
                Relationship)
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-4">
            <label class="">Name</label>
            <input type="text" name="nominate_name" id="nominate_name" class="form-control txtOnly" placeholder="Name"
                value="<?php echo e($type == $op_type ? $row->nominate_name : old('nominate_name')); ?>" maxlength='200' />
            <span id="error_nominate_name" class="text-danger"></span>
        </div>

        <div class="form-group col-md-4">
            <label class="">Address</label>
            <input type="text" name="nominate_address" id="nominate_address" class="form-control special-char"
                placeholder="Address" value="<?php echo e($type == $op_type ? $row->nominate_address : old('nominate_address')); ?>"
                maxlength='200' />
            <span id="error_nominate_address" class="text-danger"></span>
        </div>

        <div class="form-group col-md-4">
            <label class="">Relationship</label>
            <input type="text" name="nominate_relationship" id="nominate_relationship" class="form-control txtOnly"
                placeholder="Relationship"
                value="<?php echo e($type == $op_type ? $row->nominate_relationship : old('nominate_relationship')); ?>" maxlength='200' />
            <span id="error_nominate_relationship" class="text-danger"></span>
        </div>


    </div>
    <?php if($scheme_id == 17): ?>

        <div class="row">
            <div class="form-group col-md-12">
                <label class="">to receive the rest amount payable to me till my death</label>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if($scheme_id == 17): ?>
    <div class="row">
        <div class="form-group col-md-12">
            <label class="">I <select name="ssp_y_n" id="ssp_y_n">
                    <?php if($type == $op_type): ?>
                        <option value="1" <?php if($row->ssp_y_n == 1): ?> selected <?php endif; ?>> am </option>
                        <option value="0" <?php if($row->ssp_y_n == 0): ?> selected <?php endif; ?>>am not </option>
                    <?php else: ?>
                        <option value="1"> am </option>
                        <option value="0">am not </option>
                    <?php endif; ?>

                </select> a beneficiary of any other Social Security pension scheme or a recipient of Government pension or
                pension from any other organization.</label>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            <label class="">I <select name="pucca_house_y_n" id="pucca_house_y_n">
                <?php if($type == $op_type): ?>
                <option value="1" <?php if($row->pucca_house_y_n == 1): ?> selected <?php endif; ?>>do</option>
                <option value="0" <?php if($row->pucca_house_y_n == 0): ?> selected <?php endif; ?>>do not</option>
                <?php else: ?>
                <option value="1" >do</option>
                <option value="0" >do not</option>
                <?php endif; ?>
                </select> have Pucca dwelling house.</label>
        </div>
    </div>
    <div class="form-group col-md-12" tabindex="4">
        <label>Presently, I am reciving following pension(s) from</label>
        <br />
        <?php if($type == $op_type ): ?>
        <?php
                  $row_receive_pension = array();
                  if($row->receive_pension!=null)
                    $row_receive_pension = explode(',',$row->receive_pension);
                   
                ?>

<?php $__currentLoopData = Config::get('constants.pension_body'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label>
                      <input type="checkbox" class="receive-pension" name="receive_pension[]" value="<?php echo e($key); ?>"
                        <?php if(in_array($key,$row_receive_pension,true)): ?> checked <?php endif; ?>> <?php echo e($desc); ?>

                    </label>
                    <br />
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <?php else: ?>
        <?php $__currentLoopData = Config::get('constants.pension_body'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label>
                <input type="checkbox" class="receive-pension" name="receive_pension[]" value="<?php echo e($key); ?>" <?php if(in_array($key, $old_receive_pension, true)): ?> checked <?php endif; ?>> <?php echo e($desc); ?>

            </label>
            <br />
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        
        <?php endif; ?>
        
    </div>


<?php else: ?>
    <div class="row">
        <div class="form-group col-md-12" tabindex="5">
            <label>Presently, I am receiving the following social Security Pension/s (Please tick)</label>
            <br />
            <?php if($type == $op_type): ?>
            <?php $__currentLoopData = Config::get('constants.social_pension_cat'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label>
                              <input type="checkbox" class="social-security-pension" name="social_security_pension[]"
                                value="<?php echo e($key); ?>" <?php if(in_array($key,$row_social_security_pension,true)): ?> checked <?php endif; ?>>
                              <?php echo e($desc); ?>

                            </label>
                            <br />
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <?php $__currentLoopData = Config::get('constants.social_pension_cat'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label>
                        <input type="checkbox" class="social-security-pension" name="social_security_pension[]" value="<?php echo e($key); ?>"
                            <?php if(in_array($key, $old_social_security_pension, true)): ?> checked <?php endif; ?>> <?php echo e($desc); ?>

                    </label>
                    <br />
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

        </div>
    </div>
    <br />
    <?php if($scheme_id == 11): ?>
        <div class="row">
            <div class="form-group col-md-12">
                <label class="">I hereby declare that i have not done remarriage</label>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>



<br />
<div align="center" class="col-md-12">
    <button type="button" name="previous_btn_decl_details" id="previous_btn_decl_details"
        class="btn btn-info btn-lg">Previous</button>
    <input type="button" class="btn btn-success btn-lg" name="btn_submit_preview" id="btn_submit_preview"
        value="Preview and Submit" data-toggle="modal" data-target="#confirm-submit_">

</div>
<br />