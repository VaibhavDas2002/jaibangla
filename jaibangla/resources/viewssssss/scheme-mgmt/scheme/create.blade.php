@extends('scheme-mgmt.scheme.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add New Scheme</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('scheme.store') }}">
                        {{ csrf_field() }} 

                        <div class="form-group{{ $errors->has('scheme_type') ? ' has-error' : '' }}">
                            <label for="scheme_type" class="col-md-4 control-label">Scheme Type</label>

                            <div class="col-md-6">
                                <select id="scheme_type" class="form-control" name="scheme_type" value="{{ old('scheme_type') }}" required>
                                    @foreach($schemetypes as $schema)
                                    <option value="{{$schema->id}}">{{$schema->scheme_type}}</option>
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
                            <label for="scheme_name" class="col-md-4 control-label">Scheme Name</label>

                            <div class="col-md-6">
                                <input id="scheme_name" type="text" class="form-control" name="scheme_name" value="{{ old('scheme_name') }}" required autofocus>

                                @if ($errors->has('scheme_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scheme_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        

                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="description" class="col-md-4 control-label">Description</label>
                            <div class="col-md-6">
                                <textarea id="description"  class="form-control" name="description" value="{{ old('description') }}" required autofocus></textarea>
                                @if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('shortcode') ? ' has-error' : '' }}">
                            <label for="shortcode" class="col-md-4 control-label">Short Code</label>
                            <div class="col-md-6">
                                 <input id="shortcode" type="text" class="form-control" name="shortcode" value="{{ old('shortcode') }}" required autofocus>
                                @if ($errors->has('shortcode'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('shortcode') }}</strong>
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
