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
  <link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />

  <style>
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
.required-field::after {
      content: "*";
      color: red;
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

  /*.row{
  margin-right: 0px!important;
  margin-left: 0px!important;
}
.section1{
    border: 1.5px solid #9187878c!important;
    margin: 0.25cm!important;
    padding: 0.25cm!important;
    page-break-inside : avoid;
}
.color1{
  margin: 0%!important;
  background-color: #5f9ea061!important;
  -webkit-print-color-adjust: exact; 
}
.modal_field_name{
  float:left!important;
  font-weight: 700!important;
  margin-right:0.5cm!important;

}

.modal_field_value{
  padding-top:0.30cm!important;

}
.color1{
  margin: 0%!important;
  background-color: #7fffd4!important;
 -webkit-print-color-adjust: exact; 
}

.modal-header{
  background-color: #7fffd4!important;
 -webkit-print-color-adjust: exact; 
}
#divToPrint{
}*/
}
.btnJb{
  margin:20px;
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
         
             @if ( ($message = Session::get('success')) && ($id =Session::get('lb_id')))
              <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }} with LB Application ID: {{$id}}</strong>
               
               
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
               @foreach($errors as $error)
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
           

            <div class="tab-content" style="margin-top:16px;">

              




             <div class="tab-pane active" id="personal_details">
              <div class="panel panel-default">
               <div class="panel-heading"><h4><b>LB Beneficiary Imported Report </b></h4></div>
               <div class="panel-body">

                @include('pension-details-view.personal_details')
                @include('pension-details-view.personal_identification')
                @include('pension-details-view.contact_details')
                @include('pension-details-view.bank_details')
                @include('pension-details-view.enclosure_list')
                @include('pension-details-view.additional_details')

               <div class="row">
                  <div class="col-md-12">
                   <h3 style="text-align: center; color:red;">LB Application ID:{{$row->lb_application_id}}<a href="{{ route('workflow-lb60', ['scheme_id'=>$row->scheme_id])}}">
                <img width="50px;" style="pull-right" src="{{ asset("images/back.png") }}" alt="Back" /></a></h3>
                </div>
               </div>
               @if($row->back_lb==1 && !is_null($row->lb_dob))
               <div class="row">
                  <div class="col-md-12">
                   <h3 style="text-align: center; color:green;">New DOB:{{date('d/m/Y', strtotime($row->lb_dob)) }}</h3>
                </div>
               </div>
               @endif
              
              
                
               

                    

                     
                    
               
                       
                        
                      

                        

                    


                    
                   
                     
                      @if($row->doc_imported==1 && ($is_verifier_login==1))
                     <div class="row">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>New Details</h3></div>
                      </div>
                      <div class="row">
                <div class="form-group col-md-4">
                 <label class="@if($row->mobile_no=='') required-field @endif">New Mobile Number</label>
                 <input type="text" id="new_mobile_no" name="new_mobile_no" class="form-control NumOnly" placeholder="Mobile No" maxlength="10" value="{{trim($row->mobile_no)}}"   >
                 <span id="error_new_mobile_no" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="@if(trim($row->aadhar_no)=='') required-field @endif">Aadhaar Number</label>
                 <input type="text" name="new_aadhar_no" id="new_aadhar_no" class="form-control NumOnly" placeholder="Aadhar No." maxlength="12" value="{{trim($row->aadhar_no)}}"   />
                 <span id="error_new_aadhar_no" class="text-danger"></span>
                </div>
                <br/>
               </div>
                <div class="row">
                <div class="form-group col-md-4">
                 <label class="">Digital Ration Card Number</label>
                  <div class="row" >
                  <div class="col-md-5" >
                    
                    
                   <!--  <input style="margin-left:-15px; margin-right:-15px;" type="text" name="ration_card_cat" id="ration_card_cat" class="form-control special-char" placeholder="Category" maxlength="5" value="{{ old('ration_card_cat') }}"  tabindex="1" /> -->

                    <select class="form-control " name="ration_card_cat" id="ration_card_cat"  tabindex="1" style="margin-left:-15px;">
                    <option value="">Category</option>
                    @foreach(Config::get('constants.ration_cat') as $key=>$val)
                    <option value="{{$key}}">{{$val}}</option>
                    @endforeach                                          
                    </select>
                   
                  </div>
                  
                  <div class="col-md-7">
                   
                      <input style="margin-left:-15px; margin-right:-15px;" type="text" name="ration_card_no" id="ration_card_no" class="form-control NumOnly" placeholder="Card Number" maxlength="10" value=""  maxlength="10" autocomplete="off">
                      
                  </div>
                
                </div>
                 <span id="error_ration_card_cat" class="text-danger"></span><br />
                 <span id="error_ration_card_no" class="text-danger"></span>
                </div>
                <div class="form-group col-md-4">
                 <label class="">EPIC/Voter Id number</label>
                 <input type="text" name="epic_voter_id" id="epic_voter_id" class="form-control"  placeholder="EPIC/Voter Id.No."  maxlength="20" value="" autocomplete="off"/>
                 <span id="error_epic_voter_id" class="text-danger"></span>
                </div>
               </div>
               @if(trim($row->caste)=='SC' || trim($row->caste)=='ST')
               <div class="row">
               <div class="form-group col-md-4">
                              <label class="required-field">Caste Certificate No.</label>
                           <input type="text" name="caste_certificate_no" id="caste_certificate_no" class="form-control"
                            placeholder="Caste Certificate No." maxlength="200" value="{{trim($row->caste_certificate_no)}}"
                             />
                          <span id="error_caste_certificate_no" class="text-danger"></span>
                </div>
              </div>
               @endif 
             @endif  
              @if($row->doc_imported==1)
                <div class="row">
                                  <div class="col-md-12 color1"  style="margin:10px 0px"><h3>Enclosure List</h3></div>
                                  </div>
                                    @foreach ($encloser_list as $doc_all)
                                    
                                    <div class="form-group col-md-4">
                                   
                                    <label class="fileLable_{{$doc_all['id']}} {{$doc_all['required']==1 && ($is_verifier_login==1)?'required-field':''}}">{{ $doc_all['doc_name'] }}</label>
                                    @if(($is_verifier_login==1))
                                    <div class="imageSize">(Image type must be {{ $doc_all['doc_type'] }} and image size max {{ $doc_all['doc_size_kb'] }}KB)</div>
                                    <button type="button" id="doc_{{ $doc_all['id'] }}" name="encolerModal" class="btn btn-info encloserModal btnEnc" >Upload</button>
                                    @endif
                                    
                                    <span id="download_{{ $doc_all['id']}}" style="{{$doc_all['can_download']==1?'':'display:none'}}">
                                    &nbsp;&nbsp;<button type="button" id="docDownload_{{ $doc_all['id'] }}"  class="btn btn-danger downloadEncloser btnEnc" >Download</button>
                                    </span>
                                    </div>
                                   
                                    @endforeach  
                                    @if(($is_approver_login==1))
                                    @if($row->back_lb==1)
                                    <div class="form-group col-md-4">
                                   
                                    <label class="fileLable_{{$age_supporting->id}}">{{$age_supporting->doc_name}}</label>
                                  
                                   
                                   <span id="download_{{$age_supporting->id}}">
                                   &nbsp;&nbsp;<button type="button" id="docDownload_{{$age_supporting->id}}"  class="btn btn-danger downloadEncloser btnEnc" >Download</button>
                                   </span>
                                   </div>
                                   @endif
                                   @if($row->is_transfer==1 && $row->transfer_to_scheme_id==10)
                                    <div class="form-group col-md-4">
                                   
                                    <label class="fileLable_{{$age_supporting->id}}">{{$reason_order->doc_name}}</label>
                                  
                                   
                                   <span id="download_{{$reason_order->id}}">
                                   &nbsp;&nbsp;<button type="button" id="docDownload_{{$reason_order->id}}"  class="btn btn-danger downloadEncloser btnEnc" >Download</button>
                                   </span>
                                   </div>
                                   @endif
                                    @endif
              @endif     
                      <br/> <br/> <br/> <br/>
                     </div>
          
                  </div>
                  
                <div class="col-md-12" align="center">

                <div class="btn-group">
                @if($fetch_lb==1)
                <button type="button" class="btnJb btn btn-info confirmBtn" id="fetch_document" value="1" op_text="Fetch LB Document from LB">Fetch LB Doc</button>
                @endif
                @if($can_verify==1)
                <button type="button" class="btnJb btn btn-info confirmBtn" id="import_verify" value="5" op_text="Verify and Send it to Approver for Approval">Import & Verify</button>
                @endif
                @if($back_to_lb==1)
                <button type="button" class="btnJb btn btn-danger confirmBtn" id="back_to_lb" value="7" op_text="@if(($is_verifier_login==1)) Send Request to Approver for Back to LB @elseif(($is_approver_login==1)) Approved request for Back to LB @endif"> 
                  @if(($is_verifier_login==1))
                  Request for Back to LB
                  @elseif(($is_approver_login==1))
                  Approve Request for Back to LB
                  @endif</button>
                @endif
                @if($can_approve==1)
                <button type="button" class="btnJb btn btn-success confirmBtn" id="back_to_lb" value="50" op_text="@if(($is_verifier_login==1)) Send Request to Approver for Approval @elseif(($is_approver_login==1)) Approved @endif">Approve</button>
                @endif
                @if($transfer_st==1)
                <button type="button" class="btnJb btn btn-primary confirmBtn" id="back_to_st" value="70" op_text="@if(($is_verifier_login==1)) Send Request to Approver for Transfer to Jai Johar @elseif(($is_approver_login==1)) Approved request for Transfer to Jai Johar @endif">
                 @if(($is_verifier_login==1))
                  Request for Transfer to Jai Johar
                  @elseif(($is_approver_login==1))
                 Approve Request for Transfer to Jai Johar
                  @endif
                 </button>
                @endif
                @if($transfer_sc==1)
                <button type="button" class="btnJb btn btn-primary confirmBtn" id="back_to_sc" value="75" op_text="@if(($is_verifier_login==1)) Send Request to Approver for Transfer to Bandhu @elseif(($is_approver_login==1)) Approved request for Transfer to Bandhu @endif"> 
                  @if(($is_verifier_login==1))
                  Request for Transfer to  Bandhu
                  @elseif(($is_approver_login==1))
                 Approve Request for Transfer to Bandhu
                  @endif</button>
                @endif
                @if($transfer_oap==1)
                <button type="button" class="btnJb btn btn-primary confirmBtn" id="back_to_oap" value="80" op_text="@if(($is_verifier_login==1)) Send Request to Approver for Transfer to OAP @elseif(($is_approver_login==1)) Approved request for Transfer to OAP @endif">@if(($is_verifier_login==1))
                  Request for Transfer to  OAP
                  @elseif(($is_approver_login==1))
                 Approve Request for Transfer to OAP
                  @endif</button>
                @endif
                @if($undo==1)
                <button type="button" class="btnJb btn btn-info confirmBtn" id="revert" value="85" op_text="Revert">Revert</button>
                @endif
                @if($can_reject==1)
                <button type="button" class="btnJb btn btn-danger confirmBtn" id="back_to_reject" value="-100" op_text="Reject">Reject</button>
                @endif

            </div>
            
                </div>
                <br />
               </div>
              </div>
             </div>

    
              </div>
             </div>





            </div>

  



           
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
      <div id="modalReject" class="modal fade">
      <form method="post" id="commonfield"  action="{{url('lbapplicationVerify')}}"  class="submit-once" enctype="multipart/form-data">
              {{ csrf_field() }}
        
              <input type="hidden" name="designation_id" id="designation_id" value="{{$designation_id}}"/>
              <input type="hidden" name="id" id="id" value="{{$row->id}}"/>
              <input type="hidden" name="scheme_id" id="scheme_id" value="{{$row->scheme_id}}"/>
              <input type="hidden" name="action_type" id="action_type" value=""/>
              <input type="hidden" name="action_msg" id="action_msg" value=""/>
              <input type="hidden" name="district_code" id="district_code" value="{{$row->created_by_dist_code}}"/>

  
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header flex-column">
								
				<h4 class="modal-title w-100">LB Application ID:{{$row->lb_application_id}}</h4>	
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
        <div id="note_sc_st">
         <h4>Please check the following things before transfer</h4>
         <ul>
            <li>Upload New Caste Certificate.. If previous one is Invalid. </li>
            <li>Change the Caste Certificate Number.. If previous one is Invalid</li>
         </ul>
        </div>
        
         <h4 style="font-size:30px;color: #fc3903;">Are you Sure.. You want to <span id="op_text" style="font-size:30px;color: #fc3903;"></span>?</h4>
         <div id="cause_div" class="row">
          <div class="form-group col-md-12" id="divBodyCode">
            <label class="required-field">Cause</label>
            <select name="reject_revert_cause" id="reject_revert_cause" class="form-control">
              <option value="">--Select--</option>
                    @foreach ($reject_revert_cause_list as $cause_item)
                        <option value="{{ $cause_item->id }}">
                            {{ $cause_item->reason }}
                        </option>
                    @endforeach
               
            </select>
            <span id="error_reject_revert_cause" class="text-danger"></span>
           </div>
         </div>
         <div id="remark_div" class="row">
          <div class="form-group col-md-12">
            <label class="">Remarks</label>
            <input type="text" id="remarks" name="remarks" class="form-control special-char"
                    placeholder="Enter Remark" maxlength="300" value="">
            <span id="error_remarks" class="text-danger"></span>
           </div>
         </div>
         <div id="dob_change_div" class="row">
         <div class="form-group col-md-4">
          @php
          $mydate = date('Y-m-d');
          $max_date = strtotime("-25 year", strtotime($mydate));
          $max_date = date("Y-m-d", $max_date);
          $min_date = strtotime("-60 year", strtotime($mydate));
          $min_date = date("Y-m-d", $min_date);
          @endphp
                 <label class="">New Date of Birth</label>
                 <input type="date" name="dob" id="dob" class="form-control"   value="{{old('dob')}}" max="<?php echo $max_date;?>" min="<?php echo $min_date;?>"/>
                 <!-- <input type="text" id="dob" name="dob"class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask placeholder="dd/mm/yyyy"> -->
                 <span id="error_dob" class="text-danger"></span>
                </div>
        
        <div class="form-group col-md-12">
                                    <label class="required-field">{{$age_supporting->doc_name }}</label>
                                    <input type="file" name="doc_{{$age_supporting->id }}" id="doc_{{$age_supporting->id }}" class="form-control" />
                                    <div class="imageSize">(Image type must be {{$age_supporting->doc_type }} and image size max {{$age_supporting->doc_size_kb }}KB)</div>
                                    <span id="error_doc_{{$age_supporting->id }}" class="text-danger"></span>
        </div>
       </div>
       <div id="reason_order_div" class="row">
       
        <div class="form-group col-md-12">
                                    <label class="required-field">{{$reason_order->doc_name }}</label>
                                    <input type="file" name="doc_{{$reason_order->id}}" id="doc_{{$reason_order->id}}" class="form-control" />
                                    <div class="imageSize">(Image type must be {{$reason_order->doc_type }} and image size max {{$reason_order->doc_size_kb }}KB)</div>
                                    <span id="error_doc_{{$reason_order->id }}" class="text-danger"></span>
        </div>
       </div>
			</div>
			<div class="modal-footer justify-content-center">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-info modal-submitapprove" >OK</button>
         <button type="button" id="submittingapprove" value="Submit" class="btn btn-success success btn-lg"
                          disabled>Submitting please wait</button>
			</div>
		</div>
</form>
	</div>
 
</div>
  <div class="modal" id="encolser_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="encolser_name">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
            <form id="uploadForm" enctype="multipart/form-data">
            <input type="hidden" name="document_type" id="document_type"/>


   {{ csrf_field() }}
      <div class="modal-body">
       <label>Choose File:</label>
       <input type="file" name="file" id="fileInput">
      
      <div class="progress">
         <div class="progress-bar"></div>
      </div>
      <div id="uploadStatus"></div>
      </div>
      <div class="modal-footer">
        <button type="submit" id="submitButton" name='btnSubmit' class="btn btn-primary">Upload</button>
        <img  style="display:none;" src="{{ asset('images/ZKZg.gif')}}" id="btn_encolser_loader" width="150px">

      </div>
      </form>
    </div>
  </div>
</div>  
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
<script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js") }}" type="text/javascript" ></script>
<script  src="{{ asset ("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js") }}" type="text/javascript" ></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>

<!-- AdminLTE App -->
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script src="{{ asset("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ URL::asset('js/validateAdhar.js') }}"></script>
<script type="text/javascript">
$(document).ready(function(){
  $("#submittingapprove").hide();
  $(".NumOnly").keyup(function(event) {
  $(this).val($(this).val().replace(/[^\d].+/, ""));
                  if ((event.which < 48 || event.which > 57)) {
                      event.preventDefault();
                  }
  }); 
  $('.confirmBtn').click(function(){
    $('#error_mobile_no').text('');
    $('#error_new_aadhar_no').text('');
    $('#error_reject_revert_cause').text('');
    $('#error_remarks').text('');

    var designation_id=$("#designation_id").val();
      var ButtonText = $(this).text();
      var clickval = $(this).val();
     // alert(clickval);
      $("#action_type").val('');
      $('.verify_reject').text('');
      var op_text = $(this).attr("op_text");
      $('#op_text').text(op_text);
      $('#action_msg').val(op_text);
      // alert(designation_id);alert(clickval);
      if(designation_id=='Verifier'){
        if(clickval==5){
        $("#action_type").val(clickval);
        var error_new_mobile_no='';
        var error_new_aadhar_no='';
        if($.trim($('#new_mobile_no').val()) != ""){
            if($.trim($('#new_mobile_no').val()).length !=10)
            {
              error_new_mobile_no = 'Mobile Number must be 10 digit';
            $('#error_new_mobile_no').text(error_new_mobile_no);
            $('#new_mobile_no').addClass('has-error');
            }
            else
            {
              error_new_mobile_no = '';
            $('#error_mobile_no').text(error_new_mobile_no);
            $('#new_mobile_no').removeClass('has-error');

            }
        }
        if($.trim($('#new_aadhar_no').val()) != "")
        {
          if($.trim($('#new_aadhar_no').val()).length != 12)
          {

            error_new_aadhar_no = 'Aadhar No should be 12 digit ';
          $('#error_new_aadhar_no').text(error_new_aadhar_no);
          $('#new_aadhar_no').addClass('has-error');
          }
          else
          {
              var new_aadhar_no=$('#new_aadhar_no').val();
            if(new_aadhar_no!=''){
            var aadhar_valid=validate_adhar(new_aadhar_no);
            // aadhar_valid=1;
            if(aadhar_valid){
              error_new_aadhar_no = '';
                $('#error_new_aadhar_no').text(error_new_aadhar_no);
                $('#new_aadhar_no').removeClass('has-error');
            }
            else{
              error_new_aadhar_no = 'Invalid Aadhar No.';
                $('#error_new_aadhar_no').text(error_new_aadhar_no);
                $('#new_aadhar_no').addClass('has-error');
            }
            }
            else{
              error_new_aadhar_no = '';
                $('#error_new_aadhar_no').text(error_new_aadhar_no);
                $('#new_aadhar_no').removeClass('has-error');
            }
          }
        } 
      }
     
        if(error_new_mobile_no=='' && error_new_aadhar_no==''){
          $('#modalReject').modal();
        }
      }
      
      $("#action_type").val(clickval);
      if(designation_id=='Verifier'){
        //alert('clickval'+clickval);
      if(clickval==70 || clickval==75){
        $("#note_sc_st").show();
      }
      else{
        $("#note_sc_st").hide();
      }
      if(clickval==7){
        $("#dob_change_div").show();
      }
      else{
        $("#dob_change_div").hide();
      }
      if(clickval==80){
        $("#reason_order_div").show();
      }
      else{
        $("#reason_order_div").hide();
      }
      if(clickval==5 || clickval==50){
        $("#cause_div").hide();
        $("#remark_div").hide();
      }
      else{
        $("#cause_div").show();
        $("#remark_div").show();
      }
     }
     else{
     // alert(clickval);
      if(designation_id=='Approver'){
        if(clickval==-100 || clickval==85){
          $("#cause_div").show();
          $("#remark_div").show();
        }
        else{
          $("#cause_div").hide();
          $("#remark_div").hide();
        }
      }
      $("#note_sc_st").hide();
      $("#dob_change_div").hide();
      $("#reason_order_div").hide();
    
     }
      $('#modalReject').modal();
    
       
    });
    $('#reject').click(function(){
      $('.verify_reject').text('Reject');
      $("#action_type").val(4);
      $('#modalReject').modal(); 
    });
    $('.modal-submitapprove').on('click',function(e){
       e.preventDefault();
        var action_type=$('#action_type').val();
        var designation_id=$("#designation_id").val();
        var form_valid=0;
        if(action_type==5){
          var form_valid=1;
        }
        else{
          if(designation_id=='Verifier'){
            if($.trim($('#reject_revert_cause').val()) == ""){
            error_reject_revert_cause = 'Please Select Cause';
            $('#error_reject_revert_cause').text(error_reject_revert_cause);
            $('#reject_revert_cause').addClass('has-error');
          }
          else{
            var form_valid=1;
            error_reject_revert_cause = '';
            $('#error_reject_revert_cause').text(error_reject_revert_cause);
            $('#reject_revert_cause').removeClass('has-error');
          }
          }
          
         
        }
        if(designation_id=='Approver'){
          if(action_type==-100 || action_type==85){
                if($.trim($('#reject_revert_cause').val()) == ""){
                  error_reject_revert_cause = 'Please Select Cause';
                  $('#error_reject_revert_cause').text(error_reject_revert_cause);
                  $('#reject_revert_cause').addClass('has-error');
                }
                else{
                  var form_valid=1;
                  error_reject_revert_cause = '';
                  $('#error_reject_revert_cause').text(error_reject_revert_cause);
                  $('#reject_revert_cause').removeClass('has-error');
                }
           }else{
            var form_valid=1;
           }
        }
        if(form_valid==1){
        $("#commonfield").append('<input type="hidden" id="new_aadhar_no" name="new_aadhar_no" value="'+$("#new_aadhar_no").val()+'" /> ');
        $("#commonfield").append('<input type="hidden" id="new_mobile_no" name="new_mobile_no" value="'+$("#new_mobile_no").val()+'" /> ');
        $("#commonfield").append('<input type="hidden" id="ration_card_cat" name="ration_card_cat" value="'+$("#ration_card_cat").val()+'" /> ');
        $("#commonfield").append('<input type="hidden" id="ration_card_no" name="ration_card_no" value="'+$("#ration_card_no").val()+'" /> ');
        $("#commonfield").append('<input type="hidden" id="epic_voter_id" name="epic_voter_id" value="'+$("#epic_voter_id").val()+'" /> ');
        $("#commonfield").append('<input type="hidden" id="caste_certificate_no" name="caste_certificate_no" value="'+$("#caste_certificate_no").val()+'" /> ');
          $("#commonfield").submit();
        }
        
       
      });
      $(".downloadEncloser").click(function(){
      var id= $(this).attr("id");
      var id_split=id.split('_');  
      var application_id=$("#commonfield #id").val();
      var scheme_id=$("#commonfield #scheme_id").val();
      var district_code=$("#commonfield #district_code").val();

        window.open("jbDownload?created_by_dist_code="+district_code+"&document_type="+id_split[1]+"&scheme_id="+scheme_id+"&beneficiary_id="+application_id);
     });
     $('.encloserModal').click(function(){
 $("#encolser_name").html('');
 $('#uploadStatus').html('');
 $('.progress-bar').html('');
 $("#uploadForm #document_type").val('');
 $('#btn_encolser_loader').hide();
 var label = $(this).parent().find('label').text();
 $("#encolser_name").html(label);
 var id= $(this).attr("id");
 var id_split=id.split('_');
 //console.log(id_split);
 $("#uploadForm #document_type").val(id_split[1]);
 $("#encolser_modal").modal("show");

});
$("#uploadForm").on('submit', function(e){
        $('#submitButton').hide();
        $('#btn_encolser_loader').show();
        e.preventDefault();
        var form = $('#uploadForm')[0];
        var formData = new FormData(form);
        var ben_id=$("#commonfield #id").val();
        var scheme_id=$("#commonfield #scheme_id").val();
        formData.append('ben_id', ben_id);
        formData.append('scheme_id', scheme_id);
        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = ((evt.loaded / evt.total) * 100);
                        var percentComplete = Math.ceil(percentComplete);
                        $(".progress-bar").width(percentComplete + '%');
                        $(".progress-bar").html(percentComplete+'%');
                    }
                }, false);
                return xhr;
            },
            type: 'POST',
            dataType: 'json',
            url: '{{ url('jb_ajax_encloser_entry') }}',
            data: formData,
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function(){
                $(".progress-bar").width('0%');
                //$('#uploadStatus').html('<img   width="50px" height="50px" src="images/ZKZg.gif"/>');
            },
             error: function (ex){
                //console.log(ex);
                $('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');
                 $('#btn_encolser_loader').hide();
                 $('#submitButton').show();


            },
            success: function(resp){
              //console.log(resp);
                if(resp.return_status==1){
                    var id=$("#uploadForm #document_type").val();
                    $('#uploadForm')[0].reset();
                    $('#download_'+id).show();
                    $('#uploadStatus').html('<p style="color:#28A74B;">File has uploaded successfully!</p>');
                     //$(".progress-bar").width('0%');

                }else if(resp.return_status==0){
                    $('#uploadStatus').html('<p style="color:#EA4335;">'+resp.return_msg+'</p>');
                }
                  $('#btn_encolser_loader').hide();
                   $('#submitButton').show();


            }
        });
        
        
    });
	
    
$('#encolser_modal').on('hidden.bs.modal', function (e) {
  $("#uploadForm #document_type").val('');
  $(".progress-bar").html('');

});
$('.confirmBtnCaste').click(function(){
  var clickval = $(this).val();
  var application_id=$("#commonfield #id").val();
  var scheme_id=$("#commonfield #scheme_id").val();
  window.location = "changeCastelb?scheme_id=" + scheme_id+ "&id=" + application_id+ "&type=" + clickval; 
});
});

</script>
</body>
</html>


