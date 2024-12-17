@extends('workflow-nsap.base')

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
.required-field::after {
    content: "*";
    color: red;
}
.has-error
  {
   border-color:#cc0000;
   background-color:#ffff99;
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
               <h2 class="modal-title" style="text-align: center;">View Application Form</h2>
               
              
                <a href="{{ route('nsap-marked-list', ['scheme_id'=>$row->scheme_id])}}">
                <img width="50px;" style="pull-right" src="{{ asset("images/back.png") }}" alt="Back" /></a>
                
              
            </div>
            <div class="modal-body">
              <!--   <h4 class="example-screen" style="text-align: center;">Please Verify or Reject Employee's application with Comments</h4> -->
                

                <!-- We display the details entered by the user here -->
                <div class="section1">
                  <div class="row">
                  <div class="col-md-12">
                    <h3 style="text-align: center; color:red;">Application ID:{{$row->app_id}}
                      
                      </h3>
                  </div>


                  </div>
                       
                <div class="row color1">
                  <div class="col-md-12"><h3>Personal Details</h3></div>
                </div>
                <div class="row">
                    <div class="col-md-6" >
                      <div ><strong>Name :</strong> {{$row->ben_fname}} {{$row->ben_mname}} {{$row->ben_lname}}</div>
                    </div>

                    

                      <!-- <img id="blah" src="{{ asset($row->passport_image) }}" alt=""  width="200px" height="200px" />

                       <img src="{{ url('storage/'.$row->passport_image) }}" alt="" title="" /> -->

                       <!--  <img src="{{ asset('upload/'.$row->passport_image) }}" alt="" width="200px" height="200px" /> -->
                    
               
                      
                        
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

                     
                    
                      
                        
                       
                      

                        <div class="col-md-6">
                          <div><strong>Caste:</strong> {{$row->caste}}</div>
                        </div> 
                       
                       
                        <div class="col-md-6">
                          <div ><strong>Marital Status:</strong> {{$row->marital_status}}</div>
                        </div>

                         <div class="col-md-6" >
                         <div ><strong>Spouse Name :</strong> {{$row->spouse_fname}} {{$row->spouse_mname}} {{$row->spouse_lname}}</div>
                         </div>

                        <div class="col-md-6">
                          <div ><strong>Monthly Family Income(Rs.):</strong> {{$row->mothly_income}}</div>
                        </div>                      
                     

                   
                     
                    
                      </div>
                    

                      <div class="row color1"  style="margin:10px 0px" >
                          <div class="col-md-12"><h3>Personal Identification Number(S)</h3></div>
                      </div>

                      <div class="col-md-6">
                        <div ><strong>Digital Ration Card No.:</strong> {{$row->ration_card_no}} </div>
                      </div>

                      <div class="col-md-6">
                        <div ><strong>AHL TIN: </strong>{{$row->ahl_tin}}</div>
                        </div>

                        <div class="col-md-6">
                        <div ><strong>Aadhaar No., if available:</strong> 
                        @php
                        if (!empty($row->aadhar_no)) {
                         $ben_aadhar_no = trim($row->aadhar_no);
                        } else {
                            $ben_aadhar_no = '';
                           }
                        @endphp
                        {{$ben_aadhar_no}}</div>
                        </div>

                        <div class="col-md-6">
                        <div ><strong>EPIC/Voter Id.No.: </strong> {{$row->epic_voter_id}}</div>
                        
                        </div>

                        <div class="col-md-6">
                         <div ><strong>PAN, if available:</strong> {{$row->pan_no}}</div>
                       
                        </div>

                        <div class="col-md-6">
                         <div ><strong>BPL Seq No., if avaiable:</strong>  {{$row->bpl_seq_no}}</div>
                       
                        </div>

                        <div class="col-md-6">
                         <div ><strong>BPL Id No., if avaiable:</strong> {{ $row->bpl_id_no }}</div>
                       
                        </div>

                        <div class="col-md-6">
                         <div ><strong>BPL Total Score, if avaiable:</strong> {{$row->bpl_total_score}}</div>
                       
                        </div>

                      <div class="row ">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>Contact Details</h3></div>
                      </div>

                       <div class="col-md-6">
                         <div ><strong>State:</strong> West Bengal</div>
                       
                        </div>




                         <div class="col-md-6">
                         <div ><strong>Assembly Constitution:</strong>  {{$row->assembly_name}}</div>
                       
                        </div>


                         <div class="col-md-6">
                         <div ><strong>District:</strong>  {{$district_name}}</div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>Block/Municipality/Corp:</strong>{{$row->block_name}}</div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>GP/Ward No.:</strong>{{$gp_name}}</div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>Village/Town/City:</strong> {{$row->village_town_city}}</div>
                       
                        </div>



                         <div class="col-md-6">
                         <div ><strong>House/Premise No.:</strong>  {{$row->house_premise_no}}</div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>Post Office:</strong>  {{$row->post_office}}</div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>Pin Code:</strong>  {{$row->pincode}}</div>
                       
                        </div>


                         <div class="col-md-6">
                         <div ><strong>Police Station:</strong>{{$row->police_station}}</div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>Mobile Number:</strong>
                         @php
                         if (!empty($row->mobile_no)) {
                         $ben_mobile_no = trim($row->mobile_no);
                
                       } else {
                    $ben_mobile_no = '';
                       }
                       @endphp
                         {{$ben_mobile_no}}</div>
                       
                        </div> 
                        <div class="col-md-6">
                         <div ><strong>Email Id., if available:</strong> {{$row->email}}
                            
                            
                           </div>

                        </div>



                         <div class="row ">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>Bank Details</h3></div>
                      </div>

                       <div class="col-md-6">
                         <div ><strong>Bank Name:</strong>  {{$row->bank_name}}</div>
                       
                        </div>




                         <div class="col-md-6">
                         <div ><strong>Bank Branch Name:</strong> {{$row->branch_name}}</div>
                       
                        </div>


                         <div class="col-md-6">
                         <div ><strong>Bank Account No.:</strong> 
                         @php
                         if (!empty($row->bank_code)) {
                  $ben_bank_code = trim($row->bank_code);
                
                } else {
                    $ben_bank_code = '';
                }
                @endphp
                         {{$ben_bank_code}}</div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>IFS Code:</strong>{{$row->bank_ifsc}}</div>
                       
                        </div>

                      </div>

                       <div class="row color1">
                  <div class="col-md-12"><h3>Enclosure List(Self Attested)</h3></div>
                </div>
                <div class="row">
                    @foreach($docs as $doc)
                    @if($doc->doc_name !="")
                    <div class="col-md-4"  >
                      <strong>{{$doc->doc_type_name}} :</strong> 
                    </div>
                    <div class="col-md-8" style="padding-bottom: 30px; ">
                        <?php 
                          $data = $doc->doc_name;
                          $ext = pathinfo($data, PATHINFO_EXTENSION);
                        ?> 
                         @if(strtolower($ext)=='jpg')
                         <div class="col-md-12" style="border:1px solid #dcdfdf">
                          <a class="example-image-link" href="{{$doc->doc_name}}" data-lightbox="example-1">
                          <img class="example-image" src="{{$doc->doc_name}}" alt="image-1" width="200" height="180" /></a>
                          </div>
                         @elseif(strtolower($ext)=='jpeg')
                         <div class="col-md-12" style="border:1px solid #dcdfdf">
                          <a class="example-image-link" href="{{$doc->doc_name}}" data-lightbox="example-1">
                          <img class="example-image" src="{{$doc->doc_name}}" alt="image-1" width="200" height="180" /></a>
                          </div>
                          @elseif(strtolower($ext)=='jfif')
                         <div class="col-md-12" style="border:1px solid #dcdfdf">
                          <a class="example-image-link" href="{{$doc->doc_name}}" data-lightbox="example-1">
                          <img class="example-image" src="{{$doc->doc_name}}" alt="image-1" width="200" height="180" /></a>
                          </div>
                         @elseif(strtolower($ext)=='png')
                         <div class="col-md-12">
                          <a class="example-image-link" href="{{$doc->doc_name}}" data-lightbox="example-1">
                          <img class="example-image" src="{{$doc->doc_name}}" alt="image-1" width="200" height="180" /></a>
                          </div>
                         

                          @elseif(strtolower($ext)=='gif')
                          <div class="col-md-12">
                          <a class="example-image-link" href="{{$doc->doc_name}}" data-lightbox="example-1">
                          <img class="example-image" src="{{$doc->doc_name}}" alt="image-1" width="200" height="180" /></a>
                        </div>

                          @elseif(strtolower($ext)=='pdf')
                          <div class="col-md-12" style="border:1px solid #dcdfdf">
                          
                            <a id="link" href="{{$doc->doc_name}}" target="_blank" width="">Download PDF Document</a>
                          </div>
                         @endif                     
                    </div>
                    @endif         
                    @endforeach
               </div>

                
  </div>

   <div class="row">
                   
              <div class="text-center" style="margin-top: 10px;"></div><br/>
            
              
              
               
                      
                 
                       <div class="row">   
                         @if($designation_id_old=='Verifier')
                         @if(is_null($row->process_nsap_flag) || $row->nsap_flag==12)   

                        
                        <div class="col-md-3" style="text-align: center;">
                        <input type="button" name="submit" value="NSAP Marked" id="nsap_marked" class="btn btn-info btn-lg btn-action" >
                        </div>
                        
                        <div class="col-md-3" style="text-align: center;">
                        <input type="button" name="submit" value="Verify" id="Verifysubmit" class="btn btn-success btn-lg btn-action" >
                        </div>
                        <div class="col-md-3" style="text-align: center;">
                        <input type="button" name="submit" value="Reject" id="Rejectsubmit" class="btn btn-danger btn-lg btn-action">
                        </div>
                        <div class="col-md-3" style="text-align: center;">
                        <input type="button" name="submit" value="Revert" id="Rejectsubmit" class="btn btn-primary btn-lg btn-action">
                        </div>
                        @endif
                        
                         @endif
                        @if($designation_id_old=='Approver')
                        <div class="col-md-3" style="text-align: center;">
                        <input type="button" name="submit" value="Approve" id="Verifysubmit" class="btn btn-success btn-lg btn-action" >
                        </div>
                        <div class="col-md-3" style="text-align: center;">
                        <input type="button" name="submit" value="Reject" id="Rejectsubmit" class="btn btn-danger btn-lg btn-action">
                        </div>
                        <div class="col-md-3" style="text-align: center;">
                        <input type="button" name="submit" value="Revert" id="Rejectsubmit" class="btn btn-primary btn-lg btn-action">
                        </div>
                         @endif
                      </div>
                   <br/>
              
               
           
             <!--     <div class="text-center example-screen" style="margin-top: 10px;"><button style="width:25%;"class="btn btn-success btn-lg" onclick="printfunction()">Print</button></div> -->
               
            
   </div>   
                         







                

                     
                   </div>


                       </div>
                 
                      


            </div>


          </div>
          
           
        </div>
</section>
@endsection
<form method="post" action="{{ route('forward-nsap')}}" >
{{ csrf_field() }}
                      <input type="hidden" name="basePage" id="basePage" value="{{$row->is_nsap}}">
                      <input type="hidden" name="action_type" id="action_type" value="">
                      <input type="hidden" name="id" id="ben_id" value="{{$row->id}}">
                      <input type="hidden" name="scheme_id" id="scheme_id" value="{{$row->scheme_id}}">
                      <input type="hidden" name="is_reverted" id="is_reverted" value="{{$row->nsap_flag}}">
<div class="modal" tabindex="-1" role="dialog" id="myModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
     
      <div class="modal-body">
      <p>Are You Sure want to <b><span id="action_txt"></span></b>  the Application with ID <span id="id_txt" class="text-info"></span></p>
      <div class="form-group col-md-10 divNsap">
                 <label class="">Enter Last 4 digit of RHS ID </label>
                 <input type="text" name="nsap_rhs_id" id="nsap_rhs_id" class="form-control NumOnly" placeholder="RHS ID" maxlength="200"   />
                 <span id="error_nsap_rhs_id" class="text-danger"></span>
                
          </div>
          <div class="form-group col-md-10 divNsap">
                 <label class="">Enter Member Id </label>
                 <input type="text" name="nsap_member_id" id="nsap_member_id" class="form-control NumOnly" placeholder="Member Id" maxlength="200"   />
                 <span id="error_nsap_member_id" class="text-danger"></span>
                
          </div>
         
          
      </div>
      <div class="row">
                        <div class="col-md-12">
                        <input style="width:100%; padding: 2%; margin:1%;" type="text" name="comments" id="comments" class="form-control" placeholder="Comments" /> 
                        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="modal-submit">OK</button>
        <button type="button"  id="submitting" value="Submit" class="btn btn-success success btn-lg" disabled >Submitting please wait</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
</form>

<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
  $("#submitting").hide();
  $("#action_type").val('');
  $("#action_txt").text('');
  $("#id_txt").text('');
  $('.btn-action').click(function(){  
    $("#action_type").val('');
    $("#action_type").val($(this).val());
    //alert($("#action_type").val());
    $("#action_txt").text($(this).val());
    $("#id_txt").text($("#ben_id").val());
    if($(this).val()=='NSAP Marked'){
      $(".divNsap").show();
    }
    else{
      $(".divNsap").hide();
    }
    $('#myModal').modal('show');
});
$('#modal-submit').on('click',function(){
 var action_type= $("#action_type").val();
 var error_nsap_rhs_id='';
 var error_nsap_member_id='';
 if(action_type=='NSAP Marked'){
 
  
 }

if(error_nsap_rhs_id=='' && error_nsap_member_id=='' ){
  //console.log('ok1');
   $("#modal-submit").hide();
   $("#submitting").show();
   $("#submit_loader").show();
   $("#register_form").submit();
}
else{
  //console.log('ok2');
  return false;
}

});
$(".NumOnly").keyup(function(event) {
              
              $(this).val($(this).val().replace(/[^\d].+/, ""));
                  if ((event.which < 48 || event.which > 57)) {
                      event.preventDefault();
                  }
              });
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
</script>
