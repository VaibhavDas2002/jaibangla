@extends('employees-mgmt.base_pension')

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
               
            </div>
            <div class="modal-body">
              <!--   <h4 class="example-screen" style="text-align: center;">Please Verify or Reject Employee's application with Comments</h4> -->
                

                <!-- We display the details entered by the user here -->
                <div class="section1">
                  <div class="row">
                  <div class="col-md-6">
                    <h3 style="text-align: center; color:red;">Application ID:{{$row->getBenidAttribute()}}
                      
                      </h3>
                  </div>
                  <div class="col-md-6">
                    <h3 style="text-align: center;">Phase:{{$row->app_phase}}
                      
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
                            <div ><strong>Gender:</strong>  {{$row->gender}}</div>
                            
                        </div>
                        
                        @if(!is_null($row->dob))
                        <div class="col-md-6">
                          <div ><strong>Date of Birth (DD-MM-YYYY):</strong> {{date('d/m/Y', strtotime($row->dob)) }}</div>
                         
                        </div>
                        @endif

                        <div class="col-md-6" >
                          <div ><strong>Age :</strong> {{$row->ben_age}}</div>
                        </div>

                    


                    
                    <div class="col-md-6" >
                      <div ><strong>Father's Name :</strong> {{$row->father_fname}} {{$row->father_mname}} {{$row->father_lname}}</div>
                    </div>

                    <div class="col-md-6" >
                      <div ><strong>Mother's Name :</strong> {{$row->mother_fname}} {{$row->mother_mname}} {{$row->mother_lname}}</div>
                    </div>

                     
                    
                      
                        
                       
                      

                       
                        
                        <div class="col-md-6">
                          <div ><strong>Marital Status:</strong> {{$row->marital_status}}</div>
                        </div>

                        <div class="col-md-6">
                          <div ><strong>Temple Type:</strong> {{$row->temple_type}}</div>
                        </div>
                        
                         <div class="col-md-6" >
                         <div ><strong>Spouse Name :</strong> {{$row->spouse_fname}} {{$row->spouse_mname}} {{$row->spouse_lname}}</div>
                         </div>

                                       
                     

                   
                     
                    
                      </div>
                    

                      <div class="row color1"  style="margin:10px 0px" >
                          <div class="col-md-12"><h3>Personal Identification Number(S)</h3></div>
                      </div>

                      <div class="col-md-6">
                        <div ><strong>Digital Ration Card No.:</strong> {{$row->ration_card_no}} </div>
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

                        

                      <div class="row ">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>Contact Details</h3></div>
                      </div>

                       <div class="col-md-6">
                         <div ><strong>State:</strong> West Bengal</div>
                       
                        </div>


                        <div class="col-md-6">
                         <div ><strong>District:</strong>  {{$district_name}}</div>
                       
                        </div>

                         <div class="col-md-6">
                         <div ><strong>Assembly Constitution:</strong>  {{$row->assembly_name}}</div>
                       
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
                      @if($housingrecord!='')
                      <div class="row ">
                          <div class="col-md-12 color1"  style="margin:10px 0px"><h3>Land Details</h3></div>
                      </div>
                      <div class="col-md-6">
                         <div ><strong>Name of the Mouza:</strong>  {{$housingrecord->mouza_name}}</div>
                       
                        </div>
                        <div class="col-md-6">
                         <div ><strong>J.L.No:</strong>  {{$housingrecord->land_jlno}}</div>
                       
                        </div>
                        <div class="col-md-6">
                         <div ><strong>Khatian No:</strong>  {{$housingrecord->khatian_no}}</div>
                       
                        </div>
                        <div class="col-md-6">
                         <div ><strong>Plot No:</strong>  {{$housingrecord->plot_no}}</div>
                       
                        </div>
                        <div class="col-md-6">
                         <div ><strong>Area:</strong>  {{$housingrecord->land_area}}</div>
                       
                        </div>
                        <div class="col-md-6">
                         <div ><strong>In the Name of:</strong>  {{$housingrecord->land_holdername}}</div>
                       
                        </div>
                      @endif
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
               @if($housingrecord!='')
               <div class="row color1">
                <div class="col-md-12">
                <h3>Have Pucca dwelling house - {{$housingrecord->pucca_house_y_n?'Yes':'No'}}</h3>
                </div>
               </div> 
               @endif
                
  </div>

    <div class="row">        
     
      <br/>
    
      @if($row->next_level_role_id==null)
      <div class="row color1">
        <div class="col-md-12">
            <h3>Update Verification Details</h3>
        </div>
      </div>
      <form method="post" action="{{ route('forward-purohits')}}">
        {{ csrf_field() }}
        
        <input type="hidden" name="benId" value="{{$row->id}}">

        <div class="section1  example-screen">
          <div class="row" style="width:100%; padding: 2%; margin:1%;">
            <div class="col-md-3">
              <b>Monthly Pension Scheme</b>
            </div>  
            <div class="col-md-2">
              <select class="form-control" name="pensionverification" id="pensionverification">
                <option value="0">Rejected</option>
                <option value="1">Verified</option>
              </select>   
            </div>
            <div class="col-md-7">
              <input type="text" name="pensionverificationcomment" id="pensionverificationcomment" class="form-control" placeholder="Comments" /> 
            </div>
          </div>
          @if($housingrecord!='')
          <input type="hidden" name="housingBenId" value="{{$housingrecord->id}}">
          <div class="row" style="width:100%; padding: 2%; margin:1%;">
            <div class="col-md-3">
              <b>Housing Scheme</b>
            </div>  
            <div class="col-md-2">
              <select class="form-control" name="housingverification" id="housingverification">
                <option value="0">Rejected</option>
                <option value="1">Verified</option>
              </select>   
            </div>
            <div class="col-md-7">
              <input type="text" name="housingverificationcomment" id="housingverificationcomment" class="form-control" placeholder="Comments" /> 
            </div>
          </div>
          @endif
          <div class="row">                
            <div class="col-md-12" style="text-align: right;"><input type="submit" name="submit" value="Complete Verification" id="Verifysubmit" class="btn btn-success btn-lg"></div>
          </div>
        </div>
      </form>
      @elseif($row->next_level_role_id>0)
      <div class="row color1">
        <div class="col-md-12">
          @if($parent_id == 0)
            <h3>Update Approver Details</h3>
          @else
            <h3>Update Recommender Details</h3>
          @endif  
        </div>
      </div>
      <form method="post" action="{{ route('forward-approve-purohits')}}">
        {{ csrf_field() }}      
        <input type="hidden" name="benId" value="{{$row->id}}">
        <div class="section1  example-screen">
          <div class="row" style="width:100%; padding: 2%; margin:1%;">
            <div class="col-md-3">
              <b>Monthly Pension Scheme</b>
            </div>  
            <div class="col-md-2">
              <select class="form-control" name="pensionapproval" id="pensionapproval">
              
                <option value="0">Rejected</option>
                @if($parent_id == 0)
                <option value="1">Approved</option>
                @else
                <option value="1">Recommend</option>
                @endif
              </select>   
            </div>
            <div class="col-md-7">
              <input type="text" name="pensionapprovalcomment" id="pensionapprovalcomment" class="form-control" placeholder="Comments" /> 
            </div>
          </div>
          @if($housingrecord!='')
          <input type="hidden" name="housingBenId" value="{{$housingrecord->id}}">
          <div class="row" style="width:100%; padding: 2%; margin:1%;">
            <div class="col-md-3">
              <b>Housing Scheme</b>
            </div>  
            <div class="col-md-2">
              <select class="form-control" name="housingapproval" id="housingapproval">
                <option value="0">Rejected</option>
                @if($parent_id == 0)
                <option value="1">Approved</option>
                @else
                <option value="1">Recommend</option>
                @endif
              </select>   
            </div>
            <div class="col-md-7">
              <input type="text" name="housingapprovalcomment" id="housingapprovalcomment" class="form-control" placeholder="Comments" /> 
            </div>
          </div>
          @endif
          <div class="row">                
            <div class="col-md-12" style="text-align: right;"><input type="submit" name="submit" value="Process Complete" id="Approvesubmit" class="btn btn-success btn-lg"></div>
          </div>
            </div>
      </form>
      @endif    
    </div>                        
  </div>
</div>
                 
                      


            </div>


          </div>
          
           
        </div>
</section>
@endsection
<!-- <script>
function printfunction() {
  // var content=document.getElementById('divToPrint');
  // window.document.write('<html><head><style>.row{ margin-right: 0px!important; margin-left: 0px!important; margin-top: 1%!important;}.section1{border:1.5pxsolid#9187878c;margin:2%;padding:2%;}.color1{margin:0%!important;background-color: #5f9ea061;}.modal_field_name{ float:left;font-weight: 700;margin-right:1%;padding-top:1%;margin-top:1%;}.modal_field_value{margin-right:1%;padding-top:1%;margin-top:1%;}</style></head><body>'+content.innerHTML+'</body></html>');
  window.print();
}
</script> -->
