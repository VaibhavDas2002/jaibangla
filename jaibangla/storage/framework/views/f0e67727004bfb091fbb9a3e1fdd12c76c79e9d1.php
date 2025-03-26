<style type="text/css">
    .has-error {
        border-color: #cc0000;
        background-color: #ffff99;
    }

    .preloader1 {
        position: fixed;
        top: 40%;
        left: 52%;
        z-index: 999;
    }

    .preloader1 {
        background: transparent !important;
    }

    #loadingDi {
        position: absolute;
        top: 0px;
        right: 0px;
        width: 100%;
        height: 100%;
        background-color: #fff;
        background-image: url('images/ajaxgif.gif');
        background-repeat: no-repeat;
        background-position: center;
        z-index: 10000000;
        opacity: 0.4;
        filter: alpha(opacity=40);
        /* For IE8 and earlier */
    }

    .loadingDivModal {
        position: absolute;
        top: 0px;
        right: 0px;
        width: 100%;
        height: 100%;
        background-color: #fff;
        background-image: url('images/ajaxgif.gif');
        background-repeat: no-repeat;
        background-position: center;
        z-index: 10000000;
        opacity: 0.4;
        filter: alpha(opacity=40);
        /* For IE8 and earlier */
    }

    #updateDiv {
        border: 1px solid #d9d9d9;
        padding: 8px;
        box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
    }
</style>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <!-- <div class="preloader1"><img src="<?php echo e(asset('images/ZKZg.gif')); ?>" width="150px" id="loader_img"></div> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Beneficiaries With 90-100% Score( Name Match)
        </h1>
        <ol class="breadcrumb">
            <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span
                    class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
        </ol>
    </section>
    <section class="content">
        <div class="box box-default">
            <div class="box-body">
                <div id="loadingDi"></div>
                <div class="panel panel-default">
                    <div class="panel-heading" style="font-size: 14px; font-weight: bold; font-style: italic;"><span
                            id="panel-icon">Enter Filter Criteria</div>
                    <div class="panel-body" style="padding: 5px;">
                        <div class="row">
                            <div class="col-md-12">
                                <?php if(($message = Session::get('success'))): ?>
                                    <div class="alert alert-success alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong><?php echo e($message); ?> </strong>
                                    </div>
                                <?php endif; ?>
                                <?php if(($message = Session::get('message'))): ?>
                                    <div class="alert alert-danger alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong><?php echo e($message); ?></strong>
                                    </div>
                                <?php endif; ?>
                                <?php if(($message = Session::get('error'))): ?>
                                    <div class="alert alert-danger alert-block">
                                        <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong><?php echo e($message); ?></strong>
                                    </div>
                                <?php endif; ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <label class=" control-label">Scheme <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="scheme_type" id='scheme_type' required>
                                                <option value="">--Select Scheme--</option>
                                                <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($scheme->id); ?>"><?php echo e($scheme->scheme_name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <span class="text-danger" id="error_scheme_type"></span>
                                        </div>

                                        <input type="hidden" name="dist_code" value="<?php echo e($dist_code); ?>"
                                            class="client-js-district" hidden>
                                        <input type="hidden" name="is_urban" value="<?php echo e($is_urban); ?>"
                                            class="client-js-urban">


                                        <?php if($is_urban == 1): ?>
                                            <div class="form-group col-md-3">
                                                <label class=" control-label">Select Filter Criteria :Municipality</label>
                                                <select name="munc" id="munc"
                                                    class="form-control select2 full-width js-municipality client-js-localbody">
                                                    <option value="">-----Select----</option>
                                                    <?php $__currentLoopData = $urban_bodys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $urban_body): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($urban_body->urban_body_code); ?>">
                                                            <?php echo e($urban_body->urban_body_name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label class=" control-label">Select Filter Criteria :Wards</label>
                                                <select name="gp_ward" id="gp_ward" class="form-control  client-js-gpward">
                                                    <option value="">--Select --</option>
                                                </select>
                                            </div>


                                        <?php endif; ?>
                                        <?php if($is_urban == 2): ?>
                                            <input type="hidden" name="munc" id="munc" value="" />
                                            <div class="form-group col-md-3">
                                                <label class=" control-label">Select Filter Criteria : Gram
                                                    Panchayat</label>
                                                <select name="gp_ward" id="gp_ward" class="form-control select2 ">
                                                    <option value="">-----Select----</option>
                                                    <?php $__currentLoopData = $gps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($gp->gram_panchyat_code); ?>"> <?php echo e($gp->gram_panchyat_name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        <?php else: ?>
                                        <?php endif; ?>
                                    </div>




                                </div>
                                <div style="text-align: center;">
                                    <button class="btn btn-primary" name="submit_btn" id="submit_btn" type="button"
                                        disabled><i class="fa fa-search"></i> Search</button>&nbsp;
                                    

                                </div>


                            </div>
                        </div>
                    </div>
                </div>

                <div id="res_div" style="display: none;">
                    <div class="panel panel-default">
                        <div class="panel-heading" id="panel_head"
                            style="font-size: 14px; font-weight: bold; font-style: italic;">
                            List of Beneficiary</div>
                        <div class="panel-body" style="padding: 5px; font-size: 14px;">
                            <div class="table-responsive">
                                <table id="example" class="table display" cellspacing="0" width="100%">
                                    <thead style="font-size: 12px;">
                                        <th>Beneficiary ID</th>
                                        <th>Beneficiary Name</th>
                                        <th>Block/Municipality</th>
                                        <th>GP/Ward</th>
                                        <th>Bank A/C</th>
                                        <th>Bank IFSC</th>
                                        <th>Matching Score</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody style="font-size: 14px;"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


</div>
<?php $__env->stopSection(); ?>
<script src="<?php echo e(asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
    <script src="<?php echo e(asset("js/select2.full.min.js")); ?>"></script>
    <script src="<?php echo e(URL::asset('js/master-data-v2.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('js/site-client-v2.js')); ?>"></script>
    <script>
        $('.select2').select2();
    </script>
    <script src="<?php echo e(asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js")); ?>"
        type="text/javascript"></script>
    <script src="<?php echo e(asset("/bower_components/AdminLTE/dist/js/app.min.js")); ?>" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <!-- <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script> -->
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
<script>
    $(document).ready(function () {
        // Live Clock
        var interval = setInterval(function () {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);

        $('#loadingDi').hide();
        $('#failed_type_div').hide();
        $('#submit_btn').removeAttr('disabled');
        $('#reset_btn').removeAttr('disabled');
        $('#excel_btn').prop('disabled', false);




        var error_scheme_type = '';
        var error_filter_type = '';
        var error_failed_type = '';




        $('#submit_btn').click(function () {
            if ($.trim($('#scheme_type').val()).length == 0) {
                error_scheme_type = 'Scheme name is required';
                $('#error_scheme_type').text(error_scheme_type);
            } else {
                error_scheme_type = '';
                $('#error_scheme_type').text(error_scheme_type);
            }

            if (error_scheme_type != '') {
                return false;
            } else {
                loadDatatable();
            }
        });


    });



    function loadDatatable() {
        $('#loadingDi').show();
        $('#res_div').show();
        var msg = 'List of Beneficiaries of Scheme : ' + $("#scheme_type option:selected").text();
        $('#panel_head').text(msg);
        if ($.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable().destroy();
        }
        var table = $('#example').DataTable({
            dom: 'Blfrtip',
            "scrollX": true,
            "paging": true,
            "searchable": true,
            "ordering": false,
            "bFilter": true,
            "bInfo": true,
            "pageLength": 20,
            'lengthMenu': [
                [10, 20, 25, 50, 100, -1],
                [10, 20, 25, 50, 100, 'All']
            ],
            "serverSide": true,
            "processing": true,
            "bRetrieve": true,
            "oLanguage": {
                "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
            },
            "ajax": {
                url: "<?php echo e(route('getBenNameList')); ?>",
                type: "POST",
                data: function (d) {
                    d.scheme_id = $('#scheme_type').val(),
                        d.munc = $('#munc').val(),
                        d.gp_ward = $('#gp_ward').val(),
                        d._token = "<?php echo e(csrf_token()); ?>"
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#loadingDi').hide();
                    $('.preloader1').hide();
                    // console.log(jqXHR, textStatus, errorThrown);
                    ajax_error(jqXHR, textStatus, errorThrown);
                    $.alert({
                        title: 'Error!!',
                        type: 'red',
                        icon: 'fa fa-warning',
                        content: 'Loading Error! Session timeout, please logout and login again.'
                    });
                }
            },
            "initComplete": function () {
                $('#loadingDi').hide();
                //console.log('Data rendered successfully');
            },
            "columns": [
                {
                    "data": "id"
                },
                {
                    "data": "name"
                },
                {
                    "data": "block_ulb_name"
                },
                {
                    "data": "gp_ward_name"
                },
                {
                    "data": "bank_code"
                },
                {
                    "data": "bank_ifsc"
                },
                {
                    "data": "status"
                },
                {
                    "data": "view"
                },
            ],

            "buttons": [{
                extend: 'pdf',
                footer: true,
                pageSize: 'A4',
                //orientation: 'landscape',
                pageMargins: [40, 60, 40, 60],
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5],

                }
            },
            {
                extend: 'excel',
                footer: true,
                pageSize: 'A4',
                //orientation: 'landscape',
                pageMargins: [40, 60, 40, 60],
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5],
                    stripHtml: false,
                }
            },
                //'pdf','excel','print'
            ],
        });
    }






    function ajax_error(jqXHR, textStatus, errorThrown) {
        var msg = "<strong>Failed to Load data.</strong><br/>";
        if (jqXHR.status !== 422 && jqXHR.status !== 400) {
            msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
        } else {
            if (jqXHR.responseJSON.hasOwnProperty('exception')) {
                msg += "Exception: <strong>" + jqXHR.responseJSON.exception_message + "</strong>";
            } else {
                msg += "Error(s):<strong><ul>";
                $.each(jqXHR.responseJSON, function (key, value) {
                    msg += "<li>" + value + "</li>";
                });
                msg += "</ul></strong>";
            }
        }
        $.alert({
            title: 'Error!!',
            type: 'red',
            icon: 'fa fa-warning',
            content: msg,
        });
    }
</script>
<?php echo $__env->make('layouts.app-template-datatable_new', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>