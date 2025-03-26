<?php $__env->startSection('action-content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><?php echo e($report_type_name); ?></div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="">
                        <?php echo e(csrf_field()); ?> 

                        <div class="form-group<?php echo e($errors->has('scheme') ? ' has-error' : ''); ?>">
                            <label for="scheme" class="col-md-4 control-label">Select Scheme: </label>

                            <div class="col-md-6">
                                <select onchange="la(this.value)" class="form-control select2">
                                    <option value="">--Select--</option>
                                    <?php for( $i=0; $i < sizeof($scheme_list); $i++): ?>
                                        <option value="application-list-common?scheme_id=<?php echo e($scheme_list[$i]['id']); ?>&type=<?php echo e($type); ?>"><?php echo e($scheme_list[$i]['display_name']); ?></option>  
                                    <?php endfor; ?>
                                </select>
                                <span id="error_construction" class="text-danger"></span>
                            </div>
                                                </div>
                        <script>	
                            function la(src)
                            {
                                window.location=src;
                            }
                            
                        </script>

                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function display_c(){
    var refresh=1000; // Refresh rate in milli seconds
    mytime=setTimeout('display_ct()',refresh)
}

function display_ct() {
    var x = new Date()
    document.getElementById('ct').innerHTML = x.toUTCString();
    display_c();
} 

$(document).ready(function(){ 
    display_ct();
});
</script>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('pensionreport.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>