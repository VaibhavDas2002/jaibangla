@extends('users-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add new user</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('user-management.store') }}">
                        {{ csrf_field() }}
                       

                        <div class="form-group">
                            <label class="col-md-4 control-label">Employee Name</label>
                            <div class="col-md-6">
                                <select class="form-control select2" name="employee_id" required>
                                    <option value="">Please select employee</option>
                                    @foreach($employees as $employee)
                                     <option value="{{$employee->id }} ">
                                        {{$employee->firstname }} {{$employee->middlename }}&nbsp; {{$employee->lastname }}
                                    </option>
                                    @endforeach
                                </select>

                                @if ($errors->has('employee_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('employee_id') }}</strong>
                                    </span>
                                 @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username" class="col-md-4 control-label">User Name</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required autofocus>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                         <div class="form-group{{ $errors->has('mobile_no') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">Mobile Number</label>

                            <div class="col-md-6">
                                <input id="mobile_no" type="mobile_no" class="form-control" name="mobile_no" value="{{ old('mobile_no') }}" required>

                                @if ($errors->has('mobile_no'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mobile_no') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Designation</label>

                            <div class="col-md-6">
                                <select id="designation_id" type="password" class="form-control" name="designation_id" required>
                                    <option value="">Choose Designation</option>
                                     @foreach($designations as $designation)
                                      <option value="{{$designation->name}}">{{$designation->name}}</option>
                                     @endforeach
                                </select>

                                 @if ($errors->has('designation_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('designation_id') }}</strong>
                                    </span>
                                 @endif
                                
                            </div>

                        </div>

                         <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Scheme Name</label>
                            <div class="col-md-6">
                                <select id="scheme_name" type="password" class="form-control" name="scheme_name" required>
                                    <option value="">Choose Scheme</option>

                                     @foreach($schemes as $scheme)
                                      <option value="{{$scheme->id}}">{{$scheme->scheme_name}}</option>
                                     @endforeach
                                </select>
                                 @if ($errors->has('scheme_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scheme_name') }}</strong>
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
