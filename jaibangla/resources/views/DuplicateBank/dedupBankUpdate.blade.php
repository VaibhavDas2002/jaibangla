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
  <link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet"
    type="text/css" />
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
  <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/skin-blue.min.css")}}" rel="stylesheet"
    type="text/css" />

  <!-- bootstrap wysihtml5 - text editor -->
  <!-- <link rel="stylesheet" href="{{ asset("/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}"> -->

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <style>
    .box {
      width: 800px;
      margin: 0 auto;
    }

    .active_tab1 {
      background-color: #fff;
      color: #333;
      font-weight: 600;
    }
   .active_tab2 {
      background-color: #f5f5f5;
      color: #333;
      cursor: pointer;
    }
    .inactive_tab1 {
      background-color: #f5f5f5;
      color: #333;
      cursor: not-allowed;
    }

    .has-error {
      border-color: #cc0000;
      background-color: #ffff99;
    }

    .select2 {
      width: 100% !important;
    }

    .select2 .has-error {
      border-color: #cc0000;
      background-color: #ffff99;
    }

    .modal_field_name {
      float: left;
      font-weight: 700;
      margin-right: 1%;
      padding-top: 1%;
      margin-top: 1%;
    }

    .modal_field_value {
      margin-right: 1%;
      padding-top: 1%;
      margin-top: 1%;
    }

    .row {
      margin-right: 0px !important;
      margin-left: 0px !important;
      margin-top: 1% !important;
    }

    .section1 {
      border: 1.5px solid #9187878c;
      margin: 2%;
      padding: 2%;
    }

    .color1 {
      margin: 0% !important;
      background-color: #5f9ea061;
    }

    .modal-header {
      background-color: #7fffd4;
    }

    .required-field::after {
      content: "*";
      color: red;
    }

    .imageSize {
      font-size: 9px;
      color: #333;
    }
    .btnEnc {
      margin-top:10px;
    }
    
  </style>


</head>
<?php 
use Illuminate\Support\Facades\Input;

?>
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
            <div>

           

              <div>
               
                
                @if ( ($message = Session::get('success')) && ($id =Session::get('id')))
                <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }} with Application ID: {{$id}}</strong>
               
               
                </div>
               @endif

             
    
               
                 @if ( ($message = Session::get('error')))
                <div class="alert alert-danger alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }}</strong>
                

                </div>
                @endif

                  @if ($errors->any())
                <div class="alert alert-danger alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                   @foreach ($errors->all() as $error)
                  <strong>{{ $error }}</strong><br />
                   @endforeach

                </div>
                @endif


               
                  <div class="alert print-error-msg"  style="display:none;" id="errorDiv">
      <button type="button" class="close"  aria-label="Close" onclick="closeError('errorDiv')"><span aria-hidden="true">&times;</span></button>
      <ul></ul></div>
       <div class="row">
      <div class="col-md-2">
        <a href="{{ url('dedupBankView') }}?bank_code={{$bank_code}}&scheme_id={{$scheme_id}}"><img width="50px;" style="pull-left" src="{{ asset("images/back.png") }}" alt="Back" /></a></div>
              <div class="col-md-8">

                <h3 style="text-align: center;">Beneficiary Id.:<span style="color:red;">{{$application_id}}</span></h3>
                <h3 style="text-align: center;">Revert reason:<span style="color:red;">{{$revert_reason->revert_remarks}}</span></h3>

              </div>


            </div>
                <!--   @if ($message = Session::get('failure'))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
              </div>
              @endif -->
              </div>
              <!-- /.box-header -->
              <!-- form start -->
            
                 <form method="post" id="faulty_form" name="faulty_form" action="{{url('dedupBankUpdatePost')}}" enctype="multipart/form-data" class=""  autocomplete="off">

                {{ csrf_field() }}

                  <input type="hidden" id="scheme_id" name="scheme_id" value="{{ $scheme_id }}">                 
                  <input type="hidden" name="application_id" id="application_id" value="{{ $application_id }}">

                   <input type="hidden" name="old_bank_code" id="old_bank_code" value="{{$bank_code}}">

                 <ul class="nav nav-tabs">

               
                 <!--  <li class="nav-item" id="id_1">
                    <a class="nav-link active_tab2"  id="list_personal_details" ><b>Personal
                        Details</b></a>
                  </li>

                  

                  <li class="nav-item" id="id_2">
                    <a class="nav-link active_tab2" id="list_contact_details"  onclick="return tab_highlight(2)"><b>Contact
                        Details</b></a>
                  </li>
                  <li class="nav-item" id="id_3">
                    <a class="nav-link active_tab2" id="list_bank_details"  onclick="return tab_highlight(3)"><b>Bank
                        Account Details</b></a>
                  </li>
                 
                  <li class="nav-item" id="id_4">
                    <a class="nav-link active_tab2" id="list_experience_details"
                       onclick="return tab_highlight(4)"><b>Enclosure List (Self Attested)</b></a>
                  </li>

                  <li class="nav-item" id="id_5">
                    <a  class="nav-link active_tab2" onclick="return tab_highlight(5)" class="nav-link" id="list_decl_details" ><b>Self
                        Declaration</b></a>
                  </li> -->


                  <!--  <li class="active"><a data-toggle="tab" href="#list_id_details">Personal Identification Number(S)</a></li>
            <li><a data-toggle="tab" href="#list_login_details">Personal Details</a></li>
            <li><a data-toggle="tab" href="#list_personal_details">Contact Details</a></li>
            <li><a data-toggle="tab" href="#list_contact_details">Bank Account Details</a></li>
            <li><a data-toggle="tab" href="#list_experience_details">Enclosure List(Self Attested)</a></li> -->
                </ul>



                <div class="tab-content" style="margin-top:16px;">
              
                


                 
                  <div class="tab-pane active" id="personal_details">
                    <div class="panel panel-default">
                      <div class="panel-heading">
                        <h4><b>Update Beneficiary for Bank Acc No. and IFSC De-Duplication for the Scheme:{{$scheme_name}}</b></h4>
                      </div>
                      <div class="panel-body">
                     
                     <form name="personal" id="personal" method="post" action="">
                     
                       
                 
                        <div class="form-group col-md-12">
                          
                            <label class="">Bank Account Details</label>

                        </div>


                        <div class="row">
                        <div class="form-group col-md-4">
                          <label class="required-field">IFS Code</label>
                          <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control special-char"
                           autocomplete="off" placeholder="IFSC Code" onkeyup="this.value = this.value.toUpperCase();"
                            value="{{ trim($row->bank_ifsc) }}" maxlength='11'  />
                          <span id="error_bank_ifsc_code" class="text-danger"></span>
                        </div>

                        <div class="form-group col-md-4">
                          <label class="required-field">Bank Name</label>
                          <input type="text" name="name_of_bank" id="name_of_bank" class="form-control special-char"
                             placeholder="Bank Name" value="{{ trim($row->bank_name) }}" maxlength="200" 
                            readonly />
                          <span id="error_name_of_bank" class="text-danger"></span>
                        </div>
                       
                        <div class="form-group col-md-4">
                          <label class="required-field">Bank Branch Name</label>
                          <input type="text" name="bank_branch" id="bank_branch" class="form-control special-char"
                             placeholder="Bank Branch Name" value="{{ trim($row->branch_name) }}" maxlength="300" 
                            readonly />
                          <span id="error_bank_branch" class="text-danger"></span>
                        </div>

  </div>
   <div class="row">

                        <div class="form-group col-md-4">
                          <label class="required-field">Bank Account Number</label>
                          <input type="password" name="bank_account_number" id="bank_account_number"
                            class="form-control NumOnly" placeholder="Bank Account No"
                              autocomplete="off" value="{{ trim($row->bank_code) }}" maxlength='16'  />
                          <span id="error_bank_account_number" class="text-danger"></span>

                        </div>
                         

                        
                          <div class="form-group col-md-4">
                          <label class="required-field">Confirm Bank Account Number</label>
                          <input type="text" name="confirm_bank_account_number" id="confirm_bank_account_number"
                            class="form-control NumOnly" placeholder="Confirm Bank Account No"
                             autocomplete="off" value="{{ trim($row->bank_code) }}" maxlength='16' />
                          <span id="error_confirm_bank_account_number" class="text-danger"></span>

                        </div>

                   

                         
                         

                         
                       
                          

 <br />
                          


                      
                            
                         <div class="form-group col-md-12">
                         
                            <label class="">Enclosure List (Self Attested)</label>

                        </div>
                         <br />
                          @if(isset($encloser_list))
                          @php $i=0; @endphp
                                    @foreach ($encloser_list as $doc_all)
                                     @if($i==0)
                                    <div class="row">
                                    @endif
                                    <div class="form-group col-md-4">
                                    <label class="fileLable_{{$doc_all['id']}} {{$doc_all['required']==1?'required-field':''}}">{{ $doc_all['doc_name'] }}</label>
                                    <input name="doc_{{$doc_all['id']}}" type="file" id="{{ $doc_all['id'] }}" class="file_{{$doc_all['required']==1?'1':0}}">
                                    <span id="error_doc_{{$doc_all['id']}}" class="text-danger"></span>
                                    <div class="imageSize">(Image type must be {{ $doc_all['doc_type'] }} and image size max {{ $doc_all['doc_size_kb'] }}KB)</div>
                                    <span id="download_{{ $doc_all['id']}}" style="{{$doc_all['can_download']==1?'':'display:none'}}">
                                    &nbsp;&nbsp;<a href="javascript:void(0);" id="docDownload_1"  class="btn btn-danger downloadEncloser btnEnc" onclick="View_encolser_modal('<?php echo $doc_all['doc_name'];?>',<?php echo $doc_all['id'];?>)" >View</a>
                                    </span>
                                   
                                    </div>

                                    @php
                                      $i=$i+1;
                                    @endphp
                                   @if($i>=3)
                                  </div>
                                   @php
                                   $i=0;
                                   @endphp
                                   @endif
                                    @endforeach  
                                    @endif 
                           <hr />

                         



                       
                        

                       
                         <div class="col-md-12" align="center">
                        <div class="form-group col-md-8">
                          <div id="submitBtn"><button type="submit" name="btn_aplply" id="btn_apply"
                            class="btn btn-info btn-lg">Update</button></div>
                        </div>
                        <div class="form-group col-md-2">
                         <div id="submitBtn1"><button type="button" name="btn_aplply1" id="btn_apply1"
                            class="btn btn-success btn-lg">Keep Same</button></div>
                         </div>   
                          <img  style="display:none;" src="{{ asset('images/ZKZg.gif')}}" id="btn_personal_details_loader" width="150px"
                            height="150px">
                            
                        </div>
                         </form>
                          <form method="post" id="faulty_form_same" name="faulty_form_same" action="{{url('dedupBankSamePost')}}" enctype="multipart/form-data" class=""  autocomplete="off">

                    {{ csrf_field() }}

                  <input type="hidden" id="scheme_id" name="scheme_id" value="{{ $scheme_id }}">                 
                  <input type="hidden" name="application_id" id="application_id" value="{{ $application_id }}">

                   <input type="hidden" name="old_bank_code" id="old_bank_code" value="{{$bank_code}}">
                   <input type="hidden" name="old_bank_ifsc" id="old_bank_ifsc" value="{{ trim($row->bank_ifsc)}}">
                   <input type="hidden" name="is_faulty" id="is_faulty" value="{{$is_faulty}}">
                       
                         
                        
                         </form>
                      </div>
                    </div>
                  </div>
                 
                 






                

                 

                


                 





                </div>
                
                
          <!--/.col (left) -->

        </div>

<div class="modal" id="encolser_modal"  role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="encolser_name">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="encolser_content">  </div>
     
           
    </div>
  </div>
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
       <!--  <script src="{{ URL::asset('js/jquery.form.min.js') }}"></script> -->

    <script src="{{ URL::asset('js/validateAdhar.js') }}"></script>

    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript">
    </script>




    <!-- AdminLTE App -->
    <script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
    
    <script src="{{ URL::asset('js/validateAdhar.js') }}"></script>











    <script>
      var specialKeys = new Array();
        specialKeys.push(8); //Backspace
        function IsNumeric(e) {
          //alert()
            var keyCode = e.which ? e.which : e.keyCode
            var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
            document.getElementById("error").style.display = ret ? "none" : "inline";
            return ret;
        }

$(document).ready(function(){
   $('.sidebar-menu li').removeClass('active');
  $('.sidebar-menu #lk-main').addClass("active"); 
  $('.sidebar-menu #dup_bank_code_approved').addClass("active"); 

  $("#btn_personal_details_loader").hide();
  
 
  var base_url='{{ url('/') }}';
 

    $("#submitting").hide();
    $("#submit_loader").hide();
    $("#passport_image_view").hide(); 
    $("#spouse_section").hide(); 

   $( "#bank_account_number,#confirm_bank_account_number" ).on( "copy cut paste drop", function() {
                return false;
        });
   //  $('form.submit-once').submit(function(e){
   //  if( $(this).hasClass('form-submitted') ){
   //      e.preventDefault();
   //      return;
   //  }
   //  $(this).addClass('form-submitted');
   // });

    
    
   

   
  
  
    //$(".submitting").attr("disabled", true);


 


 



    
   

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
  
    //$('.client-js-district').select2({ data: districts });


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
          console.log(data);
          if (!data || data.length === 0 || data == 'null') {
            $('#error_bank_ifsc_code').text('No data found with the IFSC');
            $('#bank_ifsc_code').addClass('has-error');
            $('#error_name_of_bank').html('');
            $('#error_bank_branch').html('');
            $('#name_of_bank').val('');
          $('#bank_branch').val('');
            return;
          }
          else
          {
            data = JSON.parse(data);
         // console.log(data);
          $('#name_of_bank').val(data.bank);
          $('#bank_branch').val(data.branch);
           $('#error_name_of_bank').html('');
          $('#error_bank_branch').html('');
          $('#faulty_form_same #old_bank_ifsc').val($ifsc_data);
          }
        },
        error: function (ex) {
           alert(sessiontimeoutmessage);
           window.location.href=base_url;
        }
      });

    }else{
      $('#error_bank_ifsc_code').text('IFSC format invalid please check the code');
      $('#bank_ifsc_code').addClass('has-error');
    }
 });


   
  $('#btn_apply').click(function(){
  $("#errorDiv").hide();
  $("#errorDiv").find("ul").html('');

  

  
  var error_name_of_bank =''; 
  var error_bank_branch =''; 
  var error_bank_account_number =''; 
  var error_bank_ifsc_code =''; 
  var error_confirm_bank_account_number =''; 

  var error_doc =''; 
  
  var error_same_bank ='';    

  
 

  
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
  if($.trim($('#confirm_bank_account_number').val()).length == 0)
  {
   error_confirm_bank_account_number = 'Confirm Bank Account Number is required';
   $('#error_confirm_bank_account_number').text(error_confirm_bank_account_number);
   $('#confirm_bank_account_number').addClass('has-error');
  }
  else
  {
   error_confirm_bank_account_number = '';
   $('#error_confirm_bank_account_number').text(error_confirm_bank_account_number);
   $('#confirm_bank_account_number').removeClass('has-error');
  }
  if($.trim($('#bank_account_number').val()) != $.trim($('#confirm_bank_account_number').val()))
  {

   error_confirm_bank_account_number = 'Confirm Bank Account Number not Match with Bank Account Number';
   $('#error_confirm_bank_account_number').text(error_confirm_bank_account_number);
   $('#confirm_bank_account_number').addClass('has-error');
  }
  else
  {
   error_confirm_bank_account_number = '';
   $('#error_confirm_bank_account_number').text(error_confirm_bank_account_number);
   $('#confirm_bank_account_number').removeClass('has-error');
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
var old_bank_code=$.trim($('#old_bank_code').val());
var old_bank_ifsc=$.trim($('#old_bank_ifsc').val());
var bank_ifsc_code=$.trim($('#bank_ifsc_code').val());
var bank_account_number=$.trim($('#bank_account_number').val());


if((old_bank_code==bank_account_number) && (old_bank_ifsc==bank_ifsc_code)){
   error_bank_account_number = 'Beneficiary Bank Information has been remain same as before.. please update it';
   $('#error_bank_account_number').text(error_bank_account_number);
   $('#bank_account_number').addClass('has-error');
  }
  else{
    error_bank_account_number = '';
     $('#error_bank_account_number').text(error_bank_account_number);
      $('#bank_account_number').removeClass('has-error');
  }



  




   
 
   $("#faulty_form input[type=file]").each(function() {
      
     var id= $(this).attr("id");
     var className= $(this).attr("class");
     var input_val= $(this).val();
     if(className=='file_1'){
      if($.trim($(this).val()).length == 0)
      {
       $('#error_doc_'+id).text('This field is required');
       $('#doc_'+id).addClass('has-error');
       error_doc=1;
      }
      else
      {
       
          $('#error_doc_'+id).text('');
          $('#doc_'+id).removeClass('has-error');
        
      }
       
     }
     });
 
  if( error_name_of_bank != '' || error_bank_branch != '' || error_bank_account_number != '' || error_bank_ifsc_code != '' || error_confirm_bank_account_number != '' || error_doc==1 )

  //if( error_first_name !=''  )
  {
     $("html, body").animate({ scrollTop: 0 }, "slow");
   return false;
  }
  
   else{
        
     
    $("#btn_personal_details_loader").show();
    $("#submitBtn").hide();
    $("#submitBtn1").hide();
    //$("#faulty_form").submit();      
    // return true;
   }
   

});

 
 
$('#btn_apply1').click(function(){
    $("#btn_personal_details_loader").show();
    $("#submitBtn").hide();
    $("#submitBtn1").hide();
    $("#faulty_form_same").submit();     
   });

 




 

 
  
  
/***************************SD*********************************/
// $('#btn_submit_preview').click(function(){
// $(".modal-submit").show();
// $("#submitting").hide();
// $("#submit_loader").hide();
// $("#confirm-submit").modal("show");
// });
// $('.encloserModal').click(function(){
//  $("#encolser_name").html('');
//  $('#uploadStatus').html('');
//  $('.progress-bar').html('');
//  $("#uploadForm #document_type").val('');
//  $("#uploadForm #is_profile").val('');
//  $('#btn_encolser_loader').hide();
//  var label = $(this).parent().find('label').text();
//  $("#encolser_name").html(label);
//  var id= $(this).attr("id");
//  var id_split=id.split('_');
//  //console.log(id_split);
//  $("#uploadForm #document_type").val(id_split[1]);
//  $("#uploadForm #is_profile").val(id_split[2]);
//  $("#encolser_modal").modal("show");

// });


// $("#uploadForm").on('submit', function(e){
//         $('#submitButton').hide();
//         $('#btn_encolser_loader').show();

//         e.preventDefault();
//         var form = $('#uploadForm')[0];
//         var formData = new FormData(form);
//         var add_edit_status=$("#commonfield #add_edit_status").val();
//        // alert(add_edit_status);
//         var scheme_id=$("#commonfield #scheme_id").val();
//         var sws_card_no=$("#commonfield #sws_card_no").val();
//         var source_id=$("#commonfield #source_id").val();
//         var source_type=$("#commonfield #source_type").val();
//         var max_tab_code=$("#commonfield #max_tab_code").val();
//         var application_id=$("#commonfield #application_id").val();
//         formData.append('add_edit_status', add_edit_status);
//         formData.append('scheme_id', scheme_id);
//         formData.append('sws_card_no', sws_card_no);
//         formData.append('source_id', source_id);
//         formData.append('source_type', source_type);
//         formData.append('max_tab_code', max_tab_code);
//         formData.append('application_id', application_id);
//         $.ajax({
//             xhr: function() {
//                 var xhr = new window.XMLHttpRequest();
//                 xhr.upload.addEventListener("progress", function(evt) {
//                     if (evt.lengthComputable) {
//                         var percentComplete = ((evt.loaded / evt.total) * 100);
//                         var percentComplete = Math.ceil(percentComplete);
//                         $(".progress-bar").width(percentComplete + '%');
//                         $(".progress-bar").html(percentComplete+'%');
//                     }
//                 }, false);
//                 return xhr;
//             },
//             type: 'POST',
//             dataType: 'json',
//             url: '{{ url('ajax_faulty_encloser_entry') }}',
//             data: formData,
//             contentType: false,
//             cache: false,
//             processData:false,
//             beforeSend: function(){
//                 $(".progress-bar").width('0%');
//                 //$('#uploadStatus').html('<img   width="50px" height="50px" src="images/ZKZg.gif"/>');
//             },
//              error: function (ex){
//                 //console.log(ex);
//                 $('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');
//                  $('#btn_encolser_loader').hide();
//                  $('#submitButton').show();


//             },
//             success: function(resp){
//               //console.log(resp);
//                 if(resp.return_status==1){
//                    $("#max_tab_code").val(resp.max_tab_code);
//                     var id=$("#uploadForm #document_type").val();
//                     $('#uploadForm')[0].reset();
//                     $('#download_'+id).show();
//                     $('#uploadStatus').html('<p style="color:#28A74B;">File has uploaded successfully!</p>');
//                      //$(".progress-bar").width('0%');

//                 }else if(resp.return_status==0){
//                     $('#uploadStatus').html('<p style="color:#EA4335;">'+resp.return_msg+'</p>');
//                 }
//                   $('#btn_encolser_loader').hide();
//                    $('#submitButton').show();


//             }
//         });
        
        
//     });
  
    
// $('#encolser_modal').on('hidden.bs.modal', function (e) {
//   $("#uploadForm #document_type").val('');
//   $("#uploadForm #is_profile").val('');
//   $(".progress-bar").html('');

// });
// $(".downloadEncloser").click(function(){
//  var id= $(this).attr("id");
//  var id_split=id.split('_');  
//  var application_id=$("#application_id").val();
//   window.open("downaloadEncloser_faulty?id="+id_split[1]+"&is_profile_pic="+id_split[2]+"&application_id="+application_id);
// });


// $('.modal-submit').on('click',function(){

//         $(".modal-submit").hide();
//         $("#submitting").show();
//         $("#submit_loader").show();
       
      
// });




/***************************************************************/
});
 $('#district').change(function() {
      if($(this).val() !=''){
       $('#urban_code').val('');
       $('#block').html('<option value="">--Select--</option>');
       $('#gp_ward').html('<option value="">--Select--</option>');
      }
    }); 
$('#urban_code').change(function() {
      if($(this).val() !=''){
        var district_code=$('#district').val();
        //alert(district_code);
        var rural_urban=$(this).val();
        var error_found=1;
        if(rural_urban==2){
          error_found=0;
          var url='{{ url('masterDataAjax/getTaluka') }}';
        }
        else if(rural_urban==1){
          error_found=0;
          var url='{{ url('masterDataAjax/getUrban') }}';
        }
        
        if(error_found==0){
          //alert('ok');
                $('#block').val('');
                $('#gp_ward').val('');
                $('#error_block').html('<img  src="{{ asset('images/ZKZg.gif') }}" width="50px" height="50px"/>');
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                      district_code: district_code,
                      _token: '{{ csrf_token() }}',
                    },
                    success: function (data) {
                        //console.log(data);
                        var htmlOption='<option value="">--Select--</option>';
                        $.each(data, function (key, value) {
                                  htmlOption+='<option value="'+key+'">'+value+'</option>';
                          });
                        $('#block').html(htmlOption);
                        $('#gp_ward').html('<option value="">--Select--</option>');
                        $('#error_block').html('');
                    },
                    error: function (ex) {
                         alert(sessiontimeoutmessage);
                        window.location.href=base_url;
                    }
                  });
        }
        
      }
    }); 
      $('#block').change(function() {
         if($(this).val() !=''){
        var block_code=$(this).val();
        //alert(block_code);
        var rural_urban= $('#urban_code').val();
        var error_found=1;
        if(rural_urban==2){
          error_found=0;
          var url='{{ url('masterDataAjax/getGp') }}';
        }
        else if(rural_urban==1){
          error_found=0;
          var url='{{ url('masterDataAjax/getWard') }}';
        }
        
        if(error_found==0){
                $('#gp_ward').val('');
                $('#error_gp_ward').html('<img  src="{{ asset('images/ZKZg.gif') }}" width="50px" height="50px"/>');
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                      block_code: block_code,
                      _token: '{{ csrf_token() }}',
                    },
                    success: function (data) {
                       // console.log(data);
                        var htmlOption='<option value="">--Select--</option>';
                        $.each(data, function (key, value) {
                                  htmlOption+='<option value="'+key+'">'+value+'</option>';
                          });
                        $('#gp_ward').html(htmlOption);
                        $('#error_gp_ward').html('');
                    },
                    error: function (ex) {
                         alert(sessiontimeoutmessage);
                        window.location.href=base_url;
                    }
                  });
        }
        
      }
    });  





function View_encolser_modal(doc_name,doc_type){
var application_id= $('#faulty_form #application_id').val();
$('#encolser_name').html('');
$('#encolser_content').html('');
$('#encolser_name').html(doc_name+'('+application_id+')');
$('#encolser_content').html('<img   width="50px" height="50px" src="images/ZKZg.gif"/>');
var url='{{ url('ajaxGetEncloser') }}';

  $.ajax({
            url: url,
            type: "POST",
             data: {
               doc_type: doc_type,
               scheme_id: {{$scheme_id}},
               application_id: application_id,
               _token: '{{ csrf_token() }}',
             },
            }).done(function( data, textStatus, jqXHR ) {
            if(data.return_status){
            $('#encolser_content').html('');
            $('#encolser_content').html(data.htmlText);
            $("#encolser_modal").modal();
            }
             else{
              alert(data.return_msg);
            }
        }).fail(function( jqXHR, textStatus, errorThrown ) {
          $('#encolser_content').html('');
            alert(sessiontimeoutmessage);
           window.location.href=base_url;
        });
}
function printMsg (msg,msgtype,divid) {
            $("#"+divid).find("ul").html('');
            $("#"+divid).css('display','block');
      if(msgtype=='0'){
        //alert('error');
        $("#"+divid).removeClass('alert-success');
        //$('.print-error-msg').removeClass('alert-warning');
        $("#"+divid).addClass('alert-info');
      }
      else{
        $("#"+divid).removeClass('alert-info');
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