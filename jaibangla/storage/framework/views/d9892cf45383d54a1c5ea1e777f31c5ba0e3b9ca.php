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
      Status on De-activated Payment
    </h1>
    <ol class="breadcrumb">
      <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
    </ol>
  </section>
  <section class="content">
    <div class="box box-default">
      <div class="box-body">
        <div id="loadingDiv"></div>
        <div class="panel panel-default">
          <div class="panel-heading" style="font-size: 15px; font-weight: bold; font-style: italic;"><span id="panel-icon">Enter Filter Criteria</div>
          <div class="panel-body" style="padding: 5px;">
            <div class="row">
              <div class="col-md-12">
                <?php if(($message = Session::get('success')) ): ?>
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
                <?php if(($message = Session::get('msg1'))): ?>
                <div class="alert alert-danger alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong><?php echo e($message); ?></strong>
                </div>
                <?php endif; ?>
                <div class="row">
                  <div class="col-md-12" style="margin-bottom: 10px;">
                    <div class="col-md-4">
                      <label class=" control-label">Scheme <span class="text-danger">*</span></label>
                      <select class="form-control" name="scheme_id" id='scheme_id' required>
                        <option value="">--Select Scheme--</option>
                        <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($scheme->id); ?>"><?php echo e($scheme->scheme_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                      <span class="text-danger" id="error_scheme_id"></span>
                    </div>
                    <div class="col-md-4">
                      <label class=" control-label">Financial Year <span class="text-danger">*</span></label>
                      <select class="form-control" name="fin_year" id='fin_year' required>
                        <option value="">--Select Financial Year--</option>
                        <?php $__currentLoopData = Config::get('constants.fin_year'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($val); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                      <span class="text-danger" id="error_fin_year"></span>
                    </div>
                    

                  <div class="col-md-4" style="margin-top: 24px;">
                    <button class="btn btn-primary" name="submit_btn" id="submit_btn" type="button" disabled><i class="fa fa-search"></i> Search</button>&nbsp;
                    
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="alert print-error-msg" style="display:none;" id="errorDiv">
        <button type="button" class="close" aria-label="Close" onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
        <ul></ul>
      </div>

      <div id="search_details" style="display:none;">
        <div class="panel panel-default">
          <div class="panel-heading" id="heading_msg" style="font-size: 15px; font-weight: bold; font-style: italic;">List of Beneficiary</div>
          <div class="panel-body" style="padding: 5px; font-size: 14px;">

            <div class="table-responsive">
              <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%"  style="font-size: 14px;">
                <thead>
                      <th>Sl No</th>
                  <th id="location_id">District Name</th>
                  <th>April</th>
                  <th>May</th>
                  <th>June</th>
                  <th>July</th>
                  <th>August</th>
                  <th>September</th>
                  <th>October</th>
                  <th>November</th>
                  <th>December</th>
                  <th>January</th>
                  <th>February</th>
                  <th>March</th>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <tr>
                  <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                </tfoot>
              </table>
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
<script src="<?php echo e(asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
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
    $('#submit_btn').removeAttr('disabled');
    $('#reset_btn').removeAttr('disabled');

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
              htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
            }
          });
        } else if (select_body_type == 1) {
          $("#blk_sub_txt").text('Subdivision');
          $("#gp_ward_txt").text('Ward');
          $("#municipality_div").show();
          $.each(subDistricts, function(key, value) {
            if (value.district_code == select_district_code) {
              htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
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
              if ((value.district_code == select_district_code) && (value.sub_district_code == sub_district_code)) {
                htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
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
            if ((value.district_code == select_district_code) && (value.block_code == block_code)) {
              htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
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
                htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
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

    var error_fin_year = '';
    var error_scheme_id = '';
    $('#submit_btn').click(function() {
      if ($.trim($('#fin_year').val()).length == 0) {
        error_fin_year = 'Financial year is required';
        $('#error_fin_year').text(error_fin_year);
      } else {
        error_fin_year = '';
        $('#error_fin_year').text(error_fin_year);
      }

      if ($.trim($('#scheme_id').val()).length == 0) {
        error_scheme_id = 'Scheme name is required';
        $('#error_scheme_id').text(error_scheme_id);
      } else {
        error_scheme_id = '';
        $('#error_scheme_id').text(error_scheme_id);
      }

      if (error_fin_year != '' || error_scheme_id != '') {
        return false;
      } else {
        loadDataTable();
      }
    });

  });

  function loadDataTable() {
    var scheme_code = $('#scheme_id').val();
    var district = $('#district').val();
    var urban_code = $('#urban_code').val();
    var block = $('#block').val();
    var gp_ward = $('#gp_ward').val();
    var muncid = $('#muncid').val();
    var fin_year = $('#fin_year').val();

    $("#loadingDiv").show();
    $.ajax({
      type: 'post',
      dataType: 'json',
      url: "<?php echo e(url('linelisting-duplicate-stop-report')); ?>",
      data: {
        scheme_id: scheme_code,
        district: district,
        urban_code: urban_code,
        block: block,
        gp_ward: gp_ward,
        muncid: muncid,
        fin_year: fin_year,
        _token: '<?php echo e(csrf_token()); ?>',
      },
      success: function(data) {
        // console.log(JSON.stringify(data));
        $('#loadingDiv').hide();
        if (data.return_status) {
          var yearArr = fin_year.split('-');
          $('#search_details').show();
          $("#heading_msg").html(data.heading_msg);
          
          if ($.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable().destroy();
          }
          var table_head = $("#example > thead").html("");
          table_head.append("<tr><th>Sl No</th><th id='location_id'>District Name</th><th>April-" + yearArr[0] + "</th><th>May-" + yearArr[0] + "</th><th>June-" + yearArr[0] + "</th><th>July-" + yearArr[0] + "</th><th>August-" + yearArr[0] + "</th><th>September-" + yearArr[0] + "</th><th>October-" + yearArr[0] + "</th><th>November-" + yearArr[0] + "</th><th>December-" + yearArr[0] + "</th><th>January-" + yearArr[1] + "</th><th>February-" + yearArr[1] + "</th><th>March-" + yearArr[1] + "</th></tr>");
          $("#location_id").text(data.column);
          $("#example > tbody").html("");
          var table = $("#example tbody");
          var slno = 1;
          $.each(data.row_data, function(i, item) {
            var total_01 = isNaN(parseInt(item.month_01)) ? 0 : parseInt(item.month_01);
            var total_02 = isNaN(parseInt(item.month_02)) ? 0 : parseInt(item.month_02);
            var total_03 = isNaN(parseInt(item.month_03)) ? 0 : parseInt(item.month_03);
            var total_04 = isNaN(parseInt(item.month_04)) ? 0 : parseInt(item.month_04);
            var total_05 = isNaN(parseInt(item.month_05)) ? 0 : parseInt(item.month_05);
            var total_06 = isNaN(parseInt(item.month_06)) ? 0 : parseInt(item.month_06);
            var total_07 = isNaN(parseInt(item.month_07)) ? 0 : parseInt(item.month_07);
            var total_08 = isNaN(parseInt(item.month_08)) ? 0 : parseInt(item.month_08);
            var total_09 = isNaN(parseInt(item.month_09)) ? 0 : parseInt(item.month_09);
            var total_10 = isNaN(parseInt(item.month_10)) ? 0 : parseInt(item.month_10);
            var total_11 = isNaN(parseInt(item.month_11)) ? 0 : parseInt(item.month_11);
            var total_12 = isNaN(parseInt(item.month_12)) ? 0 : parseInt(item.month_12);

            table.append("<tr><td>" + (i + 1) + "</td><td>" + item.location_name + "</td><td>" + total_04 + "</td><td>" + total_05 + "</td><td>" + total_06 + "</td><td>" + total_07 + "</td><td>" + total_08 + "</td><td>" + total_09 + "</td><td>" + total_10 + "</td><td>" + total_11 + "</td><td>" + total_12 + "</td><td>" + total_01 + "</td><td>" + total_02 + "</td><td>" + total_03 + "</td></tr>");
            //slno++;

          });


          //$('#example tbody').empty();
          $("#example").show();
          $('#example').dataTable({
    "paging": false,
    "ordering": false,
    // "searching": false,
    "info": false,
    "scrollX": true,
    "dom": 'Bfrtip',
    "buttons": [
      // 'copy',
      {
        extend: 'excel',
        text: 'Export To Excel',
        className: "btn btn-info",
        footer: true,
        title: data.title,
        messageTop: data.heading_msg
      }
      // {
      //     extend: 'pdf',
      //     title: data.title,
      //     footer: true ,
      //     messageTop: data.heading_msg
      // }

    ],
    "footerCallback": function(row, data, start, end, display) {
      var api = this.api(),
        data;

      // converting to interger to find total
      var intVal = function(i) {
        return typeof i === 'string' ?
          i.replace(/[\$,]/g, '') * 1 :
          typeof i === 'number' ?
          i : 0;
      };

      // computing column Total of the complete result 
      var fotter_2 = api
        .column(2)
        .data()
        .reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);

      var fotter_3 = api
        .column(3)
        .data()
        .reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
      var fotter_4 = api
        .column(4)
        .data()
        .reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);

      var fotter_5 = api
        .column(5)
        .data()
        .reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);

      var fotter_6 = api
        .column(6)
        .data()
        .reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
      var fotter_7 = api
        .column(7)
        .data()
        .reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
      var fotter_8 = api
        .column(8)
        .data()
        .reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
      var fotter_9 = api
        .column(9)
        .data()
        .reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
      var fotter_10 = api
        .column(10)
        .data()
        .reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
      var fotter_11 = api
        .column(11)
        .data()
        .reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
      var fotter_12 = api
        .column(12)
        .data()
        .reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);
      var fotter_13 = api
        .column(13)
        .data()
        .reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);

      // Update footer by showing the total with the reference of the column index 
      $(api.column(0).footer()).html('');
      $(api.column(1).footer()).html('Total');
      $(api.column(2).footer()).html(fotter_2);
      $(api.column(3).footer()).html(fotter_3);
      $(api.column(4).footer()).html(fotter_4);
      $(api.column(5).footer()).html(fotter_5);
      $(api.column(6).footer()).html(fotter_6);
      $(api.column(7).footer()).html(fotter_7);
      $(api.column(8).footer()).html(fotter_8);
      $(api.column(9).footer()).html(fotter_9);
      $(api.column(10).footer()).html(fotter_10);
      $(api.column(11).footer()).html(fotter_11);
      $(api.column(12).footer()).html(fotter_12);
      $(api.column(13).footer()).html(fotter_13);

    }
  });
          
          $('.buttons-excel').removeClass('dt-button');
        } else {
          $('#search_details').hide();
          $("#example").hide();
          printMsg(data.return_msg, '0', 'errorDiv');
        }
        $("#submit_loader1").hide();
        $("#submitting").show();

      },
      error: function(jqXHR, textStatus, errorThrown) {
        $("#submit_loader1").hide();
        $('#loadingDiv').hide();
        $("#submitting").show();
        
        $.alert({
          title: 'Error!!',
          type: 'red',
          icon: 'fa fa-warning',
          content: 'Something wrong..may be session timeout. please logout and then login again',
        });
        //  location.reload();
        // ajax_error(jqXHR, textStatus, errorThrown)
      }
    });

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
</script>
<?php echo $__env->make('layouts.app-template-datatable_new', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>