<div class="tab-pane fade" id="experience_details">
    <div class="panel panel-default">
        <div class="panel-heading">
            </h4></b>Enclosure List (Self Attested)</b></h4>
        </div>
        <div class="panel-body">
            <!-- Document Dynamic-->
            <?php $__currentLoopData = $doc_list_man; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc_man): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="form-group col-md-12">
                <label <?php if(in_array($doc_man->id, $required_doc)): ?> class="required-field" <?php endif; ?>><?php echo e($doc_man->doc_name); ?></label>

                    <input type="file" name="doc_<?php echo e($doc_man->id); ?>" id="doc_<?php echo e($doc_man->id); ?>" class="form-control"
                        tabindex="1" />
                    <div class="imageSize">(Image type must be <?php echo e($doc_man->doc_type); ?> and image size max
                        <?php echo e($doc_man->doc_size_kb); ?>KB)
                    </div>
                    <span id="error_doc_<?php echo e($doc_man->id); ?>" class="text-danger"></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php $__currentLoopData = $doc_list_opt; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc_opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="form-group col-md-12">
                    <label class=""><?php echo e($doc_opt->doc_name); ?></label>
                    <input type="file" name="doc_<?php echo e($doc_opt->id); ?>" id="doc_<?php echo e($doc_opt->id); ?>" class="form-control"
                        tabindex="1" />
                    <div class="imageSize">(Image type must be <?php echo e($doc_opt->doc_type); ?> and image size max
                        <?php echo e($doc_opt->doc_size_kb); ?>KB)
                    </div>
                    <span id="error_doc_<?php echo e($doc_opt->id); ?>" class="text-danger"></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($type == $op_type): ?>
                <br>
                <br>
                <h3>Alerady Uploded</h3>
                <?php $__currentLoopData = $docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($doc->attched_document != ""): ?>
                        <div class="col-md-4">
                            <strong><?php echo e($doc->doc_type_name); ?> :</strong>
                        </div>
                        <div class="col-md-8" style="padding-bottom: 30px; ">
                            <?php 
                                                                                $document_mime_type = $doc->document_mime_type;
                        if ($document_mime_type == 'image/jpeg') {
                            $image_extension = 'jpg';
                        } else if ($document_mime_type == 'image/png') {
                            $image_extension = 'png';
                        } else if ($document_mime_type == 'application/pdf') {
                            $image_extension = 'pdf';
                        }
                        $row_image = "data:image/" . $image_extension . ";base64," . $doc->attched_document;
                                                                               ?>
                            <?php if(strtolower($image_extension) == 'jpg' || strtolower($image_extension) == 'png'): ?>
                                <div class="col-md-12" style="border:1px solid #dcdfdf">
                                    <a class="example-image-link" href="<?php echo e($row_image); ?>" data-lightbox="example-1">
                                        <img class="example-image" src="<?php echo e($row_image); ?>" alt="image-1" width="200" height="180" /></a>
                                </div>

                            <?php elseif(strtolower($image_extension) == 'pdf'): ?>
                                <div class="col-md-12" style="border:1px solid #dcdfdf">
                                    <a id="link"
                                        href="<?php echo e(route('jbDownload', ['scheme_id' => $doc->scheme_id, 'created_by_dist_code' => $doc->created_by_dist_code, 'beneficiary_id' => $doc->beneficiary_id, 'document_type' => $doc->document_type])); ?>"
                                        target="_blank" style="color: #4324ef" width="">Download PDF Document</a>
                                </div>
                            <?php else: ?>
                                <div class="col-md-12" style="border:1px solid #dcdfdf">
                                    <p>No File Found</p>
                                </div>
                            <?php endif; ?>


                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

            <!-- Document Dynamic End-->
            <div align="center" class="col-md-12">
                <button type="button" name="previous_btn_experience_details" id="previous_btn_experience_details"
                    class="btn btn-info btn-lg">Previous</button>

                <button type="button" name="btn_experience_details" id="btn_experience_details"
                    class="btn btn-success btn-lg">Next</button>
            </div>
            <br />
        </div>
    </div>
</div>

<script src="<?php echo e(asset('js/FormEntry/enclosure.js')); ?>"></script>