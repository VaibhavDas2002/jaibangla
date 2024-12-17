<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>EM | Empployee Management</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
     <link href="{{ asset("css/select2.min.css") }}" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet" type="text/css" />

   <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="{{ asset("/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  
  <style>
  .box
  {
   width:800px;
   margin:0 auto;
  }
  .active_tab1
  {
   background-color:#fff;
   color:#333;
   font-weight: 600;
  }
  .inactive_tab1
  {
   background-color: #f5f5f5;
   color: #333;
   cursor: not-allowed;
  }
  .has-error
  {
   border-color:#cc0000;
   background-color:#ffff99;
  }
  .select2{
    width:100%!important;
  }
  .select2 .has-error {
    border-color:#cc0000;
   background-color:#ffff99;
}

.row{
  margin-right: 0px!important;
  margin-left: 0px!important;
  margin-top: 1%!important;
}


.required-field::after {
    content: "*";
    color: red;
}

.emp{
  font-size: 16px;
}
.form-section{
    border: 1.5px solid #9187878c;
    margin-top:3%;
    /*margin: 2%;
    padding: 2%;*/
}

.head{
  background-color: #85a4b9;
  padding:10px;
  margin:0px;
}

  </style>

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
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  @include('layouts.header')
  <!-- Sidebar -->
  @include('layouts.sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
<form method="POST" action="{{ route('ddogenerateemployeepaysave', ['id' => $single_employee_details->application_id]) }}">>
  {{ csrf_field() }}
  <section class="content">

    <div class="row">
       <div class="col-md-4 emp">
          <strong>Employee Code: </strong>                      
          {{$single_employee_details->emp_code}}
          <input type="hidden" name="emp_code" id="emp_code" value="{{$single_employee_details->emp_code}}"/> 
          <input type="hidden" name="application_id" id="application_id" value="{{$single_employee_details->application_id}}"/>                            
        </div>
         <div class="col-md-8 emp">
          <strong> Employee Name:</strong>                   
          {{$single_employee_details->first_name}} {{$single_employee_details->middle_name}} {{$single_employee_details->last_name}}                                 
        </div>
        
    </div>
    <div class="row">
       
        <div class="col-md-4 emp">
          <strong> Designation: </strong>                      
          {{$single_employee_details->designationMaster->name}}                                  
        </div>
        <div class="col-md-8 emp">
          <strong>Date of Joining: </strong>                      
          {{$single_employee_details->doj_present_designation}}                               
        </div> 
    </div>
     <div class="row">
        <div class="col-md-4 emp">
           <strong>Posting Level:</strong>                      
          {{$single_employee_details->posting_level}}                         
        </div>
        <div class="col-md-8 emp">
          <strong>Posting Place:</strong>                       
          {{$single_employee_details->posting_place}}                           
        </div> 
    </div>
    <div class="row">
        <div class="col-md-4 emp">
           <strong>Gross Salary (Previous Month):</strong>                     
           sample salary                                 
        </div>
        <div class="col-md-4 emp">
           <strong>Net Salary (Previous Month):</strong>                       
          sample salary                                
        </div> 
        <div class="col-md-4 emp">
          <!--  <strong>Salary Start Date:</strong>                      
          sample start date        -->                        
        </div> 
    </div>
  
  <section class="form-section">
 
     <div>
   <h4 class="head"> Financial Year & Month</h4>
    <div class="form-group form-inline row">

                 <label class="required-field col-md-8">Financial Year</label>
                 <input type="text" name="financial_year" id="financial_year" class="form-control NumOnly col-md-4" />

                 <span id="error_financial_year" class="text-danger"></span>
    </div>
    <div class="form-group form-inline row">

                 <label class="required-field  col-md-8">Month</label>
                 <input type="text" name="month" id="month" class="form-control   col-md-4" />
                 
                 <span id="error_month" class="text-danger"></span>
    </div>
    <div class="form-group form-inline row">

                 <label class="required-field col-md-8">Salary Start Date</label>
                 <input type="date" name="salary_start_date" id="salary_start_date" class="form-control NumOnly col-md-4" />

                 <span id="error_financial_year" class="text-danger"></span>
    </div>
    <div class="form-group form-inline row">

                 <label class="required-field col-md-8">Salary End Date</label>
                 <input type="date" name="salary_end_date" id="salary_end_date" class="form-control NumOnly col-md-4" />

                 <span id="error_financial_year" class="text-danger"></span>
    </div>
            
  </div>
    <div>
   <h4 class="head"> Component of Monthly Consolidated Remuneration</h4>
    <div class="form-group form-inline row">

                 <label class="required-field col-md-8">Monthly Consolidated Remuneration</label>
                 <input type="text" name="monthly_consolidated_remuneration" id="monthly_consolidated_remuneration" class="form-control NumOnly col-md-4" />

                 <span id="error_monthly_consolidated_remuneration" class="text-danger"></span>
    </div>
    <div class="form-group form-inline row">

                 <label class="required-field  col-md-8">Arrear Salary</label>
                 <input type="text" name="arrear_salary" id="arrear_salary" class="form-control NumOnly col-md-4" />
                 
                 <span id="error_arrear_salary" class="text-danger"></span>
    </div>
     <div class="form-group form-inline row">

                 <label class="required-field  col-md-8">Bonus</label>
                 <input type="text" name="bonus" id="bonus" class="form-control NumOnly col-md-4" />
                 
                 <span id="error_bonus" class="text-danger"></span>
    </div>
     <div class="form-group form-inline row">

                 <label class="required-field  col-md-8">Other Allowance</label>
                 <input type="text" name="other_allowance" id="other_allowance" class="form-control NumOnly col-md-4" />
                 
                 <span id="error_other_allowance" class="text-danger"></span>
    </div>
     <div class="form-group form-inline row">

                 <label class="required-field col-md-8">Advances</label>
                 <input type="text" name="advances" id="advances" class="form-control NumOnly col-md-4" />
                 
                 <span id="error_advances" class="text-danger"></span>
    </div>
     <div class="form-group form-inline row">

                 <label class="required-field  col-md-8">Employer's Share of EPF</label>
                 <input type="text" name="employers_share_of_epf" id="employers_share_of_epf" class="form-control NumOnly col-md-4" />
                 
                 <span id="error_employers_share_of_epf" class="text-danger"></span>
    </div>
  </div>


  <div>
    <h4 class="head"> Deduction</h4>
    <div class="form-group form-inline row">

                 <label class="required-field  col-md-8">Professional Tax</label>
                 <input type="text" name="professional_tax" id="professional_tax" class="form-control NumOnly col-md-4" />

                 <span id="error_professional_tax" class="text-danger"></span>
    </div>
    <div class="form-group form-inline row">

                 <label class="required-field  col-md-8">TDS/Income Tax</label>
                 <input type="text" name="income_tax" id="income_tax" class="form-control NumOnly col-md-4" />
                 
                 <span id="error_income_tax" class="text-danger"></span>
    </div>
     <div class="form-group form-inline row">

                 <label class=" required-field col-md-8">House Rent</label>
                 <input type="text" name="house_rent" id="house_rent" class="form-control NumOnly col-md-4" />
                 
                 <span id="error_house_rent" class="text-danger"></span>
    </div>
     <div class="form-group form-inline row">

                 <label class="required-field  col-md-8">Deduction Against Advances</label>
                 <input type="text" name="deduction_against_advances" id="deduction_against_advances" class="form-control NumOnly col-md-4" />
                 
                 <span id="error_deduction_against_advances" class="text-danger"></span>
    </div>
     <div class="form-group form-inline row">

                 <label class="required-field  col-md-8">EPF Deduction</label>
                 <input type="text" name="epf_deductions" id="epf_deductions" class="form-control NumOnly col-md-4" />
                 
                 <span id="error_epf_deductions" class="text-danger"></span>
    </div>
     <div class="form-group form-inline row">

                 <label class="required-field col-md-8">Other Deduction</label>
                 <input type="text" name="other_deductions" id="other_deductions" class="form-control NumOnly col-md-4" />
                 
                 <span id="error_other_deductions" class="text-danger"></span>
    </div>

  </div>

    <div id="salary">
    <h4 class="head"> Salary</h4>
    <div class="form-group form-inline row">

                 <label class="required-field  col-md-8">Gross Salary</label>
                 <input type="text" name="gross_salary" id="gross_salary" class="form-control NumOnly col-md-4" disabled/>

                 <span id="error_gross_salary" class="text-danger"></span>
    </div>
    <div class="form-group form-inline row">

                 <label class="required-field col-md-8">Net Salary</label>
                 <input type="text" name="net_salary" id="net_salary" class="form-control NumOnly col-md-4" disabled/>
                 
                 <span id="error_net_salary" class="text-danger"></span>
    </div>
    
   
    
     
  </div>
   <div class="text-center" style="margin-bottom: 3px">
    <button type="button" onclick="calculatesalary()" name="btn_get_salary" id="btn_get_salary" class="btn btn-info btn-lg">Calculate Salary</button>
   </div>
 
  </section>
  <div class="row" style="margin-top: 4%"> <input type="submit"  name="btn_save_salary" id="btn_save_salary" class="btn btn-success btn-lg  col-md-12" value="Submit"/></div>
  
  </section>
</form>
    <!-- Main content -->
   <!--  <section class="content">

      Your Page Content Here



    </section> -->
    <!-- /.content -->
  </div>
</div>
  <!-- /.content-wrapper -->

  <!-- Footer -->
  @include('layouts.footer')
  
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

 <!-- jQuery 2.1.3 -->
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
<!-----site.js-------------------->
<script src="{{ URL::asset('js/site.js') }}"></script>

<!-------------------------------->

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script>
  $('.select2').select2();
</script>
<script>
$('.NumOnly').keydown(function (e) {
  
    if (e.altKey) {
    
      e.preventDefault();
      
    } else {
    
      var key = e.keyCode;
      
      // if (key > 31 && (key < 48 || key > 57)) {
      
      //   e.preventDefault();
        
      // }
       if ((key > 64 && key < 91) || (key > 96 && key < 123)) {
      
        e.preventDefault();
        
      }

    }
    
  });
</script>

<script>
function calculatesalary(){
  var monthly_consolidated_remuneration=Number($('#monthly_consolidated_remuneration').val());
  var arrear_salary=Number($('#arrear_salary').val());
  var bonus=Number($('#bonus').val());
  var other_allowance=Number($('#other_allowance').val());
  var advances=Number($('#advances').val());
  var employers_share_of_epf=Number($('#employers_share_of_epf').val());
  
  var professional_tax=Number($('#professional_tax').val());
  var income_tax=Number($('#income_tax').val());
  var house_rent=Number($('#house_rent').val());
  var deduction_against_advances=Number($('#deduction_against_advances').val());
  var epf_deductions=Number($('#epf_deductions').val());
  var other_deductions=Number($('#other_deductions').val());

  var gross_salary=monthly_consolidated_remuneration+arrear_salary+bonus+other_allowance+advances+employers_share_of_epf;
  var deductions=professional_tax+income_tax+house_rent+deduction_against_advances+epf_deductions+other_deductions;
 // alert(deductions);
  
  var net_salary=Number(gross_salary-deductions);
  $('#net_salary').val(net_salary);
  $('#gross_salary').val(gross_salary);

}
</script>

</body>
</html>
