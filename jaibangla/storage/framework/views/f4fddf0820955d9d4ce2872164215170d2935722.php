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
        <section class="content-header">
            <h1>
                Cross Scheme Duplicate Bank Account Beneficiary List
            </h1>
            <ol class="breadcrumb">
                <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span
                        class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
            </ol>
        </section>
        <section class="content">
            <div class="box box-default">
                <div class="box-body">
                    <div id="loadingDiv"></div>
                    <div class="panel panel-default">
                        <div class="panel-heading" style="font-size: 15px; font-weight: bold; font-style: italic;"><span
                                id="panel-icon">Enter Filter Criteria</div>
                        <div class="panel-body" style="padding: 5px;">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php if($message = Session::get('success')): ?>
                                        <div class="alert alert-success alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong><?php echo e($message); ?> </strong>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($message = Session::get('message')): ?>
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong><?php echo e($message); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($message = Session::get('msg1')): ?>
                                        <div class="alert alert-danger alert-block">
                                            <button type="button" class="close" data-dismiss="alert">×</button>
                                            <strong><?php echo e($message); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class=" control-label">Scheme <span class="text-danger">*</span></label>
                                            <select class="form-control" name="scheme" id='scheme' required>
                                                <option value="">-- Select Scheme --</option>
                                                <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($scheme->id); ?>"><?php echo e($scheme->scheme_name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <option value="20">Lakshmir Bhandar</option>
                                            </select>
                                            </select>
                                            <span class="text-danger" id="error_scheme"></span>
                                        </div>

                                        <div class="col-md-4">
                                            <label class=" control-label">Cross Scheme <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control select2" multiple name="cross_scheme"
                                                id='cross_scheme' required aria-placeholder="Select Cross Scheme">
                                                <option value="">-- Select Cross Scheme --</option>
                                            </select>
                                            <span class="text-danger" id="error_scheme"></span>
                                        </div>
                                        <div class="col-md-4">
                                            <label class=" control-label">Filter <span class="text-danger">*</span></label>
                                            <select class="form-control" name="aadhar_filter" id='aadhar_filter' required>
                                                <option value="2">Bank and Aadhar both same</option>
                                                <option value="1">Bank Same but Aadhar not same</option>
                                            </select>
                                            <span class="text-danger" id="error_aadhar_filter"></span>
                                        </div>
                                        <input type="hidden" name="dist_code" id="dist_code" value="<?php echo e($dist_code); ?>"
                                            class="js-district_1">
                                        <div class="form-group col-md-3">
                                            <label class="control-label">Rural/Urban</label>
                                            <select name="filter_1" id="filter_1" class="form-control">
                                                <option value="">-----Select----</option>
                                                <?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="control-label" id="blk_sub_txt">Block/Sub Division</label>
                                            <select name="filter_2" id="filter_2" class="form-control">
                                                <option value="">-----Select----</option>
                                            </select>
                                        </div>

                                    </div>



                                    <div class="row">
                                        <center>
                                            <div>
                                                <button class="btn btn-primary" name="submit_btn" id="submit_btn"
                                                    type="button" disabled><i class="fa fa-search"></i>
                                                    Search</button>&nbsp;
                                                <button class="btn btn-info" name="excel_btn" id="excel_btn"
                                                    type="button"><i class="fa fa-file-excel-o"></i> Export To
                                                    Excel</button>
                                            </div>
                                        </center>
                                    </div>


                                    <div id="search_details" style="display: none;">
                                        <div class="panel panel-default">
                                            <div class="panel-heading" id="heading_msg"
                                                style="font-size: 15px; font-weight: bold; font-style: italic;">List of
                                                Beneficiary</div>
                                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                                                <div class="table-responsive">
                                                    <table id="example" class="table table-striped table-bordered"
                                                        cellspacing="0" width="100%" style="font-size: 14px;">
                                                        <thead>
                                                            <th>District</th>
                                                            <th>Scheme</th>
                                                            <th>Application ID</th>
                                                            <th>Beneficiary ID</th>
                                                            <th>Name</th>
                                                            <th>Block/Municipality</th>
                                                            <th>GP/Ward</th>
                                                            <th>Mobile No.</th>
                                                            <th>Account No.</th>
                                                            <th>Aadhaar</th>
                                                            <th>Payment Status</th>
                                                            <th>Action</th>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
<?php $__env->stopSection(); ?>
<script src="<?php echo e(asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js')); ?>"></script>
<script src="<?php echo e(asset("js/select2.full.min.js")); ?>"></script>
<script src="<?php echo e(URL::asset('js/master-data-v2.js')); ?>"></script>
<script>
    $(document).ready(function () {
        setInterval(function () {
            $('.date-part').text(new Date().toLocaleDateString());
            $('.time-part').text(new Date().toLocaleTimeString());
        }, 1000);
        var error_cross_scheme = '';
        var error_scheme = '';
        $('#loadingDiv').hide();

        $('#cross_scheme').select2({
            placeholder: "-- Select Cross Scheme --",
            allowClear: true
        });

        $('#scheme').change(function () {
            var scheme = $(this).val();
            if (scheme !== '') {
                $('#submit_btn').prop('disabled', false);
                $.ajax({
                    url: "<?php echo e(route('crossSchemeAjax')); ?>",
                    method: 'POST',
                    data: {
                        scheme_id: scheme,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function (response) {
                        if (response.status === 1 && Array.isArray(response.cross_scheme) && response.cross_scheme.length > 0) {
                            let options = ''; // No need for a placeholder in multiple select
                            response.cross_scheme.forEach(function (item) {
                                options += `<option value="${item.id}">${item.scheme_name}</option>`;
                            });

                            // Update Select2 dropdown
                            $('#cross_scheme').html(options).trigger('change');
                        } else {
                            // Clear select box and show a message
                            $('#cross_scheme').html('').trigger('change');
                            $('#cross_scheme').append('<option disabled>No cross schemes available</option>').trigger('change');
                        }

                    },
                    error: function () {
                        $.confirm({
                            title: 'Error',
                            type: 'red',
                            icon: 'fa fa-warning',
                            content: 'Something went wrong while fetching cross schemes!',
                            buttons: {
                                Ok: function () {
                                    location.reload();
                                }
                            }
                        });
                    }
                });
            } else {
                $('#submit_btn').prop('disabled', true);
                $('#cross_scheme').html('<option value="">-- Select Cross Scheme --</option>').trigger('change');
            }
        });



        $('#submit_btn').click(function () {

            // Validate Cross Scheme
            if ($('#cross_scheme').val() === null || $('#cross_scheme').val().length === 0) {
                error_cross_scheme = 'Cross Scheme is required';
                $('#error_cross_scheme').text(error_cross_scheme);
            } else {
                $('#error_cross_scheme').text('');
            }

            // Validate Scheme
            if ($('#scheme').val() === null || $.trim($('#scheme').val()) === '') {
                error_scheme = 'Scheme is required';
                $('#error_scheme').text(error_scheme);
            } else {
                $('#error_scheme').text('');
            }


            // If there are errors, prevent form submission
            if (error_cross_scheme !== '' || error_scheme !== '') {
                return false;
            } else {
                $('#loadingDiv').show();
                $('#search_details').show();
                // $(':input[type="button"]').prop('disabled', false);

                var scheme_code = $('#cross_scheme').val();
                var gp_ward = $('#gp_ward').val();
                var muncid = $('#muncid').val();
                var aadhar_filter = $('#aadhar_filter').val();
                if ($.fn.DataTable.isDataTable('#example')) {
                    $('#example').DataTable().destroy();
                }
                var table = $('#example').DataTable({
                    dom: 'lfrtip',
                    "scrollX": true,
                    "paging": true,
                    "searchable": true,
                    "ordering": false,
                    "bFilter": true,
                    "bInfo": true,
                    "pageLength": 25,
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
                        url: "<?php echo e(route('crossSchemeListAjax')); ?>",
                        type: "post",
                        data: function (d) {
                            d.gp_ward = gp_ward,
                                d.cross_scheme = $('#cross_scheme').val(),
                                d.scheme = $('#scheme').val(),
                                d.rural_urban_id = $('#filter_1').val(),
                                d.block_ulb_code = $('#filter2').val(),
                                d.aadhar_filter = $('#aadhar_filter').val(),
                                d._token = "<?php echo e(csrf_token()); ?>"
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#submit_btn').attr('disabled', false);
                            $('#loadingDiv').hide();
                            $('.preloader1').hide();
                            ajax_error(jqXHR, textStatus, errorThrown);
                        }
                    },
                    "initComplete": function () {
                        $('#loadingDiv').hide();
                        //console.log('Data rendered successfully');
                    },
                    "columns": [
                        {
                            "data": "district_name"
                        },
                        {
                            "data": "scheme_name"
                        },
                        {
                            "data": "application_id"
                        },
                        {
                            "data": "beneficiary_id"
                        },
                        {
                            "data": "ben_name"
                        },
                        {
                            "data": "block_ulb_name"
                        },
                        {
                            "data": "gp_ward_name"
                        },
                        {
                            "data": "mobile_no"
                        },
                        {
                            "data": "bank_code"
                        },
                        {
                            "data": "aadhar_no"
                        },
                        {
                            "data": "payment_status"
                        },
                        {
                            "data": "action"
                        }
                    ]

                });
            }
        });


        // ------------ Master DropDown Section Start-------------------- //
        $('#filter_1').change(function () {
            var filter_1 = $(this).val();
            // alert(filter_1);
            $('#filter_2').html('<option value="">--All --</option>');
            $('#block_ulb_code').html('<option value="">--All --</option>');
            select_district_code = $('#dist_code').val();
            // alert(select_district_code);

            var htmlOption = '<option value="">--All--</option>';
            $('#gp_ward_code').html('<option value="">--All --</option>');
            if (filter_1 == 1) {
                $.each(subDistricts, function (key, value) {
                    if ((value.district_code == select_district_code)) {
                        htmlOption += '<option value="' + value.id + '">' + value.text +
                            '</option>';
                    }
                });
                $("#blk_sub_txt").text('Subdivision');
                $("#gp_ward_txt").text('Ward');
                $("#municipality_div").show();
                $("#gp_ward_div").show();
            } else if (filter_1 == 2) {
                // console.log(filter_1);
                $.each(blocks, function (key, value) {
                    if ((value.district_code == select_district_code)) {
                        htmlOption += '<option value="' + value.id + '">' + value.text +
                            '</option>';
                    }
                });
                $("#blk_sub_txt").text('Block');
                $("#gp_ward_txt").text('GP');
                $("#municipality_div").hide();
                $("#gp_ward_div").show();
            } else {
                $("#blk_sub_txt").text('Block/Subdivision');
                $("#gp_ward_txt").text('GP/Ward');
                $("#municipality_div").hide();
            }
            $('#filter_2').html(htmlOption);

        });
        $('#filter_2').change(function () {
            var rural_urbanid = $('#filter_1').val();
            $('#gp_ward_code').html('<option value="">--All --</option>');
            if (rural_urbanid == 1) {
                var sub_district_code = $(this).val();
                if (sub_district_code != '') {
                    $('#block_ulb_code').html('<option value="">--All --</option>');
                    select_district_code = $('#dist_code').val();
                    var htmlOption = '<option value="">--All--</option>';
                    $.each(ulbs, function (key, value) {
                        if ((value.district_code == select_district_code) && (value
                            .sub_district_code == sub_district_code)) {
                            htmlOption += '<option value="' + value.id + '">' + value.text +
                                '</option>';
                        }
                    });
                    $('#block_ulb_code').html(htmlOption);
                } else {
                    $('#block_ulb_code').html('<option value="">--All --</option>');
                }
            } else if (rural_urbanid == 2) {
                $('#muncid').html('<option value="">--All --</option>');
                $("#municipality_div").hide();
                var block_code = $(this).val();
                select_district_code = $('#dist_code').val();
                var htmlOption = '<option value="">--All--</option>';
                $.each(gps, function (key, value) {
                    if ((value.district_code == select_district_code) && (value.block_code ==
                        block_code)) {
                        htmlOption += '<option value="' + value.id + '">' + value.text +
                            '</option>';
                    }
                });
                $('#gp_ward_code').html(htmlOption);
                $("#gp_ward_div").show();
            } else {
                $('#block_ulb_code').html('<option value="">--All --</option>');
            }
        });
        $('#block_ulb_code').change(function () {
            var muncid = $(this).val();
            var district = $("#dist_code").val();
            var urban_code = $("#filter_1").val();
            if (district == '') {
                $('#filter_1').val('');
                $('#filter_2').html('<option value="">--All --</option>');
                $('#block_ulb_code').html('<option value="">--All --</option>');
            }
            if (urban_code == '') {
                // alert('Please Select Rural/Urban First');
                $('#filter_2').html('<option value="">--All --</option>');
                $('#block_ulb_code').html('<option value="">--All --</option>');
                $("#filter_1").focus();
            }
            if (muncid != '') {
                var rural_urbanid = $('#filter_1').val();
                if (rural_urbanid == 1) {
                    $('#gp_ward_code').html('<option value="">--All --</option>');
                    var htmlOption = '<option value="">--All--</option>';
                    $.each(ulb_wards, function (key, value) {
                        if (value.urban_body_code == muncid) {
                            htmlOption += '<option value="' + value.id + '">' + value.text +
                                '</option>';
                        }
                    });
                    $('#gp_ward_code').html(htmlOption);
                    //console.log(htmlOption);
                } else {
                    $('#gp_ward_code').html('<option value="">--All --</option>');
                    $("#gp_ward_div").hide();
                }
            } else {
                $('#gp_ward_code').html('<option value="">--All --</option>');
            }
        });


    });
</script>
<?php echo $__env->make('layouts.app-template-datatable_new', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>