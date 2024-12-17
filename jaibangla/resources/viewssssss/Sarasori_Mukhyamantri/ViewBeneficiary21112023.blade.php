@extends('Sarasori_Mukhyamantri.base')

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
.required-field::after {
      content: "*";
      color: red;
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
             
            </div>
            <div class="modal-body">
            <a href="mark-sm?scheme_id={{$scheme_id}}"> 
                <img width="50px;" style="pull-right" src="{{ asset("images/back.png") }}" alt="Back" /></a>
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
                        <div ><strong>Aadhaar No., if available:</strong> {{$row->aadhar_no}}</div>
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
                         <div ><strong>Mobile Number:</strong>{{$row->mobile_no}}</div>
                       
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
                         <div ><strong>Bank Account No.:</strong> {{$row->bank_code}}</div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>IFS Code:</strong>{{$row->bank_ifsc}}</div>
                       
                        </div>

                      </div>

               
                @if(count($docs)>0)
                <div class="row color1">
                  <div class="col-md-12"><h3>Enclosure List(Self Attested)</h3></div>
                </div>
                <div class="row">
                @foreach($docs as $doc)
                  @if($doc->attched_document !="")
                  <div class="col-md-4"  >
                    <strong>{{$doc->doc_type_name}} :</strong> 
                  </div>
                  <div class="col-md-8" style="padding-bottom: 30px; ">
                      <?php 
                        $document_mime_type = $doc->document_mime_type;
                        if($document_mime_type=='image/jpeg'){
                          $image_extension='jpg';
                        }else if($document_mime_type=='image/png'){
                           $image_extension='png';
                        }else if($document_mime_type=='application/pdf'){
                          $image_extension='pdf';
                        }
                        $row_image = "data:image/".$image_extension.";base64,".$doc->attched_document;
                       ?> 
                       @if(strtolower($image_extension)=='jpg' || strtolower($image_extension)=='png')
                       <div class="col-md-12" style="border:1px solid #dcdfdf">
                        <a class="example-image-link" href="{{$row_image}}" data-lightbox="example-1">
                        <img class="example-image" src="{{$row_image}}" alt="image-1" width="200" height="180" /></a>
                        </div>
                       
                        @elseif(strtolower($image_extension)=='pdf')
                        <div class="col-md-12" style="border:1px solid #dcdfdf">
                        <a id="link"  href="{{route('jbDownload', ['scheme_id' => $doc->scheme_id,'created_by_dist_code' => $doc->created_by_dist_code,'beneficiary_id' => $doc->beneficiary_id,'document_type' => $doc->document_type])}}" target="_blank" style="color: #4324ef" width="">Download PDF Document</a>
                        </div>
                        @else
                        <div class="col-md-12" style="border:1px solid #dcdfdf">
                        <p>No File Found</p>
                        </div>
                        @endif     
                        

                  </div>
                  @endif         
                  @endforeach
               </div>
               @endif
               {{-- <div class="row color1">
                  <div class="col-md-12"><h3>Sarasori Mukhyamantri</h3></div>
                </div>   --}}
                @if($designation_id_old=='Verifier' && is_null($row->sm_flag) && ($row->no_aadhar == 1 || $row->no_mobile == 1 || $row->dup_aadhar == 1 || $row->dup_mobile == 1 || $row->dup_bank == 1))
                <div class="row">
                <form method="POST" action="{{route('SmPostRevert')}}"  name="formReject" id="formReject">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="scheme_id" name="scheme_id" value="{{$row->scheme_id}}">
                 <input type="hidden" id="beneficiary_id" name="beneficiary_id" value="{{$row->id}}"/>
                {{-- <div class="form-group col-md-6">
                          <label class="required-field">Mobile Number</label>
                 <input type="text" name="sm_mobile_no" id="sm_mobile_no" class="form-control NumOnly" placeholder="Mobile No."  value=""  maxlength='10'/>
                 <span id="error_sm_mobile_no" class="text-danger"></span>
                </div> --}}
                <div class="form-group col-md-6">
                <button type="submit"  class="btn btn-danger danger btn-lg" id="modal-submit" style="margin-top:20px; margin-left: 380px;">Revert</button>
                        <button type="button" id="submitting" value="Submit" class="btn btn-danger btn-lg"
                          disabled style="display:none;">Submitting please wait</button>
                </div>
                @endif
                @if($row->sm_flag==1)
                <div class="form-group col-md-6">
                          <label class="required-field">Mobile Number</label>
                 <span id="" class="text-danger">{{$row->sm_mobile_no}}</span>
                </div>
                <br/>
                @endif
                </form>
                     
                   </div>


                       </div>
                 
                      


            </div>


          </div>
          
           
        </div>
</section>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
$(document).ready(function(){
  $(".NumOnly").keyup(function(event) {
              
              $(this).val($(this).val().replace(/[^\d].+/, ""));
                  if ((event.which < 48 || event.which > 57)) {
                      event.preventDefault();
                  }
    }); 
    
//   $('#modal-submit').on('click',function(e){
//          e.preventDefault();
//         var error_sm_mobile_no ='';
//         if($.trim($('#sm_mobile_no').val()).length == 0)
//         {
//           error_sm_mobile_no = 'Mobile Number is required';
//         $('#error_sm_mobile_no').text(error_sm_mobile_no);
//         $('#sm_mobile_no').addClass('has-error');
//         }
//         else
//         {
//           if($.trim($('#sm_mobile_no').val()).length !=10)
//           {
//             error_sm_mobile_no = 'Mobile Number must be 10 digit';
//           $('#error_sm_mobile_no').text(error_sm_mobile_no);
//           $('#sm_mobile_no').addClass('has-error');
//           }
//           else
//           {
//             error_sm_mobile_no = '';
//             $('#error_sm_mobile_no').text(error_sm_mobile_no);
//             $('#sm_mobile_no').removeClass('has-error');

//           }
//         }
//         if(error_sm_mobile_no == ''){
//           $("#modal-submit").hide();
//           $("#submitting").show();
//           $("#submit_loader").show();
//           $("#formReject").submit();
//         }
//         else{
//           return false;
//         }
//     //$("#register_form").submit();
// });
});
</script>
<!-- <script>
function printfunction() {
  // var content=document.getElementById('divToPrint');
  // window.document.write('<html><head><style>.row{ margin-right: 0px!important; margin-left: 0px!important; margin-top: 1%!important;}.section1{border:1.5pxsolid#9187878c;margin:2%;padding:2%;}.color1{margin:0%!important;background-color: #5f9ea061;}.modal_field_name{ float:left;font-weight: 700;margin-right:1%;padding-top:1%;margin-top:1%;}.modal_field_value{margin-right:1%;padding-top:1%;margin-top:1%;}</style></head><body>'+content.innerHTML+'</body></html>');
  window.print();
}
</script> -->
