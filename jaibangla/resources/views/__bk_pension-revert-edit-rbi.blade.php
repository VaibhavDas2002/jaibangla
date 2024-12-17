<?php 

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>
  </title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />

  <!-- Font Awesome -->
  <link href="{{ asset("/css/font-awesome.min.css")}}" rel="stylesheet" type="text/css" />
  
  <!-- Ionicons -->
  <link href="{{ asset('css/ionicons.min.css') }}" rel="stylesheet">
 
  <!-- Theme style -->
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
   <link href="{{ asset("css/select2.min.css") }}" rel="stylesheet">
 
   <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet" type="text/css" />  
     
   <style>
   .errorField{
    border-color: #990000;
  }
  .searchPosition{
    margin:70px;
  }
  .submitPosition{
    margin: 25px 0px 0px 0px;
  }

  .criteria1{
    text-transform: uppercase;
    font-weight: bold;
  }
  
  #example_length{
    margin-left: 40%;
    margin-top: 2px;
  }
 

.select2{
    width:100%!important;
  }
  .select2 .has-error {
    border-color:#cc0000;
   background-color:#ffff99;
}

  /**{
    font-size: 15px;
  }*/

.field-name{
  float:left;
  font-weight:600;
  font-size:17px;
  margin-right:3%;
  padding-top:1%;
}
.field-value{
  
  
  font-size:17px;
  padding-top:1%;
  
}

.row{
  margin-right: 0px!important;
  margin-left: 0px!important;
}
.section1{
    border: 1.5px solid #9187878c;
    overflow: hidden;
    padding-bottom: 10px;
   
   
}
.color1{
  
  background-color: #dcdfdf;
}
.color1 h3{
margin: 10px 0px 10px 0px !important;
}

.setPos{
  padding: 0px 0px 10px 0px;
  margin: 10px 0px 10px 0px;
  border:1px solid #dcdfdf;
  overflow: hidden;
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

.modal-header{
  background-color: #7fffd4;
}
.bank{
  margin-bottom:10px;
}
@media print {
  .example-screen {
       display: none;
    }

    *{
    font-size: 15px;
  }

.field-name{
  float:left;
  font-weight:600;
  font-size:17px;
  margin-right:3%;
  padding-top:1%;
}
.field-value{
  
  
  font-size:17px;
  padding-top:1%;
  
}

.row{
  margin-right: 0px!important;
  margin-left: 0px!important;
}
.section1{
    border: 1.5px solid #9187878c;
    overflow: hidden;
    padding-bottom: 10px;
   
   
}
.color1{
  
  background-color: #dcdfdf;

}
.color1 h3{
 margin: 10px 0px 10px 0px !important;
}

.setPos{
  padding: 0px 0px 10px 0px;
  margin: 10px 0px 10px 0px;
  border:1px solid #dcdfdf;
  overflow: hidden;
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

.modal-header{
  background-color: #7fffd4;
}

</style>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <!-- Main Header -->
@include('layouts.header')
<!-- Sidebar -->
@include('layouts.sidebar')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
      <!-- Content Header (Page header) -->
  <section class="content-header">
    @if(count($errors) > 0)
      <div class="alert alert-danger alert-block">
        <ul>
        @foreach($errors->all() as $error)
          <li><strong> {{ $error }}</strong></li>
        @endforeach
        </ul>
      </div>
    @endif
    <div class="modal-fade" tabindex="-1" role="document">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="example-screen">
               <!--  <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
                <!-- <span aria-hidden="true">&times;</span> -->
               <!-- </button> -->
               <h2 class="modal-title " style="text-align: center;">Update Bank Details </h2>  
            </div>
            <div class="modal-body">
              <!--   <h4 class="example-screen" style="text-align: center;">Please Verify or Reject Employee's application with Comments</h4> -->
                

              <!-- We display the details entered by the user here -->
              <div class="section1">
                <div class="row">
                  <div class="col-md-12">
                    <h3 style="text-align: center; color:red;">Application ID:{{$row->getBenidAttribute()}}
                      
                    </h3>
                  </div>
                </div>
                <div class="row color1">
                  <div class="col-md-12"><h3>Personal Details</h3></div>
                </div>
                <div class="row">
                    <div class="col-md-6" >
                      <div class="col-md-6" ><strong>Name :</strong></div>
                      <div class="col-md-6" >{{$row->ben_fname}} {{$row->ben_mname}} {{$row->ben_lname}}
                      </div>
                    </div>
                     <!-- <img id="blah" src="{{ asset($row->passport_image) }}" alt=""  width="200px" height="200px" />

                      <img src="{{ url('storage/'.$row->passport_image) }}" alt="" title="" /> -->

                      <!--  <img src="{{ asset('upload/'.$row->passport_image) }}" alt="" width="200px" height="200px" /> -->

                    <div class="col-md-6">
                        <div class="col-md-6" ><strong>Gender:</strong></div>
                        <div class="col-md-6" >{{$row->gender}}</div>    
                    </div>
                    <div class="col-md-6">
                      <div class="col-md-6" ><strong>Date of Birth :<br>(DD-MM-YYYY)</strong></div>
                      <div class="col-md-6" >{{ date('d/m/Y', strtotime($row->dob))}}</div>  
                    </div>

                    <div class="col-md-6" >
                     <div class="col-md-6" ><strong>Father's Name :</strong></div>
                     <div class="col-md-6" >{{$row->father_fname}} {{$row->father_mname}} {{$row->father_lname}}
                     </div>
                    </div>

                    <div class="col-md-6">
                     <div class="col-md-6" ><strong>Caste:</strong></div>
                     <div class="col-md-6" >{{$row->caste}}</div>
                    </div>                       
                </div>
 
                <div class="row ">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>Bank Details</h3></div>
                </div>
                <form method="post" action="{{ route('revert-update-rbi')}}">
                {{ csrf_field() }}
                <div class="col-md-6 bank">
                  <div class="row">
                  <div class="col-md-6"><strong>Mobile Number:</strong></div>
                    <div class="col-md-6"><input type="text" value="{{$row->mobile_no}}" name="mobile_no" maxlength="10" id="mobile_no">
                      <span id="error_mobile_no" class="text-danger"></span>
                    </div>
                    
                  </div>
                </div>

                <div class="col-md-6 bank">
                  <div class="row">
                  <div class="col-md-6"><strong>Bank Name:</strong></div>
                    <div class="col-md-6"><input type="text" value="{{$row->bank_name}}" name="bank_name" maxlength="200" id="bank_name">
                      <span id="error_name_of_bank" class="text-danger"></span>
                    </div>
                    
                  </div>
                </div>
                <!-- </div> -->

                <div class="col-md-6 bank">
                  <div class="row">
                  <div class="col-md-6"><strong>Bank Branch Name:</strong></div>
                    <div class="col-md-6">
                      <input type="text" value="{{$row->branch_name}}" name="branch_name" id="branch_name">
                      <span id="error_bank_branch" class="text-danger"></span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6 bank">
                  <div class="row">
                  <div class="col-md-6"><strong>Bank Account Number:</strong></div>
                    <div class="col-md-6">
                      <input type="text" value="{{$row->bank_code}}" name="bank_account_number" maxlength='16' id="bank_account_number">
                      <span id="error_bank_account_number" class="text-danger"></span>
                    </div>
                  </div>
                </div>

                <div class="col-md-6 bank">
                  <div class="row">
                  <div class="col-md-6"><strong>Bank IFSC Code:</strong></div>
                    <div class="col-md-6">
                      <input type="text" value="{{$row->bank_ifsc}}" name="bank_ifsc" onkeyup="this.value = this.value.toUpperCase();" id="bank_ifsc">
                      <span id="error_bank_ifsc_code" class="text-danger"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">        
            <input type="hidden" name="benId" value="{{$row->id}}">
                      
                <div class="section2  example-screen"style="margin-bottom: 10px;">
                  <div class="row">                
                    <div class="col-md-12" style="text-align: center;"><input type="submit" name="submit" value="Update" id="Verifysubmit" class="btn btn-success btn-lg"></div>
                  </div>
                </div>
              </form>
                 <!--     <div class="text-center example-screen" style="margin-top: 10px;"><button style="width:25%;"class="btn btn-success btn-lg" onclick="printfunction()">Print</button></div> -->  
            </div>               
         </div>
        </div>
    </div>
  </section>
</div>
</div>

</body>

<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset("js/select2.full.min.js") }}"></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<script src="{{ URL::asset('js/site.js') }}"></script>



<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>

<script type="text/javascript">
  $(document).ready(function(){
    $('#Verifysubmit').click(function(){     
  
      var error_name_of_bank =''; 
      var error_bank_branch =''; 
      var error_bank_account_number =''; 
      var error_bank_ifsc_code =''; 
      var error_mobile_no ='';

      if($.trim($('#mobile_no').val()).length == 0)
      {
       error_mobile_no = 'Mobile Number is required';
       $('#error_mobile_no').text(error_mobile_no);
       $('#mobile_no').addClass('has-error');
      }
      else if($.trim($('#mobile_no').val()).length !=10)
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

      if($.trim($('#bank_name').val()).length == 0)
      {
       error_name_of_bank = 'Name of Bank is required';
       $('#error_name_of_bank').text(error_name_of_bank);
       $('#bank_name').addClass('has-error');
      }
      else
      {
       error_name_of_bank = '';
       $('#error_name_of_bank').text(error_name_of_bank);
       $('#bank_name').removeClass('has-error');
      }

      if($.trim($('#branch_name').val()).length == 0)
      {
       error_bank_branch = 'Bank Branch is required';
       $('#error_bank_branch').text(error_bank_branch);
       $('#branch_name').addClass('has-error');
      }
      else
      {
       error_bank_branch = '';
       $('#error_bank_branch').text(error_bank_branch);
       $('#branch_name').removeClass('has-error');
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

      if($.trim($('#bank_ifsc').val()).length == 0)
      {
       error_bank_ifsc_code = 'IFS Code is required';
       $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
       $('#bank_ifsc').addClass('has-error');
      }
      else
      {
       error_bank_ifsc_code = '';
       $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
       $('#bank_ifsc').removeClass('has-error');
      }

      $ifsc_data = $.trim($('#bank_ifsc').val());
      $ifscRGEX = /^[a-z]{4}0[a-z0-9]{6}$/i;
      if($ifscRGEX.test($ifsc_data))
      {
        error_bank_ifsc_code = '';
        $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
        $('#bank_ifsc').removeClass('has-error');
      }
      else{
        error_bank_ifsc_code = 'Please check IFS Code format';
        $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
        $('#bank_ifsc').addClass('has-error');    
      }

      
       if(error_name_of_bank !='' || error_bank_branch !=''||  error_bank_account_number !='' || error_bank_ifsc_code !='')
        // if(error_name_of_bank !='' )
      {
       return false;
      }
      else
      {
        
        return true;
      }

     });
  });
</script>

</html>
<!-- <script>
function printfunction() {
  // var content=document.getElementById('divToPrint');
  // window.document.write('<html><head><style>.row{ margin-right: 0px!important; margin-left: 0px!important; margin-top: 1%!important;}.section1{border:1.5pxsolid#9187878c;margin:2%;padding:2%;}.color1{margin:0%!important;background-color: #5f9ea061;}.modal_field_name{ float:left;font-weight: 700;margin-right:1%;padding-top:1%;margin-top:1%;}.modal_field_value{margin-right:1%;padding-top:1%;margin-top:1%;}</style></head><body>'+content.innerHTML+'</body></html>');
  window.print();
}
</script> -->