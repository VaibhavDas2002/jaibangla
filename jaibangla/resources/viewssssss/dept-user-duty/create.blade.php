@extends('emp-user-duty.base')

@section('action-content')
 <style>
.required-field::after {
    content: "*";
    color: red;
}
 .imageSize{
  font-size: 9px;
  color: #333;
 }

  </style>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add new Approver</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('dept-user-duty.store') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}  
                        <div class="form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
                            <label for="firstname" class="col-md-4 control-label required-field">First Name</label>

                            <div class="col-md-6">
                                <input id="firstname" type="text" class="form-control" 
                                name="firstname" value="{{ old('firstname') }}" required autofocus>

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
                            <label for="lastname" class="col-md-4 control-label required-field">Last Name</label>

                            <div class="col-md-6">
                                <input id="lastname" type="text" class="form-control" 
                                name="lastname" value="{{ old('lastname') }}" required>

                                @if ($errors->has('lastname'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lastname') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username" class="col-md-4 control-label required-field">UserName</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control" 
                                name="username" value="{{ old('username') }}" required>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label required-field">Email</label>

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
                            <label for="mobile" class="col-md-4 control-label required-field">Mobile No</label>

                            <div class="col-md-6">
                                <input id="mobile" type="text" class="form-control NumOnly" 
                                name="mobile" value="{{ old('mobile') }}" 
                                required maxlength="10">

                                @if ($errors->has('mobile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>                   
                        
                      
                        
                        
                        <div class="form-group{{ $errors->has('designation_id_old') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label required-field">Role</label>
                            <div class="col-md-6">
                                <select class="form-control select2" name="designation_id_old" required>
                                    <option value="">--Select Role--</option>
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

                        <div class="form-group{{ $errors->has('designation_id_old') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label required-field">Scheme</label>
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

                       

                         <div class="form-group{{ $errors->has('dist_code') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label required-field">District</label>
                            <div class="col-md-6">
                                <select name="dist_code" id="dist_code" class="form-control select2" >
                                    <option value="">--Select  --</option>
                                     @foreach ($districts as $district)
                                    <option value="{{$district->district_code}}" > {{$district->district_name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('dist_code'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('district') }}</strong>
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
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
$(document).ready(function(){
     $(".NumOnly").keyup(function(event) {
              
        $(this).val($(this).val().replace(/[^\d].+/, ""));
            if ((event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        }); 
});
</script>