@extends('users-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Update user</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('user-management.update', ['id' => $user->id]) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">

                        <div class="form-group">
                            <label class="col-md-4 control-label">Employee Name</label>
                            <div class="col-md-6">
                                
                                <select class="form-control select2" name="employee_id">
                                    <option value="">Please select employee</option>
                                    @foreach($employees as $employee)

                                     <option value="{{$employee->id}}" {{ ( $employee->id === $user->emp_id ) ? 'selected' : '' }}>
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
                            <label for="username" class="col-md-4 control-label">User Name</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control" name="username" value="{{ $user->username }}" required autofocus>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Email') ? ' has-error' : '' }}">
                            <label for="Email" class="col-md-4 control-label">Email</label>

                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control" name="email" value="{{ $user->email }}" required>

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
                            <label for="password" class="col-md-4 control-label">New Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password">

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
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Desination</label>

                            <div class="col-md-6">
                                <select id="designation_id_old" type="password" class="form-control" name="designation_id_old" required>
                                    <option value="">Choose Designation</option>
                                     @foreach($designations as $designation)
                                      <option value="{{$designation->name}}"" {{ ( $designation->id === $user->designation_id_old ) ? 'selected' : '' }}>{{$designation->name}}</option>
                                     @endforeach
                                </select>

                                 @if ($errors->has('designation_id_old'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('designation_id_old') }}</strong>
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
                                      <option value="{{$scheme->id}}" {{ ( $scheme->id === $user->user_scheme_id ) ? 'selected' : '' }}>{{$scheme->scheme_name}}</option>
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
