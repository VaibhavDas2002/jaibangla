<style type="text/css">
    .required-field::after {
        content: "*";
        color: red;
    }

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

    .panel-heading {
        padding: 0;
        border: 0;
    }

    .panel-title>a,
    .panel-title>a:active {
        display: block;
        padding: 5px;
        color: #555;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        word-spacing: 3px;
        text-decoration: none;
    }

    .panel-heading a:before {
        font-family: 'Glyphicons Halflings';
        content: "\e114";
        float: right;
        transition: all 0.5s;
    }

    .panel-heading.active a:before {
        -webkit-transform: rotate(180deg);
        -moz-transform: rotate(180deg);
        transform: rotate(180deg);
    }

    #enCloserTable tbody tr td {
        padding: 10px 10px 10px 10px;
    }

    .modal-open {
        overflow: visible !important;
    }

    .required:after {
        color: red;
        content: '*';
        font-weight: bold;
        margin-left: 5px;
        float: right;
        margin-top: 5px;
    }

    #loadingDivModal {
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

    .disabledcontent {
        pointer-events: none;
        opacity: 0.4;
    }
</style>


<?php $__env->startSection('content'); ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Bank Info Change Log Master Data Entry
        </h1>

    </section>
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div>
                    <!-- class="box box-primary" -->
                    

                    <div>
                        <?php if(($message = Session::get('success'))): ?>
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e($message); ?></strong>


                            </div>
                        <?php endif; ?>

                        <?php if(count($errors) > 0): ?>
                            <div class="alert alert-danger alert-block">
                                <ul>
                                    <?php $__currentLoopData = $errors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><strong> <?php echo e($error); ?></strong></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>





                    <div class="tab-content" style="margin-top:16px;">



                        <form method="post" id="banklog" name="banklog"
                            action="<?php echo e(url('bank-info-change-log-ps-master-entry-post')); ?>" enctype="multipart/form-data"
                            class="" autocomplete="off">

                            <?php echo e(csrf_field()); ?>




                            <div class="tab-pane active" id="personal_details">
                                <div class="panel panel-default">

                                    <div class="panel-body">
                                        <div class="row">


                                            <div class="form-group col-md-4">
                                                <label class=" control-label required-field">Enter Police Case
                                                    Number</label>
                                            </div>
                                            <div class="form-group col-md-8">
                                                <input class="form-control" type="text" name="ps_case_no"
                                                    id="ps_case_no">
                                                <span id="error_ps_case_no" class="text-danger"></span>
                                            </div>
                                        </div>


                                        <div class="col-md-12" align="center">

                                            <button type="submit" id="submitting" value="Submit"
                                                class="btn btn-success success btn-lg modal-search form-submitted">Insert
                                            </button>



                                            <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                                        </div>
                                        <br />
                                    </div>
                                </div>
                            </div>
                        </form>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Police Case Number</th>
                                    <th scope="col">No. of Applicant Tagged</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($data) > 0): ?>
                                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($item['case_no']); ?></td>
                                            <td><?php echo e($item['no_data']); ?></td>
                                            <td><button value="<?php echo e($item['id']); ?>" class="btn btn-info tagNew">Tag
                                                    Applicant</button></td>
                                            <td><button value="<?php echo e($item['id']); ?>" class="btn btn-success tagList">List of Tagged
                                                    Applicant</button></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4">No Record Found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>





            </div>






        </div>
        <!-- /.box -->
</div>
<!--/.col (left) -->

</div>
</div>
</section>
</div>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script>
    //alert(base_date);

    $(document).ready(function () {
        $('.sidebar-menu li').removeClass('active');
        // $('.sidebar-menu #lk-main').addClass("active");
        $('.sidebar-menu #bankInfotrackMaster').addClass("active");
        $(".NumOnly").keyup(function (event) {
            $(this).val($(this).val().replace(/[^\d].+/, ""));
            if ((event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });
    });

    $(document).on('click', '#submitting', function (e) {
        e.preventDefault()
        var error_ps_case_no = '';
        var ps_case_no = $('#ps_case_no').val();
        if ($.trim($('#ps_case_no').val()).length == 0) {
            error_ps_case_no = 'Police Case No. is required';
            $('#error_ps_case_no').text(error_ps_case_no);
            $('#ps_case_no').addClass('has-error');
        }
        else {

            error_ps_case_no = '';
            $('#error_ps_case_no').text(error_ps_case_no);
            $('#ps_case_no').removeClass('has-error');

        }
        //error_ps_case_no='';
        if (error_ps_case_no != '') {
            return false;
        } else {
            // alert('OK');
            // $('#submit_loader1').show();
            $("#banklog").submit();


        }
    });
    $(document).on('click', '.tagNew', function (e) {
        //alert(this.value);
        window.location.href = 'bank-info-change-log?case_id=' + this.value;
    });

    $(document).on('click', '.tagList', function (e) {
        window.location.href = 'bank-info-change-log-download-list?case_id=' + this.value;
    });

    function printMsg(msg, msgtype, divid) {
        $("#" + divid).find("ul").html('');
        $("#" + divid).css('display', 'block');
        if (msgtype == '0') {
            //alert('error');
            $("#" + divid).removeClass('alert-success');
            //$('.print-error-msg').removeClass('alert-warning');
            $("#" + divid).addClass('alert-warning');
        } else {
            $("#" + divid).removeClass('alert-warning');
            $("#" + divid).addClass('alert-success');
        }
        if (Array.isArray(msg)) {
            $.each(msg, function (key, value) {
                $("#" + divid).find("ul").append('<li>' + value + '</li>');
            });
        } else {
            $("#" + divid).find("ul").append('<li>' + msg + '</li>');
        }
    }

    function closeError(divId) {
        $('#' + divId).hide();
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app-template-datatable_new', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>