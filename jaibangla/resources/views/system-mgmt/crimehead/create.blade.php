@extends('system-mgmt.crimehead.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add new Crime Head</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('crimehead.store') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('crime_head') ? ' has-error' : '' }}">
                            <label for="head_name" class="col-md-4 control-label">Crime Head</label>

                            <div class="col-md-6">
                                <input id="crime_head" type="text" class="form-control" name="crime_head" value="{{ old('crime_head') }}" required autofocus>

                                @if ($errors->has('crime_head'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('crime_head') }}</strong>
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
