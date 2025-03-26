<style type="text/css">
  .has-error
  {
    border-color:#cc0000;
    background-color:#ffff99;
  }
  .preloader1{
    position: fixed;
    top:40%;
    left: 52%;
    z-index: 999;
  }
  .preloader1 {
    background: transparent !important;
  }
  .loadingDivModal{
    position:absolute;
    top:0px;
    right:0px;
    width:100%;
    height:100%;
    background-color:#fff;
    background-image:url('images/ajaxgif.gif');
    background-repeat:no-repeat;
    background-position:center;
    z-index:10000000;
    opacity: 0.4;
    filter: alpha(opacity=40); /* For IE8 and earlier */
  }
</style>

<?php $__env->startSection('content'); ?>
  <div class="content-wrapper">
    <!-- <div class="preloader1"><img src="<?php echo e(asset('images/ZKZg.gif')); ?>" width="150px" id="loader_img"></div> -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Payee Beneficiary List
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
            <div class="panel-heading"><span id="panel-icon">Filter Here</div>
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
                  <!-- <form class="form-horizontal" role="form" method="POST" action="<?php echo e(route('search-by-name')); ?>" id="submit_form"> -->
                    <?php echo e(csrf_field()); ?>

                    <div class="row">
              
                
                 <div class="form-group col-md-4">
                 <label class="required-field">Scheme<span class="text-danger">*</label>
                 <select name="scheme_id" id="scheme_id" class="form-control" tabindex="6" >
                   <?php $__currentLoopData = $sceme_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($scheme->id); ?>"> <?php echo e($scheme->scheme_name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                 <span id="error_scheme_id" class="text-danger"></span>

                </div>

                <div class="form-group col-md-4">
                 <label class="required-field">Year<span class="text-danger">*</label>
                 <select name="lot_year" id="lot_year" class="form-control" tabindex="6" >
                  <option value="" selected>---Select Year---</option>
                  <?php $__currentLoopData = Config::get('constants.fin_year'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($key); ?>"><?php echo e($val); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                 <span id="error_lot_year" class="text-danger"></span>

                </div>

                <div class="form-group col-md-4">
                 <label class="required-field">Month<span class="text-danger">*</label>
                 <select name="lot_month" id="lot_month" class="form-control" tabindex="6" >
                    <option value="" selected>---Select Month---</option>
                    <?php $__currentLoopData = Config::get('constants.monthlist'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>"><?php echo e($month); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                 <span id="error_lot_month" class="text-danger"></span>

                </div>

              <?php if($district_visible): ?>
               <div class="form-group col-md-4">
                 <label class="">District</label>
                 <select name="district" id="district" class="form-control" tabindex="6" >
                  <option value="">--All  --</option>
                   <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($district->district_code); ?>"  <?php if(old('district')== $district->district_code): ?>  selected  <?php endif; ?>> <?php echo e($district->district_name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                 <span id="error_district" class="text-danger"></span>

                </div>
                <?php else: ?>
                <input type="hidden" name="district" id="district" value="<?php echo e($district_code_fk); ?>"/>
                <?php endif; ?>

                
                <?php if($is_urban_visible): ?>
              <div class="form-group col-md-4" id="divUrbanCode">
                <label class="">Rural/ Urban</label>
                
                <select name="urban_code" id="urban_code" class="form-control" tabindex="11" >
                  <option value="">--All  --</option>
                  <?php $__currentLoopData = Config::get('constants.rural_urban'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($key); ?>" <?php if( old('urban_code') == $key): ?>  selected  <?php endif; ?> ><?php echo e($val); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>     
                   
                </select>
                  <span id="error_urban_code" class="text-danger"></span>
              </div>
              <?php else: ?>
            <input type="hidden" name="urban_code" id="urban_code" value="<?php echo e($rural_urban_fk); ?>"/>

              <?php endif; ?>
          
               <?php if($block_visible): ?>
                <div class="form-group col-md-4" id="divBodyCode">
                <label class="" id="blk_sub_txt">Block/Sub Division.</label>
                
                <select name="block" id="block" class="form-control" tabindex="16" >
                  <option value="">--All --</option>
                  
                   
                </select>
                  <span id="error_block" class="text-danger"></span>
              </div>
               <?php else: ?>
              <input type="hidden" name="block" id="block" value="<?php echo e($block_munc_corp_code_fk); ?>"/>

               <?php endif; ?>
              
                <div class="form-group col-md-4" id="municipality_div" style="<?php echo e($municipality_visible?'':'display:none'); ?>">
                <label class="">Municipality</label>
                
                <select name="muncid" id="muncid" class="form-control" tabindex="16" >
                  <option value="">--All --</option>
                    <?php $__currentLoopData = $muncList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $munc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($munc->urban_body_code); ?>"> <?php echo e($munc->urban_body_name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                   
                </select>
                  <span id="error_muncid" class="text-danger"></span>
              </div>
               
                
            <div class="form-group col-md-4" id="gp_ward_div" style="<?php echo e($gp_ward_visible?'':'display:none'); ?>">
                <label class="" id="gp_ward_txt">GP/Ward</label>
                
                <select name="gp_ward" id="gp_ward" class="form-control" tabindex="17" >
                  <option value="">--All --</option>
                   <?php $__currentLoopData = $gpList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($gp->gram_panchyat_code); ?>"> <?php echo e($gp->gram_panchyat_name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                   
                </select>
                  <span id="error_gp_ward" class="text-danger"></span>
              </div>
               
            
               </div>
                    <div class="row">
                      <div class="col-md-12" align="center">
                        <button class="btn btn-primary" id="submit_btn" type="button" style="width: 200px;" disabled><i class="fa fa-search"></i> Search</button> &nbsp;&nbsp;&nbsp;&nbsp;
                        <button class="btn btn-info" id="excel_btn" type="button" style="width: 200px;"><i class="fa fa-file-excel-o"></i> Export All Data to Excel</button>
                      </div>
                    </div>
                  <!-- </form> -->
                </div>
              </div>
            </div>
          </div>
          
          <div id="res_div" style="display: none;">
            <div class="panel panel-default">
              <div class="panel-heading" id="panel_head">List of Beneficiary</div>
              <div class="panel-body" style="padding: 5px; font-size: 14px;">
                <div class="table-responsive">
                  <table id="example" class="table display" cellspacing="0" width="100%"> 
                    <thead style="font-size: 12px;">
                      <th>Sl No.</th>
                      <th>District</th>
                      <th>Block/Municipality</th> 
                      <th>Pension Id</th>
                      <th>Name</th>
                      <th>Month</th>
                      <th>Year</th> 
                      <th>Account No</th>
                      <th>IFSC</th>
                      <th>Payment Status</th>
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

  </div>
<?php $__env->stopSection(); ?>
<script src="<?php echo e(asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
<script src="<?php echo e(URL::asset('js/master-data-v2.js')); ?>"></script>
<script>
  $(document).ready(function(){
    // Live Clock
    var interval = setInterval(function () {
    var momentNow = moment();
      $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
      $('.time-part').html(momentNow.format('hh:mm:ss A'));
    }, 100);

    $('#loadingDiv').hide();
    $('#submit_btn').removeAttr('disabled');

    $('#excel_btn').click(function(){
      var scheme_id=$('#scheme_id').val();
      var lot_year=$('#lot_year').val();
      var lot_month=$('#lot_month').val();
      var district=$('#district').val();
      var urban_code=$('#urban_code').val();
      var block=$('#block').val();
       var gp_ward=$('#gp_ward').val();
       var muncid=$('#muncid').val();
      if (scheme_id == '' || lot_year == '' || lot_month == '') {
        alert('Scheme, Year and Month is mandatory for download excel.');
      } else {
        var  data= {'_token': '<?php echo e(csrf_token()); ?>', 'scheme_id': scheme_id, 'lot_year': lot_year, 'lot_month': lot_month, 'district': district,'urban_code':urban_code,'block':block,'gp_ward':gp_ward,'muncid':muncid };
        redirectPostExcel('<?php echo e(route("getPayeeListGetDataExcel")); ?>', data, 'post');
      }
      
    });

    /*============ Main Drop Down section =============*/
    $('#district').change(function() {
      var district=$(this).val();
      //alert(district);
        $('#urban_code').val('');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
    });

    $('#urban_code').change(function() {
       var urban_code=$(this).val();
        if(urban_code==''){
          $('#muncid').html('<option value="">--All --</option>'); 
        }
        $('#muncid').html('<option value="">--All --</option>'); 
        $('#block').html('<option value="">--All --</option>');
        $('#gp_ward').html('<option value="">--All --</option>');
        select_district_code= $('#district').val();
        if(select_district_code==''){
               alert('Please Select District First');
               $("#district").focus();
               $("#urban_code").val('');
        }
        else{
        select_body_type= urban_code;
        var htmlOption='<option value="">--All--</option>';
        $("#gp_ward_div").show();
        if(select_body_type==2){
            $("#blk_sub_txt").text('Block');
            $("#gp_ward_txt").text('GP');
            $("#municipality_div").hide();
            $.each(blocks, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        }else if(select_body_type==1){
            $("#blk_sub_txt").text('Subdivision');
            $("#gp_ward_txt").text('Ward');
            $("#municipality_div").show();
            $.each(subDistricts, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        } 
        else{
          $("#blk_sub_txt").text('Block/Subdivision');
        }   
        $('#block').html(htmlOption);
        }

    });
$('#block').change(function() {
      var block=$(this).val();
      var district=$("#district").val();
      var urban_code=$("#urban_code").val();
      if(district==''){
        $('#urban_code').val('');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
        alert('Please Select District First');
        $("#district").focus();
        
    }
    if(urban_code==''){
        alert('Please Select Rural/Urban First');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
        $("#urban_code").focus();
    }
    if(block!=''){
        var rural_urbanid= $('#urban_code').val();
      if(rural_urbanid==1){
       var sub_district_code=$(this).val();
       if(sub_district_code!=''){
        $('#muncid').html('<option value="">--All --</option>');
        select_district_code= $('#district').val();
        var htmlOption='<option value="">--All--</option>';
          $.each(ulbs, function (key, value) {
                if((value.district_code==select_district_code) && (value.sub_district_code==sub_district_code)){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        $('#muncid').html(htmlOption);
       }
       else{
          $('#muncid').html('<option value="">--All --</option>');
       }   
       } 
       else if(rural_urbanid==2){
          $('#muncid').html('<option value="">--All --</option>');
          $("#municipality_div").hide();
          var block_code=$(this).val();
          select_district_code= $('#district').val();

          var htmlOption='<option value="">--All--</option>';
          $.each(gps, function (key, value) {
                if((value.district_code==select_district_code) && (value.block_code==block_code)){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
          });
          $('#gp_ward').html(htmlOption);
          $("#gp_ward_div").show();


       }
       else{
          $('#muncid').html('<option value="">--All --</option>');
          $("#municipality_div").hide();
       } 
    }
    else{
        $('#muncid').html('<option value="">--All --</option>');
         $('#gp_ward').html('<option value="">--All --</option>');
    }
    
    });
$('#muncid').change(function() {
      var muncid=$(this).val();
      var district=$("#district").val();
      var urban_code=$("#urban_code").val();
      if(district==''){
        $('#urban_code').val('');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
        alert('Please Select District First');
        $("#district").focus();
        
    }
    if(urban_code==''){
        alert('Please Select Rural/Urban First');
        $('#block').html('<option value="">--All --</option>');
        $('#muncid').html('<option value="">--All --</option>'); 
        $("#urban_code").focus();
    }
    if(muncid!=''){
        var rural_urbanid= $('#urban_code').val();
      if(rural_urbanid==1){
       var municipality_code=$(this).val();
       if(municipality_code!=''){
        $('#gp_ward').html('<option value="">--All --</option>');
        var htmlOption='<option value="">--All--</option>';
          $.each(ulb_wards, function (key, value) {
                if(value.urban_body_code==municipality_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
        $('#gp_ward').html(htmlOption);
       }
       else{
          $('#gp_ward').html('<option value="">--All --</option>');
       }   
       } 
    
       else{
          $('#gp_ward').html('<option value="">--All --</option>');
          $("#gp_ward_div").hide();
       } 
    }
    else{
       $('#gp_ward').html('<option value="">--All --</option>');
    }
    
    });
    /*============= End Main Drop Down section ============*/

    error_scheme_id='';
    error_lot_month='';
    error_lot_year='';
    $('#submit_btn').click(function(){
      if($.trim($('#scheme_id').val()).length == 0){
        error_scheme_id = 'Scheme is required';
        $('#error_scheme_id').text(error_scheme_id);
      }
      else{
        error_scheme_id = '';
        $('#error_scheme_id').text(error_scheme_id);
      }

      if($.trim($('#lot_month').val()).length == 0){
        error_lot_month = 'Month is required';
        $('#error_lot_month').text(error_lot_month);
      }
      else{
        error_lot_month = '';
        $('#error_lot_month').text(error_lot_month);
      }

      if($.trim($('#lot_year').val()).length == 0){
        error_lot_year = 'Year is required';
        $('#error_lot_year').text(error_lot_year);
      }
      else{
        error_lot_year = '';
        $('#error_lot_year').text(error_lot_year);
      }

      if( error_scheme_id != '' || error_lot_year != '' || error_lot_month != ''){
        return false;
      }
      else{
        $('#loadingDiv').show();
        $('#res_div').show();
        var scheme_id=$('#scheme_id').val();
        var lot_year=$('#lot_year').val();
        var lot_month=$('#lot_month').val();
        var district=$('#district').val();
        var urban_code=$('#urban_code').val();
        var block=$('#block').val();
        var gp_ward=$('#gp_ward').val();
        var muncid=$('#muncid').val();

        if ( $.fn.DataTable.isDataTable('#example') ) {
          $('#example').DataTable().destroy();
        }
        var table=$('#example').DataTable( {
          dom: 'Blfrtip',
          "scrollX": true,
          "paging": true,
          "searchable": true,
          "ordering":false,
          "bFilter": true,
          "bInfo": true,
          "pageLength":100,
          'lengthMenu': [[100, 500, 1000, 2000], [100, 500, 1000, 2000]],
          "serverSide": true,
          "processing":true,
          "bRetrieve": true,
          "oLanguage": {
            "sProcessing": '<div class="preloader1" align="center"><span class="text-success" style="font-size: 20px; font-weight: bold;">Processing..</span></div>'
          },
          "ajax": 
          {
            url: "<?php echo e(route('getPayeeListGetData')); ?>",
            type: "post",
            data:function(d){
              d.scheme_id= scheme_id,
              d.lot_month= lot_month,
              d.lot_year= lot_year,
              d.district= district,
              d.urban_code= urban_code,
              d.block= block,
              d.gp_ward= gp_ward,
              d.muncid= muncid,
              d._token= '<?php echo e(csrf_token()); ?>'
            },
            error: function (jqXHR, textStatus, errorThrown) {
              $('#loadingDiv').hide();
              // ajax_error(jqXHR, textStatus, errorThrown);
            }
          },
          "initComplete":function(){
            $('#loadingDiv').hide();
            //console.log('Data rendered successfully');
          },
          "columns": [
            { "data": 'DT_RowIndex'},
            { "data": "district_name"},
            { "data": "block_ulb_name"},
            { "data": "ben_id"},
            { "data": "ben_name"},
            { "data": "lot_month"},
            { "data": "fin_year"},
            { "data": "account_no"},
            { "data": "ifsc"},
            { "data": "payment_status"}
          ],
      
          "buttons": [
            'excel'
          ],
        });
      }
    });
    
  });

  function redirectPostExcel(url, data , method = 'post'){
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

  function ajax_error(jqXHR, textStatus, errorThrown){
    var msg = "<strong>Failed to Load data.</strong><br/>";
    if (jqXHR.status !== 422 && jqXHR.status !== 400) {
      msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
    } 
    else {
      if (jqXHR.responseJSON.hasOwnProperty('exception')) {
        msg += "Exception: <strong>" + jqXHR.responseJSON.exception_message + "</strong>";
      } 
      else {
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