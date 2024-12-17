<!DOCTYPE html>

<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>JB | Jai Bangla</title>
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
  <!-- <link rel="stylesheet" href="{{ asset("/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}"> -->

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
.modal_field_name{
  float:left;
  font-weight: 700;
  margin-right:1%;
  padding-top:1%;
  margin-top:1%;
}

.modal_field_value{
  margin-right:1%;
  padding-top:1%;
  margin-top:1%;
}
.row{
  margin-right: 0px!important;
  margin-left: 0px!important;
  margin-top: 1%!important;
}

.section1{
    border: 1.5px solid #9187878c;
    margin: 2%;
    padding: 2%;
}
.color1{
  margin: 0%!important;
  background-color: #5f9ea061;
}

.modal-header{
  background-color: #7fffd4;
}
.required-field::after {
    content: "*";
    color: red;
}
 .imageSize{
  font-size: 9px;
  color: #333;
 }
 #search{
   margin-top:20px;
 }
.searchResult{
  display:none;
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
  <section class="content">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div> <!-- class="box box-primary" -->
            <div class="box-header with-border">
             <h3 class="box-title"><b>Government of West Bengal Jai Bangla Pension Scheme</b></h3>
                <!-- <p><h3 class="box-title"><b>Bandhu Prakalpa (for SC)</b></h3></p> -->
            </div>

            <div>
             @if ( ($message = Session::get('success')) && ($id =Session::get('id')))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }} with Application ID: {{$id}}</strong>
               
               
              </div>
              @endif
               @if ($message = Session::get('error') )
              <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
               
               
              </div>
              @endif
            @if(count($errors) > 0)
            <div class="alert alert-danger alert-block">
              <ul>
               @foreach($errors->all() as $error)
               <li><strong> {{ $error }}</strong></li>
               @endforeach
              </ul>
            </div>
            @endif
             <!--   @if ($message = Session::get('failure'))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
              </div>
              @endif -->
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" id="register_form" action="{{url('wcd_oap_manabik/store')}}" enctype="multipart/form-data" class="submit-once" >
              {{ csrf_field() }}
        



            <div class="tab-content" style="margin-top:16px;">

              




             <div class="tab-pane active" id="personal_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Search Applicant</b></h4></div>
               <div class="panel-body">

               

               <div class="row">
               <div class="form-group col-md-4">
                 <label class="required-field">Type of Pension</label>
                 <select name="type_of_penstion" id="type_of_penstion" class="form-control" tabindex="1">
                  <option value="">--Select  --</option>
                  <option value="1" @if(old('type_of_penstion')== 1)  selected  @endif>Old Age Pension WCD</option>
                  <option value="3" @if(old('type_of_penstion')== 3)  selected  @endif>Widow Pension WCD</option>
                </select>
                 <span id="error_type_of_penstion" class="text-danger"></span>

                </div>
                <div class="form-group col-md-4" >
                 <label class="required-field">Applicant Id</label>
                 <input type="text" name="applicant_id" id="applicant_id" class="form-control NumOnly" placeholder="Applicant Id" maxlength="200" value="{{ old('applicant_id') }}"  tabindex="2"  />
                 <span id="error_applicant_id" class="text-danger"></span>
                </div>
               
     
                <div class="form-group col-md-4" >
                <button type="button"  id="search" value="Submit" class="btn btn-info success btn-lg" >Search</button>
                 <div class="" ><img src="{{ asset('images/ZKZg.gif')}}" id="submit_loader1" width="50px" height="50px" style="display:none;" ></div>
                 </div>
             <div class="row searchResult" >

             

             <div class="form-group col-md-12">
                 <label class="">Beneficiary Name</label>
               
                </div>
                <div class="form-group col-md-4" >
                 <label class="required-field">First Name</label>
                 <input type="text" name="first_name" id="first_name" class="form-control txtOnly" placeholder="First Name" maxlength="200" value="{{ old('first_name') }}"  tabindex="3"  />
                 <span id="error_first_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label>Middle Name</label>
                 <input type="text" name="middle_name" id="middle_name" class="form-control txtOnly"  placeholder="Middle Name" maxlength="100" value="{{ old('middle_name') }}"  tabindex="4" />
                 <span id="error_middle_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="required-field">Last Name</label>
                 <input type="text" name="last_name" id="last_name" class="form-control txtOnly" placeholder="Last Name" maxlength="200" value="{{ old('last_name') }}" tabindex="5" />
                 <span id="error_last_name" class="text-danger"></span>
                </div>
                </div>
                <div class="row searchResult">
                <div class="form-group col-md-4">
                 <label class="">Mobile Number</label>
                 <input type="text" id="mobile_no" name="mobile_no" class="form-control NumOnly" placeholder="Mobile No" maxlength="10" value="{{ old('mobile_no') }}"  tabindex="6" >
                 <span id="error_mobile_no" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="">Date of Birth</label>
                 @php
                 $age_base = '2020-01-01'; // Or can put $today = date ("Y-m-d");
                 $max = date ("Y-m-d", strtotime ($age_base ."-60 years"));
                 @endphp
                 <input type="date" name="dob" id="dob" class="form-control"  tabindex="5"  max="{{$max}}"/>
                 <!-- <input type="text" id="dob" name="dob"class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask placeholder="dd/mm/yyyy"> -->
                 <span id="error_dob" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label>Age<span style=""> (as on 01/01/2020)</span></label>
                  <input type="hidden" name="hidden_age" id="hidden_age" value="">
                 <input type="text" name="txt_age" id="txt_age" class="form-control NumOnly" placeholder="Age"  value="{{ old('txt_age') }}"  maxlength="3"  tabindex="6"  />
                 <span id="error_txt_age" class="text-danger"></span>
                 
                </div>
                 <div class="form-group col-md-4">
                 <label class="required-field">State</label>
                 <input type="text" id="state" name="state" class="form-control" placeholder="" value="WEST BENGAL" readonly="true" tabindex="7">
                 <span id="error_state" class="text-danger"></span>
                </div>       
                 <!-- <div class="form-group col-md-4">
                 <label class="required-field">State</label>
                 <input type="text" id="state" name="state" class="form-control" placeholder="" value="WEST BENGAL" readonly="true" tabindex="7">
                 <span id="error_state" class="text-danger"></span>
                </div>               -->
             

               <div class="form-group col-md-4">
                 <label class="required-field">District</label>
                 <select name="district" id="district" class="form-control  client-js-district" tabindex="8">
                  <option value="">--Select  --</option>
                   @foreach ($districts as $district)
                  <option value="{{$district->district_code}}"  @if(old('district')== $district->district_code)  selected  @endif> {{$district->district_name}}</option>
                  @endforeach
                </select>
                 <span id="error_district" class="text-danger"></span>

                </div>
                 </div>
                <div class="row searchResult">
 <div class="form-group col-md-4" id="divUrbanCode">
                <label class="required-field">Rural/ Urban</label>
                
                <select name="urban_code" id="urban_code" class="form-control client-js-urban" tabindex="9">
                  <option value="">--Select  --</option>
                  @foreach(Config::get('constants.rural_urban') as $key=>$val)
                  <option value="{{$key}}" @if( old('urban_code') == $key)  selected  @endif >{{$val}}</option>
                  @endforeach     
                   
                </select>
                  <span id="error_urban_code" class="text-danger"></span>
              </div>

                <div class="form-group col-md-4" id="divBodyCode">
                <label class="required-field">Block/Municipality/Corp.</label>
                
                <select name="block" id="block" class="form-control   client-js-localbody" tabindex="10">
                  <option value="">--Select --</option>
                  
                   
                </select>
                  <span id="error_block" class="text-danger"></span>
              </div>

             
              <div class="form-group col-md-4" id="divBodyCode">
                <label  class="required-field">GP/Ward No</label>
                
                <select name="gp_ward" id="gp_ward" class="form-control   client-js-gpward" tabindex="11">
                  <option value="">--Select --</option>
                  
                   
                </select>
                    <span id="error_gp_ward" class="text-danger"></span>
              </div>


 </div>
                <div class="row searchResult">
            
             

             

            

                 

               
 <div class="form-group col-md-6">
                  <label class="required-field">IFS Code</label>
                  <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control special-char" placeholder="IFSC Code" onkeyup="this.value = this.value.toUpperCase();"  value="{{ old('bank_ifsc_code') }}" maxlength='12' tabindex="3" />
                  <span id="error_bank_ifsc_code" class="text-danger"></span>
                </div>

                <div class="form-group col-md-6">
                 <label class="required-field">Bank Name</label>
                 <input type="text" name="name_of_bank" id="name_of_bank" class="form-control special-char" placeholder="Bank Name"  value="{{ old('name_of_bank') }}" maxlength="200" tabindex="4" readonly />
                 <span id="error_name_of_bank" class="text-danger"></span>
                </div>
               
               
                
                <div class="form-group col-md-6">
                 <label class="required-field">Bank Branch Name</label>
                 <input type="text" name="bank_branch" id="bank_branch" class="form-control special-char" placeholder="Bank Branch Name"  value="{{ old('bank_branch') }}" maxlength="300" tabindex="5" readonly />
                 <span id="error_bank_branch" class="text-danger"></span>
                </div>

                <div class="form-group col-md-6">
                 <label class="required-field">Bank Account Number</label>
                 <input type="text" name="bank_account_number" id="bank_account_number" class="form-control NumOnly" placeholder="Bank Account No"  value="{{ old('bank_account_number') }}" maxlength='16' tabindex="6" />
                 <span id="error_bank_account_number" class="text-danger"></span>

                </div>
               
                

                




                    
                          
               

              
                  <br />
                  <br />
                <div class="col-md-12" align="center">

                  <button type="button"  id="submit" value="Submit" class="btn btn-success success btn-lg modal-submit" >Update </button>
                 
                  <button type="button"  id="submitting" value="Submit" class="btn btn-success success btn-lg" disabled >Submitting please wait</button>
                
                 <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                </div>
                <br />
               </div>
              </div>
             </div>
            
             <div class="col-md-12" align="center">
              <br/>
            
<div class="alert print-error-msg"  style="display:none;" id="errorDiv">
      <button type="button" class="close"  aria-label="Close" onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
      <ul></ul></div>
            </div>   


               </div>
              </div>
             </div>





            </div>

  



           </form>
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (left) -->
        
      </div>
     <!--  @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
      @endif -->
      <!-- /.row -->

      
</section>

    <!-- Main content -->
   <!--  <section class="content">

      Your Page Content Here



    </section> -->
    <!-- /.content -->
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

<script src="{{ URL::asset('js/site.js') }}"></script>

<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script src="{{ URL::asset('js/site-client.js') }}"></script>


<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>












<script>
  



$(document).ready(function(){
   $("#submitting").hide();
   $("#submit_loader").hide();
   $("#dob").on('blur',function(){ 
      var today = new Date('2020-01-01');
      var birthDate = new Date($('#dob').val());

      var diff_ms = today.getTime() - birthDate.getTime();
      var age_dt = new Date(diff_ms); 
      var age = Math.ceil(age_dt.getUTCFullYear() - 1970);

      if(isNaN(age)){
        age = 0;
      }
       $('#hidden_age').val(age);
       //if(age!=0 && $('#txt_age').val)
       $('#txt_age').val(age);
      // alert($('#hidden_age').val());
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

   
  $(".NumOnly").keyup(function(event) {
              
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

 $(".price-field").keyup(function() 
        {
          var val = $(this).val();
          if(isNaN(val)){
          val = val.replace(/[^0-9\.]/g,'');
          if(val.split('.').length>2) 
          val =val.replace(/\.+$/,"");
        }
        $(this).val(val);        
        });
       
 $('#district').change(function(){
   $("#urban_code").val('');
   $("#block").html('<option value="">--Select --</option>');
   $("#gp_ward").html('<option value="">--Select --</option>');
 });
        

 
 

 $('#bank_ifsc_code').blur(function(){
    $ifsc_data = $.trim($('#bank_ifsc_code').val());
    $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
    if($ifscRGEX.test($ifsc_data))
    {
      $('#bank_ifsc_code').removeClass('has-error');
      $('#error_bank_ifsc_code').text('');
      $('#error_name_of_bank').html('<img  src="{{ asset('images/ZKZg.gif') }}" width="50px" height="50px"/>');
      $('#error_bank_branch').html('<img  src="{{ asset('images/ZKZg.gif') }}" width="50px" height="50px"/>');
      $.ajax({
        type: 'POST',
        url: '{{ url('legacy/getBankDetails') }}',
        data: {
          ifsc: $ifsc_data,
          _token: '{{ csrf_token() }}',
        },
        success: function (data) {
          if (!data || data.length === 0) {
            $('#error_bank_ifsc_code').text('No data found with the IFSC');
            $('#bank_ifsc_code').addClass('has-error');
            return;
          }
          data = JSON.parse(data);
         // console.log(data);
          $('#name_of_bank').val(data.bank);
          $('#bank_branch').val(data.branch);
          $('#error_name_of_bank').html('');
          $('#error_bank_branch').html('');
        },
        error: function (ex) {
          $('#error_bank_ifsc_code').text('Data fetch error');
          $('#bank_ifsc_code').addClass('has-error');
          $('#error_name_of_bank').html('');
          $('#error_bank_branch').html('');
          alert('Something wrong..may be session timeout. please logout and then login again');
          location.reload();
        }
      });

    }else{
      $('#error_bank_ifsc_code').text('IFSC format invalid please check the code');
      $('#bank_ifsc_code').addClass('has-error');
      $('#error_name_of_bank').html('');
      $('#error_bank_branch').html('');
    }
 });

});
 $('#search').on('click',function(){
   var error_type_of_penstion = '';
   var error_applicant_id = '';
   $('.searchResult').hide();
   $('#errorDiv ul').html('');
   $('#errorDiv').hide();
   if($.trim($('#type_of_penstion').val()).length == 0)
  {
   error_type_of_penstion = 'Type of Pension is required';
   $('#error_type_of_penstion').text(error_type_of_penstion);
   $('#type_of_penstion').addClass('has-error');
  }
  else
  {
   error_type_of_penstion = '';
   $('#error_type_of_penstion').text(error_type_of_penstion);
   $('#type_of_penstion').removeClass('has-error');
  }

  if($.trim($('#applicant_id').val()).length == 0)
  {
   error_applicant_id = 'Applicant Id is required';
   $('#error_applicant_id').text(error_applicant_id);
   $('#applicant_id').addClass('has-error');
  }
  else
  {
   error_applicant_id = '';
   $('#error_applicant_id').text(error_applicant_id);
   $('#applicant_id').removeClass('has-error');
  }
  if( error_type_of_penstion != '' || error_applicant_id != '' )
  {
  
     return false;
  }
  else
  {
      $("#search").hide();
    $("#submit_loader1").show();
     $("#submitting").hide();
    var applicant_id=$('#applicant_id').val();
    var type_of_penstion=$('#type_of_penstion').val();
     $.ajax({
        type: 'POST',
        url: '{{ url('wcd_oap_manabik/bankAccounteditApplicantSearch') }}',
        data: {
          applicant_id: applicant_id,
          type_of_penstion: type_of_penstion,
          _token: '{{ csrf_token() }}',
        },
        success: function (data) {
          $("#search").show();
          $("#submit_loader1").hide();
         // console.log(data);
           if(data.return_status){
             if(type_of_penstion==3){
              document.getElementById("dob").setAttribute("max", '2020-01-01');
              }
              else{
                
                document.getElementById("dob").setAttribute("max", '1960-01-01');
              }
             //alert(data.applicant_row['ben_age']);
              $('#first_name').val(data.applicant_row['first_name']);
              $('#middle_name').val(data.applicant_row['middle_name']);
              $('#last_name').val(data.applicant_row['last_name']);
              $('#mobile_no').val(data.applicant_row['mobile_no']);
              $('#dob').val(data.applicant_row['dob']);
              $('#hidden_age').val(data.applicant_row['ben_age']);
              $('#txt_age').val(data.applicant_row['ben_age']);
              $('#district').val(data.applicant_row['district']).trigger('change');
              //alert(data.applicant_row['district']);
              $('#district').val(data.applicant_row['district']);
              $('#urban_code').val(data.applicant_row['urban_code']);
              var event = new Event('change');
              var element1 = document.getElementById('urban_code');
              element1.dispatchEvent(event);
              $('#block').val(data.applicant_row['block']);
              var element2 = document.getElementById('block');
              element2.dispatchEvent(event);
              $('#gp_ward').val(data.applicant_row['gp_ward']);
              $('#bank_ifsc_code').val(data.applicant_row['bank_ifsc_code']);
              var element3 = document.getElementById('bank_ifsc_code');
              var event2 = new Event('blur');
              element3.dispatchEvent(event2);
              $('#name_of_bank').val(data.applicant_row['name_of_bank']);
              $('#bank_branch').val(data.applicant_row['bank_branch']);
              $('#bank_account_number').val(data.applicant_row['bank_account_number']);
              $(".searchResult").show();
              $("#submit").show();
           }
           else{
              printMsg(data.return_msg,'0','errorDiv');
           }
        },
        error: function (ex) {
         alert('Something wrong..may be session timeout. please logout and then login again');
          location.reload();
        }
      });
      return true; 
  }
});
 $('.modal-submit').on('click',function(){

        var error_type_of_penstion = '';
        var error_applicant_id = '';
        var error_first_name =''; 
        var error_last_name =''; 
        var error_mobile_no =''; 
        var error_district =''; 
        var error_urban_code =''; 
        var error_block =''; 
        var error_gp_ward =''; 
    
        var error_name_of_bank =''; 
        var error_bank_branch =''; 
        var error_bank_account_number =''; 
        var error_bank_ifsc_code =''; 

         if($.trim($('#type_of_penstion').val()).length == 0)
        {
        error_type_of_penstion = 'Type of Pension is required';
        $('#error_type_of_penstion').text(error_type_of_penstion);
        $('#type_of_penstion').addClass('has-error');
        }
        else
        {
        error_type_of_penstion = '';
        $('#error_type_of_penstion').text(error_type_of_penstion);
        $('#type_of_penstion').removeClass('has-error');
        }
        if($.trim($('#first_name').val()).length == 0)
        {
          error_first_name = 'First Name is required';
          $('#error_first_name').text(error_first_name);
          $('#first_name').addClass('has-error');
        }
        else
        {
          error_first_name = '';
          $('#error_first_name').text(error_first_name);
          $('#first_name').removeClass('has-error');
        }
         if($.trim($('#last_name').val()).length == 0)
        {
          error_last_name = 'Last Name is required';
          $('#error_last_name').text(error_last_name);
          $('#last_name').addClass('has-error');
        }
        else
        {
          error_last_name = '';
          $('#error_last_name').text(error_last_name);
          $('#last_name').removeClass('has-error');
        }
         
         if($.trim($('#district').val()).length == 0)
        {
          error_district = 'District is required';
          $('#error_district').text(error_district);
          $('#district').addClass('has-error');
        }
        else
        {
          error_district = '';
          $('#error_district').text(error_district);
          $('#district').removeClass('has-error');
        }
         if($.trim($('#urban_code').val()).length == 0)
        {
          error_urban_code = 'Rural/ Urban is required';
          $('#error_urban_code').text(error_urban_code);
          $('#urban_code').addClass('has-error');
        }
        else
        {
          error_urban_code = '';
          $('#error_urban_code').text(error_urban_code);
          $('#urban_code').removeClass('has-error');
        }
         if($.trim($('#block').val()).length == 0)
        {
          error_block = 'Block/Municipality/Corp is required';
          $('#error_block').text(error_block);
          $('#block').addClass('has-error');
        }
        else
        {
          error_block = '';
          $('#error_block').text(error_block);
          $('#block').removeClass('has-error');
        }
         if($.trim($('#gp_ward').val()).length == 0)
        {
          error_gp_ward = 'GP/Ward No is required';
          $('#error_gp_ward').text(error_gp_ward);
          $('#gp_ward').addClass('has-error');
        }
        else
        {
          error_gp_ward = '';
          $('#error_type_of_penstion').text(error_gp_ward);
          $('#gp_ward').removeClass('has-error');
        }

      

        if($.trim($('#applicant_id').val()).length == 0)
        {
        error_applicant_id = 'Applicant Id is required';
        $('#error_applicant_id').text(error_applicant_id);
        $('#applicant_id').addClass('has-error');
        }
        else
        {
        error_applicant_id = '';
        $('#error_applicant_id').text(error_applicant_id);
        $('#applicant_id').removeClass('has-error');
        }
      if($.trim($('#name_of_bank').val()).length == 0)
      {
      error_name_of_bank = 'Name of Bank is required';
      $('#error_name_of_bank').text(error_name_of_bank);
      $('#name_of_bank').addClass('has-error');
      }
      else
      {
      error_name_of_bank = '';
      $('#error_name_of_bank').text(error_name_of_bank);
      $('#name_of_bank').removeClass('has-error');
      }

      if($.trim($('#bank_branch').val()).length == 0)
      {
      error_bank_branch = 'Bank Branch is required';
      $('#error_bank_branch').text(error_bank_branch);
      $('#bank_branch').addClass('has-error');
      }
      else
      {
      error_bank_branch = '';
      $('#error_bank_branch').text(error_bank_branch);
      $('#bank_branch').removeClass('has-error');
      }

      if($.trim($('#bank_account_number').val()).length == 0)
      {
      error_bank_account_number = 'Bank Account Number is required';
      $('#error_bank_account_number').text(error_bank_account_number);
      $('#bank_account_number').addClass('has-error');
      }
      else
      {
      error_bank_account_number = '';
      $('#error_bank_account_number').text(error_bank_account_number);
      $('#bank_account_number').removeClass('has-error');
      }

      if($.trim($('#bank_ifsc_code').val()).length == 0)
      {
      error_bank_ifsc_code = 'IFS Code is required';
      $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
      $('#bank_ifsc_code').addClass('has-error');
      }
      else
      {
      error_bank_ifsc_code = '';
      $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
      $('#bank_ifsc_code').removeClass('has-error');
      }

      $ifsc_data = $.trim($('#bank_ifsc_code').val());
      $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
      if($ifscRGEX.test($ifsc_data))
      {
        error_bank_ifsc_code = '';
        $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
        $('#bank_ifsc_code').removeClass('has-error');
      }
      else{
        error_bank_ifsc_code = 'Please check IFS Code format';
        $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
        $('#bank_ifsc_code').addClass('has-error');    
      }
      if(error_type_of_penstion != '' || error_applicant_id != '' 
       || error_first_name != '' || error_last_name != '' 
        || error_district != '' || error_block != '' || error_gp_ward != '' 
       || error_urban_code != '' || error_name_of_bank !='' 
       || error_bank_branch !=''||  error_bank_account_number !='' || error_bank_ifsc_code !='')
      {
      return false;
      }
      else
      {
        $(".modal-submit").hide();
        $("#submitting").show();
        $("#submit_loader").show();
        $("#search").hide();
        var applicant_id=$('#applicant_id').val();
        var type_of_penstion=$('#type_of_penstion').val();
        var first_name=$('#first_name').val();
        var middle_name=$('#middle_name').val();
        var last_name=$('#last_name').val();
        var mobile_no=$('#mobile_no').val();
        var dob=$('#dob').val();
        var hidden_age=$('#hidden_age').val();
        var txt_age=$('#txt_age').val();
        var district=$('#district').val();
        var urban_code=$('#urban_code').val();
        var block=$('#block').val();
        var gp_ward=$('#gp_ward').val();
        //alert(hidden_age);

        var bank_ifsc_code=$('#bank_ifsc_code').val();
        var bank_account_number=$('#bank_account_number').val();
        $.ajax({
        type: 'POST',
        url: '{{ url('wcd_oap_manabik/bankAccounteditApplicantEdit') }}',
        data: {
          applicant_id: applicant_id,
          type_of_penstion: type_of_penstion,
          first_name: first_name,
          middle_name: middle_name,
          last_name: last_name,
          mobile_no: mobile_no,
          dob: dob,
          hidden_age: hidden_age,
          txt_age: txt_age,
          district: district,
          urban_code: urban_code,
          block: block,
          gp_ward: gp_ward,
          bank_ifsc_code: bank_ifsc_code,
          bank_account_number: bank_account_number,
          _token: '{{ csrf_token() }}',
        },
        success: function (data) {
          $("#search").show();
          $(".searchResult").hide();
         // console.log(data);
           if(data.return_status){
              $(".searchResult").show();
              $('#applicant_id').val('');
               $("#submitting").hide();
               $("#submit_loader").hide();
                $(".searchResult").hide();
                $('#bank_ifsc_code').val('');
                $('#name_of_bank').val('');
                $('#bank_branch').val('');
                $('#bank_account_number').val('');
                printMsg(data.return_msg,'1','errorDiv');
           }
           else{
              printMsg(data.return_msg,'0','errorDiv');
           }
        },
        error: function (ex) {
         alert('Something wrong..may be session timeout. please logout and then login again');
        location.reload();
        }
      });
      }
});
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
   function closeError(divId){
   $('#'+divId).hide();
  }
  
</script>
</body>
</html>


