@extends('employees-mgmt.base')

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
/*.color1{
  margin: 0%!important;
  background-color: #7fffd4;
}*/

.modal-header{
  background-color: #7fffd4;
}

@media print {
  .example-screen {
       display: none;
    }

  .row{
  margin-right: 0px!important;
  margin-left: 0px!important;
 /* margin-top: 0.25cm!important;*/
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
 /* padding-top:1cm!important;*/
 /* margin-top:1cm!important;*/
  /*border:2px solid blue;*/
}

.modal_field_value{
  /*margin-right:0.35cm!important;*/
  padding-top:0.30cm!important;
 /* margin-top:3cm!important;*/
  /* border:2px solid blue;*/
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
  /*//border:3px solid red;*/
}
}


</style>
<section class="content" id="divToPrint">
<div class="modal-fade" tabindex="-1" role="document">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="example-screen">
               <!--  <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
                <!-- <span aria-hidden="true">&times;</span> -->
               <!-- </button> -->
               <h2 class="modal-title " style="text-align: center;">Print or Save </h2>
               
            </div>
            <div class="modal-body">
                <h4 class="example-screen" style="text-align: center;">You can save or take a print out of your application</h4>
                

                <!-- We display the details entered by the user here -->
                <div class="section1">
                  <div class="row">
                  <div class="col-md-12">
                    <h3 style="text-align: center; color:red;">Application ID: {{$single_employee_details->id}}</h3>
                  </div>
                  </div>
                <div class="row color1">
                  
                  <div class="col-md-12"><h3>Personal Details</h3></div>
                </div>
                <div class="row">
                    <div class="col-md-12" >
                      <div ><strong>Name :</strong>  {{$single_employee_details->title}}  {{$single_employee_details->first_name}} {{$single_employee_details->middle_name}} {{$single_employee_details->last_name}}</div>
                    </div>

                     <div class="col-md-12" >
                      <div ><strong>Name of Father/Mother/Spouse/Guardian :</strong>  {{$single_employee_details->guardian_name}}</div>
                    </div>
                    
                </div>
                    
                    
                     
                    <div class="row">
                        <div class="col-md-4">
                            <div ><strong>Relationship:</strong> {{$single_employee_details->guardian_relation}}</div>
                            
                        </div>
                        <div class="col-md-4">
                          <div ><strong>Date of Birth:</strong> {{ date('d/m/Y', strtotime($single_employee_details->dob))}}</div>
                         
                        </div>
                        <div class="col-md-4">
                            <div><strong>Gender:</strong> {{$single_employee_details->gender}} </div>
                        </div>

                        <div class="col-md-4">
                          <div><strong>Caste Category: {{$single_employee_details->caste_category}}</strong></div>
                        </div>
                     
                        <div class="col-md-4">
                          <div><strong>Whether engaged under PWD:</strong> {{$single_employee_details->pwd}}</div>
                        </div>

                         <div class="col-md-4">
                          <div><strong>Mobile Number 1: {{$single_employee_details->mobile_number_1}}</strong></div>
                         
                        </div>
                        <div class="col-md-4">
                          <div ><strong>Mobile Number 2:</strong>  @if($single_employee_details->mobile_number_2!=null)
                    
                              {{$single_employee_details->mobile_number_2}} 
                              
                              @else Nil
                              
                              @endif</div>
                        </div>
                        <div class="col-md-4">
                         <div><strong>Email:</strong> {{$single_employee_details->email}}</div>
                       </div>
                        <div class="col-md-4">
                          <div ><strong>Marital Status:</strong> {{$single_employee_details->marital_status}}</div>
                        </div>

                        <div class="col-md-4">
                        <div ><strong>Identification Mark:</strong>   @if($single_employee_details->identification_mark!=null)
                  
                            {{$single_employee_details->identification_mark}}
                            
                            @else Nil
                            
                            @endif</div>
                        
                      </div>
                      <div class="col-md-6">
                        <div ><strong>Blood Group:</strong> {{$single_employee_details->blood_group}}</div>
                      </div>
                      <hr>

                    </div>
                    <div class="row setPos" >
                       <div class="col-md-12">
                        <div ><strong>Name of the person in case of Emergency :</strong> {{ $single_employee_details->person_name_emergency}} </div>
                       
                      </div>
                      <div class="col-md-12">
                         <div><strong>Mobile No of the person in case of Emergency:</strong> {{$single_employee_details->person_emergency_mobile}}</div>
                      </div>

                       <div class="col-md-12">
                        <div><strong>Present Address: </strong></div>
                        {{$single_employee_details->present_address_line1}}
                        </div>

                         <div class="col-md-4">
                        <div ><strong>Police Station:</strong> {{$single_employee_details->present_address_police_station}} </div>
                      </div>

                       <div class="col-md-4">
                        <div ><strong>District:</strong> {{$single_employee_details->Districtpresent->district_name}}</div>
                      </div>

                      <div class="col-md-4">
                        <div ><strong>Pincode:</strong> {{$single_employee_details->present_address_pincode}}</div>
                        
                      </div>

                      </div>

                    <div class="setPos" >
                      <div class="col-md-12 " >
                        <div ><strong>Permanent Address:</strong></div>
                        <div >
                          {{$single_employee_details->permanent_address_line1}}
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div ><strong>Police Station:</strong> {{$single_employee_details->permanent_address_poilce_station}}</div>
                      </div>
                      <div class="col-md-4">
                        <div ><strong>District: </strong>  {{$single_employee_details->Districtpermanent->district_name}}</div>
                      </div>
                      <div class="col-md-4">
                        <div ><strong>Pincode:</strong> {{$single_employee_details->permanent_address_pincode}}</div>
                       
                      </div>
                    </div>

                      <div class="row color1" >
                          <div class="col-md-12"><h3>Qualifications</h3></div>
                      </div>

                     <div class="col-md-6">
                        <div ><strong>Highest Educational Qualification:</strong> {{$single_employee_details->highest_education}} </div>
                      </div>

                      <div class="col-md-6">
                        <div ><strong>Technical Qualification: </strong> @if($single_employee_details->technical_qualification!=null)
                  
                            {{$single_employee_details->technical_qualification}}
                            
                            @else Nil
                            
                            @endif</div>
                        </div>

                        <div class="col-md-6">
                        <div ><strong>Professional Qualification:</strong>  @if($single_employee_details->professional_qualification!=null)
                  
                            {{$single_employee_details->professional_qualification}}
                            
                            @else Nil
                            
                            @endif</div>
                        </div>

                        <div class="col-md-6">
                        <div ><strong>If Others,Please Specify: </strong>  @if($single_employee_details->other_professional_qualification!=null)
                  
                            {{$single_employee_details->other_professional_qualification}}
                            
                            @else Nil
                            
                            @endif</div>
                        
                        </div>

                        <div class="col-md-12">
                         <div ><strong>If Professional qualification is MBBS/BDS/BHMS/BUMS/BAMS/ Nursing Staff /Pharmacist, then Registration of respective council:</strong> @if($single_employee_details->registration!=null)
                  
                            {{$single_employee_details->registration}}
                            
                            @else Nil
                            @endif</div>
                       
                        </div>

                      

                       <div class="row color1" >
                            <div class="col-md-12"><h3>Salary Account Details</h3></div>
                        </div>


                       <div class="col-md-4">
                        <div ><strong>PAN:</strong> {{$single_employee_details->pan}}</div>
                        </div>

                        <div class="col-md-4">
                        <div ><strong>Bank Account Number (Salary Account): </strong>{{$single_employee_details->bank_account_number}}</div>
                        </div>

                        <div class="col-md-4">
                         <div ><strong>Name Of the Bank:</strong> {{$single_employee_details->name_of_bank}} </div>
                        </div>

                         <div class="col-md-4">
                          <div ><strong>Name Of the Bank Branch:</strong> {{$single_employee_details->bank_branch}} </div>
                        </div>

                         <div class="col-md-12">
                        <div><strong>IFSC Code of Salary Account: </strong>{{$single_employee_details->bank_ifsc_code}} </div>
                        </div>

                      <div class="row color1">
                          <div class="col-md-12"><h3>Experience Details</h3></div>
                      </div>

                      <div class="col-md-12">
                        <div ><strong>If previously engaged under NHM / NUHM at any level (If it is different from present post):</strong></div>
                        <div >
                          @if($single_employee_details->exp_engaged_under_nhm==1)
                          
                            Yes
                          
                          @elseif($single_employee_details->exp_engaged_under_nhm==0)
                          
                            No
                          @endif
                        </div>
                        </div>
                     

                  </div>
                    
                     
                   </div>

                  

                    
                     <div class="section1">
                      
                       <div class="row">
                        
                       </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="modal_field_name">Designation:</div>
                              <div class="modal_field_value" id="designation_nhm_modal">
                                      @if($single_employee_details->exp_engaged_under_nhm==1)
                                      
                                          @if($single_employee_details->exp_designation_nhm !=null)
                                              {{$single_employee_details->exp_designation_nhm}}
                                          @else
                                              Nil
                                          @endif
                                      
                                      @elseif($single_employee_details->exp_engaged_under_nhm==0)
                                      
                                        Nil
                                      @endif
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="modal_field_name">Duration:</div>
                            <div class="modal_field_value" id="e_duration_nhm_modal">
                              @if($single_employee_details->exp_engaged_under_nhm==1)
                              
                                    @if($single_employee_details->exp_duration_from_nhm!=null && $single_employee_details->exp_duration_to_nhm!=null)
                                            {{date('d/m/Y', strtotime($single_employee_details->exp_duration_from_nhm)) }} To {{ date('d/m/Y', strtotime($single_employee_details->exp_duration_to_nhm)) }}
                                    @elseif($single_employee_details->exp_duration_from_nhm!=null)
                                            {{date('d/m/Y', strtotime($single_employee_details->exp_duration_from_nhm)) }} To Nil
                                    @elseif($single_employee_details->exp_duration_to_nhm!=null)
                                            Nil To {{ date('d/m/Y', strtotime($single_employee_details->exp_duration_to_nhm)) }}
                                    @elseif($single_employee_details->exp_duration_from_nhm==null && $single_employee_details->exp_duration_to_nhm==null)
                                              Nil
                                    @endif
                              @elseif($single_employee_details->exp_engaged_under_nhm==0)
                              
                                Nil
                              @endif
                            </div>
                          </div>
                          <div class="col-md-6">
                               <div class="field-name">Experience(Years & Months):</div>
                                <div class="field-value">
                                @if($single_employee_details->exp_engaged_under_nhm==1)
                                                
                                   @if($single_employee_details->experience_year_month_nhm!=null)
                                        {{$single_employee_details->experience_year_month_nhm}}
                                   @else
                                        Nil
                                   @endif 

                                                 
                                                  
                                @elseif($single_employee_details->exp_engaged_under_nhm==0)
                                                
                                    Nil
                                @endif

                                  
                                </div> 
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="modal_field_name">Last Monthly Remuneration Drawn:</div>
                            <div class="modal_field_value" id="e_remuneration_nhm_modal">
                              @if($single_employee_details->exp_engaged_under_nhm==1)
                                
                                    @if($single_employee_details->last_monthly_remuneration_nhm!=null)
                                        {{$single_employee_details->last_monthly_remuneration_nhm}}
                                    @else
                                        Nil
                                    @endif 
                                
                                @elseif($single_employee_details->exp_engaged_under_nhm==0)
                                
                                  Nil
                                @endif
                            </div>
                          </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                               <div class="field-name">Remarks:</div>
                                  <div class="field-value">
                                    @if($single_employee_details->exp_engaged_under_nhm==1)
                                                    
                                       @if($single_employee_details->e_remarks_nhm!=null)
                                            {{$single_employee_details->e_remarks_nhm}}
                                       @else
                                            Nil
                                       @endif 

                                                     
                                                      
                                    @elseif($single_employee_details->exp_engaged_under_hfw==0)
                                                    
                                        Nil
                                    @endif
                                    
                                     </div> 
                            </div>
                          </div>

                      <!-- </div> -->
                      

                      <div class="row">
                        <div class="col-md-12">
                        <div class="modal_field_name">If engaged in any project/programme/scheme under H & FW or any other Department of Government of West Bengal previously:</div>
                        <div class="modal_field_value" id="engaged_or_not_hfw_modal">
                                  @if($single_employee_details->exp_engaged_under_hfw==1)
                                  
                                    Yes
                                  
                                  @elseif($single_employee_details->exp_engaged_under_hfw==0)
                                  
                                    No
                                  @endif
                        </div>
                        </div>
                      </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="modal_field_name">Designation:</div>
                            <div class="modal_field_value" id="designation_hfw_modal">
                                @if($single_employee_details->exp_engaged_under_hfw==1)
                                    
                                      @if($single_employee_details->exp_designation_hfw !=null)
                                              {{$single_employee_details->exp_designation_hfw}}
                                      @else
                                              Nil
                                      @endif
                                
                              
                              @elseif($single_employee_details->exp_engaged_under_hfw==0)
                              
                                Nil
                              @endif
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="modal_field_name">Duration:</div>
                            <div class="modal_field_value" id="e_duration_hfw_modal">
                                @if($single_employee_details->exp_engaged_under_hfw==1)
                                  
                                  @if($single_employee_details->exp_duration_from_hfw!=null && $single_employee_details->exp_duration_to_hfw!=null)
                                           {{ date('d/m/Y', strtotime($single_employee_details->exp_duration_from_hfw)) }} To {{ date('d/m/Y', strtotime($single_employee_details->exp_duration_to_hfw)) }} 
                                    @elseif($single_employee_details->exp_duration_from_hfw!=null)
                                            {{date('d/m/Y', strtotime($single_employee_details->exp_duration_from_hfw)) }} To Nil
                                    @elseif($single_employee_details->exp_duration_to_hfw!=null)
                                            Nil To {{ date('d/m/Y', strtotime($single_employee_details->exp_duration_to_hfw)) }}
                                    @elseif($single_employee_details->exp_duration_from_hfw==null && $single_employee_details->exp_duration_to_hfw==null)
                                            Nil
                                    @endif
                                  


                                @elseif($single_employee_details->exp_engaged_under_hfw==0)
                                
                                  Nil
                                @endif
                            </div>
                          </div>
                                <div class="col-md-6">
                                    <div class="field-name">Experience(Years & Months):</div>
                                      <div class="field-value">
                                        @if($single_employee_details->exp_engaged_under_hfw==1)
                                                        
                                           @if($single_employee_details->experience_year_month_hfw!=null)
                                                {{$single_employee_details->experience_year_month_hfw}}
                                           @else
                                                Nil
                                           @endif 

                                                   
                                                    
                                        @elseif($single_employee_details->exp_engaged_under_hfw==0)
                                                          
                                              Nil
                                          @endif

                                    
                                  </div> 
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="modal_field_name">Last Monthly Remuneration Drawn:</div>
                            <div class="modal_field_value" id="e_remuneration_hfw_modal">
                                @if($single_employee_details->exp_engaged_under_hfw==1)
                                
                                  @if($single_employee_details->exp_last_monthly_remuneration_hfw!=null)
                                        {{$single_employee_details->exp_last_monthly_remuneration_hfw}}
                                    @else
                                        Nil
                                    @endif 

                                 
                                  
                                @elseif($single_employee_details->exp_engaged_under_hfw==0)
                                
                                  Nil
                                @endif
                            </div>
                          </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                               <div class="field-name">Remarks:</div>
                                  <div class="field-value">
                                    @if($single_employee_details->exp_engaged_under_hfw==1)
                                                    
                                       @if($single_employee_details->e_remarks_hfw!=null)
                                            {{$single_employee_details->e_remarks_hfw}}
                                       @else
                                            Nil
                                       @endif 

                                                     
                                                      
                                    @elseif($single_employee_details->exp_engaged_under_hfw==0)
                                                    
                                        Nil
                                    @endif
                                    
                                     </div> 
                            </div>
                        </div>
                      <!-- </div> -->
                      <div class="row">
                        <div class="col-md-12"><h4 style="text-decoration:underline;">Details of Engagement in present post under NHM/NUHM</h4></div>
                        <div class="row">
                          <div class="col-md-12">
                             <div class="modal_field_name">Advertisement Number:</div>
                            <div class="modal_field_value" id="advertisement_number_modal">
                              @if($single_employee_details->advertisement_number!=null)
                  
                              {{$single_employee_details->advertisement_number}}
                              
                              @else Nil
                              
                              @endif  
                            </div>
                          </div>
                        </div>
                          <div class="row">
                             <div class="col-md-6">
                             <div class="modal_field_name">Appointing Authority:</div>
                             <div class="modal_field_value" id="appointing_authority_modal">
                               {{$single_employee_details->appointing_authority}} 
                             </div>
                             </div>
                             <div class="col-md-6">
                             <div class="modal_field_name">Contractual Employement Under:</div>
                             <div class="modal_field_value" id="contractual_employment_under_modal">
                               {{$single_employee_details->contractual_employement_under}} 
                             </div>
                             </div>
                             
                          </div>
                          <div class="row">
                             <div class="col-md-6">
                             <div class="modal_field_name">Service Category:</div>
                             <div class="modal_field_value" id="service_category_modal">
                                @if($single_employee_details->service_category==1)
                  
                                Service Delivery
                              
                              @elseif($single_employee_details->service_category==2)
                              
                                Programme Management
                              @endif
                             </div>
                             </div>
                             <div class="col-md-6">
                             <div class="modal_field_name">Contractual under NHM - Major Programme Head:</div>
                             <div class="modal_field_value" id="contractual_under_nhm_modal">
                               {{$single_employee_details->majorProgammeHeadMaster->name}} 
                             </div>
                             </div>
                             
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                             <div class="modal_field_name">Programme Head:</div>
                             <div class="modal_field_value" id="programme_head_modal">
                               {{$single_employee_details->programmeHeadMaster->name}} 
                             </div>
                             </div>
                             <div class="col-md-6">
                             <div class="modal_field_name">Designation List:</div>
                             <div class="modal_field_value" id="designation_list_modal">
                               {{$single_employee_details->designationMaster->name}} 
                             </div>
                             </div>
                          </div>
                            <div class="row">
                             <div class="col-md-12">
                             <div class="modal_field_name">Date of joining in present designation:</div>
                             <div class="modal_field_value" id="date_of_joining_modal">
                               {{ date('d/m/Y', strtotime($single_employee_details->doj_present_designation)) }} 
                             </div>
                             </div>
                            </div>
                            <div class="row">
                             <div class="col-md-12">
                             <div class="modal_field_name">Consolidated remuneration at the time of joining:</div>
                             <div class="modal_field_value" id="con_rem_time_joining_modal">
                               {{$single_employee_details->consolidated_remuneration_doj}} 
                             </div>
                             </div>
                            </div>
                             <div class="row">
                             <div class="col-md-12">
                             <div class="modal_field_name">Consolidated Monthly remuneration at the time of joining:</div>
                             <div class="modal_field_value" id="con_rem_time_joining_modal">
                               {{$single_employee_details->con_monthly_salary_joining}} 
                             </div>
                             </div>
                            </div>
                            <div class="row">
                             <div class="col-md-12">
                             <div class="modal_field_name">Date of joining in present place of posting:</div>
                             <div class="modal_field_value" id="date_of_joining_in_posting_modal">
                               {{date('d/m/Y', strtotime($single_employee_details->doj_present_posting))}} 
                             </div>
                             </div>
                            </div>
                          <div class="row">
                            <div class="col-md-12">
                              <div class="modal_field_name">Monthly Remuneration as on 01.04.2019:</div>
                              <div class="modal_field_value" id="monthly_rem_modal">
                                {{$single_employee_details->monthly_remuneration}} 
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="modal_field_name">Posting Level:</div>
                              <div class="modal_field_value" id="posting_level_modal">
                                {{$single_employee_details->posting_level}} 
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="modal_field_name">Posting Place:</div>
                              <div class="modal_field_value" id="posting_place_modal">
                                {{$single_employee_details->posting_place}} 
                              </div>
                            </div>
                          </div>
                        </div>
                     
<!-- 
                      <div class="row">
                        <div class="col-md-12"><h4 style="text-decoration:underline;">Leave Status</h4></div>

                          <div class="row">
                             <div class="col-md-6">
                             <div class="modal_field_name">Casual leave availed:</div>
                             <div class="modal_field_value" id="casual_leave_availed_modal"></div>
                             </div>
                             <div class="col-md-6">
                             <div class="modal_field_name">Earned leave availed:</div>
                             <div class="modal_field_value" id="earned_leave_availed_modal"></div>
                             </div>
                             
                          </div>

                        </div> -->
                      </div>
                       </div>
                 
                      


            </div>
          </div>

            <div class="modal-footer example-screen" style="text-align: center;">
                
                <button type="button" onclick="printfunction()" class="btn btn-success btn-lg" data-dismiss="modal">Print</button>
                <a class="btn btn-default btn-lg" href="{{ url('nhmemployee') }}">Cancel</a>
               <!--  <button type="button" class="btn btn-default btn-lg" href="{{ url('nhmemployee') }}">Cancel</button> -->
                <!-- <input type="button" class="btn btn-success button-lg" name="btn_submit_preview"    
                id="btn_submit_preview" value="Preview and Submit" data-toggle="modal" data-target="#confirm-submit"> -->
            </div>
        </div>
</section>
@endsection
<script>
function printfunction() {
  // var content=document.getElementById('divToPrint');
  // window.document.write('<html><head><style>.row{ margin-right: 0px!important; margin-left: 0px!important; margin-top: 1%!important;}.section1{border:1.5pxsolid#9187878c;margin:2%;padding:2%;}.color1{margin:0%!important;background-color: #5f9ea061;}.modal_field_name{ float:left;font-weight: 700;margin-right:1%;padding-top:1%;margin-top:1%;}.modal_field_value{margin-right:1%;padding-top:1%;margin-top:1%;}</style></head><body>'+content.innerHTML+'</body></html>');
  window.print();
}
</script>