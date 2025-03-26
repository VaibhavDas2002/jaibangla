<div class="box box-primary collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title">Enclosure List(Self Attested)</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" fdprocessedid="pcxrxb"><i
                    class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <?php $__currentLoopData = $docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($doc->attched_document != ""): ?>
                            <div class="col-md-4">
                                <strong><?php echo e($doc->doc_type_name); ?> :</strong>
                            </div>
                            <div class="col-md-8" style="padding-bottom: 30px; ">
                                <?php        $document_mime_type = $doc->document_mime_type;
                        if ($document_mime_type == 'image/jpeg') {
                            $image_extension = 'jpg';
                        } else if ($document_mime_type == 'image/png') {
                            $image_extension = 'png';
                        } else if ($document_mime_type == 'application/pdf') {
                            $image_extension = 'pdf';
                        }
                        $row_image = "data:image/" . $image_extension . ";base64," . $doc->attched_document; ?>
                                <?php if(strtolower($image_extension) == 'jpg' || strtolower($image_extension) == 'png'): ?>
                                    <div class="col-md-12" style="border:1px solid #dcdfdf">
                                        <a class="example-image-link" href="<?php echo e($row_image); ?>" data-lightbox="example-1">
                                            <img class="example-image" src="<?php echo e($row_image); ?>" alt="image-1" width="200" height="180" /></a>
                                    </div>
                                <?php elseif(strtolower($image_extension) == 'pdf'): ?>
                                    <div class="col-md-12" style="border:1px solid #dcdfdf">
                                        <a id="link"
                                            href="<?php echo e(route('jbDownload', ['scheme_id' => $doc->scheme_id, 'created_by_dist_code' => $doc->created_by_dist_code, 'beneficiary_id' => $doc->beneficiary_id, 'document_type' => $doc->document_type])); ?>"
                                            target="_blank" style="color: #4324ef" width="">Download PDF
                                            Document</a>
                                    </div>
                                <?php else: ?>
                                    <div class="col-md-12" style="border:1px solid #dcdfdf">
                                        <p>No File Found</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                    <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>