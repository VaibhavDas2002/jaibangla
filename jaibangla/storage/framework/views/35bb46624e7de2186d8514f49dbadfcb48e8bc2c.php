<div class="box box-primary collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title">Contact Details</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" fdprocessedid="pcxrxb"><i
                    class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div><strong>State:</strong> West Bengal</div>

            </div>
            <div class="col-md-6">
                <div><strong>Assembly Constitution:</strong> <?php echo e($row->assembly_name); ?>

                </div>
            </div>
            <div class="col-md-6">
                <div><strong>District:</strong> <?php echo e($district_name); ?></div>
            </div>

            <div class="col-md-6">
                <div><strong>Block/Municipality/Corp:</strong><?php echo e($block_name); ?></div>
            </div>

            <div class="col-md-6">
                <div><strong>GP/Ward No.:</strong><?php echo e($gp_name); ?></div>
            </div>
            <div class="col-md-6">
                <div><strong>Village/Town/City:</strong> <?php echo e($row->village_town_city); ?>

                </div>
            </div>
            <div class="col-md-6">
                <div><strong>House/Premise No.:</strong> <?php echo e($row->house_premise_no); ?>

                </div>
            </div>
            <div class="col-md-6">
                <div><strong>Post Office:</strong> <?php echo e($row->post_office); ?></div>
            </div>
            <div class="col-md-6">
                <div><strong>Pin Code:</strong> <?php echo e($row->pincode); ?></div>
            </div>
            <div class="col-md-6">
                <div><strong>Police Station:</strong><?php echo e($row->police_station); ?></div>
            </div>
            <div class="col-md-6">
                <div><strong>Mobile Number:</strong><?php echo e($row->mobile_no); ?></div>
            </div>
            <div class="col-md-6">
                <div><strong>Email Id., if available:</strong> <?php echo e($row->email); ?>

                </div>
            </div>
        </div>

        <?php echo $__env->yieldContent('contact-add'); ?>
    </div>
</div>