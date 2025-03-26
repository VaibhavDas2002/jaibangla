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
      Verify/Update Incomplete Pending Data
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
              <label class=" control-label">Scheme <span class="text-danger">*</span></label>
              <select class="form-control" name="scheme_type" id='scheme_type' required>
              <option value="">--Select Scheme--</option>
              <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($scheme->id); ?>"><?php echo e($scheme->scheme_name); ?>

          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <span class="text-danger" id="error_scheme_type"></span>
            </div>
            <div class="form-group col-md-4">
              <label class="required-field">Operation Type <span class="text-danger">*</span></label>
              <select class="form-control" name="filter_type" id="filter_type">
              <option value="">--Select--</option>
              <?php $__currentLoopData = $incomplete_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($type->id); ?>"><?php echo e($type->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <span id="error_filter_type" class="text-danger"></span>
            </div>


            <input type="hidden" name="urban_code" id="urban_code" value="<?php echo e($rural_urban_fk); ?>" />
            <input type="hidden" name="block" id="block" value="<?php echo e($block_munc_corp_code_fk); ?>" />
            <input type="hidden" name="type" id="type" value="<?php echo e($type_id); ?>" />

            <?php echo $__env->make('common-selection.index', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <div class="form-group col-md-3" id="failed_type_div" style="display: none;">
              <label class="required-field">Failed Type <span class="text-danger">*</span></label>
              <select class="form-control" name="failed_type" id="failed_type">
              <option value="">--Select--</option>
              <option value="3">SBI</option>
              <option value="4">RBI</option>
              <option value="5">IFMS</option>
              </select>
              <span id="error_failed_type" class="text-danger"></span>
            </div>




            </div>




          </div>
          <div style="text-align: center;">
            <button class="btn btn-primary" name="submit_btn" id="submit_btn" type="button" disabled><i
              class="fa fa-search"></i> Search</button>&nbsp;
          </div>

          <div class="col-md-12" style="text-align: left; margin-top: 20px;">
            <form action="<?php echo e(route('getNoDupListExcel')); ?>" method="post">
            <?php echo e(csrf_field()); ?>

            <input type="hidden" name="excel_scheme_id" id="excel_scheme_id" />
            <input type="hidden" name="excel_filter_id" id="excel_filter_id" />
            <input type="hidden" name="excel_filter_blk_ulb_body" id="excel_filter_blk_ulb_body" />
            <input type="hidden" name="excel_filter_gp_ward" id="excel_filter_gp_ward" />
            <button class="btn btn-success" name="excel_btn" id="excel_btn" type="submit" disabled>
              <i class="fa fa-file-excel-o"></i> Download List
            </button>
            </form>
          </div>


          <div class="col-md-12" style="text-align: right; ">
            <button class="btn btn-info btn-md" id="ild_bulk" style="margin-right: 5px;"><i class="fa fa-download"></i>
            ILDB</button>
          </div>
          </div>
        </div>
        </div>
      </div>

      <div class="panel panel-default" id="tagging_div">
        <div class="panel-heading"
        style="font-size: 14px; font-weight: bold; font-style: italic;background-color:rgb(197, 232, 231);"><span
          id="panel-icon">Tagged to Incomplete Details Beneficiary</div>
        <div class="panel-body" style="padding: 5px;">
        <div class="row">
          <form method="post" id="register_form" action="<?php echo e(route('NoDup_assign_arrival_date')); ?>">
          <?php echo e(csrf_field()); ?>

          <div class="row">
            <div class="col-md-12" style="margin-bottom: 10px;">
            <div class="col-md-3">
              <label class=" control-label">No. of Applicants <span class="text-danger">*</span></label>
              <input type="text" name="no_of_applicants" id="no_of_applicants" class="form-control NumOnly" />
              <span class="text-danger" id="error_no_of_applicants"></span>
            </div>

            <div class="col-md-3">
              <label class=" control-label">Visiting Date<span class="text-danger">*</span></label>
              <input type="date" name="arrival_date" id="arrival_date" class="form-control"
              min="<?php  echo date(" Y-m-d");  ?> "/>
      <span class=" text-danger" id="error_arrival_date"></span>
            </div>
            <div class="form-group col-md-3">
              <label class="required-field control-label">Visiting Time </label>
              <select name="visiting_time" id="visiting_time" class="form-control">
              <option value="">----- Select Time -----</option>
              <?php $__currentLoopData = $time; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($value); ?>"><?php echo e($value); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <span id="error_visiting_time" style="font-size: 14px; color: firebrick;"></span>
            </div>
            <input type="text" name="incomplete_type" id="incomplete_type" />
            <input type="text" name="failed_type_func" id="failed_type_func" />
            <input type="hidden" name="assign_scheme_id" id="assign_scheme_id">
            <div class="col-md-3" style="margin-top: 24px;">
              <button class="btn btn-primary" name="search_btn" id="search_btn" type="submit">
              Tag</button>&nbsp;&nbsp;&nbsp;
            </div>
            </div>
          </div>
          </form>
        </div>
        </div>
      </div>

      <div id="res_div" style="display: none;">
        <div class="panel panel-default">
        <div class="panel-heading" id="panel_head" style="font-size: 14px; font-weight: bold; font-style: italic;">
          List of Beneficiary</div>
        <div class="panel-body" style="padding: 5px; font-size: 14px;">
          <div class="table-responsive">
          <table id="example" class="table display" cellspacing="0" width="100%">
            <thead style="font-size: 12px;">
            <th>Application ID</th>
            <th>Applicant Name</th>
            <th>Block/Municipality</th>
            <th>GP/Ward</th>
            <th>Aadhar No</th>
            <th>Bank A/C</th>
            <th>Bank IFSC</th>
            <th>Incomplete Status</th>
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
    <!-- /.content -->

    <!-- Update Details Modal -->
    <!-- Modal -->
    <div class="modal fade" id="modalUpdate" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Verify Details</h4>
      </div>
      <div class="modal-body">
        <div class="loadingDivModal"></div>
        <div class="" id="updateDiv">
        <div class="row">
          <div class="col-md-12">
          <h4 style="text-align: center;" class="text-primary">Application ID: <span id="application_id"></span>
          </h4>
          </div>
        </div>
        <div id="benMobileDetails"></div>
        <input type="hidden" name="pension_id" id="pension_id" value="">
        <input type="hidden" name="update_scheme_id" id="update_scheme_id" value="">
        <input type="hidden" name="update_type" id="update_type" value="">
        <div class="table-responsive">
          <table class="table table-bordered table-responsive table-condensed" style="width:100%; font-size: 14px;">
          <tr>
            <th>Action: <span class="text-danger">*</span></th>
            <td>
            <select id="action_type" name="action_type" class="form-control">
              <option value="verify" selected>Verify</option>
            </select>
            <span id="error_action_type" class="text-danger"></span>
            </td>
            <th class="viewAadharCardRow">View Aadhar Card: </th>
            <td class="viewAadharCardRow">
            <button name="view_aadhar" id="view_aadhar" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i>
              View</button>
            </td>
          </tr>
          <tr>
            <th>Remarks: </th>
            <td>
            <input type="text" name="remarks" id="remarks" class="form-control" value="" maxlength="100">
            <small style="font-weight: normal;">Max 100 character allowed</small>
            </td>
          </tr>
          </table>
        </div>
        <div class="row">
          <div class="col-md-12" style="text-align: center;"><input type="button" name="submit" value="Submit"
            id="verifySubmit" class="btn btn-success btn-lg"></div>
        </div>
        <!-- </div> -->
        </div>
      </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Aadhar card view modal -->
    <div class="modal fade" id="modalAadharView" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="docTypeName">View Aadhar Card</h4>
      </div>
      <div class="modal-body">
        <img id="showAadhar">
      </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

  </div>
<?php $__env->stopSection(); ?>
<script src="<?php echo e(asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
<script src="<?php echo e(URL::asset('js/beneficiary_jb_tagged_form_oap.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/beneficiary_jb_tagged_form_manabik.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/beneficiary_jb_tagged_form_wp.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/pdfmake.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/vfs_fonts.js')); ?>"></script>
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
    $('#tagging_div').hide();
    $('#ild_bulk').hide();

    // if ($('#scheme_type').val() && $('#filter_type').val()) {
    // }else{
    //   $('#excel_btn').prop('disabled', true);
    // }

    $('#submit_btn').click(function () {
      if ($.trim($('#filter_type').val()).length !== 0 && $.trim($('#scheme_type').val()).length !== 0) {
        var incomplete_type = $('#filter_type').val();
        var failed_type = $('#failed_type').val();
        var assign_scheme_id = $('#scheme_type').val();

        $('#incomplete_type').val(incomplete_type);
        $('#failed_type_func').val(failed_type);
        $('#assign_scheme_id').val(assign_scheme_id); // Fixed missing #

        var scheme_id = parseInt(assign_scheme_id, 10); // Ensure scheme_id is an integer
        if ([2, 10, 11].includes(scheme_id)) {
          $('#tagging_div').show();
          $('#ild_bulk').show();
        } else {
          $('#tagging_div').hide();
          $('#ild_bulk').hide();
        }
      }
    });

    $('#ild_bulk').click(function(){
      
    });








    if ($('#filter_type').val() === '10') { // Ensure comparison to string
      $('#failed_type_div').show().css('display', 'inline');
    } else {
      $('#failed_type_div').hide();
    }
    // alert($('#scheme_type').val());
    $('#excel_filter_id').val($('#filter_type').val());
    // $('#excel_scheme_id').val($('#scheme_type').val());

    $('#scheme_type').change(function () {
      const value = $(this).val(); // Get the value of the selected option
      if (value !== '') {
        $('#excel_scheme_id').val(value); // Set the value of #excel_scheme_id
      }
    });

    $('#blk_ulb_code').change(function () {
      const value = $(this).val(); // Get the value of the selected option
      if (value !== '') {
        $('#excel_filter_blk_ulb_body').val(value); // Set the value of #excel_scheme_id
      }
    });



    $('#filter_type').change(function () {
      const value = $(this).val(); // Get the value of the selected option
      if (value !== '') {
        $('#excel_filter_id').val(value);
      }
    });


    var error_scheme_type = '';
    var error_filter_type = '';
    var error_failed_type = '';


    $('#filter_type').on('change', function () {
      // Get the text of the selected option
      if ($(this).find(':selected').text() === 'Payment Failure') {
        $('#failed_type_div').show();
      } else {
        $('#failed_type_div').hide();
      }
    });

    $('#submit_btn').click(function () {
      var filter_type = $.trim($('#filter_type').val());
      if (filter_type == 10) {
        if ($.trim($('#failed_type').val()).length == 0) {
          error_failed_type = 'Failed type is required';
          $('#error_failed_type').text(error_failed_type);
          $('#failed_type').addClass('has-error');
        } else {
          error_failed_type = '';
          $('#error_failed_type').text(error_failed_type);
          $('#failed_type').removeClass('has-error');
        }
      }

      if ($.trim($('#scheme_type').val()).length == 0) {
        error_scheme_type = 'Scheme name is required';
        $('#error_scheme_type').text(error_scheme_type);
      } else {
        error_scheme_type = '';
        $('#error_scheme_type').text(error_scheme_type);
      }

      if ($.trim($('#filter_type').val()).length == 0) {
        error_filter_type = 'Filter is required';
        $('#error_filter_type').text(error_filter_type);
      } else {
        error_filter_type = '';
        $('#error_filter_type').text(error_filter_type);
      }
      if (filter_type == 10) {
        if (error_scheme_type != '' || error_filter_type != '' || error_failed_type != '') {
          return false;
        } else {
          loadDatatable();
        }
      } else {
        if (error_scheme_type != '' || error_filter_type != '') {
          return false;
        } else {
          loadDatatable();
        }
      }

    });
  });

  function benDownloadAssignFunction(value, scheme_id) {
    $('#loadingDi').show();
    $.ajax({
      type: 'post',
      url: "<?php echo e(route('NoDup-validation-correction-form-download')); ?>",
      data: { scheme_id: scheme_id, id: value, _token: '<?php echo e(csrf_token()); ?>' },
      success: function (response) {
        $('#loadingDi').hide();
        if (response.status == 1) {
          $.alert({
            title: response.title,
            type: response.type,
            icon: response.icon,
            content: response.msg
          });
        }
        else {
          //console.log("Data Array:", response.data_array); // Debugging
          const data = response.data_array;
          if (data.length === 0) {
            console.warn("No Data Received!");
            return;
          }

          if (data[0].scheme_id == 10) {
            // console.log("Passing Data to OAP Function:", data);
            beneficiary_jb_tagged_form_oap_n(data);
          }
          if (data[0].scheme_id == 11) {
            // console.log("Passing Data to WP Function:", data);
            beneficiary_jb_tagged_form_wp(data);
          }
          if (data[0].scheme_id == 2) {
            // console.log("Passing Data to Manabik Function:", data);
            beneficiary_jb_tagged_form_manabik(data);
          }
        }
      },
      complete: function () {
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#loadingDi').hide();
        ajax_error(jqXHR, textStatus, errorThrown);
      }
    });
  }



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
        url: "<?php echo e(route('getNoDupList')); ?>",
        type: "POST",
        data: function (d) {
          d.scheme_id = $('#scheme_type').val(),
            d.filter_type = $('#filter_type').val(),
            d.is_urban = $('#rural_urban_code').val(),
            d.blk_ulb_code = $('#blk_ulb_code').val(),
            d.failed_type = $('#failed_type').val(),
            d.pay_validated = $('#failed_type').val(),
            d._token = "<?php echo e(csrf_token()); ?>"
        },
        error: function (jqXHR, textStatus, errorThrown) {
          $('#loadingDi').hide();
          $('.preloader1').hide();
          // ajax_error(jqXHR, textStatus, errorThrown);
          // $.alert({
          //   title: 'Error!!',
          //   type: 'red',
          //   icon: 'fa fa-warning',
          //   content: 'Loading Error! Session timeout, please logout and login again.'
          // });
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
          "data": "aadhar_no"
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

  function verifyAadharMobileFunction(ben_id, scheme_id, update_type) {
    $('#loadingDi').show();
    $.ajax({
      type: 'post',
      url: "<?php echo e(route('getVerifyDuplicateBenModalView')); ?>",
      data: {
        scheme_id: scheme_id,
        id: ben_id,
        _token: '<?php echo e(csrf_token()); ?>'
      },
      success: function (response) {
        $('#loadingDi').hide();
        // console.log(JSON.stringify(response));
        if (response.status == 1) {
          $.alert({
            title: response.title,
            type: response.type,
            icon: response.icon,
            content: response.msg
          });
        } else {
          $('.viewAadharCardRow').hide();
          $('#pension_id').val('');
          $('#update_scheme_id').val('');
          $('#update_type').val();
          $('#remarks').val('');
          $('#action_type').removeClass('has-error');
          $('#error_action_type').text('');
          var benMobileDetails_msg = '';
          benMobileDetails_msg += '<span style="font-size:16px;">Name - ' + response.ben_name + '<br/> Father\'s Name - ' + response.father_name + '<br/> Gender - ' + response.gender + '<br/> Caste - ' + response.caste;
          if (update_type == 'aadhar') {
            benMobileDetails_msg += '<br/> New Aadhar Number - ' + response.aadhar_no;
          } else if (update_type == 'mobile') {
            benMobileDetails_msg += '<br/> New Mobile Number - ' + response.mobile_no;
          } else if (update_type == 'no_mobile') {
            benMobileDetails_msg += '<br/> New No Mobile Number - ' + response.mobile_no;
          }
          benMobileDetails_msg += '</span>';
          $('#benMobileDetails').html(benMobileDetails_msg);
          $('#pension_id').val(response.id);
          $('#update_scheme_id').val(response.scheme_id);
          $('#update_type').val(update_type);
          $('#application_id').text(response.id);
          $('.loadingDivModal').hide();
          if (update_type == 'aadhar') {
            $('.viewAadharCardRow').show();
          }
          $('#modalUpdate').modal('show');
        }
      },
      complete: function () { },
      error: function (jqXHR, textStatus, errorThrown) {
        $('#loadingDi').hide();
        ajax_error(jqXHR, textStatus, errorThrown);
      }
    });
  }

  $(document).on('click', '#verifySubmit', function () {
    var error_action_type = '';
    var action_type = $.trim($('#action_type').val());

    if ($.trim($('#action_type').val()).length == 0) {
      error_action_type = 'Action type is required';
      $('#error_action_type').text(error_action_type);
      $('#action_type').addClass('has-error');
    } else {
      error_action_type = '';
      $('#error_action_type').text(error_action_type);
      $('#action_type').removeClass('has-error');
    }

    if (error_action_type != '') {
      return false;
    } else {
      // alert('OK');
      var beneficiary_Id = $('#pension_id').val();
      var updateSchemeId = $('#update_scheme_id').val();
      var update_type = $('#update_type').val();
      if (beneficiary_Id == '' || updateSchemeId == '' || update_type == '') {
        $.alert({
          title: 'Error!!',
          type: 'red',
          icon: 'fa fa-warning',
          content: 'Something went wrong !!'
        });
        return false;
      }
      $.confirm({
        type: 'orange',
        title: 'Confirmation!',
        content: 'Are you sure want to verify this beneficiary ?',
        icon: 'fa fa-warning',
        buttons: {
          confirm: {
            text: 'Confirm',
            btnClass: 'btn-blue',
            keys: ['enter', 'shift'],
            action: function () {
              // alert('OK');
              var beneficiary_Id = $('#pension_id').val();
              var updateSchemeId = $('#update_scheme_id').val();
              var update_type = $('#update_type').val();
              var action_type = $('#action_type').val();
              var remarks = $('#remarks').val();
              var formData = new FormData();
              formData.append('id', beneficiary_Id);
              formData.append('scheme_id', updateSchemeId);
              formData.append('update_type', update_type);
              formData.append('action_type', action_type);
              formData.append('remarks', remarks);
              formData.append('_token', '<?php echo e(csrf_token()); ?>');
              $('.loadingDivModal').show();
              $.ajax({
                type: 'POST',
                url: "<?php echo e(route('updateVerifiedDuplicateBen')); ?>",
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (response) {
                  $('.loadingDivModal').hide();
                  if (response.status == 1) {
                    $.alert({
                      title: response.title,
                      type: response.type,
                      icon: response.icon,
                      content: response.msg
                    });
                    $('#modalUpdate').modal('hide');
                    $('#res_div').hide();
                    loadDatatable();
                    $("html, body").animate({
                      scrollTop: 0
                    }, "slow");
                  } else {
                    var html = '';
                    html += '<ul>';
                    if (Array.isArray(response.msg)) {
                      $.each(response.msg, function (key, value) {
                        html += '<li>' + value + '</li>';
                      });
                    } else {
                      html = '<li>' + response.msg + '</li>';
                    }
                    html += '<ul>';
                    $.alert({
                      title: response.title,
                      type: response.type,
                      icon: response.icon,
                      content: html
                    });
                  }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                  $('.loadingDivModal').hide();
                  ajax_error(jqXHR, textStatus, errorThrown);
                }
              });
            }
          },
          cancel: function () { },
        }
      });
    }
  });

  // Aadhar card viewing
  $(document).on('click', '#view_aadhar', function () {
    var beneficiary_Id = $('#pension_id').val();
    var updateSchemeId = $('#update_scheme_id').val();
    var update_type = $('#update_type').val();
    if (beneficiary_Id == '' || updateSchemeId == '' || update_type != 'aadhar') {
      $.alert({
        title: 'Error!!',
        type: 'red',
        icon: 'fa fa-warning',
        content: 'Something went wrong !!'
      });
      return false;
    }
    $('.loadingDivModal').show();
    var formData = new FormData();
    formData.append('id', beneficiary_Id);
    formData.append('scheme_id', updateSchemeId);
    formData.append('update_type', update_type);
    formData.append('_token', '<?php echo e(csrf_token()); ?>');
    $.ajax({
      type: 'POST',
      url: "<?php echo e(route('viewVerifyDeDupAadharCard')); ?>",
      data: formData,
      dataType: 'json',
      processData: false,
      contentType: false,
      success: function (response) {
        $('.loadingDivModal').hide();
        if (response.status == 1) {
          $.alert({
            title: response.title,
            type: response.type,
            icon: response.icon,
            content: response.msg
          });
        } else {
          $('#docTypeName').text(response.doc_type_name);
          $('#showAadhar').attr('alt', "Aadhar Card");
          $('#showAadhar').attr('src', response.doc_name);
          $('#modalAadharView').modal('show');
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        $('.loadingDivModal').hide();
        ajax_error(jqXHR, textStatus, errorThrown);
      }
    });
  });

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