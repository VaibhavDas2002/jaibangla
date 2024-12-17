@extends('application.base')
@section('action-content')
    <!-- Main content -->
    <div class="container">
    <div class="row">
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">Upload Digital Signed Pdf </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{url('UploadpdfSignCertificate/'.$applications->application_id)}}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <!--input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"-->
                        
                            <div class="col-md-6 upload_digital_certificate  backgroundColor">
                                <label class="col-md-5 ">Application Id</label>
                                <div class="col-md-6 ">{{$applications->application_id}}</div>
                            </div>
                            <div class="col-md-6 upload_digital_certificate  backgroundColor ">
                                <label class="col-md-5 ">Applicant's Name</label>
                                <div class="col-md-6 ">{{$applications->first_name}} {{$applications->middle_name}} {{$applications->last_name}}</div>
                            </div>
                             
                            <div class="col-md-6 upload_digital_certificate  backgroundColorEven">
                                <label class="col-md-5 ">Applicant's  Father's  Name</label>
                                <div class="col-md-6 ">{{$applications->father_name}}</div>
                            </div>
                            <div class="col-md-6 upload_digital_certificate  backgroundColorEven">
                                <label class="col-md-5 ">Address</label>
                                <div class="col-md-6 ">
                                  {{$applications->present_address_line1}}</div>
                            </div>
                            <div class="col-md-6 upload_digital_certificate  backgroundColor">
                                <label class="col-md-5 ">Pincode</label>
                                <div class="col-md-6 ">{{$applications->present_pincode}}</div>
                            </div>

                            <div class="col-md-6 upload_digital_certificate  backgroundColor">
                                <label class="col-md-5 ">Gender</label>
                                <div class="col-md-6 ">{{$applications->gender}}</div>
                            </div>

                            <div class="col-md-6 upload_digital_certificate  backgroundColorEven">
                                <label class="col-md-5 ">Date of Birth</label>
                                <div class="col-md-6 ">{{$applications->dob}}</div>
                            </div>

                            <div class="col-md-6 upload_digital_certificate  backgroundColorEven">
                                <label class="col-md-5 ">Nationality</label>
                                <div class="col-md-6 ">{{$applications->nationality}}</div>
                            </div>
                            <div class="col-md-6 upload_digital_certificate  backgroundColor">
                                <label class="col-md-5 ">Spouse Name</label>
                                <div class="col-md-6 ">{{$applications->spouse_name}}</div>
                            </div>

                            <div class="col-md-6 upload_digital_certificate  backgroundColor">
                                <label class="col-md-5">Email</label>
                                <div class="col-md-6">{{$applications->email}}</div>
                            </div>

                           

                            <div class="col-md-6 upload_digital_certificate  backgroundColorEven">
                                <label class="col-md-5 ">Mobile No</label>
                                <div class="col-md-6 ">{{$applications->mobile_no}}</div>
                            </div>
                            <div class="col-md-6 upload_digital_certificate  backgroundColorEven">
                              <label class="col-md-3 "><br></label>
                                <div class="col-md-6 "><br></div>
                            </div>
                            

                            <div class="col-md-10   backgroundColor">
                                <label class="col-md-3">Digital Signed Pdf Upload <span style="color: red">*</span></label>
                                <div class="col-md-6 "><input type="file" class="filestyle" id="signedFile" name="signedFile" required></div>
                            </div>
                            <div class="col-md-2 upload_digital_certificate   backgroundColor">
                               <label class="col-md-3 "><br></label>
                                <div class="col-md-6 "><br></div>
                            </div>
                            
                            

                            <div class="col-md-6  " style="margin-top: 10px;">
                                <div class="col-md-6 col-md-offset-5">
                                    <button type="submit" class="btn btn-primary">
                                        Update
                                    </button>
                                </div>
                            </div>
                       
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection