<?php $__env->startSection('content'); ?>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
    <?php
      // $pensioner_type='';
      // if($schemetype=='O')
      // $pensioner_type = '[ Type: ONLINE ]';
      // elseif($schemetype=='Q')
      // $pensioner_type = '[ Type: QUOTA ]';
    ?>
      <h1>
      <?php if(isset($scheme_name)): ?>
        Scheme Name: <?php echo e($scheme_name); ?> 
      <?php endif; ?>  
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-clock-o"></i><b> Date: </b></a></li>
        <li class="active"><span id='ct' ></span></li>
      </ol>
    </section>
    <?php echo $__env->yieldContent('action-content'); ?>
    <!-- /.content -->
  </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app-template-datatable', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>