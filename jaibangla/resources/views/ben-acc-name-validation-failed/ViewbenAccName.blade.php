@extends('ben-acc-name-validation-failed.base')
@section('action-content')
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


</style>
<section >
<div class="modal-fade" tabindex="-1" role="document">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="example-screen">
               <!--  <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
                <!-- <span aria-hidden="true">&times;</span> -->
               <!-- </button> -->
               <h2 class="modal-title " style="text-align: center;">View Application Form</h2>
               <a href="{{ route('benaccnamefaliledlist', ['type'=>2,'scheme_id'=>$row->scheme_id])}}">
                <img width="50px;" style="pull-right" src="{{ asset("images/back.png") }}" alt="Back" /></a>
            </div>
            <div class="modal-body">
              <div class='row'>
            <div>
             @if ( ($message = Session::get('success')) && ($id =Session::get('id')))
                <div class="alert alert-success alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{{ $message }} with Beneficiary ID: {{$id}}</strong>
                  

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
                

                <!-- We display the details entered by the user here -->
                <div class="section1">
                  <div class="row">
                  <div class="col-md-12">
                    <h3 style="text-align: center; color:red;">Beneficiary ID:{{$row->id}}
                      
                      </h3>
                  </div>


                  </div>
                       
                <div class="row color1">
                  <div class="col-md-12"><h3>Personal Details</h3></div>
                </div>
                <div class="row">
                    <div class="col-md-6" >
                      <div ><strong>Name as in Jai Bangla :</strong> {{$row->ben_fname}} {{$row->ben_mname}} {{$row->ben_lname}}</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6" >
                      <div ><strong>Name which is received from Bank:</strong>{{$row->av_name_response}}</div>
                    </div>
                </div>

                      <!-- <img id="blah" src="{{ asset($row->passport_image) }}" alt=""  width="200px" height="200px" />

                       <img src="{{ url('storage/'.$row->passport_image) }}" alt="" title="" /> -->

                       <!--  <img src="{{ asset('upload/'.$row->passport_image) }}" alt="" width="200px" height="200px" /> -->
                    
                       <div class="row">
                        <div class="col-md-6">
                            <div ><strong>Gender:</strong> {{ ($row->gender=='Other') ? "Transgender" : $row->gender }} </div>
                            
                        </div>
                        
                        @if(!is_null($row->dob))
                        <div class="col-md-6">
                          <div ><strong>Date of Birth (DD-MM-YYYY):</strong> {{date('d/m/Y', strtotime($row->dob)) }}</div>
                         
                        </div>
                        @endif

                        

                    


                    
                    <div class="col-md-6" >
                      <div ><strong>Father's Name :</strong> {{$row->father_fname}} {{$row->father_mname}} {{$row->father_lname}}</div>
                    </div>

                    <div class="col-md-6" >
                      <div ><strong>Mother's Name :</strong> {{$row->mother_fname}} {{$row->mother_mname}} {{$row->mother_lname}}</div>
                    </div>

                     
                    
                      
                        
                       
                      

                        
                       
                       
                        

                        

                                         
                     

                   
                     
                    
                      </div>
                    

                      



                      <div class="row">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>Existing Bank Details</h3></div>
                      </div>

                       <div class="col-md-6">
                         <div ><strong>Bank Name:</strong>  {{$row->bank_name}}</div>
                       
                        </div>




                         <div class="col-md-6">
                         <div ><strong>Bank Branch Name:</strong> {{$row->branch_name}}</div>
                       
                        </div>


                         <div class="col-md-6">
                         <div ><strong>Bank Account No.:</strong> {{$row->bank_code}}</div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>IFS Code:</strong>{{$row->bank_ifsc}}</div>
                       
                        </div>
                        
                        @if($designation_id=='Approver')
                        
                        <div class="form-group col-md-12">
                        <label class="">Process Type</label>
                      
                        <span id="error_process_type" class="text-danger">@if($row->process_acc_validated==2) Keep existing bank information @elseif($row->process_acc_validated==0) Process with new bank information @elseif($row->process_acc_validated==-53) Process with Rejection @endif</span>
                      </div>
                        @if($row->process_acc_validated==0)
                        <div class="row">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>New Bank Details</h3></div>
                      </div>

                       <div class="col-md-6">
                         <div ><strong>Bank Name:</strong>  {{$row->new_bank_name}}</div>
                       
                        </div>




                         <div class="col-md-6">
                         <div ><strong>Bank Branch Name:</strong> {{$row->new_branch_name}}</div>
                       
                        </div>


                         <div class="col-md-6">
                         <div ><strong>Bank Account No.:</strong> {{$row->new_bank_code}}</div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>IFS Code:</strong>{{$row->new_bank_ifsc}}</div>
                       
                        </div>
                        
                      
                       <div class="row">
                          <div class="col-md-12"  style="margin:10px 0px"><h3>{{$doc_man['doc_name']}}</h3></div>

                          <div class="row">
                            @if($encolserdata->attched_document !="")
                              <div class="col-md-4"  >
                                <strong>{{$encolserdata->doc_type_name}} :</strong> 
                              </div>
                              <div class="col-md-8" style="padding-bottom: 30px; ">
                                <?php 
                                  $document_mime_type = $encolserdata->document_mime_type;
                                  if($document_mime_type=='image/jpeg'){
                                    $image_extension='jpg';
                                  }else if($document_mime_type=='image/png'){
                                    $image_extension='png';
                                  }else if($document_mime_type=='application/pdf'){
                                    $image_extension='pdf';
                                  }
                                  $row_image = "data:image/".$image_extension.";base64,".$encolserdata->attched_document;
                                ?> 
                                @if(strtolower($image_extension)=='jpg' || strtolower($image_extension)=='png')
                                  <div class="col-md-12" style="border:1px solid #dcdfdf">
                                      {{-- <a class="example-image-link" href="{{$row_image}}" data-lightbox="example-1"> --}}
                                      <img class="example-image" src="{{$row_image}}" alt="image-1" width="200" height="180" />
                                  </div>
                      
                                @elseif(strtolower($image_extension)=='pdf')
                                  <div class="col-md-12" style="border:1px solid #dcdfdf">
                                    <a id="link"  href="{{route('jbDownload', ['scheme_id' => $encolserdata->scheme_id,'created_by_dist_code' => $encolserdata->created_by_dist_code,'beneficiary_id' => $encolserdata->beneficiary_id,'document_type' => $encolserdata->document_type])}}" target="_blank" style="color: #4324ef" width="">Download PDF Document</a>
                                  </div>
                                @else
                                  <div class="col-md-12" style="border:1px solid #dcdfdf">
                                    <p>No File Found</p>
                                  </div>
                                @endif    
                                @endif 
                              </div>
                            @endif 
                          </div>
                          
                        </div>
                        
                        
                        <center>
                        
                        <button type="button" id="confirm" value="Reject"
                          class="btn btn-danger btn-lg confirm">Reject
                        </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        {{-- <button type="button" id="confirm" value="Back to Verifier"
                          class="btn btn-info btn-lg confirm">Back to Verifier
                        </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; --}}

                        @if($row->process_acc_validated==2 || $row->process_acc_validated==0)
                        <button type="button" id="confirm" value="Approve"
                          class="btn btn-success btn-lg confirm">Approve
                        </button>
                         @endif
                        </center>
                        
                        @endif
                        @if($designation_id=='Verifier')
                        <form method="post" id="register_form" action="{{url('benaccnamefaliledlistPost')}}" enctype="multipart/form-data"
                    class="submit-once" onsubmit="return client_validation()">
                    <input type="hidden" name="scheme_id" id="scheme_id" value="{{$row->scheme_id}}"/>
                    <input type="hidden" name="id" id="id" value="{{$row->id}}"/>
                    <input type="hidden" name="old_bank_ifsc" id="old_bank_ifsc" value="{{trim($row->bank_ifsc)}}"/>
                    <input type="hidden" name="old_bank_code" id="old_bank_code" value="{{trim($row->bank_code)}}"/>
                    <input type="hidden" name="acc_validated" id="acc_validated" value="{{trim($row->acc_validated)}}"/>
                    <input type="hidden" name="new_bank_is_required" id="new_bank_is_required" value=""/>
                    <input type="hidden" name="type" id="type" value="{{$type}}"/>

                     {{ csrf_field() }}
                     @if($row->acc_validated==-2)
                      <br/>
                      <div  style="font-size:20px; font-weight: bold; font-style: italic;" class="text-warning" align="center">Please select which one do you want to process ?</div>

                      <div style="padding: 5px 5px 5px 50px; border: 1px solid whitesmoke; border-radius: 5px; margin: 5px 0px; background-color: whitesmoke;" class="row">
                            <label style="cursor: pointer; margin-bottom: 5px;">
                              <input type="radio" name="process_type" id="process_type" value="1"> Minor mismatch, Keep existing bank information
                            </label><br/>
                            <label style="cursor: pointer; margin-bottom: 5px;">
                              <input type="radio" name="process_type" id="process_type" value="2"> Process with new bank information
                            </label><br/>
                            <label style="cursor: pointer; margin-bottom: 5px;">
                              <input type="radio" name="process_type" id="process_type" value="3"> Application is rejected due to major mismatch
                            </label>
                          </div>
                          <span id="error_process_type" class="text-danger"></span>
                    @endif
                    <div id="new_bank_info_div" style="@if($row->acc_validated==-2) display:none @endif">
                        <div class="row">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>Upload Bank Details</h3></div>
                        </div>
                        
                        <div class="row">
                          
                        
                          <div class="form-group col-md-6">
                  <label class="required-field">IFS Code</label>
                  <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control special-char" placeholder="IFSC Code" onkeyup="this.value = this.value.toUpperCase();"  value="{{ old('bank_ifsc_code') }}" maxlength='11' tabindex="1" />
                  <span id="error_bank_ifsc_code" class="text-danger"></span>
                </div>

                <div class="form-group col-md-6">
                 <label class="required-field">Bank Name</label>
                 <input type="text" name="name_of_bank" id="name_of_bank" class="form-control special-char" placeholder="Bank Name"  value="{{ old('name_of_bank') }}" maxlength="200" tabindex="2" readonly />
                 <span id="error_name_of_bank" class="text-danger"></span>
                </div>
               
               
                
                <div class="form-group col-md-6">
                 <label class="required-field">Bank Branch Name</label>
                 <input type="text" name="bank_branch" id="bank_branch" class="form-control" placeholder="Bank Branch Name"  value="{{ old('bank_branch') }}" maxlength="300" tabindex="3" readonly />
                 <span id="error_bank_branch" class="text-danger"></span>
                </div>

                <div class="form-group col-md-6">
                 <label class="required-field">Bank Account Number</label>
                 <input type="text" name="bank_account_number" id="bank_account_number" class="form-control NumOnly" placeholder="Bank Account No"  value="{{ old('bank_account_number') }}" maxlength='16' tabindex="4" />
                 <span id="error_bank_account_number" class="text-danger"></span>

                </div>
               </div>
               <div class="row">
                                        <div class="form-group col-md-6">
                                    <label class="required-field">{{ $doc_man['doc_name'] }}</label>
                                    <input type="file" name="doc_{{ $doc_man['id']}}" id="doc_{{ $doc_man['id'] }}" class="form-control" tabindex="1" />
                                    <div class="imageSize">(Image type must be {{ $doc_man['doc_type'] }} and image size max {{ $doc_man['doc_size_kb'] }}KB)</div>
                                    <span id="error_doc_{{ $doc_man['id'] }}" class="text-danger"></span>
                          </div>
               </div>
              </div>
              
                <center> <button type="submit" id="submit" value="Submit"
                          class="btn btn-success success btn-lg modal-submit">Submit </button>
                        <button type="button" id="submitting" value="Submit" class="btn btn-danger btn-lg"
                          disabled>Submitting please wait</button></center>
                        </div>
                        
                     
                    </form>
                  @endif
                      
                <div class="row">
                   
               </div>

                
  </div>

  
                         







                

                     
                   </div>


                       </div>
                 
                      


            </div>


          </div>
          
           
        </div>
  <div id="modalConfirm" class="modal fade">
  
  <form method="post" id="approval_form" action="{{url('benaccnamefaliledBulkApprove')}}" class="submit-once">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="action_type" id="action_type" value=""/>
      <input type="hidden" id="scheme_id" name="scheme_id" value="{{ $scheme_id }}">
      <input type="hidden" name="type" id="type" value="{{$type}}" >
      <input type="hidden" id="approvalcheck" name="approvalcheck[]" value="{{$row->id}}">
      <input type="hidden" name="process_type" id="process_type" value="{{$row->process_acc_validated==0?2:1}}" >
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header flex-column">
								
			
			</div>
			<div class="modal-body">
      <h4 class="modal-title w-100">Do you really want to <span id="verify_revert_reject">Approve</span>?</h4>	
       
         
			</div>
			<div class="modal-footer justify-content-center">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-info" id="confirm_yes" >OK</button>
         <button type="button" id="submittingapprove" value="Submit" class="btn btn-success btn-lg"
                          disabled>Submitting please wait</button>
			</div>
		</div>
	</div>
</form>
</div>
</section>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script src="{{ URL::asset('js/validateAdhar.js') }}"></script>

<script type="text/javascript">
$(document).ready(function(){
  $("#submitting").hide();
  $("#submittingapprove").hide();
  var acc_validated='{{$row->acc_validated}}';
  if(acc_validated=='-1'){
    $("#new_bank_is_required").val(1);
  }
  else{
    $("#new_bank_is_required").val(0);
  }
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
            $('#error_name_of_bank').html('');
            $('#error_bank_branch').html('');
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
        }
      });

    }else{
      $('#error_bank_ifsc_code').text('IFSC format invalid please check the code');
      $('#bank_ifsc_code').addClass('has-error');
    }
 });
 $(document).on('change', '#process_type', function() {
    var processVal = this.value;
    if (processVal == 1) {
      $('#new_bank_info_div').hide();
      $('#bank_ifsc').val('');
      $('#bank_account_number').val('');
      $("#new_bank_is_required").val(0);
    }
    else if(processVal == 2) {
      $('#new_bank_info_div').show();
      $('#remarks').val('');
      $('#bank_ifsc').val('');
      $('#bank_name').val('');
      $('#branch_name').val('');
      $('#bank_account_number').val('');
      $("#new_bank_is_required").val(1);
    }
    else if (processVal == 3) {
      $('#new_bank_info_div').hide();
      $('#bank_ifsc').val('');
      $('#bank_account_number').val('');
      $("#new_bank_is_required").val(0);
    }
    else {
      
    }
  });
  $('.confirm').click(function(){  
      $("#action_type").val('');
      var button_val=$(this).val();
      //console.log(button_val);
      $('#verify_revert_reject').text(button_val); 
      if(button_val=='Approve'){
        $("#action_type").val(1);
      } 
      else if(button_val=='Back to Verifier'){
        $("#action_type").val(2);
      } 
      else if(button_val=='Reject'){
        $("#action_type").val(3);
      } 
      $('#modalConfirm').modal();
    });
    $('#confirm_yes').on('click',function(){
        $("#confirm_yes").hide();
        $("#submittingapprove").show();
        $("#approval_form").submit();
        
       
      });

});
function client_validation(){
  var error_process_type='';
  var error_doc_10='';
  var error_bank_ifsc_code='';
  var error_bank_account_number='';
  var old_bank_ifsc=$('#old_bank_ifsc').val();
  var old_bank_code=$('#old_bank_code').val();
  var bank_ifsc_code=$.trim($('#bank_ifsc_code').val());
  var bank_account_number=$.trim($('#bank_account_number').val());
  //console.log(bank_account_number);
  var doc_10=$('#doc_10').val();
  var new_bank_is_required=$('#new_bank_is_required').val();
  var process_type = $("input[name='process_type']:checked").val();
 // console.log(new_bank_is_required);
  if(new_bank_is_required==0){
          if(process_type == "" || process_type=== undefined){
              error_process_type = 'Please Select One';
              $('#error_process_type').text(error_process_type);
              $('#process_type').addClass('has-error');
          }
          else{
            error_process_type='';
            $('#process_type').removeClass('has-error');
          }
 }
 else{
  error_process_type='';
  }
  if(new_bank_is_required==1){
  if(bank_ifsc_code == ""){
       error_bank_ifsc_code = 'IFS Code is required';
       $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
       $('#bank_ifsc_code').addClass('has-error');
  }
  else{
    error_bank_ifsc_code = '';
    $('#error_bank_ifsc_code').text(error_bank_ifsc_code);
    $('#bank_ifsc_code').removeClass('has-error');
  }
  if(bank_account_number == ""){
       error_bank_account_number = 'Bank Account Number is required';
       $('#error_bank_account_number').text(error_bank_account_number);
       $('#bank_account_number').addClass('has-error');
  }
  else{
    error_bank_account_number = '';
    $('#error_bank_account_number').text(error_bank_account_number);
    $('#bank_account_number').removeClass('has-error');
  }
  if(doc_10 == ""){
       error_doc_10 = 'Copy of Bank Pass book';
       $('#error_doc_10').text(error_doc_10);
       $('#doc_10').addClass('has-error');
  }
  else{
    error_doc_10 = '';
    $('#error_doc_10').text(error_doc_10);
    $('#doc_10').removeClass('has-error');
  }
  }
  else{
    error_bank_ifsc_code='';
    error_bank_account_number='';
    error_doc_10='';
  }
 
  if(error_process_type == '' && error_bank_ifsc_code == '' && error_bank_account_number == '' && error_doc_10 == ''){
    if(process_type==1){
      var y_n=confirm('Are You Sure..You want to Keep existing bank information?');
    }
     else if(process_type==3){
      var y_n=confirm('Are You Sure..You want to reject due to major mismatch?');
    }
    if(new_bank_is_required==1){
      var y_n=confirm('Are You Sure..You want to Process with new bank information?');
    }
    //console.log(y_n);
    if(y_n){
     $("#submit").hide();
     $("#submitting").show();
     return true;
    }
    else{
      return false;
    }
  }
  else{
    return false;
  }
}
</script>
