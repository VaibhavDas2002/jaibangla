<div class="box box-primary collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title">Personal Identification Number(S)</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" fdprocessedid="pcxrxb"><i
                    class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div><strong>Digital Ration Card No.:</strong> <?php echo e($row->ration_card_no); ?>

                </div>
            </div>

            <div class="col-md-6">
                <div><strong>AHL TIN: </strong><?php echo e($row->ahl_tin); ?></div>
            </div>
            <?php if($scheme_id == 2): ?>
                        <?php 
                            if (trim($row->aadhar_exits) != '') {
                                $sel_aadhar_exits = $row->aadhar_exits;
                            } else {
                                if (trim($row->aadhar_no) != '' && strlen($row->aadhar_no) == 12) {
                                    $sel_aadhar_exits = 1;
                                } else {
                                    $sel_aadhar_exits = 0;
                                }
                            }
                            if ($sel_aadhar_exits == 1) {
                                $sel_aadhar_exits_text = 'YES';
                            } else {
                                $sel_aadhar_exits_text = 'NO';
                            }
                         ?>
                        <div class="col-md-6">
                            <div><strong>Applicant have the Aadhaar Number:</strong>
                                <?php echo e($sel_aadhar_exits_text); ?></div>
                        </div>
                        <?php if($sel_aadhar_exits == 1): ?>
                            <div class="col-md-6">
                                <div><strong>Aadhaar No., if available:</strong> <?php echo e($row->aadhar_no); ?>

                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if($sel_aadhar_exits == 0): ?>
                            <div class="col-md-6">
                                <div><strong>Reason for Which Aadhaar Cannot be Generated:</strong>
                                    <?php echo e($row->withoutaadhar_cause); ?></div>
                            </div>
                        <?php endif; ?>
            <?php endif; ?>

            <div class="col-md-6">
                <div><strong>Aadhaar No., if available:</strong> <?php echo e($row->aadhar_no); ?>

                </div>
            </div>

            <div class="col-md-6">
                <div><strong>EPIC/Voter Id.No.: </strong> <?php echo e($row->epic_voter_id); ?></div>

            </div>

            <div class="col-md-6">
                <div><strong>PAN, if available:</strong> <?php echo e($row->pan_no); ?></div>

            </div>

            <div class="col-md-6">
                <div><strong>BPL Seq No., if avaiable:</strong> <?php echo e($row->bpl_seq_no); ?>

                </div>

            </div>

            <div class="col-md-6">
                <div><strong>BPL Id No., if avaiable:</strong> <?php echo e($row->bpl_id_no); ?>

                </div>

            </div>

            <div class="col-md-6">
                <div><strong>BPL Total Score, if avaiable:</strong>
                    <?php echo e($row->bpl_total_score); ?></div>

            </div>
        </div>

        <?php echo $__env->yieldContent('personal_id-add'); ?>

    </div>
</div>