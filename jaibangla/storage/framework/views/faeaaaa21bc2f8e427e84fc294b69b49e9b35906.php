<style type="text/css">
.requied{
  color:red;
}
.hasError {
  border: 2px solid red;
  border-radius: 4px;
}
#menu_role_mapping_panel{
  margin-left:250px;
  margin-top: 120px;
}
.modal-full {
    min-width: 80%;
  
}
#duty_add #btnaddrole{
  margin-top:25px;
  margin-left:10px;
}
.row{
  margin-right: 10px!important;
  margin-left: 60px!important;
  margin-top: 1%!important;
}
.form-control{
 width:95%!important;
}
#btnSearch{
  margin-top:20px;
  margin-left:10px;
}
#bulkSearch{
  margin-top:25px;
  margin-left:10px;
}
#btnaddrole{
  margin-top:25px;
  margin-left:10px;
}
#duty_add{
  margin-top:25px;
  margin-left:10px;
}
#excel-btn{
  margin-bottom:20px;
  display:none;

}
.blueColor{
  color:blue;
  font-weight:bold;

}
.redColor{
  color:red;
  font-weight:bold;

}
</style>

<?php $__env->startSection('action-content'); ?>
  <section class="content">
    <div class="box">
      <div class="box-header">
        <div class="row">
            <div class="col-sm-8"></div>
        </div>
      </div>
      <div class="box-body">
      <?php if( ($message = Session::get('success'))): ?>
            <div class="row msg-div" >
              <div class="alert alert-success alert-block" style="margin:10px 30px 10px 30px;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong><?php echo e($message); ?></strong>

              </div>
            </div>
            <?php endif; ?>
        <?php if(count($errors) > 0): ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="closeError('crud_msg_Crud1')">
    <span aria-hidden="true">&times;</span>
  </button>
        <div class="alert alert-danger alert-block" id="crud_msg_Crud1">
          <ul>
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><strong> <?php echo e($error); ?></strong></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
        <?php endif; ?>
        <div class="panel-group">
          <div class="panel panel-default">
           
            <div id="scheme_workflow" class="panel-collapse collapse show">
              <div class="panel-body" id="level_map"> <!-- hidden by default-->
                <div id="example2_wrapper" class="col-md-12 dataTables_wrapper form-inline dt-bootstrap js-report-form">
                <div class="alert print-error-msg" style="display:none" id="crud_msg_Crud">
                <button type="button" class="close"  aria-label="Close" onclick="closeError('crud_msg_Crud')"><span aria-hidden="true">&times;</span></button>
                <ul></ul></div>
                <div class="col-md-12 pull-right" id="addButton">

                  
                
                   <?php if($designation_id=='Admin' || $designation_id=='HOD' || $designation_id=='Verifier' || $designation_id=='Approver' || $designation_id=='Delegated Approver'): ?>
                  <a class="btn btn-primary" href="<?php echo e(route('adduser')); ?>">Add User and Assign Role</a>
                   <?php endif; ?>
                 
                 
                 
                </div>
                <hr/>
                <hr/>
                <form id="Searchmodal" class="form-inline">
                  
                    
                 
                  
                <?php if($mapping_visible): ?>
                 
                      <div class="form-group col-md-3">
                             <label class="control-label">Mapping Level </label>
                           
                                <select class="form-control" name="stake_level_home" id="stake_level_home">
                                <option value="">--ALL--</option>
                                <?php $__currentLoopData = $user_levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stake): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                   <option value="<?php echo e($stake->stake_code); ?>" ><?php echo e($stake->stake_name); ?></option>
                                   <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>       
                                </select>
                                
                           
                        </div>
                 
               <?php else: ?>
              <input type="hidden" name="stake_level_home" id="stake_level_home" value="<?php echo e($stake_level_home); ?>"/>
              <?php endif; ?>
              <?php if($role_visible): ?>
                    <div class="form-group col-md-3" id="designation_id_home_div">
                            <label class="control-label">Role </label>
                            
                                <select class="form-control" name="designation_id_home" id="designation_id_home" >
                                <option value="">--ALL--</option>
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                   <option value="<?php echo e($role->name); ?>" ><?php echo e($role->name); ?></option>
                                   <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>     
                                </select>
                                <div id="designation_id_home_ajax"></div>
                           
                 
                  </div>
                  <?php else: ?>
              <input type="hidden" name="designation_id_home" id="designation_id_home" value="<?php echo e($designation_id_home); ?>"/>
              <?php endif; ?> 
              <div class="form-group col-sm-3">
                  <label for="l_scheme_duty" class="">Scheme</label>
                  <select  style="width:200px;" id="scheme_home" class="form-control" name="scheme_home">
                  <option value="">--ALL--</option>
                <?php $__currentLoopData = $schemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scheme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($scheme->id); ?>"><?php echo e($scheme->scheme_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                 </select>
               
             </div>
             <?php if($district_visible): ?>
              <div class="form-group col-sm-3" id="district_code_home_div">
              <label for="l_district_code_home" class="">District</label>
              <select  id="district_code_home" style="width:200px;" class="form-control">
              <option value="">--ALL-</option>
                <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($district->district_code); ?>"> <?php echo e($district->district_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <div id="district_code_home_ajax"></div>
              </div>
              <?php else: ?>
              <input type="hidden" name="district_code_home" id="district_code_home" value="<?php echo e($district_code); ?>"/>
              <?php endif; ?>
              <?php if($is_urban_visible): ?>
              <div class="form-group col-sm-3" id="is_urban_home_div">
              <label for="l_is_urban_home" class="">Rural/Urban</label>
              <select  id="is_urban_home" style="width:200px;" class="form-control" name="is_urban_home">
                <option value="">--All--</option>
                <?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>" > <?php echo e($value); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <div id="is_urban_home_ajax"></div>
              </div>
              <?php endif; ?>
             
             <?php if($block_visible): ?>
             <div class="form-group col-sm-3" id="block_code_home_div">
              <label for="l_block_code_home" class="" id="blk_sub_txt">Block/Sub Div</label>
              <select  id="block_code_home" style="width:200px;" class="form-control" name="block_code_home" >
              <option value="">--ALL--</option>
               
              </select>
              <div id="block_code_home_ajax"></div>
             </div>
             <?php endif; ?>
            <button type="button" id="btnSearch" class="btn btn-primary">Search</button>
                 
             </div>
                  
                 
                 </form>
                    
                  <div class="col-md-12 text-center" id="loaderdiv" hidden>
                    <img src="<?php echo e(asset('images/ZKZg.gif')); ?>" width="100px" height="100px"/>
                  </div>  
                  
               <div class="col-md-12 text-center">
               <p clas="text-primary text-center" style="color: #000000; font-size: 16px;"><?php echo e($heading); ?></p>
                </div>
               
                    <table id="example" class="display" cellspacing="0" width="100%">
                      <input type="hidden" name="_token" id="token" value="<?php echo e(csrf_token()); ?>">
                      <thead>
                        <tr role="row">
                          
                          <th>ID</th>  
                          <th>Status</th>  
                          <th>CanUpdate</th>  
                          <th width="10%">Display Name</th>  
                          <th width="7%">Role</th>   
                          <th width="7%">Mobile Number</th>
                          <th width="7%">Email</th>
                          <th width="25%">Location</th> 
                          <th width="25%">Schemes</th>     
                         
                          <th width="6%" >User Active?</th>
                          <th width="6%" >Action</th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr>
                        
                           <th>ID</th>  
                          <th>Status</th> 
                          <th>CanUpdate</th>  
                          <th width="10%">Display Name</th> 
                          <th width="7%">Role</th> 
                          <th width="7%">Mobile Number</th> 
                          <th width="7%">Email</th>
                          <th width="25%">Location</th>
                          <th width="25%">Schemes</th>       
                          <th width="6%" >User Active?</th>
                          <th width="6%" >Action</th>
                        </tr>
                      </tfoot>     
                    </table>  
                    <div class="row">
                      <div class="col-sm-7">
                        <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                          
                        </div>
                      </div>
                    </div>  
                  </div>
                </div>
              </div>
              <!-- <div class="panel-footer">Panel Footer</div> -->
            </div>
          </div>
      
        </div>        
      </div>
    </div>

 		<!-- Start User Active/Deactive Modal -->

     <div class="modal fade" id="ben_view_modal" tabindex="-1">
			<div class="modal-dialog ">
				<div class="modal-content">
        <form method="POST" role="form" id="modal_form" action="<?php echo e(route('userDutymanagement/toggleActivate')); ?>">
        <input type="hidden" name="_token" id="token" value="<?php echo e(csrf_token()); ?>">
        <input type="hidden" name="modal_user_id" id="modal_user_id" value="">
					<div class="modal-header btn-danger">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Change User Status(Active and Deactivate from all schemes</h4>
					</div>
					<div class="modal-body">
						
          <span id="error_same" class="text-danger"></span><br/>
						<table style="width:100%">
							<tr>
								<td style="width:30%;"><span class="item_header">Display Name:</span></td>
								<td><span class="item_value" id="modal_username"></span></td>
							</tr>
							<tr>
								<td><span class="item_header">Present Location:</span></td>
								<td><span class="item_value" id="modal_location"></span></td>
							</tr>
              
              <tr>
								<td><span class="item_header">Present Status:</span></td>
								<td><span class="item_value blueColor" id="modal_pre_status"></span></td>
							</tr>
              <tr id="userActiveNew">
								<td><span class="item_header">New Status:</span></td>
								<td><span class="item_value redColor" id="modal_new_status"></span></td>
							</tr>
              <tr id="userActiveMsg">
								<td><span class="item_header">Note:</span></td>
								<td><span class="item_value redColor">User can not be deactivated as schemes of other departments are assigned.</span></td>
							</tr>
					
							
						</table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-danger" id="change_button">Change Status</button>
            <button type="button"  id="submitting" value="Submit" class="btn btn-success success btn-lg" disabled >Submitting please wait</button>

					</div>
         </form>
				</div>
			</div>
		</div>
		<!-- End Active/Deactive Modal Model -->

<!--Update User Modal-->
<div id="UserformModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-full">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><span class="crud-txt">Add User</span></h4>
      </div>
      <div class="modal-body">
        <div class="" ><img src="<?php echo e(asset('images/ZKZg.gif')); ?>" class="submit_loader" width="50px" height="50px"  ></div>
        <div class="alert print-error-msg" style="display:none" id="crud_msg_CrudModal">
        <button type="button" class="close"  aria-label="Close" onclick="closeError('crud_msg_CrudModal')"><span aria-hidden="true">&times;</span></button><ul></ul></div>
        <form id="userform" class="form-horizontal">
                        
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="must_role_adduser" id="must_role_adduser" value="1">  
             <div class="row">
                     <div class="form-group col-md-4">
                            <label for="full_name" class="control-label">Full Name <span class="requied">*</span></label>

                            
                                <input id="full_name" type="text" class="form-control txtOnly" name="full_name" value=""  >

                               
                           
                      </div>
                        
                      
              </div>  
              <div class="row">
                <div class="form-group col-md-4">
                       <label for="full_name_as_in_aadhar" class="control-label">Full Name as in Aadhaar <span class="requied">*</span></label>

                       
                           <input id="full_name_as_in_aadhar" type="text" class="form-control txtOnly" name="full_name_as_in_aadhar" value=""  >

                          
                      
                 </div>
                   
                   
           </div>          
              <div class="row">   
                        <div class="form-group col-md-4">
                            <label for="address" class="control-label">Office Address</label>

                           
                                <input id="address" type="text" class="form-control special-char" name="address" value="" >

                              
                            
                        </div>
                     
                       
                        
                               


                        

                        <div class="form-group col-md-4">
                            <label for="username" class="control-label">Display Name <span class="requied">*</span></label>

                           
                                <input id="username" type="text" class="form-control" name="username" value=""  >

                                
                           
                        </div>

                        <div class="form-group col-md-4">
                            <label for="email" class="control-label">E-Mail Address <span class="requied">*</span></label>

                           
                                <input id="email" type="text" class="form-control" name="email" value="">

                               
                            
                        </div>
                     </div>
                     <div class="row">
                         <div class="form-group col-md-4">
                            <label for="mobile_no" class="control-label">Mobile Number <span class="requied">*</span></label>

                            
                                <input id="mobile_no"  type="text" class="form-control NumOnly" name="mobile_no" value="" maxlength="10">

                               
                           
                        </div>
                        
                       
                       
                    
                        
                  
                      
                       </div>
                       
                       
       
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn-submit" >
        <span class="crud-txt">Add</span>
        </button>
         <img  style="display:none;" src="<?php echo e(asset('images/ZKZg.gif')); ?>" id="btn_addEdit_loader" width="150px"
                            height="150px">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!--End of Update User  Modal-->
 		<!-- Start Toggle Duty Modal-->

     <div class="modal fade" id="ben_duty_modal" tabindex="-1">
			<div class="modal-dialog ">
				<div class="modal-content">
        <form method="POST" role="form" id="modal_duty_form" action="<?php echo e(route('userDutymanagement/toggleDuty')); ?>">
        <input type="hidden" name="_token" id="token" value="<?php echo e(csrf_token()); ?>">
        <input type="hidden" name="modal_duty_id" id="modal_duty_id" value="">
        <input type="hidden" name="modal_duty_user_id" id="modal_duty_user_id" value="">
        <input type="hidden" name="modal_duty_scheme_id" id="modal_duty_scheme_id" value="">
					<div class="modal-header btn-danger">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Change Duty Assigment Status</h4>
					</div>
					<div class="modal-body">
						
          <span id="error_same" class="text-danger"></span><br/>
						<table style="width:100%">
							<tr>
								<td style="width:30%;"><span class="item_header">Display Name:</span></td>
								<td><span class="item_value" id="modal_duty_username"></span></td>
							</tr>
							<tr>
								<td><span class="item_header">Present Location:</span></td>
								<td><span class="item_value" id="modal_duty_location"></span></td>
							</tr>
             
              <tr>
								<td><span class="item_header">Actionable Scheme:</span></td>
								<td><span class="item_value" id="modal_duty_scheme"></span></td>
							</tr>
              <tr>
								<td><span class="item_header">Present Status:</span></td>
								<td><span class="item_value blueColor" id="modal_duty_pre_status"></span></td>
							</tr>
              <tr>
								<td><span class="item_header">New Status:</span></td>
								<td><span class="item_value redColor" id="modal_duty_new_status"></span></td>
							</tr>
              
					
							
						</table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-danger" id="change_duty">Change Duty Status</button>
            <button type="button"  id="submitting-duty" value="Submit" class="btn btn-success success btn-lg" disabled >Submitting please wait</button>

					</div>
         </form>
				</div>
			</div>
		</div>
		<!-- End Toggle Duty Modal-->


<script src="<?php echo e(asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js")); ?>"></script>
<script src="<?php echo e(asset("js/select2.full.min.js")); ?>"></script>
<script src="<?php echo e(URL::asset('js/master-data-v2.js')); ?>"></script>

<script type="text/javascript"> 
 
var table=""; 
var listItemtable = "";
var myTable = "";
var sessiontimeoutmessage='<?php echo e($sessiontimeoutmessage); ?>';
var base_url='<?php echo e(url('/')); ?>';
//console.log(sessiontimeoutmessage);
$(document).ready(function(){ 
  var table=""; 
  var listItemtable = "";
  var myTable = "";
  $("#submitting").hide();
  $("#submitting-duty").hide();
  $("#submitting-mapnewscheme").hide();
  $(".btnMap1").hide();
  if(table!=null && table != ''){
    $('#example').DataTable().destroy();
    //alert(service_designation_id);
  }
  $("#excel-btn").hide();
   $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>"
      }
    });
    table=$('#example').DataTable( {
      "paging": true,
      "pageLength":10,
      "lengthMenu": [[10,20, 50, 80, 120, 150, 180, 500,1000, 2000], [10,20, 50, 80, 120, 150, 180, 500,1000, 2000]],
      "serverSide": true,
      "deferRender": true,
      "processing":true,
      "bRetrieve": true,
      "ordering":false,
      "searching": true,
      "language": {
        "processing": '<img src="<?php echo e(asset('images/ZKZg.gif')); ?>" width="150px" height="150px"/>'
      },
      "ajax": {
        url: "<?php echo e(url('userDutymanagement/Search')); ?>",
        type: "GET",
         data   : function( d ) {
          d.mapping_level= $('#stake_level_home').val();
          d.designation_id= $('#designation_id_home').val();
          d.scheme_id= $('#scheme_home').val();
          d.district_code= $('#district_code_home').val();
          d.is_urban= $('#is_urban_home').val();
          d.block_code= $('#block_code_home').val();
       },
       error: function (jqXHR, textStatus, errorThrown) {
         alert(sessiontimeoutmessage);
         //location.reload();
          window.location.href=base_url;
        }
       
      } ,
      "columns": [
        
        { "data": "id","defaultContent":"" },
        { "data": "is_active_db","defaultContent":"" },
        { "data": "CanUpdate","defaultContent":"" },
        { "data": "username","defaultContent":"" },
        { "data": "designation_id"},
        { "data": "mobile_no"},
        { "data": "email"},
        { "data": "location"},
        { "data": "schemes"},
        { "data": "is_active"},
        { "data": "action"}
      ],
      "drawCallback": function() {
        $('.select2').select2();
        $('#preloader1').hide();
        $(".btnMap1").hide();
      },
      "columnDefs": [
            { targets: "_all","orderable": false },
				    { targets: [0,1,2], "visible": false, },
      ], 
 
      
    }); 
    table.on('click','.toggleStatus',function(){
          $('#modal_form #modal_user_id').val('');
          $('#modal_username').html('');
          $('#modal_location').html('');
          $('#modal_schemes').html('');
          $('#modal_pre_status').html('');
          $('#modal_new_status').html('');
          $tr = $(this).closest('tr');
          if(($tr).hasClass('child')){
            $tr = $tr.prev('parent');
          }
          var data = table.row($tr).data();
          if(data['is_active_db']==1){
           var cur_status='Active';
            var new_status='InActive';
          }
          if(data['is_active_db']==0){
            var cur_status='InActive';
            var new_status='Active';
          }
          var a=data['schemes'];
        
          //console.log(scheme_all);
          $('#modal_form #modal_user_id').val(data['id']);
          $('#modal_username').html(data['username']);
          $('#modal_location').html(data['location']);
          //$('#modal_schemes').html(scheme_all);
          $('#modal_pre_status').html(cur_status);
          $('#modal_new_status').html(new_status);
          if(data['CanUpdate']==1){
            $("#userActiveNew").show();
            $("#userActiveMsg").hide();
            $("#change_button").show();
          }
          else{
            $("#change_button").hide();
            $("#userActiveNew").hide();
            $("#userActiveMsg").show();
           
          }
          $('#ben_view_modal').modal('show');
      });
      table.on('click','.toggleDuty',function(){
          $('#modal_duty_form #modal_duty_id').val('');
          $('#modal_duty_form #modal_duty_user_id').val('');
          $('#modal_duty_form #modal_duty_scheme_id').val('');
          $('#modal_duty_username').html('');
          $('#modal_duty_location').html('');
          $('#modal_duty_scheme').html('');
          $('#modal_duty_pre_status').html('');
          $('#modal_duty_new_status').html('');
          $tr = $(this).closest('tr');
          if(($tr).hasClass('child')){
            $tr = $tr.prev('parent');
          }
          var data = table.row($tr).data();
        
          $('#modal_duty_form #modal_duty_id').val($(this).attr('duty_id'));
          $('#modal_duty_form #modal_duty_user_id').val(data['id']);
          $('#modal_duty_form #modal_duty_scheme_id').val($(this).attr('scheme_id'));
          $('#modal_duty_username').html(data['username']);
          $('#modal_duty_location').html(data['location']);
          $('#modal_duty_scheme').html($(this).attr('scheme_name'));
          $('#modal_duty_pre_status').html($(this).attr('pre_status'));
          $('#modal_duty_new_status').html($(this).attr('new_status'));
          $('#ben_duty_modal').modal('show');
      });
      table.on('click','.btnMap',function(){
          var id=$(this).attr('user_id');
          var scheme_list=$("#schemelistAdd_"+id).val();
          if($.trim($("#schemelistAdd_"+id).val()).length == 0){
            alert('Scheme is required');
            $("#schemelistAdd_"+id).focus();
          }
          else{
                  $("#btnmap_"+id).hide();
                  $("#btnmap_submitting_"+id).show();
                   $.ajax({
                    type: 'POST',
                    url: '<?php echo e(url('userDutymanagement/mapNewScheme')); ?>',
                    data: {
                      scheme_id_list: scheme_list,
                      user_id: id,
                      _token: '<?php echo e(csrf_token()); ?>',
                    },
                    success: function (data) {
                      if(data.return_status){
                      $(".msg-div").hide();
                      $('#example').DataTable().ajax.reload(null, false);
                      printMsg(data.return_msg,'1','crud_msg_Crud');
                      $("#btnmap_"+id).show();
                      $("#btnmap_submitting_"+id).hide();
                    }else{
                      printMsg(data.return_msg,'0','crud_msg_Crud');
                      $("#btnmap_"+id).show();
                      $("#btnmap_submitting_"+id).hide();
                    }
                    },
                    error: function (ex) {
                      $("#btnmap_"+id).show();
                      $("#btnmap_submitting_"+id).hide();
                    }
                  });
          }
      });
  
    $('#example_filter input')
     .off()
     .on('blur', function() {
               table.search( this.value ).draw();
            // $('#example').DataTable().column(0).search(this.value.trim(), false, false).draw();
     });
  $('.txtOnly').keypress(function (e) {
            var regex = new RegExp(/^[a-zA-Z\s]+$/);
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str)) {
                return true;
            }
            else {
                e.preventDefault();
                return false;
            }
    });
  $(".NumOnly").keyup(function(event){
              $(this).val($(this).val().replace(/[^\d].+/, ""));
                  if ((event.which < 48 || event.which > 57)) {
                      event.preventDefault();
       }
  }); 
  $('.special-char').keyup(function()
  {
    var yourInput = $(this).val();
    re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
    var isSplChar = re.test(yourInput);
    if(isSplChar)
    {
      var no_spl_char = yourInput.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
      $(this).val(no_spl_char);
    }
  });
   $(".dataTables_scrollHeadInner").css({"width":"100%"});
 
   $(".table ").css({"width":"100%"}); 

  //fill_datatable();
$("#btnSearch").click(function(){
   
  table.ajax.reload()
    //fill_datatable(department_id,service_designation_id,stake_level,district_code,subdiv_code,block_munc_corp_code);
});
$('.glyphicon').on('click',function(){
   alert('ok');
  });
$('#change_button').on('click',function(){
    $("#change_button").hide();
    $("#submitting").show(); 
    var id= $('#modal_form #modal_user_id').val();
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>"
      }
    });
    $.ajax({
      url: "<?php echo e(url('userDutymanagement/toggleActivate')); ?>",
      type:'POST',
      dataType: 'json',
      data: {
        id:id
      },
      success: function(data) {
        
        if(data.return_status){
          $(".msg-div").hide();
          $('#example').DataTable().ajax.reload(null, false);
          printMsg(data.return_msg,'1','crud_msg_Crud');
          $("#submitting").hide();
          $("#change_button").show();
          
        }else{
          printMsg(data.return_msg,'0','crud_msg_Crud');
        }
        $('#toggleActivate_'+id).prop('disabled', false);
        $('#ben_view_modal').modal('hide');
        $("html, body").animate({ scrollTop: "0" }); 
      },
      error: function (ex) {
        alert(sessiontimeoutmessage);
        //location.reload();
        $('#toggleActivate_'+id).prop('disabled', false);
        $("html, body").animate({ scrollTop: "0" }); 
         window.location.href=base_url;
      }
    });
    
});
$('#change_duty').on('click',function(){
    $("#change_duty").hide();
    $("#submitting-duty").show(); 
    var user_id= $('#modal_duty_form #modal_duty_user_id').val();
    var id= $('#modal_duty_form #modal_duty_id').val();
    var scheme_id= $('#modal_duty_form #modal_duty_scheme_id').val();
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>"
      }
    });
    $.ajax({
      url: "<?php echo e(url('userDutymanagement/toggleDuty')); ?>",
      type:'POST',
      dataType: 'json',
      data: {
        duty_id:id,
        user_id:user_id,
        scheme_id:scheme_id
      },
      success: function(data) {
        
        if(data.return_status){
          $(".msg-div").hide();
          $('#example').DataTable().ajax.reload(null, false);
          printMsg(data.return_msg,'1','crud_msg_Crud');
          $("#submitting-duty").hide();
          $("#change_duty").show();
          
        }else{
          printMsg(data.return_msg,'0','crud_msg_Crud');
        }
        $('#ben_duty_modal').modal('hide');
        $("html, body").animate({ scrollTop: "0" }); 
      },
      error: function (ex) {
        alert(sessiontimeoutmessage);
        //location.reload();
        //$('#toggleActivate_'+id).prop('disabled', false);
        $("html, body").animate({ scrollTop: "0" }); 
        // window.location.href=base_url;
      }
    });
});
$('#district_code_home').change(function() {
        var district=$(this).val();
        //$('#is_urban_home').html('<option value="">--All --</option>');
        $('#block_code_home').html('<option value="">--All --</option>'); 
});
$('#is_urban_home').change(function() {
       var urban_code=$(this).val();
        if(urban_code==''){
          $('#block_code_home').html('<option value="">--All --</option>'); 
        }
        select_district_code= $('#district_code_home').val();
        if(select_district_code==''){
               alert('Please Select District First');
               $("#district_code_home").focus();
              $('#block_code_home').html('<option value="">--All --</option>'); 
        }
        else{
        select_body_type= urban_code;
        var htmlOption='<option value="">--All--</option>';
        if(select_body_type==2){
          $("#blk_sub_txt").text('Block');
            $.each(blocks, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
            $('#block_code_home').html(htmlOption);
            $("#block_code_home_div").show();
        }else if(select_body_type==1){
          $("#blk_sub_txt").text('Sub Div');
            $.each(subDistricts, function (key, value) {
                if(value.district_code==select_district_code){
                    htmlOption+='<option value="'+value.id+'">'+value.text+'</option>';
                }
            });
            $('#block_code_home').html(htmlOption);
            $("#block_code_home_div").show();
        } 
        else{
              $('#block_code_home').html('<option value="">--All --</option>'); 
        }   
        
        }
});
$("#btn-submit").click(function(){
    $('#btn-submit').prop('disabled', true);
    $("#btn_addEdit_loader").show();
    $full_name = $("#full_name").val();
    $full_name_as_in_aadhar = $("#full_name_as_in_aadhar").val();
    $address = $("#address").val();
    $username = $("#username").val();
    $email = $("#email").val();
    $mobile_no = $("#mobile_no").val();
     $id = $('#id').val();
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>"
      }
    });
    $.ajax({
      url: "<?php echo e(url('userDutymanagement/Update')); ?>",
      type:'POST',
      dataType: "json",
      data: { 
        id:$id,
        full_name:$full_name,
        full_name_as_in_aadhar:$full_name_as_in_aadhar,
        address:$address,
        username:$username,
        email:$email,
        mobile_no:$mobile_no
      },
      success: function(data) {
        //console.log(data);
        if(data.return_status){
          $("#UserformModal").modal('hide');
          $("html, body").animate({ scrollTop: "0" }); 
          printMsg(data.return_msg,'1','crud_msg_Crud');
          $('#example').DataTable().ajax.reload(null,false);
          $("#btn_addEdit_loader").hide();
          
        }else{
          $('#UserformModal').animate({ scrollTop: 0 }, 'slow');
          printMsg(data.return_msg,'0','crud_msg_CrudModal');
          $("#btn_addEdit_loader").hide();

        }
        $('#btn-submit').prop('disabled', false);
      },
      error: function (ex) {
        $('#UserformModal').animate({ scrollTop: 0 }, 'slow');
        alert(sessiontimeoutmessage);
        //location.reload();
        $('#btn-submit').prop('disabled', false);
         window.location.href=base_url;
      }
    });
  });
 
});
function UpdateUserForm(id){
    var valid=1;
    $(".print-error-msg").hide();
    $("#must_role_adduser").val(1); 
    $("#full_name").val(''); 
    $("#full_name_as_in_aadhar").val(''); 
    $("#address").val(''); 
    $("#username").val(''); 
    $("#email").val(''); 
    $("#mobile_no").val(''); 
    $(".submit_loader").hide();
    //$('#mobile_no').attr('readonly', false);

    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>"
      }
    });
    if(id){

      $(".submit_loader").show();
    $.ajax({
      type: 'POST',  
      url: 'getUserInfo',
      data:{
        id: id
      },
      dataType: 'json',
      success: function (data) {
        //console.log(data);
        if (data.return_status==0) {
          $(".submit_loader").hide();
          printMsg(data.return_msg,'0','crud_msg_Crud');
        }else{
          $("#full_name").val(data.userarr.full_name); 
          $("#full_name_as_in_aadhar").val(data.userarr.full_name_as_in_aadhar); 
          $("#address").val(data.userarr.address); 
          $("#username").val(data.userarr.username); 
          $("#email").val(data.userarr.email); 
          $("#mobile_no").val(data.userarr.mobile_no); 
          $("#department_id_adduser").val(data.userarr.department_id); 
          $("#designation_id_adduser").val(data.userarr.designation_id);
          $("#user_id").val(data.userarr.id); 
          $(".forAddUserOnly").hide();
          $(".submit_loader").hide(); 
          $("#UserformModal").modal();

        }
        
      },
      error: function (ex) {
        $(".submit_loader").hide();
        alert(sessiontimeoutmessage);
        //location.reload();
         window.location.href=base_url;
      }
    });
    }
    if(id){
      //$('#mobile_no').attr('readonly', true);
      $(".forAddUserOnly").hide();
      var crud_txt='Update User';
      //$(".forAddUserOnly").hide();
    }
    else{
      //$('#mobile_no').attr('readonly', false);
      $(".forAddUserOnly").show();
      var crud_txt='Add User';
    }
    $("#id").val(id);
    $(".crud-txt").text(crud_txt);
  }
function reset(divid){
  $('#district_code_'+divid).val('');
  $('#subdiv_code_'+divid).val('');
  $('#block_munc_corp_code_'+divid).val('');
  $('#gp_ward_code_'+divid).val('');
}

  function closeError(divId){
   $('#'+divId).hide();
  }
  function printMsg (msg,msgtype,divid) {
            $("#"+divid).find("ul").html('');
            $("#"+divid).css('display','block');
			if(msgtype=='0'){
				//alert('error');
				$("#"+divid).removeClass('alert-success');
				//$('.print-error-msg').removeClass('alert-warning');
				$("#"+divid).addClass('alert-warning');
			}
			else{
				$("#"+divid).removeClass('alert-warning');
				$("#"+divid).addClass('alert-success');
			}
			if(Array.isArray(msg)){
            $.each( msg, function( key, value ) {
                $("#"+divid).find("ul").append('<li>'+value+'</li>');
            });
			}
			else{
				$("#"+divid).find("ul").append('<li>'+msg+'</li>');
			}
  }
 
</script>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('userDutymgmt.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>