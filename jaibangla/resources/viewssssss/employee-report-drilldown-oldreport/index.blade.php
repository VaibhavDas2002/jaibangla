<style type="text/css">
  .full-width{
    width:100%!important;
  }
.bg-blue{
  background-image: linear-gradient(to right top, #0073b7, #0086c0, #0097c5, #00a8c6, #00b8c4)!important;
}
.bg-red{
  /*background-image: linear-gradient(to right bottom, #dd4b39, #db4546, #d74052, #d13d5e, #c93d68)!important;*/
 /* background-image: linear-gradient(to right bottom, #dd4b39, #e65347, #ef5b55, #f76463, #ff6d71)!important;*/
 background-image: linear-gradient(to right bottom, #dd4b39, #ec6f65, #d21a13, #de0d0b, #f3060d)!important;
}
.bg-yellow{
  background-image: linear-gradient(to right bottom, #dd4b39, #e65f31, #ed7328, #f1881e, #f39c12)!important;
}
.bg-green{
 /*background-image: linear-gradient(to right bottom, #00837d, #008d7b, #009674, #009e69, #00a65a)!important;*/
 background-image: linear-gradient(to right bottom, #04736d, #008f73, #00ab6a, #00c44f, #5ddc0c)!important;
}

.bg-verify{
  background-image: linear-gradient(to right top, #f39c12, #f8b005, #fac400, #fad902, #f8ee15)!important;
}
.info-box {
    display: block;
    min-height: 90px;
    background: #b6d0ca33!important;
    width: 100%;
    box-shadow: 0px 0px 15px 0px rgba(0, 0, 0, 0.30)!important;
    border-radius: 2px;
    margin-bottom: 15px;
}
</style>

@extends('employee-report.base')
@section('action-content')

    <!-- Main content -->
    <section class="content">
      <div class="box">
      <div class="box-header">
        <div class="row">
            <div class="col-sm-8">
              <h3 class="box-title">List of Employee</h3>
            </div>
            <!-- <div class="col-sm-4">
              <a class="btn btn-primary" href="{{ route('user-management.create') }}">Add new user</a>
            </div> -->
        </div>
      </div>
      <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>
      
      <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap js-report-form">
      <div class="row">
       <!--  <div class="col-md-12"> -->
        <form method="POST" role="form" action="{{ route('employeereport.fetch') }}">
           <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <div class="form-group col-md-4">
                            <label class=" control-label">Level</label>
                           <!--  <div class=""> -->
                                <select class="form-control select2 full-width js-reportlevel1"  name="level1">
                                    <option value="">--Select--</option>
                                    <option value="State">State</option>
                                    <option value="District">District</option>
                                    <option value="ULB">ULB</option>  
                                    <option value="Block">Block</option> 
                                         
                                </select>
                           <!--  </div> -->
          </div>
           <div class="form-group col-md-4">
                            <label class=" control-label">List of State/District/Block</label>
                           <!--  <div class=""> -->
                                <select class="form-control select2 col-md-12 full-width js-reportlevel2" name="level2">
                                  <option value="">-----Select Option-----</option>
                                   <!--  <option value="">--Select--</option>
                                    <option value="State">State</option>
                                    <option value="District">District</option>
                                    <option value="Block">Block</option> 
                                    <option value="ULB">ULB</option>      -->  
                                </select>
                           <!--  </div> -->
          </div>
           <div class="form-group col-md-4">
                            <label class=" control-label">SPMU/MCH/DPMU/Hospital/CPMU/UPHC/BPMU/SC</label>
                           <!--  <div class=""> -->
                                <select class="form-control select2 col-md-12 full-width js-reportlevel3" name="level3">
                                    <option value="">--Select Option--</option>
                                  <!--   <option value="State">State</option>
                                    <option value="District">District</option>
                                    <option value="Block">Block</option> 
                                    <option value="ULB">ULB</option>   -->     
                                </select>
                          <!--   </div> -->
          </div><br><br>
            <div class="form-group col-md-8">
                            <label class=" control-label" style="margin-top: 2%;">List of Health Facility</label>
                           <!--  <div class=""> -->
                                <select class="form-control select2 col-md-12 full-width js-reportlevel4" name="level4">
                                    <option value="">--Select Option--</option>
                                  <!--   <option value="State">State</option>
                                    <option value="District">District</option>
                                    <option value="Block">Block</option> 
                                    <option value="ULB">ULB</option>   -->     
                                </select>
                          <!--   </div> -->
          </div>

          <div class="col-md-4"  style="margin-top: 2%;">
          <input type="submit" class="btn btn-success btn-lg " name="btn_submit_report" id="btn_submit_report" value="Submit" ></div>
          </form>
        </div>
       <!--  </div> -->

        <br>         
        
      <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
         
      
       <div class="col-md-4"> 
          <div class="info-box ">        
            <span class="info-box-icon bg-blue"><i class="fa fa-info" style="margin-top: 20%"></i></span>
            <div class="info-box-content">
              <span class="info-box-text" style="font-size: 17px; font-weight: bold;text-transform: none">Applications Submitted</span>
              <span class="info-box-number js-reportapplicationssubmittedcount" style="text-align: end; font-size: 35px;color:#1479b5;">0</span>
            </div>
           </div>
      </div>
      <div class="col-md-4"> 
          <div class="info-box ">        
            <span class="info-box-icon bg-verify"><i class="fa fa-exclamation-circle" style="margin-top: 20%;color:white"></i></span>
            <div class="info-box-content">
              <span class="info-box-text" style="font-size: 17px;text-transform: none;font-weight: bold ">Pending for Verification</span>
              <span class="info-box-number js-reportpendingverificationcount" style="text-align: end;font-size: 35px;color:#f7ad06">0</span>
            </div>
           </div>
      </div>
       <div class="col-md-4"> 
          <div class="info-box ">        
            <span class="info-box-icon bg-verify"><i class="fa fa-check" style="margin-top: 20%;color: green"></i></span>
            <div class="info-box-content">
              <span class="info-box-text" style="font-size: 17px;text-transform: none;font-weight: bold ">Applications Verified</span>
              <span class="info-box-number js-reportverifiedcount" style="text-align: end;font-size: 35px;color:#acbd19">0</span>
            </div>
           </div>
      </div>
      <div class="col-md-4"> 
          <div class="info-box ">        
            <span class="info-box-icon bg-red"><i class="fa fa-ban" style="margin-top: 20%"></i></span>
            <div class="info-box-content">
              <span class="info-box-text" style="font-size: 17px;text-transform: none;font-weight: bold ">Verification Rejected</span>
              <span class="info-box-number js-reportrejectedcount" style="text-align: end;font-size: 35px;color:#ea2426">0</span>
            </div>
           </div>
      </div>
      <div class="col-md-4"> 
          <div class="info-box ">        
            <span class="info-box-icon bg-yellow"><i class="fa fa-exclamation-triangle" style="margin-top: 20%"></i></span>
            <div class="info-box-content">
              <span class="info-box-text" style="font-size: 17px; font-weight: bold; text-transform: none">Pending For Approval</span>
              <span class="info-box-number js-reportpendingapprovalcount" style="text-align: end;font-size: 35px;color:#f08121">0</span>
            </div>
           </div>
      </div>
       <div class="col-md-4"> 
          <div class="info-box ">        
            <span class="info-box-icon bg-green"><i class="fa fa-check-circle" style="margin-top: 20%"></i></span>
            <div class="info-box-content">
              <span class="info-box-text" style="font-size: 17px; font-weight: bold; text-transform: none">Employee Code Generated</span>
              <span class="info-box-number js-employeecodegeneratedcount" style="text-align: end;font-size: 35px;color:#067b46">0</span>
            </div>
           </div>
      </div>
      <div class="col-md-4"> 
          <div class="info-box ">        
            <span class="info-box-icon bg-green"><i class="fa fa-ban" style="margin-top: 20%"></i></span>
            <div class="info-box-content">
              <span class="info-box-text" style="font-size: 17px; font-weight: bold; text-transform: none">Approval Rejected</span>
              <span class="info-box-number js-reportrejectedapprovalcount" style="text-align: end;font-size: 35px;color:#067b46">0</span>
            </div>
           </div>
      </div>
        </div>
      </div>
      <?php //if($flag==1):?>
     
    <?php// endif;?>
    </div>
  <!--   </div> -->
    </section>
    <!-- /.content -->
  </div>
@endsection