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
.small-box .icon{
  margin-top: 7%;
}
.small-box>.inner {
    padding: 10px;
    color: white;
}

.small-box p {
    font-size: 18px!important;
}
.select2 .select2-container{
  /*//width:100%!important;*/
} 

.link-button {
  background: none;
  border: none;
  color: blue;
  text-decoration: underline;
  cursor: pointer;
  font-size: 1em;
  font-family: serif;
}
.link-button:focus {
  outline: none;
}
.link-button:active {
  color:red;
}
.small-box-footer-custom{
  position: relative;
    text-align: center;
    padding: 3px 0;
    color: #fff;
    color: rgba(255,255,255,0.8);
    display: block;
    z-index: 10;
    background: rgba(0,0,0,0.1);
    text-decoration: none;
    font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;
    font-weight: 400;
    width:100%;
}
.small-box-footer-custom:hover {
    color: #fff;
    background: rgba(0,0,0,0.15);
}
</style>

@extends('employee-report-drilldown.base')
@section('action-content')

    <!-- Main content -->
    <section class="content">
      <div class="box">
      <div class="box-header">
        <div class="row">
            <div class="col-sm-8">
              <h3 class="box-title"></h3>
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
      <div class="row" style="margin-bottom:1%">
       <!--  <div class="col-md-12"> -->
        <form method="POST" role="form" action="{{ route('employeereport.fetch') }}">
           <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
          <div class="form-group col-md-6">
                            <label class=" control-label">Data Entry Level</label>
                           <!--  <div class=""> -->
                                <select class="form-control select2 full-width js-reportlevel1"  name="level1" id='level1'>
                                    <option value="">--Select--</option>
                                    <option value="State">State</option>
                                    <option value="District">District</option>
                                    <option value="ULB">ULB</option>  
                                    <option value="Block">Block</option> 
                                         
                                </select>
                           <!--  </div> -->
          </div>
          <div class="form-group col-md-6" id="posting_place_div">
                            <label class=" control-label">List of Districts</label><br>
                           <!--  <div class=""> -->
                                <select class="form-control select2 col-md-12 full-width js-reportlevel2d" name="level2d" id="level2d"  style="width:100%">
                                  <option value="">-----Select Option-----</option>
                                   @foreach ($districts as $district)
                                   <option value="{{$district->district_code}}">{{$district->district_name}}</option>
                                   @endforeach 
                                   <!--  <option value="">--Select--</option>
                                    <option value="State">State</option>
                                    <option value="District">District</option>
                                    <option value="Block">Block</option> 
                                    <option value="ULB">ULB</option>      -->  
                                </select>
                           <!--  </div> -->
          </div>
        </div>
        <div class="row" style="margin-bottom:1%">
           <div class="form-group col-md-12">
                            <label class=" control-label" id="level2label">List of State/District/Block</label>
                           <!--  <div class=""> -->
                                <select class="form-control select2 col-md-12 full-width js-reportlevel2" name="level2" id="level2"><!--js-reportlevel2"--> 
                                  <option value="">-----Select Option-----</option>
                                   <!--  <option value="">--Select--</option>
                                    <option value="State">State</option>
                                    <option value="District">District</option>
                                    <option value="Block">Block</option> 
                                    <option value="ULB">ULB</option>      -->  
                                </select>
                           <!--  </div> -->
          </div>
        </div>
        <div class="row" style="margin-bottom: 1%;">
           <div class="form-group col-md-12">
                            <label class=" control-label"  id="level3label">Posting Level</label>
                           <!--  <div class=""> -->
                                <select class="form-control select2 col-md-12 full-width js-reportlevel3" name="level3" id="level3"><!--js-reportlevel3-->
                                    <option value="">--Select Option--</option>
                                  <!--   <option value="State">State</option>
                                    <option value="District">District</option>
                                    <option value="Block">Block</option> 
                                    <option value="ULB">ULB</option>   -->     
                                </select>
                          <!--   </div> -->
          </div><!-- <br><br> -->
        </div>
        <div class="row" style="margin-bottom:1%">
            <div class="form-group col-md-12">
                            <label class=" control-label " id="level4label">List of Health Facility</label>
                           <!--  <div class=""> -->
                                <select class="form-control select2 col-md-12 full-width js-reportlevel4" name="level4" id="level4"><!--js-reportlevel4-->
                                    <option value="">--Select Option--</option>
                                  <!--   <option value="State">State</option>
                                    <option value="District">District</option>
                                    <option value="Block">Block</option> 
                                    <option value="ULB">ULB</option>   -->     
                                </select>
                          <!--   </div> -->
          </div>

          <div class="col-md-4"  style="margin-top: 2%;">
          <!-- <input type="submit" class="btn btn-success btn-lg " name="btn_submit_report" id="btn_submit_report" value="Submit" ></div> -->
          </form>
        </div>
      </div>
       <!--  </div> -->

                 
        
      <div class="col-md-12" id="reportbody" style="margin-top: 2%;">
         
      
       <div class="col-md-4">

          <div class="small-box bg-blue">
            <div class="inner">
                <h3 class="js-reportapplicationssubmittedcount">0</h3>
                <p >Applications Submitted</p>
            </div>
          <div class="icon">
            <i class="fa fa-info"></i>
          </div>
          
          <form method="POST" role="form" action="{{ route('employeereport.appsubmitted') }}">
          <input type="hidden" name="_token" id="token1" value="{{ csrf_token() }}">
          <input type="hidden" id="ApplicationsSubmitted_level1" name="ApplicationsSubmitted_level1">
          <input type="hidden" id="ApplicationsSubmitted_level2" name="ApplicationsSubmitted_level2">
          <input type="hidden" id="ApplicationsSubmitted_level2d" name="ApplicationsSubmitted_level2d">
          <input type="hidden" id="ApplicationsSubmitted_level3" name="ApplicationsSubmitted_level3">
          <input type="hidden" id="ApplicationsSubmitted_level4" name="ApplicationsSubmitted_level4">
        
          <button type="submit" name="submit_param" value="submit_value" class="small-box-footer-custom link-button">
              More Info<i class="fa fa-arrow-right" style='padding:3px;'></i>
          </button>
          </form>        
        </div>
          <!--old code for UI------>
          <!-- <div class="info-box ">        
            <span class="info-box-icon bg-blue"><i class="fa fa-info" style="margin-top: 20%"></i></span>
            <div class="info-box-content">
              <span class="info-box-text" style="font-size: 17px; font-weight: bold;text-transform: none">Applications Submitted</span>
              <span class="info-box-number js-reportapplicationssubmittedcount" style="text-align: end; font-size: 35px;color:#1479b5;">0</span>
             
              
            </div>

           </div> -->
          <!------old code for UI end---->
      </div>
      <div class="col-md-4">

        <div class="small-box bg-verify">
            <div class="inner">
                <h3 class="js-reportpendingverificationcount">0</h3>
                <p >Pending for Verification</p>
            </div>
          <div class="icon">
            <i class="fa fa-exclamation-circle"></i>
          </div>

          <!-- <a href="#" class="small-box-footer">
            More info <i class="fa fa-arrow-right"></i>
          </a> -->
          <form method="POST" role="form" action="{{ route('employeereport.pendingverification') }}">
          <input type="hidden" name="_token" id="token2" value="{{ csrf_token() }}">
          <input type="hidden" id="PendingVerification_level1" name="PendingVerification_level1">
          <input type="hidden" id="PendingVerification_level2" name="PendingVerification_level2">
          <input type="hidden" id="PendingVerification_level2d" name="PendingVerification_level2d">
          <input type="hidden" id="PendingVerification_level3" name="PendingVerification_level3">
          <input type="hidden" id="PendingVerification_level4" name="PendingVerification_level4">
          <button type="submit" name="submit_param" value="submit_value" class="small-box-footer-custom link-button">
              More Info<i class="fa fa-arrow-right" style='padding:3px;'></i>
          </button>
        </form>
        </div>

         <!--old code for UI------> 
          <!-- <div class="info-box ">        
            <span class="info-box-icon bg-verify"><i class="fa fa-exclamation-circle" style="margin-top: 20%;color:white"></i></span>
            <div class="info-box-content">
              <span class="info-box-text" style="font-size: 17px;text-transform: none;font-weight: bold ">Pending for Verification</span>
              <span class="info-box-number js-reportpendingverificationcount" style="text-align: end;font-size: 35px;color:#f7ad06">0</span>
            </div>
           </div> -->
          <!------old code for UI end---->
      </div>
       <div class="col-md-4"> 
          
        <div class="small-box bg-verify">
            <div class="inner">
                <h3 class="js-reportverifiedcount">0</h3>
                <p >Applications Verified</p>
            </div>
          <div class="icon">
            <i class="fa fa-check"></i>
          </div>
          <form method="POST" role="form" action="{{ route('employeereport.appverified') }}">
            <input type="hidden" name="_token" id="token3" value="{{ csrf_token() }}">
          <input type="hidden" id="ApplicationsVerified_level1" name="ApplicationsVerified_level1">
          <input type="hidden" id="ApplicationsVerified_level2" name="ApplicationsVerified_level2">
          <input type="hidden" id="ApplicationsVerified_level2d" name="ApplicationsVerified_level2d">
          <input type="hidden" id="ApplicationsVerified_level3" name="ApplicationsVerified_level3">
          <input type="hidden" id="ApplicationsVerified_level4" name="ApplicationsVerified_level4">
          <!-- <a href="#" class="small-box-footer">
            More info <i class="fa fa-arrow-right"></i>
          </a> -->
          <button type="submit" name="submit_param" value="submit_value" class="small-box-footer-custom link-button">
              More Info<i class="fa fa-arrow-right" style='padding:3px;'></i>
          </button>
        </form>
        </div>

        <!--old code for UI------> 
          <!-- <div class="info-box ">        
            <span class="info-box-icon bg-verify"><i class="fa fa-check" style="margin-top: 20%;color: green"></i></span>
            <div class="info-box-content">
              <span class="info-box-text" style="font-size: 17px;text-transform: none;font-weight: bold ">Applications Verified</span>
              <span class="info-box-number js-reportverifiedcount" style="text-align: end;font-size: 35px;color:#acbd19">0</span>
            </div>
           </div> -->
         <!------old code for UI end---->
      </div>
      <div class="col-md-4"> 
        <div class="small-box bg-red">
            <div class="inner">
                <h3 class="js-reportrejectedcount">0</h3>
                <p >Verification Rejected</p>
            </div>
          <div class="icon">
            <i class="fa fa-ban"></i>
          </div>
          <form method="POST" role="form" action="{{ route('employeereport.rejectedverification') }}">
            <input type="hidden" name="_token" id="token4" value="{{ csrf_token() }}">
          <input type="hidden" id="VerificationRejected_level1" name="VerificationRejected_level1">
          <input type="hidden" id="VerificationRejected_level2" name="VerificationRejected_level2">
          <input type="hidden" id="VerificationRejected_level2d" name="VerificationRejected_level2d">
          <input type="hidden" id="VerificationRejected_level3" name="VerificationRejected_level3">
          <input type="hidden" id="VerificationRejected_level4" name="VerificationRejected_level4">
          <!-- <a href="#" class="small-box-footer">
            More info <i class="fa fa-arrow-right"></i>
          </a> -->
          <button type="submit" name="submit_param" value="submit_value" class="small-box-footer-custom link-button">
              More Info<i class="fa fa-arrow-right" style='padding:3px;'></i>
          </button>
        </form>
        </div>
        <!--old code for UI------> 
          <!-- <div class="info-box ">        
            <span class="info-box-icon bg-red"><i class="fa fa-ban" style="margin-top: 20%"></i></span>
            <div class="info-box-content">
              <span class="info-box-text" style="font-size: 17px;text-transform: none;font-weight: bold ">Verification Rejected</span>
              <span class="info-box-number js-reportrejectedcount" style="text-align: end;font-size: 35px;color:#ea2426">0</span>
            </div>
           </div> -->
        <!------old code for UI end---->
      </div>
      <div class="col-md-4"> 
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3 class="js-reportpendingapprovalcount">0</h3>
                <p >Pending For Approval</p>
            </div>
          <div class="icon">
            <i class="fa fa-exclamation-triangle"></i>
          </div>
          <form method="POST" role="form" action="{{ route('employeereport.pendingapproval') }}">
            <input type="hidden" name="_token" id="token5" value="{{ csrf_token() }}">
          <input type="hidden" id="PendingApproval_level1" name="PendingApproval_level1">
          <input type="hidden" id="PendingApproval_level2" name="PendingApproval_level2" >
          <input type="hidden" id="PendingApproval_level2d" name="PendingApproval_level2d">
          <input type="hidden" id="PendingApproval_level3"  name="PendingApproval_level3">
          <input type="hidden" id="PendingApproval_level4" name="PendingApproval_level4">
          <!-- <a href="#" class="small-box-footer">
            More info <i class="fa fa-arrow-right"></i>
          </a> -->
          <button type="submit" name="submit_param" value="submit_value" class="small-box-footer-custom link-button">
              More Info<i class="fa fa-arrow-right" style='padding:3px;'></i>
          </button>
        </form>
        </div>

        <!--old code for UI------> 
          <!-- <div class="info-box ">        
            <span class="info-box-icon bg-yellow"><i class="fa fa-exclamation-triangle" style="margin-top: 20%"></i></span>
            <div class="info-box-content">
              <span class="info-box-text" style="font-size: 17px; font-weight: bold; text-transform: none">Pending For Approval</span>
              <span class="info-box-number js-reportpendingapprovalcount" style="text-align: end;font-size: 35px;color:#f08121">0</span>
            </div>
           </div> -->
        <!------old code for UI end---->
      </div>
       <div class="col-md-4"> 
         <div class="small-box bg-green">
            <div class="inner">
                <h3 class="js-employeecodegeneratedcount">0</h3>
                <p >Employee Code Generated</p>
            </div>
          <div class="icon">
            <i class="fa fa-check-circle"></i>
          </div>
          <form method="POST" role="form" action="{{ route('employeereport.approved') }}">
            <input type="hidden" name="_token" id="token6" value="{{ csrf_token() }}">
          <input type="hidden" id="EmployeeCodeGenerated_level1" name="EmployeeCodeGenerated_level1">
          <input type="hidden" id="EmployeeCodeGenerated_level2" name="EmployeeCodeGenerated_level2">
          <input type="hidden" id="EmployeeCodeGenerated_level2d" name="EmployeeCodeGenerated_level2d">
          <input type="hidden" id="EmployeeCodeGenerated_level3" name="EmployeeCodeGenerated_level3">
          <input type="hidden" id="EmployeeCodeGenerated_level4" name="EmployeeCodeGenerated_level4">
          <!-- <a href="#" class="small-box-footer">
            More info <i class="fa fa-arrow-right"></i>
          </a> -->
          <button type="submit" name="submit_param" value="submit_value" class="small-box-footer-custom link-button">
              More Info<i class="fa fa-arrow-right" style='padding:3px;'></i>
          </button>
        </form>
        </div>
        <!--old code for UI------> 
          <!-- <div class="info-box ">        
            <span class="info-box-icon bg-green"><i class="fa fa-check-circle" style="margin-top: 20%"></i></span>
            <div class="info-box-content">
              <span class="info-box-text" style="font-size: 17px; font-weight: bold; text-transform: none">Employee Code Generated</span>
              <span class="info-box-number js-employeecodegeneratedcount" style="text-align: end;font-size: 35px;color:#067b46">0</span>
            </div>
           </div> -->
         <!------old code for UI end---->
      </div>
      <div class="col-md-4"> 
        <div class="small-box bg-green">
            <div class="inner">
                <h3 class="js-reportrejectedapprovalcount">0</h3>
                <p >Approval Rejected</p>
            </div>
          <div class="icon">
            <i class="fa fa-ban"></i>
          </div>
          <form method="POST" role="form" action="{{ route('employeereport.rejectedapproval') }}">
            <input type="hidden" name="_token" id="token7" value="{{ csrf_token() }}">
          <input type="hidden" id="ApprovalRejected_level1" name="ApprovalRejected_level1">
          <input type="hidden" id="ApprovalRejected_level2" name="ApprovalRejected_level2">
          <input type="hidden" id="ApprovalRejected_level2d" name="ApprovalRejected_level2d">
          <input type="hidden" id="ApprovalRejected_level3" name="ApprovalRejected_level3">
          <input type="hidden" id="ApprovalRejected_level4" name="ApprovalRejected_level4">
          <!-- <a href="#" class="small-box-footer">
            More info <i class="fa fa-arrow-right"></i>
          </a> -->
          <button type="submit" name="submit_param" value="submit_value" class="small-box-footer-custom link-button">
              More Info <i class="fa fa-arrow-right" style='padding:3px;'></i>
          </button>
        </form>
        </div>

        <!--old code for UI------> 
          <!-- <div class="info-box ">        
            <span class="info-box-icon bg-green"><i class="fa fa-ban" style="margin-top: 20%"></i></span>
            <div class="info-box-content">
              <span class="info-box-text" style="font-size: 17px; font-weight: bold; text-transform: none">Approval Rejected</span>
              <span class="info-box-number js-reportrejectedapprovalcount" style="text-align: end;font-size: 35px;color:#067b46">0</span>
            </div>
           </div> -->
         <!------old code for UI end---->

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
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
<!-----site.js-------------------->
<script src="{{ URL::asset('js/site.js') }}"></script>

<!-------------------------------->

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script >
$(document).ready(function(){
$('#posting_place_div').hide();
$('#level1').change(function () {

  var level1_val=$('option:selected', this).val();
  $('#ApplicationsSubmitted_level1').val(level1_val);
  $('#PendingVerification_level1').val(level1_val);
  $('#ApplicationsVerified_level1').val(level1_val);
  $('#VerificationRejected_level1').val(level1_val);
  $('#PendingApproval_level1').val(level1_val);
  $('#ApprovalRejected_level1').val(level1_val);
  $('#EmployeeCodeGenerated_level1').val(level1_val);
  //alert($('#ApplicationsSubmitted_level1').val());

  $('#ApplicationsSubmitted_level2').removeAttr('value');
  $('#PendingVerification_level2').removeAttr('value');
  $('#ApplicationsVerified_level2').removeAttr('value');
  $('#VerificationRejected_level2').removeAttr('value');
  $('#PendingApproval_level2').removeAttr('value');
  $('#ApprovalRejected_level2').removeAttr('value');
  $('#EmployeeCodeGenerated_level2').removeAttr('value');


  $('#ApplicationsSubmitted_level2d').removeAttr('value');
  $('#PendingVerification_level2d').removeAttr('value');
  $('#ApplicationsVerified_level2d').removeAttr('value');
  $('#VerificationRejected_level2d').removeAttr('value');
  $('#PendingApproval_level2d').removeAttr('value');
  $('#ApprovalRejected_level2d').removeAttr('value');
  $('#EmployeeCodeGenerated_level2d').removeAttr('value');


  $('#ApplicationsSubmitted_level3').removeAttr('value');
  $('#PendingVerification_level3').removeAttr('value');
  $('#ApplicationsVerified_level3').removeAttr('value');
  $('#VerificationRejected_level3').removeAttr('value');
  $('#PendingApproval_level3').removeAttr('value');
  $('#ApprovalRejected_level3').removeAttr('value');
  $('#EmployeeCodeGenerated_level3').removeAttr('value');

  $('#ApplicationsSubmitted_level4').removeAttr('value');
  $('#PendingVerification_level4').removeAttr('value');
  $('#ApplicationsVerified_level4').removeAttr('value');
  $('#VerificationRejected_level4').removeAttr('value');
  $('#PendingApproval_level4').removeAttr('value');
  $('#ApprovalRejected_level4').removeAttr('value');
  $('#EmployeeCodeGenerated_level4').removeAttr('value');
    
    if ($('option:selected', this).val() == "Block")  {
        //$('form').hide();
        $('#posting_place_div').show();
        $('#level2label').text('List of Blocks');
        // $('#level2d').addClass("js-reportlevel2d");
        // $('#level2').addClass("js-reportlevel2");
        // $('#level3').addClass("js-reportlevel3");
        // $('#level4').addClass("js-reportlevel4");
        
    }else if($('option:selected', this).val() =="ULB"){
       $('#posting_place_div').show();
       $('#level2label').text('List of ULBs');
    }
    else if($('option:selected', this).val() =="District"){
       $('#posting_place_div').hide();
       $('#level2label').text('List of Districts');
       
        // $('#level2').addClass("js-reportlevel2");
        // $('#level3').addClass("js-reportlevel3");
        // $('#level4').addClass("js-reportlevel4");
       
       
    }else{
      $('#posting_place_div').hide();
      $('#level2label').text('List of States');
    }
});

$('#level2').change(function(){
  
  var level2_val=$('option:selected', this).val();
  $('#ApplicationsSubmitted_level2').val(level2_val);
  $('#PendingVerification_level2').val(level2_val);
  $('#ApplicationsVerified_level2').val(level2_val);
  $('#VerificationRejected_level2').val(level2_val);
  $('#PendingApproval_level2').val(level2_val);
  $('#ApprovalRejected_level2').val(level2_val);
  $('#EmployeeCodeGenerated_level2').val(level2_val);
  //alert($('#ApplicationsSubmitted_level2').val());


  //$('#ApplicationsSubmitted_level2d').removeAttr('value');
 // $('#PendingVerification_level2d').removeAttr('value');
  //$('#ApplicationsVerified_level2d').removeAttr('value');
  //$('#VerificationRejected_level2d').removeAttr('value');
  //$('#PendingApproval_level2d').removeAttr('value');
  //$('#ApprovalRejected_level2d').removeAttr('value');
  //$('#EmployeeCodeGenerated_level2d').removeAttr('value');


  $('#ApplicationsSubmitted_level3').removeAttr('value');
  $('#PendingVerification_level3').removeAttr('value');
  $('#ApplicationsVerified_level3').removeAttr('value');
  $('#VerificationRejected_level3').removeAttr('value');
  $('#PendingApproval_level3').removeAttr('value');
  $('#ApprovalRejected_level3').removeAttr('value');
  $('#EmployeeCodeGenerated_level3').removeAttr('value');

  $('#ApplicationsSubmitted_level4').removeAttr('value');
  $('#PendingVerification_level4').removeAttr('value');
  $('#ApplicationsVerified_level4').removeAttr('value');
  $('#VerificationRejected_level4').removeAttr('value');
  $('#PendingApproval_level4').removeAttr('value');
  $('#ApprovalRejected_level4').removeAttr('value');
  $('#EmployeeCodeGenerated_level4').removeAttr('value');
});

$('#level2d').change(function(){
  
  var level2d_val=$('option:selected', this).val();
  $('#ApplicationsSubmitted_level2d').val(level2d_val);
  $('#PendingVerification_level2d').val(level2d_val);
  $('#ApplicationsVerified_level2d').val(level2d_val);
  $('#VerificationRejected_level2d').val(level2d_val);
  $('#PendingApproval_level2d').val(level2d_val);
  $('#ApprovalRejected_level2d').val(level2d_val);
  $('#EmployeeCodeGenerated_level2d').val(level2d_val);
  //alert($('#ApplicationsSubmitted_level2d').val());


  $('#ApplicationsSubmitted_level3').removeAttr('value');
  $('#PendingVerification_level3').removeAttr('value');
  $('#ApplicationsVerified_level3').removeAttr('value');
  $('#VerificationRejected_level3').removeAttr('value');
  $('#PendingApproval_level3').removeAttr('value');
  $('#ApprovalRejected_level3').removeAttr('value');
  $('#EmployeeCodeGenerated_level3').removeAttr('value');

  $('#ApplicationsSubmitted_level4').removeAttr('value');
  $('#PendingVerification_level4').removeAttr('value');
  $('#ApplicationsVerified_level4').removeAttr('value');
  $('#VerificationRejected_level4').removeAttr('value');
  $('#PendingApproval_level4').removeAttr('value');
  $('#ApprovalRejected_level4').removeAttr('value');
  $('#EmployeeCodeGenerated_level4').removeAttr('value');
});

$('#level3').change(function(){
  
  var level3_val=$('option:selected', this).val();
  $('#ApplicationsSubmitted_level3').val(level3_val);
  $('#PendingVerification_level3').val(level3_val);
  $('#ApplicationsVerified_level3').val(level3_val);
  $('#VerificationRejected_level3').val(level3_val);
  $('#PendingApproval_level3').val(level3_val);
  $('#ApprovalRejected_level3').val(level3_val);
  $('#EmployeeCodeGenerated_level3').val(level3_val);
  //alert($('#ApplicationsSubmitted_level3').val());


  $('#ApplicationsSubmitted_level4').removeAttr('value');
  $('#PendingVerification_level4').removeAttr('value');
  $('#ApplicationsVerified_level4').removeAttr('value');
  $('#VerificationRejected_level4').removeAttr('value');
  $('#PendingApproval_level4').removeAttr('value');
  $('#ApprovalRejected_level4').removeAttr('value');
  $('#EmployeeCodeGenerated_level4').removeAttr('value');
});

$('#level4').change(function(){
  
  var level4_val=$('option:selected', this).val();
  $('#ApplicationsSubmitted_level4').val(level4_val);
  $('#PendingVerification_level4').val(level4_val);
  $('#ApplicationsVerified_level4').val(level4_val);
  $('#VerificationRejected_level4').val(level4_val);
  $('#PendingApproval_level4').val(level4_val);
  $('#ApprovalRejected_level4').val(level4_val);
  $('#EmployeeCodeGenerated_level4').val(level4_val);
  //alert($('#ApplicationsSubmitted_level4').val());
});

});

</script>