  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo e(asset("/bower_components/AdminLTE/dist/img/user2-160x160.jpg")); ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info" >
          <p ><?php echo e(Auth::user()->username); ?>  </p>
          <!-- Status -->
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>

      <!-- search form (Optional) -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu">
        <!-- Optionally, you can add icons to the links -->
        <li class="active"><a href="<?php echo e(url('/backendlogin')); ?>"><i class="fa fa-link"></i> <span>Dashboard</span></a></li>
        <?php 
        $isLifeCertificate=0;
        $incDetailsEntry=0;
        $special_cases=0;
        $marking_ds_8=0;
        $user_id = Auth::user()->id;
        $mapObj = DB::table('public.duty_assignement')->where('user_id',$user_id)->first();
         ?>
        <?php if($mapObj->district_code == 315 && $mapObj->urban_body_code == 99999 && Auth::user()->designation_id == 'Operator'): ?> 
          <li><a href="<?php echo e(route('ben-payment-status')); ?>"><i class="fa fa-link"></i> <span>Beneficiary Payment Status</span></a></li>
        <?php endif; ?>

        <?php if($mapObj->district_code == 304 && Auth::user()->designation_id == 'Approver' && $user_id == 3606): ?> 
          <li><a href="<?php echo e(url('inactive-special')); ?>"><i class="fa fa-link"></i> <span>Re-activate Beneficiary</span></a></li>
        <?php endif; ?>
        
        <?php 
        if($mapObj->district_code == 315 && (Auth::user()->designation_id == 'Verifier' || Auth::user()->designation_id == 'Approver')) {
         $special_cases=1;
        }
        if($mapObj->district_code == 315 && (Auth::user()->designation_id == 'Verifier')) {
         $marking_ds_8=1;
        }
        $designation_id = Auth::user()->designation_id;
        if($designation_id=='Operator'){
          $district_code=$mapObj->district_code;
          if($mapObj->is_urban==1){
            $block_ulb_code=$mapObj->urban_body_code;
          }
          else{
            $block_ulb_code=$mapObj->taluka_code;
          }
          $specialEntry = DB::table('public.m_block_urban_entry_mapping')->where('special_entry',TRUE)->where('district_code',$district_code)->where('block_ulb_code',$block_ulb_code)->where('is_urban',$mapObj->is_urban)->count();
        }
        else{
          $specialEntry =0;
        }
        $mapObjSchemeList = DB::table('public.duty_assignement')->where('is_active',1)->where('user_id',$user_id)->pluck('scheme_id')->toarray();
        if(in_array(17,$mapObjSchemeList) && ($designation_id=='Operator' || $designation_id=='Verifier') ){
          $isLifeCertificate=1;
        }
         ?>
        <?php 
       if((in_array(17,$mapObjSchemeList) || in_array(8,$mapObjSchemeList) || in_array(9,$mapObjSchemeList)) && ($designation_id=='Approver') ){
          $incDetailsEntry=1;
        }
         ?>
        <?php if(Storage::exists('menu/'.$designation_id.".json")): ?>
        <?php 
        $menu_contents =json_decode(Storage::disk('local')->get('menu/'.$designation_id.'.json'),JSON_FORCE_OBJECT);
         ?> 
        
        <?php $__currentLoopData = $menu_contents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mymenu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($mymenu['menu_name']=='Life Certificate' && $isLifeCertificate==0): ?>
        <?php continue; ?>;
        <?php endif; ?>
        <?php if($mymenu['menu_name']=='Beneficiary Details Entry' && $incDetailsEntry==0): ?>
        <?php continue; ?>;
        <?php endif; ?>
        <?php if($mymenu['id']==314 && $special_cases==0): ?>
        <?php continue; ?>;
        <?php endif; ?>
        <?php if(empty($mymenu['child_menu'])): ?>
        <?php if($mymenu['menu_name']=='Jai Bangla Form(Special Quota)'): ?>
        <?php if($specialEntry==0): ?>
        <?php continue; ?>;
        <?php endif; ?>
        <?php endif; ?>
      
         <li><a href="<?php echo e($mymenu['url_type'] == '2' ? 'route' (''.$mymenu['link_url']) : 'url' (''.$mymenu['link_url'])); ?>" ><i class="<?php echo e($mymenu['icon']); ?>"></i> <span><?php echo e($mymenu['menu_name']); ?></span></a></li> 
        <?php else: ?> 
        <li class="treeview">
          <a href="<?php echo e($mymenu['link_url']); ?>"><i class="<?php echo e($mymenu['icon']); ?>"></i> <span>
            
            <?php echo e($mymenu['menu_name']); ?>

           
          </span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          <?php $__currentLoopData = $mymenu['child_menu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mysubmenu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php if($mysubmenu['menu_name']=='Mark as Duare Sarkar VII'): ?>
          <?php if($marking_ds_8==0): ?>
          <?php continue; ?>;
          <?php endif; ?>
          <?php endif; ?>
          <li><a href="<?php echo e($mysubmenu['url_type'] == 2 ? 'route' (''.$mysubmenu['link_url']) : 'url' (''.$mysubmenu['link_url'])); ?>" ><i class="<?php echo e($mysubmenu['icon']); ?>"></i> <span><?php echo e($mysubmenu['menu_name']); ?></span></a></li> 
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
          </ul>

        </li>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>   
        <?php endif; ?>
        </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>