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
  <!-- Font Awesome -->
 <!--  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"> -->
  <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">

  <!-- Select2 -->
 
  <!-- Ionicons -->
  <!--link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"-->
  <link href="{{ asset('css/ionicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />
  <link href="{{ asset("/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css")}}" rel="stylesheet" type="text/css" />
  <link href="{{ asset("/bower_components/AdminLTE/plugins/datepicker/datepicker3.css")}}" rel="stylesheet" type="text/css" />
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/select2/select2.min.css")}}">
  <!-- Theme style -->
  <link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
  <link href="{{ asset("/bower_components/AdminLTE/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css" />
  <link href="{{ asset('css/app-template.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/iCheck/flat/blue.css")}}">
  <link rel="stylesheet" href="{{ asset ("/bower_components/AdminLTE/dist/css/jquery.fancybox.css") }}"  type="text/css" >
  <link rel="stylesheet" href="{{ asset ("/bower_components/AdminLTE/dist/css/prettyPhoto.css") }}"  type="text/css" >
  <link rel="stylesheet" href="{{ asset("/bower_components/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css")}}">
  <link rel="stylesheet" href="{{ asset("/css/jquery.dataTables.min.css")}}">
  <link rel="stylesheet" href="{{ asset("/css/buttons.dataTables.min.css")}}">
  <style type="text/css">
    .required-field::after {
        content: "*";
        color: red;
    }

    .preloader1 {
        position: fixed;
        top: 40%;
        left: 52%;
        z-index: 999;
    }

    .preloader1 {
        background: transparent !important;
    }

    .panel-heading {
        padding: 0;
        border: 0;
    }

    .panel-title>a,
    .panel-title>a:active {
        display: block;
        padding: 10px;
        color: #555;
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        word-spacing: 3px;
        text-decoration: none;
    }

    .panel-heading a:before {
        font-family: 'Glyphicons Halflings';
        content: "\e114";
        float: right;
        transition: ALL 0.5s;
    }

    .panel-heading.active a:before {
        -webkit-transform: rotate(180deg);
        -moz-transform: rotate(180deg);
        transform: rotate(180deg);
    }

    #enCloserTable tbody tr td {
        padding: 10px 10px 10px 10px;
    }

    .modal-open {
        overflow: visible !important;
    }

    fieldset.scheduler-border {
        border: 1px groove #ddd !important;
        padding: 0 10px 10px 10px !important;
        margin: 0 0 15px 0 !important;
        -webkit-box-shadow: 0px 0px 0px 0px #000;
        box-shadow: 0px 0px 0px 0px #000;
        background-color: #ECF0F5;
        border-radius: 10px;
    }

    legend.scheduler-border {
        font-size: 1.1em !important;
        font-weight: bold !important;
        text-align: left !important;
        width: auto;
        padding: 0 20px;
        border-bottom: none;
        font-style: italic;
        border: 1px groove #ddd;
        border-radius: 15px;
        margin-bottom: 10px;
        background-color: #F0FFFF;
    }

    table,
    th,
    td {
        border: 1px solid #b8b8b8;
        border-collapse: collapse;
    }

    #table-header {
        background: #b8b8b8;
        color: #1d1d1d;
        text-transform: uppercase;
        font-size: 14px;
        font-weight: bold;
    }

    .radio-span {
        padding-left: 5px;
        font-size: 15px;
        font-weight: 600;
    }
</style>

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
      <section class="content">
        <section class="content-header">
          
          <div class='row'>
            @if ( ($message = Session::get('message')))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
              </div>
            @endif
            @if ($message = Session::get('success'))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
              </div>
            @endif
            @if ( ($error = Session::get('error')))
              <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $error }}</strong>
              </div>
            @endif
            @if(count($errors) > 0)
              <div class="alert alert-danger alert-block">
                <ul>
                  @foreach($errors as $error)
                    <li><strong> {{ $error }}</strong></li>
                  @endforeach
                </ul>
              </div>
            @endif
          </div> 
        </section>
         

           
        <div class="row pull-right">
          
          </div>

          <div class="box box-primary">
            <div class="box-header with-border">
              <span style="font-size: 18px; font-weight: bold;">Mark Cmo Grivance Applications</span>
              <span style="float: right;">
                @if($designation_id=='Verifier')
                <a href="mark-sm-cmo?scheme_id={{$scheme_id}}"> 
                  <img width="50px;" style="pull-right" src="{{ asset("images/back.png") }}" alt="Back" /></a>
                  @endif
                  @if($designation_id=='Operator')
                  <a href="oap-wcd?pr1=wcd&wcd_type={{$scheme_id}}&cmo_id={{$cmo_id}}"> 
                    <img width="50px;" style="pull-right" src="{{ asset("images/back.png") }}" alt="Back" /></a>
                  @endif
              </span>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-12">
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border">Cmo Grievance Details</legend>
                        <div class="row">
                          <div class="col-md-3" >
                            <div ><strong>Name :</strong> {{$row->ben_name}}</div>
                          </div>
                          <div class="col-md-3" >
                            <div ><strong>Mobile Number :</strong> {{$row->sm_mobile_no}}</div>
                          </div>      
                      
                      
                      
                        <div class="col-md-3" >
                          <div ><strong>District :</strong> {{$row->dist_name}}</div>
                        </div>
                        <div class="col-md-3" >
                          <div ><strong>Block/Municipality :</strong> {{$row->block_ulb_name}}</div>
                        </div>      
                        </div>
                     
                    </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border">Search Type</legend>
                        <form method="post"  action="{{url('checkCmoEnCode')}}" 
                      class="submit-once" name="form" id="form-btn-processed">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                   <input type="hidden" id="scheme_id" name="scheme_id" value="{{$scheme_id}}">
                   <input type="hidden" id="cmo_id" name="cmo_id" value="{{$cmo_id}}"/>
                        <div class="row">
                        <div class="form-group col-md-3">
                          <label class="">Search By</label>
                          <select name="search_by_key" id="search_by_key" class="form-control" tabindex="6"  @if($designation_id=='Operator') readonly @endif>
                           <option value="1" @if($search_by_key==1) selected @endif>Cmo Grievance Mobile Number</option>
                           <option value="2" @if($search_by_key==2) selected @endif>Applicant Mobile Number</option>
                           <option value="3" @if($search_by_key==3) selected @endif>Applicant Beneficiary Id</option>
                           <option value="4" @if($search_by_key==4) selected @endif>Applicant Aadhaar Number</option>
                           <option value="5" @if($search_by_key==5) selected @endif>Applicant Bank Account Number</option>
         
                         </select>
                          <span id="error_search_by_key" class="text-danger"></span>
         
                         </div>
                         <div class="form-group col-md-3">
                                       <label class="required-field" id="searh_key_label">{{$search_by_key_label}}</label>
                              <input type="text" name="search_by_value" id="search_by_value" class="form-control"  @if($designation_id=='Operator') readonly @endif
                               value="{{$search_by_value}}"  maxlength='20' @if($search_by_key==1) disabled @endif;/>
                              <span id="error_search_by_value" class="text-danger"></span>
                             
                         </div>
                         @if($designation_id=='Verifier')
                         <div class="form-group col-md-3">
                          <button type="submit"  class="btn btn-info danger" id="filter_searh" style="margin-top:20px;">Search</button>
                          <button type="button" id="searching_loader" value="Submit" class="btn btn-danger btn-lg"
                                   disabled style="display:none;">Searching please wait</button>
                            </div>
                        </div> 
                        @endif 
                      </form>      
                    </fieldset>
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border">Search Result</legend>
                        @if(count($match_ben_list)>0)
                        <div style="text-align: center; align-content: center;">
                          <span class="label label-success" style="font-size: 13px;">Match found. Below are the probable matched lists</span>
                        </div>
                        
                        <table id="example" class="display" cellspacing="0" width="100%"> 
  
                          <thead>
                  
                                  <tr>
                                  <th>Beneficiary ID</th>
                                  <th>Beneficiary Name</th>
                                  <th>Aadhaar Number</th>
                                  <th>Mobile Number</th>
                                  <th>DOB</th>
                                  <th>Block/Munc Name</th>
                                  <th >GP/Ward Name</th>  
                                  <th>Action</th>
                                  
                                </tr>
                              </thead>
                              <tbody>
                  
                              
                               
                                @foreach ($match_ben_list as $dup_item)
                                  <tr>
                                  <td>{{$dup_item->id}}</td>
                                  <td>{{$dup_item->ben_fname}} {{$dup_item->ben_mname}} {{$dup_item->ben_lname}}</td>
                                  <td>{{$dup_item->aadhar_no}}</td>
                                  <td>{{$dup_item->mobile_no}}</td>
                                  <td>{{$dup_item->dob}}</td>
                                  <td>{{trim($dup_item->block_ulb_name)}}</td>
                                  <td>{{trim($dup_item->gp_ward_name)}}</td>
                                  @if ($dup_item->created_by_dist_code==$created_by_dist_code && $dup_item->created_by_local_body_code==$created_by_local_body_code)
                                  <td><a href="application-details-read_only/{{$dup_item->id}}?scheme_id={{$scheme_id}}"  target="_blank" class="btn btn-xs btn-info" >View Applicant Details</a>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-sm" value="{{$dup_item->id}}" id="btn-sm-{{$dup_item->id}}">Match Found and Mark as SM </button></td>
                                  @else
                                  <td><p>Related to Other District:{{$dup_item->district_name}} and Block/Sub Division:{{$dup_item->block_sub_district_name}}</td>
                                  @endif
        
                                  
                                  </tr>
                                @endforeach
                                
        
                               
                  
                                 
                              
                              </tbody>
                              <!-- <tfoot> -->
                             
                             
                              <!-- </tfoot> -->
                  
                              
                            
                            
                      </table>
                      @else
                      <span class="label label-danger" style="font-size: 13px;">No Match found</span>
                      <button type="button"  class="btn btn-warning btn-lg" id="newEntry" style="margin-top:20px; margin-left: 380px;"
                            >Sent to Operator for New Entry</button>
                      @endif
                    </fieldset>
  
                  
                </div>
  
            </div>
            </div>
          </div>
          

      </section>
      <div id="modalConfirm" class="modal fade">
        <div class="modal-dialog modal-confirm">
          <div class="modal-content">
            <div class="modal-header flex-column">
            </div>
            <div class="modal-body">
              <form method="post"  action="{{url('MarkSmCmoPost')}}" 
                      class="submit-once" name="form" id="sm-form">
                <input type="hidden" id="scheme_id" name="scheme_id" value="{{$scheme_id}}">
                <input type="hidden" id="beneficiary_id" name="beneficiary_id"/>
                <input type="hidden" id="cmo_id" name="cmo_id" value="{{$cmo_id}}"/>

                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <h4 class="modal-title w-100">Do you really want to Mark as Sarasori Mukhyamantri for the applicantion(<span id="application_text_approve"></span>)?</h4>	
               
                <div class="modal-footer justify-content-center">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btn-sm-cancel">Cancel</button>
                  <button type="submit" class="btn btn-info" id="confirm_yes" >Mark as Sarasori Mukhyamantri</button>
                  <button style="display:none;" type="button" id="submittingapprove" value="Submit" class="btn btn-success btn-lg"
                            disabled>Submitting please wait</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div id="modalNewEntry" class="modal fade">
        <div class="modal-dialog modal-confirm">
          <div class="modal-content">
            <div class="modal-header flex-column">
            </div>
            <div class="modal-body">
              <form method="post"  action="{{url('SmPostNewEntry')}}" 
                      class="submit-once" name="form" id="sm-form">
                <input type="hidden" id="scheme_id" name="scheme_id" value="{{$scheme_id}}">
                <input type="hidden" id="cmo_id" name="cmo_id" value="{{$row->id}}"/>

                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <h4 class="modal-title w-100">Do you really want to Send the Appliation to Operator for New Entry</h4>	
                <div class="form-group col-md-12">
                              <label class="required-field">Applicant New Mobile Number</label>
                     <input type="text" name="new_mobile_no" id="new_mobile_no" class="form-control" 
                     value="{{$row->sm_mobile_no}}"  maxlength='10'/>
                     <span id="error_new_mobile_no" class="text-danger"></span>
                     
                </div>
                <br/>
                <div class="modal-footer justify-content-center">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btn-sm-cancel">Cancel</button>
                  <button type="submit" class="btn btn-info" id="confirm_new_entry" >Sent to Operator for New Entry</button>
                  <button style="display:none;" type="button" id="loader_NewEntry" value="Submit" class="btn btn-success btn-lg"
                            disabled>Submitting please wait</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- Main content -->
      <!--  <section class="content">

      Your Page Content Here



    </section> -->
      <!-- /.content -->
    </div>

    @include('layouts.footer')
    <script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
    <script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js") }}" type="text/javascript" ></script>
    <script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js") }}" type="text/javascript" ></script>
    <script>
    $(document).ready(function(){
      $("#submittingapprove").hide();
      $("#rejectingapprove").hide();
      $("#revertingapprove").hide();
      $(".NumOnly").keyup(function(event) {
                  
                  $(this).val($(this).val().replace(/[^\d].+/, ""));
                      if ((event.which < 48 || event.which > 57)) {
                          event.preventDefault();
                      }
        }); 
        $(document).on('change', '#search_by_key', function() {
          var val=$(this).val();
          if(val==1){
             $("#search_by_value").val($("#old_sm_mobile_no").val());
             $("#search_by_value").attr("disabled", true);
             $("#searh_key_label").text('Cmo Grievance Mobile Number');
          }
          else if(val==2){ 
             $("#search_by_value").val('');
             $("#search_by_value").removeAttr("disabled");
             $("#searh_key_label").text('Applicant Mobile Number');

          }else if(val==3){ 
            $("#search_by_value").val('');
             $("#search_by_value").removeAttr("disabled");
             $("#searh_key_label").text('Applicant Beneficiary Id');
          }
          else if(val==4){ 
            $("#search_by_value").val('');
             $("#search_by_value").removeAttr("disabled");
             $("#searh_key_label").text('Applicant Aadhaar Number');

          }else if(val==5){ 
            $("#search_by_value").val('');
             $("#search_by_value").removeAttr("disabled");
             $("#searh_key_label").text('Applicant Bank Account Number');
          }
        });  
        $('#example').dataTable({
        "paging": false,
        "ordering": false,
        });
      $(document).on('click', '.btn-sm', function() {
      $('#sm-form #beneficiary_id').val('');
      $('.btn-sm').attr('disabled',false);
      var benid=$(this).val();
      $('#btn-sm-'+benid).attr('disabled',true);
      $('#sm-form #beneficiary_id').val(benid);
      $('#application_text_approve').text(benid);
      $('#modalConfirm').modal();
     });  
     $(document).on('click', '#newEntry', function() {
      
      $('#modalNewEntry').modal();
    });  
    $('#btn-sm-cancel').click(function() {
      $('.btn-sm').attr('disabled',false);
     });
     $('#confirm_yes').on('click',function(e){
        e.preventDefault();
        $("#confirm_yes").hide();
        $("#submittingapprove").show();
        $("#sm-form").submit();    
      }); 
      $('#filter_searh').on('click',function(e){
        e.preventDefault();
        var error_search_by_value =''; 
        var search_by_key=$('#search_by_key').val();
        if(search_by_key!=1)
        {

        
        if($.trim($('#search_by_value').val()).length == 0)
        {
          error_search_by_value = 'This field is required';
          $('#error_search_by_value').text(error_search_by_value);
          $('#search_by_value').addClass('has-error');
        }
        else
        {
          
          error_search_by_value = '';
          $('#error_search_by_value').text(error_search_by_value);
          $('#search_by_value').removeClass('has-error');

          
        }
      }
        if(error_search_by_value!=''){
          return false;
        }
        else{
          $("#filter_searh").hide();
          $("#searching_loader").show();
          $("#form-btn-processed").submit();   
        }
      
      });   
    });
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