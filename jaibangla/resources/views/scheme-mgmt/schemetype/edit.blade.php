@extends('scheme-mgmt.schemetype.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Update Scheme Type</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('SchemeType.update', ['id' => $schemes->id]) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group{{ $errors->has('scheme_type') ? ' has-error' : '' }}">
                            <label for="scheme_type" class="col-md-4 control-label">Scheme Type</label>

                            <div class="col-md-6">
                                <input id="scheme_type" type="text" class="form-control" name="scheme_type" value="{{ $schemes->scheme_type }}" required autofocus>

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
