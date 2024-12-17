@extends('application.policeverification.base')

@section('action-content')
<div class="container">
    <div class="row">
     <div class="col-md-12">          
          <!-- /.box -->
        <form action="{{ route('policeverification.update', ['id' => $application->application_id]) }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">  
              <div class="box">
                <div class="box-header">
                    <div class="col-md-12 backgroundColorEven"> 
                        <div class="col-md-4 applicant_Details "><strong>Application ID :</strong> {{ $application->application_id }}</div>
                        <div class="col-md-4 applicant_Details "><strong>Applicant's Name :</strong> {{ $application->first_name}} {{ $application->middle_name}} {{ $application->last_name}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Father's Name :</strong> {{ $application->father_name}}</div>
                    </div>
                    <div class="col-md-12 backgroundColor"> 
                        <div class="col-md-4 applicant_Details "><strong>Present Address1:</strong> {{ $application->present_address_line1}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Present Address2:</strong> {{ $application->present_address_line2}}</div>

                        <div class="col-md-4 applicant_Details "><strong>Aresent Address Landmark:</strong>  {{ $application->present_address_landmark}}</div>
                    </div>
                    <div class="col-md-12 backgroundColorEven"> 
                        <div class="col-md-4 applicant_Details "><strong>Present Pincode: </strong> {{ $application->present_pincode}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Present City:</strong> {{ $application->present_city}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Present State:</strong> {{ $application->present_state}}</div>
                    </div>
                    <div class="col-md-12 backgroundColor">
                        <div class="col-md-4 applicant_Details "><strong>Gendar:</strong> {{ $application->gender}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Dob :</strong> {{ $application->dob}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Nationality :</strong> {{ $application->nationality}}</div>
                    </div>
                    <div class="col-md-12 backgroundColorEven">
                        <div class="col-md-4 applicant_Details "><strong>Nationality :</strong> {{ $application->nationality}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Spouse Name :</strong> {{ $application->spouse_name}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Email :</strong> {{ $application->email}}</div>
                    </div>
                    <div class="col-md-12 backgroundColor">
                        <div class="col-md-4 applicant_Details " ><strong>Mobile No :</strong> {{ $application->mobile_no}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Present Stay From Date :</strong> {{ $application->present_stay_frm_date}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Present Stay From Date :</strong> {{ $application->present_stay_frm_date}}</div>
                    </div>
                    <div class="col-md-12 backgroundColorEven">
                        <div class="col-md-4 applicant_Details "><strong>Present Stay To Date :</strong> {{ $application->present_stay_to_date}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Permanent Address Line1 :</strong> {{ $application->permanent_address_line1}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Permanent Address Line2 :</strong> {{ $application->permanent_address_line2}}</div>
                    </div>
                    <div class="col-md-12 backgroundColor">
                        <div class="col-md-4 applicant_Details "><strong>Permanent Address Landmark :</strong> {{ $application->permanent_address_landmark}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Purpose :</strong> {{ $application->pcc_purpose}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Verification For :</strong> {{ $application->pcc_virification_for}}</div>
                   </div>
                    <div class="col-md-12 backgroundColorEven">
                        <div class="col-md-4 applicant_Details "><strong>Fee Paid :</strong> {{ $application->is_fee_paid}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Fee Amount :</strong> {{ $application->fee_amount}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Fee Paid Date Time :</strong> {{ $application->fee_paid_date_time}}</div>
                    </div>
                    <div class="col-md-12 backgroundColor">
                        <div class="col-md-4 applicant_Details "><strong>Transaction  Ref No :</strong> {{ $application->txn_ref_no}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Current Status :</strong> {{ $application->current_status}}</div>
                        <div class="col-md-4 applicant_Details "><strong>GRN :</strong> {{ $application->grn}}</div>
                    </div>
                    <div class="col-md-12 backgroundColorEven">
                        <div class="col-md-4 applicant_Details "><strong>GRN_date :</strong> {{ $application->grn_date}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Payement Mode :</strong> {{ $application->payement_mode}}</div>
                        <div class="col-md-4 applicant_Details "><strong>Bank Code :</strong> {{ $application->bank_code}}</div>
                    </div>
                    <div class="col-md-12 backgroundColor">
                        <div class="col-md-4 applicant_Details "><strong>BRN :</strong> {{ $application->brn}}</div>

                        <div class="col-md-4 applicant_Details "><strong>BRN Date :</strong> {{ $application->brn_date}}</div>
                        <div class="col-md-4 applicant_Details ">&nbsp;</div>
                    </div>
                    <?php 
                    $stored_file_name = json_decode($application_images->stored_file_name);
                    $extension_type = json_decode($application_images->extension_type);

                    if($stored_file_name !='' || $extension_type !=''){
                      $image_type = array_combine($stored_file_name, $extension_type);


                    }

                    $document_type_data = json_decode($application_images->document_type);
                    $document_number = json_decode($application_images->document_no);

                    if($document_type_data !='' || $document_number !=''){
                        $document_data = array_combine($document_type_data, $document_number);
                    }

                     ?>
                    @foreach($document_data as $k=>$v)
                    <div class="col-md-12 backgroundColorEven">
                        <div class="col-md-4 applicant_Details " ><strong>{{$k}} :</strong> {{ $v}}</div>
                        <div class="col-md-4 applicant_Details " ><br></div>
                        <div class="col-md-4 applicant_Details " ><br></div>
                    </div>
                    @endforeach

                    <div class="clear"></div><br/>
                    <br/>

                 <div class="col-md-12" >   
                      <h3>PURPOSE SPECIFIC DETAILS</h3>
                      <H4>RECORD VERIFICATION</H4>
                      <p><strong>Please select the purpose for PCC<span class="requiredStar" style="color:#ff0000">*</span></strong></p>
                      <div class="form-group{{ $errors->has('in_pcc_virification_for') ? 'has-error' : '' }} col-md-6">
                        <input type="checkbox" name="in_pcc_virification_for" value="crime_record" > Crime Record<br>
                        <input type="checkbox" name="in_pcc_virification_for" value="antecedents" > Antecedents<br>
                        @if ($errors->has('in_pcc_virification_for'))
                              <span class="requiredfield" style="color: #ff0000">
                                  <strong>{{ $errors->first('in_pcc_virification_for') }}</strong>
                              </span>
                        @endif
                      </div>
                </div>
    
    <div class="row">
             
        @foreach($image_type as $key=>$type)
          @if($type=='jpg')
            <div class="col-md-3 imgPosition gallery clearfix">
                <a rel="prettyPhoto[gallery1]"  href="{{URL::to('/')}}/images/{{$application->application_id}}/{{$key}}" >

                <img  src="{{URL::to('/')}}/images/{{$application->application_id}}/{{$key}}" class="imageSize " width="100" height="100"></img>
                </a>
               
            </div>

            @elseif($type=='jpeg')
            <div class="col-md-4 imgPosition gallery">
                <a rel="prettyPhoto[gallery1]"  href="{{URL::to('/')}}/images/{{$application->application_id}}/{{$key}}">
                    <img src="{{URL::to('/')}}/images/{{$application->application_id}}/{{$key}}" class="imageSize" width="100" height="100"></img>
                </a>
            </div>

            @elseif($type=='png')
            <div class="col-md-4 imgPosition gallery">
                <a rel="prettyPhoto[gallery1]"  href="{{URL::to('/')}}/images/{{$application->application_id}}/{{$key}}">
                    <img  src="{{URL::to('/')}}/images/{{$application->application_id}}/{{$key}}" class="imageSize" width="100" height="100"></img>
                </a>
            </div>
            
            
            @elseif($type=='pdf')
            <div class="col-md-4 imgPosition pdfContener">
                <a id="link" rel="gallery" class="fancybox fancybox.iframe" href="{{URL::to('/')}}/images/{{$application->application_id}}/{{$key}}" target="_blank" width="">PDF Document</a>
            </div>
            @else 
                <div class="col-md-4"><span>There Is no Image for show </span></div>
           @endif
        @endforeach
    </div>

   
<!--applicant_Details -->

        <div class="col-md-4 " style="margin-top:30px;" >
            <label for="reject or accept">Status</label>
            <select  name="acce_rej" class='form-control' required>
                <option value="">--- Select Accept Or Reject ---</option>
                <option value="Y">Accept</option>
                <option value="N">Reject</option>
                <option value="P">Enquiry Pending </option>
            </select>

            @if ($errors->has('name'))
                <span class="help-block">
                    <strong>{{ $errors->first('acce_rej') }}</strong>
                </span>
            @endif
        </div>  

        <div class="form-group{{ $errors->has('certificate_accept_for') ? ' has-error' : '' }} col-md-4 " style="margin-top:30px;" >
            <label for="Certificate Process for">Certificate Process for</label>
            <select  name="certificate_accept_for" class='form-control' >
                <option value="">--- Select Certificate accept Address ---</option>
                <option value="R">Accept with Present Address</option>
                <option value="M">Accept with Parmanent Address</option>
            </select>

            @if ($errors->has('name'))
                <span class="help-block">
                    <strong>{{ $errors->first('certificate_accept_for') }}</strong>
                </span>
            @endif
        </div>  
        
        <div class="form-group{{ $errors->has('txt_doc_insp_level') ? ' has-error' : '' }}  col-sm-3" style="margin-top:30px;" >
            <label for="reject or accept">Document Upload</label>
            <input class="form-control filestyle" type="file" name="txt_doc_insp_level">
             @if ($errors->has('txt_doc_insp_level'))
                <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                    <strong>{{ $errors->first('txt_doc_insp_level') }}</strong>
                </span>
              @endif
        </div>
       
   
    <div style="clear: both;"></div>
               
        <div class="box-body pad">
            <textarea  class="form-group{{ $errors->has('policevarificationComment') ? ' has-error' : '' }} textarea" id="policeverificationnote" name="policevarificationComment" placeholder="Place some text here" required
                      style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" >{{ old('policevarificationComment') }}
                  
            </textarea>

            @if ($errors->has('policevarificationComment'))
                <span class="help-block" style="color: red; font-size: 13px; font-style: italic;">
                    <strong>{{ $errors->first('policevarificationComment') }}</strong>
                </span>
              @endif
        </div>

        <div class="row">
           <div class="form-group col-sm-4 col-sm-offset-5">
              <button type="submit" name="submit" id="map" value="Map" class="btn btn-primary col-sm-5 col-sm-offset-2 col-xs-5 col-xs-offset-2 btn-margin " >Submit</button>
           </div>
        </div>
           </form>
          </div>
        </div>
    </div>
</div>
 </div> 
@endsection
