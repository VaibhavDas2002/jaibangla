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
    top:40%;
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
      color: #d9534f;
      content:'*';
      font-weight: bold;
      margin-left: 5px;
      float:right;
      margin-top: 5px;
  }
  #loadingDivModal{
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
  .disabledcontent {
    pointer-events: none;
    opacity: 0.4;
  }
  .has-error {
      border-color: #cc0000;
      background-color: #ffff99;
    }
    
</style>

<!--
  This is a starter template page. Use this page to start your new project from
  scratch. This page gets rid of all links and provides the needed markup only.
  -->
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>JB | Jai Bangla</title>
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset("frontend/img/favicon.ico")); ?>">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    <!-- Bootstrap 3.3.6 -->
    <link href="<?php echo e(asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css")); ?>" rel="stylesheet" type="text/css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
   
    <link href="<?php echo e(asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")); ?>" rel="stylesheet" type="text/css" />
   
    <link href="<?php echo e(asset("/bower_components/AdminLTE/dist/css/skins/_all-skins.min.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('css/app-template.css')); ?>" rel="stylesheet">
   
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

<script type="text/javascript">
  window.history.forward();
</script>
  <body class="hold-transition skin-blue sidebar-mini"  >
    <div class="wrapper">
    <!-- Main Header -->
    <?php echo $__env->make('layouts.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <!-- Sidebar -->
    <?php echo $__env->make('layouts.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Search Aadhaar Number
    </h1>

  </section>
  <section class="content">
    <div class="box box-default" id="full-content">
      <div class="box-body">
        <div class="panel panel-default">
         
          <div class="panel-body" style="padding: 5px;">
          
            <div class="row">
              <?php if( ($message = Session::get('success'))): ?>
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong><?php echo e($message); ?></strong>

              </div>
              <?php endif; ?>
              <?php if(count($errors) > 0): ?>
              <div class="col-md-12">
              <div class="alert alert-danger alert-block">
                <ul>
                  <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <li><strong> <?php echo e($error); ?></strong></li>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
              </div>
              </div>
              <?php endif; ?>
              <?php if( ($error = Session::get('error'))): ?>
               <div class="row">
               <div class="alert alert-danger alert-block" style="margin:10px 30px 10px 30px;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong><?php echo e($error); ?></strong>
        
              </div>
               </div>
              <?php endif; ?>
            </div>
             <form name="findAadhaar" id="findAadhaar" method="post" action="<?php echo e(url('findAadhaar')); ?>" onsubmit="return validate();" >
              <?php echo e(csrf_field()); ?>

            <div class="row">
              <div class="col-md-12">
               
                <div class="form-group col-md-3" id="beneficiary_id_div">
                  <label for="beneficiary"><span id="search_text">Aadhaar Number</span> <span class="text-danger">*</span></label>
                  <input type="text" name="aadhar_no" id="aadhar_no" class="form-control" 
                  onkeypress="if ( isNaN(String.fromCharCode(event.keyCode) )) return false;" 
                  placeholder="Enter Aadhaar Number" value="<?php echo e($fill_array['aadhar_no']); ?>" maxlength="12" autocomplete="off">
                   <span style="font-size: 14px;" id="error_aadhar_no" class="text-danger"></span>
                </div>
               
               
                <div class="form-group col-md-2" style="margin: 23px;">
                <input class="btn btn-success" type="submit" name="btnSubmit" value="Search">

                </div>
              </div>
            </div>
            </form>
          </div>
        </div>
          <?php if(!empty($errorMsg)): ?>
             
              <div class="alert alert-danger alert-block">
               <strong> <?php echo e($errorMsg); ?></strong></li>
                
              </div>
              
        <?php endif; ?>
        <br/>
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" style="margin:10px 20px 10px 20px;">
   <?php if(count($list_arr)>0): ?>
    <div class="panel-group wrap" id="bs-collapse">
    <?php 
      $p=1;
     ?>
     <?php $__currentLoopData = $list_arr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="panel panel-default" style="margin-bottom:20px;">
      <div class="panel-heading" role="tab" id="heading_<?php echo e($row['beneficiary_id']); ?>">
        <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_<?php echo e($row['beneficiary_id']); ?>" aria-expanded="true" aria-controls="collapse_<?php echo e($row['beneficiary_id']); ?>">
         Beneficiary Id:<?php echo e($row['beneficiary_id']); ?>

        </a>
      </h4>
      </div>
      <div id="collapse_<?php echo e($row['beneficiary_id']); ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_<?php echo e($row['beneficiary_id']); ?>">
        <div class="panel-body" >
        <table class="table table-bordered">  
           <tbody>
           <tr>       
           <th scope="row">Name</th>
           <td><?php echo e($row['ben_fullname']); ?></td>
           </tr>
          <tr>
           <th scope="row">Father Name</th>         
           <td><?php echo e($row['father_fullname']); ?></td>
           </tr>
           <tr>
           <th scope="row">Scheme Name</th>         
           <td><?php echo e($row['scheme_name']); ?></td>
           </tr>
           <tr>
           <th scope="row">Address</th>
           <td>District:<?php echo e($row['district_name']); ?>,Block/Municipality:<?php echo e($row['block_ulb_name']); ?>,GP/Ward:<?php echo e($row['gp_ward_name']); ?></td> 
           </tr>
          <tr>
           <th scope="row">DOB</th>
           <td><?php echo e($row['dob']); ?></td> 
           </tr>
          <tr>       
           <th scope="row">Current Application Status</th>
           <td><?php echo e($row['message']); ?></td>
           </tr>
            </tbody>
            </table>
        </div>
      </div>
    </div>
     <?php 
        $p++;
       ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
  
   
  </div>
        
      </div>
    </div>

  

  

  </section>
</div>
  <?php echo $__env->make('layouts.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
  
      <script src="<?php echo e(asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
      <script src="<?php echo e(asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js")); ?>" type="text/javascript"></script>

     <script src="<?php echo e(URL::asset('js/validateAdhar.js')); ?>"></script>
<script>
  $(document).ready(function() {   
    $('#loadingDiv').hide();
    $('.sidebar-menu li').removeClass('active');
    $('.sidebar-menu #lb-caste').addClass("active"); 
    $('.sidebar-menu #caste_search').addClass("active"); 
  
  });
function validate(){
 
     var error_aadhar_no =''; 
   

     if($.trim($('#aadhar_no').val()).length == 0)
     {
    error_aadhar_no = 'Aadhaar No. is required';
    $('#error_aadhar_no').text(error_aadhar_no);
    $('#aadhar_no').addClass('has-error');
     } 
    else{
      if($.trim($('#aadhar_no').val()).length != 12)
      {
     error_aadhar_no = 'Aadhaar No. should be 12 digit ';
     $('#error_aadhar_no').text(error_aadhar_no);
     $('#aadhar_no').addClass('has-error');
     }
     else
     {
        error_aadhar_no = '';
        $('#error_aadhar_no').text(error_aadhar_no);
        $('#aadhar_no').removeClass('has-error');
     }
  }
       if(error_aadhar_no =='') { 
        return true;
      }
      else {
         return false;
      }
      //return false;
}


</script>
</body>
</html>
