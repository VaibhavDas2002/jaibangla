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
    content: " *";
    color: red;
}
 .imageSize{
  font-size: 9px;
  color: #333;
 }
#schemHighlight{
  color: lightskyblue;
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
           

            <div>
             @if ( ($message = Session::get('success')) && ($id =Session::get('id')))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }} with Application ID: {{$id}}</strong>
               
               
              </div>
              @endif
             @if(!empty($error))
            <div class="alert alert-danger alert-block">
              <ul>
              
               <li><strong> {{ $error }}</strong></li>
               
              </ul>
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
            <form method="post" id="register_form" action="{{url('store-lppshortEntry')}}"  class="submit-once" >
              {{ csrf_field() }}
        
           <input type="hidden" name="scheme_id" id="scheme_id" value="{{$scheme_id}}"/>


            <div class="tab-content" style="margin-top:16px;">
            




             <div class="tab-pane active" id="personal_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>Scheme : <span id="" class="text-primary">{{$scheme_name}}</span></b></h4></div>
               <div class="panel-body">

                
                

              
               <div class="form-group col-md-12">
                 <label class="">Beneficiary Name</label>
               
                </div>
                 <div class="row">
                <div class="form-group col-md-4" >
                 <label class="required-field">First Name</label>
                 <input type="text" name="first_name" id="first_name" class="form-control txtOnly" placeholder="First Name" maxlength="200" value="{{ old('first_name') }}"    />
                 <span id="error_first_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label>Middle Name</label>
                 <input type="text" name="middle_name" id="middle_name" class="form-control txtOnly"  placeholder="Middle Name" maxlength="100" value="{{ old('middle_name') }}"  />
                 <span id="error_middle_name" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="">Last Name</label>
                 <input type="text" name="last_name" id="last_name" class="form-control txtOnly" placeholder="Last Name" maxlength="200" value="{{ old('last_name') }}"  />
                 <span id="error_last_name" class="text-danger"></span>
                </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-4">
                    <label class="required-field">Mobile Number</label>
                    <input type="text" id="mobile_no" name="mobile_no" class="form-control NumOnly" placeholder="Mobile No" maxlength="10" value="{{ old('mobile_no') }}"   >
                    <span id="error_mobile_no" class="text-danger"></span>
                  </div>
                  <div class="form-group col-md-4">
                    <label class="required-field">Gender</label>
                    <select class="form-control" name="gender" id="gender" >
                      
                      <option value="">--Select--</option>
                      @foreach(Config::get('constants.gender') as $key=>$val)
                      <option value="{{$key}}">{{$val}}</option>
                      @endforeach
                      
                    </select>
                    <span id="error_gender" class="text-danger"></span>
                  </div>
                  <div class="form-group col-md-4">
                    <label class="required-field">Folk Form</label>
                    <input type="text" name="folk_form" id="folk_form" class="form-control txtOnly" placeholder="Folk Form" maxlength="200" value="{{ old('folk-form') }}"  />
                    <span id="error_folk_form" class="text-danger"></span>
                </div>
                </div>
              <div class="row">
                <div class="form-group col-md-4">
                 <label class="required-field">Date of Birth</label>
                 <input type="date" name="dob" id="dob" class="form-control" />
                 <!-- <input type="text" id="dob" name="dob"class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask placeholder="dd/mm/yyyy"> -->
                 <span id="error_dob" class="text-danger"></span>
                </div> 
                <div class="form-group col-md-4">
                 <label class="">Caste</label>
                 <select name="caste" id="caste" class="form-control">
                  <option value="">--Select--</option>
                   <option value="SC">SC</option>
                   <option value="ST">ST</option>
                   <option value="OBC">OBC</option>
                   <option value="General">General</option>
                 </select>
                 <span id="error_caste" class="text-danger"></span>
                </div>           
               <div class="form-group col-md-4">
                 <label class="required-field">District</label>
                 <select name="district" id="district" class="form-control  client-js-district" >
                  <option value="">--Select  --</option>
                   @foreach ($districts as $district)
                  <option value="{{$district->district_code}}"   @if(trim(old('district'))!=''?trim(old('district')):trim($sel_district)== $district->district_code)  selected  @endif> {{$district->district_name}}</option>
                  @endforeach
                </select>
                 <span id="error_district" class="text-danger"></span>

                </div>
                <div class="form-group col-md-4" id="divUrbanCode">
                <label class="required-field">Rural/ Urban</label>
                
                <select name="urban_code" id="urban_code" class="form-control client-js-urban" >
                  <option value="">--Select  --</option>
                  @foreach(Config::get('constants.rural_urban') as $key=>$val)
                  <option value="{{$key}}">{{$val}}</option>
                  @endforeach     
                   
                </select>
                  <span id="error_urban_code" class="text-danger"></span>
              </div>

                <div class="form-group col-md-4" id="divBodyCode">
                <label class="required-field">Block/Municipality/Corp.</label>
                
                <select name="block" id="block" class="form-control   client-js-localbody" >
                  <option value="">--Select --</option>
                  
                   
                </select>
                  <span id="error_block" class="text-danger"></span>
              </div>

             
              <div class="form-group col-md-4" id="divBodyCode">
                <label  class="">GP/Ward No</label>
                
                <select name="gp_ward" id="gp_ward" class="form-control   client-js-gpward" >
                  <option value="">--Select --</option>
                  
                   
                </select>
                    <span id="error_gp_ward" class="text-danger"></span>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-6">
                  <label class="required-field">IFS Code</label>
                  <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control special-char" placeholder="IFSC Code" onkeyup="this.value = this.value.toUpperCase();" maxlength='11'  />
                  <span id="error_bank_ifsc_code" class="text-danger"></span>
                </div>

                <div class="form-group col-md-6">
                 <label class="required-field">Bank Name</label>
                 <input type="text" name="name_of_bank" id="name_of_bank" class="form-control special-char" placeholder="Bank Name"  maxlength="200"  readonly/>
                 <span id="error_name_of_bank" class="text-danger"></span>
                </div>
               
               
                
                <div class="form-group col-md-6">
                 <label class="required-field">Bank Branch Name</label>
                 <input type="text" name="bank_branch" id="bank_branch" class="form-control special-char" placeholder="Bank Branch Name"  maxlength="300"  readonly/>
                 <span id="error_bank_branch" class="text-danger"></span>
                </div>

                <div class="form-group col-md-6">
                 <label class="required-field">Bank Account No.</label>
                 <input type="text" name="bank_account_number" id="bank_account_number" class="form-control NumOnly" placeholder="Bank Account No"   maxlength='16'  />
                 <span id="error_bank_account_number" class="text-danger"></span>

                </div>
            </div>
              <div class="row">
               
 
               
               
                
               
              
                  <br />
                  <br />
                <div class="col-md-12" align="center">

                  <button type="submit"  id="submit" value="Submit" class="btn btn-success success btn-lg modal-submit form-submitted" >Submit </button>
                 
                  <button type="button"  id="submitting" value="Submit" class="btn btn-success success btn-lg" disabled >Submitting please wait</button>
                
                 <!--<button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>-->
                </div>
                <br />
               </div>
              </div>
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
<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>
<script src="{{ URL::asset('js/site-client1.js') }}"></script>
<script src="{{ URL::asset('js/validateAdhar.js') }}"></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>

<script>

$(document).ready(function(){
  var old_districtValue={{$sel_district}};
  // var old_assemblyValue="";
  // var old_blockValue={{$sel_urban_body_code}};
  // var old_gpValue="";
  // var old_urbanValue={{$sel_rural_urban}};
  if (old_districtValue!='')
  {
    $('#district').val(old_districtValue).trigger('change');
  }
  // if (old_urbanValue!='')
  // {
  //   $('#urban_code').val(old_urbanValue).trigger('change');
  // }
  // if (old_blockValue!='')
  // {
  //   $('#block').val(old_blockValue).trigger('change');
  // }
  
  
  $("#submitting").hide();
  $("#submit_loader").hide();

  $('form.submit-once').submit(function(e){
     
      if( $(this).hasClass('form-submitted') ){
        e.preventDefault();
        return;
    }
    $(this).addClass('form-submitted');
     
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

 $('.modal-submit').on('click',function(){
      var error_first_name ='';
      var error_mobile_no ='';
      var error_gender = '';
      var error_folk_form = '';
      var error_district = '';
      var error_urban_code = '';
      var error_block = '';
      var error_name_of_bank =''; 
      var error_bank_branch =''; 
      var error_bank_account_number =''; 
      var error_bank_ifsc_code =''; 
      
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

      if($.trim($('#mobile_no').val()).length == 0)
      {
        error_mobile_no = 'Mobile Number is required';
        $('#error_mobile_no').text(error_mobile_no);
        $('#mobile_no').addClass('has-error');
      }
      else
      {
        error_mobile_no = '';
        $('#error_mobile_no').text(error_mobile_no);
        $('#mobile_no').removeClass('has-error');

      }

      if($.trim($('#mobile_no').val()).length !=0)
      {
      if($.trim($('#mobile_no').val()).length !=10)
      {
        error_mobile_no = 'Mobile Number must be 10 digit';
        $('#error_mobile_no').text(error_mobile_no);
        $('#mobile_no').addClass('has-error');
      }
      else
      {
        error_mobile_no = '';
        $('#error_mobile_no').text(error_mobile_no);
        $('#mobile_no').removeClass('has-error');

      }
      }

      if($.trim($('#gender').val()).length == 0)
      {
       error_gender = 'Gender is required';
       $('#error_gender').text(error_gender);
       $('#gender').addClass('has-error');
      }
      else
      {
       error_gender = '';
       $('#error_gender').text(error_gender);
       $('#gender').removeClass('has-error');
      }

      if($.trim($('#folk_form').val()).length == 0)
      {
       error_folk_form = 'Folk Form is required';
       $('#error_folk_form').text(error_folk_form);
       $('#folk_form').addClass('has-error');
      }
      else
      {
       error_folk_form = '';
       $('#error_folk_form').text(error_folk_form);
       $('#folk_form').removeClass('has-error');
      }
     
      if($.trim($('#dob').val()).length == 0)
      {
       error_dob = 'Date-of-Birth is required';
       $('#error_dob').text(error_dob);
       $('#dob').addClass('has-error');
      }
      else
      {
       error_dob = '';
       $('#error_dob').text(error_dob);
       $('#dob').removeClass('has-error');
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
       error_urban_code = 'Urban/Rural is required';
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
       error_block = 'Block/Municipality is required';
       $('#error_block').text(error_block);
       $('#block').addClass('has-error');
      }
      else
      {
       error_block = '';
       $('#error_block').text(error_block);
       $('#block').removeClass('has-error');
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

      
      if(error_first_name != '' || error_mobile_no != '' || error_gender != '' || error_folk_form != '' || error_dob != '' || error_district != '' || error_urban_code != '' || error_block != '' || error_name_of_bank !='' || error_bank_branch !=''||  error_bank_account_number !='' || error_bank_ifsc_code !=''){
       
         return false;
       }
       else{
          $(".modal-submit").hide();
         $("#submitting").show();
         $("#submit_loader").show();
           
       }
    
});

 $('#bank_ifsc_code').change(function(){
  $('#name_of_bank').val('');
          $('#bank_branch').val('');
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
        url: "{{ url('legacy/getBankDetails') }}",
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
          alert('Something Wrong..may be session time out..please login again.');
           window.location.href=base_url;
        }
      });

    }else{
      $('#error_bank_ifsc_code').text('IFSC format invalid please check the code');
      $('#bank_ifsc_code').addClass('has-error');
    }
 });

});

// function setMaxage(pension_code){
//  if(pension_code==3){
//  document.getElementById("dob").setAttribute("max", '2020-01-01');
//  }
//  else{
   
//   document.getElementById("dob").setAttribute("max", '1960-01-01');
//  }
// }
</script>
</body>
</html>


