@extends('scheme-mgmt.scheme.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Update Scheme Type</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('scheme.update', ['id' => $schemes->id]) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="form-group">
                             <label class="col-md-4 control-label">Scheme Type</label>
                          <div class="col-md-6">

                                <select id="scheme_type" class="form-control" name="scheme_type" value="{{ old('scheme_type') }}" required>
                                    @foreach ($schemetype as $schemetp)
                                      <option value="{{$schemetp->id}}" {{$schemetp->id == $schemes->scheme_type_id ? 'selected' : ''}}>{{$schemetp->scheme_type}}</option>
                                     @endforeach

                                </select>

                                @if ($errors->has('scheme_type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scheme_type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                         <div class="form-group{{ $errors->has('scheme_name') ? ' has-error' : '' }}">
                            <label for="scheme_type" class="col-md-4 control-label">Scheme Name</label>

                            <div class="col-md-6">
                                <input id="scheme_name" type="text" class="form-control" name="scheme_name" value="{{ $schemes->scheme_name }}" required autofocus>

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
