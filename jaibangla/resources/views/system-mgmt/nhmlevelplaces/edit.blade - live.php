@extends('system-mgmt.nhmlevelplaces.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Update Health Facility</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('nhmPlace.update', ['id' => $nhm_level_place->id]) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group{{ $errors->has('health_facility_name') ? ' has-error' : '' }}">
                            <label for="health_facility_name" class="col-md-4 control-label">Health Facility Name</label>

                            <div class="col-md-6">
                                <input id="health_facility_name" type="text" class="form-control" name="health_facility_name" value="{{ $nhm_level_place->facility_name }}" required autofocus>

                                @if ($errors->has('health_facility_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('health_facility_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('health_facility_code') ? ' has-error' : '' }}">
                            <label for="health_facility_code" class="col-md-4 control-label">Health Facility Code</label>

                            <div class="col-md-6">
                                <input id="health_facility_code" type="text" class="form-control" name="health_facility_code" value="{{ $nhm_level_place->facilty_code }}" required autofocus>

                                @if ($errors->has('health_facility_code'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('health_facility_code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('health_facility_type') ? ' has-error' : '' }}">
                            <label for="health_facility_type" class="col-md-4 control-label">Health Facility Type</label>

                            <div class="col-md-6">

                                 <select class="form-control select2" id="health_facility_type" name="health_facility_type" required autofocus>
                                        <option value="{{$nhm_level_place->facility_type}}">{{$nhm_level_place->facility_type}}</option>
                                         <option value="">-----Select Option------</option>
                                         <option value="DH">DH</option>
                                         <option value="PHC">PHC</option>
                                         <option value="SDH">SDH</option>
                                         <option value="UPHC">UPHC</option>
                                         <option value="CH">CH</option>
                                         <option value="SC">SC</option>
                                         <option value="Others">Others</option>
                                         <option value="MCH">MCH</option>
                                         <option value="SSH">SSH</option>
                                         <option value="SGH">SGH</option>
                                 
                                </select>


                                <!-- <input id="health_facility_type" type="text" class="form-control" name="health_facility_type" value="{{ $nhm_level_place->facility_type }}" required autofocus> -->

                                @if ($errors->has('health_facility_type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('health_facility_type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                         <div class="form-group{{ $errors->has('district') ? ' has-error' : '' }}">
                            <label for="district" class="col-md-4 control-label">District</label>

                            <div class="col-md-6">
                                <select class="form-control select2 js-district_healthfacility_edit" name="district" required autofocus>
                                     <option value="{{$nhm_level_place->District->district_code}}">{{$nhm_level_place->District->district_name}}</option>
                                      @foreach ($nhm_districts as $nhm_district)
                                        <option value="{{$nhm_district->district_code}}">{{$nhm_district->district_name}}</option>
                                      @endforeach      
                                </select>
                               <!--  <input id="name" type="text" class="form-control" name="service_category" value="{{ old('service_category') }}" required autofocus> -->

                                @if ($errors->has('district'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('district') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                            <label for="location" class="col-md-4 control-label">Location</label>

                            <div class="col-md-6">
                                <select class="form-control select2 js-location_healthfacility_edit" name="location" required autofocus>
                                     @if($nhm_level_place->facility_type=="UPHC")
                                        @if($nhm_level_place->taluka_code==null)
                                        <option value="null">null</option>
                                        @else 
                                        <option value="{{$nhm_level_place->urban_body->urban_body_code}}">{{$nhm_level_place->urban_body->urban_body_name}}</option>
                                        @endif 


                                     @else
                                        @if($nhm_level_place->taluka_code==null)
                                        <option value="null">null</option>
                                        @else   
                                        <option value="{{$nhm_level_place->taluka->taluka_code}}">{{$nhm_level_place->taluka->taluka_name}}</option> 
                                        @endif    
                                     @endif
                  
                                     
                                      
                                </select>
                               <!--  <input id="name" type="text" class="form-control" name="service_category" value="{{ old('service_category') }}" required autofocus> -->

                                @if ($errors->has('location'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('location') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
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
