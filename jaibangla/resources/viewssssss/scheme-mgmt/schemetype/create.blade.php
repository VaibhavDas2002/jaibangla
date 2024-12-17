@extends('scheme-mgmt.schemetype.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add New Scheme Type</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('SchemeType.store') }}">
                        {{ csrf_field() }} 

                        <div class="form-group{{ $errors->has('scheme_type') ? ' has-error' : '' }}">
                            <label for="scheme_type" class="col-md-4 control-label">Scheme Type</label>

                            <div class="col-md-6">
                                <input id="scheme_type" type="text" class="form-control" name="scheme_type" value="{{ old('scheme_type') }}" required autofocus>

                                @if ($errors->has('scheme_type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scheme_type') }}</strong>
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
