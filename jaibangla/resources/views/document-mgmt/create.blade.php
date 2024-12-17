@extends('document-mgmt.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Add New Document</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('document-mgmt.store') }}">
                        {{ csrf_field() }} 

                        

                        <div class="form-group{{ $errors->has('doc_name') ? ' has-error' : '' }}">
                            <label for="doc_name" class="col-md-4 control-label">Document Name</label>

                            <div class="col-md-6">
                                <input id="doc_name" type="text" class="form-control" name="doc_name" value="{{ old('doc_name') }}" required autofocus>

                                @if ($errors->has('doc_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('doc_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        

                        <div class="form-group{{ $errors->has('doc_type') ? ' has-error' : '' }}">
                            <label for="doc_type" class="col-md-4 control-label">Document Type</label>
                            <div class="col-md-6">
                                <input id="doc_type" type="text" class="form-control" name="doc_type" value="{{ old('doc_type') }}" required autofocus>
                                @if ($errors->has('doc_type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('doc_type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('max_size') ? ' has-error' : '' }}">
                            <label for="max_size" class="col-md-4 control-label">Max Size <b>(in KB)</b></label>
                            <div class="col-md-6">
                                 <input id="max_size" type="text" class="form-control" name="max_size" value="{{ old('max_size') }}" required autofocus>
                                @if ($errors->has('max_size'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('max_size') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('doucument_group') ? ' has-error' : '' }}">
                            <label for="doucument_group" class="col-md-4 control-label">Document Group</label>

                            <div class="col-md-6">
                            <select name="doucument_group" id="doucument_group" class="form-control" tabindex="4">
                                <option value="">--NA  --</option>
                                @foreach(Config::get('constants.document_group') as $key=>$val)
                                <option value="{{$key}}" @if( old('doucument_group') == $key)  selected  @endif >{{$val}}</option>
                                @endforeach      
                            </select>

                                @if ($errors->has('doucument_group'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('doucument_group') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- <div class="form-group{{ $errors->has('is_profile_pic') ? ' has-error' : '' }}">
                            <label for="is_profile_pic" class="col-md-4 control-label">Profile Picture(yes/no)</label>
                            <div class="col-md-6">
                                 <input id="is_profile_pic" type="text" class="form-control" name="is_profile_pic" value="{{ old('is_profile_pic') }}" required autofocus>
                                @if ($errors->has('is_profile_pic'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('max_size') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div> -->
                       
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Add
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
