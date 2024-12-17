@extends('employees-mgmt.base')

@section('action-content')
<style>

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
.section1{
    border: 1.5px solid #9187878c;
    margin: 2%;
    padding: 2%;
}
.color1{
  margin: 0%!important;
  background-color: #5f9ea061;
}

@media print {
  /*.content {
    margin: 0;
    border:5px solid red;
    color: ;
    background-color: blue;
  }*/

  .row{
  margin-right: 0px!important;
  margin-left: 0px!important;
  margin-top: 0.25cm!important;
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
 .example-screen {
       display: none;
    }
}
</style>
<section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-8">
          <h3 class="box-title">Employee Detail</h3>
        </div>
        <!-- <div class="col-sm-4">
          <a class="btn btn-primary" href="{{ route('commissionerate.create') }}">Add new Commissionerate</a>
        </div> -->
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
      <div class="row">
        <div class="col-sm-6"></div>
        <div class="col-sm-6"></div>
      </div>

    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
     
      <div class="section1">
      <div class="row color1">
      <div class="col-md-12"><h3 style="margin-top:0px!important">Personal Details</h3></div>
      </div>
      <div class="row">
        <div class="col-md-12" >
               <div class="field-name" >Name:</div>
               	<div class="field-value">{{$single_employee_details->title}}  {{$single_employee_details->first_name}} {{$single_employee_details->middle_name}} {{$single_employee_details->last_name}} </div> 
        </div>                     
      </div>
      @if($single_employee_details->verification_status == "Verified")
      <div class="row">
        <div class="col-md-12">
          <div class="field-name">Employee Code :</div> 
          <div class="field-value">{{$single_employee_details->emp_code}}</div>      
        </div>                     
      </div>
      @endif
      
      <div class="row">
        <div class="col-md-6">
              <div class="field-name">Name of Father/Mother/Spouse/Guardian:</div>
               	<div class="field-value">{{$single_employee_details->guardian_name}} </div> 
        </div>
        <div class="col-md-6">
               <div class="field-name">Relation:</div>
               	<div class="field-value">{{$single_employee_details->guardian_relation}} </div> 
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
              <div class="field-name">Date of Birth:</div>
               	<div class="field-value">{{ date('d/m/Y', strtotime($single_employee_details->dob))}} </div> 
        </div>
        <div class="col-md-6">
               <div class="field-name">Gender:</div>
               	<div class="field-value">{{$single_employee_details->gender}} </div> 
        </div>
      </div>

       <div class="row">
        <div class="col-md-6">
              <div class="field-name">Caste Category:</div>
               	<div class="field-value">{{$single_employee_details->caste_category}} </div> 
        </div>
        <div class="col-md-6">
               <div class="field-name">Whether engaged under PWD:</div>
               	<div class="field-value">{{$single_employee_details->pwd}} </div> 
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
              <div class="field-name">Mobile Number 1:</div>
               	<div class="field-value">{{$single_employee_details->mobile_number_1}} </div> 
        </div>
        <div class="col-md-6">
               <div class="field-name">Mobile Number 2:</div>
               	<div class="field-value"> @if($single_employee_details->mobile_number_2!=null)
                  
                  {{$single_employee_details->mobile_number_2}} 
                  
                  @else Nil
                  
                  @endif
                 
                </div> 
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
              <div class="field-name">Email:</div>
               	<div class="field-value">{{$single_employee_details->email}} </div> 
        </div>
        <div class="col-md-6">
               <div class="field-name">Marital Status:</div>
               	<div class="field-value">{{$single_employee_details->marital_status}} </div> 
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
              <div class="field-name">Identification Mark:</div>
               	<div class="field-value"> @if($single_employee_details->identification_mark!=null)
                  
                  {{$single_employee_details->identification_mark}}
                  
                  @else Nil
                  
                  @endif
                  </div> 
        </div>
        <div class="col-md-6">
               <div class="field-name">Blood Group:</div>
               	<div class="field-value">{{$single_employee_details->blood_group}} </div> 
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
              <div class="field-name">Name of the person in case of Emergency:</div>
               	<div class="field-value">{{$single_employee_details->person_name_emergency}} </div> 
        </div>
        <div class="col-md-6">
               <div class="field-name">Mobile No of the person in case of Emergency:</div>
               	<div class="field-value">{{$single_employee_details->person_emergency_mobile}} </div> 
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
              <div class="field-name">Present Address:</div>
               	<div class="field-value">{{$single_employee_details->present_address_line1}}</div> 
        </div>
        <div class="col-md-6">
               <div class="field-name">Police Station And Pincode:</div>
               	<div class="field-value">{{$single_employee_details->present_address_police_station}}, {{$single_employee_details->present_address_pincode}}</div> 
        </div>
      </div>
        <div class="row">
          <div class="col-md-12">
              <div class="field-name">District:</div>
                <div class="field-value">{{$single_employee_details->Districtpresent->district_name}}</div> 
        </div>
        </div>
      

      <div class="row">
        <div class="col-md-6">
              <div class="field-name">Permanent Address:</div>
               	<div class="field-value">{{$single_employee_details->permanent_address_line1}}</div> 
        </div>
        <div class="col-md-6">
               <div class="field-name">Police Station And Pincode:</div>
               	<div class="field-value">{{$single_employee_details->permanent_address_poilce_station}}, {{$single_employee_details->permanent_address_pincode}}</div> 
        </div>

      </div>
      <div class="row">
         <div class="col-md-12">
              <div class="field-name">District:</div>
                <div class="field-value">{{$single_employee_details->Districtpermanent->district_name}}</div> 
        </div>
      </div>
  </div>
  </div>
 
 <div class="section1">
      <div class="row color1">
      	 <div class="col-md-12"><h3 style="margin-top:0px!important">Qualification Details</h3></div>
      </div>
      <div class="row">
        <div class="col-md-12">
              <div class="field-name">Highest Educational Qualification:</div>
               	<div class="field-value">{{$single_employee_details->highest_education}} </div> 
        </div>
      </div>
       
       <div class="row">
        <div class="col-md-12">
               <div class="field-name">Technical Qualification:</div>

               	<div class="field-value"> @if($single_employee_details->technical_qualification!=null)
               		
               		{{$single_employee_details->technical_qualification}}
               		
               		@else Nil
               		
               		@endif
               		
               	</div> 
        </div>
    	
      </div>
     <!--  <div class="row">
        <div class="col-md-12">
           <div class="field-name">If Other ,Please specify:</div>

                <div class="field-value"> @if($single_employee_details->other_professional_qualification!=null)
                  
                  {{$single_employee_details->technical_qualification}}
                  
                  @else Nil
                  
                  @endif
                  
                </div> 
        </div>
      </div> -->

      <div class="row">
      	 
        <div class="col-md-6">
              <div class="field-name">Professional Qualification:</div>
               	<div class="field-value">@if($single_employee_details->professional_qualification!=null)
               		
               		{{$single_employee_details->professional_qualification}}
               		
               		@else Nil
               		
               		@endif

               	</div> 
        </div>
        <div class="col-md-6">
          <div class="field-name">If Other ,Please specify:</div>

                <div class="field-value"> @if($single_employee_details->other_professional_qualification!=null)
                  
                  {{$single_employee_details->other_professional_qualification}}
                  
                  @else Nil
                  
                  @endif
                  
                </div> 
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
               <div class="field-name">If Professional qualification is MBBS/BDS/BHMS/BUMS/BAMS/Staff Nurse/Pharmacist, then Registration of respective council:</div>
               	<div class="field-value">@if($single_employee_details->registration!=null)
               		
               		{{$single_employee_details->registration}}
               		
               		@else Nil
               		
               		@endif
               		
               	</div> 
        </div>
      </div>
  </div>


  <div class="section1">

      <div class="row color1">
      	 <div class="col-md-12"><h3 style="margin-top:0px!important">Salary Account Details</h3></div>
      </div>
      <div class="row">
        <div class="col-md-6">
              <div class="field-name">PAN:</div>
               	<div class="field-value">{{$single_employee_details->pan}} </div> 
        </div>
        <div class="col-md-6">
               <div class="field-name">Bank Account Number (Salary Account):</div>
               	<div class="field-value">{{$single_employee_details->bank_account_number}} </div> 
        </div>
      </div>

      <div class="row">
      	 
        <div class="col-md-6">
              <div class="field-name">Name Of the Bank:</div>
               	<div class="field-value">{{$single_employee_details->name_of_bank}} </div> 
        </div>
        <div class="col-md-6">
               <div class="field-name">Name Of the Bank Branch:</div>
               	<div class="field-value">{{$single_employee_details->bank_branch}} </div> 
        </div>
      </div>

      <div class="row">
      	 
        <div class="col-md-6">
              <div class="field-name">IFSC Code of Salary Account:</div>
               	<div class="field-value">{{$single_employee_details->bank_ifsc_code}} </div> 
        </div>

      </div>
</div>

<div class="section1">
      <div class="row color1">
      	 <div class="col-md-12"><h3 style="margin-top:0px!important">Experience Details</h3></div>
      </div>
      <div class="row">
        <div class="col-md-12">
              <div class="field-name">If previously engaged under NHM / NUHM at any level (If it is different from present post):</div>
               	<div class="field-value">
               		@if($single_employee_details->exp_engaged_under_nhm==1)
               		
               			Yes
               		
               		@elseif($single_employee_details->exp_engaged_under_nhm==0)
               		
               			No
               		@endif
               		
               	</div> 
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
               <div class="field-name">Designation:</div>
               	<div class="field-value">
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
               <div class="field-name">Duration:</div>
               	<div class="field-value">
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
      		 <div class="field-name">Last Monthly Remuneration Drawn:</div>
              <div class="field-value">
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

      <div class="row">
      	
        <div class="col-md-12">
              <div class="field-name">If engaged in any project/programme/scheme under H & FW or any other Department of Government of West Bengal previously:</div>
               	<div class="field-value">
               		@if($single_employee_details->exp_engaged_under_hfw==1)
               		
               			Yes
               		
               		@elseif($single_employee_details->exp_engaged_under_hfw==0)
               		
               			No
               		@endif
               		
               	</div> 
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
               <div class="field-name">Designation:</div>
               	<div class="field-value">
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
               <div class="field-name">Duration:</div>
               	<div class="field-value">
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
      		 <div class="field-name">Last Monthly Remuneration Drawn:</div>
              <div class="field-value">
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

      <div class="row">
      	<h4 style="text-decoration: underline; padding-left: 2%">Details of Engagement in present post under NHM/NUHM</h4>
      	<div class="col-md-6">
      		 <div class="field-name">Advertisement Number:</div>
              <div class="field-value">
              	@if($single_employee_details->advertisement_number!=null)
               		
               		{{$single_employee_details->advertisement_number}}
               		
               		@else Nil
               		
               		@endif	
              

               </div> 
      	</div>
      	<div class="col-md-6">
      		 <div class="field-name">Appointing Authority:</div>
              <div class="field-value">{{$single_employee_details->appointing_authority}} </div> 
      	</div>
      </div>

      <div class="row">
      
      	<div class="col-md-6">
      		 <div class="field-name">Contractual Employement Under:</div>
              <div class="field-value">{{$single_employee_details->contractual_employement_under}} </div> 
      	</div>
      	<div class="col-md-6">
      		 <div class="field-name">Service Category:</div>
              <div class="field-value">
              		@if($single_employee_details->service_category==1)
               		
               			Service Delivery
               		
               		@elseif($single_employee_details->service_category==2)
               		
               			Programme Management
               		@endif

              	 </div> 
      	</div>
      </div>

      <div class="row">
      
      	<div class="col-md-6">
      		 <div class="field-name">Contractual under NHM - Major Programme Head:</div>
              <div class="field-value">{{$single_employee_details->majorProgammeHeadMaster->name}} </div> 
      	</div>
      	<div class="col-md-6">
      		 <div class="field-name">Programme Head:</div>
              <div class="field-value">{{$single_employee_details->programmeHeadMaster->name}} </div> 
      	</div>
      </div>

      <div class="row">
      
      	<div class="col-md-6">
      		 <div class="field-name">Designation List:</div>
              <div class="field-value">{{$single_employee_details->designationMaster->name}} </div> 
      	</div>
      	<div class="col-md-6">
      		 <div class="field-name">Date of joining in present designation:</div>
              <div class="field-value">{{ date('d/m/Y', strtotime($single_employee_details->doj_present_designation)) }} </div> 
      	</div>
      </div>

      <div class="row">
      
      <!-- 	<div class="col-md-6">
      		 <div class="field-name">Consolidated remuneration at the time of joining:</div>
              <div class="field-value">{{$single_employee_details->consolidated_remuneration_doj}} </div> 
      	</div> -->
        <div class="col-md-6">
           <div class="field-name">Consolidated Monthly remuneration at the time of joining:</div>
              <div class="field-value">{{$single_employee_details->consolidated_remuneration_doj}} </div> 
        </div>
      </div>
      <div class="row">
      	<div class="col-md-12">
      		 <div class="field-name">Date of joining in present place of posting:</div>
              <div class="field-value">{{date('d/m/Y', strtotime($single_employee_details->doj_present_posting))}} </div> 
      	</div>
      </div>

      <div class="row">
      
      	<div class="col-md-12">
      		 <div class="field-name">Consolidated Monthly Remuneration as on 01.04.2019:</div>
              <div class="field-value">{{$single_employee_details->monthly_remuneration}} </div> 
      	</div>
      	
      </div>
       <div class="row">
      
        <div class="col-md-6">
           <div class="field-name">Posting Level:</div>
              <div class="field-value">{{$single_employee_details->posting_level}} </div> 
        </div>
        <div class="col-md-6">
           <div class="field-name">Posting Place:</div>
              <div class="field-value">{{$single_employee_details->posting_place}} </div> 
        </div>
        
      </div>
 
 <!--      <div class="row">
      	<h4 style="text-decoration: underline;padding-left: 2%;">Leave Status</h4>
      	<div class="col-md-6">
      		 <div class="field-name">Casual leave availed:</div>
              <div class="field-value">{{$single_employee_details->casual_leave_availed}} </div> 
      	</div>
      	<div class="col-md-6">
      		 <div class="field-name">Earned leave availed:</div>
              <div class="field-value">{{$single_employee_details->earned_leave_availed}} </div> 
      	</div>
      </div> -->
  </div>
  @if($single_employee_details->verification_status=="Not Verified")
  			<form method="post" action="{{url('verifynhmemployeedata')}}">
  			 <input type="hidden" name="id" id="id" value="{{$single_employee_details->id}}">
  			<!--  <input type="hidden" name="verification_status" id="verification_status" value="Verified"> -->
             <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
               
              
              <div class="section1">
              <div class="row">
                <div class="col-md-12">
                <input style="width:100%; padding: 2%; margin:1%;" type="text" name="comments" id="comments" class="form-control" placeholder="Comments" /> 
                </div>
              </div>
               <div class="row">                
                <div class="col-md-6" style="text-align: center;"><input type="submit" name="submit" value="Verify" id="Verifysubmit" class="btn btn-success btn-lg"></div>
                <div class="col-md-6" style="text-align: center;"><input type="submit" name="submit" value="Reject" id="Rejectsubmit" class="btn btn-danger btn-lg"></div>
              </div>
            </div>
  			</form>
        @endif
        @if($single_employee_details->verification_status=="Verified")
         <div class="text-center example-screen"><button style="width:25%;"class="btn btn-success btn-lg" onclick="printfunction()">Print</button></div>
        @endif 
      </div>
    </div>
  </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>

@endsection
<script>
function printfunction() {
  // var content=document.getElementById('divToPrint');
  // window.document.write('<html><head><style>.row{ margin-right: 0px!important; margin-left: 0px!important; margin-top: 1%!important;}.section1{border:1.5pxsolid#9187878c;margin:2%;padding:2%;}.color1{margin:0%!important;background-color: #5f9ea061;}.modal_field_name{ float:left;font-weight: 700;margin-right:1%;padding-top:1%;margin-top:1%;}.modal_field_value{margin-right:1%;padding-top:1%;margin-top:1%;}</style></head><body>'+content.innerHTML+'</body></html>');
  window.print();
}
</script>