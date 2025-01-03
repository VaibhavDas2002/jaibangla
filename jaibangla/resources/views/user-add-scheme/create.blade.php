@extends('emp-user-duty.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add new employee</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('emp-user-duty.store') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}  
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

                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username" class="col-md-4 control-label">UserName</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">Email</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                            <label for="mobile" class="col-md-4 control-label">Mobile No</label>

                            <div class="col-md-6">
                                <input id="mobile" type="number" class="form-control" name="mobile" value="{{ old('mobile') }}" required min="1000000000" max="9999999999">

                                @if ($errors->has('mobile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>                   
                        
                      
                        
                        
                        <div class="form-group{{ $errors->has('designation_id') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Role</label>
                            <div class="col-md-6">
                                <select class="form-control select2" name="designation_id" required>
                                    <option value="">--Select Role--</option>
                                    @foreach ($designations as $designation)
                                        <option value="{{$designation->id}}">{{$designation->name}}</option>
                                    @endforeach
                                </select>
                                 @if ($errors->has('designation_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('designation_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div> 

                        <div class="form-group{{ $errors->has('designation_id') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Scheme</label>
                            <div class="col-md-6">
                                <select  id="scheme" class="form-control select2" name="schemelist[]" 
                                multiple="multiple" required>
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
                        </div>
                        <input type="hidden" name="dist_code" value="{{ $dist_code }}" class="js-district">

                        <div class="form-group{{ $errors->has('designation_id') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Urban/Rural</label>
                            <div class="col-md-6">
                                <select name="urban_code" id="urban_code" class="form-control select2 js-block-subdiv" >
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
                        </div> 

                        <div class="form-group{{ $errors->has('body_code') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Block/Sub Division</label>
                            <div class="col-md-6">
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

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Create
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
