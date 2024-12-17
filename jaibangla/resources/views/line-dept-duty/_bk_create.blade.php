@extends('application.base')
@section('action-content')
<section class="content">
    <div class="box">
      <div class="box-header">
        <div class="row">
          <div class="col-sm-12">
            <h3 class="box-title">Configurable Duty Setting Setting</h3>
          </div>
          <div>


              <!--@if(session('message'))
                  <h4 class="col-sm-6 col-sm-offset-3 alert alert-success" id="id">
                      {{session('message')}}
                  </h4>
             @endif-->
          </div>
          
        </div>
      </div>

       <div class="box-body">
        <form action="{{ route('emp-user-duty.store') }}" method="post">
           {{ csrf_field() }}
          <div class="row">

            <div class="form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
                  <label for="firstname" class="col-md-4 control-label">First Name</label>

                  <div class="col-md-6">
                      <input id="firstname" type="text" class="form-control" name="firstname" value="{{ old('firstname') }}" required autofocus>

                      @if ($errors->has('firstname'))
                          <span class="help-block">
                              <strong>{{ $errors->first('firstname') }}</strong>
                          </span>
                      @endif
                  </div>
              </div>
              <div class="form-group{{ $errors->has('middlename') ? ' has-error' : '' }}">
                  <label for="middlename" class="col-md-4 control-label">Middle Name</label>

                  <div class="col-md-6">
                      <input id="middlename" type="text" class="form-control" name="middlename" value="{{ old('middlename') }}" >

                      @if ($errors->has('middlename'))
                          <span class="help-block">
                              <strong>{{ $errors->first('middlename') }}</strong>
                          </span>
                      @endif
                  </div>
              </div>
              <div class="form-group{{ $errors->has('lastname') ? ' has-error' : '' }}">
                  <label for="lastname" class="col-md-4 control-label">Last Name</label>

                  <div class="col-md-6">
                      <input id="lastname" type="text" class="form-control" name="lastname" value="{{ old('lastname') }}" required>

                      @if ($errors->has('lastname'))
                          <span class="help-block">
                              <strong>{{ $errors->first('lastname') }}</strong>
                          </span>
                      @endif
                  </div>
              </div>
              

              <div class="form-group{{ $errors->has('scheme') ? ' has-error' : '' }}">
                <label for="scheme" class="col-md-4 control-label">Scheme</label>
                <select  id="scheme" class="form-control select2" name="schemelist[]" multiple="multiple"required>
                  <option value="">--Select Scheme--</option>
                  @foreach ($schemes as $scheme)
                  <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                  @endforeach
                </select>
                  @if ($errors->has('scheme'))
                      <span class="help-block">
                          <strong>{{ $errors->first('scheme') }}</strong>
                      </span>
                  @endif
              </div>

              <div class="form-group{{ $errors->has('designation_id_old') ? ' has-error' : '' }}">
                  <label class="col-md-4 control-label">Role</label>
                  <div class="col-md-6">
                      <select class="form-control" name="designation_id_old" required>
                          @foreach ($designations as $designation)
                              <option value="{{$designation->id}}">{{$designation->name}}</option>
                          @endforeach
                      </select>
                       @if ($errors->has('designation_id_old'))
                          <span class="help-block">
                              <strong>{{ $errors->first('designation_id_old') }}</strong>
                          </span>
                      @endif
                  </div>
              </div>
  
              <input type="hidden" name="district_code" id="district_code" value="{{ $dist_code }}" class="js-district"/>
  
              <div class="form-group{{ $errors->has('urban_code') ? ' has-error' : '' }} " id="divUrbanCode">
                <label for="urban_code" class="col-md-4 control-label">Rural/Urban</label>              
                <select name="urban_code" id="urban_code" class="form-control select2 js-urban" >
                  <option value="">--Select  --</option>
                   @foreach ($levels as $key=>$value)
                  <option value="{{$key}}" > {{$value}}</option>
                  @endforeach            
                   
                </select>
                  @if ($errors->has('urban_code'))
                      <span class="help-block">
                          <strong>{{ $errors->first('urban_code') }}</strong>
                      </span>
                  @endif
              </div>
              <div class="form-group{{ $errors->has('body_code') ? ' has-error' : '' }} col-sm-12" id="divBodyCode">
                <label for="body_code" class="col-md-4 control-label">Municipality/Taluka</label>
                
                <select name="body_code" id="body_code" class="form-control select2 js-localbody">
                  <option value="">--Select Option --</option>
                  
                   
                </select>
                  @if ($errors->has('body_code'))
                      <span class="help-block">
                          <strong>{{ $errors->first('body_code') }}</strong>
                      </span>
                  @endif
              </div>
          </div>

          <div class="row">
           <div class="form-group col-sm-4 col-sm-offset-5">
              <button type="submit" name="submit" id="map" value="Map" class="btn btn-warning col-sm-5 col-sm-offset-2 col-xs-5 col-xs-offset-2 btn-margin " >Create User</button>
           </div>
          </div>
        </form>

       
      </div>

      



      
      </div>
  </div>
</section>





@endsection