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
        <!-- <div class="preloader1"><img src="<?php echo e(asset('images/ZKZg.gif')); ?>" width="150px" id="loader_img"></div> -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Sarasori Mukhyamantri (CMO Grievance) for Approved Beneficiary List 
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
                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                            
                                            <div class="col-md-4">
                                                <label class=" control-label">Scheme <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control" name="scheme_id" id='scheme_id' required>
                                                    <option value="">--Select Scheme--</option>
                                                    <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($scheme->id); ?>"><?php echo e($scheme->scheme_name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <span class="text-danger" id="error_scheme_id"></span>
                                            </div>
                                            <?php if($district_visible): ?>
                                                <div class="form-group col-md-4">
                                                    <label class="">District <span
                                                        class="text-danger">*</span></label>
                                                    <select name="district" id="district" class="form-control"
                                                        tabindex="6">
                                                        <option value="">--All --</option>
                                                        <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($district->district_code); ?>"
                                                                <?php if(old('district') == $district->district_code): ?> selected <?php endif; ?>>
                                                                <?php echo e($district->district_name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                    <span id="error_district" class="text-danger"></span>
                                                </div>
                                            <?php else: ?>
                                                <input type="hidden" name="district" id="district"
                                                    value="<?php echo e($district_code_fk); ?>" />
                                            <?php endif; ?>

                                            <?php if($is_urban_visible): ?>
                                                <div class="form-group col-md-4" id="divUrbanCode">
                                                    <label class="">Rural/ Urban</label>
                                                    <select name="urban_code" id="urban_code" class="form-control"
                                                        tabindex="11">
                                                        <option value="">--All --</option>
                                                        <?php $__currentLoopData = Config::get('constants.rural_urban'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($key); ?>"
                                                                <?php if(old('urban_code') == $key): ?> selected <?php endif; ?>>
                                                                <?php echo e($val); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                    <span id="error_urban_code" class="text-danger"></span>
                                                </div>
                                            <?php else: ?>
                                                <input type="hidden" name="urban_code" id="urban_code"
                                                    value="<?php echo e($rural_urban_fk); ?>" />
                                            <?php endif; ?>

                                            <?php if($block_visible): ?>
                                                <div class="form-group col-md-4" id="divBodyCode">
                                                    <label class="" id="blk_sub_txt">Block/Sub Division</label>
                                                    <select name="block" id="block" class="form-control"
                                                        tabindex="16">
                                                        <option value="">--All --</option>
                                                    </select>
                                                    <span id="error_block" class="text-danger"></span>
                                                </div>
                                            <?php else: ?>
                                                <input type="hidden" name="block" id="block"
                                                    value="<?php echo e($block_munc_corp_code_fk); ?>" />
                                            <?php endif; ?>

                                           

                                        </div>
                                    </div>
                                    <div class="row">
                                        <center>
                                            <div>
                                                <button class="btn btn-primary" name="submit_btn" id="submit_btn"
                                                    type="button" disabled><i class="fa fa-search"></i>
                                                    Search</button>&nbsp;
                                                <button class="btn btn-info" name="excel_btn" id="excel_btn" type="button"><i class="fa fa-file-excel-o"></i> Export To Excel</button>
                                            </div>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert print-error-msg" style="display:none;" id="errorDiv">
                        <button type="button" class="close" aria-label="Close" onclick="closeError('errorDiv')"><span
                                aria-hidden="true">&times;</span></button>
                        <ul></ul>
                    </div>
                    <form method="post" id="register_form" action="<?php echo e(url('sm-cmo-revert-bulk')); ?>" 
                    class="submit-once">
                    <div class="row">
                    <button type="submit"  style="margin: 0% 0% 2% 0%;" type="button" name="bulk_revert" id="bulk_revert" value="approve" class="btn btn-info col-sm-3 col-xs-5 btn-margin">
                        Bulk Revert
                    </button>
                    </div>
                  
                    <input type="hidden" name="_token" id="token" value="<?php echo e(csrf_token()); ?>">
          <input type="hidden" id="scheme_id" name="scheme_id" value="<?php echo e($scheme_id); ?>">
                    <div id="search_details" style="display: none;">
                        <div class="panel panel-default">
                            <div class="panel-heading" id="heading_msg"
                                style="font-size: 15px; font-weight: bold; font-style: italic;">List of Beneficiary</div>
                            <div class="panel-body" style="padding: 5px; font-size: 14px;">
                      
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped table-bordered" cellspacing="0"
                                        width="100%" style="font-size: 14px;">
                                        <thead>
                                            <th>Beneficiary ID</th>
                                            <th>Name</th>
                                            <th>District</th>
                                            <th>Block/Municipality</th>
                                            <th>GP/Ward</th>
                                            <th>CMO Grievance Mobile No.</th>
                                            <th>Applicant Mobile No.</th>
                                            <th>Approval Date</th>
                                            <th></th>
                                            <th>Check</th>

                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                                           </form>

                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <div class="modal fade" id="ben_revert_modal" tabindex="-1">

     

    <div class="modal-dialog ">
      <div class="modal-content">
        <div class="modal-header btn-danger">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title ">Revert Beneficiary Application</h4>
        </div>
        <div class="modal-body">
          <h4>Are you sure you want to revert the application back to <b>Verifier</b> level with the beneficiary details mentioned below?</h4><hr/>

          <table style="width:100%">
            <tr>
              <td><span class="item_header">Beneficiary Id:</span></td>
              <td><span class="item_value" id="revert_ben_id"></span></td>
            </tr>
            <tr>
              <td><span class="item_header">Beneficiary Name:</span></td>
              <td><span class="item_value" id="revert_ben_name"></span></td>
            </tr>
            <tr>
              <td><span class="item_header">Beneficiary Mobile Number:</span></td>
              <td><span class="item_value" id="revert_app_mobile_no"></span></td>
            </tr>
            <tr>
              <td><span class="item_header">CMO Grievance Mobile Number:</span></td>
              <td><span class="item_value" id="revert_smo_mobile_no"></span></td>
            </tr>
            <tr>
              <td colspan="2"><hr/></td>
            </tr>
          </table>
          <input type="hidden" id="revert_beneficiary_id"/>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success" id="revert_Button" data-dismiss="modal">Revert</button>
          <button type="button" id="submittingapprove" value="Submit" class="btn btn-success btn-lg"
                          disabled>Submitting please wait</button>
        </div>
      </div>
    </div>

  </div>
  <div class="modal fade" id="ben_unmark_modal" tabindex="-1">

     

    <div class="modal-dialog ">
      <div class="modal-content">
        <div class="modal-header btn-danger">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title ">Unmark Beneficiary Application</h4>
        </div>
        <div class="modal-body">
          <h4>Are you sure you want to unmark the application  with the beneficiary details mentioned below?</h4><hr/>

          <table style="width:100%">
            <tr>
              <td><span class="item_header">Beneficiary Id:</span></td>
              <td><span class="item_value" id="unmark_ben_id"></span></td>
            </tr>
            <tr>
              <td><span class="item_header">Beneficiary Name:</span></td>
              <td><span class="item_value" id="unmark_ben_name"></span></td>
            </tr>
            <tr>
              <td><span class="item_header">Beneficiary Mobile Number:</span></td>
              <td><span class="item_value" id="unmark_app_mobile_no"></span></td>
            </tr>
            <tr>
              <td><span class="item_header">CMO Grievance Mobile Number:</span></td>
              <td><span class="item_value" id="unmark_smo_mobile_no"></span></td>
            </tr>
            <tr>
              <td colspan="2"><hr/></td>
            </tr>
          </table>
          <input type="hidden" id="unmark_beneficiary_id"/>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success" id="unmark_Button" data-dismiss="modal">Unmark</button>
          <button type="button" id="submittingapprove" value="Submit" class="btn btn-success btn-lg" style="display:none;"
                          disabled>Submitting please wait</button>
        </div>
      </div>
    </div>

  </div>
<?php $__env->stopSection(); ?>

  <!-- End Revert Model -->
<script src="<?php echo e(asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/master-data-v2.js')); ?>"></script>
<script>
    $(document).ready(function() {
        // Live Clock
        var interval = setInterval(function() {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);
        $('#loadingDiv').hide();
        $('#bulk_revert').hide();
        $('#submittingapprove').hide();
        $('#submit_btn').removeAttr('disabled');
        $('#reset_btn').removeAttr('disabled');
        // $('#search_for').change(function() {
        //     var search_for = $(this).val();
        //     if(search_for=='dup_ration_card' || search_for=='no_ration_card'){
        //         $("#scheme_id option").each(function(i){
        //             if($(this).val()==1 || $(this).val()==3 || $(this).val()==19){
        //                 $("#scheme_id option[value='"+ $(this).val() + "']").attr('disabled', false); 
        //             }
        //             else{
        //                 $("#scheme_id option[value='"+ $(this).val() + "']").attr('disabled', true);  
        //             }
                 
        //         });
        //     }
        //     else{
        //         $("#scheme_id option").each(function(i){
                    
        //                 $("#scheme_id option[value='"+ $(this).val() + "']").attr('disabled', false);  
                
                 
        //         });
        //     }
        // })
        // Master drop down 
        $('#district').change(function() {
            var district = $(this).val();
            //alert(district);
            $('#urban_code').val('');
            $('#block').html('<option value="">--All --</option>');
            $('#muncid').html('<option value="">--All --</option>');
        });

        $('#urban_code').change(function() {
            var urban_code = $(this).val();
            if (urban_code == '') {
                $('#muncid').html('<option value="">--All --</option>');
            }
            $('#muncid').html('<option value="">--All --</option>');
            $('#block').html('<option value="">--All --</option>');
            $('#gp_ward').html('<option value="">--All --</option>');
            select_district_code = $('#district').val();
            if (select_district_code == '') {
                alert('Please Select District First');
                $("#district").focus();
                $("#urban_code").val('');
            } else {
                select_body_type = urban_code;
                var htmlOption = '<option value="">--All--</option>';
                $("#gp_ward_div").show();
                if (select_body_type == 2) {
                    $("#blk_sub_txt").text('Block');
                    $("#gp_ward_txt").text('GP');
                    $("#municipality_div").hide();
                    $.each(blocks, function(key, value) {
                        if (value.district_code == select_district_code) {
                            htmlOption += '<option value="' + value.id + '">' + value.text +
                                '</option>';
                        }
                    });
                } else if (select_body_type == 1) {
                    $("#blk_sub_txt").text('Subdivision');
                    $("#gp_ward_txt").text('Ward');
                    $("#municipality_div").show();
                    $.each(subDistricts, function(key, value) {
                        if (value.district_code == select_district_code) {
                            htmlOption += '<option value="' + value.id + '">' + value.text +
                                '</option>';
                        }
                    });
                } else {
                    $("#blk_sub_txt").text('Block/Subdivision');
                }
                $('#block').html(htmlOption);
            }

        });
        $('#block').change(function() {
            var block = $(this).val();
            var district = $("#district").val();
            var urban_code = $("#urban_code").val();
            if (district == '') {
                $('#urban_code').val('');
                $('#block').html('<option value="">--All --</option>');
                $('#muncid').html('<option value="">--All --</option>');
                alert('Please Select District First');
                $("#district").focus();

            }
            if (urban_code == '') {
                alert('Please Select Rural/Urban First');
                $('#block').html('<option value="">--All --</option>');
                $('#muncid').html('<option value="">--All --</option>');
                $("#urban_code").focus();
            }
            if (block != '') {
                var rural_urbanid = $('#urban_code').val();
                if (rural_urbanid == 1) {
                    var sub_district_code = $(this).val();
                    if (sub_district_code != '') {
                        $('#muncid').html('<option value="">--All --</option>');
                        select_district_code = $('#district').val();
                        var htmlOption = '<option value="">--All--</option>';
                        $.each(ulbs, function(key, value) {
                            if ((value.district_code == select_district_code) && (value
                                    .sub_district_code == sub_district_code)) {
                                htmlOption += '<option value="' + value.id + '">' + value.text +
                                    '</option>';
                            }
                        });
                        $('#muncid').html(htmlOption);
                    } else {
                        $('#muncid').html('<option value="">--All --</option>');
                    }
                } else if (rural_urbanid == 2) {
                    $('#muncid').html('<option value="">--All --</option>');
                    $("#municipality_div").hide();
                    var block_code = $(this).val();
                    select_district_code = $('#district').val();

                    var htmlOption = '<option value="">--All--</option>';
                    $.each(gps, function(key, value) {
                        if ((value.district_code == select_district_code) && (value
                                .block_code == block_code)) {
                            htmlOption += '<option value="' + value.id + '">' + value.text +
                                '</option>';
                        }
                    });
                    $('#gp_ward').html(htmlOption);
                    $("#gp_ward_div").show();


                } else {
                    $('#muncid').html('<option value="">--All --</option>');
                    $("#municipality_div").hide();
                }
            } else {
                $('#muncid').html('<option value="">--All --</option>');
                $('#gp_ward').html('<option value="">--All --</option>');
            }

        });
        $('#muncid').change(function() {
            var muncid = $(this).val();
            var district = $("#district").val();
            var urban_code = $("#urban_code").val();
            if (district == '') {
                $('#urban_code').val('');
                $('#block').html('<option value="">--All --</option>');
                $('#muncid').html('<option value="">--All --</option>');
                alert('Please Select District First');
                $("#district").focus();

            }
            if (urban_code == '') {
                alert('Please Select Rural/Urban First');
                $('#block').html('<option value="">--All --</option>');
                $('#muncid').html('<option value="">--All --</option>');
                $("#urban_code").focus();
            }
            if (muncid != '') {
                var rural_urbanid = $('#urban_code').val();
                if (rural_urbanid == 1) {
                    var municipality_code = $(this).val();
                    if (municipality_code != '') {
                        $('#gp_ward').html('<option value="">--All --</option>');
                        var htmlOption = '<option value="">--All--</option>';
                        $.each(ulb_wards, function(key, value) {
                            if (value.urban_body_code == municipality_code) {
                                htmlOption += '<option value="' + value.id + '">' + value.text +
                                    '</option>';
                            }
                        });
                        $('#gp_ward').html(htmlOption);
                    } else {
                        $('#gp_ward').html('<option value="">--All --</option>');
                    }
                } else {
                    $('#gp_ward').html('<option value="">--All --</option>');
                    $("#gp_ward_div").hide();
                }
            } else {
                $('#gp_ward').html('<option value="">--All --</option>');
            }

        });


        // End Master drop down
        var error_scheme_id = '';
      
        var error_district = '';
        $('#submit_btn').click(function() {

            if ($.trim($('#scheme_id').val()).length == 0) {
                error_scheme_id = 'Scheme name is required';
                $('#error_scheme_id').text(error_scheme_id);
            } else {
                error_scheme_id = '';
                $('#error_scheme_id').text(error_scheme_id);
            }
        
            if ($.trim($('#district').val()).length == 0) {
                error_district = 'District is required';
                $('#error_district').text(error_district);
            } else {
                error_district = '';
                $('#error_district').text(error_district);
            }
            if (error_scheme_id != '' || error_district != '') {
                return false;
            } else {
                $('#loadingDiv').show();
                $('#search_details').show();
                // $(':input[type="button"]').prop('disabled', false);

                // var search_option = $('#search_for').val();
                var scheme_code = $('#scheme_id').val();
                var district = $('#district').val();
                var urban_code = $('#urban_code').val();
                var block = $('#block').val();
                var gp_ward = $('#gp_ward').val();
                var muncid = $('#muncid').val();
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
                    "pageLength": 25,
                    'lengthMenu': [
                        [10, 20, 25, 50, 100],
                        [10, 20, 25, 50, 100]
                    ],
                    "serverSide": true,
                    "processing": true,
                    "bRetrieve": true,
                    "oLanguage": {
                        "sProcessing": '<div class="preloader1" align="center"><font style="font-size: 20px; font-weight: bold; color: green;">Processing...</font></div>'
                    },
                    "ajax": {
                        url: "<?php echo e(url('sm-cmoMisReportlistPost')); ?>",
                        type: "post",
                        data: function(d) {
                            d.scheme_code = scheme_code,
                                d.district = district,
                                d.scheme_code = $('#scheme_id').val(),
                                d.urban_code = $('#urban_code').val(),
                                d.block = $('#block').val(),
                                d.gp_ward = $('#gp_ward').val(),
                                d.muncid = $('#muncid').val(),
                                d._token = "<?php echo e(csrf_token()); ?>"
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $('#submit_btn').attr('disabled', false);
                            $('#loadingDiv').hide();
                            $('.preloader1').hide();
                            ajax_error(jqXHR, textStatus, errorThrown);
                        }
                    },
                    "initComplete": function() {
                        $('#loadingDiv').hide();
                        $('#confirm_yes').on('click',function(){
                            $("#confirm_yes").hide();
                            $("#submittingapprove").show();
                            $("#revert_form").submit();
                            
                        
                        });
                       
                    },
                    "columns": [{
                            "data": "id"
                        },
                        {
                            "data": "name"
                        },
                        {
                            "data": "district_name"
                        },
                        {
                            "data": "block_name"
                        },
                        {
                            "data": "gp"
                        },
                        {
                            "data": "mobile_no"
                        },
                        {
                            "data": "sm_mobile_no"
                        },
                        {
                            "data": "approval_date"
                        }, 
                        {
                            "data": "view"
                        },  
                        {
                            "data": "check"
                        },   
                    ],
                    "buttons": [
                        {
                           extend: 'pdf',
                           footer: true,
                           pageSize:'A4',
                           //orientation: 'landscape',
                           pageMargins: [ 40, 60, 40, 60 ],
                           exportOptions: {
                                columns: [0,1,2,3,4,5,6,7,8,9],

                            }
                           },
                        //    {
                        //        extend: 'excel',
                        //        footer: true,
                        //        pageSize:'A4',
                        //        //orientation: 'landscape',
                        //        pageMargins: [ 40, 60, 40, 60 ],
                        //        exportOptions: {
                        //             columns: [0,1,2,3,4,5,6],
                        //             stripHtml: false,
                        //         }
                        //     },
                        // 'pdf'
                    ],
                });
                table.on('click','.ben_revert_button',function(){
                $tr = $(this).closest('tr');
                if(($tr).hasClass('child')){
                    $tr = $tr.prev('parent');
                }
                var data = table.row($tr).data();
                $('#revert_beneficiary_id').val(data['id']);
                $('#revert_ben_id').html(data['id']);
                $('#revert_ben_name').html(data['ben_name']);
                $('#revert_app_mobile_no').html(data['mobile_no']);
                $('#revert_smo_mobile_no').html(data['sm_mobile_no']);
                $('#ben_revert_modal').modal('show');
                });
                table.on('click','.ben_unmark_button',function(){
                $tr = $(this).closest('tr');
                if(($tr).hasClass('child')){
                    $tr = $tr.prev('parent');
                }
                var data = table.row($tr).data();
                $('#unmark_beneficiary_id').val(data['id']);
                $('#unmark_ben_id').html(data['id']);
                $('#unmark_ben_name').html(data['ben_name']);
                $('#unmark_app_mobile_no').html(data['mobile_no']);
                $('#unmark_smo_mobile_no').html(data['sm_mobile_no']);
                $('#ben_unmark_modal').modal('show');
                });
            }
        });



        $('#revert_Button').click(function(e){
        e.preventDefault();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '<?php echo e(url('sm-cmo-revert')); ?>',
            data: {
            ben_id: $('#revert_beneficiary_id').val(),
            scheme_id: $('#scheme_id').val(),
            _token: '<?php echo e(csrf_token()); ?>',
            },
            success: function (data) {
                if(data.return_status){
                    if(data.return_msg){
                    printMsg(data.return_msg,'0','errorDiv');
                    //console.log(data.session_lb_lifecertificate.is_error);
                    }
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    $('#example').DataTable().ajax.reload();
                }else{
          
                   printMsg(data.return_msg,'0','errorDiv');
                  $("html, body").animate({ scrollTop: 0 }, "slow");
                   return false;
        }
           
            },
            error: function (ex) {
             alert(sessiontimeoutmessage);
             window.location.href=base_url;
            }
        });
        });

        $('#unmark_Button').click(function(e){
        e.preventDefault();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '<?php echo e(url('sm-cmo-unmark')); ?>',
            data: {
            ben_id: $('#unmark_beneficiary_id').val(),
            scheme_id: $('#scheme_id').val(),
            _token: '<?php echo e(csrf_token()); ?>',
            },
            success: function (data) {
                if(data.return_status){
                    if(data.return_msg){
                    printMsg(data.return_msg,'0','errorDiv');
                    //console.log(data.session_lb_lifecertificate.is_error);
                    }
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    $('#example').DataTable().ajax.reload();
                }else{
          
                   printMsg(data.return_msg,'0','errorDiv');
                  $("html, body").animate({ scrollTop: 0 }, "slow");
                   return false;
        }
           
            },
            error: function (ex) {
             alert(sessiontimeoutmessage);
             window.location.href=base_url;
            }
        });
        });





        // Export Excel
        $('#excel_btn').click(function() {
            var error_scheme_id = '';
            var error_district = '';
            if ($.trim($('#scheme_id').val()).length == 0) {
                error_scheme_id = 'Scheme name is required';
                $('#error_scheme_id').text(error_scheme_id);
            } else {
                error_scheme_id = '';
                $('#error_scheme_id').text(error_scheme_id);
            }
            if ($.trim($('#district').val()).length == 0) {
                error_district = 'District is required';
                $('#error_district').text(error_district);
            } else {
                error_district = '';
                $('#error_district').text(error_district);
            }
            if (error_scheme_id != ''  || error_district != '') {
                return false;
            } else {
                var scheme_code = $('#scheme_id').val();
                var district = $('#district').val();
                var urban_code = $('#urban_code').val();
                var block = $('#block').val();
                var gp_ward = $('#gp_ward').val();
                var muncid = $('#muncid').val();
                var token = "<?php echo e(csrf_token()); ?>";
                var data = {
                    '_token': token,
                    scheme_id: scheme_code,
                    district: district,
                    urban_code: urban_code,
                    block: block,
                    gp_ward: gp_ward,
                    $muncid: muncid
                };
                redirectPost('sm-cmoMisReportlistExcel', data);
            }
        });
       
    });

    function redirectPost(url, data, method = 'post') {
        var form = document.createElement('form');
        form.method = method;
        form.action = url;
        for (var name in data) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = data[name];
            form.appendChild(input);
        }
        $('body').append(form);
        form.submit();
    }

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
            $.each(msg, function(key, value) {
                $("#" + divid).find("ul").append('<li>' + value + '</li>');
            });
        } else {
            $("#" + divid).find("ul").append('<li>' + msg + '</li>');
        }
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
                $.each(jqXHR.responseJSON, function(key, value) {
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
    function controlCheckBox(){
    //console.log('ok');
    var anyBoxesChecked = false;
    $(' input[type="checkbox"]').each(function() {
      if ($(this).is(":checked")) {
        anyBoxesChecked = true;
      }
    });
    if (anyBoxesChecked == true) {
      $("#bulk_revert").show();
      document.getElementById('bulk_revert').disabled = false;
    } else{
      $("#bulk_revert").hide();
      document.getElementById('bulk_revert').disabled = true;
    }
  }
  function closeError(divId){
   $('#'+divId).hide();
  }
</script>

<?php echo $__env->make('layouts.app-template-datatable_new', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>