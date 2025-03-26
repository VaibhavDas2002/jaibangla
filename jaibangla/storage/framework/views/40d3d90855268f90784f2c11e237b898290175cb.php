<!DOCTYPE html>

<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>JB | Jai Bangla</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link href="<?php echo e(asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css")); ?>" rel="stylesheet"
    type="text/css" />
  <link href="<?php echo e(asset("css/select2.min.css")); ?>" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link href="<?php echo e(asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")); ?>" rel="stylesheet" type="text/css" />
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
  <link href="<?php echo e(asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")); ?>" rel="stylesheet"
    type="text/css" />

  <!-- bootstrap wysihtml5 - text editor -->
  <!-- <link rel="stylesheet" href="<?php echo e(asset("/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")); ?>"> -->

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <link href="<?php echo e(asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")); ?>" rel="stylesheet"
    type="text/css" />

  <style>
    * {
      font-size: 15px;
    }

    .field-name {
      float: left;
      font-weight: 600;
      font-size: 17px;
      margin-right: 3%;
      padding-top: 1%;
    }

    .field-value {


      font-size: 17px;
      padding-top: 1%;

    }

    .required-field::after {
      content: "*";
      color: red;
    }

    .row {
      margin-right: 0px !important;
      margin-left: 0px !important;
    }

    .section1 {
      border: 1.5px solid #9187878c;
      overflow: hidden;
      padding-bottom: 10px;


    }

    .color1 {

      background-color: #dcdfdf;
    }

    .color1 h3 {
      margin: 10px 0px 10px 0px !important;
    }

    .setPos {
      padding: 0px 0px 10px 0px;
      margin: 10px 0px 10px 0px;
      border: 1px solid #dcdfdf;
      overflow: hidden;
    }

    .modal_field_name {
      float: left;
      font-weight: 700;
      margin-right: 1%;
      padding-top: 1%;
      margin-top: 1%;
    }

    .modal_field_value {
      margin-right: 1%;
      padding-top: 1%;
      margin-top: 1%;
    }

    .modal-header {
      background-color: #7fffd4;
    }

    @media  print {
      .example-screen {
        display: none;
      }

      * {
        font-size: 15px;
      }

      .field-name {
        float: left;
        font-weight: 600;
        font-size: 17px;
        margin-right: 3%;
        padding-top: 1%;
      }

      .field-value {


        font-size: 17px;
        padding-top: 1%;

      }

      .row {
        margin-right: 0px !important;
        margin-left: 0px !important;
      }

      .section1 {
        border: 1.5px solid #9187878c;
        overflow: hidden;
        padding-bottom: 10px;


      }

      .color1 {

        background-color: #dcdfdf;

      }

      .color1 h3 {
        margin: 10px 0px 10px 0px !important;
      }

      .setPos {
        padding: 0px 0px 10px 0px;
        margin: 10px 0px 10px 0px;
        border: 1px solid #dcdfdf;
        overflow: hidden;
      }

      .modal_field_name {
        float: left;
        font-weight: 700;
        margin-right: 1%;
        padding-top: 1%;
        margin-top: 1%;
      }

      .modal_field_value {
        margin-right: 1%;
        padding-top: 1%;
        margin-top: 1%;
      }

      .modal-header {
        background-color: #7fffd4;
      }

      /*.row{
  margin-right: 0px!important;
  margin-left: 0px!important;
}
.section1{
    border: 1.5px solid #9187878c!important;
    margin: 0.25cm!important;
    padding: 0.25cm!important;
    page-break-inside : avoid;
}
.color1{
  margin: 0%!important;
  background-color: #5f9ea061!important;
  -webkit-print-color-adjust: exact; 
}
.modal_field_name{
  float:left!important;
  font-weight: 700!important;
  margin-right:0.5cm!important;

}

.modal_field_value{
  padding-top:0.30cm!important;

}
.color1{
  margin: 0%!important;
  background-color: #7fffd4!important;
 -webkit-print-color-adjust: exact; 
}

.modal-header{
  background-color: #7fffd4!important;
 -webkit-print-color-adjust: exact; 
}
#divToPrint{
}*/
    }

    .btnJb {
      margin: 20px;
    }
  </style>


</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <!-- Main Header -->
    <?php echo $__env->make('layouts.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <!-- Sidebar -->
    <?php echo $__env->make('layouts.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">

            <?php if(($message = Session::get('success')) && ($id = Session::get('lb_id'))): ?>
        <div class="alert alert-success alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong><?php echo e($message); ?> with LB Application ID: <?php echo e($id); ?></strong>


        </div>
      <?php endif; ?>
            <?php if($message = Session::get('error')): ?>
        <div class="alert alert-danger alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong><?php echo e($message); ?></strong>


        </div>
      <?php endif; ?>
           

      <?php if(count($errors) > 0): ?>
                            <div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul>
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><strong> <?php echo e($error); ?></strong></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
            <!--   <?php if($message = Session::get('failure')): ?>
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong><?php echo e($message); ?></strong>
              </div>
              <?php endif; ?> -->
          </div>
          <!-- /.box-header -->
          <!-- form start -->


          <div class="tab-content" style="margin-top:16px;">


            <div class="row">
              <div class="col-md-12">
                <h3 style="text-align: center; color:red;">LB Application ID:<?php echo e($row->lb_application_id); ?><a
                    href="<?php echo e(route('rejectrevivallist', ['scheme_id' => $row->scheme_id])); ?>">
                    <img width="50px;" style="pull-right" src="<?php echo e(asset("images/back.png")); ?>" alt="Back" /></a></h3>
              </div>
            </div>



            <div class="tab-pane active" id="personal_details">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h4><b>LB Beneficiary Imported Report </b></h4>
                </div>
                <div class="panel-body">

                  <?php echo $__env->make('pension-details-view.personal_details', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  <?php echo $__env->make('pension-details-view.personal_identification', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  <?php echo $__env->make('pension-details-view.contact_details', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  <?php echo $__env->make('pension-details-view.bank_details', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  <?php echo $__env->make('pension-details-view.enclosure_list', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  <?php echo $__env->make('pension-details-view.additional_details', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>


                  <div class="panel panel-default">
                    <div class="panel-heading" id="panel_head"
                      style="font-size: 14px; font-weight: bold; font-style: italic;">Beneficiary Revival Details</div>
                    <div class="panel-body" style="padding: 5px; font-size: 14px;">
                      <div class="row">
                        <form method="post" id="ReviveBen" action="<?php echo e(url('lbapplicationRevive')); ?>" class="submit-once"
                          enctype="multipart/form-data">
                          <?php echo e(csrf_field()); ?>


                          <input type="hidden" name="id" id="id" value="<?php echo e($row->id); ?>" />
                          <input type="hidden" name="scheme_id" id="scheme_id" value="<?php echo e($row->scheme_id); ?>" />
                          <input type="hidden" name="action_type" id="action_type" value="" />
                          <input type="hidden" name="action_msg" id="action_msg" value="" />
                          <input type="hidden" name="district_code" id="district_code"
                            value="<?php echo e($row->created_by_dist_code); ?>" />
                          <div id="cause_div">
                            <div class="form-group col-md-12" id="divBodyCode">
                              <label class="required-field">Cause</label>
                              <select name="reject_revert_cause" id="reject_revert_cause" class="form-control">
                                <option value="">--Select--</option>
                                <?php $__currentLoopData = $reject_revert_cause_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cause_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                  <option value="<?php echo e($cause_item->id); ?>">
                                    <?php echo e($cause_item->reason); ?>

                                  </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              </select>
                              <span id="error_reject_revert_cause" class="text-danger"></span>
                            </div>
                          </div>
                          <div class="form-group col-md-4">
                            <label class="required-field">Remarks</label>
                            <input type="text" name="remarks" id="remarks" class="form-control"
                              placeholder="EPIC/Voter Id.No." maxlength="20" value="<?php echo e(old('remarks')); ?>" />
                            <span id="remarks" class="text-danger"></span>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>



                </div>

















                <br /> <br /> <br /> <br />
              </div>

            </div>

            <div class="col-md-12" align="center">

              <div class="btn-group">

                <?php if($can_revive == 1): ?>
          <button type="button" class="btnJb btn btn-info confirmBtn" id="revive" value="10"
            op_text="Revieve the Beneficiary">Revive</button>
        <?php endif; ?>





              </div>

            </div>
            <br />
          </div>
        </div>
    </div>


  </div>
  </div>





  </div>






  </div>
  <!-- /.box -->
  </div>
  <!--/.col (left) -->

  </div>
  <!--  <?php if(session()->has('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session()->get('success')); ?>

        </div>
      <?php endif; ?> -->
  <!-- /.row -->
  <div id="modalReject" class="modal fade">



    <div class="modal-dialog modal-confirm">
      <div class="modal-content">
        <div class="modal-header flex-column">

          <h4 class="modal-title w-100">LB Application ID:<?php echo e($row->lb_application_id); ?></h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body">


          <h4 style="font-size:30px;color: #fc3903;">Are you Sure.. You want to <span id="op_text"
              style="font-size:30px;color: #fc3903;"></span>?</h4>


        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-info modal-submitapprove">OK</button>
          <button type="button" id="submittingapprove" value="Submit" class="btn btn-success success btn-lg"
            disabled>Submitting please wait</button>
        </div>
      </div>
      </form>
    </div>

  </div>

  </section>

  <!-- Main content -->
  <!--  <section class="content">

      Your Page Content Here



    </section> -->
  <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Footer -->
  <?php echo $__env->make('layouts.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

  <!-- ./wrapper -->

  <!-- REQUIRED JS SCRIPTS -->

  <!-- jQuery 2.1.3 -->
  <script src="<?php echo e(asset("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
  <script src="<?php echo e(asset("/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js")); ?>"
    type="text/javascript"></script>
  <script src="<?php echo e(asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js")); ?>"
    type="text/javascript"></script>

  <!-- Bootstrap 3.3.2 JS -->
  <script src="<?php echo e(asset("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js")); ?>" type="text/javascript"></script>

  <!-- AdminLTE App -->
  <script src="<?php echo e(asset("/bower_components/AdminLTE/dist/js/app.min.js")); ?>" type="text/javascript"></script>
  <script src="<?php echo e(asset("js/jquery.dataTables.min.js")); ?>"></script>
  <script src="<?php echo e(URL::asset('js/validateAdhar.js')); ?>"></script>
  <script type="text/javascript">
    $(document).ready(function () {
      $("#submittingapprove").hide();
      $(".NumOnly").keyup(function (event) {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
          event.preventDefault();
        }
      });

      $('.confirmBtn').click(function () {

      }
      );


      $('.confirmBtn').click(function () {
        $('#error_mobile_no').text('');
        $('#error_new_aadhar_no').text('');
        $('#error_reject_revert_cause').text('');
        $('#error_remarks').text('');

        var designation_id = $("#designation_id").val();
        var ButtonText = $(this).text();
        var clickval = $(this).val();
        // alert(clickval);
        $("#action_type").val('');
        $('.verify_reject').text('');
        var op_text = $(this).attr("op_text");
        $('#op_text').text(op_text);
        $('#action_msg').val(op_text);
        // alert(designation_id);alert(clickval);
        if (designation_id == 'Verifier') {
          if (clickval == 5) {
            $("#action_type").val(clickval);
            var error_new_mobile_no = '';
            var error_new_aadhar_no = '';
            if ($.trim($('#new_mobile_no').val()) != "") {
              if ($.trim($('#new_mobile_no').val()).length != 10) {
                error_new_mobile_no = 'Mobile Number must be 10 digit';
                $('#error_new_mobile_no').text(error_new_mobile_no);
                $('#new_mobile_no').addClass('has-error');
              }
              else {
                error_new_mobile_no = '';
                $('#error_mobile_no').text(error_new_mobile_no);
                $('#new_mobile_no').removeClass('has-error');

              }
            }
            if ($.trim($('#new_aadhar_no').val()) != "") {
              if ($.trim($('#new_aadhar_no').val()).length != 12) {

                error_new_aadhar_no = 'Aadhar No should be 12 digit ';
                $('#error_new_aadhar_no').text(error_new_aadhar_no);
                $('#new_aadhar_no').addClass('has-error');
              }
              else {
                var new_aadhar_no = $('#new_aadhar_no').val();
                if (new_aadhar_no != '') {
                  var aadhar_valid = validate_adhar(new_aadhar_no);
                  // aadhar_valid=1;
                  if (aadhar_valid) {
                    error_new_aadhar_no = '';
                    $('#error_new_aadhar_no').text(error_new_aadhar_no);
                    $('#new_aadhar_no').removeClass('has-error');
                  }
                  else {
                    error_new_aadhar_no = 'Invalid Aadhar No.';
                    $('#error_new_aadhar_no').text(error_new_aadhar_no);
                    $('#new_aadhar_no').addClass('has-error');
                  }
                }
                else {
                  error_new_aadhar_no = '';
                  $('#error_new_aadhar_no').text(error_new_aadhar_no);
                  $('#new_aadhar_no').removeClass('has-error');
                }
              }
            }
          }

          if (error_new_mobile_no == '' && error_new_aadhar_no == '') {
            $('#modalReject').modal();
          }
        }

        $("#action_type").val(clickval);
        if (designation_id == 'Verifier') {
          //alert('clickval'+clickval);
          if (clickval == 70 || clickval == 75) {
            $("#note_sc_st").show();
          }
          else {
            $("#note_sc_st").hide();
          }
          if (clickval == 7) {
            $("#dob_change_div").show();
          }
          else {
            $("#dob_change_div").hide();
          }
          if (clickval == 80) {
            $("#reason_order_div").show();
          }
          else {
            $("#reason_order_div").hide();
          }
          if (clickval == 5 || clickval == 50) {
            $("#cause_div").hide();
            $("#remark_div").hide();
          }
          else {
            $("#cause_div").show();
            $("#remark_div").show();
          }
        }
        else {
          // alert(clickval);
          if (designation_id == 'Approver') {
            if (clickval == -100 || clickval == 85) {
              $("#cause_div").show();
              $("#remark_div").show();
            }
            else {
              $("#cause_div").hide();
              $("#remark_div").hide();
            }
          }
          $("#note_sc_st").hide();
          $("#dob_change_div").hide();
          $("#reason_order_div").hide();

        }
        $('#modalReject').modal();


      });
      $('#reject').click(function () {
        $('.verify_reject').text('Reject');
        $("#action_type").val(4);
        $('#modalReject').modal();
      });
      $('.modal-submitapprove').on('click', function (e) {
        e.preventDefault();

        $('#ReviveBen').submit();
        // var action_type = $('#action_type').val();
        // var designation_id = $("#designation_id").val();
        // var form_valid = 0;
        // if (action_type == 5) {
        //   var form_valid = 1;
        // }
        // else {
        //   if (designation_id == 'Verifier') {
        //     if ($.trim($('#reject_revert_cause').val()) == "") {
        //       error_reject_revert_cause = 'Please Select Cause';
        //       $('#error_reject_revert_cause').text(error_reject_revert_cause);
        //       $('#reject_revert_cause').addClass('has-error');
        //     }
        //     else {
        //       var form_valid = 1;
        //       error_reject_revert_cause = '';
        //       $('#error_reject_revert_cause').text(error_reject_revert_cause);
        //       $('#reject_revert_cause').removeClass('has-error');
        //     }
        //   }


        // }
        // if (designation_id == 'Approver') {
        //   if (action_type == -100 || action_type == 85) {
        //     if ($.trim($('#reject_revert_cause').val()) == "") {
        //       error_reject_revert_cause = 'Please Select Cause';
        //       $('#error_reject_revert_cause').text(error_reject_revert_cause);
        //       $('#reject_revert_cause').addClass('has-error');
        //     }
        //     else {
        //       var form_valid = 1;
        //       error_reject_revert_cause = '';
        //       $('#error_reject_revert_cause').text(error_reject_revert_cause);
        //       $('#reject_revert_cause').removeClass('has-error');
        //     }
        //   } else {
        //     var form_valid = 1;
        //   }
        // }
        // if (form_valid == 1) {
        //   $("#commonfield").append('<input type="hidden" id="new_aadhar_no" name="new_aadhar_no" value="' + $("#new_aadhar_no").val() + '" /> ');
        //   $("#commonfield").append('<input type="hidden" id="new_mobile_no" name="new_mobile_no" value="' + $("#new_mobile_no").val() + '" /> ');
        //   $("#commonfield").append('<input type="hidden" id="ration_card_cat" name="ration_card_cat" value="' + $("#ration_card_cat").val() + '" /> ');
        //   $("#commonfield").append('<input type="hidden" id="ration_card_no" name="ration_card_no" value="' + $("#ration_card_no").val() + '" /> ');
        //   $("#commonfield").append('<input type="hidden" id="epic_voter_id" name="epic_voter_id" value="' + $("#epic_voter_id").val() + '" /> ');
        //   $("#commonfield").append('<input type="hidden" id="caste_certificate_no" name="caste_certificate_no" value="' + $("#caste_certificate_no").val() + '" /> ');
        //   $("#commonfield").submit();
        // }


      });



      $('#encolser_modal').on('hidden.bs.modal', function (e) {
        $("#uploadForm #document_type").val('');
        $(".progress-bar").html('');

      });
      $('.confirmBtnCaste').click(function () {
        var clickval = $(this).val();
        var application_id = $("#commonfield #id").val();
        var scheme_id = $("#commonfield #scheme_id").val();
        window.location = "changeCastelb?scheme_id=" + scheme_id + "&id=" + application_id + "&type=" + clickval;
      });
    });

  </script>
</body>

</html>