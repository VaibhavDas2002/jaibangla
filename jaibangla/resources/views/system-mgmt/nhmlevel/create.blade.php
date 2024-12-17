@extends('system-mgmt.nhmlevel.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add new Level</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('nhmLevel.store') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Level Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="level_name" value="{{ old('level_name') }}" required autofocus>

                                @if ($errors->has('level_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('level_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Level</label>
                            <div class="col-md-6">
                                <select class="form-control" name="level">
                                    <option value="">--Select--</option>
                                    <option value="State">State</option>
                                    <option value="District">District</option>
                                    <option value="Block">Block</option> 
                                    <option value="ULB">ULB</option>       
                                </select>
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
