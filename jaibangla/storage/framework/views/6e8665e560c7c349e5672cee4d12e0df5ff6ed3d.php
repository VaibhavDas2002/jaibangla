<?php $__env->startSection('content'); ?>
<div class="content-wrapper d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <!-- Content Header -->
    <section class="content-header text-center">
        <h1>Scheme-Based Required Fields Settings</h1>
        <ol class="breadcrumb text-center">
            <li><i class="fa fa-clock-o"></i> Date:
                <span style="font-size: 12px; font-weight: bold;">
                    <span class="date-part"></span>&nbsp;&nbsp;
                    <span class="time-part"></span>
                </span>
            </li>
        </ol>
    </section>

    <!-- Main Content -->
    <section class="content">
        <?php if(Session::has('success')): ?>
            <div class="alert alert-success alert-dismissible show" role="alert">
                <strong><?php echo e(Session::get('success')); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if(Session::has('error')): ?>
            <div class="alert alert-danger alert-dismissible show" role="alert">
                <strong><?php echo e(Session::get('error')); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div id="loadingDiv" class="text-center">
            <p>Loading, please wait...</p>
        </div>

        <!-- Form Section -->
        <form method="POST" action="<?php echo e(route('scheme-req-field.store')); ?>" id="schemeReqFieldForm" role="form">
            <?php echo e(csrf_field()); ?>

            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title text-center">Settings Form</h3>
                        </div>
                        <div class="box-body">
                            <!-- Scheme Dropdown -->
                            <div class="form-group">
                                <label for="scheme" class="form-label required-field">Scheme</label>
                                <select class="form-control select2" name="scheme_id" id="scheme">
                                    <option value="">Select a Scheme</option>
                                    <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($scheme->id); ?>"><?php echo e($scheme->scheme_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <!-- Normal Fields -->
                            <div class="form-group">
                                <label for="normalFields" class="form-label">Normal Fields</label>
                                <select class="form-control select2" name="normal_fields[]" id="normalFields" multiple>
                                    <?php $__currentLoopData = $normal_fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $normal_field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($normal_field->id); ?>"><?php echo e($normal_field->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <!-- Document Fields -->
                            <div class="form-group">
                                <label for="docFields" class="form-label">Document Fields</label>
                                <select class="form-control select2" name="doc_fields[]" id="docFields" multiple>
                                    <?php $__currentLoopData = $doc_fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc_field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($doc_field->id); ?>"><?php echo e($doc_field->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="box-footer text-center">
                            <button type="submit" class="btn btn-info">Submit</button>
                            <button type="reset" class="btn btn-default">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- DataTable Section -->
        <div class="datatable">
            <table id="scheme-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Sl. No.</th>
                        <th>Scheme Name</th>
                        <th>Required Fields</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<!-- Scripts -->
<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2();

        // Update Date and Time
        setInterval(function () {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);

        // Initialize DataTable
        $('#scheme-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "<?php echo e(route('get-scheme-data-required')); ?>",
            columns: [
                { data: "sl_no" },
                { data: "scheme_name" },
                { data: "required_fields" },
                { data: "action", orderable: false, searchable: false }
            ],
            order: [[0, 'asc']],
        });

        // Hide Loading Div
        $('#loadingDiv').hide();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app-template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>