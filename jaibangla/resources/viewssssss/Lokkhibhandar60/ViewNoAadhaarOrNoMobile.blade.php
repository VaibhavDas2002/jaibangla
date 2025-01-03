@extends('NoAadhaarMobile.base')
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
               <a href="{{ route('workflow-noaadhaarnomobile', ['scheme_id'=>$row->scheme_id])}}">
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
                    <h3 style="text-align: center; color:red;">Benefiiary ID:{{$row->id}}
                      
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
                    

                      



                      <div class="row">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>Bank Details</h3></div>
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
                        <form method="post" id="register_form" action="{{url('NoAadhaarNoMobileWorkflowPost')}}" 
                    class="submit-once" >
                    <input type="hidden" name="scheme_id" id="scheme_id" value="{{$row->scheme_id}}"/>
                    <input type="hidden" name="id" id="id" value="{{$row->id}}"/>
                    @if($designation_id=='Verifier')
                    <input type="hidden" name="action_type" id="action_type" value="2"/>
                    @endif
                   @if($designation_id=='Approver')
                    <input type="hidden" name="action_type" id="action_type" value="3"/>
                    @endif
                     {{ csrf_field() }}
                      @if($row->no_aadhar==1)
                        <div class="row">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>Uploaded Aadhaar Details</h3></div>
                        </div>

                        <div class="row">
                          @if(!empty($doc_aadhar))
                          <?php 
                          $data = $doc_aadhar->doc_name;
                          $ext = pathinfo($data, PATHINFO_EXTENSION);
                          ?> 
                           @if(strtolower($ext)=='jpg')
                         <div class="col-md-12" style="border:1px solid #dcdfdf">
                          <a class="example-image-link" href="{{$doc_aadhar->doc_name}}" data-lightbox="example-1">
                          <img class="example-image" src="{{$doc_aadhar->doc_name}}" alt="image-1" width="200" height="180" /></a>
                          </div>
                         @elseif(strtolower($ext)=='jpeg')
                         <div class="col-md-12" style="border:1px solid #dcdfdf">
                          <a class="example-image-link" href="{{$doc_aadhar->doc_name}}" data-lightbox="example-1">
                          <img class="example-image" src="{{$doc_aadhar->doc_name}}" alt="image-1" width="200" height="180" /></a>
                          </div>
                          @elseif(strtolower($ext)=='jfif')
                         <div class="col-md-12" style="border:1px solid #dcdfdf">
                          <a class="example-image-link" href="{{$doc_aadhar->doc_name}}" data-lightbox="example-1">
                          <img class="example-image" src="{{$doc_aadhar->doc_name}}" alt="image-1" width="200" height="180" /></a>
                          </div>
                         @elseif(strtolower($ext)=='png')
                         <div class="col-md-12">
                          <a class="example-image-link" href="{{$doc_aadhar->doc_name}}" data-lightbox="example-1">
                          <img class="example-image" src="{{$doc_aadhar->doc_name}}" alt="image-1" width="200" height="180" /></a>
                          </div>
                         

                          @elseif(strtolower($ext)=='gif')
                          <div class="col-md-12">
                          <a class="example-image-link" href="{{$doc_aadhar->doc_name}}" data-lightbox="example-1">
                          <img class="example-image" src="{{$doc_aadhar->doc_name}}" alt="image-1" width="200" height="180" /></a>
                        </div>

                          @elseif(strtolower($ext)=='pdf')
                          <div class="col-md-12" style="border:1px solid #dcdfdf">
                          
                            <a id="link" href="{{$doc_aadhar->doc_name}}" target="_blank" width="">Download PDF Document</a>
                          </div>
                         @endif           
                          @endif
                        </div>
                        <div class="row">
                         
                        <div class="form-group col-md-4">
                            <label >New Aadhaar Number</label>
                            {{trim($row->aadhar_no)}}
                          </div>
                         
                         
                        
                        </div>
                         @endif
                         @if($row->no_mobile==1)
                        <div class="row">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>Uploaded Mobile Details</h3></div>
                        </div>
                        <div class="row">
                      
                        <div class="form-group col-md-4">
                            <label>New Mobile Number</label>
                             {{trim($row->mobile_no)}}
                          </div>
                         
                          
                       </div>
                        @endif
                       <center> <button type="submit" id="submit" value="Submit"
                          class="btn btn-success success btn-lg modal-submit">@if($designation_id=='Verifier') Verify @endif @if($designation_id=='Approver') Approve @endif</button>
                        <button type="button" id="submitting" value="Submit" class="btn btn-success success btn-lg"
                          disabled>Submitting please wait</button></center>
                      

                      </div>
                    </form>

                      
                <div class="row">
                   
               </div>

                
  </div>

  
                         







                

                     
                   </div>


                       </div>
                 
                      


            </div>


          </div>
          
           
        </div>
</section>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset ("/bower_components/AdminLTE/dist/js/app.min.js") }}" type="text/javascript"></script>
<script src="{{ URL::asset('js/validateAdhar.js') }}"></script>

<script type="text/javascript">
$(document).ready(function(){
  $("#submitting").hide();
  $('#submit').click(function(){
     $("#submitting").show();
      $("#submit").hide();
      // $("#register_form").submit();
  });
});
function client_validation(){

}
</script>
