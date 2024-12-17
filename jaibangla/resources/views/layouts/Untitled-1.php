  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ asset("/bower_components/AdminLTE/dist/img/user2-160x160.jpg") }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info" >
          <p >{{ Auth::user()->username}}  </p>
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
        <li class="active"><a href="{{ url('/backendlogin') }}"><i class="fa fa-link"></i> <span>Dashboard</span></a></li>
        <!-- <li class="active"><a href="{{ url('resetpassword') }}"><i class="fa fa-link"></i> <span>Reset Password</span></a></li> -->
        

        


        @if(Auth::user()->designation_id_old == 'Admin')

       
        
        <li><a href="{{ url('employee-management') }}"><i class="fa fa-link"></i> <span>Employee Management</span></a></li>

        <li><a href="{{ route('user-management.index') }}"><i class="fa fa-link"></i> <span>User management</span></a></li>
        <li><a href="{{ url('scheme-management/SchemeType') }}"><i class="fa fa-link"></i>Scheme Type</a></li>
        <li><a href="{{ url('scheme-management/scheme') }}"><i class="fa fa-link"></i>Scheme</a></li>
        
        <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>System Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
            <li><a href="{{ url('system-management/department') }}"><i class="fa fa-link"></i>Department</a></li>
            <li><a href="{{ url('system-management/division') }}"><i class="fa fa-link"></i>Division</a></li>
            <li><a href="{{ url('system-management/country') }}"><i class="fa fa-link"></i>Country</a></li>
            <li><a href="{{ url('system-management/state') }}"><i class="fa fa-link"></i>State</a></li>
            <li><a href="{{ url('system-management/city') }}"><i class="fa fa-link"></i>City</a></li>
            <li><a href="{{ url('system-management/designation') }}"><i class="fa fa-link"></i>Designation</a></li>
            <li><a href="{{ url('maplevel-management') }}"><i class="fa fa-link"></i>Map Level Management</a></li>
            <li><a href="{{ url('document-mgmt') }}"><i class="fa fa-link"></i>Upload Document Master</a></li>
            <li><a href="{{ url('scheme-doc-map') }}"><i class="fa fa-link"></i>Scheme Document Map</a></li>
          </ul>

        </li>
        <li><a href="{{ url('config') }}"><i class="fa fa-link"></i><span>Duty Management</span></a></li>
       <!--  <li><a href="{{ url('admingetreports') }}"><i class="fa fa-link"></i><span>Reports</span></a></li> -->
        <!--<li><a href="{{ url('employee-report') }}"><i class="fa fa-link"></i><span>Employee Report</span></a></li>-->
        <li><a href="{{ url('employee-report-drilldown') }}"><i class="fa fa-link"></i><span>Dashboard Report</span></a></li>
        <li><a href="{{ url('lot-generation') }}"><i class="fa fa-link"></i> <span>Lot Generation</span></a></li>
        <li><a href="{{ url('push-to-ifms') }}"><i class="fa fa-link"></i> <span>Push To IFMS</span></a></li>
        <li><a href="{{ url('approvalResult') }}"><i class="fa fa-link"></i><span>Data Entry Verification</span></a></li>
         <!-- <li><a href="{{ url('duplicateentry') }}"><i class="fa fa-link"></i><span>Duplicate Entry-I</span></a></li>-->
         <li><a href="{{ url('import_excel') }}"><i class="fa fa-link"></i><span>Import data from Excel</span></a></li>

         <!--<li><a href="{{ url('district-drill-down') }}"><i class="fa fa-link"></i><span>District Wise Report</span></a></li>-->

         <li><a href="{{ url('district-drill-down-consolidated') }}"><i class="fa fa-link"></i><span> Payment Mandate Report</span></a></li>

         <li><a href="{{ url('district-drill-down-payment/IFMS') }}"><i class="fa fa-link"></i> <span>IFMS Payment Failure</span>
         </a></li>

         <li><a href="{{ url('district-drill-down-payment/RBI') }}"><i class="fa fa-link"></i> <span>RBI Payment Failure</span>
         </a></li>

         <li><a href="{{ url('parijayi_mis') }}"><i class="fa fa-link"></i> <span>Sneher Paras Report</span></a></li>

        @elseif(Auth::user()->designation_id_old == 'SPDashboard')
        <li><a href="{{ url('parijayi_mis') }}"><i class="fa fa-link"></i> <span>Sneher Paras Report</span></a></li>
        @elseif(Auth::user()->designation_id_old == 'SPNodal')
        <li><a href="{{ url('parijayi_mis') }}"><i class="fa fa-link"></i> <span>Sneher Paras Report</span></a></li>     
        <li><a href="{{ url('parijayi_generate_lot') }}"><i class="fa fa-link"></i> <span>LOT Generation</span></a></li> 

        @elseif(Auth::user()->designation_id_old == 'Corp')
        <li><a href="{{ url('append-lot') }}"><i class="fa fa-link"></i> <span>Repeat Lot</span></a></li>     
        <li><a href="{{ url('push-to-sbi') }}"><i class="fa fa-link"></i> <span>Push to SBI</span></a></li>   
        <li><a href="{{ url('report-lot-master-sbi/index') }}"><i class="fa fa-link"></i> <span>Lot Status</span></a></li>   

        @elseif(Auth::user()->designation_id_old == 'Dashboard')

       
        <!-- <li><a href="{{ url('district-drill-down') }}"><i class="fa fa-link"></i><span>District Wise Report</span></a></li> -->

        <li><a href="{{ url('district-drill-down-consolidated') }}"><i class="fa fa-link"></i><span> District Wise Report</span></a></li>

        <li><a href="{{ url('district-drill-down-consolidated') }}"><i class="fa fa-link"></i><span> Payment Mandate Report</span></a></li>

        <li><a href="{{ url('district-drill-down-payment/IFMS') }}"><i class="fa fa-link"></i> <span>IFMS Payment Failure</span></a></li>
        
        <li><a href="{{ url('district-drill-down-payment/RBI') }}"><i class="fa fa-link"></i> <span>RBI Payment Failure</span></a></li>

        <li><a href="{{ url('parijayi_mis') }}"><i class="fa fa-link"></i> <span>Sneher Paras Report</span></a></li>


        <!-- <li><a href="{{ url('district-drill-down-consolidated') }}"><i class="fa fa-link"></i><span> Payment Mandate Report</span></a></li>
 -->
         
        @elseif(Auth::user()->designation_id_old === 'DDO')

        <li><a href="{{ url('lot-generation') }}"><i class="fa fa-link"></i> <span>Lot Generation</span></a></li>

        <li><a href="{{ url('large-lot-generation') }}"><i class="fa fa-link"></i> <span>Large Lot Generation</span></a></li>

        <li><a href="{{ url('repeat-lot-generation') }}"><i class="fa fa-link"></i> <span>Repeat Lot</span></a></li>

        <li><a href="{{ url('lot-verification-selectYearMonth') }}"><i class="fa fa-link"></i> <span>Check Lot</span></a></li>
        
        <!--<li><a href="{{ url('push-to-ifms') }}"><i class="fa fa-link"></i> <span>Push To IFMS</span></a></li>-->
		<li><a href="{{ url('report_lot_master') }}"><i class="fa fa-link"></i> <span>Push To IFMS</span></a></li>
        <!--<li><a href="{{ url('ifms-status') }}"><i class="fa fa-link"></i> <span>IFMS Status</span></a></li>-->

        <li><a href="{{ url('block-drill-down-payment/IFMS') }}"><i class="fa fa-link"></i> <span>IFMS Payment Failure</span></a></li>
        <li><a href="{{ url('block-drill-down-payment/RBI') }}"><i class="fa fa-link"></i> <span>RBI Payment Failure</span></a></li>

        
        
        @elseif(Auth::user()->designation_id_old === 'HOD')

        <!-- <li><a href="{{ url('district-drill-down') }}"><i class="fa fa-link"></i><span>District Wise Report</span></a></li> -->

        <li><a href="{{ url('district-drill-down-consolidated') }}"><i class="fa fa-link"></i><span> District Wise Report</span></a></li>

        <li><a href="{{ url('district-drill-down-consolidated') }}"><i class="fa fa-link"></i><span> Payment Mandate Report</span></a></li>

        <li><a href="{{ url('district-drill-down-payment/IFMS') }}"><i class="fa fa-link"></i> <span>IFMS Payment Failure</span></a></li>

        <li><a href="{{ url('district-drill-down-payment/RBI') }}"><i class="fa fa-link"></i> <span>RBI Payment Failure</span></a></li>
        
        <li><a href="{{ url('line-dept-duty') }}"><i class="fa fa-link"></i> <span>Duty Management</span></a></li>



       

        @elseif(Auth::user()->designation_id_old === 'Operator' )
        
         
        <!-- <li><a href="{{ url('employee-report-drilldown') }}"><i class="fa fa-link"></i><span>Dashboard Report</span></a></li> -->
        <!-- <li><a href="{{ url('approvalResult') }}"><i class="fa fa-link"></i><span>Data Entry Verification</span></a></li> -->
        
        <li class="active"><a href="{{ url('mainform') }}"><i class="fa fa-link"></i> <span>Jai Bangla Form</span></a></li>

        <!-- <li class="active"><a href="{{ url('pensionform') }}"><i class="fa fa-link"></i> <span>Application Form</span></a></li> -->
        <li class="active"><a href="{{ url('schemelist') }}"><i class="fa fa-link"></i> <span>Application List</span></a></li>

        <li class="active"><a href="{{ url('approved_schemelist') }}"><i class="fa fa-link"></i> <span>Verified/Approved/Rejected List</span></a></li>
        
         @elseif(Auth::user()->designation_id_old === 'Verifier')
        
       <li><a href="{{ url('scheme-selection') }}" ><i class="fa fa-link"></i> <span>Process Application</span></a></li>
        
       <li><a href="{{ url('scheme-selection-revert') }}" ><i class="fa fa-link"></i> <span>IFMS Failed</span></a></li>
       
       <li><a href="{{ url('scheme-selection-revert-rbi') }}" ><i class="fa fa-link"></i> <span>RBI Failed</span></a></li> 

        @elseif(Auth::user()->designation_id_old === 'Approver')
        
       <li><a href="{{ url('scheme-selection') }}" ><i class="fa fa-link"></i> <span>Process Application</span></a></li> 
        
        <li><a href="{{ url('block-drill-down') }}"><i class="fa fa-link"></i><span>Block Drill Down</span></a></li>

       <li><a href="{{ url('block-drill-down-consolidated') }}"><i class="fa fa-link"></i><span> Payment Mandate Report</span></a></li>
        
       <li><a href="{{ url('block-drill-down-payment/IFMS') }}"><i class="fa fa-link"></i> <span>IFMS Payment Failure</span></a></li>

       <li><a href="{{ url('block-drill-down-payment/RBI') }}"><i class="fa fa-link"></i> <span>RBI Payment Failure</span></a></li>
	   
	   <li><a href="{{ route('update-ben-details') }}"><i class="fa fa-link"></i><span>Update Beneficiary Details</span></a></li>
	   
	   <li><a href="{{ url('duplicate-approval') }}"><i class="fa fa-link"></i><span>Reject Duplicate Approval</span></a></li>

	   <li><a href="{{ route('report-duplicate-approve') }}"><i class="fa fa-link"></i><span>Report Duplicate Rejected</span></a></li>

       <li class="treeview">
        <a href="#"><i class="fa fa-link"></i> <span>Sneher Paras</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">

          <li><a href="{{ url('parijayi/F') }}"><i class="fa fa-link"></i> <span>Fresh Application</span></a></li>
          <li><a href="{{ url('parijayi/A') }}"><i class="fa fa-link"></i> <span>Approved Application</span></a></li>
          <li><a href="{{ url('parijayi/R') }}"><i class="fa fa-link"></i> <span>Rejected Application</span></a></li>
        </ul>
       </li>

        <li><a href="{{ url('emp-user-duty') }}"><i class="fa fa-link"></i><span>Duty Management</span></a></li>

        <li><a href="{{ url('user-add-scheme-index') }}"><i class="fa fa-link"></i><span>Add Scheme</span></a></li>
       
        @endif
        

        
     
        
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>